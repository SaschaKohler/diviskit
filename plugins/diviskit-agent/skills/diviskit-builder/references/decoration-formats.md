# Divi 5 Decoration & Pattern Families (Tier 1 + Tier 2)

Universal `module.decoration.*` structure and the font/icon pattern families
shared across all modules. Pair with [module-formats.md](module-formats.md)
(Tier 3 per-module reference) and [modules/](modules/) (auto-generated
per-element detail).

---

## Tier 1 — Common Decoration (all modules)

Every Divi module supports `module.decoration.*` for visual styling. This is the universal base — document it once, applies everywhere.

```json
{
  "module": {
    "decoration": {
      "border": {
        "desktop": {
          "value": {
            "radius": {"topLeft": "12px", "topRight": "12px", "bottomLeft": "12px", "bottomRight": "12px", "sync": "on"},
            "styles": {"all": {"width": "2px", "color": "#6366f1"}}
          },
          "hover": {"styles": {"all": {"color": "#f59e0b"}}}
        }
      },
      "background": {
        "desktop": {
          "value": {
            "color": "#0f172a",
            "gradient": {"enabled": "on", "stops": [{"position": "0", "color": "#0f172a"}, {"position": "100", "color": "#1e3a5f"}]},
            "image": {"url": "https://example.com/image.jpg"}
          },
          "hover": {"color": "#1e293b"}
        }
      },
      "spacing": {
        "desktop": {"value": {"padding": {"top": "20px", "bottom": "20px", "left": "20px", "right": "20px", "syncVertical": "on", "syncHorizontal": "on"}, "margin": {"top": "10px", "syncVertical": "off", "syncHorizontal": "off"}}},
        "tablet": {"value": {"padding": {"top": "15px", "bottom": "15px", "left": "15px", "right": "15px", "syncVertical": "on", "syncHorizontal": "on"}}}
      },
      "sizing": {"desktop": {"value": {"maxWidth": "800px", "width": "42rem", "flexType": "8_24"}}},
      "overflow": {"desktop": {"value": {"x": "hidden", "y": "hidden"}}},
      "animation": {"desktop": {"value": {"style": "slide", "direction": "left", "duration": "800ms", "delay": "200ms", "speedCurve": "ease-in-out", "intensity": {"slide": "20%"}, "repeat": "once", "startingOpacity": "5%"}}},
      "scroll": {"desktop": {"value": {"verticalMotion": {"enable": "on", "offset": {"start": "2", "mid": "0", "end": "-2"}, "viewport": {"bottom": "0", "end": "50", "start": "50", "top": "100"}}, "motionTriggerStart": "middle"}}}
    }
  },
  "builderVersion": "5.1.1"
}
```

