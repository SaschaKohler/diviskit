# Design Elements — reusable component recipes

Site-agnostic recipes for non-generic components. Adapt class names and token
references (`gcid-*`, `--vn-*`) to the target site's design system.

## Index Card (numbered editorial card)

Flat editorial entry instead of a decorated box: hairline top rule, running
index number (`01`, `02`, …), heading, text. No fill, no radius, no shadow,
no decorative pseudo-elements. Hover = rule color change only.

Why it reads designed, not generated: the standard AI card (rounded white box +
soft border + corner ornament + icon + hover lift) is replaced by typographic
rhythm — number, rule, type. Pairs naturally with a 2- or 3-col CSS-grid row;
even card counts read best (`gridColumnCount: "2"` for 4 cards → 2×2).

### Anatomy (Divi 5)

Put everything expressible into a **column module preset** (VB-editable, roll
out via `diviskit_preset_reassign`):

- `attributes` → class `vn-idx-card` (or site equivalent), `targetElement: "main"`
- `spacing.padding` → asymmetric, left `0px` (grid gap carries separation)
- `border.desktop.value.styles.top` → `{width: "1px", color: <line token>, style: "solid"}`;
  `border.desktop.hover.styles.top` → accent color; Divi emits the transition
- `layout.justifyContent` → `flex-start`
- `animation` → reuse the site's standard (e.g. slideBottom)

Child-theme CSS only for what module attrs cannot express — the running
counter and suppressing a decorative leading icon:

```css
/* counter reset on whichever row contains index cards — no row class needed */
.et_pb_row:has(.vn-idx-card) { counter-reset: vn-idx; }

.vn-idx-card::before {
  counter-increment: vn-idx;
  content: counter(vn-idx, decimal-leading-zero);
  display: block;
  /* small, letterspaced, accent color */
}

/* hide the decorative icon paragraph inside the card text module */
.vn-idx-card .vn-card-content > p:first-child:has(svg) { display: none; }
```

The card's inner text module keeps the site's normal typography preset stack —
the recipe changes the container, not the type.

### Pitfalls

- **Grid rows need inline `display:"grid"`**: `DetectFeature::has_css_grid_layout_enabled`
  regexes post_content for `"layout":{…"display":"grid"` — preset-provided grid
  structure is invisible to it. After any et-cache rebuild the
  `.et_grid_row{display:grid}` base CSS is missing and grid columns stack
  full-width. Always write `advanced.flexColumnStructure` + `layout.display:grid`
  + `gridColumnWidths` inline on preset-bound grid rows. Check
  `_divi_dynamic_assets_cached_feature_used` → `css_grid_layout_enabled` must
  be `[true]`, not `[]`.
- **Partial rollouts**: `preset_reassign` swaps per page, not per row. To
  convert only some card rows on a page, rewrite `modulePreset` in
  post_content directly (`$wpdb->update` byte-write path).
- Keep a card-type boundary: converting *every* card variant (value, feature,
  testimonial) flattens the hierarchy — pick which grids carry the look.

### Reference implementation

Verena site (`verena` project): preset `Vn Index Card`
`a9ef464f-5b0b-41ad-a604-2a4d116159e0`, border `gcid-9l1grdx6di` → hover
`gcid-goldbase01`, counter in Abel/gold, applied to all even-count value-card
grids (see project `AGENTS.md`).
