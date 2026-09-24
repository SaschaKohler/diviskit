# Accordion
`divi/accordion`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `closedToggle` | `object` | `closedToggle.settings.decoration.background`, `closedToggle.settings.decoration.font`, `closedToggle.settings.decoration.font.item`, `closedToggle.settings.decoration.font.item.color` |
| `closedToggleIcon` | `object` | `closedToggleIcon.settings.decoration.icon` |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+14 more)* |
| `openToggle` | `object` | `openToggle.settings.decoration.background`, `openToggle.settings.decoration.font` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.item`, `title.settings.decoration.font.item.color`, `title.settings.decoration.font.item.headingLevel` |

### `closedToggle` decoration paths

- `closedToggle.settings.decoration.background`
- `closedToggle.settings.decoration.font`
- `closedToggle.settings.decoration.font.item`
- `closedToggle.settings.decoration.font.item.color`

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
- `module.settings.decoration.scroll.item`
- `module.settings.decoration.scroll.item.gridMotion`
- `module.settings.decoration.sizing`
- `module.settings.decoration.spacing`
- `module.settings.decoration.sticky`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `openToggle` decoration paths

- `openToggle.settings.decoration.background`
- `openToggle.settings.decoration.font`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.item`
- `title.settings.decoration.font.item.color`
- `title.settings.decoration.font.item.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `title` | `{{selector}} > .et_pb_toggle > h1.et_pb_toggle_title, {{selector}} > .et_pb_toggle > h2.et_pb_toggle_title, {{selector}}...` |
| `closedToggleIcon` | `{{selector}} > .et_pb_toggle_close:not(.et_pb_toggle_empty) > .et_pb_toggle_title:before` |
| `content` | `{{selector}}.et_pb_accordion .et_pb_toggle_content` |
| `openToggle` | `{{selector}} .et_pb_toggle_open` |
| `closedToggle` | `{{selector}} .et_pb_toggle_close` |
