<?php
/**
 * Diviskit Optin — admin: tabbed page (Subscriber / Mail-Templates /
 * Einstellungen) in the Diviskit admin design, CSV export, template CRUD.
 *
 * Menu placement follows the suite pattern: submenu under the Diviskit
 * Agent menu when the agent plugin is present, standalone top-level
 * menu otherwise.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ==========================================================================
   MENU + SETTINGS REGISTRATION
   ========================================================================== */

function dkopt_admin_menu() {
    if ( class_exists( 'Diviskit_Agent' ) ) {
        $GLOBALS['dkopt_page_hook'] = add_submenu_page(
            'diviskit',
            'Diviskit Optin',
            'Optin',
            'manage_options',
            'diviskit-optin',
            'dkopt_admin_page'
        );
    } else {
        $GLOBALS['dkopt_page_hook'] = add_menu_page(
            'Diviskit Optin',
            'Diviskit Optin',
            'manage_options',
            'diviskit-optin',
            'dkopt_admin_page',
            dkopt_menu_icon(),
            58
        );
    }
}
add_action( 'admin_menu', 'dkopt_admin_menu' );

/**
 * Diviskit mark as the standalone top-level menu icon — same recoloring
 * trick as in Diviskit Agent/Consent (admin chrome expects light glyphs).
 */
function dkopt_menu_icon() {
    $svg_path = DIVISKIT_OPTIN_PATH . 'assets/diviskit-mark.svg';
    if ( is_readable( $svg_path ) ) {
        $svg = file_get_contents( $svg_path );
        if ( false !== $svg ) {
            return 'data:image/svg+xml;base64,' . base64_encode( str_replace( 'fill="black"', 'fill="#f0f0f1"', $svg ) );
        }
    }
    return 'dashicons-email-alt';
}

function dkopt_admin_init() {
    register_setting( 'diviskit_optin_group', DIVISKIT_OPTIN_OPTION, array( 'sanitize_callback' => 'dkopt_sanitize_options' ) );
}
add_action( 'admin_init', 'dkopt_admin_init' );

