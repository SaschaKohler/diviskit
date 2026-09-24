# Custom Field
`divi/signup-custom-field`

- Category: `child-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `fieldItem.fieldItem.settings` | Title | content |
| `conditionalLogic.conditionalLogic.settings` | Rules | ['html'] |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `checkbox` | `object` | — |
| `className` | `string` | — |
| `conditionalLogic` | `object` | — |
| `field` | `object` | `field.settings.decoration.background`, `field.settings.decoration.font` |
| `fieldItem` | `object` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+9 more)* |
| `radio` | `object` | — |
| `style` | `object` | — |

### `field` decoration paths

- `field.settings.decoration.background`
- `field.settings.decoration.font`

### `module` decoration paths

- `module.settings.decoration.animation`
- `module.settings.decoration.attributes`
- `module.settings.decoration.background`
- `module.settings.decoration.border`
- `module.settings.decoration.boxShadow`
- `module.settings.decoration.conditions`
- `module.settings.decoration.disabledOn`
- `module.settings.decoration.filters`
- `module.settings.decoration.order`
- `module.settings.decoration.overflow`
- `module.settings.decoration.position`
- `module.settings.decoration.scroll`
- `module.settings.decoration.sizing`
- `module.settings.decoration.spacing`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `field` | `{{selector}}.et_pb_contact_field .input:not([type=checkbox]):not([type=radio])` |
| `checkbox` | `{{selector}} [type=checkbox]` |
| `radio` | `{{selector}} [type=radio]` |
| `fieldItem` | `{{selector}} .et_pb_contact_form_label` |
