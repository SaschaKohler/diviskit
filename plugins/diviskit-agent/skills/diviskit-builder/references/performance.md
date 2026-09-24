# Divi 5 / WordPress Performance Patterns (PageSpeed)

Verified fixes for common Lighthouse/PageSpeed failures on Divi 5 sites.
Baseline evidence: ja-zum-leben.at mobile audit 2026-09 — these changes moved
Performance ~57→73 and removed ~2s of render-blocking.

All snippets live in the **child theme `functions.php`** — never patch Divi core.

---

## 1. Fonts: self-host, preload, and kill Divi's render-blocking Google request

Divi emits a per-page `<link>` to `fonts.googleapis.com/css2?...` (handle
`et-builder-googlefonts-cached-variable`, sometimes `...-cached`). It is the
single largest render-blocking asset (~1.5–2s LCP damage). Divi only inlines
the *standard* request when `divi_google_fonts_inline` is on — the variable
handle has no inline path, so do it in the child theme:

```php
function jzl_inline_divi_variable_fonts( $html, $handle, $href, $media ) {
    if ( ! in_array( $handle, array( 'et-builder-googlefonts-cached-variable', 'et-builder-googlefonts-cached' ), true ) ) {
        return $html;
    }
    $cache_key = 'jzl_gf_var_' . md5( $href );
    $css       = get_transient( $cache_key );
    if ( false === $css ) {
        $response = wp_remote_get( $href, array(
            'timeout'    => 10,
            // Chrome UA is required — otherwise Google serves the legacy
            // (non-variable, non-woff2-only) CSS payload.
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ) );
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return $html; // keep the original <link> as fallback
        }
        $css = wp_remote_retrieve_body( $response );
        if ( empty( $css ) || false === strpos( $css, '@font-face' ) ) {
            return $html;
        }
        set_transient( $cache_key, $css, WEEK_IN_SECONDS );
    }
    return '<style id="et-builder-googlefonts-cached-variable-inline">' . $css . '</style>';
}
add_filter( 'style_loader_tag', 'jzl_inline_divi_variable_fonts', 10, 4 );
```

Notes:
- The inlined CSS still references `fonts.gstatic.com` woff2 files — fine,
  `display=swap` font files aren't render-blocking; only the CSS was.
- For design-system fonts, prefer fully self-hosted woff2 variable fonts in
  `fonts/` + `fonts.css`, and preload the primary ones:

```php
add_action( 'wp_head', function() {
    if ( is_admin() || isset( $_GET['et_fb'] ) ) return;
    $base = get_stylesheet_directory_uri() . '/fonts/';
    echo '<link rel="preload" href="' . esc_url( $base . 'primary.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}, 4 );
```

## 2. Defer all frontend scripts

jQuery and Divi scripts sit render-blocking in `<head>`. One filter defers
everything (deferred scripts keep execution order):

```php
function jzl_defer_scripts( $tag, $handle, $src ) {
    if ( is_admin() || isset( $_GET['et_fb'] ) || strpos( $tag, 'defer' ) !== false || strpos( $tag, 'async' ) !== false ) {
        return $tag;
    }
    return str_replace( '<script ', '<script defer ', $tag );
}
add_filter( 'script_loader_tag', 'jzl_defer_scripts', 10, 3 );
```

Prerequisite: no inline `<script>` in content calls jQuery before
DOMContentLoaded — grep the page for `jQuery(` / `$(` outside of
DOMContentLoaded wrappers before enabling.

## 3. mediaelement: dequeue the *dependency chain*, not just `wp-mediaelement`

