#!/usr/bin/env node
/**
 * `diviskit-registry` — deterministic generator for
 * `data/verified-attrs.json` + `data/verified-attrs-backlog.json`.
 *
 * Inputs:
 *   data/registry-seed.json   Hand-maintained seed: legacy diviops
 *                             evidence (sources prefixed `legacy-diviops:`)
 *                             plus SCHEMA_OBSERVED doc-derived cells.
 *   data/evidence/*.capture.json
 *                             Machine-produced captures from
 *                             `diviskit-preset capture` (REST storage
 *                             roundtrips written by capture.ts).
 *
 * Merge contract:
 *   - Evidence can only RAISE a cell's level, never lower it. A
 *     mismatch verification produces a backlog `drift_detected` entry —
 *     it does not downgrade the cell.
 *   - >= 1 match verification on a (family, module[, variant]) cell maps
 *     to VB_PRESET_STORAGE_VERIFIED; matches on >= 2 distinct
 *     `divi_version` values promote to CROSS_VERSION_STABLE.
 *   - Cells absent from `applicability` stay absent — evidence creates
 *     cells explicitly, never implicitly.
 *
 * Modes:
 *   default    Write verified-attrs.json + verified-attrs-backlog.json.
 *   --check    Regenerate in memory; exit 1 when the committed files
 *              differ (ignoring `generated_at`). Catches hand-edits and
 *              stale builds in CI.
 */

import { existsSync, readFileSync, readdirSync, writeFileSync } from "node:fs";
import { dirname, join, relative } from "node:path";
import { fileURLToPath } from "node:url";
import type {
  ApplicabilityCell,
  Tier12Entry,
  VerifiedAttrsRegistry,
} from "./registry.js";
import type { EvidenceFile, Verification } from "./evidence.js";

const __dirname = dirname(fileURLToPath(import.meta.url));

export const REGISTRY_VERSION = "1.1.0";
export const GENERATOR = "diviskit-registry (src/preset-cli/build-registry.ts)";

const LEVEL_MATCH = "VB_PRESET_STORAGE_VERIFIED";
const LEVEL_STABLE = "CROSS_VERSION_STABLE";

interface SeedFile extends VerifiedAttrsRegistry {
  notes?: string[];
  tier3?: unknown[];
  patterns_index?: Record<string, unknown>;
  effective_evidence_rule?: string;
  write_emitter_threshold?: string;
  seed_provenance?: string;
  [key: string]: unknown;
}

interface BacklogGap {
  pattern_family: string;
  module: string;
  pattern_variant?: string;
  effective_level: string;
  needed_level: string;
  reason: "below_write_threshold" | "drift_detected";
  source?: string;
  detail?: string;
}

interface BacklogFile {
  schema_version: string;
  generated_at: string;
  generator: string;
  gaps: BacklogGap[];
}

function dataDir(): string {
  const candidates = [
    join(__dirname, "..", "..", "data"),
    join(__dirname, "..", "data"),
  ];
  for (const p of candidates) {
    if (existsSync(p)) return p;
  }
  return candidates[0];
}

function levelNumber(reg: VerifiedAttrsRegistry, name?: string): number {
  if (!name) return 0;
  const n = reg.evidence_level_ordering[name];
  return typeof n === "number" ? n : 0;
}

function levelName(reg: VerifiedAttrsRegistry, num: number): string {
  for (const [name, n] of Object.entries(reg.evidence_level_ordering)) {
    if (n === num) return name;
  }
  return "UNVERIFIED";
}

/** Distinct divi_version values on which this capture matched. */
function matchedVersions(file: EvidenceFile): string[] {
  const versions = new Set<string>();
  for (const v of file.verifications) {
    if (v.result === "match" && v.divi_version) versions.add(v.divi_version);
  }
  return [...versions].sort();
}

