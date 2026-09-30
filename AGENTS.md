# AGENTS.md — diviskit (free suite repo)

This repo is the **source of truth** for the free Diviskit plugins:

- `plugins/diviskit-agent` — REST bridge / MCP backend
- `plugins/diviskit-design-library` — design effects library
- `plugins/sk-mailerlite-doi` — newsletter double-opt-in

Also here: `diviskit-server/` (MCP server source), `claude/` (skill
artifacts), `templates/` (site AGENTS.md + MCP config templates),
`bin/` (release.sh, install-skills.sh, sync-skills.sh).

## Rules

- **Edit plugin code HERE, never on installed site copies.** Sites get
  `rsync -a --delete` copies of `plugins/<slug>/` — direct edits on
  `wp-content/plugins/` of any DDEV/live site are lost on the next
  sync. Site-level configuration (plugin settings, Divi layouts,
  content) stays on the site.
- Free plugins additionally mirror into `diviskit-pro/plugins/` (pro
  suite bundle) — `bin/release.sh` handles this automatically
  (BUNDLE=1 for agent + design-library). sk-mailerlite-doi is
  free-repo only.
- Remote: `git@github.com:SaschaKohler/diviskit.git`
- Dev/test site: `diviskit-shop` (`https://diviskit-shop.ddev.site`,
  sibling dir `../diviskit-shop`) — the vendokit store.
- Release: `bin/release.sh <slug> <version> [--push] [--staging]`.
