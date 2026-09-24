# Changelog

## v1.0.0 — First public Diviskit distribution (2026-09-24)

First public release of the Diviskit stack — the GPL-licensed fork line of
the DiviOps suite, rebranded end-to-end (`diviskit/v1` canonical REST
namespace, `diviops/v1` kept as a compat alias).

### WordPress Plugins

- `diviskit-agent` **1.6.0** — REST bridge (104 routes on `diviskit/v1`,
  identical tree on `diviops/v1`), schema-dump endpoint
  (`schema/module/dump-all`), admin overview with MCP-config snippet +
  schema-dump card.
- `diviskit-design-library` **1.0.0** — GPL fork of diviops-design-library;
  `dsk-*` CSS classes, `_diviskit_design_*` post meta, `diviskit-design-fx` /
  `threejs` script handles.

### MCP Server

- `@diviskit/mcp-server` — source included in `diviskit-server/` (npm
  publication pending). 93 `diviskit_*` tools on the free surface;
  the 18 `diviskit_vk_*` Vendokit tools are Pro and handshake-gated.
  `DIVISKIT_*` env vars canonical, `DIVIOPS_*` accepted as fallbacks.

### Skills (Claude plugin bundle)

- `claude/diviskit-agent` — `diviskit` (harness primer), `diviskit-builder`
  (Divi 5 authoring incl. generated per-module maps, Divi 5.13 schema),
  `diviskit-scf` (SCF/ACF automation).
- `.claude-plugin/marketplace.json` exposes the bundle as the
  `diviskit-agent` plugin: `claude plugin marketplace add SaschaKohler/diviskit`.
- `bin/install-skills.sh` — installer for clients without a plugin
  marketplace (Devin, Codex): `--client devin|codex`, `--scope user|project`.
- `templates/` — project `AGENTS.md` Grundtemplate + Devin
  `mcp_config.local.json` example.

### Pro (separate distribution)

`diviskit-pro` (Vendokit coverage slice), the `diviskit-vendokit` +
`diviskit-mega-menu` skills, and `sk-consent` ship in the Pro distribution —
see <https://diviskit.com>.
