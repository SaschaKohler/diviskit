# Social Media Follow
`divi/social-media-follow`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `button.button.settings` | button.button.settings | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `button` | `object` | `button.settings.decoration.button`, `button.settings.decoration.button.alignment`, `button.settings.decoration.button.buttonIconGroup`, `button.settings.decoration.button.fontGroup` |
| `className` | `string` | — |
| `icon` | `object` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `socialNetwork` | `object` | — |
| `style` | `object` | — |

### `button` decoration paths

- `button.settings.decoration.button`
- `button.settings.decoration.button.alignment`
- `button.settings.decoration.button.buttonIconGroup`
- `button.settings.decoration.button.fontGroup`

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
| `module` | `{{selectorPrefix}}ul{{baseSelector}}` |
| `icon` | `{{selector}} li.et_pb_social_icon a.icon, {{selector}} li.et_pb_social_icon a.icon:before` |
| `button` | `body #page-container .et_pb_section ul{{baseSelector}} .follow_button` |
