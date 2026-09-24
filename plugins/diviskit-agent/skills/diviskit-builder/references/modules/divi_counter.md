# Bar Counter
`divi/counter`

- Category: `child-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `title.title.settings` | Title | content |
| `barProgress.barProgress.settings` | Percent | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `barCounter` | `object` | `barCounter.settings.decoration.background`, `barCounter.settings.decoration.border`, `barCounter.settings.decoration.boxShadow`, `barCounter.settings.decoration.sizing` |
| `barProgress` | `object` | `barProgress.settings.decoration.background`, `barProgress.settings.decoration.background.color`, `barProgress.settings.decoration.font` |
| `className` | `string` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.conditions`, `module.settings.decoration.filters`, `module.settings.decoration.interactions`, `module.settings.decoration.layout`, `module.settings.decoration.order`, `module.settings.decoration.overflow` *(+6 more)* |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `barCounter` decoration paths

- `barCounter.settings.decoration.background`
- `barCounter.settings.decoration.border`
- `barCounter.settings.decoration.boxShadow`
- `barCounter.settings.decoration.sizing`

### `barProgress` decoration paths

- `barProgress.settings.decoration.background`
- `barProgress.settings.decoration.background.color`
- `barProgress.settings.decoration.font`

### `module` decoration paths

- `module.settings.decoration.animation`
- `module.settings.decoration.attributes`
- `module.settings.decoration.conditions`
- `module.settings.decoration.filters`
- `module.settings.decoration.interactions`
- `module.settings.decoration.layout`
- `module.settings.decoration.order`
- `module.settings.decoration.overflow`
- `module.settings.decoration.position`
- `module.settings.decoration.scroll`
- `module.settings.decoration.spacing`
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
| `barCounter` | `{{selectorPrefix}}.et_pb_counters li{{baseSelector}} .et_pb_counter_container` |
| `title` | `{{selectorPrefix}}.et_pb_counters {{baseSelector}} .et_pb_counter_title` |
| `barProgress` | `{{selectorPrefix}}.et_pb_counters {{baseSelector}} .et_pb_counter_amount_number` |
