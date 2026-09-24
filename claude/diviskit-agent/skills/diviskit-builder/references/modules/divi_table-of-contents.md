# Table of Contents
`divi/table-of-contents`

- Category: `module`
- Divi version: 5.13
- Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`

## innerContent

| Path | Label | Preset |
|------|-------|--------|
| `title.title.settings` | Title | content |
| `list.list.settings` | list.list.settings | — |
| `emptyState.emptyState.settings` | Empty State Message | content |

## Element Map

| Element | Type | Decoration Paths |
|---------|------|-----------------|
| `className` | `string` | — |
| `emptyState` | `object` | `emptyState.settings.decoration.font` |
| `list` | `object` | `list.settings.decoration.font` |
| `list1` | `object` | `list1.settings.decoration.font` |
| `list2` | `object` | `list2.settings.decoration.font` |
| `list3` | `object` | `list3.settings.decoration.font` |
| `list4` | `object` | `list4.settings.decoration.font` |
| `list5` | `object` | `list5.settings.decoration.font` |
| `list6` | `object` | `list6.settings.decoration.font` |
| `lock` | `object` | — |
| `marker` | `object` | `marker.settings.decoration.font` |
| `metadata` | `object` | — |
| `module` | `object` | `module.settings.decoration.animation`, `module.settings.decoration.attributes`, `module.settings.decoration.background`, `module.settings.decoration.border`, `module.settings.decoration.boxShadow`, `module.settings.decoration.conditions`, `module.settings.decoration.disabledOn`, `module.settings.decoration.filters` *(+12 more)* |
| `style` | `object` | — |
| `title` | `object` | `title.settings.decoration.font`, `title.settings.decoration.font.headingLevel` |

### `emptyState` decoration paths

- `emptyState.settings.decoration.font`

### `list` decoration paths

- `list.settings.decoration.font`

### `list1` decoration paths

- `list1.settings.decoration.font`

### `list2` decoration paths

- `list2.settings.decoration.font`

### `list3` decoration paths

- `list3.settings.decoration.font`

### `list4` decoration paths

- `list4.settings.decoration.font`

### `list5` decoration paths

- `list5.settings.decoration.font`

### `list6` decoration paths

- `list6.settings.decoration.font`

### `marker` decoration paths

- `marker.settings.decoration.font`

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
| `title` | `{{selector}} .et_pb_table_of_contents__title` |
| `list` | `{{selector}} .et_pb_table_of_contents__nav .et_pb_table_of_contents__link` |
| `list1` | `{{selector}} .et_pb_table_of_contents__list--level-1 > .et_pb_table_of_contents__link` |
| `list2` | `{{selector}} .et_pb_table_of_contents__list--level-2 > .et_pb_table_of_contents__link` |
| `list3` | `{{selector}} .et_pb_table_of_contents__list--level-3 > .et_pb_table_of_contents__link` |
| `list4` | `{{selector}} .et_pb_table_of_contents__list--level-4 > .et_pb_table_of_contents__link` |
| `list5` | `{{selector}} .et_pb_table_of_contents__list--level-5 > .et_pb_table_of_contents__link` |
| `list6` | `{{selector}} .et_pb_table_of_contents__list--level-6 > .et_pb_table_of_contents__link` |
| `marker` | `{{selector}} .et_pb_table_of_contents__marker` |
| `emptyState` | `{{selector}} .et_pb_table_of_contents__empty` |