/** Latest verification chronologically (ISO timestamps sort lexically). */
function latestVerification(file: EvidenceFile): Verification | undefined {
  return [...file.verifications].sort((a, b) =>
    a.captured_at.localeCompare(b.captured_at),
  ).at(-1);
}

function loadEvidenceFiles(
  evidenceDir: string,
): Array<{ path: string; file: EvidenceFile }> {
  if (!existsSync(evidenceDir)) return [];
  return readdirSync(evidenceDir)
    .filter((f) => f.endsWith(".capture.json"))
    .sort()
    .map((f) => ({
      path: join(evidenceDir, f),
      file: JSON.parse(
        readFileSync(join(evidenceDir, f), "utf-8"),
      ) as EvidenceFile,
    }));
}

function findOrCreateTierEntry(
  registry: VerifiedAttrsRegistry,
  family: string,
  variant: string | undefined,
  repoRoot: string,
  evidencePath: string,
): Tier12Entry {
  const tiers: Array<{ list: Tier12Entry[]; isTier1: boolean }> = [
    { list: (registry.tier2 ??= []), isTier1: false },
    { list: (registry.tier1 ??= []), isTier1: true },
  ];
  for (const { list } of tiers) {
    const hit = list.find(
      (e) => e.pattern_family === family && e.pattern_variant === variant,
    );
    if (hit) return hit;
  }
  // New family observed by a capture — create the entry in the tier the
  // family belongs to (module.decoration.* → tier1, everything else →
  // tier2). Entries created here carry only evidence-derived data.
  const entry: Tier12Entry = {
    pattern_family: family,
    ...(variant !== undefined ? { pattern_variant: variant } : {}),
    pattern_evidence_level: "UNVERIFIED",
    pattern_evidence_source: relative(repoRoot, evidencePath),
    applicability: {},
  };
  const target = family.startsWith("module.decoration.")
    ? tiers[1].list
    : tiers[0].list;
  target.push(entry);
  return entry;
}

export interface BuildResult {
  registry: SeedFile & { registry_version: string; generated_at: string };
  backlog: BacklogFile;
  /** Human-readable upgrade/drift notes for the CLI summary. */
  notes: string[];
}

