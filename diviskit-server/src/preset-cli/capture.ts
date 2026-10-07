/**
 * `diviskit-preset capture` — automated evidence capture for the
 * verified-attrs registry.
 *
 * Roundtrip: emit canonical preset → `preset/create` on a designated
 * scratch site → `preset/inspect` readback → byte-diff emitted vs stored
 * attrs → append a verification record to `data/evidence/*.capture.json`
 * → delete the preset again.
 *
 * A `match` verification is the input `build-registry.ts` promotes to
 * `VB_PRESET_STORAGE_VERIFIED` (or `CROSS_VERSION_STABLE` across Divi
 * versions). A `mismatch` is recorded too — it is the drift signal —
 * but never raises evidence.
 *
 * Site safety: capture refuses to run unless `--site <host>` is passed,
 * `<host>` appears in the `DIVISKIT_VERIFY_SITES` allowlist env var,
 * AND `WP_URL`'s hostname equals `<host>`. Three checks so a stray
 * WP_URL can never write verification presets to production.
 */

import {
  appendVerification,
  defaultEvidenceDir,
  diffAttrs,
  evidenceFilePath,
  ADOPT_KIND,
  CAPTURE_KIND,
  EVIDENCE_SCHEMA_VERSION,
  type CaptureAppliesTo,
  type EvidenceFile,
  type Verification,
} from "./evidence.js";
import {
  assertPresetBodyIsolation,
  assertStorageCapability,
  type PresetWriteClient,
} from "./write-path.js";
import type { DiviopsResponse } from "../envelope.js";
import type { HandshakeResult } from "../compatibility.js";

/** The emitter commands that produce preset-creatable entries. */
export const CAPTURABLE_COMMANDS = [
  "button",
  "heading-font",
  "text-body-font",
  "spacing",
] as const;
export type CapturableCommand = (typeof CAPTURABLE_COMMANDS)[number];

/** Minimal shape every preset emitter's entry satisfies. */
export interface EmittedPresetEntry {
  type: string;
  module_name: string;
  name: string;
  attrs: Record<string, unknown>;
  group_name?: string;
  group_id?: string;
  pattern_variant?: string;
  primary_attr_name?: string;
}

export class SiteRefusedError extends Error {
  constructor(message: string) {
    super(message);
    this.name = "SiteRefusedError";
  }
}

export class CaptureMismatchError extends Error {
  constructor(
    public readonly diffCount: number,
    public readonly evidencePath: string,
  ) {
    super(
      `Capture produced ${diffCount} diff(s) between emitted and stored ` +
        `attrs — the cell is NOT verified. The mismatch was recorded in ` +
        `${evidencePath} for the backlog (reason: drift_detected).`,
    );
    this.name = "CaptureMismatchError";
  }
}

/**
 * Triple site-safety check. `--site` names the intended host,
 * `DIVISKIT_VERIFY_SITES` is the operator-curated allowlist, and WP_URL
 * must actually point at that host. Any disagreement refuses the run.
 */
export function assertVerifySite(
  wpUrl: string,
  siteFlag: string | undefined,
  env: NodeJS.ProcessEnv = process.env,
): string {
  if (!siteFlag) {
    throw new SiteRefusedError(
      "capture requires --site <host> naming the intended scratch site " +
        "(e.g. --site diviskit-shop.ddev.site). This is a deliberate " +
        "speed-bump so captures never hit production by accident.",
    );
  }
  const allowlist = (env.DIVISKIT_VERIFY_SITES ?? "")
    .split(",")
    .map((h) => h.trim())
    .filter(Boolean);
  if (!allowlist.includes(siteFlag)) {
    throw new SiteRefusedError(
      `--site ${siteFlag} is not in the DIVISKIT_VERIFY_SITES allowlist ` +
        `(currently: ${allowlist.join(", ") || "(empty)"}). Add it to ` +
        `DIVISKIT_VERIFY_SITES to bless this host for captures.`,
    );
  }
  let wpHost: string;
  try {
    wpHost = new URL(wpUrl).hostname;
  } catch {
    throw new SiteRefusedError(`WP_URL is not a valid URL: ${wpUrl}`);
  }
  if (wpHost !== siteFlag) {
    throw new SiteRefusedError(
      `--site ${siteFlag} does not match WP_URL host ${wpHost}. ` +
        `Capture requires the credentials to point at the named site.`,
    );
  }
  return siteFlag;
}

