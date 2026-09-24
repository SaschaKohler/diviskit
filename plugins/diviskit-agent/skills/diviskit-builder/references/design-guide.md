# Design Guide — Creative Direction and Technical Patterns

A creativity framework plus implementation recipes for high-quality Divi 5 pages.
Use recipes for mechanics, never as a substitute for original art direction. Use
alongside [module-formats.md](module-formats.md) for exact attr paths and
[presets.md](presets.md) for design tokens.

## DiviSkit Creativity Levels

Creativity is a required design parameter, not optional decoration. Select the
level before choosing a layout. A user instruction overrides the defaults.

| Level | Name | Use | Required divergence |
|---|---|---|---|
| **C0** | Preserve | Repairs, migrations, pixel-accurate reproduction | No unsolicited visual invention |
| **C1** | Distinct | New sections inside an established site | One fresh compositional move while preserving the site's grammar |
| **C2** | Signature | Default for net-new pages and layouts | A content-derived thesis, one signature device, and divergence on at least three design axes |
| **C3** | Art Direction | `besonders kreativ`, experimental, unusual, surprising, AI-art, or explicit level 3 requests | A strong concept, one productive rule-break, divergence on at least five axes, and no stock genre skeleton |

Interpret `creative level 3`, `Kreativitätslevel 3`, `C3`, and natural-language
equivalents identically. If no level is given, use C2 for net-new design and C1
when extending an existing visual system. Never reduce C3 to random distortion,
more gradients, or more animation. Creative risk must have a readable concept.

### Creative preflight — mandatory before implementation

Create a short private design brief from the actual project. Do not ask the user
to approve it unless their request is genuinely ambiguous.

1. **Extract content signals** — subject, audience, emotional tone, strongest nouns and verbs, real assets, and conversion goal.
2. **Name the obvious genre answer** — for example, “centered SaaS hero followed by logo strip and three-card features.”
3. **Reject one default explicitly** — state internally what this design will not do.
4. **Form a one-sentence thesis** — `This experience should feel like [specific world/material/process], expressed through [composition rule], because [content reason].`
5. **Choose a signature device** — a recurring visual or structural behavior that belongs to this content, not a detachable gimmick.
6. **Set the novelty budget** — keep essential navigation and primary actions familiar; spend novelty on composition, hierarchy, imagery, rhythm, or interaction.
7. **Plan mobile as a re-composition** — preserve the thesis on small screens rather than merely stacking every desktop block.

Good thesis: “A restoration studio should feel like an annotated conservation
table: cropped material studies, marginal notes, and measured asymmetry reveal
the craft.” Bad thesis: “A modern page with gradients, cards, and animations.”

### Design axes

Use these axes to create structural difference. C2 must diverge from genre defaults
on at least three; C3 on at least five.

1. **Composition** — asymmetry, overlap, split horizon, diagonal progression, dense-to-empty field, editorial margins, spatial chapters.
2. **Hierarchy** — scale contrast, interrupted reading order, side annotations, repeated micro-headlines, image-led rather than headline-led opening.
3. **Rhythm** — alternating compression and release, irregular section depth, pauses, recurring markers, controlled density.
4. **Typography** — type roles, width, weight contrast, vertical or marginal type, sentence-like display text; not merely a different font.
5. **Shape language** — sharp, cut, circular, archival, hand-drawn, technical, soft, monolithic; use one coherent family instead of universal rounded cards.
6. **Color/material** — palette proportions, paper/ink, luminous screen, oxidized metal, botanical, monochrome with one signal color, image-derived color.
7. **Imagery/data** — unusual crops, sequencing, masks, diagrams, captions, evidence, texture, or data as composition rather than generic decoration.
8. **Motion/interaction** — one coherent behavior such as reveal, orbit, scan, assemble, drift, or snap. Motion may be absent when stillness strengthens the idea.

A color swap does not count as meaningful divergence. Decorative CSS without a
changed composition or hierarchy does not count either.

### Signature-device test

A signature device must satisfy all three:

- **Specific** — traceable to the brand, story, product, or subject.
- **Systemic** — appears with variation in multiple places, not as one isolated trick.
- **Functional** — helps hierarchy, storytelling, navigation, comparison, or emotional tone.

