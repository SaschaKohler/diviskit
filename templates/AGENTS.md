# AGENTS.md — Diviskit project rules (Grundtemplate)

> Copy this file into the WordPress project root and fill in the `{{...}}`
> placeholders. Devin, opencode, Codex and DeepSeek Harness (dsh) read
> `AGENTS.md` automatically as project rules; Claude Code reads it too (or
> symlink it as `CLAUDE.md`). Delete sections that don't apply to the site.

## Site

- Site: `{{SITE_NAME}}` — `{{WP_URL}}`
- WP user for MCP: `{{WP_USER}}`
- Divi: `{{DIVI_VERSION}}` (5.1+ required)
- Plugins: diviskit-agent `{{AGENT_VERSION}}`, diviskit-pro `{{PRO_VERSION}}`,
  diviskit-design-library, vendokit, vendokit-divi, diviskit-consent
  (delete what isn't installed)

## MCP server config

Project-scope MCP config lives in `.devin/mcp_config.local.json` (Devin —
gitignored, contains the Application Password) or in `opencode.json`
(opencode — gitignore it too, or use `{env:NAME}` references for the
secrets). dsh (DeepSeek Harness) connects via a `dsh-mcp-client` row in
`~/.dsh/profiles/<profile>/cordis.patch.yml` or `~/.dsh/cordis.patch.yml`
— secrets via `!!js process.env.NAME`. Server command
`npx -y --package @diviskit/mcp-server diviskit-mcp`, env `WP_URL` /
`WP_USER` / `WP_APP_PASSWORD` / `WP_CLI_CMD` / `WP_PATH`. Ready examples:
`templates/devin/mcp_config.local.json`, `templates/opencode/opencode.json`
and `templates/dsh/cordis.patch.yml` in the suite.
Devin also reads `~/.config/devin/mcp_config.json` (user scope) and
`~/.codeium/windsurf/mcp_config.json` (legacy); opencode reads
`~/.config/opencode/opencode.json`. Restart the session after
changing MCP config.

## Plugin code — source of truth is the suite repos

Plugin code of the Diviskit suite (diviskit-agent, -pro,
-design-library, vendokit, vendokit-divi, vendokit-support, diviskit-consent,
diviskit-optin) is edited ONLY in the suite repos —
`Active_Projects/diviskit/` (free) and `Active_Projects/diviskit-pro/`
(payware). The copies under `wp-content/plugins/` on this site are
deploy targets: never edit them directly — changes get overwritten by
the next `rsync -a --delete` sync or release. Fix in the repo, sync,
verify here.

Site-level configuration stays on the site and is fine to change:
plugin settings (e.g. the diviskit-consent cookie-button color), Divi
layouts, presets, content.

## Divi 5 block authoring — never violate

Violating these corrupts data or silently strips attributes.

1. **Admin labels** go at `module.meta.adminLabel.desktop.value` — nested in
   `module`, never top-level `meta.adminLabel` (VB ignores it). Label every
   block: sections, rows, columns, groups, content modules. Use descriptive,
   unique labels.
2. **NEVER write Divi `post_content` via `wp post update --post_content`.**
   WP expects slashed input; `wp_unslash()` mangles `\u003c`-escapes into
   `\u005c` corruption. Write via `$wpdb->update` in `ddev wp eval` / WP-CLI
   `eval`, or via `diviskit_*` MCP tools.
3. **NEVER dump `post_content` via `wp db query`** — mysql batch mode
   display-escapes output (`\`→`\\`, newline→`\n`). Use
   `wp post get <ID> --field=post_content`.
4. **Verify after any direct write**: `wp post get <ID> --field=post_content`
   must byte-match the source; the frontend must show no literal `\uXXXX`
   (inside `<script>` JSON it is normal).
5. **`diviskit_module_update` replaces the entire `module` object** — pass
   complete attrs (decoration + advanced + meta), never only `meta`.
6. **`diviskit_tb_layout_update` strips labels from nested modules** (depth
   5+). Write the layout first, then re-apply nested labels via
   `diviskit_module_update`, or write via `$wpdb->update`.
7. **Icon `unicode` = HTML entity** (`&#xf0c9;`), not raw codepoint
   (`\uf0c9` renders empty).
8. **Block JSON must be compact and valid** before upload:
   `json.dumps(attrs, ensure_ascii=False, separators=(',', ':'))`, closing
   `} -->`, self-closing `} /-->`. Malformed JSON makes WordPress strip ALL
   attributes — the block saves as a bare tag.
9. **Validator limits**: `diviskit_validate_blocks` false-positives
   `missing_builder_version` on bloated blocks (~>1.5 KB attrs) and on ANY
   `module.advanced.dropdown` object. Keep blocks compact; validate
   dropdown-bearing markup locally with `json.loads` per block.
10. **After preset/style writes**, flush with `diviskit_meta_flush_cache all`
    — `wp cache flush` does not touch `wp-content/et-cache/`.
11. **Never emit Divi 4 `[et_pb_*]` shortcode markup** — authoring targets
    `<!-- divi/... -->` block comments with JSON attrs. Shortcode output
    parses as freeform text, not modules, and `diviskit_validate_blocks`
    flags it as `legacy_shortcode`.

## Writes go through `dry_run` first

Every `diviskit_*` write tool accepts `dry_run: true` — it returns the plan
envelope without mutating state. Preview first on anything destructive or
structural.

## Project structure (adapt to the site)

- Generators live in `.devin/generators/` → output to `.devin/generators/out/`,
  write via `$wpdb->update` (rule 2).
- Installed skills live under `.devin/skills/` (Devin), `.opencode/skills/`
  (opencode), `.dsh/skills/` (dsh) or `.claude/skills/` (Claude Code) —
  `install-skills.sh --client <client> --scope project` puts them in the
  right place.
- `design-system.json` (design tokens for this site) lives under the
  client's `skills/diviskit-builder/` dir or project docs — keep it out of
  any plugin distribution.

## Key IDs (fill in per site)

| Thing | ID/Slug |
|---|---|
| Front page | `{{...}}` |
| Header TB layout | `{{...}}` |
| Footer TB layout | `{{...}}` |
| Shop / cart / checkout / receipt / account | `{{...}}` |
| License portal | `{{...}}` |

## Logbook

Append dated, verified findings here — block-format quirks, validator
false-positives, canvas/dropdown behavior, plugin version bumps. The rules
above are only worth what the newest verification says.