### Key rules
- **Hover (decoration blocks)**: in `*.decoration.*` paths, hover goes as `desktop.hover` — **inside** the breakpoint object, sibling of `value`. Exception: `icon.advanced.color.desktop.hover` is a scalar value, not an object
- **Hover level is load-bearing — `font.hover` crashes the VB** *(VB-verified 2026-09-29, Divi 5.14)*: placing `hover` one level too high (sibling of `desktop`, e.g. `bodyFont.body.font.hover`) makes every affected module render "**Oops! An Error Has Occurred**" in the Visual Builder while the **frontend still renders correctly** — a silent builder-only failure with no save-time warning. A VB save does NOT strip the misplaced attr. Correct: `bodyFont.body.font.desktop.hover`.
- **Font hover shape requires `style`** *(VB-verified 2026-09-29, Divi 5.14)*: the VB emits font hover states as `{"style": [], "color": "<token-or-hex>"}` — include the `style` array (empty or e.g. `["underline"]`), not just `color`.
- **`$variable` token payloads carry `settings`** *(VB-verified 2026-09-29, Divi 5.14)*: the VB writes tokens as `$variable({"type":"color","value":{"name":"gcid-x","settings":{}}})$` — always include `"settings":{}` in the value payload.
- **Responsive**: add `tablet`/`phone` siblings to `desktop` — tablet inherits desktop, phone inherits tablet
- **Defaults omitted**: VB only exports values that differ from the active preset. Missing keys are resolved via the full cascade (preset → render attrs → printed style attrs → theme CSS), not errors. See "Default Value Resolution" below
- **Sync fields**: `syncVertical`/`syncHorizontal` control VB's paired editing UI
- **Gap**: use `columnGap` + `rowGap` separately, never single `gap`
- **flexType**: 24-unit grid for flex child sizing (`"8_24"` = 1/3, `"12_24"` = 1/2) — do NOT use flexGrow/flexBasis
- **Animation styles**: `fade`, `slide`, `bounce`, `zoom`, `flip`, `fold`, `roll`
- **Animation direction**: VB label = entrance direction (`"left"` = slides in from left)
- **Animation `intensity`**: nested by style name — `intensity.slide: "20%"`, `intensity.bounce: "30%"`, etc.
- **Animation `speedCurve`**: CSS-style with hyphens — `"ease-in-out"`, `"ease-in"`, `"ease-out"`, `"linear"` (different from transition's camelCase `"easeInOut"`)
- **Animation `repeat`**: `"once"` or `"loop"` (string, not boolean)
- **Scroll effects**: 6 types — `verticalMotion`, `horizontalMotion`, `rotating`, `scaling`, `fade`, `blur`
- **Scroll offset units vary**: vertical/horizontal = unitless, rotating = `°`, scaling/fade = `%`, blur = `px`
- **Scroll `motionTriggerStart`**: `"top"`, `"middle"` (default), `"bottom"` — shared across all effects
- **Scroll + animation**: scroll effects override entrance animation when both active

### Grid layout and offset rules *(Divi 5.9.0, source-verified 2026-07-29)*

Grid settings live in the normal layout value object. Offset rules only emit CSS when the same breakpoint/state has `display: "grid"`.

```json
"module": {
  "decoration": {
    "layout": {
      "desktop": {
        "value": {
          "display": "grid",
          "gridOffsetRules": {
            "rules": [
              {
                "targetOffset": "first-child",
                "offsetValues": {
                  "columnStart": "2",
                  "columnSpan": "2",
                  "rowStart": "1"
                }
              }
            ]
          }
        }
      }
    }
  }
}
```

- **Canonical 5.9.0 inner shape**: each rule has one `targetOffset` and one `offsetValues` map, so a target can carry several start/end/span values together. Do not author the legacy `{"offsetRule":"columnStart","offsetValue":"2"}` pair.
- **Allowed `offsetValues` keys**: `columnSpan`, `columnStart`, `columnEnd`, `rowSpan`, `rowStart`, `rowEnd`, and `gridTemplateColumns`. Values are strings. Span keys emit `grid-column-end: span N` / `grid-row-end: span N`; number-variable tokens are resolved before CSS generation.
- **Targets**: `"first-child"` and `"last-child"` use the corresponding direct-child selector. Other values become `:nth-of-type(<value>)`; use `targetOffset: "custom"` plus `customTargetOffset` for a custom nth expression.
- **Migration coverage**: Divi 5.9.0 rewrites legacy content during portability import, frontend migration, and VB raw-content load. Its preset migration rewrites `attrs`, `styleAttrs`, and `renderAttrs` for module and group presets in VB/REST contexts, then stamps changed preset items at `5.9.0`.
- **Column-width normalization**: the same migration converts `gridColumnWidths: "equalMinimum"` or `"equalFixed"` to `"equal"` only when the corresponding minimum/fixed width field is empty.
- **Evidence tier**: the shape and migration behavior are source-verified against Divi 5.9.0. Treat them as schema-canonical; a VB round-trip has not yet been stamped here.

### Universal Element Decoration (Composable Settings)

Since Divi 5.1.1, decoration groups are universally available on **any element** via Composable Settings (`dynamicSubgroupHost`). The `module.decoration.*` pattern documented above applies identically to any named element:

> `{element}.decoration.{background, border, sizing, spacing, boxShadow, filters, animation, transform, ...}`

**Examples**: `button.decoration.background`, `imageIcon.decoration.sizing`, `tab.decoration.font.font`, `arrows.decoration.border`, `openToggle.decoration.background`

**Implication for Tier 3**: Per-module docs only list **element names**, **innerContent shapes**, and **surprises** (non-standard fields or paths that break the universal pattern). Standard decoration on any element is assumed — never repeated.

**`dynamicOptionGroups`**: When a user **dynamically adds a design sub-group** via the Composable Settings "+" affordance (on elements with `dynamicSubgroupHost: true`), a top-level `dynamicOptionGroups` key is written to track what was enabled. Format: `{"element": {"groupName": {"decoration": {"groupType": true}}}}`. Example (Button, layout sub-group added to `button` element): `{"button": {"button": {"decoration": {"layout": true}}}}`. Informational only — decoration paths work regardless. Applying values to existing default groups (e.g. box shadow on the Module wrapper) does NOT write this key.

### Verification depth

| Decoration option | Status | Notes |
|-------------------|--------|-------|
| `border` (radius, styles, hover) | ✅ Verified | 13+ modules confirmed |
| `background` (color, gradient, image) | ✅ Verified | gradient requires `enabled: "on"`, position as strings |
| `background` (video, pattern, masks) | ✅ Verified | Full structures documented below: video (5 attrs), pattern (24 styles, 10 attrs), mask (23 styles, 11 attrs) |
| `spacing` (padding, margin, sync) | ✅ Verified | 13+ modules confirmed |
| `sizing` (width, height, maxWidth) | ✅ Verified | Image exception: uses `module.advanced.sizing` |
| `sizing.flexType` (column sizing) | ✅ Verified | 24-unit grid: `"8_24"` = 1/3, `"12_24"` = 1/2 — use on flex children, NOT flexGrow/flexBasis |
| `overflow` (x, y) | ✅ Verified | Section, Row, Column, Group |
| `animation` (full depth) | ✅ Verified | style, direction, duration, delay, speedCurve, `intensity.{style}` (nested by style name), repeat (`"loop"`/`"once"`), startingOpacity |
| `scroll` (all 6 effects) | ✅ Verified | 6 effects: verticalMotion, horizontalMotion, rotating, scaling, fade, blur. Each: `{enable, offset: {start,mid,end}, viewport: {bottom,end,start,top}}`. `motionTriggerStart`: `"top"`/`"middle"`/`"bottom"` |
| `boxShadow` | ✅ Verified | 7 props: horizontal, vertical, blur, spread, position, color, style. `position: "inner"` = inset, `"outer"` = outset. Hover sparse |
| `filters` | ✅ Verified | 8 props: brightness, blur, contrast, saturate, opacity, invert, sepia, hueRotate (camelCase). All strings with units |
| `transform` | ✅ Verified | Sub-objects: scale, rotate, translate, skew, origin. Each has x/y (rotate also z). Scale uses `%` not decimal. `linked: "on"/"off"` |
| `position` + `zIndex` | ✅ Verified | `position.mode`, `position.origin.absolute`, `position.offset.vertical/horizontal`. **zIndex is separate**: `decoration.zIndex` |
| `transition` | ✅ Verified | duration (`"400ms"`), delay (`"200ms"`), speedCurve (`"easeInOut"` camelCase) |
| `customCSS` | ✅ Verified | **Top-level `css` key** (not inside `module`). Selectors: `mainElement`, `before`, `after`. Responsive: `css.tablet.value.*` |
| `semanticHTML` | ✅ Verified | `module.advanced.html.desktop.value.elementType` — 22 tags available. `htmlBefore`/`htmlAfter` for raw HTML/wrapper injection |
| `interactions` | ✅ Verified | VB roundtrip confirmed. `module.decoration.interactions.desktop.value.interactions[]` + `interactionTrigger`/`interactionTarget` markers. See interactions section below |
| `disabledOn` | ✅ Verified | `module.decoration.disabledOn.{desktop,tablet,phone}.value` — `"on"`/`"off"` per breakpoint |
| `dividers` (Section only) | ✅ Verified | `module.advanced.dividers.{top,bottom}` — 26 shapes, 6 settings. See Dividers section below |

### Dividers (Section only) *(VB-verified 2026-03-23)*

Decorative shape dividers at top/bottom of Sections. Path: `module.advanced.dividers.{top,bottom}`.

```json
"dividers": {
  "top": {"desktop": {"value": {"style": "wave", "height": "120px", "color": "#6366f1", "repeat": "1x", "flip": [], "arrangement": "below"}}},
  "bottom": {"desktop": {"value": {"style": "mountains", "height": "80px", "color": "#1e293b", "repeat": "1x", "flip": ["horizontal"], "arrangement": "below"}}}
}
```

**Settings:**

| Setting | Type | Default | Values |
|---------|------|---------|--------|
| `style` | string | `"none"` | 26 shapes: `arrow`, `arrow2`, `arrow3`, `asymmetric`–`asymmetric4`, `clouds`, `clouds2`, `curve`, `curve2`, `graph`–`graph4`, `mountains`, `mountains2`, `ramp`, `ramp2`, `slant`, `slant2`, `triangle`, `wave`, `wave2`, `waves`, `waves2` |
| `height` | string | `"100px"` | CSS value (e.g. `"80px"`, `"5%"`) |
| `color` | string | auto | Hex, rgba, or `$variable()$`. When omitted, resolved from context (adjacent section background) |
| `repeat` | string | `"1x"` | Number + `x` suffix (e.g. `"2x"`, `"0.5x"`). Ignored when shape is non-repeatable (clouds, clouds2, triangle) |
| `flip` | array | `[]` | `["horizontal"]`, `["vertical"]`, or `["horizontal", "vertical"]` |
| `arrangement` | string | `"below"` | `"below"` (z-index 1) or `"above"` (z-index 10). Fullwidth Sections always use z-index 10 regardless |

- **Section only** — Row, Column, Group do NOT support dividers
- Responsive: add `tablet`/`phone` breakpoints as usual
- Non-repeatable shapes (clouds, clouds2, triangle) use `background-size: cover`

### Default Value Resolution

VB saves only values that differ from the active preset. Divi resolves styling through a 4-layer cascade:

```
Module instance (block JSON — explicit overrides only)
    ↓ fallback
Presets (two types: module presets + attribute-level presets)
    ↓ fallback
_all_modules_default_render_attributes.php (structural defaults: heading levels, toggle states)
    ↓ fallback
_all_modules_default_printed_style_attributes.php (default CSS styles generated per module)
    ↓ fallback
Divi theme CSS (base visual defaults: font-size, color, line-height, margins)
```

**Two types of presets:**
1. **Module presets** — apply to the whole module (e.g. "Dark" for Text). Only work on the module type they were created for. The module type's default preset is used implicitly when `modulePreset` is omitted.
2. **Attribute-level presets** — apply to specific attribute groups (e.g. a font preset, border preset). **Shareable across different module types** — a font preset from Text can be reused on Heading, Blurb, etc.

**`modulePreset` reference** (top-level block key):
- `"modulePreset": ["uuid"]` — primary form: array of one or more preset UUIDs (stacked; later entries override earlier)
- `"modulePreset": "uuid"` — legacy/unmigrated form: single string
- `"modulePreset": "default"` / `"_initial"` — sentinel values meaning "use the module type's default preset"
- Omit entirely to use the default preset

**Practical rules for MCP:**
- A bare module with no decoration attrs is valid — presets + CSS defaults handle styling
- Setting explicit values that match defaults is harmless (just increases JSON size)
- Do NOT strip defaults in MCP — we'd need the full cascade knowledge, which is fragile
- When comparing MCP output to VB output, "missing" attrs are preset defaults, not bugs

**Text alignment** uses `module.advanced.text.text.desktop.value.orientation` (not `textAlign`):
- Values: `"left"`, `"center"`, `"right"`, `"justify"`

### Gradient background
```json
{"module":{"decoration":{"background":{"desktop":{"value":{"gradient":{"enabled":"on","stops":[{"position":"0","color":"#7c3aed"},{"position":"100","color":"#2563eb"}]}}}}}}}
```
- **`enabled: "on"`** is REQUIRED — without it the gradient silently fails
- **`position`**: strings (`"0"`, `"50"`, `"100"`) — VB exports strings, not numbers
- **`type`** — VB-verified enum `"linear"` / `"circular"` / `"elliptical"` / `"conic"` (default linear), **not** `"radial"`. Same `GradientUtils` as text-fill gradients: `circular`→`radial-gradient(circle at …)`, `elliptical`→`radial-gradient(ellipse at …)`, `conic`→`conic-gradient(from <direction> at …)`. Render-verified on Divi 5.7.4 (2026-06-15): `type:"circular"` → `radial-gradient(circle at center,…)`, `type:"conic"` → `conic-gradient(from 45deg at center,…)`.
- `direction`: CSS angle (`"135deg"`, `"180deg"`) — used by linear + conic; optional, defaults to `"180deg"`
- `directionRadial`: position keyword (`"center"`, `"top left"`, …) — used by circular/elliptical/conic; defaults to `"center"`
- `stops[]`: array of `{position, color}` (min 2)
- Works on any module with `decoration.background`
- Gradient + color coexist (gradient on top); `gradient.overlaysImage: "on"` places gradient above image
- `gradient.repeat: "off"` — repeat toggle
- **Render-verified on Divi 5.7.4 (2026-06-14):** a Section authored at `module.decoration.background.desktop.value.gradient` (`enabled:"on"`, two `{position,color}` stops, `direction:"135deg"`) emits `.et_pb_section_0{background-image:linear-gradient(135deg,#2B87DA 0%,#29C4A9 100%)!important;background-repeat:no-repeat!important}` in the compiled module CSS.
- **Preset binding (Divi 5.7+)**: the canonical *preset-map* key for a background gradient is now `…background__gradient` (subName `gradient`, binds the whole gradient object), replacing the pre-5.7 `…background__gradient.stops` (subName `gradient.stops`, which bound only the stops array). The sibling `gradient.*` preset keys (`enabled`, `type`, `direction`, `directionRadial`, `repeat`, `length`, `overlaysImage`) are unchanged. The module-attr **value path above is unchanged** — author gradients at `…background.<breakpoint>.<state>.gradient.{enabled,stops[],…}` exactly as shown; only the preset-binding key shape moved. The new whole-object `gradient` slot also backs Divi 5.7's gradient global variables (a single slot can now carry a `gvid-…` reference).

### Video background
```json
{"module":{"decoration":{"background":{"desktop":{"value":{"video":{"mp4":"","webm":"https://example.com/video.webm","width":"","height":"650","allowPlayerPause":"on"}}}}}}}
```
- `mp4`/`webm`: separate URL fields (at least one required)
- `width`/`height`: strings, no units (pixels implied)
- `allowPlayerPause`: `"on"`/`"off"` — pause when another video plays
- `pauseOutsideViewport`: `"on"` (default, omitted when default)
- No poster image on Text modules (Video module may differ)

### Pattern background
```json
{"module":{"decoration":{"background":{"desktop":{"value":{"pattern":{"enabled":"on","style":"diamonds","color":"rgba(99, 102, 241, 0.15)","transform":["flipVertical"],"size":"cover","repeatOrigin":"right top","horizontalOffset":"1%","verticalOffset":"1%","repeat":"space","blend":"overlay"}}}}}}}
```
- **`enabled: "on"`** is REQUIRED
- **24 styles**: 3d-diamonds, checkerboard, confetti, crosses, cubes, diagonal-stripes, diagonal-stripes-2, diamonds, honeycomb, inverted-chevrons, inverted-chevrons-2, ogees, pills, pinwheel, polka-dots (default), scallops, shippo, smiles, squares, triangles, tufted, waves, zig-zag, zig-zag-2
- `transform`: array — any combination of `"flipVertical"`, `"flipHorizontal"`, `"rotate"`, `"invert"`
- `size`: `"cover"`, `"contain"`, `"stretch"`, or `"custom"` (use `width` and `height` fields for custom dimensions)
- `blend`: CSS blend mode — normal, multiply, screen, overlay, darken, lighten, color-dodge, color-burn, hard-light, soft-light, difference, exclusion, hue, saturation, color, luminosity
- `repeat`: `"repeat"`, `"space"`, `"no-repeat"`, etc.
- `repeatOrigin`: CSS position string (`"right top"`, `"center center"`)

### Mask background
```json
{"module":{"decoration":{"background":{"desktop":{"value":{"mask":{"enabled":"on","style":"wave","color":"rgba(0, 0, 0, 0.8)","transform":["flipHorizontal","invert"],"aspectRatio":"square","size":"cover","height":"100%","position":"center bottom","horizontalOffset":"1%","verticalOffset":"1%","blend":"multiply"}}}}}}}
```
- **`enabled: "on"`** is REQUIRED
- **23 styles**: arch, bean, blades, caret, chevrons, corner-blob, corner-lake, corner-paint, corner-pill, corner-square, diagonal, diagonal-bars, diagonal-bars-2, diagonal-pills, ellipse, floating-squares, honeycomb, layer-blob (default), paint, rock-stack, square-stripes, triangles, wave
- `transform`: array — any combination of `"flipHorizontal"`, `"flipVertical"`, `"rotate"`, `"invert"`
- `aspectRatio`: `"square"`, `"landscape"`, `"portrait"`
- `size`: `"cover"`, `"contain"`, `"stretch"`, `"custom"` (with `width` and `height` values)
- `position`: CSS position string (`"center bottom"`, `"left top"`)
- `blend`: same 16 CSS blend modes as pattern

## Tier 2 — Pattern Families

### Font Family A: bodyFont (HTML body text)
Used by: **Text** (body), **Testimonial** (content), **Accordion item** (content), **Slide** (content), **Blurb** (content)

```json
"content": {
  "innerContent": {"desktop": {"value": "\u003cp\u003eHTML text content\u003c/p\u003e"}},
  "decoration": {
    "bodyFont": {
      "body": {"font": {"desktop": {"value": {"color": "#94a3b8", "size": "1rem", "weight": "500", "lineHeight": "1.7em", "textAlign": "left", "style": ["italic"]}}}},
      "link": {"font": {"desktop": {"value": {"color": "#6366f1"}, "hover": {"style": [], "color": "#a78bfa"}}}}
    }
  }
}
```
- `content.innerContent` is HTML (unicode-escaped `\u003cp\u003e` tags)
- `bodyFont.link.font` for link color/hover (Text module only, others may not have link sub-font)
- Font `style` is an array for non-capitalization styles such as `["italic"]`, `["underline"]`, or `["italic", "underline"]`
- Divi 5.8.1+ capitalization is the dedicated scalar `capitalization`: `"uppercase"`, `"lowercase"`, `"capitalize"`, `"smallCaps"`, or `"allSmallCaps"`. Legacy capitalization tokens in `style` are migration input only; Divi moves them into `capitalization` for content and preset attr bags.
- Font `weight`: numeric string — Thin=`"100"`, Light=`"300"`, Regular=`"400"`, Medium=`"500"`, Semi Bold=`"600"`, Bold=`"700"`, Ultra Bold=`"800"`, Heavy=`"900"`
- Font size units: `px`, `rem`, `em`, `vw`, `clamp(22px, 3vw, 36px)`
- Divi 5.8.1+ adds `weightFineTune`, `opticalSizing`, `lineThickness`, `underlineOffset`, `textWrap`, `writingMode`, `hyphens`, `columnCount`, and `columnGap` inside the same `{breakpoint}.{state}.value` font object. Body paragraph spacing uses `bodyFont.body.list.<breakpoint>.<state>.value.paragraphSpacing`; ordered/unordered list spacing uses `bodyFont.{ol,ul}.list.<breakpoint>.<state>.value.listSpacing`.
- Body-font groups can style a drop cap at `bodyFont.dropCap.font.<breakpoint>.<state>.value`. Its canonical fields are `dropCapLineSize`, `dropCapSpacing`, `family`, `weight`, `weightFineTune`, `opticalSizing`, `color`, `capitalization`, `style`, `lineColor`, `lineThickness`, `underlineOffset`, and `lineStyle`; text shadow remains the sibling `bodyFont.dropCap.textShadow`.

### Font Family B: element.decoration.font.font (titles and form controls)
Used by: **Heading** (title), **Number Counter** (title, number), **Accordion item** (title), **Slide** (title), **Blurb** (title), **Testimonial** (author, jobTitle, company), **Contact Form** (title, captcha, field, button)

```json
"{element}": {
  "innerContent": {"desktop": {"value": "Plain text or object"}},
  "decoration": {
    "font": {
      "font": {
        "desktop": {
          "value": {"headingLevel": "h3", "color": "#ffffff", "size": "32px", "weight": "700", "textAlign": "center", "letterSpacing": "-1px", "lineHeight": "1.2em", "style": ["uppercase"]},
          "hover": {"color": "#6366f1"}
        },
        "tablet": {"value": {"size": "24px"}}
      }
    }
  }
}
```
- Double `font` nesting: `{element}.decoration.font.font`
- `headingLevel`: `"h1"` through `"h6"` — lives inside font object
- `innerContent` can be a plain string or an object (see innerContent variants below)
- The expanded Divi 5.8.1+ scalar fields listed under Font Family A (`weightFineTune`, `opticalSizing`, line controls, wrapping/writing/hyphenation, columns, and `capitalization`) also live in this inner `font` value object.

### Font Text Effects: element.decoration.font.textEffects *(Divi 5.7+, render-verified on Divi 5.7.4 2026-06-14)*
Available on any element that exposes a Font option (Heading `title`, Text `bodyFont`, Button `font`, etc.). `textEffects` is a sub-bucket of the font decoration, sitting parallel to the inner `font.font`:

`{element}.decoration.font.textEffects.{breakpoint}.{state}.value.{...}`

```json
"title": {"decoration": {"font": {"textEffects": {"desktop": {"value": {
  "fillType": "gradient",
  "gradient": {"enabled": "on", "stops": [{"position": "0", "color": "#7c3aed"}, {"position": "100", "color": "#2563eb"}], "type": "linear", "direction": "180deg"},
  "strokeColor": "#0f172a",
  "strokeWidth": "1px",
  "strokePosition": "stroke-fill"
}}}}}}
```

- **`fillType`** selects how the glyphs are filled: `"none"` (default — normal text color), `"gradient"` (gradient-filled text), `"image"` (image-filled text), `"transparent"` (no fill — pair with a stroke for outline-only text). Gradient and image fills render via `background-clip: text` + `-webkit-text-fill-color: transparent`.
- **`gradient`** (used when `fillType: "gradient"`): same shape as a background gradient — `enabled` (**`"on"` — include it**, see VB-compat note below), `stops[]` (min 2 `{position, color}`; `color` accepts a `$variable(gcid-…)$` color-variable token), and a `type` whose **VB-verified enum is `"linear"` / `"circular"` / `"elliptical"` / `"conic"`** (default linear) — **not** `"radial"`. `circular`→`radial-gradient(circle at …)`, `elliptical`→`radial-gradient(ellipse at …)`, `conic`→`conic-gradient(from … at …)`. `direction` (CSS angle, used by linear + conic; default `"180deg"`), `directionRadial` (position keyword, used by circular/elliptical/conic; default `"center"`), `repeat` (default `"off"`), `length` (default `"100%"`). **The VB emits only touched fields** — a plain linear save is just `{enabled, stops, direction, type:"linear"}` (no `length`/`directionRadial`); a radial save is `{enabled, stops, type:"circular", directionRadial}` (no `direction`).
- **VB-compat — include `gradient.enabled: "on"`:** the frontend render path gates a gradient text-fill only on `fillType: "gradient"` + ≥2 stops, so it renders *without* the `enabled` flag. The **VB editor is stricter** — it expects `gradient.enabled: "on"` (mirroring the background-gradient convention) to treat the text-fill gradient as actively set. Omit it and the glyphs render gradient-filled on the frontend but the VB Text Effects controls show the gradient as unconfigured; re-choosing the gradient in the VB re-adds `enabled: "on"`. VB-verified on Divi 5.7.4 (2026-06-14): a DiviOps-authored text-fill gradient missing `enabled` round-tripped to VB-canonical only after the VB re-added `gradient.enabled: "on"` (the sole meaningful delta).
- **`imageFill`** (used when `fillType: "image"`): `{url, size, width, height, position, horizontalOffset, verticalOffset, repeat, blend}` — same semantics as a Pattern/Image background. `url` accepts an image or gradient global-variable reference. **VB-verified minimal shape:** picking just an image saves `fillType:"image"` + `imageFill:{url:"…"}` (url only — other keys appear only when their controls are touched) and renders `background-image:url('…')` on the glyphs.
- **`strokeColor`** / **`strokeWidth`** apply independently of `fillType` (emitted as `-webkit-text-stroke-color` / `-webkit-text-stroke-width`). **Stroke-only is VB-canonical with NO `fillType` key at all** — the VB saves just `{strokeWidth, strokeColor}` and the stroke renders. For outline-only (transparent) glyphs the VB writes `fillType:"transparent"` + stroke (emits `background-image:none; -webkit-text-fill-color:transparent`).
- **`strokePosition`** (Divi 5.8.1+) is the canonical text-effects sibling for paint order. `"stroke-fill"` emits `paint-order: stroke`; the alternate position emits `paint-order: fill`. When omitted, Divi defaults strokes wider than `1` to stroke-first paint order.
- **Binding a gradient *global variable* (Divi 5.7.4):** choosing a gradient variable in the VB replaces `gradient.stops` (normally an array) with a **string token** — `"stops": "$variable({\"type\":\"gradient\",\"value\":{\"name\":\"gvid-…\",\"settings\":{}}})$"` — keeping `enabled:"on"`. The same token works in a background gradient's `gradient.stops`. **Caveat (verified 2026-06-15):** Divi resolves the token to CSS **only** if the referenced gvid is stored in its canonical structured shape (its `value` is itself a `$variable({type:gradient,value:{name:"gradient",settings:{stops[],type,direction,…}}})$` token). A gradient variable whose stored `value` is a plain CSS string (e.g. `linear-gradient(…)`) is **not** resolved: Divi emits the `var(--gvid-…)` reference but never defines the custom property, so the bound module renders nothing. Create gradient variables in the VB Variable Manager **or** via `diviskit_variable_create({type:"gradients", gradient:{stops:[…], type, direction, …}})` (diviskit-agent ≥ 1.5.4 / server ≥ 1.5.28), which serializes this exact structured token; a raw CSS-string `value` is rejected.
- **Preset-map keys** (canonical): `{font}.textEffects__fillType`, `…__gradient` (whole object) plus `…__gradient.{type,direction,directionRadial,repeat,length}`, `…__imageFill.{blend,height,horizontalOffset,position,repeat,size,url,verticalOffset,width}`, `…__strokeColor`, `…__strokeWidth`, and (5.8.1+) `…__strokePosition`.
- **Provenance**: paths + field semantics verified against Divi 5.7.4 `TextEffectsPresetAttrsMap` and the `TextEffects` style declaration; the `{breakpoint}.{state}.value` wrapper follows the universal decoration convention. **Render-verified on Divi 5.7.4 (2026-06-14):** a Heading authored at `title.decoration.font.textEffects.desktop.value` with `fillType: "gradient"` + `strokeWidth`/`strokeColor` emits, in the compiled module CSS, `.et_pb_heading_0 …h1..h6{-webkit-text-stroke-width:1px;-webkit-text-stroke-color:#111111;background-image:linear-gradient(120deg,#ff0080 0%,#7928ca 100%);background-repeat:no-repeat;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}`. Note the nesting trap: `textEffects` is a sibling of the inner `font` (i.e. `…font.textEffects.{breakpoint}.{state}.value.*`), **not** nested under `font.{breakpoint}.value.textEffects` — the latter silently emits no CSS. **VB-matrix-verified on Divi 5.7.4 (2026-06-15)** by authoring each fill type in the VB and reading back stored attrs + compiled CSS: linear → `linear-gradient(90deg,…)`, radial (`type:"circular"`) → `radial-gradient(circle at center,…)`, image → `background-image:url('…')`, transparent+stroke, and stroke-only (no `fillType`) — all render; the `type` enum and stroke-only / imageFill shapes above are taken directly from those read-backs (see `docs/vb-verification-process.md`).

### Icon Family: element.decoration.icon
Used by: **Testimonial** (quoteIcon), **Video** (playIcon)

```json
"{element}": {
  "decoration": {
    "icon": {"desktop": {"value": {"unicode": "&#xf04b;", "type": "fa", "weight": "900", "color": "#712727", "useSize": "on", "size": "90px"}}},
    "background": {"desktop": {"value": {"color": "#fefef5"}}}
  }
}
```
- Icon types: `"divi"` (Divi icons) or `"fa"` (Font Awesome)
- FA weights: `"400"` (regular/outline), `"900"` (solid)
- `useSize: "on"` + `size` enables custom sizing
- Background is separate from icon — wraps the icon element

Note: The standalone **Icon module** (`divi/icon`) uses a different pattern — see Tier 3.

### Container Cascade: children.module.decoration
Used by: **Slider** (children = all slides), **Accordion** (container styles cascade to items)

```json
"children": {"module": {"decoration": {"background": {"desktop": {"value": {"color": "#0f172a"}}}}}}
```
- Container sets shared styles via `children.module.decoration`
- Child items can override with their own `module.decoration`
- Accordion: container `module.decoration` cascades; items override per-item

### Module Link: module.advanced.link
Used by: **Heading**, **Blurb**, **Testimonial**

```json
"module": {"advanced": {"link": {"desktop": {"value": {"url": "#link", "target": "on"}}}}}
```
- Wraps the entire module in a clickable link
- `target: "on"` = opens in new tab
- Different from element-specific links (e.g. Icon's `icon.innerContent.desktop.value.url`)

### innerContent Variants

| Type | Example modules | Format |
|------|-----------------|--------|
| HTML string | Text, Accordion content, Slide content | `"\u003cp\u003eHTML\u003c/p\u003e"` |
| Plain string | Heading title, Author name, Job title | `"Plain text"` |
| Object `{text, linkUrl, linkTarget}` | Testimonial company | `{"text": "Corp", "linkUrl": "#", "linkTarget": "on"}` |
| Object `{url}` | Testimonial portrait | `{"url": "https://example.com/photo.jpg"}` |
| Object `{src, id, alt, ...}` | Image, Slide image | `{"src": "https://...", "id": "49", "alt": "Desc"}` |
| Object `{text, linkUrl}` | Button | `{"text": "Click", "linkUrl": "#"}` |
| Object `{unicode, type, weight, url}` | Icon | `{"unicode": "&#xf0eb;", "type": "fa", "weight": "900"}` |

## Attribute Tree Layout: Top-Level vs `module.*`

Divi's block `attrs` object splits across two tree levels, and **which level is authoritative is per-group** — there is no uniform rule. Writing to the wrong level causes `diviskit_module_update` to return `success`, but Divi reads from the other path, so the write is silently ignored on render. The VB re-render shows the old value; the tool reports no error. If you don't read-back verify, this burns debug time before you notice.

**Known top-level keys** (siblings of `module` — write here, NOT under `module.*`):

| Top-level key | What it configures | Wrong path (silent fail) |
|---|---|---|
| `css.{breakpoint}.value.{mainElement,before,after}` | Custom CSS override (per-module selectors) | `module.css.*` |
| `css.{breakpoint}.value.freeForm` | Module-scoped free-form CSS with `selector` token replacement | `module.css.*` |
| `content` (or `innerContent` per module) | Module content payload (text/button/icon/etc. — shape varies per module) | `module.content`, `module.innerContent` |
| `modulePreset` | Preset ID array (stacked presets — array, not single string) | `module.modulePreset` |
| `groupPreset.{slot}.presetId` | Group-level preset refs (array of preset IDs per slot — stackable, not single string; slot keys are camelCase like `designTitleText`, `designText`, `button`, etc. — each slot also carries a sibling `groupName` value like `"divi/font"` or `"divi/button"` identifying the group type) | `module.groupPreset` |
| `dynamicOptionGroups` | Composable Settings sub-group tracking (5.1.1+) | `module.dynamicOptionGroups` |
| `builderVersion` | Auto-migration trigger version | `module.builderVersion` |

**Known nested keys** (write under `module.*` — NOT at top-level):

| Nested key | What it configures | Wrong path (silent fail) |
|---|---|---|
| `module.meta.adminLabel.{breakpoint}.value` | VB admin label (layer list) | `meta.adminLabel`, `module.adminLabel` |
| `module.decoration.*` | All visual styling (border, background, spacing, sizing, layout, overflow, animation, scroll, transform, filters, boxShadow, ...) | `decoration.*` (top-level) |
| `module.advanced.*` | HTML output + behavior (elementType, htmlBefore/After, link, position, sticky, visibility, transition, order, ...) | `advanced.*` (top-level) |

**Non-module elements** follow the same pattern under their own element name — e.g. `button.decoration.*`, `imageIcon.decoration.sizing`, `fieldItem.advanced.type`. The split is `{element}.*` (nested) vs the small fixed set of top-level siblings listed above.

**Verification pattern** — when in doubt, read back after write:

1. `diviskit_module_update` → returns `success`
2. `diviskit_page_get_layout` → fetch the same block
3. Confirm the value landed at your target path. If it landed at a different path, or isn't present at all, you picked the wrong level.

The module renderer reads from the authoritative location per the tables above. Mismatches fall through to the pre-existing value (or the group default) — which is why the VB still shows the old state even though the tool claimed success.

## Design Token References in Attrs: Canonical `$variable()$` Only

Module attrs hold literal CSS values or canonical `$variable({...})$` tokens — nothing else. A hand-authored `var(--arbitrary-alias)` inside an attr value is a cross-system reference: it depends on a CSS variable some external stylesheet must declare. If that declaration is missing, the CSS spec says the property falls through to its initial value (0 for padding, browser default for color). The write succeeds, the renderer emits the ref as-is, and the page silently breaks.

**Isolation rule**: Divi owns the `gcid-*` / `gvid-*` namespace. Variable Manager tokens auto-emit into `:root` on every page. Modules reference them via canonical `$variable({...})$`; the renderer rewrites to `var(--gvid-*)` / `var(--gcid-*)` at emission time with the matching `:root` declaration always present. Child-theme CSS lives on its own track for non-Divi surfaces — neither side `var()`s across the boundary.

| Attr value | Result |
|---|---|
| `"80px"`, `"#ff0000"`, `"clamp(2rem, 5vw, 4rem)"` | Literal — emitted as-is. |
| `$variable({"type":"content","value":{"name":"gvid-oa-space-4","settings":{}}})$` | Canonical — resolves to `var(--gvid-oa-space-4)`; `:root { --gvid-oa-space-4: <value> }` auto-emitted. |
| `"var(--gcid-oa-primary-500)"` / `"var(--gvid-oa-space-4)"` | Tolerated (Divi-owned prefix, resolves via `:root`) but non-canonical — prefer `$variable({...})$`. |
| `"var(--space-3)"` or any `var(--<non-gvid-non-gcid>)` | **Banned.** Silent-failure class — falls through to the property's initial value. |
| `$variable(gvid-xxx)$` (shorthand, bare ID) | **Does not resolve.** The canonical token must wrap a JSON payload; the shorthand emits literally into CSS and the browser drops the declaration. Full payload format: [presets.md → Variable Tokens](presets.md#variable-tokens). |

Need a semantic name? Register it inside Divi as a `gvid-*` / `gcid-*` in the Variable Manager (e.g. `gvid-oa-space-hero-xl`) and reference via `$variable({...})$`. Don't layer a child-theme alias on top.

## Exceptions Quick Reference

**These modules break the standard `module.decoration.*` pattern. Getting these wrong causes silent failures.**

| Module | What's different | Correct path | Wrong pattern (silent fail) |
|--------|-----------------|--------------|--------------------------|
| **Button** | Border/bg/font on button root | `button.decoration.{border,background,font}` | `module.decoration.border` |
| **Button** | Sizing on button element (5.1.1+) | `button.decoration.sizing` | `module.decoration.sizing` |
| **Button** | Alignment inside sizing (5.1.1+) | `button.decoration.sizing.desktop.value.alignment` | `module.advanced.alignment` (schema only, not saved) |
| **Button** | Icon enable required | `button.decoration.button.desktop.value.icon.enable: "off"` | omitting `icon.enable` |
| **Image** | Spacing/sizing on advanced | `module.advanced.{spacing,sizing}` | `module.decoration.{spacing,sizing}` |
| **Image** | Border on image element | `image.decoration.border` | `module.decoration.border` |
| **Icon** | Border/bg on module only | `module.decoration.{border,background}` | `icon.decoration.{border,background}` |
| **Video** | No module background | `overlay.decoration.background` | `module.decoration.background` |
| **Company** (Testimonial) | innerContent is object | `{text, linkUrl, linkTarget}` | plain string |
| **Contact Form** | Title/field/captcha/button fonts use double `font.font` | `title.decoration.font.font`, `field.decoration.font.font`, etc. | `title.decoration.font` (single) |
| **Contact Field** | Label on `fieldItem`, not `title` | `fieldItem.innerContent`, `fieldItem.advanced.type` | `title.innerContent`, `content.advanced.type` |
| **Social Media Follow** | Custom icon size lives under `icon.advanced`, gated by `useSize` toggle | `icon.advanced.useSize: "on"` + `icon.advanced.size: "<value>"` (`"96px"`, `"$variable({...})$"`, `"calc(2rem + 1vw)"`, `"clamp(48px, 5vw, 96px)"`, `"var(--gvid-...)"`, or length keywords — all accepted at parity with other length fields per 5.3.3 fix `SocialMediaFollowModule.php:306-316, 390-415`) | omitting `useSize` (size is ignored) or assuming a numeric-only field (pre-5.3.3 dropped math/var/keyword silently) |
