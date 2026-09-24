---
name: diviskit-scf
description: Secure Custom Fields (SCF) automation in Diviskit — field group creation, post-type registration, ACF/SCF data operations via the Diviskit MCP server.
---

<!--
Requires diviskit-mcp connected to a WP site with SCF 6.8.4+ for JSON
status/export/import/sync; field-group read tools also work against
ACF-compatible `acf-field-group` storage. Requires the diviskit-agent plugin
active. WP_PATH (Local by Flywheel) or WP_CLI_CMD (containerized) must be set —
on this project the MCP server does not currently set WP_CLI_CMD, so wp-cli
passthrough tools in this skill will not work until that env var is added.
Ported from the purchased diviops-pro-suite-v1.5.51-beta distribution.
Author: diviskit-dev, skill version: 1.0.
-->


# Diviskit SCF slice

SCF (Secure Custom Fields) and legacy ACF automation through the Diviskit MCP server. All `diviskit_scf_*` tools shell out to wp-cli — there is no plugin REST route. SCF 6.8.4+ ships the `wp scf json {status,sync,import,export}` family; the field-group read tools fall back to the `acf-field-group` post type directly since SCF 6.8.4 dropped `wp acf field-group …`.

Read [SKILL.md → diviskit](../diviskit/SKILL.md) first for the envelope contract, capability handshake, and `dry_run` shape — this slice inherits all of it.

## Tool surface

Six `diviskit_scf_*` tools (v1.5.x):

| Tool | Mutates | dry_run | Purpose |
|---|---|---|---|
| `diviskit_scf_status` | no | n/a | Sync status — JSON-on-disk vs DB |
| `diviskit_scf_field_group_list` | no | n/a | All field groups (ACF key, title, status, modified) |
| `diviskit_scf_field_group_get` | no | n/a | Single field group by ACF key or numeric post ID |
| `diviskit_scf_export` | no (writes to safe-root) | n/a | Export field groups / post types / taxonomies / options pages to a directory or stdout |
| `diviskit_scf_import` | yes | **not supported** (upstream gap) | Import field groups + co-definitions from a JSON file |
| `diviskit_scf_sync` | yes (default `dry_run: true`) | wp-cli passthrough | Apply pending JSON-on-disk changes to the DB |

Defaults to `dry_run: true` is intentional on `scf_sync` — caller must opt in to mutation.

For ad-hoc commands not covered by the typed wrappers (post CRUD against `acf-field-group`, options page reads, raw `wp scf …` invocations), use `diviskit_meta_wp_cli` with a wp-cli command. The typed wrappers are preferred because they pre-validate args and carry namespace-prefixed error codes; the raw passthrough is the escape hatch.

## Error codes

Inherits the standard envelope from [diviskit/](../diviskit/SKILL.md). SCF-specific namespace-prefixed codes:

