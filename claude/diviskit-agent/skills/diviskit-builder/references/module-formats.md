# DiviSkit Module Reference (Tier 3)

Auto-generated from Divi 5.13 schema dump.
Schema version: `4ae1b5c8794947d76f70515881b7aa93170359fe`
Modules: 87

Each entry lists **elements**, **innerContent shapes**, and **surprises** only.
Standard decoration (`{element}.decoration.*`) is assumed — NOT repeated here.
Combine with Tier 1 (universal decoration) and Tier 2 (font/icon patterns) for full blocks.

Generated via `diviskit_schema_get_module` with `mode: 'dump_all'`.
Re-generate with: `python3 gen_all.py`

---

## Structure Modules (6)

### Column
`divi/column`

**Elements**: `module`

- CSS `!important` on `module`: spacing.margin-right, spacing.phone.margin-right, spacing.phone.margin-top

---

### Inner Column
`divi/column-inner`

**Elements**: `module`

---

### Group
`divi/group`

**Elements**: `module`

- CSS `!important` on `module.sizing.width`
- CSS `!important` on `module.spacing`

---

### Row
`divi/row`

**Elements**: `module`

- CSS `!important` on `module`: position, sizing.margin-left, sizing.margin-right, sizing.max-width, sizing.width, spacing

---

### Inner Row
`divi/row-inner`

**Elements**: `module`

- CSS `!important` on `module.spacing.padding`

---

### Section
`divi/section`

**Elements**: `column1`, `column2`, `column3`, `innerSizing`, `module`

- `column1.decoration` limited to: background, spacing
- `column2.decoration` limited to: background, spacing
- `column3.decoration` limited to: background, spacing
- `innerSizing.decoration` limited to: sizing
- VB-hidden: `column1.decoration.spacing.margin` (functional via block JSON)
- VB-hidden: `column2.decoration.spacing.margin` (functional via block JSON)
- VB-hidden: `column3.decoration.spacing.margin` (functional via block JSON)
- CSS `!important` on `module`: background, sizing.margin-left, sizing.margin-right, zIndex

---

## Content Modules (56)

### Accordion
`divi/accordion`

**Elements**: `closedToggle`, `closedToggleIcon`, `content`, `module`, `openToggle`, `title`

- Font Family A (bodyFont) on `content`
- `openToggle.decoration` limited to: background
- `closedToggle.decoration` limited to: background
- VB-hidden: `closedToggleIcon.decoration.icon.contentIcon.color` (functional via block JSON)
- VB-hidden: `closedToggleIcon.decoration.icon.contentIcon.useSize` (functional via block JSON)
- VB-hidden: `closedToggleIcon.decoration.icon.contentIcon.size` (functional via block JSON)
- VB-hidden: `closedToggleIcon.decoration.icon.designIcon.icon` (functional via block JSON)
- CSS `!important` on `closedToggleIcon`: icon.content, icon.font-family, icon.font-weight
- CSS `!important` on `module.spacing.margin`

**CSS selectors**:
- `title`: `{{selector}} > .et_pb_toggle > h1.et_pb_toggle_title, {{selector}} > .et_pb_toggle > h2.et_pb_toggle_title, {{selector}}...`
- `closedToggleIcon`: `{{selector}} > .et_pb_toggle_close:not(.et_pb_toggle_empty) > .et_pb_toggle_title:before`
- `content`: `{{selector}}.et_pb_accordion .et_pb_toggle_content`
- `openToggle`: `{{selector}} .et_pb_toggle_open`
- `closedToggle`: `{{selector}} .et_pb_toggle_close`

---

### Audio
`divi/audio`

**Elements**: `albumName`, `artistName`, `audio`, `caption`, `image`, `module`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `artistName` | Artist | content |
| `albumName` | Album | content |
| `audio` | Audio File | content |
| `title` | Title | content |
| `image` | Image | content |

- CSS `!important` on `caption.font.color`
- CSS `!important` on `module`: background, sizing.margin-left, sizing.margin-right, spacing
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `caption`: `{{selector}}.et_pb_audio_module p`
- `artistName`: `{{selector}}.et_pb_audio_module p.et_audio_module_meta strong`
- `albumName`: `{{selector}}.et_pb_audio_module p.et_audio_module_meta span`
- `title`: `{{selector}} h2, {{selector}} h1.et_pb_module_header, {{selector}} h3.et_pb_module_header, {{selector}} h4.et_pb_module_...`
- `image`: `{{selector}} .et_pb_audio_cover_art`

---

### Before/After Image
`divi/before-after-image`

**Elements**: `afterImage`, `afterLabel`, `beforeImage`, `beforeLabel`, `labels`, `module`, `slider`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `beforeImage` | beforeImage.beforeImage.settings | — |
| `afterImage` | afterImage.afterImage.settings | — |
| `beforeLabel` | Before Label | content |
| `afterLabel` | After Label | content |

- `slider.advanced.orientation` — element-specific advanced field
- `slider.advanced.position` — element-specific advanced field
- `slider.advanced.color` — element-specific advanced field
- `slider.advanced.arrowColor` — element-specific advanced field
- `labels.decoration` limited to: background, border, spacing

**CSS selectors**:
- `beforeImage`: `{{selector}} .et_pb_before_image img`
- `afterImage`: `{{selector}} .et_pb_after_image img`
- `beforeLabel`: `{{selector}} .et_pb_before_label`
- `afterLabel`: `{{selector}} .et_pb_after_label`
- `labels`: `{{selector}} .et_pb_before_label, {{selector}} .et_pb_after_label`

---

### Blog
`divi/blog`

**Elements**: `blogGrid`, `content`, `fullwidth`, `image`, `masonry`, `meta`, `module`, `overlay`, `overlayIcon`, `pagination`, `post`, `readMore`, `title`

- `post.advanced.useCurrentLoop` — element-specific advanced field
- `post.advanced.type` — element-specific advanced field
- `post.advanced.number` — element-specific advanced field
- `post.advanced.categories` — element-specific advanced field
- `post.advanced.dateFormat` — element-specific advanced field
- `post.advanced.excerptContent` — element-specific advanced field
- `post.advanced.excerptManual` — element-specific advanced field
- `post.advanced.excerptLength` — element-specific advanced field
- `post.advanced.offset` — element-specific advanced field
- `post.advanced.showExcerpt` — element-specific advanced field
- `post.decoration` limited to: border
- `image.advanced.enable` — element-specific advanced field
- `readMore.advanced.enable` — element-specific advanced field
- `pagination.advanced.enable` — element-specific advanced field
- `meta.advanced.showAuthor` — element-specific advanced field
- `meta.advanced.showDate` — element-specific advanced field
- `meta.advanced.showCategories` — element-specific advanced field
- `meta.advanced.showComments` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- `overlay.advanced.enable` — element-specific advanced field
- `overlay.decoration` limited to: background
- `fullwidth.decoration` limited to: border
- `blogGrid.decoration` limited to: layout
- `masonry.decoration` limited to: background
- CSS `!important` on `content.bodyFont.body.font.color`
- CSS `!important` on `content.bodyFont.link.font.color`
- CSS `!important` on `meta.font.color`
- CSS `!important` on `overlayIcon.icon`
- CSS `!important` on `pagination`: font.color, font.font-size, font.font-weight
- CSS `!important` on `readMore.font.color`

**CSS selectors**:
- `post`: `{{selector}} .et_grid_module article.et_pb_post`
- `image`: `{{selector}} .et_pb_post .entry-featured-image-url,{{selector}} .et_pb_post .et_pb_slides,{{selector}} .et_pb_post .et_p...`
- `readMore`: `{{selector}} .et_pb_post div.post-content a.more-link`
- `pagination`: `{{selector}} .wp-pagenavi a, {{selector}} .wp-pagenavi span, {{selector}} .pagination a`
- `meta`: `{{selector}} .et_pb_post .post-meta, {{selector}} .et_pb_post .post-meta a, #left-area {{selector}} .et_pb_post .post-me...`
- `title`: `{{selector}} .et_pb_post .entry-title, {{selector}} .not-found-title`
- `content`: `{{selector}} .et_pb_post .post-content, {{selector}}.et_pb_bg_layout_light .et_pb_post .post-content p, {{selector}}.et_...`
- `overlay`: `{{selector}} .et_overlay`
- `overlayIcon`: `{{selector}} .et_overlay::before`
- `fullwidth`: `{{selector}}:not(.et_pb_blog_grid_wrapper) article.et_pb_post`
- `blogGrid`: `{{selector}} .et_pb_blog_posts`
- `masonry`: `{{selector}} .et_grid_module .et_pb_post`

---

### Blurb
`divi/blurb`

**Elements**: `content`, `contentContainer`, `imageIcon`, `module`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `imageIcon` | imageIcon.imageIcon.settings | — |
| `content` | content.content.settings | — |

- `imageIcon.advanced.color` — element-specific advanced field
- `imageIcon.advanced.placement` — element-specific advanced field
- `imageIcon.decoration` limited to: animation, background, spacing
- Font Family A (bodyFont) on `content`
- `contentContainer.decoration` limited to: sizing
- CSS `!important` on `imageIcon.spacing`
- CSS `!important` on `module.spacing`
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `imageIcon`: `{{selector}} > .et_pb_blurb_content > .et_pb_main_blurb_image .et-pb-icon, {{selector}} > .et_pb_blurb_content > .et_pb_...`
- `title`: `{{selector}} .et_pb_module_header`
- `content`: `{{selector}} .et_pb_blurb_description`
- `contentContainer`: `{{selector}} .et_pb_blurb_content`

---

### Breadcrumbs
`divi/breadcrumbs`

**Elements**: `breadcrumb`, `breadcrumbLink`, `home`, `module`, `separator`, `trail`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `home` | home.home.settings | — |
| `separator` | Separator | content |

- VB-hidden: `breadcrumb.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `breadcrumb.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `breadcrumbLink.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `breadcrumbLink.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `home.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `home.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `separator.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `separator.decoration.background.image.parallaxMethod` (functional via block JSON)

**CSS selectors**:
- `breadcrumb`: `{{selector}} .et_pb_breadcrumbs--trail .et_pb_breadcrumbs--breadcrumb`
- `breadcrumbLink`: `{{selector}} .et_pb_breadcrumbs--trail a.et_pb_breadcrumbs--breadcrumb:not(.et_pb_breadcrumbs--home)`
- `home`: `{{selector}} .et_pb_breadcrumbs--trail a.et_pb_breadcrumbs--home`
- `separator`: `{{selector}} .et_pb_breadcrumbs--trail .et_pb_breadcrumbs--separator`

---

### Button
`divi/button`

**Elements**: `button`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `button` | Button Text | content |

- VB-hidden: `button.decoration.button.alignment` (functional via block JSON)
- VB-hidden: `button.decoration.button.boxShadowGroup` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- VB-hidden: `button.decoration.button.spacingGroup` (functional via block JSON)
- VB-hidden: `button.decoration.spacing.margin` (functional via block JSON)
- VB-hidden: `button.decoration.spacing.padding` (functional via block JSON)
- CSS `!important` on `button`: font.color, font.font-size, font.letter-spacing, font.line-height, spacing
- CSS `!important` on `module.spacing`

**CSS selectors**:
- `button`: `body #page-container .et_pb_section {{baseSelector}}`

---

### Canvas Portal
`divi/canvas-portal`

**Elements**: `canvas`, `css`, `module`

