# Diviskit Design Library

WordPress plugin providing modern design effects for Divi 5 pages. CSS animations, glass morphism, Three.js WebGL shaders, and scroll-triggered effects.

Forked from the GPL-licensed `diviops-design-library`. The integration contract was renamed in the fork: `dsk-*` classes, `_diviskit_design_*` post-meta keys, `diviskit-design-fx`/`threejs` handles (previously `ddl-*`, `_divi_design_*`, `divi-design-fx` — hard cutover, no legacy aliases).

## Upgrade From The Previous Plugin Name

1. Deactivate the old `DiviOps Design Library` plugin.
2. Install or copy `diviskit-design-library/`.
3. Activate `Diviskit Design Library`.

## What It Provides

### CSS Classes (add via VB Advanced > CSS Classes)

| Class | Effect |
|-------|--------|
| `dsk-animate dsk-fade-up` | Fade in from below |
| `dsk-animate dsk-fade-in` | Fade in |
| `dsk-animate dsk-scale-in` | Scale up from 90% |
| `dsk-animate dsk-slide-left` | Slide in from left |
| `dsk-animate dsk-slide-right` | Slide in from right |
| `dsk-delay-1` to `dsk-delay-6` | Stagger delays (0.1s increments) |
| `dsk-glass` | Glass morphism (dark) |
| `dsk-glass-light` | Glass morphism (light) |
| `dsk-hover-lift` | Lift on hover (-4px + shadow) |
| `dsk-gradient-animated` | Animated gradient background |
| `dsk-gradient-text` | Static gradient text |
| `dsk-gradient-text-animated` | Animated gradient text |
| `dsk-text-stroke` | Light text outline (stroke) |
| `dsk-text-stroke-dark` | Dark text outline (stroke) |
| `dsk-pulse-dot` | Pulsing green indicator |

### Three.js WebGL
- Three.js r128 bundled locally (no CDN)
- Loaded when post meta `_diviskit_design_threejs` is exactly `'1'`, or when page content contains one of these case-sensitive markers: `webgl`, `THREE`, `shader`, `three.js`
- Use with Code module for custom shader heroes

### Gooey Text Morphing
- SVG `feColorMatrix` filter for liquid text transitions
- IntersectionObserver for scroll-triggered class toggling
- No external dependencies

## How Effects Are Applied

1. **Via VB**: Add CSS classes in module Advanced > Custom Attributes
2. **Via MCP**: Use `module.decoration.attributes` to add classes programmatically
3. **Via freeForm CSS**: Section-level custom CSS for animations/keyframes

## Files

```
diviskit-design-library/
├── diviskit-design-library.php    # Plugin registration, CSS output, script enqueueing
└── assets/
    └── js/
        ├── design-fx.js       # IntersectionObserver + gooey SVG injection
        └── three.min.js       # Three.js r128 (bundled)
```

## Conditional Loading
- CSS is always printed (lightweight, no external requests)
- `design-fx.js` loads on all frontend pages
- `three.min.js` only loads through the explicit `_diviskit_design_threejs = '1'` post-meta opt-in or the documented content markers