/**
 * Registry cells a capture of `entry` exercises. Derived from the
 * emitted attrs tree — the diff only proves what was actually emitted,
 * so `applies_to` enumerates exactly the touched families.
 */
export function captureAppliesTo(
  command: CapturableCommand,
  entry: EmittedPresetEntry,
): CaptureAppliesTo[] {
  const wrapper = Object.keys(entry.attrs)[0];
  const common = {
    module: entry.module_name,
    ...(entry.pattern_variant
      ? { pattern_variant: entry.pattern_variant }
      : {}),
    ...(wrapper ? { wrapper } : {}),
    preset_type: entry.type === "group" ? ("group" as const) : ("module" as const),
    ...(entry.group_name ? { group_name: entry.group_name } : {}),
    ...(entry.group_id ? { group_id: entry.group_id } : {}),
  };

  if (command === "button") {
    // The button group preset touches up to three registry families —
    // one per emitted decoration bag (background / border / font / the
    // hover-padding `button` bag). Enumerate what was actually emitted.
    const decoration =
      (entry.attrs["button"] as Record<string, unknown> | undefined)?.[
        "decoration"
      ] ?? {};
    return Object.keys(decoration as Record<string, unknown>).map((k) => {
      // `divi/button.font` exists per pattern variant (A = separate
      // weight key, B = weight encoded in family). Derive from the
      // emitted bag so the cell lands on the right variant entry.
      const variant =
        k === "font" ? fontVariantFromAttrs(entry.attrs, "button") : undefined;
      return {
        pattern_family: `divi/button.${k}`,
        ...(variant ? { pattern_variant: variant } : {}),
        ...common,
      };
    });
  }

  // Single-family emitters: the group_name IS the registry family.
  return [
    {
      pattern_family: entry.group_name ?? entry.module_name,
      ...common,
    },
  ];
}

interface PresetCreateData {
  success?: boolean;
  preset?: { id?: string };
}

interface PresetInspectData {
  attrs?: Record<string, unknown> | null;
  styleAttrs?: Record<string, unknown> | null;
  renderAttrs?: Record<string, unknown> | null;
  coordinates?: Record<string, unknown>;
}

export interface CaptureSummary {
  result: "match" | "mismatch";
  evidence_file: string;
  preset_id: string;
  preset_deleted: boolean;
  divi_version: string | null;
  plugin_version: string | null;
  diff_count: number;
  applies_to: CaptureAppliesTo[];
}

/**
 * Run one capture roundtrip. `createBody` is the `/preset/create` wire
 * body (WITHOUT dry_run). The preset name is suffixed with a capture
 * tag so repeated runs don't collide with the route's uniqueness probe.
 */