export function buildRegistry(
  seed: SeedFile,
  evidenceFiles: Array<{ path: string; file: EvidenceFile }>,
  opts: { repoRoot: string; generatedAt: string },
): BuildResult {
  const registry = JSON.parse(JSON.stringify(seed)) as SeedFile;
  const notes: string[] = [];
  const driftGaps: BacklogGap[] = [];

  for (const { path: evidencePath, file } of evidenceFiles) {
    const relPath = relative(opts.repoRoot, evidencePath);
    const versions = matchedVersions(file);
    const derivedLevelName =
      versions.length >= 2 ? LEVEL_STABLE : versions.length === 1 ? LEVEL_MATCH : null;
    const derivedLevel = derivedLevelName
      ? levelNumber(registry, derivedLevelName)
      : 0;
    const latest = latestVerification(file);

    for (const applies of file.applies_to) {
      // Drift: latest run mismatched — report, never downgrade.
      if (latest && latest.result === "mismatch") {
        driftGaps.push({
          pattern_family: applies.pattern_family,
          module: applies.module,
          ...(applies.pattern_variant
            ? { pattern_variant: applies.pattern_variant }
            : {}),
          effective_level: "n/a",
          needed_level: LEVEL_MATCH,
          reason: "drift_detected",
          source: relPath,
          detail:
            `Latest capture (${latest.captured_at}, Divi ` +
            `${latest.divi_version ?? "?"}) produced ${latest.diff_paths.length} ` +
            `diff(s) — emitted shape no longer matches stored shape. ` +
            `Investigate before the registry trusts this cell again.`,
        });
        notes.push(
          `DRIFT ${applies.pattern_family} on ${applies.module}: latest capture mismatched (${relPath})`,
        );
        continue;
      }
      if (!derivedLevelName) continue;

      const entry = findOrCreateTierEntry(
        registry,
        applies.pattern_family,
        applies.pattern_variant,
        opts.repoRoot,
        evidencePath,
      );
      const applicability = (entry.applicability ??= {});
      const existing = applicability[applies.module];
      const existingLevel = levelNumber(registry, existing?.cell_evidence_level);

      // Cross-version union: an already-verified cell (seed or prior
      // evidence) plus a fresh match on a DIFFERENT divi_version is real
      // multi-version evidence — the effective derived level counts both.
      let effectiveDerived = derivedLevel;
      const thresholdLevel = levelNumber(registry, LEVEL_MATCH);
      if (existing && existingLevel >= thresholdLevel && existing.cell_divi_version) {
        const priorVersions = new Set(
          String(existing.cell_divi_version)
            .split(",")
            .map((v) => v.trim())
            .filter(Boolean),
        );
        const union = new Set([...priorVersions, ...versions]);
        if (union.size >= 2) {
          effectiveDerived = levelNumber(registry, LEVEL_STABLE);
        }
      }
      const effectiveDerivedName =
        effectiveDerived > 0 ? levelName(registry, effectiveDerived) : null;

      // Pattern level: evidence can only raise it.
      const patternLevel = levelNumber(registry, entry.pattern_evidence_level);
      if (effectiveDerived > patternLevel && effectiveDerivedName) {
        entry.pattern_evidence_level = effectiveDerivedName;
        entry.pattern_evidence_source = relPath;
        notes.push(
          `pattern ${applies.pattern_family}${applies.pattern_variant ? ` (${applies.pattern_variant})` : ""}: ${levelName(registry, patternLevel)} → ${effectiveDerivedName}`,
        );
      }

      const cell: ApplicabilityCell = {
        ...(applies.wrapper ? { wrapper: applies.wrapper } : {}),
        ...(applies.preset_type ? { preset_type: applies.preset_type } : {}),
        ...(applies.group_name ? { group_name: applies.group_name } : {}),
        ...(applies.group_id ? { group_id: applies.group_id } : {}),
        cell_evidence_level: effectiveDerivedName ?? derivedLevelName ?? "UNVERIFIED",
        cell_divi_version:
          versions.length === 1 ? versions[0] : versions.join(", "),
        verified_at: latest?.captured_at.slice(0, 10),
        source: relPath,
        caveats: [
          "Verified via automated REST storage roundtrip " +
            "(preset/create → preset/inspect byte-diff), not a Visual " +
            "Builder UI roundtrip.",
        ],
      };

      if (effectiveDerivedName && effectiveDerived !== derivedLevel) {
        cell.cell_divi_version = [
          ...new Set(
            [
              ...(existing?.cell_divi_version
                ? String(existing.cell_divi_version).split(",").map((v) => v.trim())
                : []),
              ...versions,
            ],
          ),
        ]
          .sort()
          .join(", ");
      }

      if (!existing || effectiveDerived > existingLevel) {
        applicability[applies.module] = cell;
        notes.push(
          `cell ${applies.pattern_family} / ${applies.module}: ${existing?.cell_evidence_level ?? "absent"} → ${effectiveDerivedName ?? derivedLevelName}`,
        );
      }
    }
  }

  // Backlog: every tier1/tier2 applicability cell below the write
  // threshold is a capture candidate; drift entries ride along.
  const gaps: BacklogGap[] = [...driftGaps];
  const threshold = levelNumber(
    registry,
    typeof registry.write_emitter_threshold === "string"
      ? String(registry.write_emitter_threshold).split(" ")[0]
      : LEVEL_MATCH,
  );
  for (const tier of [registry.tier1 ?? [], registry.tier2 ?? []]) {
    for (const entry of tier) {
      const patternLevel = levelNumber(registry, entry.pattern_evidence_level);
      for (const [module, cell] of Object.entries(entry.applicability ?? {})) {
        const effective = Math.min(
          patternLevel,
          levelNumber(registry, cell.cell_evidence_level),
        );
        if (effective < threshold) {
          gaps.push({
            pattern_family: entry.pattern_family,
            module,
            ...(entry.pattern_variant
              ? { pattern_variant: entry.pattern_variant }
              : {}),
            effective_level: levelName(registry, effective),
            needed_level: LEVEL_MATCH,
            reason: "below_write_threshold",
            source: cell.source,
          });
        }
      }
    }
  }
  gaps.sort((a, b) =>
    `${a.pattern_family}/${a.module}/${a.pattern_variant ?? ""}`.localeCompare(
      `${b.pattern_family}/${b.module}/${b.pattern_variant ?? ""}`,
    ),
  );

  const out = {
    schema_version: registry.schema_version,
    registry_version: REGISTRY_VERSION,
    generated_at: opts.generatedAt,
    generator: GENERATOR,
    ...registry,
  } as BuildResult["registry"];

  return {
    registry: out,
    backlog: {
      schema_version: "1.0.0",
      generated_at: opts.generatedAt,
      generator: GENERATOR,
      gaps,
    },
    notes,
  };
}