Examples include an exhibition index that becomes navigation, crop marks that
organize a photographer's case studies, a waveform controlling section rhythm
for an audio product, or a trail map becoming the information architecture for
an outdoor guide. Generic glowing orbs, floating glass panels, and arbitrary
marquees fail this test.

## Anti-Slop Gate Before Implementation

Do not implement until the concept passes these checks:

1. **Noun-swap test** — If replacing the nouns would make the same layout work unchanged for an unrelated SaaS, portfolio, game, and blog, it is too generic.
2. **Silhouette test** — Viewed as gray rectangles, the page must not be indistinguishable from the standard hero → logo strip → three cards → stats → testimonials → CTA stack.
3. **Reference test** — A reference pattern may solve block syntax or responsive mechanics, but must not dictate the section sequence, palette, or visual grammar.
4. **Repetition test** — When an existing site/page is available, compare hero alignment, section order, column ratios, card shape, palette, and motion. Repeating more than two dominant choices requires a content-based reason.
5. **Coherence test** — Every unusual choice must support the thesis. Remove effects that exist only to signal “creative.”
6. **Usability test** — Primary navigation, core message, primary action, focus states, contrast, and mobile reading order remain clear.

If a concept fails, change its composition or thesis rather than adding surface
effects. At C3, prefer one bold and coherent risk over five unrelated tricks.

### Do

- Start from content metaphors, real artifacts, domain processes, and brand contradictions.
- Use asymmetry, negative space, scale, cropping, and sequence intentionally.
- Let each page type express the same identity differently instead of cloning sections.
- Vary repeated components through hierarchy and content while preserving recognition.
- Make typography, imagery, layout, and motion tell the same story.
- Allow tension, awkward beauty, visual surprise, or controlled imperfection at C3.
- Keep reusable tokens for consistency while composing them in a non-template way.
- Record the chosen level and thesis in the work summary when reporting the finished design.

### Don't

- Do not default to a centered hero, tiny pill eyebrow, gradient headline, two CTA buttons, floating dashboard mockup, logo strip, bento grid, three equal icon cards, metric counters, testimonials, and final gradient CTA.
- Do not default to dark navy with violet/cyan glow, glassmorphism, oversized rounded rectangles, generic mesh gradients, blobs, or stars.
- Do not force every idea into cards. Use lists, fields, timelines, annotations, canvases, bands, diagrams, image sequences, or open composition when appropriate.
- Do not add fake metrics, fake testimonials, fake logos, or invented social proof merely to fill a familiar layout slot.
- Do not use equal spacing everywhere; deliberate rhythm needs compression and release.
- Do not animate every visible module or apply a fixed animation quota. Repeated fade-up staggering is not an art direction.
- Do not confuse novelty with poor contrast, hidden controls, unreadable type, disorienting motion, or broken responsive behavior.
- Do not recreate a previous DiviSkit result unless the user explicitly asks for consistency or reuse.

## Design Thinking — Before You Code

After selecting the creativity level and passing the anti-slop gate, verify:

- Typography hierarchy is clear (H1 > H2 > H3 visually distinct, not just smaller).
- Color has intentional proportion and sufficient contrast, not equal distribution.
- Spacing expresses the chosen rhythm rather than applying one universal gap.
- Motion follows one concept and respects reduced-motion needs; purposeful stillness is valid.
- Responsive behavior preserves reading order and re-composes unusual desktop layouts.
- The signature device survives without obscuring content or controls.

Use `oa` design tokens ([presets.md](presets.md)) for consistency, but do not let
the token library dictate the visual concept. Override per-instance only for
deliberate, thesis-driven variation.

### Contrast and Readability

Every text element must be readable against its background. Getting this wrong is the most common design failure.

**Rules:**
1. **Dark background** → white or light text (`#ffffff`, `neutral-100`–`neutral-300`). Never use mid-grays above `neutral-400` (too dark to read).
2. **Light background** → dark text (`neutral-600`–`neutral-900`). Never use light grays below `neutral-400` (too light to read).
3. **Gradient backgrounds** — evaluate contrast against the *lightest* stop color (worst case). If the gradient goes `#0f172a` → `#334155`, check text readability against `#334155`.
4. **Semi-transparent text** — never go below `rgba(x,x,x,0.5)` for body text. Kickers and secondary labels can go to `0.4` minimum.
5. **Button text vs button background** — always verify. A `#6366f1` button needs white text, not dark text.
6. **Hover states** — check contrast on hover too. A white button that hovers to light yellow with white text becomes unreadable.
7. **Image/video backgrounds** — add an overlay (`rgba(0,0,0,0.4)`+) or use text shadow before placing text on images.