export async function runCapture(
  client: PresetWriteClient,
  command: CapturableCommand,
  entry: EmittedPresetEntry,
  createBody: Record<string, unknown>,
  opts: {
    serverVersion: string;
    site: string;
    evidenceDir?: string;
    keepPreset?: boolean;
  },
): Promise<CaptureSummary> {
  const appliesTo = captureAppliesTo(command, entry);

  // Same isolation + capability gates as --apply: a capture must exercise
  // the real write path, not a shortcut around it.
  assertPresetBodyIsolation({ ...createBody });
  const hs: HandshakeResult = await assertStorageCapability(
    client,
    opts.serverVersion,
  );

  const captureName = `${entry.name} [capture-${Date.now()}]`;
  const body: Record<string, unknown> = { ...createBody, name: captureName };
  delete body["dry_run"];

  const created = await client.requestEnveloped<PresetCreateData>(
    "/preset/create",
    { method: "POST", body },
  );
  if (!created.ok) {
    throw new Error(
      `preset/create failed: ${created.error.code} — ${created.error.message}`,
    );
  }
  const presetId = created.data?.preset?.id;
  if (!presetId) {
    throw new Error(
      `preset/create did not return a preset id: ${JSON.stringify(created.data)}`,
    );
  }

  let stored: PresetInspectData;
  try {
    const inspected = await client.requestEnveloped<PresetInspectData>(
      `/preset/inspect/${encodeURIComponent(presetId)}`,
      { method: "GET" },
    );
    if (!inspected.ok) {
      throw new Error(
        `preset/inspect failed: ${inspected.error.code} — ${inspected.error.message}`,
      );
    }
    stored = inspected.data ?? {};
  } finally {
    if (!opts.keepPreset) {
      await client
        .requestEnveloped("/preset/delete", {
          method: "POST",
          body: { preset_id: presetId, force: true },
        })
        .catch(() => undefined);
    }
  }

  const diffs = diffAttrs(entry.attrs, stored.attrs ?? null);
  const verification: Verification = {
    method: "rest_storage_roundtrip",
    captured_at: new Date().toISOString(),
    site: opts.site,
    divi_version: hs.divi?.version ?? null,
    plugin_version: hs.plugin_version ?? null,
    preset_id: presetId,
    result: diffs.length === 0 ? "match" : "mismatch",
    diff_paths: diffs,
    emitted_attrs: entry.attrs,
    stored_attrs: (stored.attrs as Record<string, unknown>) ?? null,
  };

  const evidenceDir = opts.evidenceDir ?? defaultEvidenceDir();
  const path = evidenceFilePath(
    evidenceDir,
    command,
    entry.module_name,
    entry.pattern_variant,
  );
  const init: Omit<EvidenceFile, "verifications"> = {
    schema_version: EVIDENCE_SCHEMA_VERSION,
    kind: CAPTURE_KIND,
    command,
    applies_to: appliesTo,
  };
  appendVerification(path, init, verification);

  const summary: CaptureSummary = {
    result: verification.result,
    evidence_file: path,
    preset_id: presetId,
    preset_deleted: !opts.keepPreset,
    divi_version: verification.divi_version,
    plugin_version: verification.plugin_version,
    diff_count: diffs.length,
    applies_to: appliesTo,
  };

  if (verification.result === "mismatch") {
    throw new CaptureMismatchError(diffs.length, path);
  }
  return summary;
}

/* ------------------------------------------------------------------ */
/* adopt — promote an existing VB-authored preset into evidence        */
/* ------------------------------------------------------------------ */

interface PresetCoordinates {
  bucket?: string;
  module_name?: string | null;
  group_name?: string | null;
  group_id?: string | null;
}

/**
 * Font variant heuristic for adoptions: the canonical difference between
 * Pattern A (Google CDN) and Pattern B (local-hosted) is whether the
 * font value bag carries a separate numeric `weight` key. Pattern B
 * encodes the weight inside the family string and has none.
 */
function fontVariantFromAttrs(
  attrs: Record<string, unknown>,
  wrapper: string | undefined,
): string | undefined {
  if (!wrapper) return undefined;
  const value = (
    (((attrs[wrapper] as Record<string, unknown> | undefined)?.[
      "decoration"
    ] as Record<string, unknown> | undefined)?.["font"] as Record<
      string,
      unknown
    > | undefined)?.["font"] as Record<string, unknown> | undefined
  )?.["desktop"] as Record<string, unknown> | undefined;
  const bag = value?.["value"] as Record<string, unknown> | undefined;
  if (!bag) return undefined;
  return "weight" in bag ? "google_fonts_pattern_a" : "local_hosted_pattern_b";
}

/**
 * Derive the registry cells a stored preset covers — the inverse of
 * `captureAppliesTo`: instead of "what did our emitter touch" it is
 * "which families does this VB-authored preset exercise". The stored
 * shape is ground truth, so any family derivable from its coordinates +
 * attrs tree is legitimately covered.
 */
