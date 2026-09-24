# Pagination
`divi/post-nav`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `links` | `object` | `links.settings.decoration.background`, `links.settings.decoration.border`, `links.settings.decoration.boxShadow`, `links.settings.decoration.font`, `links.settings.decoration.font.textAlign`, `links.settings.decoration.spacing` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters`, `module.settings.decoration.interactions`, `module.settings.decoration.layout`, `module.settings.decoration.order` *(+8 more)* |
| `style` | `object` | — |

### `links` decoration paths

- `links.settings.decoration.background`
- `links.settings.decoration.border`
- `links.settings.decoration.boxShadow`
- `links.settings.decoration.font`
- `links.settings.decoration.font.textAlign`
- `links.settings.decoration.spacing`

### `module` decoration paths

- `module.settings.decoration.animation`
- `module.settings.decoration.attributes`
- `module.settings.decoration.conditions`
- `module.settings.decoration.disabledOn`
- `module.settings.decoration.filters`
- `module.settings.decoration.interactions`
- `module.settings.decoration.layout`
- `module.settings.decoration.order`
- `module.settings.decoration.overflow`
- `module.settings.decoration.position`
- `module.settings.decoration.scroll`
- `module.settings.decoration.sizing`
- `module.settings.decoration.sticky`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `links` | `{{selector}} .wp-pagenavi a, {{selector}} .wp-pagenavi span, {{selector}} .pagination a, {{selectorPrefix}}.et_pb_posts_...` |
