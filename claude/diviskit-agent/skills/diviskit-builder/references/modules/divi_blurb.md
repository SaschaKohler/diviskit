# Blurb
`divi/blurb`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `imageIcon.imageIcon.settings` | imageIcon.imageIcon.settings | — |
| `content.content.settings` | content.content.settings | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `contentContainer` | `object` | `contentContainer.settings.decoration.sizing` |
| `imageIcon` | `object` | `imageIcon.settings.decoration.animation`, `imageIcon.settings.decoration.background`, `imageIcon.settings.decoration.spacing` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font` |

### `content` decoration paths

- `content.settings.decoration.bodyFont`

### `contentContainer` decoration paths

- `contentContainer.settings.decoration.sizing`

### `imageIcon` decoration paths

- `imageIcon.settings.decoration.animation`
- `imageIcon.settings.decoration.background`
- `imageIcon.settings.decoration.spacing`

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

### `title` decoration paths

- `title.settings.decoration.font`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `imageIcon` | `{{selector}} > .et_pb_blurb_content > .et_pb_main_blurb_image .et-pb-icon, {{selector}} > .et_pb_blurb_content > .et_pb_...` |
| `module` | `{{selector}}` |
| `title` | `{{selector}} .et_pb_module_header` |
| `content` | `{{selector}} .et_pb_blurb_description` |
| `contentContainer` | `{{selector}} .et_pb_blurb_content` |