**Quick reference:**

| Background | Heading color | Body color | Accent/kicker |
|------------|--------------|------------|---------------|
| Dark (`neutral-800`+) | `#ffffff` | `neutral-200`–`neutral-300` | `primary-400` or lighter |
| Light (`neutral-50`–`neutral-200`) | `neutral-900` | `neutral-600`–`neutral-700` | `primary-600` or darker |
| Gradient | Check against lightest stop | Same rule | Same rule |
| Image | White + overlay | Short labels only; avoid body text even with overlay | White or accent with overlay |

### Style-to-Token Mapping

This table is a token fallback, not a menu of complete art directions. Form the
content-derived thesis first; then borrow or adapt a column if it supports that
thesis. Never let these five examples limit C2/C3 exploration, and **do NOT
default to Dark Minimal every time.**

| Token | Dark Minimal | Light Airy | Bold Vibrant | Editorial | Glassmorphism |
|-------|-------------|------------|-------------|-----------|---------------|
| **Section bg** | `neutral-900` | `neutral-50` | `primary-800` | `white` | `neutral-950` |
| **Alt section bg** | `neutral-800` | `white` | `primary-900` | `neutral-50` | `neutral-900` |
| **Heading color** | `white` | `neutral-900` | `white` | `neutral-900` | `white` |
| **Body color** | `neutral-200` | `neutral-700` | `neutral-100` | `neutral-600` | `neutral-300` |
| **Accent** | `primary-400` | `primary-500` | `secondary-500` | `primary-600` | `primary-300` |
| **Accent hover** | `primary-300` | `primary-600` | `secondary-400` | `primary-500` | `primary-200` |
| **Heading preset** | oa Heading H1 + oa Heading Light | oa Heading H1 | oa Heading H1 + oa Heading Light | oa Heading H1 Small | oa Heading H1 + oa Heading Light |
| **Body preset** | oa Text Standard + oa Text Light | oa Text Standard | oa Text Standard + oa Text Light | oa Text Big | oa Text Standard + oa Text Light |
| **Button** | oa Button Primary | oa Button Primary | oa Button Secondary | oa Button Primary Outline | oa Button White |
| **Alt button** | oa Button White | oa Button Primary Outline | oa Button White | oa Button Primary | oa Button Primary Outline |
| **Radius** | `rounded-xl` | `rounded-2xl` | `rounded-lg` | `rounded` | `rounded-3xl` |
| **Card border** | 1px `neutral-700` | 1px `neutral-200` | none | 1px `neutral-300` | 1px `neutral-700` |
| **Card bg** | `white` @ 5% opacity | `white` | `primary-700` | `neutral-50` | `white` @ 5% opacity |
| **Section padding** | `space-16` | `space-16` | `space-12` | `space-16` | `space-12` |
| **Stack light preset** | Yes | No | Yes | No | Yes |
| **Module preset** | oa Dark Section | — | — | — | — |

The table uses shorthand token names (e.g., `primary-400`, `rounded-xl`). To get full token IDs, add the `oa` prefix: colors → `gcid-oa-{name}`, numbers → `gvid-oa-{name}`. See [presets.md](presets.md) for the complete ID list and preset UUIDs.

**How to use**: If one column supports the thesis, use it as a token starting point
and deliberately compose beyond it. Do not reproduce the example as a complete
look or mix named styles without a content-based reason.

## Multi-Column Layout (Group-Based)

**Use Groups for multi-column layouts, not Row with multiple columns.** Divi's column CSS conflicts with `display: flex` on rows, causing columns to stack.

### 3-Column Card Grid

Parent Group — flex row container:
```jsonc
// Outer Group: flex row with percentage gap
"module": {
  "decoration": {
    "layout": {
      "desktop": {"value": {"display": "flex", "flexDirection": "row", "alignItems": "stretch", "columnGap": "3.5%", "rowGap": "24px", "flexWrap": "wrap"}},
      "phone": {"value": {"display": "flex", "flexDirection": "column", "alignItems": "stretch", "rowGap": "20px"}}
    }
  }
}
```

