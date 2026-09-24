# Circle Counter
`divi/circle-counter`

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
| `circle` | `object` | — |
| `className` | `string` | — |
| `contentContainer` | `object` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `number` | `object` | `number.settings.decoration.font`, `number.settings.decoration.font.item`, `number.settings.decoration.font.item.headingLevel`, `number.settings.decoration.font.item.lineHeight` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.item`, `title.settings.decoration.font.item.headingLevel` |

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
- `number.settings.decoration.font.item`
- `number.settings.decoration.font.item.headingLevel`
- `number.settings.decoration.font.item.lineHeight`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.item`
- `title.settings.decoration.font.item.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `circle` | `{{selector}} .percent canvas` |
| `title` | `{{selector}}.et_pb_circle_counter h3, {{selector}}.et_pb_circle_counter h1.et_pb_module_header, {{selector}}.et_pb_circl...` |
| `number` | `{{selector}}.et_pb_circle_counter .percent p` |
| `contentContainer` | `{{selector}} .et_pb_circle_counter_inner` |
