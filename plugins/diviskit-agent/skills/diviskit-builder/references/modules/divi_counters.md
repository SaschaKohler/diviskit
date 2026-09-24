# Bar Counters
`divi/counters`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `barCounter` | `object` | `barCounter.settings.decoration.background`, `barCounter.settings.decoration.border`, `barCounter.settings.decoration.boxShadow` |
| `barProgress` | `object` | `barProgress.settings.decoration.font` |
| `children` | `object` | `children.settings.decoration.background`, `children.settings.decoration.background.color` |
| `className` | `string` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters`, `module.settings.decoration.interactions`, `module.settings.decoration.layout`, `module.settings.decoration.order` *(+11 more)* |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `barCounter` decoration paths

- `barCounter.settings.decoration.background`
- `barCounter.settings.decoration.border`
- `barCounter.settings.decoration.boxShadow`

### `barProgress` decoration paths

- `barProgress.settings.decoration.font`

### `children` decoration paths

- `children.settings.decoration.background`
- `children.settings.decoration.background.color`

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
- `module.settings.decoration.scroll.item`
- `module.settings.decoration.scroll.item.gridMotion`
- `module.settings.decoration.sizing`
- `module.settings.decoration.spacing`
- `module.settings.decoration.sticky`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `barCounter` | `{{selector}} .et_pb_counter_container` |
| `title` | `{{selector}}.et_pb_counters .et_pb_counter_title` |
| `barProgress` | `{{selector}}.et_pb_counters .et_pb_counter_amount_number` |