- `canvas.advanced.canvasId` — element-specific advanced field
- `css.advanced.customCss` — element-specific advanced field
- `css.advanced.cssId` — element-specific advanced field
- `css.advanced.cssClass` — element-specific advanced field

---

### Chart
`divi/charts`

**Elements**: `chart`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `chart` | chart.chart.settings | — |

- `chart.advanced.config` — element-specific advanced field
- `chart.advanced.title` — element-specific advanced field
- `chart.advanced.subtitle` — element-specific advanced field
- `chart.advanced.legend` — element-specific advanced field
- `chart.advanced.tooltip` — element-specific advanced field

**CSS selectors**:
- `chart`: `{{selector}} .et_pb_charts__canvas-wrap`

---

### Circle Counter
`divi/circle-counter`

**Elements**: `circle`, `contentContainer`, `module`, `number`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |
| `number` | Number | content |

- `circle.advanced.circle` — element-specific advanced field
- `number.advanced.percentSign` — element-specific advanced field
- VB-hidden: `number.decoration.font.headingLevel` (functional via block JSON)
- VB-hidden: `number.decoration.font.lineHeight` (functional via block JSON)
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing.margin
- CSS `!important` on `number.font.color`
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `circle`: `{{selector}} .percent canvas`
- `title`: `{{selector}}.et_pb_circle_counter h3, {{selector}}.et_pb_circle_counter h1.et_pb_module_header, {{selector}}.et_pb_circl...`
- `number`: `{{selector}}.et_pb_circle_counter .percent p`
- `contentContainer`: `{{selector}} .et_pb_circle_counter_inner`

---

### Code
`divi/code`

**Elements**: `content`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Code | content |

- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing.margin

**CSS selectors**:
- `content`: `{{selector}} .et_pb_code_inner`

---

### Comments
`divi/comments`

**Elements**: `button`, `commentCount`, `commentText`, `field`, `formTitle`, `image`, `meta`, `module`

- `image.advanced.showAvatar` — element-specific advanced field
- `commentCount.advanced.showCount` — element-specific advanced field
- `meta.advanced.showMeta` — element-specific advanced field
- VB-hidden: `button.decoration.button.fontGroup.lineHeight` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- CSS `!important` on `button.spacing`
- CSS `!important` on `commentCount.font.color`
- CSS `!important` on `commentText.font.color`
- CSS `!important` on `field.border`
- CSS `!important` on `field.font`
- CSS `!important` on `meta.font`
- CSS `!important` on `module.border`
- CSS `!important` on `module.spacing`

**CSS selectors**:
- `image`: `{{selector}} .commentlist img.avatar`
- `field`: `{{selector}} #commentform textarea, {{selector}} #commentform input[type='text'], {{selector}} #commentform input[type='...`

---

### Contact Form
`divi/contact-form`

**Elements**: `button`, `captcha`, `checkbox`, `email`, `field`, `module`, `radio`, `redirect`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |
| `button` | Submit Button | content |
| `email` | Message Pattern | content |
| `redirect` | Redirect URL | content |

- `email.advanced.receiver` — element-specific advanced field
- `redirect.advanced.useRedirect` — element-specific advanced field
- VB-hidden: `captcha.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `button.spacing`
- CSS `!important` on `module.spacing.margin`

**CSS selectors**:
- `title`: `{{selector}} h1, {{selector}} h2.et_pb_contact_main_title, {{selector}} h3.et_pb_contact_main_title, {{selector}} h4.et_...`
- `captcha`: `{{selector}}.et_pb_contact_form_container .et_pb_contact_right p`
- `field`: `{{selector}} .input:not([type=checkbox]):not([type=radio])`
- `checkbox`: `{{selector}} [type=checkbox]`
- `radio`: `{{selector}} [type=radio]`
- `button`: `{{selector}} .et_pb_contact_submit.et_pb_button`

---

### Countdown Timer
`divi/countdown-timer`

**Elements**: `content`, `label`, `module`, `number`, `separator`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |

- `content.advanced.dateTime` — element-specific advanced field
- VB-hidden: `separator.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `font.font-size`
- CSS `!important` on `font.line-height`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing
- CSS `!important` on `number.font`
- CSS `!important` on `separator.font`
- CSS `!important` on `title.font.color`
- CSS `!important` on `title.font.font-size`

**CSS selectors**:
- `title`: `{{selector}} h4, {{selector}} h1.title, {{selector}} h2.title, {{selector}} h3.title, {{selector}} h5.title, {{selector}...`
- `content`: `{{selector}} .value`
- `number`: `{{selector}} .section p.value, {{selector}} .section.sep p`
- `separator`: `{{selector}} .et_pb_countdown_timer_container .section.sep p`
- `label`: `{{selector}} .section p.label`

---

### Bar Counters
`divi/counters`

**Elements**: `barCounter`, `barProgress`, `children`, `module`, `title`

- `barCounter.decoration` limited to: background, border, boxShadow
- `barProgress.advanced.usePercentages` — element-specific advanced field
- `children.decoration` limited to: background
- VB-hidden: `title.decoration.font.headingLevel` (functional via block JSON)
- CSS `!important` on `barProgress.font.color`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing.margin
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `barCounter`: `{{selector}} .et_pb_counter_container`
- `title`: `{{selector}}.et_pb_counters .et_pb_counter_title`
- `barProgress`: `{{selector}}.et_pb_counters .et_pb_counter_amount_number`

---

### Call To Action
`divi/cta`

**Elements**: `button`, `content`, `module`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |
| `content` | content.content.settings | — |
| `button` | Button | content |

- Font Family A (bodyFont) on `content`
- CSS `!important` on `button`: font.color, font.line-height, spacing.margin, spacing.padding
- CSS `!important` on `content.bodyFont.body.font.color`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing
- CSS `!important` on `title.font`

**CSS selectors**:
- `title`: `{{selector}} h2, {{selector}} h1.et_pb_module_header, {{selector}} h3.et_pb_module_header, {{selector}} h4.et_pb_module_...`
- `content`: `{{selector}} .et_pb_promo_description .et_pb_promo_content`
- `button`: `body #page-container {{selector}} .et_pb_promo_button.et_pb_button`

---

### Divider
`divi/divider`

**Elements**: `divider`, `module`

- `divider.advanced.line` — element-specific advanced field
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, sizing.min-height, sizing.width, spacing.margin

---

### Dropdown
`divi/dropdown`

**Elements**: `module`

---

### Filterable Portfolio
`divi/filterable-portfolio`

**Elements**: `filter`, `image`, `meta`, `module`, `overlay`, `pagination`, `portfolio`, `portfolioGrid`, `portfolioItem`, `title`

- `portfolio.advanced.postsNumber` — element-specific advanced field
- `portfolio.advanced.includedCategories` — element-specific advanced field
- `portfolio.advanced.showTitle` — element-specific advanced field
- `portfolio.advanced.showCategories` — element-specific advanced field
- `portfolio.advanced.showPagination` — element-specific advanced field
- `portfolioGrid.decoration` limited to: layout
- `portfolioItem.decoration` limited to: border
- `overlay.decoration` limited to: background
- VB-hidden: `overlay.decoration.icon.icon.color` (functional via block JSON)
- VB-hidden: `overlay.decoration.icon.icon.useSize` (functional via block JSON)
- VB-hidden: `filter.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `filter.font.color`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing.margin
- CSS `!important` on `overlay`: icon.color, icon.content, icon.font-family, icon.font-weight
- CSS `!important` on `title.font`

**CSS selectors**:
- `portfolioGrid`: `{{selector}} .et_pb_posts`
- `title`: `{{selector}}.et_pb_filterable_portfolio h2, {{selector}}.et_pb_filterable_portfolio h2 a, {{selector}}.et_pb_filterable_...`
- `overlay`: `{{selector}}.et_pb_filterable_portfolio .et_overlay`
- `image`: `{{selector}} .et_portfolio_image`
- `filter`: `{{selector}}.et_pb_filterable_portfolio .et_pb_portfolio_filter, {{selector}} .et_pb_portfolio_filter a`
- `meta`: `{{selector}}.et_pb_filterable_portfolio .post-meta, {{selector}}.et_pb_filterable_portfolio .post-meta a`
- `pagination`: `{{selector}}.et_pb_filterable_portfolio .et_pb_portofolio_pagination a`

---

### Gallery
`divi/gallery`

**Elements**: `caption`, `galleryGrid`, `image`, `item`, `module`, `overlay`, `pagination`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `overlay` | overlay.overlay.settings | — |

- `pagination.advanced.showPagination` — element-specific advanced field
- `item.decoration` limited to: border
- `image.advanced.orientation` — element-specific advanced field
- `image.advanced.galleryIds` — element-specific advanced field
- `image.advanced.galleryOrderby` — element-specific advanced field
- `overlay.advanced.zoomIconColor` — element-specific advanced field
- `overlay.advanced.hoverOverlayColor` — element-specific advanced field
- `galleryGrid.decoration` limited to: layout
- CSS `!important` on `caption.font.color`
- CSS `!important` on `image.spacing.margin-left`
- CSS `!important` on `image.spacing.margin-right`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing.margin
- CSS `!important` on `pagination.font.color`
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `title`: `{{selector}} .et_pb_gallery_title`
- `caption`: `{{selector}} .et_pb_gallery_caption`
- `pagination`: `{{selector}} .et_pb_gallery_pagination`
- `item`: `{{selector}} .et_pb_gallery_item`
- `image`: `{{selector}} .et_pb_gallery_image img`
- `galleryGrid`: `{{selector}} .et_pb_gallery_items`

---

### Group Carousel
`divi/group-carousel`

**Elements**: `activeGroups`, `arrows`, `children`, `dotNav`, `module`

- `arrows.advanced.show` — element-specific advanced field
- `arrows.advanced.leftIcon` — element-specific advanced field
- `arrows.advanced.rightIcon` — element-specific advanced field
- `arrows.advanced.color` — element-specific advanced field
- `arrows.advanced.size` — element-specific advanced field
- `arrows.advanced.position` — element-specific advanced field
- `dotNav.advanced.show` — element-specific advanced field
- `dotNav.advanced.position` — element-specific advanced field
- `dotNav.advanced.alignment` — element-specific advanced field
- `dotNav.advanced.size` — element-specific advanced field
- `dotNav.advanced.color` — element-specific advanced field
- CSS `!important` on `module.spacing.margin`

**CSS selectors**:
- `arrows`: `{{selector}} .et_pb_group_carousel_arrow`
- `dotNav`: `{{selector}} .et_pb_group_carousel_dots`
- `children`: `{{selector}} .et_pb_group_carousel_slide > .et_pb_group`
- `activeGroups`: `{{selector}} .et_pb_group_carousel_slide_active > .et_pb_group`

---

### Heading
`divi/heading`

**Elements**: `module`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Heading | content |

- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `title`: `{{selector}} .et_pb_heading_container h1, {{selector}} .et_pb_heading_container h2, {{selector}} .et_pb_heading_containe...`

---

### Icon
`divi/icon`

**Elements**: `icon`, `iconLink`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `icon` | Icon | content |

- `icon.advanced.color` — element-specific advanced field
- `icon.advanced.size` — element-specific advanced field
- `icon.advanced.align` — element-specific advanced field
- CSS `!important` on `module.spacing`

**CSS selectors**:
- `icon`: `{{selector}} .et_pb_icon_wrap .et-pb-icon`

---

### Icon List
`divi/icon-list`

**Elements**: `icon`, `listItem`, `module`

- `icon.advanced.color` — element-specific advanced field
- `icon.advanced.size` — element-specific advanced field
- CSS `!important` on `module.spacing.margin`
- CSS `!important` on `module.spacing.padding`

**CSS selectors**:
- `icon`: `{{selector}} .et-pb-icon`
- `listItem`: `{{selector}} .et_pb_icon_list_text`

---

### Image
`divi/image`

**Elements**: `image`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `image` | image.image.settings | — |

- sizing/spacing on `module.advanced.{sizing, spacing}` — NOT `module.decoration`
- Alignment: `module.advanced.align.desktop.value`
- `image.advanced.lightbox` — element-specific advanced field
- `image.advanced.overlay` — element-specific advanced field
- `image.advanced.overlayIcon` — element-specific advanced field
- `image.decoration` limited to: border, boxShadow
- CSS `!important` on `module.sizing.margin-left`
- CSS `!important` on `module.sizing.margin-right`

**CSS selectors**:
- `image`: `{{selector}} img`

---

### Instagram Feed
`divi/instagram-feed`

**Elements**: `feed`, `followButton`, `item`, `media`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `feed` | feed.feed.settings | — |
| `followButton` | followButton.followButton.settings | — |

- `followButton.advanced.show` — element-specific advanced field
- VB-hidden: `decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `decoration.background.image.parallaxMethod` (functional via block JSON)

