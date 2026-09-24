# Diviskit

**An AI harness for WordPress site authoring — Divi-native today, WordPress-wide by design.**

[![npm](https://img.shields.io/npm/v/@diviskit/mcp-server.svg?label=%40diviskit%2Fmcp-server)](https://www.npmjs.com/package/@diviskit/mcp-server)
[![Divi 5](https://img.shields.io/badge/Divi-5.1.0%2B-7E3DD3.svg)](https://www.elegantthemes.com/gallery/divi/)

Diviskit gives Claude Code, Codex, Devin, Claude Desktop, and other MCP clients a typed control layer over WordPress site state. It pairs an MCP server, the Diviskit Agent WordPress plugin, and skill knowledge so AI agents can author Divi pages, inspect schemas, manage design tokens, work with SCF/CPT data models, run safe WP-CLI operations, and drive the Vendokit commerce stack.

Diviskit Agent is a GPL-licensed fork of the DiviOps Agent. It serves the identical REST contract on the canonical `diviskit/v1` namespace plus a `diviops/v1` compatibility alias. Divi is a registered trademark of Elegant Themes, Inc. Diviskit is not affiliated with or endorsed by Elegant Themes.

```
Claude Code / Devin ◄──► MCP Server (stdio) ◄──► WordPress REST API ◄──► Diviskit Agent plugin
                                                    ▲
                                                    │
                                        diviskit-builder skill
                                        (block format + design rules)
```

> **Beta software.** Diviskit is under active development. Use on production sites at your own discretion. Always back up your WordPress site before running write operations.

## What's in this distribution

| Component | What it is | Where it lives |
|---|---|---|
| **Diviskit Agent** WordPress plugin | REST API endpoints for Divi page data, section targeting, block validation, preset management. The contract layer between WordPress + Divi and the MCP server. | `diviskit-agent.zip` (source: `plugins/diviskit-agent/`) |
| **Diviskit Design Library** plugin | Optional. CSS entrance animations, gradient text, glass effects, Three.js WebGL shaders (`dsk-*` classes). | `diviskit-design-library.zip` (source: `plugins/diviskit-design-library/`) |
| **`@diviskit/mcp-server`** | Node.js MCP server bridging MCP clients to WordPress. Distributed via npm — no clone, no build. Source included for reference. | `npx -y --package @diviskit/mcp-server diviskit-mcp` (source: `diviskit-server/`) |
| **Skill bundle `diviskit-agent`** | `diviskit` harness primer, `diviskit-builder` (block formats + design rules + per-module maps), `diviskit-scf` (SCF/ACF automation). | `claude/diviskit-agent/skills/` |
| **Diviskit Pro** *(separate distribution)* | `diviskit-pro` plugin (Vendokit `diviskit_vk_*` handlers) + `diviskit-vendokit` and `diviskit-mega-menu` skills. | <https://diviskit.com> |
| **Skill installer** | Copies skill bundles for Codex/Devin users without a plugin marketplace. | `bin/install-skills.sh` |
| **Project templates** | `AGENTS.md` Grundtemplate + Devin `mcp_config.local.json` example. | `templates/` |

The WordPress plugin, npm MCP server, and client-side skills are three independent
components. WordPress and npm updates do not install or refresh a manually copied
skill. A working MCP tool call proves connectivity only; native Divi authoring also
requires a current `diviskit-builder` skill in the active client session.

## Use cases

- **Page building (Divi authoring)** — create + edit Divi pages, sections, modules, canvases via prompt; preset-driven design system reuse; Theme Builder layouts and templates.
- **SCF setup + management** — provision Secure Custom Fields field groups, sync schemas, export/import field group definitions.
- **CPT + post population** — register custom post types via wp-cli passthrough; bulk-populate posts and pages across any post type.
- **Commerce operations** — Vendokit products, variations, orders, licenses, and gateways as an MCP tool surface (`diviskit_vk_*`, Pro).
- **Data model reasoning** — schema introspection across Divi modules + SCF field groups + post meta.
- **WordPress site auditing** — preset audits, design-token usage scans, orphan detection; broader site surveys via wp-cli.

## Quick start

The first three steps prove connectivity; steps 4 and 5 establish native Divi
authoring readiness. For containerized environments, HTTPS configuration, and
troubleshooting, see [SETUP.md](SETUP.md).

### 1. Install the WordPress plugins

Upload **`diviskit-agent.zip`** via **WP Admin → Plugins → Add New → Upload Plugin**, then activate it. Requires Divi 5.1+ on WordPress 6.5+.

Verify: visit `http://your-site.local/wp-json/diviskit/v1/schema/settings` — you should get a 401 (auth required).

Optionally also install **`diviskit-design-library.zip`** — design effects
(`dsk-*` CSS classes, WebGL shaders) used by the `diviskit-builder` skill.

> **Pro + Vendokit ship separately.** The `diviskit_vk_*` commerce tools,
> `diviskit-vendokit` and `diviskit-mega-menu` skills live in the Pro
> distribution (<https://diviskit.com>). They are handshake-gated — on sites
> without the Pro plugin they simply don't appear on the MCP surface.

**Plugin updates:** replace the plugin ZIP through **Plugins → Add New → Upload Plugin** and choose **Replace current with uploaded**. Your Application Password and MCP config stay unchanged. Diviskit Pro does not use license-key activation — the Pro plugin's vendokit module toggle lives under **wp-admin → Diviskit → Pro** (option `diviskit_pro_module_vendokit`, defaults ON when Vendokit is present).

### 2. Create an Application Password

In **WP Admin → Users → Your Profile → Application Passwords**:

- Enter a name (e.g. "Claude MCP")
- Click "Add New Application Password"
- **Strip the spaces** from the generated password — WordPress shows `758r WQ1X URcg ...` for readability but accepts the spaceless form, which avoids argument-parsing surprises in `claude mcp add`.

### 3. Register the MCP server

Requires Node.js 22 or newer.

Claude Code:

```bash
claude mcp add diviskit-mysite \
  --env WP_URL=http://your-site.local \
  --env WP_USER=your-wp-username \
  --env WP_APP_PASSWORD=xxxxXXXXxxxxXXXXxxxxXXXX \
  -- npx -y --package @diviskit/mcp-server diviskit-mcp
```

For Local by Flywheel (enables the `diviskit_meta_wp_cli` tool), add `--env "WP_PATH=/Users/you/Local Sites/your-site/app/public"`. For DDEV, use `--env WP_CLI_CMD="ddev wp"` plus `--env "WP_PATH=/path/to/project"`.

Codex `~/.codex/config.toml`:

```toml
[mcp_servers.diviskit-mysite]
command = "npx"
args = ["-y", "--package", "@diviskit/mcp-server", "diviskit-mcp"]

[mcp_servers.diviskit-mysite.env]
WP_URL = "http://your-site.local"
WP_USER = "your-wp-username"
WP_APP_PASSWORD = "xxxxXXXXxxxxXXXXxxxxXXXX"
```

Devin `mcp_config.json` (user scope `~/.config/devin/mcp_config.json`, or project scope `.devin/mcp_config.local.json`):

```json
{
  "mcpServers": {
    "diviskit-mcp": {
      "command": "npx",
      "args": ["-y", "--package", "@diviskit/mcp-server", "diviskit-mcp"],
      "env": {
        "WP_URL": "https://your-site.ddev.site",
        "WP_USER": "your-wp-username",
        "WP_APP_PASSWORD": "xxxxXXXXxxxxXXXXxxxxXXXX",
        "WP_CLI_CMD": "ddev wp",
        "WP_PATH": "/absolute/path/to/project",
        "NODE_TLS_REJECT_UNAUTHORIZED": "0"
      }
    }
  }
}
```

Restart your client, then ask: **"List the pages on my site."** The assistant calls
`diviskit_page_list` and renders the result. This proves connectivity; complete the
skill install and native-module smoke below before authoring content.

### 4. Load the skills

The skills teach the assistant the correct Divi 5 block format and the slice-specific tool contracts. Without them, the agent guesses attr formats and produces broken pages.

The distribution ships the `diviskit-agent` Claude plugin, bound to the free
WordPress plugin — `skills/`: `diviskit` (harness primer), `diviskit-builder`,
`diviskit-scf`.

```bash
claude plugin marketplace add SaschaKohler/diviskit
claude plugin install diviskit-agent@diviskit
```

Verify with `What skills do you have?` — you should see `diviskit`, `diviskit-builder`, and `diviskit-scf` listed.

(Pro buyers install `diviskit-pro@diviskit` from the Pro distribution —
`diviskit-vendokit` + `diviskit-mega-menu` skills.)

For Codex, Devin, and other clients without a plugin marketplace, use the
bundled installer:

```bash
./bin/install-skills.sh --client devin --scope user
./bin/install-skills.sh --client devin --scope project   # per-site install
./bin/install-skills.sh --client codex --scope user
```

…it copies `claude/diviskit-agent/skills/*` into the client's skills directory
(`~/.config/devin/skills/`, `~/.codex/skills/`, or project `.devin/skills/` /
`.codex/skills/` with `--scope project`). Manual copies do not update with
WordPress or npm — re-run the installer after each update and restart the
client.

Also copy [templates/AGENTS.md](templates/AGENTS.md) into your WordPress
project root — Devin (and Claude Code) read it automatically and it carries
the Divi 5 safety rules the MCP tools alone can't enforce.

### 5. Verify native Divi authoring

Ask the agent to build a disposable draft page with a heading, text, and button.
It must produce and report native section, row, column, heading, text, and button
modules. Code modules, page-sized HTML, iframe layouts, and structural HTML stuffed
into text/container fields are not acceptable fallbacks.

## Example workflow

> **You:** Create a hero section on a new page called "Spring Launch" with a heading, subheading, and a CTA button. Use my brand colors.

The agent orchestrates a few tool calls in sequence:

1. `diviskit_global_color_list` — discovers your brand palette.
2. `diviskit_template_list` / `diviskit_template_get` — pulls a verified hero template that matches the request.
3. `diviskit_validate_blocks` with inline `content` — confirms the constructed hero markup is well-formed before any write.
4. `diviskit_page_create` — creates `Spring Launch` as a draft using those exact validated bytes.
5. `diviskit_validate_blocks` with the saved `page_id` — verifies persisted readback.
6. `diviskit_render_preview` — returns the rendered HTML so you can verify before publishing.

## Tools at a glance

The suite exposes **111 tools** (`diviskit_*`), 18 of which are the Pro-only
`diviskit_vk_*` Vendokit surface. Per-tool descriptions, request shapes, and
response payloads live in the server [README](diviskit-server/README.md).

| Category | Use case | Tool prefixes |
|---|---|---|
| Page authoring | Create, edit, restructure pages | `page_*`, `section_*`, `module_*` |
| Design system | Manage colors, fonts, variables, presets | `variable_*`, `global_color_*`, `global_font_*`, `preset_*` |
| Library + templates | Reusable layouts + Theme Builder | `library_*`, `template_*`, `tb_*` |
| Schema introspection | Module attribute discovery | `schema_*` |
| Canvas / off-canvas | Popups, modals, menus | `canvas_*` |
| Menus | Navigation menus + items | `menu_*` |
| SCF integration | Secure Custom Fields sync | `scf_*` |
| SEO | Meta + sitemap helpers | `seo_*` |
| Vendokit commerce (Pro) | Products, variations, orders, licenses, gateways | `vk_*` |
| Render + validate | Preview HTML, validate block markup | `render_preview`, `validate_blocks` |
| WP-CLI passthrough | Escape hatch for site ops | `meta_wp_cli` |
| Cache + meta | Connection probe, identity, icons, cache flush | `meta_*` |

## Response contract

Tools return a standardized envelope. The shape lets clients branch on `ok` and machine-readable `error.code` without parsing freeform messages.

```jsonc
// Success
{ "ok": true, "data": <payload> }
// Failure
{ "ok": false, "error": { "code": "<code>", "message": "<human>", "hint": "<optional>" } }
```

Every write tool accepts `dry_run: boolean` (default `false`). When `true`, the response carries a uniform plan shape and no state is mutated. See the server [README](diviskit-server/README.md) for the plan envelope and per-tool `_meta.idempotent` markers.

## Free vs Pro

Diviskit is a harness. The Free surface carries core Divi authoring; Pro adds the
Vendokit coverage slice — skill knowledge plus plugin handlers.

| | Free | Pro |
|---|:---:|:---:|
| `diviskit-agent` WordPress plugin | ✓ | ✓ (same binary) |
| `diviskit-pro` WordPress plugin | — | ✓ |
| `diviskit-design-library` plugin | ✓ | ✓ (same binary) |
| `@diviskit/mcp-server` on npm | ✓ | ✓ (same package) |
| Skills: `diviskit` primer, `diviskit-builder` (incl. generated per-module maps), `diviskit-scf` | ✓ | ✓ |
| Skill: `diviskit-vendokit` coverage guide | — | ✓ |
| Skill: `diviskit-mega-menu` (dropdown mega menus, off-canvas headers) | — | ✓ |
| `diviskit_vk_*` tool surface (18 tools) | — | ✓ (handshake-gated) |

Pro tools are gated by the capability handshake, not feature-flagged in the MCP
server: when `diviskit-pro` or the Vendokit plugin is absent (or the vendokit
module toggle is off), the `diviskit_vk_*` tools simply do not appear on the
MCP surface. Vendokit itself is not distributed yet — it currently powers the
diviskit.com store and will ship separately once released.

## Requirements

- Node.js 22+
- PHP 7.4+
- WordPress 6.5+
- Divi 5.1.0+ theme active
- Diviskit Agent WordPress plugin installed and active

## Troubleshooting

Common quick fixes:

- **401 Unauthorized** — strip spaces from the Application Password; verify `WP_USER` and `WP_APP_PASSWORD`.
- **503 `divi_unavailable`** — Divi 5 theme is not active.
- **MCP not appearing** — `claude mcp list`; if absent, `claude mcp remove` and re-add. Fully restart the client (not just the window).
- **Preset edits not visible on the frontend** — Divi serves frontend CSS from `wp-content/et-cache/{post_id}/`, which `wp cache flush` doesn't touch. Use `diviskit_meta_flush_cache` after preset writes.
- **VB shows raw `$variable()$`** — dynamic content binding rendered as text; click the chip to edit it inline.
- **`diviskit_vk_*` tools missing** — check `diviskit_meta_info`: `pro_active`, `available_targets.vendokit.present`, and `active_modules.vendokit` must all be true.

Full troubleshooting matrix and environment-specific setup (DDEV, wp-env, WordPress Studio, DevKinsta) is in [SETUP.md](SETUP.md).

## Documentation

- **[SETUP.md](SETUP.md)** — full onboarding walkthrough (containerized envs, HTTPS, environment variables, WP-CLI security)
- **[diviskit-server/README.md](diviskit-server/README.md)** — MCP server reference (response contract, error codes, `dry_run` plan shape, per-tool registration)
- **[claude/diviskit-agent/skills/diviskit-builder/SKILL.md](claude/diviskit-agent/skills/diviskit-builder/SKILL.md)** — block format rules, design patterns, workflow guidance

## License

Mixed per component — see [LICENSE](LICENSE). WordPress plugins are GPL-2.0+;
the MCP server and skills are MIT.
