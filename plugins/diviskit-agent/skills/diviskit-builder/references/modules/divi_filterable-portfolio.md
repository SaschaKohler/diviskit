# Filterable Portfolio
`divi/filterable-portfolio`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `filter` | `object` | `filter.settings.decoration.font`, `filter.settings.decoration.font.textAlign` |
| `image` | `object` | `image.settings.decoration.image` |
| `lock` | `object` | — |
| `meta` | `object` | `meta.settings.decoration.font` |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters`, `module.settings.decoration.interactions` *(+12 more)* |
| `overlay` | `object` | `overlay.settings.decoration.background`, `overlay.settings.decoration.icon` |
| `pagination` | `object` | `pagination.settings.decoration.font` |
| `portfolio` | `object` | — |
| `portfolioGrid` | `object` | `portfolioGrid.settings.decoration.layout`, `portfolioGrid.settings.decoration.layout.item`, `portfolioGrid.settings.decoration.layout.item.gridColumnCount` |
| `portfolioItem` | `object` | `portfolioItem.settings.decoration.border` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `filter` decoration paths

- `filter.settings.decoration.font`
- `filter.settings.decoration.font.textAlign`

### `image` decoration paths

- `image.settings.decoration.image`

### `meta` decoration paths

- `meta.settings.decoration.font`

### `module` decoration paths

- `module.settings.decoration.animation`
- `module.settings.decoration.attributes`
- `module.settings.decoration.background`
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
- `overlay.settings.decoration.icon`

### `pagination` decoration paths

- `pagination.settings.decoration.font`

### `portfolioGrid` decoration paths

- `portfolioGrid.settings.decoration.layout`
- `portfolioGrid.settings.decoration.layout.item`
- `portfolioGrid.settings.decoration.layout.item.gridColumnCount`

### `portfolioItem` decoration paths

- `portfolioItem.settings.decoration.border`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `portfolio` | `{{selector}}` |
| `portfolioGrid` | `{{selector}} .et_pb_posts` |
| `portfolioItem` | `{{selector}}` |
| `title` | `{{selector}}.et_pb_filterable_portfolio h2, {{selector}}.et_pb_filterable_portfolio h2 a, {{selector}}.et_pb_filterable_...` |
| `overlay` | `{{selector}}.et_pb_filterable_portfolio .et_overlay` |
| `image` | `{{selector}} .et_portfolio_image` |
| `filter` | `{{selector}}.et_pb_filterable_portfolio .et_pb_portfolio_filter, {{selector}} .et_pb_portfolio_filter a` |
| `meta` | `{{selector}}.et_pb_filterable_portfolio .post-meta, {{selector}}.et_pb_filterable_portfolio .post-meta a` |
| `pagination` | `{{selector}}.et_pb_filterable_portfolio .et_pb_portofolio_pagination a` |
