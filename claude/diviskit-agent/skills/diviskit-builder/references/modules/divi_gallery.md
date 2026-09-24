# Gallery
`divi/gallery`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `overlay.overlay.settings` | overlay.overlay.settings | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `caption` | `object` | `caption.settings.decoration.font` |
| `className` | `string` | — |
| `galleryGrid` | `object` | `galleryGrid.settings.decoration.layout`, `galleryGrid.settings.decoration.layout.item`, `galleryGrid.settings.decoration.layout.item.gridColumnCount` |
| `image` | `object` | `image.settings.decoration.image` |
| `item` | `object` | `item.settings.decoration.border` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters`, `module.settings.decoration.interactions` *(+12 more)* |
| `overlay` | `object` | — |
| `pagination` | `object` | `pagination.settings.decoration.font`, `pagination.settings.decoration.font.textAlign` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `caption` decoration paths

- `caption.settings.decoration.font`

### `galleryGrid` decoration paths

- `galleryGrid.settings.decoration.layout`
- `galleryGrid.settings.decoration.layout.item`
- `galleryGrid.settings.decoration.layout.item.gridColumnCount`

### `image` decoration paths

- `image.settings.decoration.image`

### `item` decoration paths

- `item.settings.decoration.border`

### `module` decoration paths

- `module.settings.decoration.animation`
- `module.settings.decoration.attributes`
- `module.settings.decoration.background`
- `module.settings.decoration.boxShadow`
- `module.settings.decoration.conditions`
- `module.settings.decoration.disabledOn`
- `module.settings.decoration.filters`
- `module.settings.decoration.interactions`
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

### `pagination` decoration paths

- `pagination.settings.decoration.font`
- `pagination.settings.decoration.font.textAlign`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `title` | `{{selector}} .et_pb_gallery_title` |
| `caption` | `{{selector}} .et_pb_gallery_caption` |
| `pagination` | `{{selector}} .et_pb_gallery_pagination` |
| `item` | `{{selector}} .et_pb_gallery_item` |
| `image` | `{{selector}} .et_pb_gallery_image img` |
| `galleryGrid` | `{{selector}} .et_pb_gallery_items` |