Each child Group — sized via `flexType`:
```jsonc
// Each card: flexType controls width (8/24 = 33%)
"module": {
  "decoration": {
    "layout": {"desktop": {"value": {"display": "flex", "flexDirection": "column", "rowGap": "16px"}}},
    "sizing": {"desktop": {"value": {"flexType": "8_24"}}, "phone": {"value": {"flexType": "24_24", "width": "100%", "maxWidth": "100%"}}},
    "background": {"desktop": {"value": {"color": "rgba(255,255,255,0.05)"}, "hover": {"color": "rgba(255,255,255,0.08)"}}},
    "border": {"desktop": {"value": {"radius": {"topLeft": "16px", "topRight": "16px", "bottomLeft": "16px", "bottomRight": "16px", "sync": "on"}, "styles": {"all": {"width": "1px", "color": "rgba(255,255,255,0.1)"}}}}},
    "spacing": {"desktop": {"value": {"padding": {"top": "32px", "bottom": "32px", "left": "32px", "right": "32px", "syncVertical": "on", "syncHorizontal": "on"}}}},
    "animation": {"desktop": {"value": {"style": "fade", "delay": "0ms"}}}
  }
}
```

### Responsive card-grid rule *(verified 2026-05-28)*

Desktop multi-column Groups must include explicit phone stacking. Block validation catches malformed markup and known path traps, but it cannot prove that cards are visually full-width on a phone viewport.

- Parent Group phone layout: `display: "flex"`, `flexDirection: "column"`, `alignItems: "stretch"`, and a sensible `rowGap`.
- Child card Groups phone sizing: `module.decoration.sizing.phone.value.flexType = "24_24"`; add `width: "100%"` and `maxWidth: "100%"` when the card also carries width or max-width constraints.
- Verify the saved page in a mobile viewport after `diviskit_validate_blocks` passes. Do not treat validator success as responsive acceptance.

### Column sizing reference <!-- VB-verified: 2026-03-21 -->

Divi uses a **24-unit grid** for flex child sizing. Path: `module.decoration.sizing.desktop.value.flexType`

| flexType | Fraction | Width | VB label |
|----------|----------|-------|----------|
| `"4_24"` | 4/24 | ~17% | 1/6 |
| `"6_24"` | 6/24 | 25% | 1/4 |
| `"8_24"` | 8/24 | ~33% | 1/3 |
| `"12_24"` | 12/24 | 50% | 1/2 |
| `"16_24"` | 16/24 | ~67% | 2/3 |
| `"18_24"` | 18/24 | 75% | 3/4 |
| `"24_24"` | 24/24 | 100% | Full |

Common layouts:

| Layout | Child flexTypes |
|--------|----------------|
| 3 equal columns | `"8_24"` + `"8_24"` + `"8_24"` |
| 2 equal columns | `"12_24"` + `"12_24"` |
| 4 equal columns | `"6_24"` × 4 |
| Sidebar + content | `"8_24"` + `"16_24"` |
| Content + sidebar | `"16_24"` + `"8_24"` |

> **Note**: `flexType` handles gap-aware sizing internally on desktop grids — do NOT also set desktop `width` or `flexBasis`. Use `flexType` alone for column sizing; add phone `width` / `maxWidth` only when clearing prior width constraints for mobile stacking.

### Section/Row/Column as simple containers

Always keep these minimal when using Group layouts:
```jsonc
// Section, Row, Column — just display block, no flex
"module": {"decoration": {"layout": {"desktop": {"value": {"display": "block"}}}}}
```

### Centering elements with maxWidth

Any module with `maxWidth` in a block parent aligns left by default. Add auto margins:
```jsonc
"spacing": {"desktop": {"value": {"margin": {"left": "auto", "right": "auto", "syncHorizontal": "off"}}}}
```

## Native-First Layout Fixes (advisory)

For Divi-owned layout issues, map the behavior to native Divi settings before adding CSS. CSS is the last resort when the native setting cannot express the behavior; broad selectors and `!important` require an explicit rationale in the work notes.