| Code | HTTP | Meaning |
|---|---|---|
| `scf.not_configured` | 400 | `WP_PATH` / `WP_CLI_CMD` env var missing — wp-cli runner not initialized. Gate code. |
| `scf.command_failed` | 400 | wp-cli failed at runtime. Carries `error.data = { exit_code: number \| null, stdout: string, stderr: string, failure_kind: 'exited' \| 'killed' \| 'spawn_failed' \| 'rejected', command: string[] }`. The `failure_kind` discriminator is lifted into a structured field (vs. `meta_wp_cli`'s message-prefix disambiguator) and `command[]` echoes the wp-cli argv (no secrets — SCF args carry none) so debugging doesn't require parsing the message string. |
| `not_found` | 404 | `scf_field_group_get`: unresolvable ACF key or numeric ID that wp-cli rejects with "Could not find the post with ID …". `error.data.key` echoes the input. Hint points to `diviskit_scf_field_group_list`. |
| `invalid_input` | 400 | `scf_export` mutual-exclusion: neither / both of `dir` / `stdout` provided → `error.data = { missing: ["dir","stdout"] }` or `{ conflict: ["dir","stdout"] }`. |
| `wp_error` | 500 | `scf_field_group_list`: malformed JSON from wp-cli's `--format=json` output. Hint suggests re-running with `WP_CLI_DEBUG=1`. |

The four `failure_kind` branches on `scf.command_failed`:

- **`exited`** — wp-cli ran and exited non-zero. `exit_code` is the numeric exit; `stdout`/`stderr` carry the raw streams.
- **`killed`** — `execFile` launched the child but it was killed (timeout or signal). `exit_code` is `null`; streams carry whatever was emitted before the kill.
- **`spawn_failed`** — the OS refused to start the child (ENOENT / EACCES / EPERM). `exit_code` is `null`; streams are empty.
- **`rejected`** — pre-execution rejection by the allowlist or filesystem validator. The reason is synthesized into `error.data.stderr` so callers see a uniform `{ exit_code, stdout, stderr }` shape across all four failure modes.

## Identifier conventions

SCF distinguishes the **def's key** (what the SCF JSON carries — `group_abc123`, `post_type_xxx`, `taxonomy_xxx`) from the **registered slug** (what `wp post list` and REST URLs use — `event`, `book`, …). The typed wrappers' `field_groups[]`, `post_types[]`, `taxonomies[]` filter args match against the **def's key** or its admin **title** (case-insensitive), NOT the registered slug.

To discover def keys, run `diviskit_scf_export --stdout` with no filters and inspect the top-level entries; the `parent` field distinguishes field-groups (`parent='field-group'`) from post-type defs (`parent='post-type'`) from taxonomy defs.

`acf-field-group` is the WordPress post type SCF/ACF stores field groups in. The `_field_group_list` tool queries this post type via `wp post list` rather than a dedicated SCF subcommand — necessary because SCF 6.8.4 dropped `wp acf field-group …`. Returns `{ ID, post_name, post_title, post_status, post_modified }[]` where `post_name` is the ACF key.

## Filesystem semantics

`scf_export --dir=…` and `scf_import <file>` are filesystem-touching commands subject to Diviskit' safe-root constraint:

- Default safe root is `<WP_PATH>/.diviops-tmp/`.
- Override via `DIVISKIT_WP_CLI_SAFE_FS_ROOT` env var.
- Disable entirely (unsafe) via `DIVISKIT_WP_CLI_UNSAFE_FS=1`.
- In `WP_CLI_CMD` wrapper mode (containerized), `DIVISKIT_WP_CLI_SAFE_FS_ROOT` is **mandatory** for FS-sensitive commands — host-derived paths don't correspond to the container namespace.

SCF's `export` writes a single fixed filename `acf-export-YYYY-MM-DD.json` inside the target directory. **Two exports on the same day silently overwrite.** Copy/rename if you're archiving baselines.

## `dry_run` divergence

`scf_sync` flows the `dry_run` flag through to wp-cli's `--dry-run`. The success payload reflects the flag in `data.dry_run` so callers can branch without re-checking input args, but the preview output is wp-cli's plain-text summary — NOT the standardized `data.plan = { summary, changes[] }` shape used by plugin-routed `dry_run` tools.

`scf_import` does NOT accept `dry_run` — SCF's upstream `wp scf json import` lacks a `--dry-run` flag. For preview-then-commit flows, sync the JSON to a staging directory under the safe-root, run `scf_sync --dry_run` against it, then `scf_import` once the diff looks right.

## Coverage scope

This slice covers the SCF MCP tool surface as shipped in Diviskit v1.5.x. Field-group authoring recipes, hybrid Divi-page + SCF-field integration patterns, per-field-type quirks, and post-type registration workflows are not yet documented here — extraction surfaced gaps rather than authored content. See the PR description for filed follow-up issues.

When you need ACF/SCF semantics not covered above, fall back to `diviskit_meta_wp_cli` with `wp acf …` or `wp scf …` commands. The default wp-cli allowlist includes the SCF schema ops; filesystem-touching variants are subject to the same safe-root constraint above.
