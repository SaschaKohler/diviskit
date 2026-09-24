# Blog
`divi/blog`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `blogGrid` | `object` | `blogGrid.settings.decoration.layout`, `blogGrid.settings.decoration.layout.item`, `blogGrid.settings.decoration.layout.item.gridColumnCount` |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `fullwidth` | `object` | `fullwidth.settings.decoration.border` |
| `image` | `object` | `image.settings.decoration.image` |
| `lock` | `object` | — |
| `masonry` | `object` | `masonry.settings.decoration.background` |
| `meta` | `object` | `meta.settings.decoration.font` |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters`, `module.settings.decoration.interactions` *(+12 more)* |
| `overlay` | `object` | `overlay.settings.decoration.background` |
| `overlayIcon` | `object` | `overlayIcon.settings.decoration.icon` |
| `pagination` | `object` | `pagination.settings.decoration.font` |
| `post` | `object` | `post.settings.decoration.border` |
| `readMore` | `object` | `readMore.settings.decoration.font` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `blogGrid` decoration paths

- `blogGrid.settings.decoration.layout`
- `blogGrid.settings.decoration.layout.item`
- `blogGrid.settings.decoration.layout.item.gridColumnCount`

### `content` decoration paths

- `content.settings.decoration.bodyFont`

### `fullwidth` decoration paths

- `fullwidth.settings.decoration.border`

### `image` decoration paths

- `image.settings.decoration.image`

### `masonry` decoration paths

- `masonry.settings.decoration.background`

### `meta` decoration paths

- `meta.settings.decoration.font`

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

### `overlay` decoration paths

- `overlay.settings.decoration.background`

### `overlayIcon` decoration paths

- `overlayIcon.settings.decoration.icon`

### `pagination` decoration paths

- `pagination.settings.decoration.font`

### `post` decoration paths

- `post.settings.decoration.border`

### `readMore` decoration paths

- `readMore.settings.decoration.font`

### `title` decoration paths

- `title.settings.decoration.font`
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `post` | `{{selector}} .et_grid_module article.et_pb_post` |
| `image` | `{{selector}} .et_pb_post .entry-featured-image-url,{{selector}} .et_pb_post .et_pb_slides,{{selector}} .et_pb_post .et_p...` |
| `readMore` | `{{selector}} .et_pb_post div.post-content a.more-link` |
| `pagination` | `{{selector}} .wp-pagenavi a, {{selector}} .wp-pagenavi span, {{selector}} .pagination a` |
| `meta` | `{{selector}} .et_pb_post .post-meta, {{selector}} .et_pb_post .post-meta a, #left-area {{selector}} .et_pb_post .post-me...` |
| `title` | `{{selector}} .et_pb_post .entry-title, {{selector}} .not-found-title` |
| `content` | `{{selector}} .et_pb_post .post-content, {{selector}}.et_pb_bg_layout_light .et_pb_post .post-content p, {{selector}}.et_...` |
| `overlay` | `{{selector}} .et_overlay` |
| `overlayIcon` | `{{selector}} .et_overlay::before` |
| `fullwidth` | `{{selector}}:not(.et_pb_blog_grid_wrapper) article.et_pb_post` |
| `blogGrid` | `{{selector}} .et_pb_blog_posts` |
| `masonry` | `{{selector}} .et_grid_module .et_pb_post` |
