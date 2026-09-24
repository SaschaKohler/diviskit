# Toggle
`divi/toggle`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `content.content.settings` | Content | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `closedTitle` | `object` | `closedTitle.settings.decoration.font`, `closedTitle.settings.decoration.font.item`, `closedTitle.settings.decoration.font.item.headingLevel` |
| `closedToggle` | `object` | `closedToggle.settings.decoration.background` |
| `closedToggleIcon` | `object` | `closedToggleIcon.settings.decoration.icon` |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `openToggle` | `object` | `openToggle.settings.decoration.background`, `openToggle.settings.decoration.font` |
| `openToggleIcon` | `object` | `openToggleIcon.settings.decoration.icon` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font` |

### `closedTitle` decoration paths

- `closedTitle.settings.decoration.font`
- `closedTitle.settings.decoration.font.item`
- `closedTitle.settings.decoration.font.item.headingLevel`

### `closedToggle` decoration paths

- `closedToggle.settings.decoration.background`

### `closedToggleIcon` decoration paths

- `closedToggleIcon.settings.decoration.icon`

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

### `openToggle` decoration paths

- `openToggle.settings.decoration.background`
- `openToggle.settings.decoration.font`

### `openToggleIcon` decoration paths

- `openToggleIcon.settings.decoration.icon`

### `title` decoration paths

- `title.settings.decoration.font`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `openToggleIcon` | `{{selector}}.et_pb_toggle_open > .et_pb_toggle_title:before` |
| `closedToggleIcon` | `{{selector}}.et_pb_toggle_close > .et_pb_toggle_title:before` |
| `title` | `{{selector}}.et_pb_toggle > h5, {{selector}}.et_pb_toggle > h1.et_pb_toggle_title, {{selector}}.et_pb_toggle > h2.et_pb_...` |
| `closedTitle` | `{{selector}}.et_pb_toggle.et_pb_toggle_close > h5, {{selector}}.et_pb_toggle.et_pb_toggle_close > h1.et_pb_toggle_title,...` |
| `content` | `{{selector}} .et_pb_toggle_content` |
| `openToggle` | `{{selector}}.et_pb_toggle.et_pb_toggle_open` |
| `closedToggle` | `{{selector}}.et_pb_toggle.et_pb_toggle_close` |
