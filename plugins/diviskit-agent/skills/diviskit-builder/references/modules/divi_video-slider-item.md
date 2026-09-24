# Video Slider Item
`divi/video-slider-item`

- Category: `child-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `overlay.overlay.settings` | overlay.overlay.settings | — |
| `video.video.settings` | video.video.settings | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.disabledOn.disabledOnGroup`, `module.settings.decoration.filters`, `module.settings.decoration.interactions`, `module.settings.decoration.order` *(+6 more)* |
| `overlay` | `object` | `overlay.settings.decoration.background` |
| `playIcon` | `object` | `playIcon.settings.decoration.icon`, `playIcon.settings.decoration.icon.item`, `playIcon.settings.decoration.icon.item.icon` |
| `sliderControls` | `object` | — |
| `style` | `object` | — |
| `video` | `object` | — |

### `module` decoration paths

- `module.settings.decoration.animation`
- `module.settings.decoration.attributes`
- `module.settings.decoration.conditions`
- `module.settings.decoration.disabledOn`
- `module.settings.decoration.disabledOn.disabledOnGroup`
- `module.settings.decoration.filters`
- `module.settings.decoration.interactions`
- `module.settings.decoration.order`
- `module.settings.decoration.overflow`
- `module.settings.decoration.position`
- `module.settings.decoration.sticky`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `overlay` decoration paths

- `overlay.settings.decoration.background`

### `playIcon` decoration paths

- `playIcon.settings.decoration.icon`
- `playIcon.settings.decoration.icon.item`
- `playIcon.settings.decoration.icon.item.icon`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `overlay` | `{{selector}} .et_pb_video_overlay_hover:hover` |
| `playIcon` | `{{selector}} .et_pb_video_overlay .et_pb_video_play` |
| `video` | `{{selector}} .et_pb_video_wrap .et_pb_video_box` |
