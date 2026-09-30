<?php
/**
 * SK MailerLite DOI — admin: settings page, subscriber list, CSV export.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ==========================================================================
   MENU + SETTINGS REGISTRATION
   ========================================================================== */

function skml_admin_menu() {
    add_menu_page(
        'SK MailerLite DOI',
        'SK MailerLite DOI',
        'manage_options',
        'skml-doi',
        'skml_admin_subscribers_page',
        'dashicons-email-alt',
        58
    );
    add_submenu_page( 'skml-doi', 'Subscriber', 'Subscriber', 'manage_options', 'skml-doi', 'skml_admin_subscribers_page' );
    add_submenu_page( 'skml-doi', 'Einstellungen', 'Einstellungen', 'manage_options', 'skml-doi-settings', 'skml_admin_settings_page' );
}
add_action( 'admin_menu', 'skml_admin_menu' );

function skml_admin_init() {
    register_setting( 'skml_doi_group', SKML_OPTION, array( 'sanitize_callback' => 'skml_sanitize_options' ) );
}
add_action( 'admin_init', 'skml_admin_init' );

/* ==========================================================================
   SETTINGS PAGE
   ========================================================================== */

function skml_field( $key, $type = 'text', $placeholder = '', $desc = '' ) {
    $o = skml_options();
    printf(
        '<input type="%s" name="%s[%s]" value="%s" class="regular-text" placeholder="%s">',
        esc_attr( $type ),
        esc_attr( SKML_OPTION ),
        esc_attr( $key ),
        esc_attr( $o[ $key ] ),
        esc_attr( $placeholder )
    );
    if ( $desc ) {
        printf( '<p class="description">%s</p>', esc_html( $desc ) );
    }
}

function skml_field_area( $key, $desc = '' ) {
    $o = skml_options();
    printf(
        '<textarea name="%s[%s]" rows="3" class="large-text">%s</textarea>',
        esc_attr( SKML_OPTION ),
        esc_attr( $key ),
        esc_textarea( $o[ $key ] )
    );
    if ( $desc ) {
        printf( '<p class="description">%s</p>', esc_html( $desc ) );
    }
}

