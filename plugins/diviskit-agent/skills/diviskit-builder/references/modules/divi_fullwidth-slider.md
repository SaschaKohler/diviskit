# Fullwidth Slider
`divi/fullwidth-slider`

- Category: `fullwidth-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `arrows` | `object` | — |
| `button` | `object` | `button.settings.decoration.button`, `button.settings.decoration.button.item`, `button.settings.decoration.button.item.borderGroup`, `button.settings.decoration.button.item.fontGroup` |
| `children` | `object` | `children.settings.decoration.background`, `children.settings.decoration.border` |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont`, `content.settings.decoration.sizing` |
| `dotNav` | `object` | `dotNav.settings.decoration.background` |
| `image` | `object` | `image.settings.decoration.border`, `image.settings.decoration.boxShadow` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+11 more)* |
| `pagination` | `object` | — |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.item`, `title.settings.decoration.font.item.headingLevel` |

### `button` decoration paths

- `button.settings.decoration.button`
- `button.settings.decoration.button.item`
- `button.settings.decoration.button.item.borderGroup`
- `button.settings.decoration.button.item.fontGroup`

### `children` decoration paths

- `children.settings.decoration.background`
- `children.settings.decoration.border`

### `content` decoration paths

- `content.settings.decoration.bodyFont`
- `content.settings.decoration.sizing`

### `dotNav` decoration paths

- `dotNav.settings.decoration.background`

### `image` decoration paths

- `image.settings.decoration.border`
- `image.settings.decoration.boxShadow`

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
- `title.settings.decoration.font.item`
- `title.settings.decoration.font.item.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `arrows` | `{{selector}} .et-pb-slider-arrows .et-pb-arrow-prev, {{selector}} .et-pb-slider-arrows .et-pb-arrow-next` |
| `title` | `{{selector}}.et_pb_slider .et_pb_slide_description .et_pb_slide_title` |
| `button` | `{{selector}} .et_pb_more_button.et_pb_button` |
| `content` | `{{selector}}.et_pb_slider .et_pb_slide_content` |
| `image` | `{{selector}} .et_pb_slide_image img` |
| `dotNav` | `{{selector}} .et-pb-controllers a, {{selector}} .et-pb-controllers .et-pb-active-control` |
