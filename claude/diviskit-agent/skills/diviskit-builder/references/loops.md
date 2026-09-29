# Loop Elements (`module.advanced.loop`)

*(VB-verified 2026-09-29, Divi 5.14 — TB footer layout, divi/text)*

Any Divi 5 module can become a loop element: the module renders once per
query result, and its fields resolve against the current loop item via
`type:"content"` variables. Verified on `divi/text` (footer link lists) and
`divi/link` (nav links — see
[diviskit-mega-menu/dropdown-pattern.md](../../diviskit-mega-menu/references/dropdown-pattern.md)).

## Anatomy (divi/text as link list)

```json
<!-- wp:divi/text {
  "module": {
    "meta": {"adminLabel": {"desktop": {"value": "Footer Product Links"}}},
    "decoration": {...},
    "advanced": {
      "loop": {"desktop": {"value": {
        "enable": "on",
        "loopId": "loop-ehli06jhdf",
        "queryType": "post_types",
        "subTypes": [{"value": "page", "label": "Pages"}],
        "includePostWithSpecificTerms": "",
        "excludePostWithSpecificTerms": "",
        "includeSpecificPosts": [
          {"value": "17", "label": "SK Consent"},
          {"value": "18", "label": "Vendokit"},
          {"value": "19", "label": "Diviskit Agent"}
        ],
        "excludeSpecificPosts": "",
        "orderBy": "date"
      }}},
      "link": {"desktop": {"value": {
        "url": "$variable({\"type\":\"content\",\"value\":{\"name\":\"loop_post_link\",\"settings\":{\"before\":\"\",\"after\":\"\",\"loop_position\":\"\",\"text\":\"post_title\",\"custom_text\":\"\"}}})$"
      }}}
    }
  },
  "content": {"innerContent": {"desktop": {"value":
    "$variable({\"type\":\"content\",\"value\":{\"name\":\"loop_post_title\",\"settings\":{\"before\":\"\",\"after\":\"\",\"loop_position\":\"\"}}})$"
  }}},
  "builderVersion": "5.11.1"
} /-->
```

## Field reference

| Path | Meaning |
|---|---|
| `module.advanced.loop.<bp>.value.enable` | `"on"` turns the module into a loop |
| `…value.loopId` | Unique loop instance id (`loop-<rand>`). **Use a distinct id per module** when several loops render on one page — collisions in the loop resolver |
| `…value.queryType` | `"post_types"` (observed); other VB options: terms, users |
| `…value.subTypes` | `[{"value":"page","label":"Pages"}]` — post type picker selection |
| `…value.includeSpecificPosts` | `[{"value":"<post_id>","label":"<title>"}]` — "Only Include Specific Posts" |
| `…value.includePostWithSpecificTerms` / `excludePostWithSpecificTerms` / `excludeSpecificPosts` | Term/post filters; `""` when unused |
| `…value.orderBy` | `"date"` (observed); VB also offers title, menu_order, rand |

## Content variables (`type:"content"`)

| Variable | Resolves to | Settings |
|---|---|---|
| `loop_post_title` | item `post_title` | `before`, `after`, `loop_position` |
| `loop_post_link` | item permalink | `before`, `after`, `loop_position`, `text` (`"post_title"` or `"custom_text"`), `custom_text` |

Always emit the full `settings` object — the VB includes every key, even empty.

## Where the variables live per module type

| Module | Text | URL |
|---|---|---|
| `divi/text` | `content.innerContent.desktop.value` = `loop_post_title` variable (plain string) | `module.advanced.link.desktop.value.url` = `loop_post_link` variable |
| `divi/link` | `content.innerContent.desktop.value.text` | `content.innerContent.desktop.value.linkUrl` |

For `divi/text` the anchor wraps the whole rendered module output — the VB
"Link" option (`module.advanced.link`), not an inline `<a>` in innerContent.
This is the VB-native replacement for hardcoded `<a href>` link lists and
survives VB saves.

## Gotchas

- **Don't combine loop content vars with the misplaced-`hover` crash**: a
  loop module whose font carries `font.hover` (wrong level) shows one
  "Oops!" box **per loop item** in the VB — the query resolves (N items),
  each item render throws. See decoration-formats.md hover rules.
- Loop order in the footer/nav use-case: `includeSpecificPosts` preserves
  the picked set; `orderBy:"date"` may reorder — for curated link lists,
  verify emitted order matches intent.
