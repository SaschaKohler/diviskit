# Diviskit MCP Server

**An AI harness for WordPress site authoring — Divi-native today, WordPress-wide by design.**

`@diviskit/mcp-server` is the Node.js MCP server of the Diviskit suite. It gives
Devin, Claude Code, Codex, Claude Desktop, and other MCP clients a typed control
layer over WordPress site state, dispatching to the **Diviskit Agent** plugin
for Divi 5 page authoring, SCF and CPT data models, design tokens, presets,
library and Theme Builder templates, site audits, and safe WP-CLI passthrough.
Pairs with the `diviskit-builder` skill so the agent applies Divi's block format
and design rules correctly.

```
AI client <-> MCP Server (stdio) <-> WordPress REST API <-> Diviskit Agent plugin
```

## Use cases

- **Page building (Divi authoring)** — create + edit Divi pages, sections, modules, canvases via prompt; preset-driven design system reuse; Theme Builder layouts and templates.
- **Commerce automation** — Vendokit products, variations, orders, licenses and gateways via capability-gated Pro tools.
- **SCF setup + management** — provision Secure Custom Fields field groups, sync schemas, export/import field group definitions; SCF data model becomes a tool surface, not an admin-UI flow.
- **CPT + post population** — register custom post types via wp-cli passthrough; bulk-populate posts and pages across any post type, not just Divi-built ones.
- **Data model reasoning** — schema introspection across Divi modules + SCF field groups + post meta; ask the agent what fields a post type carries, what attributes a module accepts, what tokens are defined.
- **WordPress site auditing** — preset audits, design-token usage scans, orphan detection (presets, variables, dangling references); broader site surveys via wp-cli (`wp option list`, `wp post list --format=json`, `wp user list`).
- **Hybrid sites (Divi + custom PHP)** — Divi authors the marketing pages; custom PHP templates handle dynamic ones (CPT listings, single-post views, member portals); design tokens harmonized across both surfaces via CSS custom properties driven from the Divi variable system.

## Quick start

### 1. Install the WordPress plugin

