# Diviskit — Setup Guide

Get from zero to generating Divi 5 pages with Claude Code, Codex, or Devin in ~15 minutes. For project framing, suite components, and the response-contract overview, see the [README](README.md).

> **Beta software.** Diviskit is under active development. Use on production sites at your own discretion. Always back up your WordPress site before running write operations.

## Prerequisites

- **WordPress** 6.5+ with **Divi 5** theme (5.1.0+)
- **PHP** 7.4+
- **Node.js** 22+ (for the MCP server)
- **Claude Code** CLI, **Codex**, or **Devin** installed
- A local or remote WordPress site

## Three independent components

Native Divi authoring requires all three components below. They are installed and
updated independently:

1. **Diviskit Agent** runs inside WordPress and owns the REST capability surface.
2. **`@diviskit/mcp-server`** connects the AI client to that WordPress site.
3. **Diviskit skills** give the client the verified Divi block formats, tool
   contracts, and slice knowledge.

A successful MCP connection proves transport, not native Divi authoring knowledge.
Updating WordPress or npm does not update a manually copied skill. Verify all three
components before the first write and after changing clients, workspaces, or skill
installation methods.

## Step 1: Install the WordPress Plugins

1. Upload `diviskit-agent.zip` via **WP Admin → Plugins → Add New → Upload Plugin** and activate it.
2. Verify: visit `http://your-site.local/wp-json/diviskit/v1/schema/settings` — you should get a 401 (auth required). (`/wp-json/diviops/v1/...` works identically — it is a registered compat alias.)

> **If Divi is not active**, authenticated requests return `503 divi_unavailable`. Unauthenticated requests return 401 first.

Optionally also install **`diviskit-design-library.zip`** — design effects
(`dsk-*` CSS classes, WebGL shaders) used by the `diviskit-builder` skill.

