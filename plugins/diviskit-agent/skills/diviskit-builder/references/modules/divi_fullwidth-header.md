# Hero
`divi/fullwidth-header`

- Category: `fullwidth-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `image.image.settings` | image.image.settings | — |
| `logo.logo.settings` | logo.logo.settings | — |
| `title.title.settings` | Title | content |
| `subhead.subhead.settings` | Subtitle | content |
| `content.content.settings` | Body | content |
| `buttonOne.buttonOne.settings` | buttonOne.buttonOne.settings | — |
| `buttonTwo.buttonTwo.settings` | buttonTwo.buttonTwo.settings | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `buttonOne` | `object` | `buttonOne.settings.decoration.background`, `buttonOne.settings.decoration.border`, `buttonOne.settings.decoration.boxShadow`, `buttonOne.settings.decoration.button`, `buttonOne.settings.decoration.button.sizingGroup`, `buttonOne.settings.decoration.font`, `buttonOne.settings.decoration.sizing`, `buttonOne.settings.decoration.spacing` |
| `buttonTwo` | `object` | `buttonTwo.settings.decoration.background`, `buttonTwo.settings.decoration.border`, `buttonTwo.settings.decoration.boxShadow`, `buttonTwo.settings.decoration.button`, `buttonTwo.settings.decoration.button.sizingGroup`, `buttonTwo.settings.decoration.font`, `buttonTwo.settings.decoration.sizing`, `buttonTwo.settings.decoration.spacing` |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `image` | `object` | — |
| `lock` | `object` | — |
| `logo` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `overlay` | `object` | `overlay.settings.decoration.background` |
| `scrollDown` | `object` | `scrollDown.settings.decoration.icon` |
| `style` | `object` | — |
| `subhead` | `object` | `subhead.settings.decoration.font` |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `buttonOne` decoration paths

- `buttonOne.settings.decoration.background`
- `buttonOne.settings.decoration.border`
- `buttonOne.settings.decoration.boxShadow`
- `buttonOne.settings.decoration.button`
- `buttonOne.settings.decoration.button.sizingGroup`
- `buttonOne.settings.decoration.font`
- `buttonOne.settings.decoration.sizing`
- `buttonOne.settings.decoration.spacing`

### `buttonTwo` decoration paths

- `buttonTwo.settings.decoration.background`
- `buttonTwo.settings.decoration.border`
- `buttonTwo.settings.decoration.boxShadow`
- `buttonTwo.settings.decoration.button`
- `buttonTwo.settings.decoration.button.sizingGroup`
- `buttonTwo.settings.decoration.font`
- `buttonTwo.settings.decoration.sizing`
- `buttonTwo.settings.decoration.spacing`

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

### `overlay` decoration paths

- `overlay.settings.decoration.background`

### `scrollDown` decoration paths

- `scrollDown.settings.decoration.icon`

### `subhead` decoration paths

- `subhead.settings.decoration.font`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `image` | `{{selector}} .header-logo, {{selector}} .header-image-container img` |
| `logo` | `{{selector}} .header-logo` |
| `title` | `{{selector}}.et_pb_fullwidth_header .header-content h1, {{selector}}.et_pb_fullwidth_header .header-content h2.et_pb_mod...` |
| `subhead` | `{{selector}}.et_pb_fullwidth_header .et_pb_fullwidth_header_subhead` |
| `content` | `{{selector}}.et_pb_fullwidth_header .et_pb_header_content_wrapper` |
| `buttonOne` | `{{selector}} .et_pb_button_one.et_pb_button` |
| `buttonTwo` | `{{selector}} .et_pb_button_two.et_pb_button` |
| `scrollDown` | `{{selector}}.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a .et-pb-icon` |
| `overlay` | `{{selector}}.et_pb_fullwidth_header .et_pb_fullwidth_header_overlay` |
