# Timeline Item
`divi/timeline-item`

- Category: `child-module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `marker.marker.settings` | Icon | content |
| `date.date.settings` | Date | content |
| `title.title.settings` | Title | content |
| `content.content.settings` | Body | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `card` | `object` | `card.settings.decoration.background`, `card.settings.decoration.border`, `card.settings.decoration.boxShadow`, `card.settings.decoration.layout`, `card.settings.decoration.sizing`, `card.settings.decoration.sizing.item`, `card.settings.decoration.sizing.item.alignSelf`, `card.settings.decoration.sizing.item.flexType` *(+2 more)* |
| `className` | `string` | — |
| `connector` | `object` | `connector.settings.decoration.background`, `connector.settings.decoration.border`, `connector.settings.decoration.boxShadow`, `connector.settings.decoration.sizing`, `connector.settings.decoration.sizing.item`, `connector.settings.decoration.sizing.item.alignSelf`, `connector.settings.decoration.sizing.item.flexType`, `connector.settings.decoration.sizing.item.size` *(+1 more)* |
| `content` | `object` | `content.settings.decoration.bodyFont` |
| `date` | `object` | `date.settings.decoration.font` |
| `lock` | `object` | — |
| `marker` | `object` | `marker.settings.decoration.background`, `marker.settings.decoration.border`, `marker.settings.decoration.boxShadow`, `marker.settings.decoration.icon`, `marker.settings.decoration.icon.item`, `marker.settings.decoration.icon.item.icon`, `marker.settings.decoration.sizing`, `marker.settings.decoration.sizing.item` *(+3 more)* |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+10 more)* |
| `spacer` | `object` | `spacer.settings.decoration.background`, `spacer.settings.decoration.border`, `spacer.settings.decoration.boxShadow`, `spacer.settings.decoration.layout`, `spacer.settings.decoration.sizing`, `spacer.settings.decoration.sizing.item`, `spacer.settings.decoration.sizing.item.flexType`, `spacer.settings.decoration.spacing` |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font` |

### `card` decoration paths

- `card.settings.decoration.background`
- `card.settings.decoration.border`
- `card.settings.decoration.boxShadow`
- `card.settings.decoration.layout`
- `card.settings.decoration.sizing`
- `card.settings.decoration.sizing.item`
- `card.settings.decoration.sizing.item.alignSelf`
- `card.settings.decoration.sizing.item.flexType`
- `card.settings.decoration.sizing.item.size`
- `card.settings.decoration.spacing`

### `connector` decoration paths

- `connector.settings.decoration.background`
- `connector.settings.decoration.border`
- `connector.settings.decoration.boxShadow`
- `connector.settings.decoration.sizing`
- `connector.settings.decoration.sizing.item`
- `connector.settings.decoration.sizing.item.alignSelf`
- `connector.settings.decoration.sizing.item.flexType`
- `connector.settings.decoration.sizing.item.size`
- `connector.settings.decoration.spacing`

### `content` decoration paths

- `content.settings.decoration.bodyFont`

### `date` decoration paths

- `date.settings.decoration.font`

### `marker` decoration paths

- `marker.settings.decoration.background`
- `marker.settings.decoration.border`
- `marker.settings.decoration.boxShadow`
- `marker.settings.decoration.icon`
- `marker.settings.decoration.icon.item`
- `marker.settings.decoration.icon.item.icon`
- `marker.settings.decoration.sizing`
- `marker.settings.decoration.sizing.item`
- `marker.settings.decoration.sizing.item.alignSelf`
- `marker.settings.decoration.sizing.item.flexType`
- `marker.settings.decoration.spacing`

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
- `module.settings.decoration.order`
- `module.settings.decoration.overflow`
- `module.settings.decoration.position`
- `module.settings.decoration.scroll`
- `module.settings.decoration.sizing`
- `module.settings.decoration.spacing`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `spacer` decoration paths

- `spacer.settings.decoration.background`
- `spacer.settings.decoration.border`
- `spacer.settings.decoration.boxShadow`
- `spacer.settings.decoration.layout`
- `spacer.settings.decoration.sizing`
- `spacer.settings.decoration.sizing.item`
- `spacer.settings.decoration.sizing.item.flexType`
- `spacer.settings.decoration.spacing`

### `title` decoration paths

- `title.settings.decoration.font`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selectorPrefix}}.et_pb_timeline {{baseSelector}}` |
| `spacer` | `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_spacer` |
| `connector` | `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_connector` |
| `marker` | `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_marker` |
| `card` | `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_card` |
| `date` | `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_date` |
| `title` | `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} h1.et_pb_timeline_title, {{selectorPrefix}}.et_pb_timeline {{baseSele...` |
| `content` | `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_content` |
