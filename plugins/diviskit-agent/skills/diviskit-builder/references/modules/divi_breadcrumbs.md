# Breadcrumbs
`divi/breadcrumbs`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `home.home.settings` | home.home.settings | — |
| `separator.separator.settings` | Separator | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `breadcrumb` | `object` | `breadcrumb.settings.decoration.background`, `breadcrumb.settings.decoration.background.item`, `breadcrumb.settings.decoration.background.item.image`, `breadcrumb.settings.decoration.border`, `breadcrumb.settings.decoration.boxShadow`, `breadcrumb.settings.decoration.font`, `breadcrumb.settings.decoration.sizing`, `breadcrumb.settings.decoration.spacing` |
| `breadcrumbLink` | `object` | `breadcrumbLink.settings.decoration.background`, `breadcrumbLink.settings.decoration.background.item`, `breadcrumbLink.settings.decoration.background.item.image`, `breadcrumbLink.settings.decoration.border`, `breadcrumbLink.settings.decoration.boxShadow`, `breadcrumbLink.settings.decoration.font`, `breadcrumbLink.settings.decoration.sizing`, `breadcrumbLink.settings.decoration.spacing` |
| `className` | `string` | — |
| `home` | `object` | `home.settings.decoration.background`, `home.settings.decoration.background.item`, `home.settings.decoration.background.item.image`, `home.settings.decoration.border`, `home.settings.decoration.boxShadow`, `home.settings.decoration.font`, `home.settings.decoration.sizing`, `home.settings.decoration.spacing` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+11 more)* |
| `separator` | `object` | `separator.settings.decoration.background`, `separator.settings.decoration.background.item`, `separator.settings.decoration.background.item.image`, `separator.settings.decoration.border`, `separator.settings.decoration.boxShadow`, `separator.settings.decoration.font`, `separator.settings.decoration.sizing`, `separator.settings.decoration.spacing` |
| `style` | `object` | — |
| `trail` | `object` | — |

### `breadcrumb` decoration paths

- `breadcrumb.settings.decoration.background`
- `breadcrumb.settings.decoration.background.item`
- `breadcrumb.settings.decoration.background.item.image`
- `breadcrumb.settings.decoration.border`
- `breadcrumb.settings.decoration.boxShadow`
- `breadcrumb.settings.decoration.font`
- `breadcrumb.settings.decoration.sizing`
- `breadcrumb.settings.decoration.spacing`

### `breadcrumbLink` decoration paths

- `breadcrumbLink.settings.decoration.background`
- `breadcrumbLink.settings.decoration.background.item`
- `breadcrumbLink.settings.decoration.background.item.image`
- `breadcrumbLink.settings.decoration.border`
- `breadcrumbLink.settings.decoration.boxShadow`
- `breadcrumbLink.settings.decoration.font`
- `breadcrumbLink.settings.decoration.sizing`
- `breadcrumbLink.settings.decoration.spacing`

### `home` decoration paths

- `home.settings.decoration.background`
- `home.settings.decoration.background.item`
- `home.settings.decoration.background.item.image`
- `home.settings.decoration.border`
- `home.settings.decoration.boxShadow`
- `home.settings.decoration.font`
- `home.settings.decoration.sizing`
- `home.settings.decoration.spacing`

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
- `module.settings.decoration.overflow`
- `module.settings.decoration.position`
- `module.settings.decoration.scroll`
- `module.settings.decoration.sizing`
- `module.settings.decoration.spacing`
- `module.settings.decoration.sticky`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `separator` decoration paths

- `separator.settings.decoration.background`
- `separator.settings.decoration.background.item`
- `separator.settings.decoration.background.item.image`
- `separator.settings.decoration.border`
- `separator.settings.decoration.boxShadow`
- `separator.settings.decoration.font`
- `separator.settings.decoration.sizing`
- `separator.settings.decoration.spacing`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `breadcrumb` | `{{selector}} .et_pb_breadcrumbs--trail .et_pb_breadcrumbs--breadcrumb` |
| `breadcrumbLink` | `{{selector}} .et_pb_breadcrumbs--trail a.et_pb_breadcrumbs--breadcrumb:not(.et_pb_breadcrumbs--home)` |
| `home` | `{{selector}} .et_pb_breadcrumbs--trail a.et_pb_breadcrumbs--home` |
| `separator` | `{{selector}} .et_pb_breadcrumbs--trail .et_pb_breadcrumbs--separator` |
