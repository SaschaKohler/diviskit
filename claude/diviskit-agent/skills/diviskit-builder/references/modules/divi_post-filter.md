# Post Filter
`divi/post-filter`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `button` | `object` | `button.settings.decoration.background`, `button.settings.decoration.border`, `button.settings.decoration.boxShadow`, `button.settings.decoration.button`, `button.settings.decoration.button.buttonIconGroup`, `button.settings.decoration.font`, `button.settings.decoration.sizing`, `button.settings.decoration.spacing` |
| `checkbox` | `object` | — |
| `className` | `string` | — |
| `field` | `object` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `multipleOrderButton` | `object` | `multipleOrderButton.settings.decoration.button`, `multipleOrderButton.settings.decoration.button.buttonIconGroup` |
| `option` | `object` | `option.settings.decoration.background`, `option.settings.decoration.background.item`, `option.settings.decoration.background.item.image`, `option.settings.decoration.border`, `option.settings.decoration.boxShadow`, `option.settings.decoration.sizing`, `option.settings.decoration.spacing` |
| `radio` | `object` | — |
| `style` | `object` | — |

### `button` decoration paths

- `button.settings.decoration.background`
- `button.settings.decoration.border`
- `button.settings.decoration.boxShadow`
- `button.settings.decoration.button`
- `button.settings.decoration.button.buttonIconGroup`
- `button.settings.decoration.font`
- `button.settings.decoration.sizing`
- `button.settings.decoration.spacing`

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

### `multipleOrderButton` decoration paths

- `multipleOrderButton.settings.decoration.button`
- `multipleOrderButton.settings.decoration.button.buttonIconGroup`

### `option` decoration paths

- `option.settings.decoration.background`
- `option.settings.decoration.background.item`
- `option.settings.decoration.background.item.image`
- `option.settings.decoration.border`
- `option.settings.decoration.boxShadow`
- `option.settings.decoration.sizing`
- `option.settings.decoration.spacing`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `field` | `{{selector}} .et_pb_post_filter__item-control-surface .et_pb_post_filter__item-control:not([type=checkbox]):not([type=ra...` |
| `option` | `{{selector}} .et_pb_post_filter__item-option` |
| `checkbox` | `{{selector}} .et_pb_post_filter__item-control[type=checkbox]` |
| `radio` | `{{selector}} .et_pb_post_filter__item-control[type=radio]` |
| `multipleOrderButton` | `{{selector}} .et_pb_post_filter__item-multiple-order-action` |
| `button` | `{{selector}} .et_pb_post_filter__item-control-button` |
