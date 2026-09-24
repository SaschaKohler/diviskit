# Number Counter
`divi/number-counter`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `title.title.settings` | Title | content |
| `number.number.settings` | Number | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `number` | `object` | `number.settings.decoration.font`, `number.settings.decoration.font.headingLevel` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

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

### `number` decoration paths

- `number.settings.decoration.font`
- `number.settings.decoration.font.headingLevel`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `title` | `{{selector}}.et_pb_number_counter h3, {{selector}}.et_pb_number_counter h1.title, {{selector}}.et_pb_number_counter h2.t...` |
| `number` | `{{selector}}.et_pb_number_counter .percent p` |