WP/Divi enqueue MediaElement.js (2 styles + 4 scripts) on pages without any
media. Trap: **`wp-mediaelement` is never in the queue** — it gets pulled in
as a dependency of `divi-script-library-audio`, which Divi 5's dynamic assets
enqueue whenever a `divi/blog` module exists on the page ("blog posts could
contain audio post formats"). Dequeuing `wp-mediaelement` alone is a no-op.

Also, Divi re-enqueues during rendering at `wp_footer:10`, so dequeue twice:

```php
function jzl_dequeue_mediaelement() {
    if ( is_admin() || isset( $_GET['et_fb'] ) ) return;

    $posts = is_singular() ? array( get_post() ) : $GLOBALS['wp_query']->posts;
    foreach ( (array) $posts as $p ) {
        if ( $p instanceof WP_Post && preg_match( '/(\[audio|\[video|\[playlist|wp:audio|wp:video|wp:playlist|divi\/audio|divi\/video|et_pb_video|wp-video|wp-audio|mejs-)/', $p->post_content ) ) {
            return; // real media on the page — keep the player
        }
    }

    foreach ( array( 'mediaelement', 'mediaelement-core', 'mediaelement-migrate',
                     'mediaelement-vimeo', 'wp-mediaelement', 'mediaelementplayer',
                     'divi-script-library-audio' ) as $handle ) {
        wp_dequeue_style( $handle );
        wp_dequeue_script( $handle );
    }
}
add_action( 'wp_enqueue_scripts', 'jzl_dequeue_mediaelement', 100 );
add_action( 'wp_footer', 'jzl_dequeue_mediaelement', 15 ); // after Divi :10, before print :20
```

## 4. Fix Divi's non-zoomable viewport (a11y)

Divi prints `<meta name="viewport" content="…maximum-scale=1.0, user-scalable=0">`
via `et_add_viewport_meta` on `wp_head` — a Lighthouse a11y failure. The hook
is registered in the *parent* functions.php, which loads after the child —
remove it on a later hook:

```php
function jzl_fix_viewport_meta() {
    remove_action( 'wp_head', 'et_add_viewport_meta' );
    add_action( 'wp_head', 'jzl_accessible_viewport_meta', 1 );
}
add_action( 'after_setup_theme', 'jzl_fix_viewport_meta', 20 );

function jzl_accessible_viewport_meta() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />' . "\n";
}
```

## 5. Consent banner without the Speed Index / CLS penalty

A JS-injected banner paints late and covers the mobile viewport. Pattern that
fixes it (implemented in the reusable `sk-consent` plugin):

- Render the full banner markup **server-side in `wp_footer`** — visible from
  first paint for new visitors, zero layout shift (position:fixed).
- Tiny inline script at `wp_head:0` adds `html.jzl-consent-given` when
  localStorage consent exists → CSS `html.jzl-consent-given #banner { display:none }`
  hides it *before* first paint for returning visitors.
- `consent.js` only binds events to existing markup; it never creates it.

## 6. GTM / GA4 off the critical path

Wrap the GTM bootstrap in `window.addEventListener('load', …)` and inject the
GA4 `<script async>` dynamically inside the same handler. Consent Mode v2
`default: denied` still fires at `wp_head:1`, so compliance is preserved —
only the payload download is deferred.

## 7. Images

- Every `<img>` needs width/height (CLS) — backfill missing WP attachments
  incl. `_wp_attachment_metadata`.
- `loading="lazy"` + `decoding="async"` on below-fold/external images; the LCP
  image gets neither.
- Convert large PNGs to WebP (2.3MB → ~140KB observed) and regenerate sizes.
- `wp_resource_hints` preconnect for external image/API origins.

## 8. "Use efficient cache lifetimes" is a *server* issue

If Lighthouse flags hundreds of KiB: check `curl -sI <asset>` — hosts commonly
serve static files with **no `cache-control`/`expires` at all**. Fix at
nginx/CDN level (e.g. `expires 30d` for `css|js|woff2|webp|png|jpg`), not in
WordPress.

## Typical audit → fix map

| Finding | Fix |
|---|---|
| Render-blocking requests ~2s | §1 fonts inline + §2 defer |
| Legacy/unused JS (mediaelement) | §3 dependency-chain dequeue |
| `user-scalable=no` a11y fail | §4 viewport |
| Banner inflates Speed Index/CLS | §5 SSR banner |
| 3rd-party GTM/GA4 delay | §6 window.load |
| Missing image dimensions → CLS | §7 |
| Efficient cache lifetimes | §8 server headers |
