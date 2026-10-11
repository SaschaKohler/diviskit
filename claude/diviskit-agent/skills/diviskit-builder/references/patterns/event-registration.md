# Pattern: Event Registration

A registration page organized around an actual date, place, and program. The primary CTA goes to a working ticket or RSVP destination. Keep logistics visible rather than burying them under social proof; omit any unconfirmed speaker or sponsor.

## Section Structure

```
1. Event opening   — H1 + date/time + venue or online format + registration CTA
2. Why attend      — concrete takeaways and intended audience
3. Program         — timed agenda or session sequence, only if confirmed
4. People          — confirmed hosts/speakers and their real roles (optional)
5. Practicalities  — location/access, price, accessibility, cancellation
6. Register        — availability and repeated CTA with next-step details
```

## Module counts

- Base: 5 sections, about 5–7 rows; add a section for confirmed speakers.
- Typical: 7–11 headings, 10–18 text modules, 2 buttons, optional speaker images and map link.
- Module totals depend on agenda length; don't invent sessions to fill a grid.

## Animation pattern

- **Opening**: no entrance animation on date, venue, or CTA.
- **Program**: optional subtle `fade` by agenda group; keep times and labels legible at once.
- **People**: restrained image reveal only if real portraits are available.
- **Practicalities / Register**: static; no urgency gimmicks or fake countdown.
- **Reduced motion**: check that the registration route and agenda remain usable.

## Section blueprints

### 1. Event opening
```
Section (bg: palette drawn from real venue/topic; semantic header)
└── Row (two columns; phone: event details first)
    ├── Column
    │   ├── Text [event type] — workshop, talk, conference, etc.
    │   ├── Heading [H1] — actual event title
    │   ├── Text [when/where] — date, time, timezone, venue or online
    │   └── Button [register] — real RSVP/ticket URL
    └── Column
        └── Image [event visual] — licensed artwork or real venue image
```

### 2. Why attend
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — what attendees can expect
        └── Group [takeaways] — open list, phone: same reading order
            ├── Text [audience and benefit 01]
            ├── Text [benefit 02]
            └── Text [benefit 03]
```

### 3. Program
```
Section (bg: contrasting neutral)
└── Row
    └── Column
        ├── Heading [H2] — schedule
        └── Group [agenda] — flex column, ruled rows
            ├── Group [session 01] — Text [time] + Heading [H3] + Text [description]
            ├── Group [session 02] — same structure
            └── Group [session 03] — same structure
```

### 4. People (optional)
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — meet the hosts
        └── Group [speakers] — flex row, phone: column
            ├── Group [speaker] — Image [portrait] + Heading [H3] + Text [role]
            └── Group [speaker] — same structure if confirmed
```

### 5. Practicalities
```
Section (bg: neutral)
└── Row
    └── Column
        ├── Heading [H2] — everything needed to attend
        ├── Text [venue/access link or online joining details]
        ├── Text [price and ticket terms]
        ├── Text [accessibility and contact route]
        └── Text [cancellation terms, if applicable]
```

### 6. Register
```
Section (bg: clear brand accent, high contrast)
└── Row (narrow width)
    └── Column
        ├── Heading [H2] — invitation with actual date
        ├── Text [next steps after registration]
        └── Button [register] — same destination as opening
```

## Using with presets

Use the site's heading/body/button presets via `.devin/skills/diviskit-builder/design-system.json` when present, otherwise inline values. Refer to [minimal-snippets.md](../minimal-snippets.md) for valid block shapes. Verify text contrast on any event image or accent background.

## Customization points

- **Format**: distinguish in-person, hybrid, and online; show the timezone for remote attendees.
- **Program**: publish only confirmed times; when pending, replace the timed agenda with topics and say it is provisional.
- **People**: use approved bios/portraits; omit unconfirmed names and sponsors.
- **Tickets**: link to the actual booking destination; state the verified price, terms, and capacity only.
- **Art direction**: derive typographic rhythm or wayfinding from the event; see [design-guide.md](../design-guide.md) instead of cloning a generic landing-page skeleton.
- **Divi mechanics**: section → row → column nesting, unique `module.meta.adminLabel.desktop.value` labels, tablet/phone overrides, one H1.
