# Icon List Item
`divi/icon-list-item`

- Category: `child-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `content.content.settings` | Text | content |
| `icon.icon.settings` | Icon | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.font` |
| `icon` | `object` | `icon.settings.decoration.background`, `icon.settings.decoration.border`, `icon.settings.decoration.boxShadow`, `icon.settings.decoration.spacing` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `style` | `object` | — |

### `content` decoration paths

- `content.settings.decoration.font`

### `icon` decoration paths

- `icon.settings.decoration.background`
- `icon.settings.decoration.border`
- `icon.settings.decoration.boxShadow`
- `icon.settings.decoration.spacing`

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
- `module.settings.decoration.sizing`
- `module.settings.decoration.spacing`
- `module.settings.decoration.sticky`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `content` | `{{selector}}.et_pb_icon_list_item .et_pb_icon_list_text` |
| `icon` | `{{selector}}.et_pb_icon_list_item .et-pb-icon` |
