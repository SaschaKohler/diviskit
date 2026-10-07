/**
 * Evidence-capture files for the verified-attrs registry pipeline.
 *
 * A capture file records one preset's REST storage roundtrip:
 * `preset/create` → `preset/inspect` → byte-diff of emitted vs stored
 * attrs. Files live in `data/evidence/` and are the machine-produced
 * input `build-registry.ts` merges with `data/registry-seed.json`.
 *
 * File naming — one file per (capture target, variant):
 *   `<command>--<module>[--<variant>].capture.json`
 * with `/` sanitized to `~` (e.g. `spacing--divi~section.capture.json`,
 * `heading-font--divi~heading--google_fonts_pattern_a.capture.json`).
 *
 * A file carries a `verifications` array — re-running a capture appends
 * instead of overwriting, which is what makes cross-version stability
 * detectable (matches on >= 2 distinct `divi_version` values promote a
 * cell to CROSS_VERSION_STABLE in the generated registry).
 */

import { existsSync, mkdirSync, readFileSync, writeFileSync } from "node:fs";
import { dirname, join } from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = dirname(fileURLToPath(import.meta.url));

export const EVIDENCE_SCHEMA_VERSION = "1.0.0";

/**
 * `preset_storage_roundtrip`: our emitter's shape was written and read
 * back byte-identical — proves storage fidelity of an already-verified
 * shape (drift detection, cross-version stability).
 *
 * `vb_authored_adoption`: a preset authored in the Visual Builder was
 * read back from canonical storage via `preset/inspect` — the stored
 * shape IS the ground truth, so it can verify NEW cells the emitters
 * don't know yet. This replaces the manual JSON dumps from the diviops
 * workflow.
 */
export const CAPTURE_KIND = "preset_storage_roundtrip";
export const ADOPT_KIND = "vb_authored_adoption";
export type EvidenceKind = typeof CAPTURE_KIND | typeof ADOPT_KIND;

/** One registry cell a capture proves (or refutes). */
export interface CaptureAppliesTo {
  pattern_family: string;
  module: string;
  pattern_variant?: string;
  /** Attr-tree root key the cell writes under (e.g. "module", "title"). */
  wrapper?: string;
  preset_type?: "group" | "module";
  group_name?: string;
  group_id?: string;
}

/** A single capture run appended to a capture file's history. */
export interface Verification {
  /** How this verification was produced — audits the honesty of the level. */
  method?: "rest_storage_roundtrip" | "vb_authored_adoption";
  captured_at: string;
  site: string;
  divi_version: string | null;
  plugin_version: string | null;
  preset_id: string;
  result: "match" | "mismatch";
  diff_paths: DiffEntry[];
  emitted_attrs: Record<string, unknown>;
  stored_attrs: Record<string, unknown> | null;
}

export interface EvidenceFile {
  schema_version: string;
  kind: EvidenceKind;
  /** CLI command that produced the preset (button, spacing, adopt, ...). */
  command: string;
  /** Registry cells this capture exercises. */
  applies_to: CaptureAppliesTo[];
  verifications: Verification[];
}

export interface DiffEntry {
  path: string;
  kind: "value_mismatch" | "missing_in_stored" | "extra_in_stored";
  emitted?: unknown;
  stored?: unknown;
}

/** `/` and whitespace are unsafe in portable filenames. */
export function sanitizeComponent(value: string): string {
  return value.replace(/\//g, "~").replace(/\s+/g, "_");
}

/**
 * Evidence file path for a capture target. `evidenceDir` is injectable
 * for tests; production resolution mirrors `loadRegistry`'s candidates.
 */
export function evidenceFilePath(
  evidenceDir: string,
  command: string,
  module: string,
  variant?: string,
): string {
  const parts = [sanitizeComponent(command), sanitizeComponent(module)];
  if (variant) parts.push(sanitizeComponent(variant));
  return join(evidenceDir, `${parts.join("--")}.capture.json`);
}

/** Resolve the repo `data/evidence/` dir using the same layout
 * tolerance as `loadRegistry` (dist/ and src/ both work). */
export function defaultEvidenceDir(): string {
  const candidates = [
    join(__dirname, "..", "..", "data", "evidence"),
    join(__dirname, "..", "data", "evidence"),
  ];
  for (const p of candidates) {
    if (existsSync(dirname(p))) return p;
  }
  return candidates[0];
}

export function loadEvidenceFile(path: string): EvidenceFile | null {
  if (!existsSync(path)) return null;
  return JSON.parse(readFileSync(path, "utf-8")) as EvidenceFile;
}

/**
 * Append a verification to the capture file at `path`, creating the file
 * when absent. Returns the updated file. Never rewrites history.
 */
export function appendVerification(
  path: string,
  init: Omit<EvidenceFile, "verifications">,
  verification: Verification,
): EvidenceFile {
  const existing = loadEvidenceFile(path);
  const file: EvidenceFile = existing ?? { ...init, verifications: [] };
  if (existing) {
    // Guard against a stale/foreign file being reused for a different
    // capture target — the filename encodes (command, module, variant)
    // so a mismatch here is a caller bug, not drift.
    if (
      existing.command !== init.command ||
      JSON.stringify(existing.applies_to) !== JSON.stringify(init.applies_to)
    ) {
      throw new Error(
        `Evidence file ${path} exists but describes a different capture ` +
          `target (command=${existing.command}). Refusing to append.`,
      );
    }
  }
  file.verifications.push(verification);
  mkdirSync(dirname(path), { recursive: true });
  writeFileSync(path, JSON.stringify(file, null, 2) + "\n", "utf-8");
  return file;
}

/**
 * Structural deep-diff between the emitted attrs bag and what
 * `preset/inspect` read back. Strict: extra keys in stored count as
 * diffs — byte-canonical means the whole bag, not a subset.
 */
export function diffAttrs(
  emitted: unknown,
  stored: unknown,
  basePath = "attrs",
): DiffEntry[] {
  const diffs: DiffEntry[] = [];

  const isPlainObject = (v: unknown): v is Record<string, unknown> =>
    v !== null && typeof v === "object" && !Array.isArray(v);

  if (isPlainObject(emitted) && isPlainObject(stored)) {
    for (const key of Object.keys(emitted)) {
      const path = `${basePath}.${key}`;
      if (!(key in stored)) {
        diffs.push({ path, kind: "missing_in_stored", emitted: emitted[key] });
      } else {
        diffs.push(...diffAttrs(emitted[key], stored[key], path));
      }
    }
    for (const key of Object.keys(stored)) {
      if (!(key in emitted)) {
        diffs.push({
          path: `${basePath}.${key}`,
          kind: "extra_in_stored",
          stored: stored[key],
        });
      }
    }
    return diffs;
  }

  if (Array.isArray(emitted) && Array.isArray(stored)) {
    if (emitted.length !== stored.length) {
      diffs.push({
        path: basePath,
        kind: "value_mismatch",
        emitted,
        stored,
      });
      return diffs;
    }
    for (let i = 0; i < emitted.length; i++) {
      diffs.push(...diffAttrs(emitted[i], stored[i], `${basePath}[${i}]`));
    }
    return diffs;
  }

  if (emitted !== stored) {
    diffs.push({ path: basePath, kind: "value_mismatch", emitted, stored });
  }
  return diffs;
}
