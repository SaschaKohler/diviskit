# data/evidence/ — machine-produced registry captures

`*.capture.json` files here are written by `diviskit-preset capture` and
`diviskit-preset adopt`. They are the automated replacement for the
manual canonical-shape dumps of the diviops workflow (whose sources are
marked `legacy-diviops:` in `data/registry-seed.json`).

## Kinds

- **`preset_storage_roundtrip`** (`capture <emitter>`): emit canonical
  JSON → `preset/create` → `preset/inspect` → byte-diff. A `match`
  proves our emitted shape survives storage losslessly — used for drift
  detection and cross-version stability of already-verified cells.
- **`vb_authored_adoption`** (`adopt <preset_id>`): a preset authored in
  the Visual Builder on a scratch site is read back from canonical
  storage — the stored shape IS ground truth and can verify cells the
  emitters don't cover yet.

## File format

One file per (command, module[, variant]) — e.g.
`spacing--divi~section.capture.json`. A file carries a `verifications`
array; re-runs append, never rewrite, so multi-version evidence
accumulates. `npm run registry:build` merges these files with
`data/registry-seed.json` into `data/verified-attrs.json`:

- ≥1 `match` on a cell → `VB_PRESET_STORAGE_VERIFIED`
- matches on ≥2 distinct `divi_version` values → `CROSS_VERSION_STABLE`
- latest verification `mismatch` → backlog `drift_detected` entry
  (never an automatic downgrade)

## Safety

Captures only run against hosts in the `DIVISKIT_VERIFY_SITES` env
allowlist, and only when `--site <host>` also equals the `WP_URL`
hostname. Never hand-edit `verified-attrs.json` — `npm run
registry:check` fails on drift between inputs and the generated file.
