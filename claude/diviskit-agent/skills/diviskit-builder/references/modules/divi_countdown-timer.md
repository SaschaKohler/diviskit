# Countdown Timer
`divi/countdown-timer`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `title.title.settings` | Title | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `content` | `object` | — |
| `label` | `object` | `label.settings.decoration.font` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `number` | `object` | `number.settings.decoration.font` |
| `separator` | `object` | `separator.settings.decoration.font`, `separator.settings.decoration.font.item`, `separator.settings.decoration.font.item.textAlign` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.item`, `title.settings.decoration.font.item.headingLevel` |

### `label` decoration paths

- `label.settings.decoration.font`

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

### `separator` decoration paths

- `separator.settings.decoration.font`
- `separator.settings.decoration.font.item`
- `separator.settings.decoration.font.item.textAlign`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.item`
- `title.settings.decoration.font.item.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `title` | `{{selector}} h4, {{selector}} h1.title, {{selector}} h2.title, {{selector}} h3.title, {{selector}} h5.title, {{selector}...` |
| `content` | `{{selector}} .value` |
| `number` | `{{selector}} .section p.value, {{selector}} .section.sep p` |
| `separator` | `{{selector}} .et_pb_countdown_timer_container .section.sep p` |
| `label` | `{{selector}} .section p.label` |