### Theme Builder footer bottom crop/tightness *(verified 2026-05-28)*

If a Global Footer looks cropped or too tight at the bottom, first change the root footer Section's native bottom padding:

- VB path (operator mapping; not stamped VB-verified here): `Theme Builder > Global Footer > Section: Global Footer > Design > Spacing > Padding > Bottom`
- Attrs: `module.decoration.spacing.desktop.value.padding.bottom`, `module.decoration.spacing.tablet.value.padding.bottom`, `module.decoration.spacing.phone.value.padding.bottom`
- Avoid broad `.et-l--footer` CSS for native spacing problems; it hides the real editable setting from future VB users.

### Theme Builder mobile header nav hiding *(VB-verified 2026-05-28)*

Hide a mobile nav/link Group with Divi's native visibility control:

- VB path: `Theme Builder > Global Header + Footer > Global Header > Nav Links group > Advanced > Visibility > Disable On > Phone`
- Attr: `module.decoration.disabledOn.phone.value = "on"`
- Do not rely on `module.decoration.layout.phone.value.display = "none"` for this case. That value can exist in block attrs without hiding the Group on the frontend.

## Animation Staggering

Use this recipe only when staggered entrance supports the chosen motion language.
It is not a page-wide default and there is no minimum animation count:

```jsonc
// Card 1: immediate
"animation": {"desktop": {"value": {"style": "fade", "duration": "800ms", "delay": "0ms", "startingOpacity": "0%", "speedCurve": "ease-out"}}}

// Card 2: 150ms delay
"animation": {"desktop": {"value": {"style": "fade", "duration": "800ms", "delay": "150ms", "startingOpacity": "0%", "speedCurve": "ease-out"}}}

// Card 3: 300ms delay
"animation": {"desktop": {"value": {"style": "fade", "duration": "800ms", "delay": "300ms", "startingOpacity": "0%", "speedCurve": "ease-out"}}}

// Card 4: 450ms delay
"animation": {"desktop": {"value": {"style": "fade", "duration": "800ms", "delay": "450ms", "startingOpacity": "0%", "speedCurve": "ease-out"}}}
```

### Slide with direction

```jsonc
"animation": {"desktop": {"value": {"style": "slide", "direction": "bottom", "duration": "800ms", "delay": "200ms", "intensity": {"slide": "10%"}, "startingOpacity": "0%", "speedCurve": "ease-out"}}}
```

### Where to apply animations

| Element | Animation | Delay pattern |
|---------|-----------|---------------|
| Hero heading | `fade`, 0ms | First visible |
| Hero subtitle | `fade`, 200ms | After heading |
| Hero CTA buttons | `fade`, 400ms | After subtitle |
| Section headings | `fade`, 0ms | On scroll into view |
| Feature cards | `fade`, 0ms/150ms/300ms | Stagger left to right |
| Stats counters | `fade`, 0ms/150ms/300ms | Stagger left to right |
| Review cards | `fade`, 0ms/150ms/300ms | Stagger left to right |
| Split section image | `slide` from left, 0ms | On scroll |
| Split section content | `fade`, 200ms | After image |

## Hover States

### Card hover (Group)

```jsonc
"background": {"desktop": {"value": {"color": "rgba(255,255,255,0.05)"}, "hover": {"color": "rgba(255,255,255,0.08)"}}},
"border": {"desktop": {"value": {"styles": {"all": {"color": "rgba(255,255,255,0.1)"}}}, "hover": {"styles": {"all": {"color": "rgba(124,58,237,0.4)"}}}}}
```

### Button hover

```jsonc
// Primary button
"background": {"desktop": {"value": {"color": "#7c3aed"}, "hover": {"color": "#6d28d9"}}}

// Ghost/outline button
"background": {"desktop": {"value": {"color": "rgba(255,255,255,0.08)"}, "hover": {"color": "rgba(255,255,255,0.12)"}}},
"font": {"font": {"desktop": {"value": {"color": "rgba(226,232,240,0.9)"}, "hover": {"color": "#ffffff"}}}}
```

### Icon hover

```jsonc
"icon": {"advanced": {"color": {"desktop": {"value": "#6366f1", "hover": "#ffffff"}}}}
```

