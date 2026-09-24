# Call To Action
`divi/cta`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `title.title.settings` | Title | content |
| `content.content.settings` | content.content.settings | — |
| `button.button.settings` | Button | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `button` | `object` | `button.settings.decoration.background`, `button.settings.decoration.border`, `button.settings.decoration.boxShadow`, `button.settings.decoration.button`, `button.settings.decoration.font`, `button.settings.decoration.sizing`, `button.settings.decoration.spacing` |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font` |

### `button` decoration paths

- `button.settings.decoration.background`
- `button.settings.decoration.border`
- `button.settings.decoration.boxShadow`
- `button.settings.decoration.button`
- `button.settings.decoration.font`
- `button.settings.decoration.sizing`
- `button.settings.decoration.spacing`

### `content` decoration paths

- `content.settings.decoration.bodyFont`

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
| `module` | `{{selector}}` |
| `title` | `{{selector}} h2, {{selector}} h1.et_pb_module_header, {{selector}} h3.et_pb_module_header, {{selector}} h4.et_pb_module_...` |
| `content` | `{{selector}} .et_pb_promo_description .et_pb_promo_content` |
| `button` | `body #page-container {{selector}} .et_pb_promo_button.et_pb_button` |
