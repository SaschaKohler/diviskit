# Contact Form
`divi/contact-form`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `title.title.settings` | Title | content |
| `button.button.settings` | Submit Button | content |
| `email.email.settings` | Message Pattern | content |
| `redirect.redirect.settings` | Redirect URL | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `button` | `object` | `button.settings.decoration.background`, `button.settings.decoration.border`, `button.settings.decoration.boxShadow`, `button.settings.decoration.button`, `button.settings.decoration.font`, `button.settings.decoration.sizing`, `button.settings.decoration.spacing` |
| `captcha` | `object` | `captcha.settings.decoration.font`, `captcha.settings.decoration.font.textAlign` |
| `checkbox` | `object` | — |
| `className` | `string` | — |
| `email` | `object` | — |
| `field` | `object` | — |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `radio` | `object` | — |
| `redirect` | `object` | — |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `button` decoration paths

- `button.settings.decoration.background`
- `button.settings.decoration.border`
- `button.settings.decoration.boxShadow`
- `button.settings.decoration.button`
- `button.settings.decoration.font`
- `button.settings.decoration.sizing`
- `button.settings.decoration.spacing`

### `captcha` decoration paths

- `captcha.settings.decoration.font`
- `captcha.settings.decoration.font.textAlign`

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
- `title.settings.decoration.font.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `title` | `{{selector}} h1, {{selector}} h2.et_pb_contact_main_title, {{selector}} h3.et_pb_contact_main_title, {{selector}} h4.et_...` |
| `captcha` | `{{selector}}.et_pb_contact_form_container .et_pb_contact_right p` |
| `field` | `{{selector}} .input:not([type=checkbox]):not([type=radio])` |
| `checkbox` | `{{selector}} [type=checkbox]` |
| `radio` | `{{selector}} [type=radio]` |
| `button` | `{{selector}} .et_pb_contact_submit.et_pb_button` |
