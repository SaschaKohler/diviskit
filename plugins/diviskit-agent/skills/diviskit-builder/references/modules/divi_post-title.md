# Post Title
`divi/post-title`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `image` | `object` | `image.settings.decoration.image` |
| `lock` | `object` | — |
| `meta` | `object` | `meta.settings.decoration.font` |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `style` | `object` | — |
| `textWrapper` | `object` | `textWrapper.settings.decoration.background` |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `image` decoration paths

- `image.settings.decoration.image`

### `meta` decoration paths

- `meta.settings.decoration.font`

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

### `textWrapper` decoration paths

- `textWrapper.settings.decoration.background`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `title` | `{{selector}} .et_pb_title_container h1.entry-title,{{selector}} .et_pb_title_container h2.entry-title,{{selector}} .et_p...` |
| `meta` | `{{selector}} .et_pb_title_container .et_pb_title_meta_container, {{selector}} .et_pb_title_container .et_pb_title_meta_c...` |
| `textWrapper` | `{{selector}} .et_pb_title_container` |
| `image` | `{{selector}} .et_pb_title_featured_container img` |