## Stats Section (Number Counter)

Use `divi/number-counter` — it animates counting on scroll. Do NOT use `divi/text` with static numbers.

```jsonc
// Parent Group: flex row, centered
{"module": {"decoration": {"layout": {"desktop": {"value": {"display": "flex", "flexDirection": "row", "alignItems": "center", "justifyContent": "center", "columnGap": "64px", "rowGap": "32px", "flexWrap": "wrap"}}}}}}

// Each counter
{"module": {"decoration": {"animation": {"desktop": {"value": {"style": "fade", "delay": "0ms"}}}}},
 "title": {"innerContent": {"desktop": {"value": "Verified Modules"}}, "decoration": {"font": {"font": {"desktop": {"value": {"color": "rgba(148,163,184,0.6)", "size": "13px", "weight": "600", "letterSpacing": "2px", "style": ["uppercase"]}}}}}},
 "number": {"innerContent": {"desktop": {"value": "16"}}, "advanced": {"enablePercentSign": {"desktop": {"value": "off"}}}, "decoration": {"font": {"font": {"desktop": {"value": {"color": "#a78bfa", "size": "48px", "weight": "800"}}}}}}}
```

## Review Card Pattern

Stars + quote (italic) + author name inline. Use Group with flex column:

```jsonc
// Review card Group
{"module": {"decoration": {
  "layout": {"desktop": {"value": {"display": "flex", "flexDirection": "column", "rowGap": "12px"}}},
  "background": {"desktop": {"value": {"color": "rgba(255,255,255,0.05)"}}},
  "border": {"desktop": {"value": {"radius": {"topLeft": "16px", "topRight": "16px", "bottomLeft": "16px", "bottomRight": "16px", "sync": "on"}, "styles": {"all": {"width": "1px", "color": "rgba(255,255,255,0.08)"}}}}},
  "spacing": {"desktop": {"value": {"padding": {"top": "24px", "bottom": "24px", "left": "24px", "right": "24px", "syncVertical": "on", "syncHorizontal": "on"}}}},
  "sizing": {"desktop": {"value": {"flexType": "8_24"}}},
  "animation": {"desktop": {"value": {"style": "fade", "delay": "0ms"}}}
}}}

```

Inside the review card Group, add 3 Text modules:

Stars:
```jsonc
{"content": {"innerContent": {"desktop": {"value": "\u003cp\u003e⭐⭐⭐⭐⭐\u003c/p\u003e"}}, "decoration": {"bodyFont": {"body": {"font": {"desktop": {"value": {"size": "16px"}}}}}}}}
```

Quote (italic):
```jsonc
{"content": {"innerContent": {"desktop": {"value": "\u003cp\u003e\u201cYour testimonial quote here.\u201d\u003c/p\u003e"}}, "decoration": {"bodyFont": {"body": {"font": {"desktop": {"value": {"color": "rgba(226,232,240,0.85)", "size": "15px", "lineHeight": "1.7em", "style": ["italic"]}}}}}}}}
```

Author + role (single text module):
```jsonc
{"content": {"innerContent": {"desktop": {"value": "\u003cp\u003e\u003cstrong style=\"color:#fff\"\u003eJane Smith\u003c/strong\u003e \u00b7 Frontend Developer\u003c/p\u003e"}}, "decoration": {"bodyFont": {"body": {"font": {"desktop": {"value": {"color": "rgba(148,163,184,0.6)", "size": "13px"}}}}}}}}
```

## Gradient Hero

CSS animation on the section — add via `css.desktop.value.freeForm` and a custom class:

```css
@keyframes gradient-shift {
  0%   { background-position: 0% 50%; }
  50%  { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.hero-grad.et_pb_section {
  background-image: linear-gradient(-45deg, #0f172a, #312e81, #7c3aed, #4f46e5, #0f172a) !important;
  background-size: 400% 400% !important;
  animation: gradient-shift 12s ease infinite;
}
```

> **Note**: When using in `css.desktop.value.freeForm`, minify the CSS (remove line breaks) since the freeForm field is a single string.

Apply the class via custom attributes:
```jsonc
"attributes": {"desktop": {"value": {"attributes": [{"id": "hero-cls", "name": "class", "value": "hero-grad", "adminLabel": "class hero-grad", "targetElement": "main"}]}}}
```

