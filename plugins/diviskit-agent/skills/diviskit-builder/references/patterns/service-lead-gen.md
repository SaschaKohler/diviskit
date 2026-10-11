# Pattern: Service Lead Generation

A consultation-focused page for a specific service and audience. Use a real service process as the organizing device; do not invent outcomes, client names, or credentials. The primary action is one concrete inquiry or booking destination.

## Section Structure

```
1. Hero           — H1 + audience/problem framing + primary booking CTA + service image
2. Situation      — recognizable client scenarios, presented as an editorial list
3. Approach       — three sequential steps with deliverables and handoffs
4. Evidence       — one verifiable case or credential (omit if unavailable)
5. Fit & scope    — who it is for, what's included, what happens next
6. Contact CTA    — recap + booking/form link + response expectations
```

## Module counts

- Base: 5 sections, about 5 rows and 6 columns; evidence adds one section, row, and column.
- Typical: 10–11 headings, 14–17 text modules, 2 buttons, 1 image, 3 step groups.
- Numbers are planning estimates, not a quota; omit modules when the story needs less.

## Animation pattern

- **Hero**: static image and H1; optional restrained `fade` for supporting copy. Keep the CTA visible immediately.
- **Situation**: no stagger if the scenarios need to be scanned together.
- **Approach**: optional `fade` on step groups in reading order, short delays only.
- **Evidence / Contact**: static; motion must not obscure proof or the form.
- **Reduced motion**: check the final page with the OS preference enabled.

## Section blueprints

### 1. Hero
```
Section (bg: neutral, generous top padding; semantic header)
└── Row (two columns, phone: text then image)
    ├── Column [offer]
    │   ├── Text [service category] — small, descriptive
    │   ├── Heading [H1] — specific service + audience/outcome
    │   ├── Text [scope] — plain-language problem and offer
    │   └── Button [book] — real booking or inquiry URL
    └── Column [evidence image]
        └── Image — real team, process, or work; meaningful alt text
```

### 2. Situation
```
Section (bg: contrasting neutral)
└── Row
    └── Column
        ├── Heading [H2] — recognize the visitor's situation
        └── Group [scenario list] — flex column, ruled entries rather than cards
            ├── Group [scenario] — Text [label] + Text [consequence]
            ├── Group [scenario] — same structure
            └── Group [scenario] — same structure
```

### 3. Approach
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — how the collaboration works
        └── Group [step sequence] — flex row; phone: column in numeric order
            ├── Group [01] — Heading [H3] + Text [deliverable]
            ├── Group [02] — Heading [H3] + Text [deliverable]
            └── Group [03] — Heading [H3] + Text [handoff]
```

### 4. Evidence (optional)
```
Section (bg: quiet accent)
└── Row
    └── Column
        ├── Heading [H2] — verified project or qualification
        ├── Text [context] — what was done, with attributable facts
        └── Text [source] — name/role or link, only with permission
```

### 5. Fit & scope
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — who should inquire
        └── Group [scope] — flex row, phone: column
            ├── Group [included] — Heading [H3] + Text [deliverables]
            └── Group [next step] — Heading [H3] + Text [timeline/process]
```

### 6. Contact CTA
```
Section (bg: dark or brand accent)
└── Row (narrow width)
    └── Column
        ├── Heading [H2] — clear next step
        ├── Text — truthful availability and response expectations
        └── Button [book] — same destination as hero
```

## Using with presets

Resolve heading, body, and button role keys from `.devin/skills/diviskit-builder/design-system.json` when present. Otherwise use inline values; never copy preset IDs from another site. See [presets.md](../presets.md) for the role mapping and [minimal-snippets.md](../minimal-snippets.md) for block shapes.

## Customization points

- **Audience & service**: reframe the H1 and situations with actual visitor language.
- **Process**: change the number of steps to match the real engagement, not a fixed three-step promise.
- **Evidence**: use approved case material or remove the section; no fabricated testimonials.
- **Contact**: link to an existing form or scheduler; don't display a non-functional form.
- **Design thesis**: derive composition, imagery, and type from the work itself. Follow [design-guide.md](../design-guide.md) rather than reusing the SaaS card stack.
- **Divi mechanics**: label every block under `module.meta.adminLabel.desktop.value`; use a section → row → column hierarchy and responsive layout overrides.