**CSS selectors**:
- `feed`: `{{selector}} .et_pb_instagram_feed__items`
- `item`: `{{selector}} .et_pb_instagram_feed__item`
- `media`: `{{selector}} .et_pb_instagram_feed__media`
- `followButton`: `{{selector}} .et_pb_instagram_feed__follow_button.et_pb_button`

---

### Link
`divi/link`

**Elements**: `content`, `icon`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Text | content |
| `icon` | Icon | content |

- `icon.advanced.color` — element-specific advanced field
- `icon.advanced.size` — element-specific advanced field
- CSS `!important` on `module.spacing`

**CSS selectors**:
- `content`: `{{selector}} .et_pb_link_inner`
- `icon`: `{{selector}} .et-pb-icon`

---

### Login
`divi/login`

**Elements**: `button`, `content`, `field`, `module`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |
| `content` | Body | content |
| `button` | Button | content |

- Font Family A (bodyFont) on `content`
- VB-hidden: `button.innerContent.link` (functional via block JSON)
- VB-hidden: `button.decoration.button.alignment` (functional via block JSON)
- CSS `!important` on `button`: font.font-size, font.line-height, font.text-decoration-color, font.text-decoration-line, font.text-decoration-style, spacing.margin, spacing.padding
- CSS `!important` on `content.bodyFont.body.font.color`
- CSS `!important` on `module`: position, sizing.margin-left, sizing.margin-right, spacing
- CSS `!important` on `title.font`

**CSS selectors**:
- `title`: `{{selector}}.et_pb_login h1.et_pb_module_header, {{selector}}.et_pb_login h2, {{selector}}.et_pb_login h3.et_pb_module_h...`
- `content`: `{{selector}}.et_pb_login .et_pb_newsletter_description_content, {{selector}}.et_pb_login p, {{selector}}.et_pb_login spa...`
- `field`: `{{selector}} input[type="password"], {{selector}} input[type="text"], {{selector}} textarea, {{selector}} input`
- `button`: `{{selector}}.et_pb_login .et_pb_newsletter_button.et_pb_button`

---

### Lottie
`divi/lottie`

**Elements**: `lottie`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `lottie` | lottie.lottie.settings | — |

- CSS `!important` on `module.spacing`

**CSS selectors**:
- `lottie`: `{{selector}} .et_pb_lottie_animation`

---

### Map
`divi/map`

**Elements**: `map`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `map` | Map Center Address | content |

- `map.advanced.warning` — element-specific advanced field
- `map.advanced.googleAPIKey` — element-specific advanced field
- `map.advanced.mouseWheel` — element-specific advanced field
- `map.advanced.mobileDragging` — element-specific advanced field
- `map.advanced.grayscaleFilter` — element-specific advanced field
- `map.advanced.grayscaleFilterAmount` — element-specific advanced field
- `map.decoration` limited to: filters
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, sizing.width, spacing.margin

**CSS selectors**:
- `map`: `{{selector}} .gm-style>div>div>div>div>div>img`

---

### Menu
`divi/menu`

**Elements**: `cartIcon`, `cartQuantity`, `hamburgerMenuIcon`, `logo`, `menu`, `menuContent`, `menuDropdown`, `menuMobile`, `module`, `searchIcon`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `logo` | logo.logo.settings | — |

- `menu.advanced.menuId` — element-specific advanced field
- `menu.advanced.style` — element-specific advanced field
- `menu.advanced.activeLinkColor` — element-specific advanced field
- `menuDropdown.advanced.direction` — element-specific advanced field
- `menuDropdown.advanced.animation` — element-specific advanced field
- `menuDropdown.advanced.lineColor` — element-specific advanced field
- `menuDropdown.advanced.activeLinkColor` — element-specific advanced field
- `menuDropdown.decoration` limited to: background
- `menuMobile.decoration` limited to: background
- `cartQuantity.advanced.show` — element-specific advanced field
- `cartIcon.advanced.show` — element-specific advanced field
- `searchIcon.advanced.show` — element-specific advanced field
- VB-hidden: `logo.decoration.sizing.alignment` (functional via block JSON)
- VB-hidden: `logo.decoration.sizing.minHeight` (functional via block JSON)
- VB-hidden: `menu.decoration.font.textAlign` (functional via block JSON)
- VB-hidden: `cartQuantity.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `menuDropdown.background`
- CSS `!important` on `menuDropdown.font.color`
- CSS `!important` on `menuMobile.background`
- CSS `!important` on `menuMobile.font`
- CSS `!important` on `module.spacing.margin`

**CSS selectors**:
- `module`: `{{selector}}.et_pb_menu`
- `logo`: `{{selector}} .et_pb_menu__logo-wrap img`
- `menuContent`: `{{selector}} .et_pb_menu__menu`
- `menuMobile`: `{{selector}} .et_mobile_nav_menu`
- `cartQuantity`: `{{selector}}.et_pb_menu .et_pb_menu__icon.et_pb_menu__icon__with_count .et_pb_menu__cart-count`
- `cartIcon`: `{{selector}} .et_pb_menu__icon.et_pb_menu__cart-button`
- `searchIcon`: `{{selector}} .et_pb_menu__icon.et_pb_menu__search-button`

---

### Number Counter
`divi/number-counter`

**Elements**: `module`, `number`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |
| `number` | Number | content |

- `number.advanced.enablePercentSign` — element-specific advanced field
- VB-hidden: `number.decoration.font.headingLevel` (functional via block JSON)
- CSS `!important` on `module.spacing.margin`
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `title`: `{{selector}}.et_pb_number_counter h3, {{selector}}.et_pb_number_counter h1.title, {{selector}}.et_pb_number_counter h2.t...`
- `number`: `{{selector}}.et_pb_number_counter .percent p`

---

### Payment Button
`divi/payment-button`

**Elements**: `button`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `button` | Payment Button Text | content |

- VB-hidden: `button.innerContent.link` (functional via block JSON)
- VB-hidden: `button.decoration.button.alignment` (functional via block JSON)
- VB-hidden: `button.decoration.button.boxShadowGroup` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- VB-hidden: `button.decoration.button.spacingGroup` (functional via block JSON)
- VB-hidden: `button.decoration.spacing.margin` (functional via block JSON)
- VB-hidden: `button.decoration.spacing.padding` (functional via block JSON)
- CSS `!important` on `button`: font.color, font.font-size, font.letter-spacing, font.line-height, spacing
- CSS `!important` on `module.spacing`

**CSS selectors**:
- `button`: `body #page-container .et_pb_section {{baseSelector}}`

---

### Portfolio
`divi/portfolio`

**Elements**: `image`, `meta`, `module`, `overlay`, `pagination`, `portfolio`, `portfolioGrid`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `portfolio` | portfolio.portfolio.settings | — |

- `portfolio.advanced.showTitle` — element-specific advanced field
- `portfolio.advanced.showCategories` — element-specific advanced field
- `portfolio.advanced.showPagination` — element-specific advanced field
- `portfolioGrid.decoration` limited to: layout
- `overlay.advanced.iconColor` — element-specific advanced field
- `overlay.advanced.hoverIcon` — element-specific advanced field
- `overlay.decoration` limited to: background
- CSS `!important` on `module.spacing.margin`
- CSS `!important` on `title.font`

**CSS selectors**:
- `portfolioGrid`: `{{selector}} .et_pb_posts`
- `title`: `{{selector}} .et_pb_module_header`
- `overlay`: `{{selector}} .et_pb_portfolio_item .et_overlay`
- `image`: `{{selector}} .et_portfolio_image`
- `meta`: `{{selector}} .et_pb_portfolio_item .post-meta, {{selector}} .et_pb_portfolio_item .post-meta a, {{selector}} .et_pb_port...`
- `pagination`: `{{selector}} .wp-pagenavi a, {{selector}} .wp-pagenavi span, {{selector}} .pagination a`

---

### Post Content
`divi/post-content`

**Elements**: `image`, `module`

**CSS selectors**:
- `image`: `{{selector}} img`

---

### Post Filter
`divi/post-filter`

**Elements**: `button`, `checkbox`, `field`, `module`, `multipleOrderButton`, `option`, `radio`

- VB-hidden: `option.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `option.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `multipleOrderButton.decoration.button.buttonIconGroup` (functional via block JSON)
- VB-hidden: `button.decoration.button.buttonIconGroup` (functional via block JSON)
- CSS `!important` on `button.spacing.margin`
- CSS `!important` on `button.spacing.padding`

**CSS selectors**:
- `field`: `{{selector}} .et_pb_post_filter__item-control-surface .et_pb_post_filter__item-control:not([type=checkbox]):not([type=ra...`
- `option`: `{{selector}} .et_pb_post_filter__item-option`
- `checkbox`: `{{selector}} .et_pb_post_filter__item-control[type=checkbox]`
- `radio`: `{{selector}} .et_pb_post_filter__item-control[type=radio]`
- `multipleOrderButton`: `{{selector}} .et_pb_post_filter__item-multiple-order-action`
- `button`: `{{selector}} .et_pb_post_filter__item-control-button`

---

### Pagination
`divi/post-nav`

**Elements**: `links`, `module`

