---
name: diviskit-builder
description: Use this skill when users want to build, edit, or design pages and layouts on Divi 5 WordPress sites. Triggers include creating landing pages, hero sections, testimonial cards, pricing tables, feature grids, blog listing pages with post loops and pagination; adding or modifying sections on existing pages by page ID; setting up design systems with tokens and presets; theme builder templates for headers, footers, and post/page body layouts; mega menus and site navigation; saving to or loading from the Divi library; auditing or cleaning up presets. Handles animations, hover effects, responsive design, and dynamic content. Also covers special effects like WebGL shader backgrounds and advanced CSS animations via the Diviskit Design Library plugin. Do NOT use for custom PHP/plugins, child theme development, standalone CSS authoring, SQL queries, WooCommerce configuration, SEO setup, standalone Three.js projects without WordPress, or non-Divi builders like Elementor.
compatibility: Requires diviskit-mcp MCP server connected to a WordPress site with Divi 5 and the diviskit-agent plugin active.
metadata:
  author: diviskit
  version: "1.1"
  divi-version: "5.13"
---

# DiviSkit Builder Skill

Build modern, VB-editable Divi 5 pages programmatically via MCP tools.

DiviSkit is an independent skill derived from the Diviskit Free skill (GPL v2)
with auto-generated Tier 3 module maps. No annual license required.

## Creative Direction Contract

