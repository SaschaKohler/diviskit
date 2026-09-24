# Person
`divi/team-member`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `name.name.settings` | Name | content |
| `position.position.settings` | Position | content |
| `image.image.settings` | image.image.settings | — |
| `content.content.settings` | Body | — |
| `social.social.settings` | social.social.settings | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `image` | `object` | `image.settings.decoration.image` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `name` | `object` | `name.settings.decoration.font`, `name.settings.decoration.font.headingLevel` |
| `position` | `object` | `position.settings.decoration.font` |
| `social` | `object` | `social.settings.decoration.icon`, `social.settings.decoration.icon.item`, `social.settings.decoration.icon.item.icon` |
| `style` | `object` | — |

### `content` decoration paths

- `content.settings.decoration.bodyFont`

### `image` decoration paths

- `image.settings.decoration.image`

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

### `name` decoration paths

- `name.settings.decoration.font`
- `name.settings.decoration.font.headingLevel`

### `position` decoration paths

- `position.settings.decoration.font`

### `social` decoration paths

- `social.settings.decoration.icon`
- `social.settings.decoration.icon.item`
- `social.settings.decoration.icon.item.icon`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `name` | `{{selector}}.et_pb_team_member h4, {{selector}}.et_pb_team_member h1.et_pb_module_header, {{selector}}.et_pb_team_member...` |
| `position` | `{{selector}}.et_pb_team_member .et_pb_member_position` |
| `image` | `{{selector}} .et_pb_team_member_image img` |
| `content` | `{{selector}} .et_pb_team_member_description_content > div` |
| `social` | `{{selector}} .et_pb_member_social_links a` |