- `links.advanced.prevText` — element-specific advanced field
- `links.advanced.nextText` — element-specific advanced field
- `links.advanced.showPrev` — element-specific advanced field
- `links.advanced.showNext` — element-specific advanced field
- VB-hidden: `links.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `links`: boxShadow, font.color, font.font-size, font.font-weight
- CSS `!important` on `module.sizing.margin-left`
- CSS `!important` on `module.sizing.margin-right`

**CSS selectors**:
- `links`: `{{selector}} .wp-pagenavi a, {{selector}} .wp-pagenavi span, {{selector}} .pagination a, {{selectorPrefix}}.et_pb_posts_...`

---

### Post Slider
`divi/post-slider`

**Elements**: `arrows`, `button`, `content`, `contentOverlay`, `image`, `meta`, `module`, `pagination`, `post`, `slideOverlay`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `button` | Button | — |

- `content.advanced.showOnMobile` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- `content.decoration` limited to: sizing
- `meta.advanced.enable` — element-specific advanced field
- `image.advanced.enable` — element-specific advanced field
- `image.advanced.placement` — element-specific advanced field
- `image.advanced.showOnMobile` — element-specific advanced field
- `button.advanced.enable` — element-specific advanced field
- `button.advanced.showOnMobile` — element-specific advanced field
- `post.advanced.number` — element-specific advanced field
- `post.advanced.categories` — element-specific advanced field
- `post.advanced.orderby` — element-specific advanced field
- `post.advanced.contentSource` — element-specific advanced field
- `post.advanced.excerptManual` — element-specific advanced field
- `post.advanced.excerptLength` — element-specific advanced field
- `post.advanced.offset` — element-specific advanced field
- `arrows.advanced.color` — element-specific advanced field
- `arrows.advanced.enable` — element-specific advanced field
- `pagination.advanced.enable` — element-specific advanced field
- `pagination.decoration` limited to: background
- `slideOverlay.advanced.use` — element-specific advanced field
- `slideOverlay.decoration` limited to: background
- `contentOverlay.advanced.use` — element-specific advanced field
- `contentOverlay.decoration` limited to: background, border
- VB-hidden: `content.decoration.sizing.alignment` (functional via block JSON)
- VB-hidden: `content.decoration.sizing.height` (functional via block JSON)
- VB-hidden: `content.decoration.sizing.maxHeight` (functional via block JSON)
- VB-hidden: `content.decoration.sizing.minHeight` (functional via block JSON)
- VB-hidden: `button.innerContent.link` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.lineHeight` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.styles` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.stylesTabNav` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.stylesTabbedWidth` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.stylesTabbedColor` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.stylesTabbedStyle` (functional via block JSON)
- CSS `!important` on `button.spacing`
- CSS `!important` on `content.bodyFont`
- CSS `!important` on `meta.font`
- CSS `!important` on `module.spacing.margin`
- CSS `!important` on `title.font.color`
- CSS `!important` on `title.font.font-size`

**CSS selectors**:
- `title`: `{{selector}}.et_pb_slider .et_pb_slide_description .et_pb_slide_title, {{selector}}.et_pb_slider .et_pb_slide_descriptio...`
- `content`: `{{selector}}.et_pb_slider .et_pb_slide_content_body`
- `meta`: `{{selector}}.et_pb_slider .et_pb_slide_content .post-meta, {{selector}}.et_pb_slider .et_pb_slide_content .post-meta a`
- `image`: `{{selector}} .et_pb_slide_image img`
- `button`: `{{selector}}.et_pb_slider .et_pb_more_button.et_pb_button`
- `arrows`: `{{selector}} .et-pb-slider-arrows .et-pb-arrow-prev, {{selector}} .et-pb-slider-arrows .et-pb-arrow-next`
- `pagination`: `{{selector}} .et-pb-controllers a, {{selector}} .et-pb-controllers .et-pb-active-control`
- `slideOverlay`: `{{selector}} .et_pb_slide .et_pb_slide_overlay_container`
- `contentOverlay`: `{{selector}} .et_pb_slide .et_pb_text_overlay_wrapper`

---

### Post Title
`divi/post-title`

**Elements**: `image`, `meta`, `module`, `textWrapper`, `title`

- `title.advanced.showTitle` — element-specific advanced field
- `meta.advanced.showMeta` — element-specific advanced field
- `meta.advanced.showAuthor` — element-specific advanced field
- `meta.advanced.showDate` — element-specific advanced field
- `meta.advanced.dateFormat` — element-specific advanced field
- `meta.advanced.showCategories` — element-specific advanced field
- `meta.advanced.showCommentsCount` — element-specific advanced field
- `textWrapper.advanced.useBackground` — element-specific advanced field
- `textWrapper.decoration` limited to: background
- `image.advanced.enabled` — element-specific advanced field
- `image.advanced.placement` — element-specific advanced field
- CSS `!important` on `meta.font.color`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing
- CSS `!important` on `title.font`

**CSS selectors**:
- `title`: `{{selector}} .et_pb_title_container h1.entry-title,{{selector}} .et_pb_title_container h2.entry-title,{{selector}} .et_p...`
- `meta`: `{{selector}} .et_pb_title_container .et_pb_title_meta_container, {{selector}} .et_pb_title_container .et_pb_title_meta_c...`
- `textWrapper`: `{{selector}} .et_pb_title_container`
- `image`: `{{selector}} .et_pb_title_featured_container img`

---

### Pricing Tables
`divi/pricing-tables`

**Elements**: `button`, `children`, `content`, `currencyFrequency`, `excluded`, `featuredContent`, `featuredCurrencyFrequency`, `featuredExcluded`, `featuredPrice`, `featuredSubtitle`, `featuredTable`, `featuredTitle`, `module`, `price`, `subtitle`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `button` | button.button.settings | — |

- `title.decoration` limited to: background
- `price.decoration` limited to: background, border
- `content.advanced.bulletColor` — element-specific advanced field
- `content.advanced.showBullet` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- `featuredTable.advanced.showDropShadow` — element-specific advanced field
- `featuredTable.decoration` limited to: background
- `featuredTitle.decoration` limited to: background
- `featuredContent.advanced.bulletColor` — element-specific advanced field
- `featuredPrice.decoration` limited to: background
- VB-hidden: `button.innerContent.text` (functional via block JSON)
- VB-hidden: `button.innerContent.link` (functional via block JSON)
- VB-hidden: `button.innerContent.attributes` (functional via block JSON)
- CSS `!important` on `button`: border.border-width, font, spacing.padding
- CSS `!important` on `currencyFrequency.font.color`
- CSS `!important` on `currencyFrequency.spacing.margin-left`
- CSS `!important` on `featuredContent.font.color`
- CSS `!important` on `featuredCurrencyFrequency.font.color`
- CSS `!important` on `featuredPrice.font.color`
- CSS `!important` on `featuredSubtitle.font.color`
- CSS `!important` on `featuredTitle.background.color`
- CSS `!important` on `featuredTitle.font.color`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing
- CSS `!important` on `price.font.color`
- CSS `!important` on `subtitle.font.color`
- CSS `!important` on `title.font`

**CSS selectors**:
- `title`: `{{selector}} .et_pb_pricing_heading h2,{{selector}} .et_pb_pricing_heading h1.et_pb_pricing_title,{{selector}} .et_pb_pr...`
- `subtitle`: `{{selector}} .et_pb_best_value`
- `price`: `{{selector}} .et_pb_et_price .et_pb_sum,{{selector}} .et_pb_pricing_content_top`
- `currencyFrequency`: `{{selector}} .et_pb_frequency,{{selector}} .et_pb_dollar_sign`
- `content`: `{{selector}} .et_pb_pricing li`
- `excluded`: `{{selector}} ul.et_pb_pricing li.et_pb_not_available,{{selector}} ul.et_pb_pricing li.et_pb_not_available span,{{selecto...`
- `featuredTable`: `{{selector}} .et_pb_featured_table`
- `featuredTitle`: `{{selector}} .et_pb_featured_table .et_pb_pricing_heading`
- `featuredContent`: `{{selector}} .et_pb_featured_table .et_pb_pricing li`
- `featuredSubtitle`: `{{selector}} .et_pb_featured_table .et_pb_best_value`
- `featuredPrice`: `{{selector}} .et_pb_featured_table .et_pb_pricing_content_top`
- `featuredCurrencyFrequency`: `{{selector}} .et_pb_featured_table .et_pb_frequency,{{selector}} .et_pb_featured_table .et_pb_dollar_sign`
- `featuredExcluded`: `{{selector}} .et_pb_featured_table ul.et_pb_pricing li.et_pb_not_available,{{selector}} .et_pb_featured_table ul.et_pb_p...`
- `button`: `{{selector}} .et_pb_button`

---

### Search
`divi/search`

**Elements**: `button`, `field`, `module`, `search`, `searchPlaceholder`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `searchPlaceholder` | searchPlaceholder.searchPlaceholder.settings | — |

- `search.advanced.showButton` — element-specific advanced field
- `search.advanced.excludePages` — element-specific advanced field
- `search.advanced.excludePosts` — element-specific advanced field
- `field.advanced.focus` — element-specific advanced field
- `field.advanced.placeholder` — element-specific advanced field
- `field.decoration` limited to: background
- `button.decoration` limited to: background
- VB-hidden: `button.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `button`: background, font.color, font.font-weight, font.line-height, font.textShadow
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing

**CSS selectors**:
- `field`: `{{selector}} input.et_pb_s`
- `button`: `{{selector}} input.et_pb_searchsubmit`

---

### Sidebar
`divi/sidebar`

**Elements**: `module`, `sidebar`, `sidebarWidgets`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `sidebar` | sidebar.sidebar.settings | — |

- `sidebar.advanced.layout` — element-specific advanced field
- `sidebarWidgets.advanced.flexType` — element-specific advanced field
- VB-hidden: `title.decoration.font.headingLevel` (functional via block JSON)
- VB-hidden: `sidebar.decoration.font.headingLevel` (functional via block JSON)
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing.margin
- CSS `!important` on `sidebar.font.color`
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `title`: `{{selector}}.et_pb_widget_area h1:first-of-type, {{selector}}.et_pb_widget_area h2:first-of-type, {{selector}}.et_pb_wid...`
- `sidebar`: `{{selector}}.et_pb_widget_area li, {{selector}}.et_pb_widget_area li:before, {{selector}}.et_pb_widget_area a, {{selecto...`
- `sidebarWidgets`: `{{selector}} .et_pb_widget`

---

### Email Optin
`divi/signup`

**Elements**: `button`, `checkbox`, `content`, `customFields`, `field`, `footerContent`, `formField`, `module`, `radio`, `resultMessage`, `success`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Body | content |
| `button` | Button | content |
| `footerContent` | Footer | content |

- Font Family A (bodyFont) on `content`
- `field.advanced.nameFieldOnly` — element-specific advanced field
- `field.advanced.nameField` — element-specific advanced field
- `field.advanced.firstNameField` — element-specific advanced field
- `field.advanced.lastNameField` — element-specific advanced field
- `field.advanced.nameFullwidth` — element-specific advanced field
- `field.advanced.firstNameFullwidth` — element-specific advanced field
- `field.advanced.lastNameFullwidth` — element-specific advanced field
- `field.advanced.emailFullwidth` — element-specific advanced field
- `field.advanced.ipAddress` — element-specific advanced field
- `customFields.advanced.enable` — element-specific advanced field
- `customFields.advanced.fields` — element-specific advanced field
- `customFields.advanced.notice` — element-specific advanced field
- `success.advanced.action` — element-specific advanced field
- `success.advanced.message` — element-specific advanced field
- `success.advanced.redirectUrl` — element-specific advanced field
- `success.advanced.redirectQuery` — element-specific advanced field
- VB-hidden: `button.innerContent.link` (functional via block JSON)
- VB-hidden: `button.decoration.button.alignment` (functional via block JSON)
- CSS `!important` on `button.spacing`
- CSS `!important` on `content.bodyFont.body.font.font-size`
- CSS `!important` on `module.spacing`
- CSS `!important` on `title.font`

**CSS selectors**:
- `title`: `{{selector}} .et_pb_newsletter_description h2, {{selector}} .et_pb_newsletter_description h1.et_pb_module_header, {{sele...`
- `content`: `{{selector}} .et_pb_newsletter_description div`
- `button`: `body #page-container {{selector}}.et_pb_subscribe .et_pb_newsletter_button.et_pb_button`
- `field`: `{{selector}} .input:not([type=checkbox]):not([type=radio])`
- `checkbox`: `{{selector}} [type=checkbox]`
- `radio`: `{{selector}} [type=radio]`
- `footerContent`: `{{selector}} .et_pb_newsletter_footer`

---

### Slider
`divi/slider`

**Elements**: `arrows`, `button`, `children`, `content`, `dotNav`, `image`, `module`, `pagination`, `title`

- `arrows.advanced.color` — element-specific advanced field
- `arrows.advanced.show` — element-specific advanced field
- `pagination.advanced.show` — element-specific advanced field
- `pagination.advanced.style` — element-specific advanced field
- `pagination.advanced.swipeText` — element-specific advanced field
- `pagination.advanced.showCounter` — element-specific advanced field
- `pagination.advanced.showSwipeLabel` — element-specific advanced field
- `pagination.advanced.color` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- `content.decoration` limited to: sizing
- `children.advanced.slideOverlay` — element-specific advanced field
- `children.advanced.contentOverlay` — element-specific advanced field
- `children.advanced.content` — element-specific advanced field
- `children.advanced.button` — element-specific advanced field
- `children.decoration` limited to: background, border
- `image.advanced.showOnMobile` — element-specific advanced field
- `dotNav.decoration` limited to: background
- VB-hidden: `button.decoration.button.fontGroup.lineHeight` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- VB-hidden: `button.decoration.button.borderGroup.styles` (functional via block JSON)
- CSS `!important` on `button.font.color`
- CSS `!important` on `button.spacing`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing.margin
- CSS `!important` on `title.font.color`
- CSS `!important` on `title.font.font-size`

**CSS selectors**:
- `arrows`: `{{selector}} .et-pb-slider-arrows .et-pb-arrow-prev, {{selector}} .et-pb-slider-arrows .et-pb-arrow-next`
- `title`: `{{selector}}.et_pb_slider .et_pb_slide_description .et_pb_slide_title`
- `button`: `body #page-container {{selector}} .et_pb_more_button.et_pb_button`
- `content`: `{{selector}}.et_pb_slider .et_pb_slide_content`
- `image`: `{{selector}} .et_pb_slide_image img`
- `dotNav`: `{{selector}}:not(.et_pb_slider_bottom_controls) .et-pb-controllers a, {{selector}}:not(.et_pb_slider_bottom_controls) .e...`

---

### Social Media Follow
`divi/social-media-follow`

**Elements**: `button`, `icon`, `module`, `socialNetwork`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `button` | button.button.settings | — |

- `icon.advanced.color` — element-specific advanced field
- `icon.advanced.size` — element-specific advanced field
- `socialNetwork.advanced.followButton` — element-specific advanced field
- VB-hidden: `button.innerContent.url` (functional via block JSON)
- VB-hidden: `button.decoration.button.alignment` (functional via block JSON)
- VB-hidden: `button.decoration.button.buttonIconGroup` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing

**CSS selectors**:
- `module`: `{{selectorPrefix}}ul{{baseSelector}}`
- `icon`: `{{selector}} li.et_pb_social_icon a.icon, {{selector}} li.et_pb_social_icon a.icon:before`
- `button`: `body #page-container .et_pb_section ul{{baseSelector}} .follow_button`

---

### SVG
`divi/svg`

**Elements**: `module`, `svg`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `svg` | svg.svg.settings | — |

- `svg.advanced.fill` — element-specific advanced field
- `svg.advanced.stroke` — element-specific advanced field
- CSS `!important` on `module.spacing.margin-bottom`

**CSS selectors**:
- `svg`: `{{selector}} .et_pb_svg_inner svg`

---

### Table of Contents
`divi/table-of-contents`

**Elements**: `emptyState`, `list`, `list1`, `list2`, `list3`, `list4`, `list5`, `list6`, `marker`, `module`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |
| `list` | list.list.settings | — |
| `emptyState` | Empty State Message | content |

- `list.advanced.interaction` — element-specific advanced field

**CSS selectors**:
- `title`: `{{selector}} .et_pb_table_of_contents__title`
- `list`: `{{selector}} .et_pb_table_of_contents__nav .et_pb_table_of_contents__link`
- `list1`: `{{selector}} .et_pb_table_of_contents__list--level-1 > .et_pb_table_of_contents__link`
- `list2`: `{{selector}} .et_pb_table_of_contents__list--level-2 > .et_pb_table_of_contents__link`
- `list3`: `{{selector}} .et_pb_table_of_contents__list--level-3 > .et_pb_table_of_contents__link`
- `list4`: `{{selector}} .et_pb_table_of_contents__list--level-4 > .et_pb_table_of_contents__link`
- `list5`: `{{selector}} .et_pb_table_of_contents__list--level-5 > .et_pb_table_of_contents__link`
- `list6`: `{{selector}} .et_pb_table_of_contents__list--level-6 > .et_pb_table_of_contents__link`
- `marker`: `{{selector}} .et_pb_table_of_contents__marker`
- `emptyState`: `{{selector}} .et_pb_table_of_contents__empty`

---

### Tabs
`divi/tabs`

**Elements**: `activeTab`, `content`, `module`, `tab`

- Font Family A (bodyFont) on `content`
- `content.decoration` limited to: background
- `tab.decoration` limited to: background
- `activeTab.decoration` limited to: background
- VB-hidden: `tab.decoration.font.textAlign` (functional via block JSON)
- VB-hidden: `activeTab.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `activeTab.font.color`
- CSS `!important` on `content.bodyFont.body.font.color`
- CSS `!important` on `module.spacing.margin`
- CSS `!important` on `tab.font.color`

**CSS selectors**:
- `content`: `{{selector}} > .et_pb_all_tabs`
- `tab`: `{{selector}} .et_pb_tabs_controls li, {{selector}} .et_pb_tabs_controls li a`
- `activeTab`: `{{selector}} .et_pb_tabs_controls li.et_pb_tab_active, {{selector}} .et_pb_tabs_controls li.et_pb_tab_active a`

---

### Person
`divi/team-member`

**Elements**: `content`, `image`, `module`, `name`, `position`, `social`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `name` | Name | content |
| `position` | Position | content |
| `image` | image.image.settings | — |
| `content` | Body | — |
| `social` | social.social.settings | — |

- Font Family A (bodyFont) on `content`
- VB-hidden: `social.decoration.icon.icon` (functional via block JSON)
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing

**CSS selectors**:
- `name`: `{{selector}}.et_pb_team_member h4, {{selector}}.et_pb_team_member h1.et_pb_module_header, {{selector}}.et_pb_team_member...`
- `position`: `{{selector}}.et_pb_team_member .et_pb_member_position`
- `image`: `{{selector}} .et_pb_team_member_image img`
- `content`: `{{selector}} .et_pb_team_member_description_content > div`
- `social`: `{{selector}} .et_pb_member_social_links a`

---

### Testimonial
`divi/testimonial`

**Elements**: `author`, `company`, `content`, `jobTitle`, `module`, `portrait`, `quoteIcon`, `testimonialDescription`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `company` | company.company.settings | — |
| `content` | Body | content |
| `author` | Author | content |
| `jobTitle` | Job Title | content |
| `portrait` | Image | content |

- `quoteIcon.decoration` limited to: background
- Font Family A (bodyFont) on `content`
- VB-hidden: `company.decoration.font.textAlign` (functional via block JSON)
- VB-hidden: `content.decoration.bodyFont.body.textShadowGroup` (functional via block JSON)
- VB-hidden: `jobTitle.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `module.spacing`
- CSS `!important` on `quoteIcon`: icon.content, icon.font-family, icon.font-weight

**CSS selectors**:
- `company`: `{{selector}}.et_pb_testimonial .et_pb_testimonial_company, {{selector}}.et_pb_testimonial .et_pb_testimonial_company a`
- `quoteIcon`: `{{selector}}.et_pb_testimonial:before`
- `content`: `{{selector}}.et_pb_testimonial .et_pb_testimonial_content`
- `author`: `{{selector}}.et_pb_testimonial .et_pb_testimonial_author`
- `jobTitle`: `{{selector}}.et_pb_testimonial .et_pb_testimonial_position, {{selector}}.et_pb_testimonial .et_pb_testimonial_separator`
- `portrait`: `{{selector}} .et_pb_testimonial_portrait`
- `testimonialDescription`: `{{selector}} .et_pb_testimonial_description`

---

### Text
`divi/text`

**Elements**: `content`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Body | content |

- Font Family A (bodyFont) on `content`
- VB-hidden: `content.decoration.bodyFont.body.textAlign` (functional via block JSON)
- CSS `!important` on `content`: bodyFont.body.font.color, headingFont.h1.font.color, headingFont.h2.font.color, headingFont.h3.font.color, headingFont.h4.font.color, headingFont.h5.font.color, headingFont.h6.font.color
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing

**CSS selectors**:
- `content`: `{{selector}} .et_pb_text_inner`

---

### Timeline
`divi/timeline`

**Elements**: `card`, `cardEven`, `children`, `connector`, `content`, `contentEven`, `date`, `dateEven`, `item`, `itemEven`, `marker`, `module`, `spacer`, `spacerEven`, `title`, `titleEven`, `track`

- `children.advanced.spacer` — element-specific advanced field
- `marker.advanced.position` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- Font Family A (bodyFont) on `contentEven`
- VB-hidden: `track.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `track.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `track.decoration.sizing.alignSelf` (functional via block JSON)
- VB-hidden: `track.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `track.decoration.sizing.size` (functional via block JSON)
- VB-hidden: `decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `itemEven.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `itemEven.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `itemEven.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `spacer.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `spacer.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `spacer.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `spacerEven.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `spacerEven.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `spacerEven.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `connector.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `connector.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `connector.decoration.sizing.alignSelf` (functional via block JSON)
- VB-hidden: `connector.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `connector.decoration.sizing.size` (functional via block JSON)
- VB-hidden: `marker.decoration.icon.icon` (functional via block JSON)
- VB-hidden: `marker.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `marker.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `marker.decoration.sizing.alignSelf` (functional via block JSON)
- VB-hidden: `marker.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `card.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `card.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `card.decoration.sizing.alignSelf` (functional via block JSON)
- VB-hidden: `card.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `card.decoration.sizing.size` (functional via block JSON)
- VB-hidden: `cardEven.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `cardEven.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `cardEven.decoration.sizing.alignSelf` (functional via block JSON)
- VB-hidden: `cardEven.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `cardEven.decoration.sizing.size` (functional via block JSON)
- VB-hidden: `dateEven.decoration.font.headingLevel` (functional via block JSON)
- VB-hidden: `titleEven.decoration.font.headingLevel` (functional via block JSON)

**CSS selectors**:
- `track`: `{{selector}} .et_pb_timeline_track`
- `item`: `{{selector}} .et_pb_timeline_item`
- `itemEven`: `{{selector}} :where(.et_pb_timeline_track) > :nth-child(even).et_pb_timeline_item`
- `spacer`: `{{selector}} .et_pb_timeline_spacer`
- `spacerEven`: `{{selector}} :where(.et_pb_timeline_track) > :nth-child(even) .et_pb_timeline_spacer`
- `connector`: `{{selector}} .et_pb_timeline_connector`
- `marker`: `{{selector}} .et_pb_timeline_marker`
- `card`: `{{selector}} .et_pb_timeline_card`
- `cardEven`: `{{selector}} :where(.et_pb_timeline_track) > :nth-child(even) .et_pb_timeline_card`
- `date`: `{{selector}} .et_pb_timeline_date`
- `dateEven`: `{{selector}} :where(.et_pb_timeline_track) > :nth-child(even) .et_pb_timeline_date`
- `title`: `{{selector}} h1.et_pb_timeline_title, {{selector}} h2.et_pb_timeline_title, {{selector}} h3.et_pb_timeline_title, {{sele...`
- `titleEven`: `{{selector}} :where(.et_pb_timeline_track) > :nth-child(even) h1.et_pb_timeline_title, {{selector}} :where(.et_pb_timeli...`
- `content`: `{{selector}} .et_pb_timeline_content`
- `contentEven`: `{{selector}} :where(.et_pb_timeline_track) > :nth-child(even) .et_pb_timeline_content`

---

### Toggle
`divi/toggle`

**Elements**: `closedTitle`, `closedToggle`, `closedToggleIcon`, `content`, `module`, `openToggle`, `openToggleIcon`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Content | content |

- Font Family A (bodyFont) on `content`
- `openToggle.decoration` limited to: background
- `closedToggle.decoration` limited to: background
- VB-hidden: `title.decoration.font.fontGroup.color` (functional via block JSON)
- VB-hidden: `closedTitle.decoration.font.headingLevel` (functional via block JSON)
- CSS `!important` on `closedToggleIcon`: icon.content, icon.font-family, icon.font-weight
- CSS `!important` on `module.spacing`
- CSS `!important` on `openToggleIcon`: icon.content, icon.font-family, icon.font-weight

**CSS selectors**:
- `openToggleIcon`: `{{selector}}.et_pb_toggle_open > .et_pb_toggle_title:before`
- `closedToggleIcon`: `{{selector}}.et_pb_toggle_close > .et_pb_toggle_title:before`
- `title`: `{{selector}}.et_pb_toggle > h5, {{selector}}.et_pb_toggle > h1.et_pb_toggle_title, {{selector}}.et_pb_toggle > h2.et_pb_...`
- `closedTitle`: `{{selector}}.et_pb_toggle.et_pb_toggle_close > h5, {{selector}}.et_pb_toggle.et_pb_toggle_close > h1.et_pb_toggle_title,...`
- `content`: `{{selector}} .et_pb_toggle_content`
- `openToggle`: `{{selector}}.et_pb_toggle.et_pb_toggle_open`
- `closedToggle`: `{{selector}}.et_pb_toggle.et_pb_toggle_close`

---

### Tooltip
`divi/tooltip`

**Elements**: `content`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Body | content |

- Font Family A (bodyFont) on `content`
- CSS `!important` on `module.spacing`

**CSS selectors**:
- `content`: `{{selector}} .et_pb_tooltip_inner`

---

### Video
`divi/video`

**Elements**: `module`, `overlay`, `playIcon`, `video`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `overlay` | overlay.overlay.settings | — |
| `video` | video.video.settings | — |

- `overlay.decoration` limited to: background
- CSS `!important` on `module`: sizing.height, sizing.margin-left, sizing.margin-right, sizing.max-height, sizing.min-height, sizing.width, spacing.margin
- CSS `!important` on `playIcon`: icon.content, icon.font-family, icon.font-weight

**CSS selectors**:
- `overlay`: `{{selector}} .et_pb_video_overlay_hover:hover`
- `video`: `{{selector}} .et_pb_video`
- `playIcon`: `{{selector}} .et_pb_video_overlay .et_pb_video_play`

---

### Video Slider
`divi/video-slider`

**Elements**: `module`, `overlay`, `playIcon`, `sliderControls`, `video`

- `video.decoration` limited to: border, boxShadow
- `overlay.advanced.showImageOverlay` — element-specific advanced field
- `playIcon.decoration` limited to: background
- `sliderControls.advanced.color` — element-specific advanced field
- `sliderControls.advanced.useArrows` — element-specific advanced field
- `sliderControls.advanced.useThumbnails` — element-specific advanced field
- CSS `!important` on `module`: sizing.height, sizing.margin-left, sizing.margin-right, sizing.max-height, sizing.min-height, sizing.width, spacing.margin
- CSS `!important` on `playIcon`: icon.color, icon.content, icon.font-family, icon.font-weight

**CSS selectors**:
- `overlay`: `{{selector}} .et_pb_video_overlay_hover:hover`
- `playIcon`: `{{selector}} .et_pb_video_overlay .et_pb_video_play`

---

## Fullwidth Modules (10)

### Fullwidth Code
`divi/fullwidth-code`

**Elements**: `content`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Code | content |

- CSS `!important` on `module.sizing`

**CSS selectors**:
- `content`: `{{selector}} .et_pb_code_inner`

---

### Hero
`divi/fullwidth-header`

**Elements**: `buttonOne`, `buttonTwo`, `content`, `image`, `logo`, `module`, `overlay`, `scrollDown`, `subhead`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `image` | image.image.settings | — |
| `logo` | logo.logo.settings | — |
| `title` | Title | content |
| `subhead` | Subtitle | content |
| `content` | Body | content |
| `buttonOne` | buttonOne.buttonOne.settings | — |
| `buttonTwo` | buttonTwo.buttonTwo.settings | — |

- `image.advanced.orientation` — element-specific advanced field
- `content.advanced.orientation` — element-specific advanced field
- `content.advanced.maxWidth` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- `overlay.decoration` limited to: background
- VB-hidden: `buttonOne.decoration.button.sizingGroup.alignment` (functional via block JSON)
- VB-hidden: `buttonOne.decoration.button.sizingGroup.alignSelf` (functional via block JSON)
- VB-hidden: `buttonOne.decoration.button.sizingGroup.gridAlignSelf` (functional via block JSON)
- VB-hidden: `buttonOne.decoration.button.sizingGroup.gridJustifySelf` (functional via block JSON)
- VB-hidden: `buttonTwo.decoration.button.sizingGroup.alignment` (functional via block JSON)
- VB-hidden: `buttonTwo.decoration.button.sizingGroup.alignSelf` (functional via block JSON)
- VB-hidden: `buttonTwo.decoration.button.sizingGroup.gridAlignSelf` (functional via block JSON)
- VB-hidden: `buttonTwo.decoration.button.sizingGroup.gridJustifySelf` (functional via block JSON)
- CSS `!important` on `buttonOne.border`
- CSS `!important` on `buttonOne.spacing`
- CSS `!important` on `buttonTwo.border`
- CSS `!important` on `buttonTwo.spacing`
- CSS `!important` on `content.bodyFont.body.font.color`
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `image`: `{{selector}} .header-logo, {{selector}} .header-image-container img`
- `logo`: `{{selector}} .header-logo`
- `title`: `{{selector}}.et_pb_fullwidth_header .header-content h1, {{selector}}.et_pb_fullwidth_header .header-content h2.et_pb_mod...`
- `subhead`: `{{selector}}.et_pb_fullwidth_header .et_pb_fullwidth_header_subhead`
- `content`: `{{selector}}.et_pb_fullwidth_header .et_pb_header_content_wrapper`
- `buttonOne`: `{{selector}} .et_pb_button_one.et_pb_button`
- `buttonTwo`: `{{selector}} .et_pb_button_two.et_pb_button`
- `scrollDown`: `{{selector}}.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a .et-pb-icon`
- `overlay`: `{{selector}}.et_pb_fullwidth_header .et_pb_fullwidth_header_overlay`

---

### Fullwidth Image
`divi/fullwidth-image`

**Elements**: `image`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `image` | image.image.settings | — |

- `image.advanced.lightbox` — element-specific advanced field
- `image.advanced.overlay` — element-specific advanced field
- `image.advanced.overlayIcon` — element-specific advanced field
- CSS `!important` on `module.spacing`

**CSS selectors**:
- `image`: `{{selector}} img`

---

### Fullwidth Map
`divi/fullwidth-map`

**Elements**: `map`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `map` | Map Center Address | content |

- `map.advanced.googleAPIKey` — element-specific advanced field
- `map.advanced.mouseWheel` — element-specific advanced field
- `map.advanced.mobileDragging` — element-specific advanced field
- `map.decoration` limited to: filters
- CSS `!important` on `module.spacing.margin`

**CSS selectors**:
- `map`: `{{selector}} .gm-style>div>div>div>div>div>img`

---

### Fullwidth Menu
`divi/fullwidth-menu`

**Elements**: `cartIcon`, `cartQuantity`, `hamburgerMenuIcon`, `logo`, `menu`, `menuDropdown`, `menuMobile`, `module`, `searchIcon`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `logo` | logo.logo.settings | — |

- `menu.advanced.menuId` — element-specific advanced field
- `menu.advanced.style` — element-specific advanced field
- `menu.advanced.activeLinkColor` — element-specific advanced field
- `menu.advanced.fullwidth` — element-specific advanced field
- `menuDropdown.advanced.direction` — element-specific advanced field
- `menuDropdown.advanced.animation` — element-specific advanced field
- `menuDropdown.advanced.lineColor` — element-specific advanced field
- `menuDropdown.advanced.activeLinkColor` — element-specific advanced field
- `menuDropdown.decoration` limited to: background
- `menuMobile.decoration` limited to: background
- `cartQuantity.advanced.show` — element-specific advanced field
- `cartIcon.advanced.show` — element-specific advanced field
- `searchIcon.advanced.show` — element-specific advanced field
- VB-hidden: `logo.decoration.sizing.alignment` (functional via block JSON)
- VB-hidden: `logo.decoration.sizing.minHeight` (functional via block JSON)
- VB-hidden: `menu.decoration.font.textAlign` (functional via block JSON)
- VB-hidden: `cartQuantity.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `menuDropdown.background`
- CSS `!important` on `menuDropdown.font.color`
- CSS `!important` on `menuMobile.background`
- CSS `!important` on `menuMobile.font`
- CSS `!important` on `module.spacing.margin`

**CSS selectors**:
- `module`: `{{selector}}.et_pb_fullwidth_menu`
- `logo`: `{{selector}} .et_pb_menu__logo-wrap img`

---

### Post Carousel
`divi/fullwidth-portfolio`

**Elements**: `image`, `meta`, `module`, `overlay`, `portfolio`, `portfolioGrid`, `portfolioItemTitle`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `portfolio` | portfolio.portfolio.settings | — |
| `title` | Carousel Title | content |

- `portfolio.advanced.layout` — element-specific advanced field
- `portfolio.advanced.showTitle` — element-specific advanced field
- `portfolio.advanced.showDate` — element-specific advanced field
- `overlay.decoration` limited to: background
- VB-hidden: `overlay.decoration.icon.icon.color` (functional via block JSON)
- VB-hidden: `overlay.decoration.icon.icon.useSize` (functional via block JSON)
- CSS `!important` on `meta.font.color`
- CSS `!important` on `overlay`: icon.color, icon.content, icon.font-family, icon.font-weight
- CSS `!important` on `portfolio.font`
- CSS `!important` on `title.font`

**CSS selectors**:
- `title`: `{{selector}} .et_pb_portfolio_title`
- `overlay`: `{{selector}} .et_pb_portfolio_item .et_overlay`
- `image`: `{{selector}} .et_pb_portfolio_image`
- `meta`: `{{selector}} .post-meta, {{selector}} .post-meta a`
- `portfolioGrid`: `{{selector}} .et_pb_portfolio_items`
- `portfolioItemTitle`: `{{selector}} .et_pb_module_header`

---

### Fullwidth Post Content
`divi/fullwidth-post-content`

**Elements**: `image`, `module`

- `image.decoration` limited to: border, boxShadow

**CSS selectors**:
- `image`: `{{selector}} img`

---

### Fullwidth Post Slider
`divi/fullwidth-post-slider`

**Elements**: `arrows`, `button`, `content`, `contentOverlay`, `image`, `meta`, `module`, `pagination`, `post`, `slideOverlay`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `button` | Button | content |

- `content.advanced.showOnMobile` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- `content.decoration` limited to: sizing
- `meta.advanced.enable` — element-specific advanced field
- `image.advanced.enable` — element-specific advanced field
- `image.advanced.placement` — element-specific advanced field
- `image.advanced.showOnMobile` — element-specific advanced field
- `image.decoration` limited to: border, boxShadow, filters
- `button.advanced.enable` — element-specific advanced field
- `button.advanced.showOnMobile` — element-specific advanced field
- `post.advanced.number` — element-specific advanced field
- `post.advanced.categories` — element-specific advanced field
- `post.advanced.orderby` — element-specific advanced field
- `post.advanced.contentSource` — element-specific advanced field
- `post.advanced.excerptManual` — element-specific advanced field
- `post.advanced.excerptLength` — element-specific advanced field
- `post.advanced.offset` — element-specific advanced field
- `arrows.advanced.color` — element-specific advanced field
- `arrows.advanced.enable` — element-specific advanced field
- `pagination.advanced.enable` — element-specific advanced field
- `pagination.decoration` limited to: background
- `slideOverlay.advanced.use` — element-specific advanced field
- `slideOverlay.decoration` limited to: background
- `contentOverlay.advanced.use` — element-specific advanced field
- `contentOverlay.decoration` limited to: background, border
- VB-hidden: `content.decoration.sizing.alignment` (functional via block JSON)
- VB-hidden: `content.decoration.sizing.height` (functional via block JSON)
- VB-hidden: `content.decoration.sizing.maxHeight` (functional via block JSON)
- VB-hidden: `content.decoration.sizing.minHeight` (functional via block JSON)
- VB-hidden: `button.innerContent.link` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.lineHeight` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.styles` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.stylesTabNav` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.stylesTabbedWidth` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.stylesTabbedColor` (functional via block JSON)
- VB-hidden: `contentOverlay.decoration.border.stylesTabbedStyle` (functional via block JSON)
- CSS `!important` on `button.spacing`
- CSS `!important` on `content.bodyFont`
- CSS `!important` on `meta.font`
- CSS `!important` on `title.font.color`
- CSS `!important` on `title.font.font-size`

**CSS selectors**:
- `title`: `{{selector}}.et_pb_slider .et_pb_slide_description .et_pb_slide_title, {{selector}}.et_pb_slider .et_pb_slide_descriptio...`
- `content`: `{{selector}}.et_pb_slider .et_pb_slide_content, {{selector}}.et_pb_slider .et_pb_slide_content div`
- `meta`: `{{selector}}.et_pb_slider .et_pb_slide_content .post-meta, {{selector}}.et_pb_slider .et_pb_slide_content .post-meta a`
- `image`: `{{selector}} .et_pb_slide_image img`
- `button`: `{{selector}}.et_pb_slider .et_pb_more_button.et_pb_button`
- `arrows`: `{{selector}} .et-pb-slider-arrows .et-pb-arrow-prev, {{selector}} .et-pb-slider-arrows .et-pb-arrow-next`
- `pagination`: `{{selector}} .et-pb-controllers a, {{selector}} .et-pb-controllers .et-pb-active-control`
- `slideOverlay`: `{{selector}} .et_pb_slide .et_pb_slide_overlay_container`
- `contentOverlay`: `{{selector}} .et_pb_slide .et_pb_text_overlay_wrapper`

---

### Fullwidth Post Title
`divi/fullwidth-post-title`

**Elements**: `featuredImage`, `meta`, `module`, `textWrapper`, `title`

- `title.advanced.showTitle` — element-specific advanced field
- `meta.advanced.showMeta` — element-specific advanced field
- `meta.advanced.showAuthor` — element-specific advanced field
- `meta.advanced.showDate` — element-specific advanced field
- `meta.advanced.dateFormat` — element-specific advanced field
- `meta.advanced.showCategories` — element-specific advanced field
- `meta.advanced.showCommentsCount` — element-specific advanced field
- `textWrapper.advanced.useBackground` — element-specific advanced field
- `textWrapper.decoration` limited to: background
- `featuredImage.advanced.enabled` — element-specific advanced field
- `featuredImage.advanced.forceFullwidth` — element-specific advanced field
- `featuredImage.advanced.placement` — element-specific advanced field
- `featuredImage.decoration` limited to: sizing
- CSS `!important` on `meta.font`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing
- CSS `!important` on `title.font`

**CSS selectors**:
- `title`: `{{selector}} .et_pb_title_container h1.entry-title,{{selector}} .et_pb_title_container h2.entry-title,{{selector}} .et_p...`
- `meta`: `{{selector}} .et_pb_title_container .et_pb_title_meta_container, {{selector}} .et_pb_title_container .et_pb_title_meta_c...`
- `textWrapper`: `{{selector}} .et_pb_title_container`
- `featuredImage`: `{{selector}} .et_pb_title_featured_container img`

---

### Fullwidth Slider
`divi/fullwidth-slider`

**Elements**: `arrows`, `button`, `children`, `content`, `dotNav`, `image`, `module`, `pagination`, `title`

- `arrows.advanced.color` — element-specific advanced field
- `arrows.advanced.show` — element-specific advanced field
- `pagination.advanced.show` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- `content.decoration` limited to: sizing
- `children.advanced.slideOverlay` — element-specific advanced field
- `children.advanced.contentOverlay` — element-specific advanced field
- `children.advanced.content` — element-specific advanced field
- `children.advanced.button` — element-specific advanced field
- `children.decoration` limited to: background, border
- `image.advanced.showOnMobile` — element-specific advanced field
- `image.decoration` limited to: border, boxShadow
- `dotNav.decoration` limited to: background
- VB-hidden: `button.decoration.button.fontGroup.lineHeight` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- VB-hidden: `button.decoration.button.borderGroup.styles` (functional via block JSON)
- CSS `!important` on `button.spacing`
- CSS `!important` on `module`: sizing.margin-left, sizing.margin-right, spacing.margin
- CSS `!important` on `title.font.color`
- CSS `!important` on `title.font.font-size`

**CSS selectors**:
- `arrows`: `{{selector}} .et-pb-slider-arrows .et-pb-arrow-prev, {{selector}} .et-pb-slider-arrows .et-pb-arrow-next`
- `title`: `{{selector}}.et_pb_slider .et_pb_slide_description .et_pb_slide_title`
- `button`: `{{selector}} .et_pb_more_button.et_pb_button`
- `content`: `{{selector}}.et_pb_slider .et_pb_slide_content`
- `image`: `{{selector}} .et_pb_slide_image img`
- `dotNav`: `{{selector}} .et-pb-controllers a, {{selector}} .et-pb-controllers .et-pb-active-control`

---

## Child Modules (13)

### Accordion Item
`divi/accordion-item`

**Elements**: `closedToggle`, `closedToggleIcon`, `content`, `module`, `openToggle`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |
| `content` | Body | content |

- `openToggle.decoration` limited to: background
- `closedToggle.decoration` limited to: background
- Font Family A (bodyFont) on `content`
- VB-hidden: `closedToggleIcon.decoration.icon.iconAttributes.icon` (functional via block JSON)
- VB-hidden: `closedToggle.decoration.font.font.color` (functional via block JSON)
- CSS `!important` on `closedToggleIcon`: icon.content, icon.font-family, icon.font-size, icon.font-weight
- CSS `!important` on `content.bodyFont.body.font.color`
- CSS `!important` on `module.boxShadow`
- CSS `!important` on `module.spacing`
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `title`: `{{selector}} > .et_pb_toggle_title`
- `closedToggleIcon`: `{{selector}}.et_pb_toggle_close:not(.et_pb_toggle_empty) > .et_pb_toggle_title:before`
- `openToggle`: `{{selector}}.et_pb_accordion_item.et_pb_toggle.et_pb_toggle_open`
- `closedToggle`: `{{selector}}.et_pb_accordion_item.et_pb_toggle.et_pb_toggle_close`
- `content`: `{{selector}}.et_pb_toggle .et_pb_toggle_content`

---

### Field
`divi/contact-field`

**Elements**: `checkbox`, `conditionalLogic`, `field`, `fieldItem`, `fieldTitle`, `module`, `radio`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `fieldItem` | Title | content |
| `conditionalLogic` | Rules | content |

- `fieldItem.advanced.id` — element-specific advanced field
- `fieldItem.advanced.type` — element-specific advanced field
- `fieldItem.advanced.minLength` — element-specific advanced field
- `fieldItem.advanced.maxLength` — element-specific advanced field
- `fieldItem.advanced.allowedSymbols` — element-specific advanced field
- `fieldItem.advanced.checkboxOptions` — element-specific advanced field
- `fieldItem.advanced.radioOptions` — element-specific advanced field
- `fieldItem.advanced.selectOptions` — element-specific advanced field
- `fieldItem.advanced.required` — element-specific advanced field
- `conditionalLogic.advanced.enable` — element-specific advanced field
- `conditionalLogic.advanced.relation` — element-specific advanced field
- CSS `!important` on `fieldTitle.font.color`
- CSS `!important` on `module.boxShadow`
- CSS `!important` on `module.spacing.margin`

**CSS selectors**:
- `field`: `{{selector}}.et_pb_contact_field .input:not([type=checkbox]):not([type=radio])`
- `checkbox`: `{{selector}} [type=checkbox]`
- `radio`: `{{selector}} [type=radio]`
- `fieldTitle`: `{{selector}}.et_pb_contact_field .et_pb_contact_field_options_title`
- `fieldItem`: `{{selector}} .et_pb_contact_form_label`

---

### Bar Counter
`divi/counter`

**Elements**: `barCounter`, `barProgress`, `module`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `title` | Title | content |
| `barProgress` | Percent | content |

- `barProgress.decoration` limited to: background
- VB-hidden: `title.decoration.font.headingLevel` (functional via block JSON)
- CSS `!important` on `barCounter.background`
- CSS `!important` on `barProgress.font.color`
- CSS `!important` on `barProgress.font.text-align`
- CSS `!important` on `title.font.color`

**CSS selectors**:
- `barCounter`: `{{selectorPrefix}}.et_pb_counters li{{baseSelector}} .et_pb_counter_container`
- `title`: `{{selectorPrefix}}.et_pb_counters {{baseSelector}} .et_pb_counter_title`
- `barProgress`: `{{selectorPrefix}}.et_pb_counters {{baseSelector}} .et_pb_counter_amount_number`

---

### Icon List Item
`divi/icon-list-item`

**Elements**: `content`, `icon`, `module`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Text | content |
| `icon` | Icon | content |

- `icon.advanced.color` — element-specific advanced field
- `icon.advanced.size` — element-specific advanced field
- CSS `!important` on `content.font.color`
- CSS `!important` on `module.layout`
- CSS `!important` on `module.spacing`

**CSS selectors**:
- `content`: `{{selector}}.et_pb_icon_list_item .et_pb_icon_list_text`
- `icon`: `{{selector}}.et_pb_icon_list_item .et-pb-icon`

---

### Map Pin
`divi/map-pin`

**Elements**: `content`, `pin`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `pin` | Map Pin Address | content |
| `title` | Title | content |
| `content` | Body | ['style'] |

- `pin.advanced.html` — element-specific advanced field
- `pin.advanced.loop` — element-specific advanced field

**CSS selectors**:
- `title`: `{{selector}} h3`
- `content`: `{{selector}} .infowindow`

---

### Post Filter Item
`divi/post-filter-item`

**Elements**: `button`, `checkbox`, `field`, `label`, `module`, `multipleOrderButton`, `option`, `radio`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `field` | field.field.settings | — |
| `label` | Field Label | — |

- `field.advanced.orderbyEnabledOptions` — element-specific advanced field
- `field.advanced.checkboxOptions` — element-specific advanced field
- `field.advanced.radioOptions` — element-specific advanced field
- `field.advanced.selectOptions` — element-specific advanced field
- VB-hidden: `option.decoration.background.image.parallaxEnabled` (functional via block JSON)
- VB-hidden: `option.decoration.background.image.parallaxMethod` (functional via block JSON)
- VB-hidden: `multipleOrderButton.decoration.button.buttonIconGroup` (functional via block JSON)
- VB-hidden: `button.decoration.button.buttonIconGroup` (functional via block JSON)
- CSS `!important` on `button.spacing.margin`
- CSS `!important` on `button.spacing.padding`

**CSS selectors**:
- `field`: `.et_pb_post_filter {{selector}} .et_pb_post_filter__item-control-surface .et_pb_post_filter__item-control:not([type=chec...`
- `option`: `.et_pb_post_filter {{selector}} .et_pb_post_filter__item-option`
- `checkbox`: `{{selector}} .et_pb_post_filter__item-control[type=checkbox]`
- `radio`: `{{selector}} .et_pb_post_filter__item-control[type=radio]`
- `multipleOrderButton`: `{{selector}} .et_pb_post_filter__item-multiple-order-action`
- `button`: `{{selector}} .et_pb_post_filter__item-control-button`
- `label`: `{{selector}} .et_pb_post_filter__item-label`

---

### Pricing Table
`divi/pricing-table`

**Elements**: `button`, `content`, `currencyFrequency`, `excluded`, `module`, `price`, `subtitle`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `currencyFrequency` | currencyFrequency.currencyFrequency.settings | — |
| `subtitle` | Subtitle | content |
| `title` | Title | content |
| `price` | Price | content |
| `button` | Button | — |
| `content` | Body | — |

- `title.decoration` limited to: background
- `price.decoration` limited to: background, border
- `content.advanced.bulletColor` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- VB-hidden: `currencyFrequency.decoration.font.headingLevel` (functional via block JSON)
- VB-hidden: `subtitle.decoration.font.headingLevel` (functional via block JSON)
- CSS `!important` on `button`: border.border-width, font, spacing.padding
- CSS `!important` on `currencyFrequency.font.color`
- CSS `!important` on `currencyFrequency.spacing.margin-left`
- CSS `!important` on `module.spacing`
- CSS `!important` on `price.font.color`
- CSS `!important` on `subtitle.font.color`
- CSS `!important` on `title.font`

**CSS selectors**:
- `currencyFrequency`: `{{selector}} .et_pb_frequency`
- `subtitle`: `{{selector}} .et_pb_best_value`
- `title`: `{{selector}} .et_pb_pricing_heading h2,{{selector}} .et_pb_pricing_heading h1.et_pb_pricing_title,{{selector}} .et_pb_pr...`
- `price`: `{{selector}} .et_pb_et_price .et_pb_sum`
- `button`: `{{selector}} .et_pb_button`
- `excluded`: `{{selector}} ul.et_pb_pricing li.et_pb_not_available,{{selector}} ul.et_pb_pricing li.et_pb_not_available span,{{selecto...`
- `content`: `{{selector}} .et_pb_pricing_content`

---

### Custom Field
`divi/signup-custom-field`

**Elements**: `checkbox`, `conditionalLogic`, `field`, `fieldItem`, `module`, `radio`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `fieldItem` | Title | content |
| `conditionalLogic` | Rules | ['html'] |

- `field.decoration` limited to: background
- `fieldItem.advanced.fullwidth` — element-specific advanced field
- `fieldItem.advanced.predefinedField` — element-specific advanced field
- `fieldItem.advanced.id` — element-specific advanced field
- `fieldItem.advanced.type` — element-specific advanced field
- `fieldItem.advanced.minLength` — element-specific advanced field
- `fieldItem.advanced.maxLength` — element-specific advanced field
- `fieldItem.advanced.allowedSymbols` — element-specific advanced field
- `fieldItem.advanced.checkboxOptions` — element-specific advanced field
- `fieldItem.advanced.radioOptions` — element-specific advanced field
- `fieldItem.advanced.selectOptions` — element-specific advanced field
- `fieldItem.advanced.required` — element-specific advanced field
- `fieldItem.advanced.hidden` — element-specific advanced field
- `conditionalLogic.advanced.enable` — element-specific advanced field
- `conditionalLogic.advanced.relation` — element-specific advanced field
- CSS `!important` on `module.boxShadow`
- CSS `!important` on `module.spacing.margin`

**CSS selectors**:
- `field`: `{{selector}}.et_pb_contact_field .input:not([type=checkbox]):not([type=radio])`
- `checkbox`: `{{selector}} [type=checkbox]`
- `radio`: `{{selector}} [type=radio]`
- `fieldItem`: `{{selector}} .et_pb_contact_form_label`

---

### Slide
`divi/slide`

**Elements**: `arrows`, `button`, `content`, `contentOverlay`, `dotNav`, `image`, `module`, `slideOverlay`, `title`, `video`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `image` | image.image.settings | — |
| `title` | Title | content |
| `button` | button.button.settings | — |
| `content` | Body | content |
| `video` | Video | content |

- `image.advanced.alignment` — element-specific advanced field
- `contentOverlay.advanced.useTextOverlay` — element-specific advanced field
- `contentOverlay.decoration` limited to: background, border
- Font Family A (bodyFont) on `content`
- `slideOverlay.advanced.useBackgroundOverlay` — element-specific advanced field
- `slideOverlay.decoration` limited to: background
- `arrows.advanced.color` — element-specific advanced field
- `dotNav.advanced.color` — element-specific advanced field
- VB-hidden: `button.decoration.button.fontGroup.lineHeight` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- VB-hidden: `button.decoration.button.borderGroup.styles` (functional via block JSON)
- CSS `!important` on `button.font.color`
- CSS `!important` on `button.spacing`
- CSS `!important` on `module.spacing.padding`
- CSS `!important` on `title`: font.color, font.font-size, font.font-weight, font.letter-spacing, font.line-height, font.text-align

**CSS selectors**:
- `image`: `{{selector}}.et_pb_slide .et_pb_slide_image img`
- `title`: `{{selectorPrefix}}.et_pb_slider {{baseSelector}}.et_pb_slide .et_pb_slide_description .et_pb_slide_title`
- `contentOverlay`: `{{selector}}.et_pb_slide .et_pb_text_overlay_wrapper`
- `button`: `body #page-container {{selector}}.et_pb_slide .et_pb_more_button.et_pb_button`
- `content`: `{{selectorPrefix}}.et_pb_slider.et_pb_module {{baseSelector}}.et_pb_slide .et_pb_slide_description .et_pb_slide_content`
- `slideOverlay`: `{{selector}}.et_pb_slide .et_pb_slide_overlay_container`

---

### Social Network
`divi/social-media-follow-network`

**Elements**: `button`, `icon`, `iconLink`, `module`, `socialNetwork`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `socialNetwork` | socialNetwork.socialNetwork.settings | — |

- `icon.advanced.color` — element-specific advanced field
- `icon.advanced.size` — element-specific advanced field
- VB-hidden: `button.decoration.button.alignment` (functional via block JSON)
- VB-hidden: `button.decoration.button.buttonIconGroup` (functional via block JSON)
- VB-hidden: `button.decoration.button.fontGroup.textAlign` (functional via block JSON)
- CSS `!important` on `module`: background, boxShadow, spacing.margin

**CSS selectors**:
- `icon`: `{{selectorPrefix}}.et_pb_social_media_follow {{baseSelector}} .icon, {{selectorPrefix}}.et_pb_social_media_follow {{base...`
- `iconLink`: `{{selector}} a.icon`
- `button`: `body #page-container .et_pb_section .et_pb_social_media_follow li{{baseSelector}} .follow_button`

---

### Tab
`divi/tab`

**Elements**: `content`, `module`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `content` | Body | content |
| `title` | Title | content |

- Font Family A (bodyFont) on `content`
- VB-hidden: `title.decoration.font.textAlign` (functional via block JSON)
- CSS `!important` on `content.bodyFont.body.font.color`
- CSS `!important` on `title`: font.color, font.font-family, font.font-size, font.font-weight, font.text-transform

**CSS selectors**:
- `module`: `{{selector}}, .et_pb_tab_nav_item_{{orderId}}`
- `content`: `{{selector}} .et_pb_tab_content`
- `title`: `{{selector}} .et_pb_tab_nav_item_link`

---

### Timeline Item
`divi/timeline-item`

**Elements**: `card`, `connector`, `content`, `date`, `marker`, `module`, `spacer`, `title`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `marker` | Icon | content |
| `date` | Date | content |
| `title` | Title | content |
| `content` | Body | content |

- `spacer.advanced.displayElementsOnSpacer` — element-specific advanced field
- `marker.advanced.position` — element-specific advanced field
- `content.advanced.text` — element-specific advanced field
- Font Family A (bodyFont) on `content`
- VB-hidden: `spacer.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `connector.decoration.sizing.alignSelf` (functional via block JSON)
- VB-hidden: `connector.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `connector.decoration.sizing.size` (functional via block JSON)
- VB-hidden: `marker.decoration.icon.icon` (functional via block JSON)
- VB-hidden: `marker.decoration.sizing.alignSelf` (functional via block JSON)
- VB-hidden: `marker.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `card.decoration.sizing.alignSelf` (functional via block JSON)
- VB-hidden: `card.decoration.sizing.flexType` (functional via block JSON)
- VB-hidden: `card.decoration.sizing.size` (functional via block JSON)
- VB-hidden: `content.advanced.text` (functional via block JSON)

**CSS selectors**:
- `module`: `{{selectorPrefix}}.et_pb_timeline {{baseSelector}}`
- `spacer`: `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_spacer`
- `connector`: `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_connector`
- `marker`: `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_marker`
- `card`: `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_card`
- `date`: `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_date`
- `title`: `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} h1.et_pb_timeline_title, {{selectorPrefix}}.et_pb_timeline {{baseSele...`
- `content`: `{{selectorPrefix}}.et_pb_timeline {{baseSelector}} .et_pb_timeline_content`

---

### Video Slider Item
`divi/video-slider-item`

**Elements**: `module`, `overlay`, `playIcon`, `sliderControls`, `video`

| Element | innerContent | Preset |
|---------|-------------|--------|
| `overlay` | overlay.overlay.settings | — |
| `video` | video.video.settings | — |

- `overlay.decoration` limited to: background
- `sliderControls.advanced.color` — element-specific advanced field
- CSS `!important` on `module.position.position`
- CSS `!important` on `playIcon`: icon.color, icon.content, icon.font-family, icon.font-weight

**CSS selectors**:
- `overlay`: `{{selector}} .et_pb_video_overlay_hover:hover`
- `playIcon`: `{{selector}} .et_pb_video_overlay .et_pb_video_play`
- `video`: `{{selector}} .et_pb_video_wrap .et_pb_video_box`

---

## Unsupported (1)

### Shortcode Module
`divi/shortcode-module`

---

## Other (1)

### 
`divi/layout`

**Elements**: `layoutContent`

---