function skml_admin_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $o         = skml_options();
    $providers = skml_providers();
    $provider  = isset( $providers[ $o['provider'] ] ) ? $o['provider'] : 'mailerlite';

    // Connectivity check for the selected provider (needs its key set).
    $has_key   = 'none' === $provider;
    foreach ( $providers[ $provider ]['fields'] as $key => $label ) {
        if ( false !== stripos( $key, 'key' ) || false !== stripos( $key, 'token' ) ) {
            $has_key = '' !== trim( (string) $o[ $key ] );
        }
    }
    $api_status = '';
    if ( $has_key && 'none' !== $provider ) {
        $ping       = skml_provider_ping();
        $api_status = is_wp_error( $ping )
            ? '<span style="color:#d63638;">✗ ' . esc_html( $ping->get_error_message() ) . '</span>'
            : '<span style="color:#00a32a;">✓ Verbindung ok</span>';
    }
    ?>
    <div class="wrap">
      <h1>SK MailerLite DOI — Einstellungen</h1>

      <form method="post" action="options.php">
        <?php settings_fields( 'skml_doi_group' ); ?>

        <h2>Provider</h2>
        <?php if ( $api_status ) : ?><p>API-Status: <?php echo $api_status; // phpcs:ignore ?></p><?php endif; ?>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="skml-provider">List-Provider</label></th>
            <td>
              <select id="skml-provider" name="<?php echo esc_attr( SKML_OPTION ); ?>[provider]">
                <?php foreach ( $providers as $id => $p ) : ?>
                  <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $provider, $id ); ?>><?php echo esc_html( $p['label'] ); ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <?php foreach ( $providers as $id => $p ) : ?>
            <?php foreach ( $p['fields'] as $key => $label ) : ?>
              <tr class="skml-pfield" data-provider="<?php echo esc_attr( $id ); ?>">
                <th scope="row"><?php echo esc_html( $label ); ?></th>
                <td><?php skml_field( $key, ( false !== stripos( $key, 'key' ) || false !== stripos( $key, 'token' ) ) ? 'password' : 'text' ); ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if ( '' !== $p['hint'] ) : ?>
              <tr class="skml-pfield" data-provider="<?php echo esc_attr( $id ); ?>">
                <th></th><td><p class="description"><?php echo esc_html( $p['hint'] ); ?></p></td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </table>
        <script>
        (function () {
          var sel = document.getElementById('skml-provider');
          function toggle() {
            document.querySelectorAll('.skml-pfield').forEach(function (row) {
              row.style.display = row.dataset.provider === sel.value ? '' : 'none';
            });
          }
          sel.addEventListener('change', toggle);
          toggle();
        })();
        </script>

        <h2>Spam-Schutz (reCAPTCHA v3)</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">reCAPTCHA aktiv</th><td>
            <label><input type="checkbox" name="<?php echo esc_attr( SKML_OPTION ); ?>[recaptcha_enabled]" value="1" <?php checked( ! empty( $o['recaptcha_enabled'] ) ); ?>>
              Spam-Prüfung einschalten</label>
            <p class="description">Auf lokaler DDEV-Dev ausgeschaltet lassen — Keys bleiben gespeichert, greifen aber erst wenn aktiviert. Honeypot + Rate-Limit laufen immer.</p>
          </td></tr>
          <tr><th scope="row">reCAPTCHA Site Key</th><td><?php skml_field( 'recaptcha_site_key', 'text', '', 'v3 (unsichtbar). Wirkt nur, wenn „reCAPTCHA aktiv“ an ist.' ); ?></td></tr>
          <tr><th scope="row">reCAPTCHA Secret</th><td><?php skml_field( 'recaptcha_secret_key', 'password' ); ?></td></tr>
        </table>

        <h2>Einwilligung</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Consent-Text</th><td><?php skml_field_area( 'consent_text', 'Wird neben der Checkbox gezeigt UND bei jeder Anmeldung als Nachweis gespeichert. Bei Änderung des Wortlauts gilt die alte Version für Bestandsnachweise.' ); ?></td></tr>
          <tr><th scope="row">Datenschutz-URL</th><td><?php skml_field( 'policy_url', 'text', '/datenschutz/', 'Link im Formular + Footer der Bestätigungsmail.' ); ?></td></tr>
          <tr><th scope="row">Impressum-URL</th><td><?php skml_field( 'imprint_url', 'text', '/impressum/' ); ?></td></tr>
        </table>

        <h2>Produkt-Interessen (Warteliste)</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Checkboxen aktiv</th><td>
            <label><input type="checkbox" name="<?php echo esc_attr( SKML_OPTION ); ?>[interests_enabled]" value="1" <?php checked( ! empty( $o['interests_enabled'] ) ); ?>>
              Optionale Produkt-Checkboxen im Formular zeigen</label>
          </td></tr>
          <tr><th scope="row">Zwischenüberschrift</th><td><?php skml_field( 'interests_heading', 'text', 'Wofür interessierst du dich? (optional)' ); ?></td></tr>
          <?php foreach ( skml_interest_registry() as $slug => $keys ) : ?>
            <tr>
              <th scope="row">Interesse „<?php echo esc_html( $slug ); ?>"</th>
              <td>
                <?php skml_field( $keys['label_key'], 'text', 'Label (leer = ausblenden)' ); ?>
                &nbsp;ML-Group-ID:&nbsp;<?php skml_field( $keys['group_key'], 'text', 'optional', 'MailerLite-Group-ID — bestätigte Subscriber mit diesem Interesse werden zusätzlich in diese Gruppe geschrieben.' ); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

        <h2>Formular-Texte</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Überschrift</th><td><?php skml_field( 'form_heading' ); ?></td></tr>
          <tr><th scope="row">Subline</th><td><?php skml_field( 'form_subline' ); ?></td></tr>
          <tr><th scope="row">Placeholder</th><td><?php skml_field( 'form_placeholder' ); ?></td></tr>
          <tr><th scope="row">Button</th><td><?php skml_field( 'form_button' ); ?></td></tr>
          <tr><th scope="row">Erfolg: Überschrift</th><td><?php skml_field( 'form_success_heading' ); ?></td></tr>
          <tr><th scope="row">Erfolg: Text</th><td><?php skml_field_area( 'form_success', 'Nach dem Absenden — Hinweis auf die Bestätigungsmail.' ); ?></td></tr>
        </table>

        <h2>Bestätigungs-Mail</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Betreff</th><td><?php skml_field( 'mail_subject' ); ?></td></tr>
          <tr><th scope="row">Überschrift</th><td><?php skml_field( 'mail_heading' ); ?></td></tr>
          <tr><th scope="row">Intro</th><td><?php skml_field_area( 'mail_intro' ); ?></td></tr>
          <tr><th scope="row">Button</th><td><?php skml_field( 'mail_button' ); ?></td></tr>
          <tr><th scope="row">Footer</th><td><?php skml_field_area( 'mail_footer', 'Kleingedrucktes unter der Mail (z.B. „Nicht angemeldet? Einfach ignorieren.“).' ); ?></td></tr>
        </table>

        <h2>Technik</h2>
        <table class="form-table" role="presentation">
          <tr><th scope="row">Redirect nach Bestätigung</th><td><?php skml_field( 'redirect_confirm', 'url', 'https://…/danke/', 'Leer = Startseite mit ?skml=confirmed.' ); ?></td></tr>
          <tr><th scope="row">Redirect bei Fehler</th><td><?php skml_field( 'redirect_error', 'url', 'https://…/fehler/', 'Leer = Startseite mit ?skml=error.' ); ?></td></tr>
          <tr><th scope="row">Token-TTL (Stunden)</th><td><?php skml_field( 'token_ttl', 'number', '48' ); ?></td></tr>
        </table>

        <?php submit_button(); ?>
      </form>

      <p class="description">Formular einbinden via Shortcode <code>[skml_doi_form]</code> (z.B. Divi-Code-Modul).</p>
    </div>
    <?php
}