For every visual design task, read and follow the creativity system in
[design-guide.md](references/design-guide.md#diviskit-creativity-levels). This is
not optional for page generation.

- **C0 Preserve** — repairs and exact implementation; introduce no new art direction.
- **C1 Distinct** — restrained originality for extensions to an existing site.
- **C2 Signature** — default for every net-new page or layout; create a recognizable, content-derived visual idea.
- **C3 Art Direction** — activate when the user asks for `besonders kreativ`, `experimentell`, `ungewöhnlich`, `KI-Kunst`, a surprising result, or explicitly requests level 3.

The user may set the level with `C0`–`C3`, `Kreativitätslevel 0`–`3`, or equivalent
language. If unclear, use C2 for a new design and C1 for an addition to an
established design system. C3 permits productive strangeness, not broken UX:
accessibility, readability, responsiveness, semantic structure, and conversion
clarity remain hard constraints.

**Binding anti-slop rules:**
1. Do not select a stock page archetype and merely recolor it.
2. Do not use a centered gradient hero, pill-shaped eyebrow, three equal feature cards, bento grid, glass cards, purple/cyan dark palette, or decorative blobs as defaults.
3. Before building, derive a private design thesis from the actual subject, audience, voice, and content. Define one signature device and at least one deliberate rejection of the obvious genre solution.
4. Change composition, hierarchy, rhythm, typography, shape language, image treatment, and motion logic as a system; novelty is not a random effect added to a generic layout.
5. Treat files in `references/patterns/` as technical examples only. Never copy their section sequence or visual grammar unless the user explicitly requests that pattern.
6. Audit the current page/site when available and avoid repeating its dominant hero structure, grid, palette, card treatment, and section rhythm without a content-based reason.
7. Do not claim originality from color changes alone. If the result could fit an unrelated SaaS, portfolio, game, or blog after swapping nouns, redesign it.

## Diviskit harness conventions

Cross-cutting contracts the Divi-builder tools inherit — response envelope
(`{ ok, data?, error: { code, message, hint?, data? } }`), capability handshake,
`dry_run` plan shape, idempotency conventions, and namespace-prefixed error codes
— live in the [diviskit/](../diviskit/SKILL.md) primer skill.

## Reference Files

Read the right file for the task at hand — don't load everything.

| Task | Read first |
|------|-----------|
| Using MCP tools & targeting | [tools.md](references/tools.md) |
| Creating/editing pages | [design-guide.md](references/design-guide.md) creativity level first → [module-formats.md](references/module-formats.md) |
| Copy-paste minimum-valid block snippets | [minimal-snippets.md](references/minimal-snippets.md) (Heading, Text, Button, Blurb, Icon, Image) |
| Module attribute paths | [module-formats.md](references/module-formats.md) — Tier 1+2+3 all included |
| Per-module detailed schema | [modules/](references/modules/) — 87 individual module files |
| Adding CSS classes to modules | [design-effects.md](references/design-effects.md) — uses `module.decoration.attributes`, NOT `className` |
| CSS effects & WebGL shaders | [design-effects.md](references/design-effects.md) |
| Mega menus & navigation | Pro skill `diviskit-mega-menu` (`../diviskit-mega-menu/SKILL.md`) — `divi/dropdown` mega menus + zero-canvas mobile drawer + loop-driven nav links + three-canvas off-canvas panels. If the skill is not installed, use `divi/dropdown` with `parentElement` as the click trigger and `meta.meta.forceVisible: whileInBuilder`. |
| Presets & cleanup | [presets.md](references/presets.md) |
| PageSpeed/performance fixes | [performance.md](references/performance.md) — fonts inline, script defer, mediaelement dequeue, viewport, SSR consent |
| Design system setup | [SKILL.md](#design-system-lifecycle) (below) → [presets.md](references/presets.md) |
| Page templates | [patterns/](references/patterns/) — SaaS landing, more coming |

### Tier 3 module maps — auto-generated

The Tier 3 per-module reference in [module-formats.md](references/module-formats.md)
is **auto-generated** from the live Divi 5.13 schema via
`diviskit_schema_get_module` with `mode: 'dump_all'`.

This replaces the Diviskit Pro Tier 2+3 manually-verified module maps.
Auto-generated maps include:
- Element names (which sub-objects exist per module)
- innerContent shapes (which elements accept content)
- Auto-detected surprises (Image `module.advanced` exception, limited decoration, VB-hidden fields, CSS `!important`)
- CSS selectors per element

**What auto-generation does NOT capture** (requires manual VB verification):
- VB round-trip safety (does VB rewrite the shape on save?)
- Render behavior quirks (e.g., Image border-radius from preset alone)
- Composable-settings locks (e.g., Button blocks transform/filters/animation)
- Dynamic content integration details

For these, use `diviskit_schema_get_module` with `raw: true` for full schema,
or `diviskit_validate_blocks` to catch known silent-failure patterns.

**Re-generation after a Divi update**: Run the update script from anywhere
inside the project — it searches upward for `.devin/mcp_config.local.json`
(server key `diviskit-mcp` or `diviops-mcp`), or pass the project directory
as the first argument:

```bash
~/.config/devin/skills/diviskit-builder/references/modules/update_skills.sh
```

This fetches the schema dump via REST API, regenerates all module maps
(`module-formats.md`, individual `divi_*.md` files, `index.md`, `schema-raw.json`),
and updates the `divi-version` reference in `diviskit-builder/SKILL.md`.
The wp-admin dashboard card (Diviskit Agent → Schema dump) shows the live
schema fingerprint — when it changes after a Divi update, re-run the script.

The generator (`gen_all.py`) and fetch script (`update_skills.sh`) live in
`references/modules/`. The old `gen_module_maps.py` is superseded by `gen_all.py`.

## Workflow Best Practices

1. **Set the creativity level first**: Follow the Creative Direction Contract before choosing a composition or pattern.
2. **Build incrementally**: `create_page` → `section_append` × N
3. **Label every block**: Set `module.meta.adminLabel.desktop.value` on every Section, Row, Column, Group, and content module with a descriptive, unique label.
4. **Validate before saving**: Use `diviskit_validate_blocks` before `diviskit_page_update_content`, `diviskit_tb_layout_update`, or `diviskit_library_save`
5. **Use `diviskit_meta_find_icon`**: Don't guess icon codes — search by keyword
6. **Prefer VB-native**: Use Divi attributes over CSS whenever possible
7. **Font inheritance**: Set global fonts via theme options, skip explicit `family` on modules
8. **Use semantic HTML**: Set `elementType` for SEO/accessibility (`header`, `nav`, `main`, `article`, `footer`)
9. **Always use section/row/column structure**: Wrapperless top-level modules lose styling
10. **Cache invalidation**: All write tools auto-invalidate Divi's CSS cache. If styles appear stale, hard-refresh the browser.

### Verification convention

Skill docs label findings by evidence quality:

| Marker | Meaning |
|---|---|
| **`*(VB-verified YYYY-MM-DD)*`** | User saved the shape in VB; observed as-written |
| **`*(verified YYYY-MM-DD)*`** | Frontend renders correctly, VB round-trip not tested |
| **`*(schema-derived)*`** | Extracted from schema dump, not yet VB-verified |

Auto-generated Tier 3 maps are `*(schema-derived)*` unless manually verified.

### Design Quality Checklist
When generating pages, ALWAYS apply:
- **A declared creative level and design thesis** before implementation
- **A content-derived signature device** appropriate to that level
- **Purposeful motion only** — choose a motion language tied to the concept; static restraint is valid
- **Meaningful hover/focus states** on interactive elements (use `desktop.hover` format where supported)
- **Responsive overrides** (tablet/phone: padding, font sizes, layout re-composition)
- **Visual desktop and mobile checks**, including unusual compositions and Group/card grids
- **Icon glyph-coverage check** — confirm icons paint the correct glyph
- **Use semantic modules** such as `divi/number-counter` when their behavior serves the content
- **Use Group flex** when the chosen composition requires multi-column layout with `flexType` sizing
- **Run the anti-slop similarity test** from [design-guide.md](references/design-guide.md#anti-slop-gate-before-implementation)

## Design System Lifecycle

### How DiviSkit handles styling

Every page you generate uses one of three tiers for any given style value:

| Tier | What it is | When to use |
|------|-----------|-------------|
| **Inline values** | Colors, sizes, fonts hardcoded in each block | Always works. Default. |
| **Token variables** | Divi global tokens referenced via `$variable({...payload...})$` | When global tokens exist in Divi |
| **Presets** | Module-level style templates referenced via UUID | When presets exist + a manifest maps roles to UUIDs |

**DiviSkit generates working, design-complete pages at any tier.**

### First time on a project? Start here

**You don't need to set anything up.** Page generation works out of the box
with inline values using patterns from [design-guide.md](references/design-guide.md).

### Manifest: `.devin/skills/diviskit-builder/design-system.json`

When bootstrap is complete, this per-project file maps preset **role keys**
to site-specific UUIDs. NOT shipped with the skill — lives in the project's
`.devin/skills/diviskit-builder/` directory.

### Project state reference

| State | Tokens? | Presets? | Manifest? | Behavior |
|-------|---------|----------|-----------|----------|
| **Fresh site** (default) | No | No | No | Inline values |
| Branded, not normalized | No (has colors) | No | No | Inline values; bootstrap suggested |
| Partially bootstrapped | Some | No | No | Use available tokens inline |
| Tokens complete, presets pending | Yes | No | No | Tokens via `$variable()$`; inline font/button |
| Fully bootstrapped | Yes | Yes | Yes | Full preset-driven generation |
| Bootstrapped, stale manifest | Yes | Yes | Outdated | Re-run audit + manifest regeneration |

### Bootstrap workflow (optional)

Run this only when you want the full token + preset setup for a site.
**Not required for page generation.**

**Step 1 — Audit existing site:**
1. `diviskit_variable_list` with `type: "colors"`
2. `diviskit_variable_list` with `type: "numbers"`
3. `diviskit_preset_audit`

**Step 2 — Create tokens (if missing):**
1. Ask user for brand colors: primary, secondary, neutral
2. Generate shade scales (50-950) for each family
3. Create color tokens via `diviskit_variable_create`
4. Create number tokens (font sizes, spacings, radii, line heights)

**Step 3 — Create presets (if missing):**
Use `diviskit_preset_create` to write each preset to the D5 registry.

**Step 4 — Generate manifest:**
1. Match preset names to role keys
2. Write `.devin/skills/diviskit-builder/design-system.json`

## Critical Block Format Rules

1. **Always wrap in `divi/placeholder`**: `<!-- wp:divi/placeholder -->...<!-- /wp:divi/placeholder -->`
2. **Always include `builderVersion`**: `"builderVersion":"5.1.1"` on every block
3. **Self-closing blocks**: Use `<!-- wp:divi/text {...} /-->` (with `/-->`) for leaf modules
4. **HTML in innerContent**: Use unicode escapes: `\u003cp\u003e` not `<p>`
5. **Layout display on containers**: Section, Row, Column, Group need `"module":{"decoration":{"layout":{"desktop":{"value":{"display":"block"}}}}}`
6. **Admin labels on every block**: `"module":{"meta":{"adminLabel":{"desktop":{"value":"My Label"}}}}` — the Visual Builder ignores top-level `meta.adminLabel`
7. **`$variable()$` trailing `$` is load-bearing**: tokens must end with `)$`, not just `)`.
8. **Module attrs must not contain `var(--<custom-alias>)`**: attr values hold literal CSS or canonical `$variable({...})$` tokens.

## Safe post_content I/O (DDEV / WP-CLI)

Divi 5 block markup is full of `\u003c`, `\"`, and real newlines. The wrong
read/write path adds or strips an escape layer and the frontend renders
literal `\u003c` text. Use only these paths:

**Reading / dumping (byte-faithful):**
```bash
ddev wp post get <ID> --field=post_content > dump.html   # raw bytes, safe
```
NEVER dump via `ddev wp db query "SELECT post_content ..."` — the mysql
batch-mode output display-escapes (`\`→`\\`, newline→`\n`). Files produced
that way look correct but contain a phantom escape layer; writing them back
corrupts the post.

**Writing:**
- Prefer MCP write tools (`diviskit_page_update_content`, `diviskit_tb_layout_update`)
  — they serialize correctly.
- For a direct byte-preserving write, use `$wpdb->update` via `ddev wp eval`.
  Put the file inside the docroot first (host `/tmp` is NOT container `/tmp`):
  ```bash
  cp dump.html .tmp_write.html
  ddev wp eval 'global $wpdb;
    $wpdb->update("wp_posts",
      ["post_content" => file_get_contents("/var/www/html/.tmp_write.html")],
      ["ID" => 109]);
    clean_post_cache(109);'
  rm .tmp_write.html
  ```
- NEVER use `ddev wp post update <ID> --post_content="$(cat file)"` for Divi
  markup: WP expects *slashed* input and `wp_unslash()` mangles every `\uXXXX`
  escape (`\u003c` → `\u005c` corruption). Only safe if the content has been
  `addslashes()`'d first — prefer `$wpdb->update` instead.

**Verifying after any write:**
```bash
ddev wp post get <ID> --field=post_content | diff - dump.html   # byte-identical
curl -skL "https://<site>/?page_id=<ID>" | grep -c '\\\\u00'      # rendered text: must be 0
```
Escapes inside `<script>` JSON (`diviElementMultiViewData` etc.) are normal —
only literal `\uXXXX` in rendered text/HTML indicates corruption.

## Module Gotchas (Silent Failures)

Full attribute paths in [module-formats.md](references/module-formats.md).
**Copy-paste minimum-valid snippets**: [minimal-snippets.md](references/minimal-snippets.md).
Run `diviskit_validate_blocks` to catch known silent-failure patterns before write.

**Content-shape traps** (block renders but with wrong/missing content):

- **Heading**: explicit `headingLevel` required — omit and Divi renders `<h2>` regardless.
  Path: `title.decoration.font.font.desktop.value.headingLevel`. Allowed `"h1"`–`"h6"`.
- **Button content**: lives on `button.innerContent.desktop.value`, NOT `content.innerContent.*`.
  Must be an **object** `{text, linkUrl}`, not a plain string.
- **Blurb title**: `title.innerContent.desktop.value` is an **object** `{text}`, NOT a plain string.
- **Blurb icon**: when `imageIcon.innerContent.desktop.value.icon` is set, `useIcon: "on"` is required.
- **Body font path**: `content.decoration.bodyFont.body.font.*` (Font Family A, triple-nested).
  Writing `bodyFont.bodyFont.*` is a silent failure.
- **Image**: sizing/spacing on `module.advanced.{sizing, spacing}` — NOT `module.decoration`.
- **Icon**: color at `icon.advanced.color.desktop.{value, hover}` — NOT `icon.decoration.color`.
- **Image border-radius from preset alone doesn't render** — reinforce inline.

See [module-formats.md](references/module-formats.md) for the full per-module reference.