Download the free **Diviskit Agent** plugin from
[diviskit.com](https://diviskit.com) and install it via
**WP Admin → Plugins → Add New → Upload Plugin**. Requires Divi 5.1+ on
WordPress 6.5+.

The npm MCP server updates through npm. The WordPress plugin updates through
the normal WordPress plugin update flow. Server and plugin versions are
independent — compatibility is negotiated per-capability at handshake.

### 2. Create an Application Password

In **WP Admin → Users → Your Profile → Application Passwords**:

- Enter a name (e.g. "Diviskit MCP")
- Click "Add New Application Password"
- Copy the generated password and strip the spaces

Or via WP-CLI: `wp user application-password create admin "Diviskit MCP"`.

### 3. Register the MCP server

Requires Node.js 22 or newer.

Generic MCP JSON config (Devin `.devin/mcp_config.local.json`, Claude Desktop,
Windsurf, etc.):

```json
{
  "mcpServers": {
    "diviskit-mcp": {
      "command": "npx",
      "args": ["-y", "--package", "@diviskit/mcp-server", "diviskit-mcp"],
      "env": {
        "WP_URL": "https://your-site.example",
        "WP_USER": "your-wp-username",
        "WP_APP_PASSWORD": "xxxxXXXXxxxxXXXXxxxxXXXX"
      }
    }
  }
}
```

Claude Code:

```bash
claude mcp add diviskit-mcp \
  --env WP_URL=https://your-site.example \
  --env WP_USER=your-wp-username \
  --env WP_APP_PASSWORD=xxxxXXXXxxxxXXXXxxxxXXXX \
  -- npx -y --package @diviskit/mcp-server diviskit-mcp
```

Codex `~/.codex/config.toml`:

```toml
[mcp_servers.diviskit-mcp]
command = "npx"
args = ["-y", "--package", "@diviskit/mcp-server", "diviskit-mcp"]

[mcp_servers.diviskit-mcp.env]
WP_URL = "https://your-site.example"
WP_USER = "your-wp-username"
WP_APP_PASSWORD = "xxxxXXXXxxxxXXXXxxxxXXXX"
```

The explicit `--package` + bin form is required: the tarball ships two bins
(`diviskit-mcp`, `diviskit-preset`), so npx
cannot infer which one to run from the package name alone.

Then ask your AI client: **"List the pages on my site."** It calls
`diviskit_page_list` and renders the result. This proves the plugin and MCP
transport; it does not prove that the client has loaded Divi authoring
knowledge — for that, install the `diviskit-builder` skill (step 4).

### 4. Load the `diviskit-builder` skill

The skill teaches the client Divi's block format, design-token system, and the
write/validate/readback contract. Install it into your client's skill
directory, restart, and confirm it is listed before authoring pages.

### 5. Prove native module authoring

Ask the client to build a small section — a heading plus a button — and verify
the result in the Visual Builder. Stop if the skill or required tools are
unavailable. Do not accept page-sized HTML, Code modules, iframe layouts, or
structural HTML in text/container fields as a fallback for editable native
Divi modules.

## Example workflow

> **You:** Create a hero section on a new page called "Spring Launch" with a heading, subheading, and a CTA button. Use my brand colors.

The agent orchestrates a few tool calls in sequence:

1. `diviskit_global_color_list` — discovers your brand palette.
2. `diviskit_template_list` / `diviskit_template_get` — pulls a verified hero template that matches the request.
3. `diviskit_validate_blocks` with inline `content` — confirms the constructed hero markup is well-formed before any write.
4. `diviskit_page_create` — creates `Spring Launch` as a draft using those exact validated bytes.
5. `diviskit_validate_blocks` with the saved `page_id` — verifies persisted readback.
6. `diviskit_render_preview` — returns the rendered HTML so you can verify before publishing. Accepts inline `content` or a `page_id` to preview an existing page.

## Tools at a glance

The server exposes **~94 always-on tools** across these categories:

| Category | Use case | Tool prefixes |
|----------|----------|---------------|
| Page authoring | Create, edit, restructure pages | `page_*`, `section_*`, `module_*` |
| Design system | Manage colors, fonts, variables, presets | `variable_*`, `global_color_*`, `global_font_*`, `preset_*` |
| Library + templates | Reusable layouts + Theme Builder | `library_*`, `template_*`, `tb_*` |
| WordPress menus | Author reusable nav menus and theme-location assignments | `menu_*` |
| Semantic SEO metadata | Inspect provider support and author two explicit TSF text fields with checksum/readback guards | `seo_*` |
| Schema introspection | Module attribute discovery | `schema_*` |
| Canvas / off-canvas | Popups, modals, menus | `canvas_*` |
| SCF integration | Secure Custom Fields sync | `scf_*` |
| Render + validate | Preview HTML, validate block markup | `render_preview`, `validate_blocks` |
| WP-CLI passthrough | Escape hatch for site ops | `meta_wp_cli` |
| Cache + meta | Connection probe, identity, icons, cache flush | `meta_*` |

All tool names carry the `diviskit_` prefix (e.g. `diviskit_page_list`).
Use `diviskit_meta_info` as the preflight before authoring work — it returns
`server_version`, a numeric `tool_count`, a `tools` catalog summary
(`registered_total`, always-on count, Pro possible/registered counts by
target), `plugins` version records, plus handshake and slice state.

Additional **conditionally-registered Pro tools** appear only on sites that
have **Diviskit Pro** active alongside the target coverage plugin:

| Category | Conditional gate | Tool names |
|----------|------------------|------------|
| Vendokit commerce (products, variations, orders, licenses, gateways, status) | Pro plugin + Vendokit module enabled | `diviskit_vk_*` (18 tools) |

When the gates are not satisfied, the tools simply don't appear on the MCP
surface — no error envelope, no missing-capability hint. See the
`diviskit-vendokit` and `diviskit-scf` skill bundles for the
operator-side guides.

## Bundled CLI — `diviskit-preset`

The package ships a standalone command-line preset emitter, `diviskit-preset`,
that produces byte-canonical Divi 5.5+ preset JSON gated by the verified-attrs
registry (`data/verified-attrs.json`). It is independent of the MCP stdio
server — run it directly:

| Command | Emits |
|---|---|
| `diviskit-preset button [options]` | `divi/button` group preset |
| `diviskit-preset heading-font [options]` | `divi/font` group preset for `divi/heading` (Pattern A — Google Fonts — or Pattern B — local-hosted) |
| `diviskit-preset text-body-font [options]` | `divi/font-body` group preset for `divi/text` — **Pattern A (Google Fonts) only**; Pattern B for body-text has no registered canonical shape and is refused |
| `diviskit-preset spacing [options]` | `divi/spacing` group preset (currently `divi/section` only; padding + margin, desktop state). Other module cells are `SCHEMA_OBSERVED` and refused at the gate |

```bash
npx -y --package @diviskit/mcp-server diviskit-preset button \
  --name "Primary" --bg-color gcid-primary-color \
  --bg-color-hover gcid-secondary-color --radius 8px \
  --font-family Inter --font-weight 600 --font-color gcid-body-color
```

`--dry-run` (the default) composes and prints the canonical JSON with no
credentials and no network. `--apply` posts to the `/preset/create` REST route,
reusing the same `WP_URL` / `WP_USER` / `WP_APP_PASSWORD` env vars. The CLI's
coverage is intentionally narrow: only the (module, group, variant)
combinations whose canonical shape is VB-verified in the registry are
emittable.

## Response contract

Tools return a standardized envelope so clients can branch on `ok` and
machine-readable `error.code` without parsing freeform messages:

```jsonc
// Success
{ "ok": true, "data": <payload> }
// Failure
{ "ok": false, "error": { "code": "<code>", "message": "<human>", "hint": "<optional>" } }
```

### Standard error codes

| code | HTTP | meaning |
|---|---|---|
| `not_found` | 404 | Target ID does not resolve |
| `invalid_input` | 400 | Schema violation, malformed args |
| `validation_failed` | 400 | `validate_blocks`-detected shape error |
| `conflict` | 409 | Uniqueness collision |
| `forbidden` | 403 | Row-level WordPress auth signal |
| `capability_missing` | 412 | Connected plugin does not advertise the capability required by this tool |
| `wp_error` | 500 | Underlying WordPress error |
| `divi_error` | 500 | Divi-specific error (block parser, validator, etc.) |

Namespaces extend the vocabulary using the `<namespace>.<reason>` convention —
e.g. `meta_wp_cli.command_failed`, `scf.not_configured`,
`preset.bucket_mismatch`. Some tools attach structured `error.data` (exit
codes, conflicting fields, checksums, rollback evidence) — the shape is
documented in each tool's description.

### `dry_run` plan shape

Every write tool accepts `dry_run: boolean` (default `false`). When `true`,
the response carries a uniform plan shape and no state is mutated:

```json
{
  "ok": true,
  "data": {
    "dry_run": true,
    "plan": {
      "summary": "Would update 1 attr path(s) on module 'Hero CTA' (page #42, type divi/button).",
      "changes": [
        { "kind": "module.update", "target": "page#42/divi/button/Hero CTA#button.decoration.font.font.desktop.value.color", "before": "#000", "after": "#ff0066" }
      ]
    }
  }
}
```

Selected guarded post-content write tools also accept `backup: true`: the
plugin stores an option-backed rollback snapshot before writing and returns
`data.backup` evidence. `diviskit_rollback_snapshot_restore` restores those
snapshots only to their captured target and refuses on content drift.

Every tool's `_meta.idempotent` field documents its repeat-call behavior.

## Configuration

### Environment variables

| Variable | Required | Description |
|----------|----------|-------------|
| `WP_URL` | Yes | WordPress site URL (e.g. `https://mysite.ddev.site`) |
| `WP_USER` | Yes | WordPress username with Editor or Admin role |
| `WP_APP_PASSWORD` | Yes | Application Password (spaces stripped) |
| `WP_PATH` | No | WordPress filesystem path for Local by Flywheel, or wrapper working directory when `WP_CLI_CMD` needs project context |
| `WP_CLI_CMD` | No | Custom WP-CLI command prefix for containerized environments (e.g. `ddev wp`, `npx wp-env run cli wp`) |
| `LOCAL_SITE_ID` | No | Override auto-detection of Local by Flywheel site ID |
| `DIVISKIT_WP_CLI_ALLOW` | No | Opt-in extended WP-CLI commands (comma-separated subset, or `*` for all extended commands) |
| `DIVISKIT_WP_CLI_SAFE_FS_ROOT` | No | Path to constrain filesystem-touching wp-cli commands. **Required** in `WP_CLI_CMD` wrapper mode |
| `DIVISKIT_WP_CLI_UNSAFE_FS` | No | Set to `1` to disable filesystem flag validation entirely |

### Containerized environments

The server connects via the standard WordPress REST API and works with any
environment that exposes WordPress over HTTP with Application Password support
— Local by Flywheel, DDEV, wp-env, WordPress Studio, DevKinsta, custom hosts.
For DDEV, set `WP_CLI_CMD=ddev wp` (plus `DIVISKIT_WP_CLI_SAFE_FS_ROOT` for
filesystem-touching commands). For self-signed local certs, add
`NODE_TLS_REJECT_UNAUTHORIZED=0` to the server env.

## Troubleshooting

- **"Missing required environment variable(s)"** — ensure `WP_URL`, `WP_USER`, `WP_APP_PASSWORD` are all set in the server env.
- **`npx` fails with "could not determine executable to run"** — use `npx -y --package @diviskit/mcp-server diviskit-mcp`; this explicitly selects the MCP server bin.
- **"Connection failed"** — verify the plugin is active by POSTing to `{WP_URL}/wp-json/diviskit/v1/handshake` with your credentials; a healthy site answers `{ "compatible": true, "server": "diviskit", ... }`.
- **"This tool requires plugin capability"** — the connected plugin does not advertise the capability this tool needs. Server and plugin versions are independent; install a compatible plugin version, then reconnect or restart the MCP session to refresh the handshake.
- **Preset edits not visible on the frontend** — Divi serves frontend CSS from `wp-content/et-cache/{post_id}/`, which `wp cache flush` doesn't touch. Use `diviskit_meta_flush_cache` after preset writes; `post_id` mode also sweeps that exact directory and reports `post_dir_sweep` evidence.

## Requirements

- Node.js >= 22.0.0
- PHP >= 7.4
- WordPress >= 6.5
- Divi 5 theme active
- Diviskit Agent WordPress plugin installed and active

## License

MIT — see [LICENSE](./LICENSE).