/* ==========================================================================
   SUBSCRIBER LIST + CSV EXPORT
   ========================================================================== */

function skml_admin_subscribers_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $counts  = skml_subscriber_counts();
    $entries = skml_subscriber_entries( 100 );
    ?>
    <div class="wrap">
      <h1>SK MailerLite DOI — Subscriber</h1>
      <p>
        <span class="dashicons dashicons-clock"></span> Pending: <strong><?php echo (int) $counts['pending']; ?></strong>
        &nbsp;·&nbsp; <span class="dashicons dashicons-yes-alt"></span> Confirmed: <strong><?php echo (int) $counts['confirmed']; ?></strong>
        &nbsp;·&nbsp; Expired: <strong><?php echo (int) $counts['expired']; ?></strong>
        &nbsp;&nbsp;
        <a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=skml_export' ), 'skml_export' ) ); ?>">CSV-Export</a>
      </p>

      <table class="widefat striped">
        <thead><tr>
          <th>E-Mail</th><th>Status</th><th>Angemeldet</th><th>Bestätigt</th><th>ML-Sync</th><th>IP</th><th>Interessen</th><th>Consent-Text</th>
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
                <span style="color:#00a32a;">✓ <?php echo esc_html( $e['ml_synced_at'] ); ?></span>
              <?php elseif ( '' !== (string) $e['ml_error'] ) : ?>
                <span style="color:#d63638;" title="<?php echo esc_attr( $e['ml_error'] ); ?>">✗ <?php echo esc_html( $e['ml_error'] ); ?></span>
              <?php else : ?>—<?php endif; ?>
            </td>
            <td><?php echo esc_html( $e['ip_address'] ); ?></td>
            <td><?php echo esc_html( isset( $e['interests'] ) ? str_replace( ',', ', ', (string) $e['interests'] ) : '' ); ?></td>
            <td><small><?php echo esc_html( wp_trim_words( $e['consent_text'], 15 ) ); ?></small></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
}

function skml_export_csv() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Forbidden', 403 );
    }
    check_admin_referer( 'skml_export' );

    global $wpdb;
    $table = skml_table();
    $rows  = $wpdb->get_results(
        "SELECT email, status, consent_text, ip_address, user_agent, created_at, confirmed_at, ml_synced_at, interests FROM {$table} ORDER BY created_at DESC",
        ARRAY_A
    );

    nocache_headers();
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="skml-subscribers-' . gmdate( 'Y-m-d' ) . '.csv"' );

    $out = fopen( 'php://output', 'w' );
    fputcsv( $out, array( 'email', 'status', 'consent_text', 'ip_address', 'user_agent', 'created_at', 'confirmed_at', 'ml_synced_at', 'interests' ) );
    foreach ( $rows as $row ) {
        fputcsv( $out, $row );
    }
    fclose( $out );
    exit;
}
add_action( 'admin_post_skml_export', 'skml_export_csv' );
