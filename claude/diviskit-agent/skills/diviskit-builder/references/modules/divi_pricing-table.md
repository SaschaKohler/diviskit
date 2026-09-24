# Pricing Table
`divi/pricing-table`

- Category: `child-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `currencyFrequency.currencyFrequency.settings` | currencyFrequency.currencyFrequency.settings | — |
| `subtitle.subtitle.settings` | Subtitle | content |
| `title.title.settings` | Title | content |
| `price.price.settings` | Price | content |
| `button.button.settings` | Button | — |
| `content.content.settings` | Body | — |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `button` | `object` | `button.settings.decoration.background`, `button.settings.decoration.border`, `button.settings.decoration.boxShadow`, `button.settings.decoration.button`, `button.settings.decoration.button.alignment`, `button.settings.decoration.font`, `button.settings.decoration.sizing`, `button.settings.decoration.spacing` |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `currencyFrequency` | `object` | `currencyFrequency.settings.decoration.font`, `currencyFrequency.settings.decoration.font.headingLevel` |
| `excluded` | `object` | `excluded.settings.decoration.font` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+11 more)* |
| `price` | `object` | `price.settings.decoration.background`, `price.settings.decoration.border`, `price.settings.decoration.font` |
| `style` | `object` | — |
| `subtitle` | `object` | `subtitle.settings.decoration.font`, `subtitle.settings.decoration.font.headingLevel` |
| `title` | `object` | `title.settings.decoration.background`, `title.settings.decoration.font`, `title.settings.decoration.font.item`, `title.settings.decoration.font.item.headingLevel` |

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

### `currencyFrequency` decoration paths

- `currencyFrequency.settings.decoration.font`
- `currencyFrequency.settings.decoration.font.headingLevel`

### `excluded` decoration paths

- `excluded.settings.decoration.font`

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
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `price` decoration paths

- `price.settings.decoration.background`
- `price.settings.decoration.border`
- `price.settings.decoration.font`

### `subtitle` decoration paths

- `subtitle.settings.decoration.font`
- `subtitle.settings.decoration.font.headingLevel`

### `title` decoration paths

- `title.settings.decoration.background`
- `title.settings.decoration.font`
- `title.settings.decoration.font.item`
- `title.settings.decoration.font.item.headingLevel`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `currencyFrequency` | `{{selector}} .et_pb_frequency` |
| `subtitle` | `{{selector}} .et_pb_best_value` |
| `title` | `{{selector}} .et_pb_pricing_heading h2,{{selector}} .et_pb_pricing_heading h1.et_pb_pricing_title,{{selector}} .et_pb_pr...` |
| `price` | `{{selector}} .et_pb_et_price .et_pb_sum` |
| `button` | `{{selector}} .et_pb_button` |
| `excluded` | `{{selector}} ul.et_pb_pricing li.et_pb_not_available,{{selector}} ul.et_pb_pricing li.et_pb_not_available span,{{selecto...` |
| `content` | `{{selector}} .et_pb_pricing_content` |