function dkopt_admin_assets( $hook ) {
    if ( empty( $GLOBALS['dkopt_page_hook'] ) || $GLOBALS['dkopt_page_hook'] !== $hook ) {
        return;
    }
    wp_enqueue_style( 'diviskit-optin-admin', DIVISKIT_OPTIN_URL . 'assets/diviskit-admin.css', array( 'dashicons' ), DIVISKIT_OPTIN_VERSION );
    wp_enqueue_style( 'diviskit-optin-page', DIVISKIT_OPTIN_URL . 'assets/admin.css', array( 'diviskit-optin-admin' ), DIVISKIT_OPTIN_VERSION );
    wp_enqueue_script( 'diviskit-optin-admin', DIVISKIT_OPTIN_URL . 'assets/admin.js', array(), DIVISKIT_OPTIN_VERSION, true );
    wp_localize_script( 'diviskit-optin-admin', 'DKOPT_ADMIN', array(
        // Sample values for the live preview — same map the mailer uses.
        'vars' => dkopt_template_vars( 'max@example.com', dkopt_confirm_url( str_repeat( 'a', 64 ) ) ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'dkopt_admin_assets' );

/**
 * Plugins list: direct links to settings + templates.
 */
function dkopt_action_links( $links ) {
    $url = admin_url( 'admin.php?page=diviskit-optin' );
    array_unshift( $links,
        '<a href="' . esc_url( $url . '&tab=settings' ) . '">Einstellungen</a>',
        '<a href="' . esc_url( $url . '&tab=templates' ) . '">Mail-Templates</a>'
    );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( DIVISKIT_OPTIN_PATH . 'diviskit-optin.php' ), 'dkopt_action_links' );

/**
 * One-time notice after activation pointing at the settings tab.
 */
function dkopt_activation_notice() {
    if ( ! get_transient( 'diviskit_optin_activated' ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    delete_transient( 'diviskit_optin_activated' );
    printf(
        '<div class="notice notice-success is-dismissible"><p>%s <a href="%s">%s</a></p></div>',
        esc_html( 'Diviskit Optin ist aktiviert.' ),
        esc_url( admin_url( 'admin.php?page=diviskit-optin&tab=settings' ) ),
        esc_html( 'Jetzt einrichten →' )
    );
}
add_action( 'admin_notices', 'dkopt_activation_notice' );

/* ==========================================================================
   PAGE SHELL (Diviskit design: header + tab nav + cards)
   ========================================================================== */

function dkopt_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $tabs = array(
        'subscribers' => 'Subscriber',
        'templates'   => 'Mail-Templates',
        'settings'    => 'Einstellungen',
    );
    $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'subscribers';
    if ( ! isset( $tabs[ $tab ] ) ) {
        $tab = 'subscribers';
    }
    ?>
    <div class="wrap">
      <h1 class="screen-reader-text"><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <div class="diviskit-admin">
        <header class="diviskit-header">
          <div>
            <div class="diviskit-brand">
              <img src="<?php echo esc_url( DIVISKIT_OPTIN_URL . 'assets/diviskit-wordmark.svg' ); ?>" alt="Diviskit" width="166" height="42" />
              <span class="diviskit-edition">Optin</span>
            </div>
            <p>Eigenes Double-Opt-In für Newsletter — Mail-Templates, DSGVO-Einwilligungsnachweis, Provider-Sync</p>
          </div>
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer" class="button"><span class="dashicons dashicons-visibility" aria-hidden="true"></span>Formular testen</a>
        </header>
        <nav class="diviskit-nav" aria-label="Diviskit Optin pages">
          <?php foreach ( $tabs as $id => $label ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=diviskit-optin&tab=' . $id ) ); ?>"<?php if ( $tab === $id ) echo ' aria-current="page"'; ?>><?php echo esc_html( $label ); ?></a>
          <?php endforeach; ?>
        </nav>
        <div class="diviskit-content">
          <?php
          if ( 'templates' === $tab ) {
              dkopt_admin_templates_tab();
          } elseif ( 'settings' === $tab ) {
              dkopt_admin_settings_tab();
          } else {
              dkopt_admin_subscribers_tab();
          }
          ?>
        </div>
        <footer class="diviskit-footer">
          Diviskit Optin <?php echo esc_html( DIVISKIT_OPTIN_VERSION ); ?>
          · Shortcode <code>[diviskit_optin_form]</code> (Legacy: <code>[skml_doi_form]</code>)
        </footer>
      </div>
    </div>
    <?php
}

/* ==========================================================================
   TAB: SETTINGS
   ========================================================================== */

function dkopt_field( $key, $type = 'text', $placeholder = '', $desc = '' ) {
    $o = dkopt_options();
    printf(
        '<input type="%s" name="%s[%s]" value="%s" class="regular-text" placeholder="%s">',
        esc_attr( $type ),
        esc_attr( DIVISKIT_OPTIN_OPTION ),
        esc_attr( $key ),
        esc_attr( $o[ $key ] ),
        esc_attr( $placeholder )
    );
    if ( $desc ) {
        printf( '<p class="description">%s</p>', esc_html( $desc ) );
    }
}

function dkopt_field_area( $key, $desc = '' ) {
    $o = dkopt_options();
    printf(
        '<textarea name="%s[%s]" rows="3" class="large-text">%s</textarea>',
        esc_attr( DIVISKIT_OPTIN_OPTION ),
        esc_attr( $key ),
        esc_textarea( $o[ $key ] )
    );
    if ( $desc ) {
        printf( '<p class="description">%s</p>', esc_html( $desc ) );
    }
}

function dkopt_admin_settings_tab() {
    $o         = dkopt_options();
    $providers = dkopt_providers();
    $provider  = isset( $providers[ $o['provider'] ] ) ? $o['provider'] : 'mailerlite';

    // Connectivity check for the selected provider (needs its key/url set).
    $has_key   = 'none' === $provider;
    foreach ( $providers[ $provider ]['fields'] as $key => $label ) {
        if ( false !== stripos( $key, 'key' ) || false !== stripos( $key, 'token' ) || false !== stripos( $key, 'url' ) ) {
            $has_key = '' !== trim( (string) $o[ $key ] );
        }
    }
    $api_status = '';
    if ( $has_key && 'none' !== $provider ) {
        $ping       = dkopt_provider_ping();
        $api_status = is_wp_error( $ping )
            ? '<span class="diviskit-status diviskit-status--error">' . esc_html( $ping->get_error_message() ) . '</span>'
            : '<span class="diviskit-status diviskit-status--success">Verbindung ok</span>';
    }
    ?>
    <form method="post" action="options.php">
      <?php settings_fields( 'diviskit_optin_group' ); ?>

      <section class="diviskit-card">
        <div class="diviskit-section-heading">
          <h2>Provider</h2>
          <?php if ( $api_status ) : ?><?php echo $api_status; // phpcs:ignore ?><?php endif; ?>
        </div>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="dkopt-provider">List-Provider</label></th>
            <td>
              <select id="dkopt-provider" name="<?php echo esc_attr( DIVISKIT_OPTIN_OPTION ); ?>[provider]">
                <?php foreach ( $providers as $id => $p ) : ?>
                  <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $provider, $id ); ?>><?php echo esc_html( $p['label'] ); ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <?php foreach ( $providers as $id => $p ) : ?>
            <?php foreach ( $p['fields'] as $key => $label ) : ?>
              <tr class="dkopt-pfield" data-provider="<?php echo esc_attr( $id ); ?>">
                <th scope="row"><?php echo esc_html( $label ); ?></th>
                <td><?php dkopt_field( $key, ( false !== stripos( $key, 'key' ) || false !== stripos( $key, 'token' ) || false !== stripos( $key, 'secret' ) ) ? 'password' : 'text' ); ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if ( '' !== $p['hint'] ) : ?>
              <tr class="dkopt-pfield" data-provider="<?php echo esc_attr( $id ); ?>">
                <th></th><td><p class="description"><?php echo esc_html( $p['hint'] ); ?></p></td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </table>
      </section>

      <section class="diviskit-card">
        <h2>Spam-Schutz (reCAPTCHA v3)</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">reCAPTCHA aktiv</th><td>
            <label><input type="checkbox" name="<?php echo esc_attr( DIVISKIT_OPTIN_OPTION ); ?>[recaptcha_enabled]" value="1" <?php checked( ! empty( $o['recaptcha_enabled'] ) ); ?>>
              Spam-Prüfung einschalten</label>
            <p class="description">Auf lokaler DDEV-Dev ausgeschaltet lassen — Keys bleiben gespeichert, greifen aber erst wenn aktiviert. Honeypot + Rate-Limit laufen immer.</p>
          </td></tr>
          <tr><th scope="row">reCAPTCHA Site Key</th><td><?php dkopt_field( 'recaptcha_site_key', 'text', '', 'v3 (unsichtbar). Wirkt nur, wenn „reCAPTCHA aktiv“ an ist.' ); ?></td></tr>
          <tr><th scope="row">reCAPTCHA Secret</th><td><?php dkopt_field( 'recaptcha_secret_key', 'password' ); ?></td></tr>
        </table>
      </section>

      <section class="diviskit-card">
        <h2>Einwilligung</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Consent-Text</th><td><?php dkopt_field_area( 'consent_text', 'Wird neben der Checkbox gezeigt UND bei jeder Anmeldung als Nachweis gespeichert. Bei Änderung des Wortlauts gilt die alte Version für Bestandsnachweise.' ); ?></td></tr>
          <tr><th scope="row">Datenschutz-URL</th><td><?php dkopt_field( 'policy_url', 'text', '/datenschutz/', 'Link im Formular + Footer der Bestätigungsmail.' ); ?></td></tr>
          <tr><th scope="row">Impressum-URL</th><td><?php dkopt_field( 'imprint_url', 'text', '/impressum/' ); ?></td></tr>
        </table>
      </section>

      <section class="diviskit-card">
        <h2>Produkt-Interessen (Warteliste)</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Checkboxen aktiv</th><td>
            <label><input type="checkbox" name="<?php echo esc_attr( DIVISKIT_OPTIN_OPTION ); ?>[interests_enabled]" value="1" <?php checked( ! empty( $o['interests_enabled'] ) ); ?>>
              Optionale Produkt-Checkboxen im Formular zeigen</label>
          </td></tr>
          <tr><th scope="row">Zwischenüberschrift</th><td><?php dkopt_field( 'interests_heading', 'text', 'Wofür interessierst du dich? (optional)' ); ?></td></tr>
          <?php foreach ( dkopt_interest_registry() as $slug => $keys ) : ?>
            <tr>
              <th scope="row">Interesse „<?php echo esc_html( $slug ); ?>“</th>
              <td>
                <?php dkopt_field( $keys['label_key'], 'text', 'Label (leer = ausblenden)' ); ?>
                &nbsp;ML-Group-ID:&nbsp;<?php dkopt_field( $keys['group_key'], 'text', 'optional', 'MailerLite-Group-ID — bestätigte Subscriber mit diesem Interesse werden zusätzlich in diese Gruppe geschrieben.' ); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </section>

      <section class="diviskit-card">
        <h2>Formular-Texte</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Überschrift</th><td><?php dkopt_field( 'form_heading' ); ?></td></tr>
          <tr><th scope="row">Subline</th><td><?php dkopt_field( 'form_subline' ); ?></td></tr>
          <tr><th scope="row">Placeholder</th><td><?php dkopt_field( 'form_placeholder' ); ?></td></tr>
          <tr><th scope="row">Button</th><td><?php dkopt_field( 'form_button' ); ?></td></tr>
          <tr><th scope="row">Erfolg: Überschrift</th><td><?php dkopt_field( 'form_success_heading' ); ?></td></tr>
          <tr><th scope="row">Erfolg: Text</th><td><?php dkopt_field_area( 'form_success', 'Nach dem Absenden — Hinweis auf die Bestätigungsmail.' ); ?></td></tr>
        </table>
      </section>

      <section class="diviskit-card">
        <h2>Bestätigungs-Mail — Texte</h2>
        <p class="description">Diese Texte stehen den Mail-Templates als <code>{{subject}}</code>, <code>{{heading}}</code>, <code>{{intro}}</code>, <code>{{button_text}}</code>, <code>{{footer}}</code> zur Verfügung. Das HTML-Layout selbst bearbeitest du im Tab
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=diviskit-optin&tab=templates' ) ); ?>">Mail-Templates</a>.</p>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Betreff</th><td><?php dkopt_field( 'mail_subject' ); ?></td></tr>
          <tr><th scope="row">Überschrift</th><td><?php dkopt_field( 'mail_heading' ); ?></td></tr>
          <tr><th scope="row">Intro</th><td><?php dkopt_field_area( 'mail_intro' ); ?></td></tr>
          <tr><th scope="row">Button</th><td><?php dkopt_field( 'mail_button' ); ?></td></tr>
          <tr><th scope="row">Footer</th><td><?php dkopt_field_area( 'mail_footer', 'Kleingedrucktes unter der Mail (z.B. „Nicht angemeldet? Einfach ignorieren.“).' ); ?></td></tr>
        </table>
      </section>

      <section class="diviskit-card">
        <h2>Technik</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Redirect nach Bestätigung</th><td><?php dkopt_field( 'redirect_confirm', 'url', 'https://…/danke/', 'Leer = Startseite mit ?optin=confirmed.' ); ?></td></tr>
          <tr><th scope="row">Redirect bei Fehler</th><td><?php dkopt_field( 'redirect_error', 'url', 'https://…/fehler/', 'Leer = Startseite mit ?optin=error.' ); ?></td></tr>
          <tr><th scope="row">Token-TTL (Stunden)</th><td><?php dkopt_field( 'token_ttl', 'number', '48' ); ?></td></tr>
        </table>
      </section>

      <?php submit_button(); ?>
    </form>
    <?php
}

/* ==========================================================================
   TAB: MAIL TEMPLATES
   ========================================================================== */

function dkopt_tpl_url( $args = array() ) {
    return admin_url( 'admin.php?page=diviskit-optin&tab=templates&' . http_build_query( $args ) );
}

function dkopt_tpl_action_url( $action, $id ) {
    return wp_nonce_url(
        admin_url( 'admin-post.php?action=dkopt_tpl_' . $action . '&template=' . rawurlencode( $id ) ),
        'dkopt_tpl_' . $action . '_' . $id
    );
}

function dkopt_admin_templates_tab() {
    $action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
    $id     = isset( $_GET['template'] ) ? sanitize_key( wp_unslash( $_GET['template'] ) ) : '';

    if ( 'edit' === $action || 'new' === $action ) {
        dkopt_admin_template_edit( $id );
        return;
    }

    dkopt_admin_templates_list();
}

function dkopt_admin_templates_list() {
    $templates = dkopt_templates();
    $active    = (string) dkopt_opt( 'active_template' );
    if ( ! isset( $templates[ $active ] ) ) {
        $active = 'default';
    }
    $notice  = isset( $_GET['dkopt_notice'] ) ? sanitize_key( wp_unslash( $_GET['dkopt_notice'] ) ) : '';
    $notices = array(
        'saved'      => array( 'success', 'Template gespeichert.' ),
        'activated'  => array( 'success', 'Template aktiviert.' ),
        'duplicated' => array( 'success', 'Template dupliziert.' ),
        'deleted'    => array( 'success', 'Template gelöscht.' ),
        'reset'      => array( 'success', 'Template auf Werkseinstellung zurückgesetzt.' ),
        'tested'     => array( 'success', 'Testmail an deine Admin-Adresse gesendet.' ),
        'testfail'   => array( 'error', 'Testmail konnte nicht gesendet werden (wp_mail fehlgeschlagen).' ),
        'invalid'    => array( 'error', 'Aktion fehlgeschlagen (unbekanntes oder geschütztes Template).' ),
    );
    ?>
    <?php if ( isset( $notices[ $notice ] ) ) : ?>
      <div class="notice notice-<?php echo esc_attr( $notices[ $notice ][0] ); ?> is-dismissible"><p><?php echo esc_html( $notices[ $notice ][1] ); ?></p></div>
    <?php endif; ?>

    <section class="diviskit-card">
      <div class="diviskit-section-heading">
        <h2>Mail-Templates</h2>
        <a href="<?php echo esc_url( dkopt_tpl_url( array( 'action' => 'new' ) ) ); ?>" class="button button-primary">Neues Template</a>
      </div>
      <p class="diviskit-muted">Templates sind vollständige HTML-Mails mit <code>{{platzhaltern}}</code>. Pflicht: <code>{{confirm_url}}</code> — ohne Bestätigungslink kein Double-Opt-In. Das aktive Template wird für jede Bestätigungsmail genutzt.</p>

      <table class="widefat striped">
        <thead><tr>
          <th>Name</th><th>Status</th><th>Aktionen</th>
        </tr></thead>
        <tbody>
        <?php foreach ( $templates as $tpl ) : ?>
          <tr>
            <td>
              <strong><?php echo esc_html( $tpl['name'] ); ?></strong>
              <?php if ( ! empty( $tpl['builtin'] ) ) : ?><span class="diviskit-muted">(eingebaut)</span><?php endif; ?>
              <br><code><?php echo esc_html( $tpl['id'] ); ?></code>
            </td>
            <td>
              <?php if ( $tpl['id'] === $active ) : ?>
                <span class="diviskit-status diviskit-status--success">aktiv</span>
              <?php else : ?><span class="diviskit-muted">—</span><?php endif; ?>
            </td>
            <td>
              <a href="<?php echo esc_url( dkopt_tpl_url( array( 'action' => 'edit', 'template' => $tpl['id'] ) ) ); ?>">Bearbeiten</a>
              · <a href="<?php echo esc_url( dkopt_tpl_action_url( 'duplicate', $tpl['id'] ) ); ?>">Duplizieren</a>
              · <a href="<?php echo esc_url( dkopt_tpl_action_url( 'test', $tpl['id'] ) ); ?>">Testmail</a>
              <?php if ( $tpl['id'] !== $active ) : ?>
                · <a href="<?php echo esc_url( dkopt_tpl_action_url( 'activate', $tpl['id'] ) ); ?>">Aktivieren</a>
              <?php endif; ?>
              <?php if ( ! empty( $tpl['builtin'] ) ) : ?>
                · <a href="<?php echo esc_url( dkopt_tpl_action_url( 'reset', $tpl['id'] ) ); ?>">Werkseinstellung</a>
              <?php else : ?>
                · <a href="<?php echo esc_url( dkopt_tpl_action_url( 'delete', $tpl['id'] ) ); ?>"
                     onclick="return confirm('Template wirklich löschen?');" style="color:#9b252a;">Löschen</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <?php
}

function dkopt_admin_template_edit( $id ) {
    $is_new = ( '' === $id );
    $tpl    = $is_new
        ? array( 'id' => '', 'name' => '', 'html' => dkopt_factory_template_html() )
        : dkopt_template( $id );

    if ( ! $tpl ) {
        wp_safe_redirect( dkopt_tpl_url( array( 'dkopt_notice' => 'invalid' ) ) );
        exit;
    }

    $err = isset( $_GET['dkopt_error'] ) ? sanitize_text_field( wp_unslash( $_GET['dkopt_error'] ) ) : '';
    ?>
    <section class="diviskit-card">
      <div class="diviskit-section-heading">
        <h2><?php echo $is_new ? 'Neues Mail-Template' : 'Mail-Template bearbeiten'; // phpcs:ignore ?></h2>
        <a class="button" href="<?php echo esc_url( dkopt_tpl_url() ); ?>">← Zurück zur Übersicht</a>
      </div>
      <?php if ( '' !== $err ) : ?>
        <div class="diviskit-callout diviskit-callout--error"><p><?php echo esc_html( $err ); ?></p></div>
      <?php endif; ?>

      <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="dkopt_tpl_save">
        <input type="hidden" name="tpl_id" value="<?php echo esc_attr( $tpl['id'] ); ?>">
        <?php wp_nonce_field( 'dkopt_tpl_save' ); ?>

        <div class="dkopt-editor-grid">
          <div>
            <p>
              <label for="dkopt-tpl-name"><strong>Name</strong></label><br>
              <input type="text" id="dkopt-tpl-name" name="tpl_name" value="<?php echo esc_attr( $tpl['name'] ); ?>" class="regular-text" required>
            </p>
            <p>
              <label for="dkopt-tpl-html"><strong>HTML</strong> <span class="diviskit-muted">— Vorschau rechts aktualisiert sich live</span></label><br>
              <textarea id="dkopt-tpl-html" name="tpl_html" rows="30" class="large-text code dkopt-tpl-textarea"><?php echo esc_textarea( $tpl['html'] ); ?></textarea>
            </p>
            <details class="dkopt-placeholders">
              <summary><strong>Platzhalter</strong></summary>
              <table class="widefat" style="margin-top:8px;">
                <?php foreach ( dkopt_template_placeholders() as $ph => $desc ) : ?>
                  <tr><td style="width:150px;"><code><?php echo esc_html( $ph ); ?></code></td><td><?php echo esc_html( $desc ); ?></td></tr>
                <?php endforeach; ?>
              </table>
            </details>
            <p>
              <?php submit_button( 'Template speichern', 'primary', 'submit', false ); ?>
              <a class="button" href="<?php echo esc_url( dkopt_tpl_url() ); ?>">Abbrechen</a>
            </p>
          </div>
          <div class="dkopt-preview">
            <p class="diviskit-muted">Vorschau mit Beispielwerten — endgültige Werte kommen beim Versand aus den Einstellungen.</p>
            <iframe id="dkopt-tpl-preview" class="dkopt-preview-frame" title="Mail-Vorschau" sandbox></iframe>
          </div>
        </div>
      </form>
    </section>
    <?php
}

/* ------------------------------------------------------------------ */
/*  Template admin-post actions                                        */
/* ------------------------------------------------------------------ */

function dkopt_tpl_redirect( $notice ) {
    wp_safe_redirect( dkopt_tpl_url( array( 'dkopt_notice' => $notice ) ) );
    exit;
}

function dkopt_tpl_error_redirect( $id, WP_Error $err ) {
    wp_safe_redirect( dkopt_tpl_url( array( 'action' => 'edit', 'template' => $id, 'dkopt_error' => $err->get_error_message() ) ) );
    exit;
}

function dkopt_tpl_request_id() {
    return isset( $_REQUEST['template'] ) ? sanitize_key( wp_unslash( $_REQUEST['template'] ) ) : '';
}

function dkopt_tpl_guard( $action, $id ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Forbidden', 403 );
    }
    check_admin_referer( 'dkopt_tpl_' . $action . '_' . $id );
}

function dkopt_handle_tpl_save() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Forbidden', 403 );
    }
    check_admin_referer( 'dkopt_tpl_save' );

    $id   = isset( $_POST['tpl_id'] ) ? sanitize_key( wp_unslash( $_POST['tpl_id'] ) ) : '';
    $name = isset( $_POST['tpl_name'] ) ? wp_unslash( $_POST['tpl_name'] ) : '';
    $html = isset( $_POST['tpl_html'] ) ? wp_unslash( $_POST['tpl_html'] ) : '';

    $result = dkopt_save_template( $id, $name, $html );
    if ( is_wp_error( $result ) ) {
        dkopt_tpl_error_redirect( $id, $result );
    }
    dkopt_tpl_redirect( 'saved' );
}
add_action( 'admin_post_dkopt_tpl_save', 'dkopt_handle_tpl_save' );

function dkopt_handle_tpl_activate() {
    $id = dkopt_tpl_request_id();
    dkopt_tpl_guard( 'activate', $id );
    dkopt_tpl_redirect( dkopt_activate_template( $id ) ? 'activated' : 'invalid' );
}
add_action( 'admin_post_dkopt_tpl_activate', 'dkopt_handle_tpl_activate' );

function dkopt_handle_tpl_duplicate() {
    $id = dkopt_tpl_request_id();
    dkopt_tpl_guard( 'duplicate', $id );
    dkopt_tpl_redirect( dkopt_duplicate_template( $id ) ? 'duplicated' : 'invalid' );
}
add_action( 'admin_post_dkopt_tpl_duplicate', 'dkopt_handle_tpl_duplicate' );

function dkopt_handle_tpl_delete() {
    $id = dkopt_tpl_request_id();
    dkopt_tpl_guard( 'delete', $id );
    dkopt_tpl_redirect( dkopt_delete_template( $id ) ? 'deleted' : 'invalid' );
}
add_action( 'admin_post_dkopt_tpl_delete', 'dkopt_handle_tpl_delete' );

function dkopt_handle_tpl_reset() {
    $id = dkopt_tpl_request_id();
    dkopt_tpl_guard( 'reset', $id );
    dkopt_tpl_redirect( dkopt_reset_template( $id ) ? 'reset' : 'invalid' );
}
add_action( 'admin_post_dkopt_tpl_reset', 'dkopt_handle_tpl_reset' );

function dkopt_handle_tpl_test() {
    $id = dkopt_tpl_request_id();
    dkopt_tpl_guard( 'test', $id );
    $to = wp_get_current_user();
    $ok = $to && $to->user_email ? dkopt_send_test_mail( $id, $to->user_email ) : false;
    dkopt_tpl_redirect( $ok ? 'tested' : 'testfail' );
}
add_action( 'admin_post_dkopt_tpl_test', 'dkopt_handle_tpl_test' );

/* ==========================================================================
   TAB: SUBSCRIBER LIST + CSV EXPORT
   ========================================================================== */

function dkopt_admin_subscribers_tab() {
    $counts  = dkopt_subscriber_counts();
    $entries = dkopt_subscriber_entries( 100 );
    ?>
    <section class="diviskit-card">
      <div class="diviskit-section-heading">
        <h2>Subscriber</h2>
        <a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=dkopt_export' ), 'dkopt_export' ) ); ?>"><span class="dashicons dashicons-download" aria-hidden="true"></span>CSV-Export</a>
      </div>
      <p>
        <span class="diviskit-status diviskit-status--warning">Pending: <strong><?php echo (int) $counts['pending']; ?></strong></span>
        &nbsp; <span class="diviskit-status diviskit-status--success">Confirmed: <strong><?php echo (int) $counts['confirmed']; ?></strong></span>
        &nbsp; <span class="diviskit-status diviskit-status--neutral">Expired: <strong><?php echo (int) $counts['expired']; ?></strong></span>
      </p>

      <table class="widefat striped">
        <thead><tr>
          <th>E-Mail</th><th>Status</th><th>Angemeldet</th><th>Bestätigt</th><th>Sync</th><th>IP</th><th>Interessen</th><th>Consent-Text</th>
        </tr></thead>
        <tbody>
        <?php if ( ! $entries ) : ?>
          <tr><td colspan="8"><em>Noch keine Einträge.</em></td></tr>
        <?php endif; ?>
        <?php foreach ( $entries as $e ) : ?>
          <tr>
            <td><?php echo esc_html( $e['email'] ); ?></td>
            <td><?php echo esc_html( $e['status'] ); ?></td>
            <td><?php echo esc_html( $e['created_at'] ); ?></td>
            <td><?php echo esc_html( (string) $e['confirmed_at'] ); ?></td>
            <td>
              <?php if ( $e['ml_synced_at'] ) : ?>
                <span class="diviskit-status diviskit-status--success"><?php echo esc_html( $e['ml_synced_at'] ); ?></span>
              <?php elseif ( '' !== (string) $e['ml_error'] ) : ?>
                <span class="diviskit-status diviskit-status--error" title="<?php echo esc_attr( $e['ml_error'] ); ?>"><?php echo esc_html( $e['ml_error'] ); ?></span>
              <?php else : ?>—<?php endif; ?>
            </td>
            <td><?php echo esc_html( $e['ip_address'] ); ?></td>
            <td><?php echo esc_html( isset( $e['interests'] ) ? str_replace( ',', ', ', (string) $e['interests'] ) : '' ); ?></td>
            <td><small><?php echo esc_html( wp_trim_words( $e['consent_text'], 15 ) ); ?></small></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <?php
}

function dkopt_export_csv() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Forbidden', 403 );
    }
    check_admin_referer( 'dkopt_export' );

    global $wpdb;
    $table = dkopt_table();
    $rows  = $wpdb->get_results(
        "SELECT email, status, consent_text, ip_address, user_agent, created_at, confirmed_at, ml_synced_at, interests FROM {$table} ORDER BY created_at DESC",
        ARRAY_A
    );

    nocache_headers();
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="diviskit-optin-subscribers-' . gmdate( 'Y-m-d' ) . '.csv"' );

    $out = fopen( 'php://output', 'w' );
    fputcsv( $out, array( 'email', 'status', 'consent_text', 'ip_address', 'user_agent', 'created_at', 'confirmed_at', 'ml_synced_at', 'interests' ) );
    foreach ( $rows as $row ) {
        fputcsv( $out, $row );
    }
    fclose( $out );
    exit;
}
add_action( 'admin_post_dkopt_export', 'dkopt_export_csv' );
