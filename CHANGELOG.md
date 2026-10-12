# Changelog

## Unreleased

### Validation hardening

- `diviskit_validate_blocks` now flags Divi 4 `[et_pb_*]` shortcode markup
  with a dedicated `legacy_shortcode` error code — detected in freeform
  blocks and embedded inside a named block's inner content. LLM output
  pulled from the legacy-corpus fails loudly at validate time instead of
  landing as literal shortcode text in `post_content`. Covered by new
  `ValidateTest` unit tests.
- `diviskit-builder` skill and the AGENTS.md template gain an explicit
  rule: never emit `[et_pb_*]` — authoring targets `<!-- divi/... -->`
  block comments with JSON attrs.

### Client support

- Adds DeepSeek Harness (`dsh`) as a supported client:
  `install-skills.sh --client dsh` targets `.dsh/skills/` (project) and
  `$DSH_HOME/skills` (`~/.dsh/skills`, user) — dsh also auto-discovers
  `.agents/skills/` at both levels. Credentials resolve from a
  `dsh-mcp-client` row's `config.env` in `cordis.patch.yml`
  (`!!js process.env.NAME` supported), searched upward from the project
  plus `$DSH_HOME/cordis.patch.yml` and `$DSH_HOME/profiles/*/`.
- New `templates/dsh/cordis.patch.yml` registers `@diviskit/mcp-server`
  through dsh's shipped `dsh-mcp-client` plugin — tools reach the model
  as `mcp__diviskit__diviskit_*`. dsh reads `AGENTS.md` natively, so the
  project-rules template works unchanged.
- Setup docs, README and the AGENTS.md Grundtemplate now cover dsh
  alongside Claude Code, Codex, Devin, opencode and Cursor.

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
`diviskit-mega-menu` skills, and `diviskit-consent` ship in the Pro distribution —
see <https://diviskit.com>.
