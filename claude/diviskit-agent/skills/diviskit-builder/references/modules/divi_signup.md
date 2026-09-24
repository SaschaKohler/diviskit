# Email Optin
`divi/signup`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `content.content.settings` | Body | content |
| `button.button.settings` | Button | content |
| `footerContent.footerContent.settings` | Footer | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `button` | `object` | `button.settings.decoration.background`, `button.settings.decoration.border`, `button.settings.decoration.boxShadow`, `button.settings.decoration.button`, `button.settings.decoration.button.alignment`, `button.settings.decoration.font`, `button.settings.decoration.sizing`, `button.settings.decoration.spacing` |
| `checkbox` | `object` | — |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `customFields` | `object` | — |
| `field` | `object` | — |
| `footerContent` | `object` | — |
| `formField` | `object` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `radio` | `object` | — |
| `resultMessage` | `object` | `resultMessage.settings.decoration.font` |
| `style` | `object` | — |
| `success` | `object` | — |
| `title` | `object` | `title.settings.decoration.font` |

### `button` decoration paths

- `button.settings.decoration.background`
- `button.settings.decoration.border`
- `button.settings.decoration.boxShadow`
- `button.settings.decoration.button`
- `button.settings.decoration.button.alignment`
- `button.settings.decoration.font`
- `button.settings.decoration.sizing`
- `button.settings.decoration.spacing`

### `content` decoration paths

- `content.settings.decoration.bodyFont`

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

### `resultMessage` decoration paths

- `resultMessage.settings.decoration.font`

### `title` decoration paths

- `title.settings.decoration.font`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `title` | `{{selector}} .et_pb_newsletter_description h2, {{selector}} .et_pb_newsletter_description h1.et_pb_module_header, {{sele...` |
| `content` | `{{selector}} .et_pb_newsletter_description div` |
| `button` | `body #page-container {{selector}}.et_pb_subscribe .et_pb_newsletter_button.et_pb_button` |
| `field` | `{{selector}} .input:not([type=checkbox]):not([type=radio])` |
| `checkbox` | `{{selector}} [type=checkbox]` |
| `radio` | `{{selector}} [type=radio]` |
| `footerContent` | `{{selector}} .et_pb_newsletter_footer` |
