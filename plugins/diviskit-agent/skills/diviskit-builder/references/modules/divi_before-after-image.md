# Before/After Image
`divi/before-after-image`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `beforeImage.beforeImage.settings` | beforeImage.beforeImage.settings | — |
| `afterImage.afterImage.settings` | afterImage.afterImage.settings | — |
| `beforeLabel.beforeLabel.settings` | Before Label | content |
| `afterLabel.afterLabel.settings` | After Label | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `afterImage` | `object` | `afterImage.settings.decoration.image` |
| `afterLabel` | `object` | — |
| `beforeImage` | `object` | `beforeImage.settings.decoration.image` |
| `beforeLabel` | `object` | — |
| `className` | `string` | — |
| `labels` | `object` | `labels.settings.decoration.background`, `labels.settings.decoration.border`, `labels.settings.decoration.font`, `labels.settings.decoration.spacing` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `slider` | `object` | — |
| `style` | `object` | — |

### `afterImage` decoration paths

- `afterImage.settings.decoration.image`

### `beforeImage` decoration paths

- `beforeImage.settings.decoration.image`

### `labels` decoration paths

- `labels.settings.decoration.background`
- `labels.settings.decoration.border`
- `labels.settings.decoration.font`
- `labels.settings.decoration.spacing`

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
| `beforeImage` | `{{selector}} .et_pb_before_image img` |
| `afterImage` | `{{selector}} .et_pb_after_image img` |
| `slider` | `{{selector}}` |
| `beforeLabel` | `{{selector}} .et_pb_before_label` |
| `afterLabel` | `{{selector}} .et_pb_after_label` |
| `labels` | `{{selector}} .et_pb_before_label, {{selector}} .et_pb_after_label` |