## Responsive Overrides

Always include tablet/phone adjustments:

```jsonc
// Font size reduction
"font": {"desktop": {"value": {"size": "48px"}}, "tablet": {"value": {"size": "36px"}}, "phone": {"value": {"size": "28px"}}}

// Padding reduction
"spacing": {"desktop": {"value": {"padding": {"top": "100px", "bottom": "100px"}}}, "tablet": {"value": {"padding": {"top": "60px", "bottom": "60px"}}}}

// Stack columns on phone
"layout": {"desktop": {"value": {"flexDirection": "row"}}, "phone": {"value": {"flexDirection": "column"}}}
```

## Kicker / Eyebrow Labels

A kicker (also called eyebrow, overline, or pre-header) is a short label above a heading that categorizes the section. It is NOT a heading — getting this wrong breaks visual hierarchy.

### Identification checklist

Before styling text as a kicker, verify ALL four:
1. **Short** — typically 1-3 words (e.g. "Features", "How It Works", "Testimonials")
2. **Categorical** — labels what the section is about, not what it says
3. **Not the primary message** — the heading below carries the main content
4. **Appears above a heading** — never stands alone as the section's only text

If any rule fails, it's a heading, not a kicker. A common mistake: treating the main CTA heading as a kicker and shrinking it to tiny uppercase text.

### Module and tag

Use `divi/text` with `<p>` tag — never `<h1>`-`<h6>`. Kickers are decorative labels, not semantic headings. Using heading tags pollutes the page's SEO/accessibility hierarchy.

### Styling pattern
```jsonc
// Kicker above a section heading
"content": {"innerContent": {"desktop": {"value": "\u003cp\u003eFeatures\u003c/p\u003e"}},
  "decoration": {"bodyFont": {"body": {"font": {"desktop": {"value": {"color": "#7c3aed", "size": "13px", "weight": "700", "letterSpacing": "3px", "style": ["uppercase"]}}}}}}}
```

Common styling: small size (12-14px), bold weight (600-700), uppercase, wide letter-spacing (2-4px), accent color. Adjust to match the project's design system.

## Preset-Driven Generation

When the oa design system is set up (see [presets.md](presets.md)), use `groupPreset` references instead of inline font styling. This reduces token count and ensures design consistency.

### Before (inline — ~250 chars per heading)
```jsonc
{"title":{"innerContent":{"desktop":{"value":"Page Title"}},"decoration":{"font":{"font":{"desktop":{"value":{"weight":"800","size":"clamp(30px, 8vw, 100px)","lineHeight":"1.1em","color":"#ffffff"}}}}}}}
```

### After (preset — ~180 chars, no size/weight/lineHeight attrs)
```jsonc
{"title":{"innerContent":{"desktop":{"value":"Page Title"}},"decoration":{"font":{"font":{"desktop":{"value":{"color":"$variable({\"type\":\"color\",\"value\":{\"name\":\"gcid-oa-white\",\"settings\":{}}})$"}}}}}},"groupPreset":{"designTitleText":{"presetId":["<heading-h1>"],"groupName":"divi/font"}}}
```

Size, weight, and line height come from the preset. Color uses a `$variable()$` token. Per-instance overrides (like animation delay) can still be added inline.

### Available presets — Quick lookup

Resolve preset role keys to UUIDs via `.devin/skills/diviskit-builder/design-system.json`. Full catalog with weights, tokens, and markup examples: [presets.md](presets.md).

| Category | groupId | groupName | Role keys |
|----------|---------|-----------|-----------|
| Headings | `designTitleText` | `divi/font` | `heading-h1` through `heading-h6-small`, `heading-light` |
| Body text | `designText` | `divi/font-body` | `text-standard`, `text-small`, `text-big`, `text-light` |
| Buttons | `button` | `divi/button` | `button-primary`, `button-primary-outline`, `button-secondary`, `button-white` |
| Module-level | (via `modulePreset`) | — | `section-dark`, `card-glass`, `icon-badge` |

**Common needs**: Hero heading → `heading-h1`, section heading → `heading-h2`, card heading → `heading-h4`, body text → `text-standard`, primary CTA → `button-primary`, dark section → `section-dark`.
