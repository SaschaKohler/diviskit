# Portfolio
`divi/portfolio`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `portfolio.portfolio.settings` | portfolio.portfolio.settings | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `image` | `object` | `image.settings.decoration.image` |
| `lock` | `object` | — |
| `meta` | `object` | `meta.settings.decoration.font` |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+13 more)* |
| `overlay` | `object` | `overlay.settings.decoration.background` |
| `pagination` | `object` | `pagination.settings.decoration.font` |
| `portfolio` | `object` | — |
| `portfolioGrid` | `object` | `portfolioGrid.settings.decoration.layout`, `portfolioGrid.settings.decoration.layout.item`, `portfolioGrid.settings.decoration.layout.item.gridColumnCount` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `image` decoration paths

- `image.settings.decoration.image`

### `meta` decoration paths

- `meta.settings.decoration.font`

### `module` decoration paths

- `module.settings.decoration.animation`
- `module.settings.decoration.attributes`
- `module.settings.decoration.background`
- `module.settings.decoration.border`
- `module.settings.decoration.boxShadow`
- `module.settings.decoration.conditions`
- `module.settings.decoration.disabledOn`
- `module.settings.decoration.filters`
- `module.settings.decoration.interactions`
- `module.settings.decoration.order`
- `module.settings.decoration.overflow`
- `module.settings.decoration.position`
- `module.settings.decoration.scroll`
- `module.settings.decoration.scroll.item`
- `module.settings.decoration.scroll.item.gridMotion`
- `module.settings.decoration.sizing`
- `module.settings.decoration.spacing`
- `module.settings.decoration.sticky`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `overlay` decoration paths

- `overlay.settings.decoration.background`

### `pagination` decoration paths

- `pagination.settings.decoration.font`

### `portfolioGrid` decoration paths

- `portfolioGrid.settings.decoration.layout`
- `portfolioGrid.settings.decoration.layout.item`
- `portfolioGrid.settings.decoration.layout.item.gridColumnCount`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `portfolio` | `{{selector}}` |
| `portfolioGrid` | `{{selector}} .et_pb_posts` |
| `title` | `{{selector}} .et_pb_module_header` |
| `overlay` | `{{selector}} .et_pb_portfolio_item .et_overlay` |
| `image` | `{{selector}} .et_portfolio_image` |
| `meta` | `{{selector}} .et_pb_portfolio_item .post-meta, {{selector}} .et_pb_portfolio_item .post-meta a, {{selector}} .et_pb_port...` |
| `pagination` | `{{selector}} .wp-pagenavi a, {{selector}} .wp-pagenavi span, {{selector}} .pagination a` |
