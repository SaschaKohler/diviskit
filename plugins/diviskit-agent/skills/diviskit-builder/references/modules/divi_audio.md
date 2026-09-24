# Audio
`divi/audio`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `artistName.artistName.settings` | Artist | content |
| `albumName.albumName.settings` | Album | content |
| `audio.audio.settings` | Audio File | content |
| `title.title.settings` | Title | content |
| `image.image.settings` | Image | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `albumName` | `object` | — |
| `artistName` | `object` | — |
| `audio` | `object` | — |
| `caption` | `object` | `caption.settings.decoration.font` |
| `className` | `string` | — |
| `image` | `object` | `image.settings.decoration.image` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font` |

### `caption` decoration paths

- `caption.settings.decoration.font`

### `image` decoration paths

- `image.settings.decoration.image`

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

### `title` decoration paths

- `title.settings.decoration.font`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `caption` | `{{selector}}.et_pb_audio_module p` |
| `artistName` | `{{selector}}.et_pb_audio_module p.et_audio_module_meta strong` |
| `albumName` | `{{selector}}.et_pb_audio_module p.et_audio_module_meta span` |
| `title` | `{{selector}} h2, {{selector}} h1.et_pb_module_header, {{selector}} h3.et_pb_module_header, {{selector}} h4.et_pb_module_...` |
| `image` | `{{selector}} .et_pb_audio_cover_art` |
