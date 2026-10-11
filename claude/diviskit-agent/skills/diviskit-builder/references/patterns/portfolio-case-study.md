# Pattern: Portfolio Case Study

An image-led project narrative for a designer, studio, or maker. The work is the proof: lead with a real artifact and explain decisions and constraints instead of adding generic feature cards or invented awards.

## Section Structure

```
1. Project opening  — H1 + role/discipline + lead image
2. Brief & scope    — client context, objective, contribution, constraints
3. Process          — annotated sequence of sketches, drafts, or decisions
4. Outcome          — final work with captions and verifiable results, if any
5. Reflection       — what changed, learned, or should be done differently
6. Next project     — link to another real case + project inquiry
```

## Module counts

- 6 sections, usually 6–8 rows; one or two columns per row depending on image/text pairing.
- Typical: 6–9 headings, 8–12 text modules, 4–8 images, 2 buttons or linked text modules.
- Scale with available evidence; never create empty image slots just to hit a count.

## Animation pattern

- **Opening**: keep the title and first work sample visible without scroll animation.
- **Process**: optional `fade` on separate image-caption pairs, not on the entire section.
- **Outcome**: static for clear inspection and predictable image loading.
- **Next project**: hover/focus treatment on the link; avoid motion that hides its label.
- **Reduced motion**: verify reading order and contrast without any animation.

## Section blueprints

### 1. Project opening
```
Section (bg: gallery-like neutral; semantic header)
└── Row
    └── Column
        ├── Text [discipline] — factual project category
        ├── Heading [H1] — project title
        ├── Text [one-sentence thesis] — what was made and for whom
        └── Image [hero work] — real artifact with alt text
```

### 2. Brief & scope
```
Section (bg: neutral)
└── Row (two columns; phone: context before scope)
    ├── Column [story]
    │   ├── Heading [H2] — the brief
    │   └── Text [context and constraint]
    └── Column [facts]
        ├── Text [role and collaborators]
        ├── Text [deliverables]
        └── Text [date or timeframe, if known]
```

### 3. Process
```
Section (bg: contrasting paper tone)
└── Row
    └── Column
        ├── Heading [H2] — decisions that shaped the work
        ├── Group [artifact 01] — Image + Text [decision / caption]
        ├── Group [artifact 02] — Image + Text [iteration / caption]
        └── Group [artifact 03] — Image + Text [reason for final direction]
```

### 4. Outcome
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — final result
        ├── Image [finished artifact] — correct crop on tablet/phone
        ├── Text [caption] — what the visitor is seeing
        └── Text [result] — attributed metric or qualitative observation, if verified
```

### 5. Reflection
```
Section (bg: quiet accent)
└── Row (narrow text measure)
    └── Column
        ├── Heading [H2] — what this project taught us
        └── Text [specific takeaway] — no generic praise
```

### 6. Next project
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — explore another project
        ├── Button [next project] — real case-study URL
        └── Text [inquiry link] — secondary action, visually demoted
```

## Using with presets

Use the site's heading/body roles from `.devin/skills/diviskit-builder/design-system.json` when available. Set image sizing/spacing on `module.advanced` (not `module.decoration`); see [module-formats.md](../module-formats.md). Keep image captions consistent without forcing identical crops.

## Customization points

- **Project type**: choose artifacts that explain the actual process (photography, prototypes, drawings, or UI).
- **Evidence**: replace outcome copy with real results; omit unverifiable figures, awards, and client quotes.
- **Media**: obtain rights and alt text for every image; use available aspect ratios to guide composition.
- **Sequence**: expand or contract the process to fit the work; keep only one H1.
- **Art direction**: derive an editorial rule from the work; see [design-guide.md](../design-guide.md) and do not turn the case into a SaaS landing page.
- **Divi mechanics**: use section → row → column → modules, unique `module.meta.adminLabel.desktop.value` labels, and explicit phone ordering.
