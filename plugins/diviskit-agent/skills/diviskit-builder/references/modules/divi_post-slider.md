# Post Slider
`divi/post-slider`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `button.button.settings` | Button | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `arrows` | `object` | — |
| `button` | `object` | `button.settings.decoration.background`, `button.settings.decoration.border`, `button.settings.decoration.boxShadow`, `button.settings.decoration.button`, `button.settings.decoration.button.alignment`, `button.settings.decoration.button.fontGroup`, `button.settings.decoration.font`, `button.settings.decoration.sizing` *(+1 more)* |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont`, `content.settings.decoration.sizing`, `content.settings.decoration.sizing.item`, `content.settings.decoration.sizing.item.alignment`, `content.settings.decoration.sizing.item.height`, `content.settings.decoration.sizing.item.maxHeight`, `content.settings.decoration.sizing.item.minHeight` |
| `contentOverlay` | `object` | `contentOverlay.settings.decoration.background`, `contentOverlay.settings.decoration.border`, `contentOverlay.settings.decoration.border.item`, `contentOverlay.settings.decoration.border.item.styles`, `contentOverlay.settings.decoration.border.item.stylesTabNav`, `contentOverlay.settings.decoration.border.item.stylesTabbedColor`, `contentOverlay.settings.decoration.border.item.stylesTabbedStyle`, `contentOverlay.settings.decoration.border.item.stylesTabbedWidth` |
| `image` | `object` | `image.settings.decoration.image` |
| `lock` | `object` | — |
| `meta` | `object` | `meta.settings.decoration.font`, `meta.settings.decoration.font.lineHeight`, `meta.settings.decoration.font.size` |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `pagination` | `object` | `pagination.settings.decoration.background` |
| `post` | `object` | — |
| `slideOverlay` | `object` | `slideOverlay.settings.decoration.background` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font` |

### `button` decoration paths

- `button.settings.decoration.background`
- `button.settings.decoration.border`
- `button.settings.decoration.boxShadow`
- `button.settings.decoration.button`
- `button.settings.decoration.button.alignment`
- `button.settings.decoration.button.fontGroup`
- `button.settings.decoration.font`
- `button.settings.decoration.sizing`
- `button.settings.decoration.spacing`

### `content` decoration paths

- `content.settings.decoration.bodyFont`
- `content.settings.decoration.sizing`
- `content.settings.decoration.sizing.item`
- `content.settings.decoration.sizing.item.alignment`
- `content.settings.decoration.sizing.item.height`
- `content.settings.decoration.sizing.item.maxHeight`
- `content.settings.decoration.sizing.item.minHeight`

### `contentOverlay` decoration paths

- `contentOverlay.settings.decoration.background`
- `contentOverlay.settings.decoration.border`
- `contentOverlay.settings.decoration.border.item`
- `contentOverlay.settings.decoration.border.item.styles`
- `contentOverlay.settings.decoration.border.item.stylesTabNav`
- `contentOverlay.settings.decoration.border.item.stylesTabbedColor`
- `contentOverlay.settings.decoration.border.item.stylesTabbedStyle`
- `contentOverlay.settings.decoration.border.item.stylesTabbedWidth`

### `image` decoration paths

- `image.settings.decoration.image`

### `meta` decoration paths

- `meta.settings.decoration.font`
- `meta.settings.decoration.font.lineHeight`
- `meta.settings.decoration.font.size`

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

### `pagination` decoration paths

- `pagination.settings.decoration.background`

### `slideOverlay` decoration paths

- `slideOverlay.settings.decoration.background`

### `title` decoration paths

- `title.settings.decoration.font`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `title` | `{{selector}}.et_pb_slider .et_pb_slide_description .et_pb_slide_title, {{selector}}.et_pb_slider .et_pb_slide_descriptio...` |
| `content` | `{{selector}}.et_pb_slider .et_pb_slide_content_body` |
| `meta` | `{{selector}}.et_pb_slider .et_pb_slide_content .post-meta, {{selector}}.et_pb_slider .et_pb_slide_content .post-meta a` |
| `image` | `{{selector}} .et_pb_slide_image img` |
| `button` | `{{selector}}.et_pb_slider .et_pb_more_button.et_pb_button` |
| `arrows` | `{{selector}} .et-pb-slider-arrows .et-pb-arrow-prev, {{selector}} .et-pb-slider-arrows .et-pb-arrow-next` |
| `pagination` | `{{selector}} .et-pb-controllers a, {{selector}} .et-pb-controllers .et-pb-active-control` |
| `slideOverlay` | `{{selector}} .et_pb_slide .et_pb_slide_overlay_container` |
| `contentOverlay` | `{{selector}} .et_pb_slide .et_pb_text_overlay_wrapper` |
