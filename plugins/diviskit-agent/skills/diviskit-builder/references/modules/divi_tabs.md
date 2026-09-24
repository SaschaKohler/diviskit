# Tabs
`divi/tabs`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `activeTab` | `object` | `activeTab.settings.decoration.background`, `activeTab.settings.decoration.font`, `activeTab.settings.decoration.font.item`, `activeTab.settings.decoration.font.item.textAlign` |
| `className` | `string` | — |
| `content` | `object` | `content.settings.decoration.background`, `content.settings.decoration.bodyFont` |
| `lock` | `object` | — |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters`, `module.settings.decoration.interactions` *(+10 more)* |
| `style` | `object` | — |
| `tab` | `object` | `tab.settings.decoration.background`, `tab.settings.decoration.font`, `tab.settings.decoration.font.item`, `tab.settings.decoration.font.item.textAlign` |

### `activeTab` decoration paths

- `activeTab.settings.decoration.background`
- `activeTab.settings.decoration.font`
- `activeTab.settings.decoration.font.item`
- `activeTab.settings.decoration.font.item.textAlign`

### `content` decoration paths

- `content.settings.decoration.background`
- `content.settings.decoration.bodyFont`

### `module` decoration paths

- `module.settings.decoration.animation`
- `module.settings.decoration.attributes`
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
- `module.settings.decoration.sticky`
- `module.settings.decoration.transform`
- `module.settings.decoration.transition`
- `module.settings.decoration.zIndex`

### `tab` decoration paths

- `tab.settings.decoration.background`
- `tab.settings.decoration.font`
- `tab.settings.decoration.font.item`
- `tab.settings.decoration.font.item.textAlign`

## CSS Selectors

| Element | Selector |
|---------|----------|
| `module` | `{{selector}}` |
| `content` | `{{selector}} > .et_pb_all_tabs` |
| `tab` | `{{selector}} .et_pb_tabs_controls li, {{selector}} .et_pb_tabs_controls li a` |
| `activeTab` | `{{selector}} .et_pb_tabs_controls li.et_pb_tab_active, {{selector}} .et_pb_tabs_controls li.et_pb_tab_active a` |