export function adoptionAppliesTo(inspect: {
  attrs?: Record<string, unknown> | null;
  coordinates?: Record<string, unknown>;
}): CaptureAppliesTo[] {
  const attrs = inspect.attrs;
  const coords = (inspect.coordinates ?? {}) as PresetCoordinates;
  const module = coords.module_name ?? undefined;
  if (!attrs || !module) return [];

  const wrapper = Object.keys(attrs)[0];
  const decoration = (
    attrs[wrapper ?? ""] as Record<string, unknown> | undefined
  )?.["decoration"] as Record<string, unknown> | undefined;

  const common = {
    module,
    ...(wrapper ? { wrapper } : {}),
    preset_type: coords.bucket === "group" ? ("group" as const) : ("module" as const),
    ...(coords.group_name ? { group_name: coords.group_name } : {}),
    ...(coords.group_id ? { group_id: coords.group_id } : {}),
  };

  if (coords.bucket === "group") {
    if (coords.group_name === "divi/button") {
      return Object.keys(decoration ?? {}).map((k) => {
        const variant =
          k === "font" ? fontVariantFromAttrs(attrs, wrapper) : undefined;
        return {
          pattern_family: `divi/button.${k}`,
          ...(variant ? { pattern_variant: variant } : {}),
          ...common,
        };
      });
    }
    const variant =
      coords.group_name === "divi/font" || coords.group_name === "divi/font-body"
        ? fontVariantFromAttrs(attrs, wrapper)
        : undefined;
    return [
      {
        pattern_family: coords.group_name ?? module,
        ...(variant ? { pattern_variant: variant } : {}),
        ...common,
      },
    ];
  }

  // Module presets exercise tier-1 shared-decoration families.
  return Object.keys(decoration ?? {}).map((k) => ({
    pattern_family: `module.decoration.${k}`,
    ...common,
  }));
}

export interface AdoptionSummary {
  result: "match";
  evidence_file: string;
  preset_id: string;
  divi_version: string | null;
  plugin_version: string | null;
  applies_to: CaptureAppliesTo[];
}

/**
 * Adopt an existing preset as ground-truth evidence. Use this for cells
 * the emitters can't emit yet: author the preset once in the Visual
 * Builder on the scratch site, then `adopt <preset_id>` records its
 * stored shape — the automated replacement for the manual canonical-
 * shape dumps from the diviops workflow.
 */
export async function runAdoption(
  client: PresetWriteClient,
  presetId: string,
  opts: {
    serverVersion: string;
    site: string;
    evidenceDir?: string;
  },
): Promise<AdoptionSummary> {
  const hs = await assertStorageCapability(client, opts.serverVersion);

  const inspected = await client.requestEnveloped<PresetInspectData>(
    `/preset/inspect/${encodeURIComponent(presetId)}`,
    { method: "GET" },
  );
  if (!inspected.ok) {
    throw new Error(
      `preset/inspect ${presetId} failed: ${inspected.error.code} — ${inspected.error.message}`,
    );
  }
  const stored = inspected.data ?? {};
  const storedAttrs =
    (stored.attrs as Record<string, unknown> | null) ?? null;
  if (!storedAttrs || Object.keys(storedAttrs).length === 0) {
    throw new Error(
      `preset ${presetId} has no stored attrs — nothing to adopt.`,
    );
  }

  const appliesTo = adoptionAppliesTo(stored);
  if (appliesTo.length === 0) {
    throw new Error(
      `Could not derive any registry cell from preset ${presetId} ` +
        `(coordinates: ${JSON.stringify(stored.coordinates ?? null)}). ` +
        `The adoption covers nothing — refusing to record empty evidence.`,
    );
  }
  // Stable ordering so re-adoptions of the same cell set hit the same file.
  appliesTo.sort((a, b) =>
    a.pattern_family.localeCompare(b.pattern_family),
  );

  const primary = appliesTo[0];
  const verification: Verification = {
    method: "vb_authored_adoption",
    captured_at: new Date().toISOString(),
    site: opts.site,
    divi_version: hs.divi?.version ?? null,
    plugin_version: hs.plugin_version ?? null,
    preset_id: presetId,
    result: "match",
    diff_paths: [],
    emitted_attrs: storedAttrs,
    stored_attrs: storedAttrs,
  };

  const evidenceDir = opts.evidenceDir ?? defaultEvidenceDir();
  const path = evidenceFilePath(
    evidenceDir,
    "adopt",
    primary.module,
    primary.pattern_variant ?? primary.pattern_family,
  );
  const init: Omit<EvidenceFile, "verifications"> = {
    schema_version: EVIDENCE_SCHEMA_VERSION,
    kind: ADOPT_KIND,
    command: "adopt",
    applies_to: appliesTo,
  };
  appendVerification(path, init, verification);

  return {
    result: "match",
    evidence_file: path,
    preset_id: presetId,
    divi_version: verification.divi_version,
    plugin_version: verification.plugin_version,
    applies_to: appliesTo,
  };
}
