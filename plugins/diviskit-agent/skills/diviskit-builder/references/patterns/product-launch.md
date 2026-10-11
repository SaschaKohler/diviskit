# Pattern: Product Launch / Waitlist

A launch page for a not-yet-available product. One action—join the waitlist or register interest—repeats at the top and bottom. Be explicit about what exists today, what is planned, and what sign-up means; do not imply a live product or promise a release date without confirmation.

## Section Structure

```
1. Hero             — H1 + product promise + real preview + sign-up CTA
2. Problem          — concrete current workflow and its friction
3. Product preview  — available screens, prototypes, or product details
4. How it works     — sequence from first use to expected outcome
5. Status & FAQ     — availability, eligibility, data usage, next updates
6. Waitlist CTA     — action repeated with consent/expectation microcopy
```

## Module counts

- 6 sections, about 6 rows and 7 columns depending on preview layout.
- Typical: 7–9 headings, 9–14 text modules, 1–3 images, 2 CTA buttons, optional FAQ modules.
- Counts vary with the amount of material available; remove sections that cannot be supported honestly.

## Animation pattern

- **Hero**: keep headline and action visible immediately; optional `fade` on the preview only.
- **Preview**: if showing a sequence, reveal one step at a time with modest delays; avoid fake app interactions.
- **Status / FAQ**: static; expanded content should remain readable with keyboard navigation.
- **Reduced motion**: verify the preview and CTA work without any entrance effects.

## Section blueprints

### 1. Hero
```
Section (bg: brand-derived surface; semantic header)
└── Row (two columns; phone: copy then preview)
    ├── Column
    │   ├── Text [status] — e.g. in development, only if true
    │   ├── Heading [H1] — specific benefit without unproven claims
    │   ├── Text [who it is for / what it does]
    │   ├── Button [waitlist] — real form or registration destination
    │   └── Text [microcopy] — when and why subscribers hear back
    └── Column
        └── Image [real preview] — screenshot/prototype with truthful caption
```

### 2. Problem
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — name the current friction
        └── Group [workflow] — flex column, numbered steps with short Text items
            ├── Text [current step 01]
            ├── Text [current step 02]
            └── Text [point of friction]
```

### 3. Product preview
```
Section (bg: contrasting surface)
└── Row
    └── Column
        ├── Heading [H2] — see what is actually available to show
        ├── Image [interface or prototype] — descriptive alt text
        └── Text [caption] — clearly distinguish concept from shipped behavior
```

### 4. How it works
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — intended user journey
        └── Group [steps] — flex row, phone: column in reading order
            ├── Group [01] — Heading [H3] + Text [action]
            ├── Group [02] — Heading [H3] + Text [action]
            └── Group [03] — Heading [H3] + Text [outcome]
```

### 5. Status & FAQ
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — what to expect
        ├── Text [availability] — confirmed status, no invented deadline
        ├── Text [eligibility / pricing] — only known details
        ├── Text [privacy] — link to actual privacy policy if collecting emails
        └── Group [FAQ, optional] — verified question and answer pairs
```

### 6. Waitlist CTA
```
Section (bg: brand accent with sufficient contrast)
└── Row (narrow width)
    └── Column
        ├── Heading [H2] — recap the product's value
        ├── Button [waitlist] — same form destination as hero
        └── Text [consent / updates] — accurate frequency and purpose
```

## Using with presets

Use site-specific heading, body, and button roles via `.devin/skills/diviskit-builder/design-system.json` when available; otherwise style inline. Resolve block syntax through [minimal-snippets.md](../minimal-snippets.md), not by copying a site's preset UUIDs.

## Customization points

- **State of product**: distinguish prototype, beta, and general availability in the copy.
- **Preview assets**: use real or clearly labeled conceptual imagery; no fake dashboard metrics.
- **Signup**: wire both buttons to a working form; confirm consent, privacy link, and confirmation flow externally.
- **Journey**: change the steps and ordering to fit the real experience.
- **Art direction**: build a visual device from the product's domain; consult [design-guide.md](../design-guide.md), not the SaaS card arrangement.
- **Divi mechanics**: unique `module.meta.adminLabel.desktop.value` on every block, section → row → column nesting, explicit tablet/phone overrides.
