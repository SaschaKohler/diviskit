# Instagram Feed
`divi/instagram-feed`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `feed.feed.settings` | feed.feed.settings | — |
| `followButton.followButton.settings` | followButton.followButton.settings | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `feed` | `object` | `feed.settings.decoration.background`, `feed.settings.decoration.border`, `feed.settings.decoration.boxShadow`, `feed.settings.decoration.layout`, `feed.settings.decoration.sizing`, `feed.settings.decoration.spacing` |
| `followButton` | `object` | `followButton.settings.decoration.background`, `followButton.settings.decoration.border`, `followButton.settings.decoration.boxShadow`, `followButton.settings.decoration.button`, `followButton.settings.decoration.font`, `followButton.settings.decoration.sizing`, `followButton.settings.decoration.spacing` |
| `item` | `object` | `item.settings.decoration.background`, `item.settings.decoration.background.item`, `item.settings.decoration.background.item.image`, `item.settings.decoration.border`, `item.settings.decoration.boxShadow`, `item.settings.decoration.sizing`, `item.settings.decoration.spacing` |
| `lock` | `object` | — |
| `media` | `object` | `media.settings.decoration.image` |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `style` | `object` | — |

### `feed` decoration paths

- `feed.settings.decoration.background`
- `feed.settings.decoration.border`
- `feed.settings.decoration.boxShadow`
- `feed.settings.decoration.layout`
- `feed.settings.decoration.sizing`
- `feed.settings.decoration.spacing`

### `followButton` decoration paths

- `followButton.settings.decoration.background`
- `followButton.settings.decoration.border`
- `followButton.settings.decoration.boxShadow`
- `followButton.settings.decoration.button`
- `followButton.settings.decoration.font`
- `followButton.settings.decoration.sizing`
- `followButton.settings.decoration.spacing`

### `item` decoration paths

- `item.settings.decoration.background`
- `item.settings.decoration.background.item`
- `item.settings.decoration.background.item.image`
- `item.settings.decoration.border`
- `item.settings.decoration.boxShadow`
- `item.settings.decoration.sizing`
- `item.settings.decoration.spacing`

### `media` decoration paths

- `media.settings.decoration.image`

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

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `feed` | `{{selector}} .et_pb_instagram_feed__items` |
| `item` | `{{selector}} .et_pb_instagram_feed__item` |
| `media` | `{{selector}} .et_pb_instagram_feed__media` |
| `followButton` | `{{selector}} .et_pb_instagram_feed__follow_button.et_pb_button` |
