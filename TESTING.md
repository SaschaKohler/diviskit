# TESTING.md — Beta-readiness test plan (diviskit-agent + diviskit-pro)

Audit basis: diviskit-agent 1.7.2 / diviskit-pro 1.0.3 (2026-10).
The plugin has **no automated test suite yet** — vendokit ships a
wp-phpunit scaffold (`plugins/vendokit/{composer.json,phpunit.xml.dist,tests/}`)
that can be cloned as the starting point. Until then, items below are
manual/scripted checks against the DDEV sites.

Status legend: **[B]** beta-blocker · **[R]** recommended before 1.0 · **[L]** later

---

## 1. Security — authentication & capabilities [B]

All curlable against `https://diviskit-com.ddev.site/wp-json/diviskit/v1`.
Create test users (admin, editor, author, contributor, subscriber) with
Application Passwords.

- [ ] **Unauthenticated sweep** — request every route (GET + POST) without
      credentials → uniform 401/`rest_forbidden` or envelope `forbidden`,
      never a stack trace, never partial data.
- [ ] **Role matrix** — subscriber: nothing; contributor+author:
      `check_read_permission` routes only (edit_posts), no writes;
      editor: read + write (edit_pages), no `manage_options` routes;
      admin: everything.
- [ ] **Per-object checks** — author tries `page/update-content`,
      `module/update`, `section/*`, `seo/metadata` POST, `page/trash`
      on an admin-owned page → `forbidden`. Handlers deferring to
      `check_authenticated_permission` (seo/*, scf/text-value/update,
      menu/get) must enforce `edit_post`/`read_post` inside.
- [ ] **Revoked Application Password** → immediate 401.
- [ ] **Handshake payload** — leaks nothing sensitive: plugin/theme
      versions and capability keys are fine; verify no absolute server
      paths, user list, or option values.
- [ ] **Divi-inactive catch-all** `/(?P<path>.*)` — registered only when
      `et_get_option` is absent; verify it does NOT shadow real routes
      when Divi is active, and still requires auth (no unauthenticated
      plugin-status oracle).

## 2. Security — input validation & injection [B]

- [ ] **Schema enforcement** — malformed args (wrong enum, bad
      `expected_checksum` pattern, missing required) → envelope
      `invalid_input` (via `wrap_rest_framework_validation_errors`),
      not raw `rest_invalid_param`.
- [ ] **AUTHORING_SHAPE_LIMITS boundary** — payloads at 1 MB±1,
      4096 blocks±1, depth 64±1 → clean rejection, no WSOD/OOM.
- [ ] **post_content byte-roundtrip** — write content containing
      `\u003c`, quotes, serialized-looking strings, emoji, `\\` via
      `page/update-content`; `wp post get --field=post_content` must
      byte-match source (known corruption vector — see repo AGENTS.md).
- [ ] **Render XSS** — `validate/blocks` + `/render` with hostile module
      attrs (`"><script>`, `javascript:` URLs, event handlers) → output
      sanitization verified; render output is preview HTML, confirm no
      unfiltered `script`/`iframe` passes through.
- [ ] **theme-options write** — only whitelisted Divi option keys
      accepted; arbitrary `option_name` rejected.
- [ ] **Option/meta writes** — `page/set-meta`, `page/update-meta`:
      serialized blobs, `object`/`array` injection into meta, oversized
      values.
- [ ] **SQL audit** — `variable_delete` fast path + preset/variable
      scanners: confirm every interpolated value runs through
      `$wpdb->prepare` or is a validated int/slug.
- [ ] **Stored payloads** — preset/variable/global-color/font `upsert`
      with serialized PHP payloads (`O:8:"..."`) → `maybe_unserialize`
      must not instantiate objects (object-injection probe).
- [ ] **File writes** — `write_project_agents_md` (ABSPATH/AGENTS.md):
      marker-block replacement preserves foreign content; no writes when
      file unreadable; verify no path influence from any request input.
      Rollback snapshots are options (validated `^[A-Za-z0-9][\w-]{2,127}$`),
      not files — regression-check ID validation anyway.
- [ ] **Admin XSS** — rollback dashboard metadata, support ticket
      subject/reply bodies (stored in DB, rendered in thread view),
      `dklc_message` GET param → all `esc_html`'d (one known sink:
      `$_GET['dklc_notice']` class attribute — fixed-token guard, verify).
- [ ] **CSRF** — settings + license + ticket `admin-post.php` handlers
      reject missing/foreign nonce; REST writes with cookie auth need
      `X-WP-Nonce`; Application-Password auth bypasses CSRF by design
      (non-cookie) — document this.
- [ ] **SSRF** — license/support clients POST only to the configured
      `api_url` (constant/filter, not request input) — verify no request
      param can redirect the target.
- [ ] **Rate limiting** — >120 read or >30 write req/min per user →
      429 + `Retry-After`; headers `X-RateLimit-*` sane; separate buckets;
      `DIVISKIT_RATE_LIMIT_DISABLED` honored; behavior with persistent
      object cache (transients → Redis) documented.
- [ ] **Update channel** — `check_plugin_update` only touches its own
      slug; package URL is store-controlled https; Pro: no update without
      active license; free: anonymous updates work.

## 3. Functional contract [B]

- [ ] **Handshake integrity** — `compatible:true`; every route in
      `CAPABILITIES` ↔ registered route ↔ MCP tool name (drift check:
      add-route-without-capability regression test).
- [ ] **dry_run** on every mutating route → `{dry_run:true, plan:{summary,
      changes[]}}` shape, zero DB mutation.
- [ ] **Idempotency** — `page_trash` twice → `already_trashed`;
      same-status `page_update_status` → `noop:true`; repeated upserts.
- [ ] **Write round-trips** — page create → update-content → get;
      module clone/move/lock/unlock/update (full-attr replace!);
      section append/replace/remove; canvas create/duplicate/delete;
      library save/get; preset create/update/delete/set-default/
      reassign/cleanup/registry-doctor; variable create/delete/
      create-fluid-system; global color/font CRUD; menu create +
      add-page/add-custom/assign; tb template create/trash + layout
      update/block-insert; seo metadata get/update with checksum
      (stale checksum → conflict, never blind overwrite).
- [ ] **Rollback** — backup-enabled write → snapshot listed → restore →
      content byte-reverted; expired/interrupted states render correctly
      in the admin dashboard.
- [ ] **Skills endpoints** — manifest sha256 matches bundle bytes;
      base64 decodes; `diviskit_skills_dirs` filter: Pro bundle appears
      only with Pro installed (module gating honored).
- [ ] **Pro module gate** — vendokit routes answer
      `vendokit.module_inactive` (412) when the module toggle is off or
      the vendokit plugin is absent — not generic 404.
- [ ] **Admin pages** — agent dashboard (+ support view, design-system
      view), pro page (+ support view): render without notices on
      PHP 8.x; self-test button works; copy buttons work.

## 4. i18n (new — verify before beta) [B]

- [ ] `de_DE` site: agent dashboard fully German incl. design-system JS
      strings and schema-dump inline script; license + support panels
      German (`diviskit` domain); pro page German; plugin list shows
      translated Name/Description.
- [ ] `en_US` site: no German bleed anywhere.
- [ ] Third locale (e.g. `fr_FR`): falls back to English, no fatal.
- [ ] `wp i18n make-pot` for `diviskit-agent`, `diviskit-pro`, `diviskit`
      domains → zero warnings (`bin/make-translations.sh`).
- [ ] `.mo` ships in release zip (`unzip -l` check) — release.sh zips
      `languages/` automatically, regression-verify.

## 5. Compatibility & release engineering [R]

- [ ] **PHP floor** — header/readme now declare 8.0 (code uses `match`,
      `?->`, `str_contains` — 7.4 would fatal); verify plugin-check
      accepts; ideally activation smoke on PHP 8.0 and 8.3.
- [ ] **WP floor** — 6.5 install smoke (JIT textdomain + Domain Path,
      `wp_date`, block APIs).
- [ ] **Clean install** — activate on fresh site: AGENTS.md block
      written, no fatal, handshake OK, no Divi → guarded routes.
- [ ] **Deactivation/re-activation** — idempotent AGENTS.md marker
      block (no duplication), options preserved as designed.
- [ ] **Uninstall story** — decide + document: leftover options
      (`diviskit_agents_md_version`, `diviskit_rl_*` transients,
      `diviskit_snapshot_*`, `dklc_*`, `dks_tickets_*`). Either ship
      `uninstall.php` or document retention. Currently none exists.
- [ ] **Multisite** — activation smoke (per-site options, app passwords).
- [ ] **SDK mirror check** — all bundled `class-diviskit-*-client.php`
      byte-identical to `vendokit` canonical (md5 sweep).
- [ ] **Co-activation matrix** — agent alone, agent+pro, agent+pro
      without vendokit plugin, pro without agent (admin notice only,
      no fatal, routes absent).
- [ ] **readme.txt ↔ header sync** — release.sh asserts stable-tag ==
      version; `Tested up to` realistic.

## 6. Recommended infrastructure [R]

- [ ] Clone vendokit's wp-phpunit scaffold for diviskit-agent:
      `composer.json` (wp-phpunit/wp-phpunit), `phpunit.xml.dist`,
      `tests/bootstrap.php` — start with permission-callback unit tests
      and the handler-level `edit_post` guards.
- [ ] REST smoke script (curl bats or shell) against DDEV site covering
      §1–§3 — can run pre-release in CI later.
- [ ] `bin/make-translations.sh` in release.sh pipeline (regenerate POT
      + MO on every release so `languages/` never goes stale).

## 7. Known follow-ups (not beta-blockers) [L]

- Other suite plugins bundling the SDK (design-library, consent,
  vendokit-*) do not yet register the `diviskit` textdomain path or ship
  `diviskit-*.mo` — their license/support panels stay English under
  `de_DE`. Same pattern as implemented here applies when they are
  internationalized.
- `readme.txt`/`README.md` remain English-only (fine for beta).
- vendokit plugin itself has no Domain Path/`load_plugin_textdomain`
  for its own UI yet (separate product).