/** Compare ignoring `generated_at` — the only intentionally volatile field. */
function normalizeForCompare(value: unknown): string {
  const clone = JSON.parse(JSON.stringify(value)) as Record<string, unknown>;
  delete clone["generated_at"];
  return JSON.stringify(clone, null, 2);
}

function main(): number {
  const args = process.argv.slice(2);
  const check = args.includes("--check");
  const opt = (flag: string): string | undefined => {
    const i = args.indexOf(flag);
    return i >= 0 ? args[i + 1] : undefined;
  };

  const data = dataDir();
  const repoRoot = join(data, "..");
  const seedPath = opt("--seed") ?? join(data, "registry-seed.json");
  const evidenceDir = opt("--evidence-dir") ?? join(data, "evidence");
  const outPath = opt("--out") ?? join(data, "verified-attrs.json");
  const backlogPath =
    opt("--backlog-out") ?? join(data, "verified-attrs-backlog.json");

  if (!existsSync(seedPath)) {
    process.stderr.write(`Seed not found: ${seedPath}\n`);
    return 1;
  }
  const seed = JSON.parse(readFileSync(seedPath, "utf-8")) as SeedFile;
  const evidenceFiles = loadEvidenceFiles(evidenceDir);

  const result = buildRegistry(seed, evidenceFiles, {
    repoRoot,
    generatedAt: new Date().toISOString().slice(0, 10),
  });

  if (check) {
    let failed = false;
    for (const [label, path, generated] of [
      ["verified-attrs.json", outPath, result.registry],
      ["verified-attrs-backlog.json", backlogPath, result.backlog],
    ] as const) {
      const committed = existsSync(path)
        ? (JSON.parse(readFileSync(path, "utf-8")) as unknown)
        : null;
      if (
        committed === null ||
        normalizeForCompare(committed) !== normalizeForCompare(generated)
      ) {
        process.stderr.write(
          `${label} is stale — run \`npm run registry:build\`.\n`,
        );
        failed = true;
      }
    }
    if (!failed) {
      process.stdout.write("registry artifacts are up to date.\n");
    }
    return failed ? 1 : 0;
  }

  writeFileSync(outPath, JSON.stringify(result.registry, null, 2) + "\n");
  writeFileSync(backlogPath, JSON.stringify(result.backlog, null, 2) + "\n");
  process.stdout.write(
    `verified-attrs.json regenerated from seed + ${evidenceFiles.length} evidence file(s).\n`,
  );
  for (const note of result.notes) process.stdout.write(`  ${note}\n`);
  process.stdout.write(
    `backlog: ${result.backlog.gaps.length} gap(s) → verified-attrs-backlog.json\n`,
  );
  return 0;
}

const isMain =
  process.argv[1] && fileURLToPath(import.meta.url) === process.argv[1];
if (isMain) {
  process.exitCode = main();
}
