# Slide
`divi/slide`

- Category: `child-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `image.image.settings` | image.image.settings | — |
| `title.title.settings` | Title | content |
| `button.button.settings` | button.button.settings | — |
| `content.content.settings` | Body | content |
| `video.video.settings` | Video | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `arrows` | `object` | — |
| `button` | `object` | `button.settings.decoration.button`, `button.settings.decoration.button.item`, `button.settings.decoration.button.item.borderGroup`, `button.settings.decoration.button.item.fontGroup` |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `contentOverlay` | `object` | `contentOverlay.settings.decoration.background`, `contentOverlay.settings.decoration.border` |
| `dotNav` | `object` | — |
| `image` | `object` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters`, `module.settings.decoration.interactions`, `module.settings.decoration.layout`, `module.settings.decoration.order` *(+14 more)* |
| `slideOverlay` | `object` | `slideOverlay.settings.decoration.background` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |
| `video` | `object` | — |

### `button` decoration paths

- `button.settings.decoration.button`
- `button.settings.decoration.button.item`
- `button.settings.decoration.button.item.borderGroup`
- `button.settings.decoration.button.item.fontGroup`

### `content` decoration paths

- `content.settings.decoration.bodyFont`

### `contentOverlay` decoration paths

- `contentOverlay.settings.decoration.background`
- `contentOverlay.settings.decoration.border`

### `module` decoration paths

- `module.settings.decoration.attributes`
- `module.settings.decoration.background`
- `module.settings.decoration.conditions`
- `module.settings.decoration.disabledOn`
- `module.settings.decoration.filters`
- `module.settings.decoration.interactions`
- `module.settings.decoration.layout`
- `module.settings.decoration.order`
- `module.settings.decoration.overflow`
- `module.settings.decoration.sizing`
- `module.settings.decoration.sizing.item`
- `module.settings.decoration.sizing.item.alignSelf`
- `module.settings.decoration.sizing.item.alignment`
- `module.settings.decoration.sizing.item.height`
- `module.settings.decoration.sizing.item.maxHeight`
- `module.settings.decoration.sizing.item.minHeight`
- `module.settings.decoration.spacing`
- `module.settings.decoration.spacing.item`
- `module.settings.decoration.spacing.item.margin`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `slideOverlay` decoration paths

- `slideOverlay.settings.decoration.background`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `image` | `{{selector}}.et_pb_slide .et_pb_slide_image img` |
| `title` | `{{selectorPrefix}}.et_pb_slider {{baseSelector}}.et_pb_slide .et_pb_slide_description .et_pb_slide_title` |
| `contentOverlay` | `{{selector}}.et_pb_slide .et_pb_text_overlay_wrapper` |
| `button` | `body #page-container {{selector}}.et_pb_slide .et_pb_more_button.et_pb_button` |
| `content` | `{{selectorPrefix}}.et_pb_slider.et_pb_module {{baseSelector}}.et_pb_slide .et_pb_slide_description .et_pb_slide_content` |
| `slideOverlay` | `{{selector}}.et_pb_slide .et_pb_slide_overlay_container` |
