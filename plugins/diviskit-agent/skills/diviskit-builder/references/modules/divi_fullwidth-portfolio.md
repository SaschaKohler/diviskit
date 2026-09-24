# Post Carousel
`divi/fullwidth-portfolio`

- Category: `fullwidth-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `portfolio.portfolio.settings` | portfolio.portfolio.settings | — |
| `title.title.settings` | Carousel Title | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `image` | `object` | `image.settings.decoration.image` |
| `lock` | `object` | — |
| `meta` | `object` | `meta.settings.decoration.font` |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+14 more)* |
| `overlay` | `object` | `overlay.settings.decoration.background`, `overlay.settings.decoration.icon` |
| `portfolio` | `object` | `portfolio.settings.decoration.font`, `portfolio.settings.decoration.font.item`, `portfolio.settings.decoration.font.item.headingLevel` |
| `portfolioGrid` | `object` | — |
| `portfolioItemTitle` | `object` | — |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.item`, `title.settings.decoration.font.item.headingLevel` |

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
- `module.settings.decoration.layout`
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

### `portfolio` decoration paths

- `portfolio.settings.decoration.font`
- `portfolio.settings.decoration.font.item`
- `portfolio.settings.decoration.font.item.headingLevel`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.item`
- `title.settings.decoration.font.item.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `portfolio` | `{{selector}}` |
| `title` | `{{selector}} .et_pb_portfolio_title` |
| `overlay` | `{{selector}} .et_pb_portfolio_item .et_overlay` |
| `image` | `{{selector}} .et_pb_portfolio_image` |
| `meta` | `{{selector}} .post-meta, {{selector}} .post-meta a` |
| `portfolioGrid` | `{{selector}} .et_pb_portfolio_items` |
| `portfolioItemTitle` | `{{selector}} .et_pb_module_header` |
