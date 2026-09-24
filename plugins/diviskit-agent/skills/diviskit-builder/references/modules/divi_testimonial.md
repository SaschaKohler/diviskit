# Testimonial
`divi/testimonial`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `company.company.settings` | company.company.settings | — |
| `content.content.settings` | Body | content |
| `author.author.settings` | Author | content |
| `jobTitle.jobTitle.settings` | Job Title | content |
| `portrait.portrait.settings` | Image | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `author` | `object` | `author.settings.decoration.font` |
| `className` | `string` | — |
| `company` | `object` | `company.settings.decoration.font`, `company.settings.decoration.font.item`, `company.settings.decoration.font.item.textAlign` |
| `content` | `object` | `content.settings.decoration.bodyFont`, `content.settings.decoration.bodyFont.item`, `content.settings.decoration.bodyFont.item.body` |
| `jobTitle` | `object` | `jobTitle.settings.decoration.font`, `jobTitle.settings.decoration.font.item`, `jobTitle.settings.decoration.font.item.textAlign` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `portrait` | `object` | `portrait.settings.decoration.image` |
| `quoteIcon` | `object` | `quoteIcon.settings.decoration.background`, `quoteIcon.settings.decoration.icon` |
| `style` | `object` | — |
| `testimonialDescription` | `object` | — |

### `author` decoration paths

- `author.settings.decoration.font`

### `company` decoration paths

- `company.settings.decoration.font`
- `company.settings.decoration.font.item`
- `company.settings.decoration.font.item.textAlign`

### `content` decoration paths

- `content.settings.decoration.bodyFont`
- `content.settings.decoration.bodyFont.item`
- `content.settings.decoration.bodyFont.item.body`

### `jobTitle` decoration paths

- `jobTitle.settings.decoration.font`
- `jobTitle.settings.decoration.font.item`
- `jobTitle.settings.decoration.font.item.textAlign`

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

### `portrait` decoration paths

- `portrait.settings.decoration.image`

### `quoteIcon` decoration paths

- `quoteIcon.settings.decoration.background`
- `quoteIcon.settings.decoration.icon`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `company` | `{{selector}}.et_pb_testimonial .et_pb_testimonial_company, {{selector}}.et_pb_testimonial .et_pb_testimonial_company a` |
| `quoteIcon` | `{{selector}}.et_pb_testimonial:before` |
| `content` | `{{selector}}.et_pb_testimonial .et_pb_testimonial_content` |
| `author` | `{{selector}}.et_pb_testimonial .et_pb_testimonial_author` |
| `jobTitle` | `{{selector}}.et_pb_testimonial .et_pb_testimonial_position, {{selector}}.et_pb_testimonial .et_pb_testimonial_separator` |
| `portrait` | `{{selector}} .et_pb_testimonial_portrait` |
| `testimonialDescription` | `{{selector}} .et_pb_testimonial_description` |