> **Pro ships separately.** `diviskit-pro.zip` (Vendokit `diviskit_vk_*`
> handlers) plus the `diviskit-vendokit` and `diviskit-mega-menu` skills are
> part of the Pro distribution (<https://diviskit.com>). The Pro tools
> self-gate on the handshake — they only appear on sites where the Pro plugin
> is installed and its target plugin is present.

For updates, re-upload the newer ZIP through **Plugins → Add New → Upload Plugin** and choose **Replace current with uploaded**. Application Passwords and MCP config stay unchanged.

## Step 2: Create an Application Password

1. Go to **WP Admin → Users → Your Profile**
2. Scroll to **Application Passwords**
3. Enter a name (e.g. "Claude MCP") and click **Add New Application Password**
4. Copy the generated password

> **Strip the spaces.** WordPress generates passwords like `758r WQ1X URcg GW3s wCwQ QI0V` for readability but accepts them without spaces. Use `758rWQ1XURcgGW3swCwQQI0V` in `claude mcp add` — spaces can be misparsed as separate arguments.

> Save this — you won't see it again.

## Step 3: Register with Your AI Client

The MCP server runs from the published npm package — no clone, no build step.
The server requires **Node.js 22 or newer**.

> **Important**: Choose a unique MCP name that won't conflict with other MCP servers you have registered. Use your site name (e.g., `diviskit-mysite`).

### Claude Code

#### Minimal (REST API only — works with any WordPress host)

```bash
claude mcp add diviskit-mysite \
  --env WP_URL=http://your-site.local \
  --env WP_USER=your-username \
  --env WP_APP_PASSWORD=xxxxXXXXxxxxXXXXxxxxXXXX \
  -- npx -y --package @diviskit/mcp-server diviskit-mcp
```

#### With WP-CLI (Local by Flywheel — enables the `diviskit_meta_wp_cli` tool)

```bash
claude mcp add diviskit-mysite \
  --env WP_URL=http://your-site.local \
  --env WP_USER=your-username \
  --env WP_APP_PASSWORD=xxxxXXXXxxxxXXXXxxxxXXXX \
  --env "WP_PATH=/Users/you/Local Sites/your-site/app/public" \
  -- npx -y --package @diviskit/mcp-server diviskit-mcp
```

> **Use `--env` flags, not the `env` command.** Claude Code's native `--env KEY=VALUE` flags survive copy-paste; the older `-- env KEY=VALUE` form (piping through unix `env`) breaks silently when any value contains a space. Quote any value with spaces using regular double quotes.

> `LOCAL_SITE_ID` is auto-detected from `WP_PATH` — no need to find it manually.

### Codex

Add an MCP server entry to `~/.codex/config.toml`:

```toml
[mcp_servers.diviskit-mysite]
command = "npx"
args = ["-y", "--package", "@diviskit/mcp-server", "diviskit-mcp"]

[mcp_servers.diviskit-mysite.env]
WP_URL = "http://your-site.local"
WP_USER = "your-username"
WP_APP_PASSWORD = "xxxxXXXXxxxxXXXXxxxxXXXX"
```

For a local WordPress site where you want WP-CLI passthrough tools, add under `[mcp_servers.diviskit-mysite.env]`:

```toml
WP_PATH = "/absolute/path/to/wordpress"
```

Restart Codex after changing MCP config.

### Devin

Devin reads MCP config from several locations:

- `~/.config/devin/mcp_config.json` — user scope (global)
- `~/.codeium/windsurf/mcp_config.json` — Windsurf legacy (still read)
- `.devin/mcp_config.local.json` — project scope (gitignored, per-site credentials)

Recommended: keep credentials out of the global config — define the server per
project in `.devin/mcp_config.local.json` and gitignore it:

```json
{
  "mcpServers": {
    "diviskit-mcp": {
      "command": "npx",
      "args": ["-y", "--package", "@diviskit/mcp-server", "diviskit-mcp"],
      "env": {
        "WP_URL": "https://your-site.ddev.site",
        "WP_USER": "admin",
        "WP_APP_PASSWORD": "xxxxXXXXxxxxXXXXxxxxXXXX",
        "WP_CLI_CMD": "ddev wp",
        "WP_PATH": "/absolute/path/to/project",
        "NODE_TLS_REJECT_UNAUTHORIZED": "0"
      }
    }
  }
}
```

`NODE_TLS_REJECT_UNAUTHORIZED=0` is only needed for self-signed local certs (DDEV). Restart Devin after changing MCP config.

### Claude Desktop JSON

```json
{
  "mcpServers": {
    "diviskit-mysite": {
      "command": "npx",
      "args": ["-y", "--package", "@diviskit/mcp-server", "diviskit-mcp"],
      "env": {
        "WP_URL": "http://your-site.local",
        "WP_USER": "your-username",
        "WP_APP_PASSWORD": "xxxxXXXXxxxxXXXXxxxxXXXX"
      }
    }
  }
}
```

### Fallback: Global Install

If the client cannot find `npx`, install the package globally and register the installed bin:

```bash
npm install -g @diviskit/mcp-server@latest

claude mcp add diviskit-mysite \
  --env WP_URL=http://your-site.local \
  --env WP_USER=your-username \
  --env WP_APP_PASSWORD=xxxxXXXXxxxxXXXXxxxxXXXX \
  -- diviskit-mcp
```

If the global bin directory is also missing from the client's `PATH`, register the absolute entrypoint:

```bash
claude mcp add diviskit-mysite \
  --env WP_URL=http://your-site.local \
  --env WP_USER=your-username \
  --env WP_APP_PASSWORD=xxxxXXXXxxxxXXXXxxxxXXXX \
  -- node "$(npm root -g)/@diviskit/mcp-server/dist/index.js"
```

### Local Development Environments

Diviskit connects via standard WordPress REST API and works with any host that exposes WordPress over HTTP with Application Password support.

| Environment | `WP_URL` | WP-CLI setup | Notes |
|-------------|----------|--------------|-------|
| **Local by Flywheel** | `http://site-name.local` | `WP_PATH=/path/to/site/app/public` | Site ID auto-detected |
| **WordPress Studio** | `http://localhost:{port}` | `WP_CLI_CMD="studio wp --path=/path/to/site"` | Port auto-assigned (8881, 8882, …); SQLite |
| **DDEV** | `https://site-name.ddev.site` | `WP_CLI_CMD="ddev wp"` plus `WP_PATH=/path/to/project` | Wrapper runs from `WP_PATH`; set `NODE_TLS_REJECT_UNAUTHORIZED=0` for self-signed certs |
| **wp-env** | `http://localhost:8888` | `WP_CLI_CMD="npx wp-env run cli wp"` plus `WP_PATH=/path/to/project` | Requires `WP_ENVIRONMENT_TYPE=local` (see below) |
| **DevKinsta** | `https://site-name.local` | `WP_CLI_CMD="docker exec -u www-data devkinsta_fpm wp --path=/www/kinsta/public/sitename"` | HTTPS with self-signed certs |
| **Custom / Remote** | Your site URL | `WP_PATH=/path/to/site` or `WP_CLI_CMD="..."` | Works with any WP host |

> **Application Passwords on HTTP:** WordPress requires HTTPS for Application Passwords unless `WP_ENVIRONMENT_TYPE` is set to `'local'`. HTTPS environments (DDEV, DevKinsta) work out of the box. HTTP environments (wp-env, WordPress Studio) need this in `wp-config.php`:
> ```php
> define('WP_ENVIRONMENT_TYPE', 'local');
> ```
> Local by Flywheel sets this automatically.

### Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `WP_URL` | Yes | WordPress site URL (e.g. `http://mysite.local`) |
| `WP_USER` | Yes | WordPress username with Editor or Admin role |
| `WP_APP_PASSWORD` | Yes | Application Password (spaces stripped) |
| `WP_PATH` | No | WordPress filesystem path for Local by Flywheel, or wrapper working directory when `WP_CLI_CMD` needs project context |
| `WP_CLI_CMD` | No | Custom WP-CLI command prefix for containerized environments |
| `LOCAL_SITE_ID` | No | Override auto-detection of Local by Flywheel site ID |
| `DIVISKIT_WP_CLI_ALLOW` | No | Comma-separated list of extended WP-CLI commands to enable ([see below](#wp-cli-security)) |
| `DIVISKIT_WP_CLI_SAFE_FS_ROOT` | No | Restrict WP-CLI filesystem writes to this root |
| `DIVISKIT_WP_CLI_UNSAFE_FS` | No | Opt out of filesystem write restrictions (not recommended) |
| `NODE_TLS_REJECT_UNAUTHORIZED` | No | `0` for self-signed local certs (DDEV) |

> `DIVIOPS_*` variants of the `DIVISKIT_*` variables are still accepted as legacy fallbacks. Canonical names are `DIVISKIT_*`.

### Common Pitfalls

- **Strip spaces from the app password** — covered above; this is the #1 setup snag
- **Use absolute paths** for `WP_PATH` — relative paths break when the client runs from a different directory
- **Unique MCP name** — don't reuse a name from another project
- **Paths with spaces** — wrap the entire `KEY=VALUE` argument in double quotes (e.g. `--env "WP_PATH=/path with spaces/"`)
- **MCP not appearing after registration** — in Claude Code, run `claude mcp list`. If it's not there, `claude mcp remove` and re-add. In Codex, verify the `~/.codex/config.toml` entry and restart Codex. In Devin, restart the session.

## Step 4: Verify Registration

```bash
claude mcp list
```

You should see your MCP server listed with the correct env vars. If anything looks wrong, remove and re-add:

```bash
claude mcp remove diviskit-mysite
claude mcp add diviskit-mysite --env KEY=VALUE ... -- npx -y --package @diviskit/mcp-server diviskit-mcp
```

## Step 5: Test Connection

Restart your client, then run:

```
Use diviskit_meta_ping to verify the MCP is working.
```

You should see your site URL, WordPress version, and Divi version.

Then try:

```
Use diviskit_page_list to show all pages.
```

> **If tools don't appear**: In Claude Code, check `claude mcp list`. In Codex, check `~/.codex/config.toml` and restart. In Devin, check the MCP config file and restart the session. The `npx` command must be reachable on your `PATH`. The `-y --package @diviskit/mcp-server diviskit-mcp` form avoids `npx` prompts and explicitly selects the MCP server bin from the package.

### Pro capabilities (Pro distribution only)

With the Pro plugin active, `diviskit_meta_info` reports the capability
summary — e.g. `slices.vendokit.active: true` and the `diviskit_vk_*` tools on
sites where Vendokit is installed. Gated tools are intentionally omitted when
their requirements aren't met — absence is a gate, not an error.

## Step 6: Load the skills

The skills teach the assistant the verified Divi 5 block formats and the slice tool contracts. This distribution ships the `diviskit-agent` Claude plugin, bound to the free WordPress plugin:

| Claude plugin | WordPress plugin it backs | Skills |
|---|---|---|
| `diviskit-agent` | `diviskit-agent` (Free) | `diviskit` (harness primer), `diviskit-builder`, `diviskit-scf` |

(The Pro distribution adds `diviskit-pro` → `diviskit-vendokit` + `diviskit-mega-menu`.)

### Claude Code

This repo is a Claude plugin marketplace — the manifest lives in
[`.claude-plugin/marketplace.json`](.claude-plugin/marketplace.json):

```bash
claude plugin marketplace add SaschaKohler/diviskit
claude plugin install diviskit-agent@diviskit
```

Verify with `What skills do you have?` — you should see `diviskit`, `diviskit-builder`, and `diviskit-scf` listed.

### Codex, Devin, and other clients (install script)

Clients without a plugin marketplace install skills by copying the bundle's
`skills/` dirs. The shipped installer handles the target paths:

```bash
# Devin — user scope (all projects)
./bin/install-skills.sh --client devin --scope user

# Devin — project scope (single site, run from the project root)
./bin/install-skills.sh --client devin --scope project

# Codex
./bin/install-skills.sh --client codex --scope user
```

Manual equivalent for Devin user scope:

```bash
mkdir -p "$HOME/.config/devin/skills"
cp -R claude/diviskit-agent/skills/* "$HOME/.config/devin/skills/"
```

Restart the client/session after copying.

> Manual copies do not update with WordPress or npm. Replace them from each newer distribution and restart the client. Do not leave a stale manual copy active beside the plugin-managed copy.

### Project rules — `AGENTS.md` Grundtemplate

Copy [templates/AGENTS.md](templates/AGENTS.md) into the WordPress project root
and fill in the marked placeholders (site URL, page IDs, store state).
Devin reads `AGENTS.md` automatically as project rules; Claude Code reads it
too (or symlink it as `CLAUDE.md`). It carries the Divi 5 authoring rules that
prevent silent corruption — admin-label paths, the `post_content` write
restrictions, icon entity format, validator limits — plus the per-project MCP
config shape.

## Step 7: First-run native Divi verification

Ask the agent for a disposable draft, e.g.:

> Create a draft page "MCP smoke" with one section containing a heading, a text module, and a button. Use dry_run first, then write it.

The result must be native section/row/column/heading/text/button modules — not
code modules, page-sized HTML, or iframe layouts. `diviskit_validate_blocks`
readback on the saved `page_id` should report a clean structure.

## WP-CLI Security

`diviskit_meta_wp_cli` runs a curated allowlist of read-only wp-cli commands.
`DIVISKIT_WP_CLI_ALLOW` extends that list with additional commands — each entry
is a full wp-cli command prefix (e.g. `plugin install`). Filesystem-writing
commands are additionally confined to `DIVISKIT_WP_CLI_SAFE_FS_ROOT` unless
`DIVISKIT_WP_CLI_UNSAFE_FS=1` opts out. Keep the defaults on production sites.

## Troubleshooting matrix

| Symptom | Likely cause | Fix |
|---|---|---|
| `401` on handshake | App password has spaces / wrong user | Strip spaces; re-check `WP_USER` |
| `503 divi_unavailable` | Divi theme inactive | Activate Divi 5.x |
| Tools list empty | MCP server failed to start | `claude mcp list`, check `npx` on PATH, Node 22+ |
| `diviskit_vk_*` absent | Pro gate closed | `diviskit_meta_info` → `pro_active`, `vendokit.present`, `active_modules.vendokit` |
| Frontend CSS stale after preset write | `et-cache` not flushed | `diviskit_meta_flush_cache` |
| `wp-cli` tools absent | No `WP_PATH`/`WP_CLI_CMD` | Set per environment table above |
| HTTPS cert errors on DDEV | Self-signed cert | `NODE_TLS_REJECT_UNAUTHORIZED=0` |
