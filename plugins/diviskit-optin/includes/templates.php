<?php
/**
 * Diviskit Optin — mail templates.
 *
 * Templates are admin-authored HTML documents stored in a non-autoloaded
 * option. {{placeholders}} are replaced with escaped values at send time —
 * {{confirm_url}} is mandatory, without it the mail is not a DOI.
 *
 * Storage: option diviskit_optin_templates = id => { id, name, html, builtin? }.
 * The builtin 'default' template only enters the option once edited;
 * deleting that entry restores the factory markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ==========================================================================
   STORAGE
   ========================================================================== */

/**
 * Create the templates option with autoload off (WP <6.6 wants the 'no'
 * string, >=6.6 wants bool false).
 */
function dkopt_templates_init() {
    if ( false === get_option( DIVISKIT_OPTIN_TEMPLATES_OPTION ) ) {
        $autoload = version_compare( get_bloginfo( 'version' ), '6.6', '>=' ) ? false : 'no';
        add_option( DIVISKIT_OPTIN_TEMPLATES_OPTION, array(), '', $autoload );
    }
}

/**
 * Builtin template registry — a small selection of ready-made layouts.
 * They merge into dkopt_templates() and can be overridden per id by a
 * stored copy; "Werkseinstellung" removes that copy.
 *
 * @return array id => template
 */
function dkopt_builtin_templates() {
    return array(
        'default'  => array( 'id' => 'default',  'name' => 'Standard',  'builtin' => true, 'html' => dkopt_builtin_html_default() ),
        'diviskit' => array( 'id' => 'diviskit', 'name' => 'Diviskit',  'builtin' => true, 'html' => dkopt_builtin_html_diviskit() ),
        'minimal'  => array( 'id' => 'minimal',  'name' => 'Schlicht',  'builtin' => true, 'html' => dkopt_builtin_html_minimal() ),
        'dark'     => array( 'id' => 'dark',     'name' => 'Dark',      'builtin' => true, 'html' => dkopt_builtin_html_dark() ),
    );
}

/**
 * @return array id => template. Builtin templates always present —
 * a stored copy with the same id overrides the builtin markup.
 */
function dkopt_templates() {
    $stored = get_option( DIVISKIT_OPTIN_TEMPLATES_OPTION, array() );
    if ( ! is_array( $stored ) ) {
        $stored = array();
    }
    $out = array();
    foreach ( dkopt_builtin_templates() as $id => $tpl ) {
        $out[ $id ] = isset( $stored[ $id ] ) && is_array( $stored[ $id ] ) ? $stored[ $id ] : $tpl;
        $out[ $id ]['builtin'] = true;
    }
    foreach ( $stored as $id => $tpl ) {
        if ( ! isset( $out[ $id ] ) && is_array( $tpl ) ) {
            $out[ $id ] = $tpl;
        }
    }
    return $out;
}

function dkopt_template( $id ) {
    $templates = dkopt_templates();
    return isset( $templates[ $id ] ) ? $templates[ $id ] : null;
}

/**
 * @return array the active template, falling back to 'default'.
 */
function dkopt_active_template() {
    $tpl = dkopt_template( (string) dkopt_opt( 'active_template' ) );
    return $tpl ? $tpl : dkopt_template( 'default' );
}

/**
 * Persist the template map. Keeps the no-autoload flag on first write.
 */
function dkopt_templates_store( $templates ) {
    if ( false === get_option( DIVISKIT_OPTIN_TEMPLATES_OPTION ) ) {
        dkopt_templates_init();
    }
    update_option( DIVISKIT_OPTIN_TEMPLATES_OPTION, $templates );
}

/**
 * Create or update a template.
 *
 * @param string $id   Existing id, or '' to create.
 * @param string $name Display name.
 * @param string $html Full HTML document, must contain {{confirm_url}}.
 * @return string|WP_Error template id
 */
function dkopt_save_template( $id, $name, $html ) {
    $name = trim( sanitize_text_field( $name ) );
    $html = trim( (string) $html );

    if ( '' === $name ) {
        return new WP_Error( 'dkopt_tpl_name', 'Bitte gib dem Template einen Namen.' );
    }
    if ( false === strpos( $html, '{{confirm_url}}' ) ) {
        return new WP_Error( 'dkopt_tpl_confirm', 'Das Template muss den Platzhalter {{confirm_url}} enthalten — ohne Bestätigungslink ist es kein Double-Opt-In.' );
    }

    $templates = dkopt_templates();
    $id        = sanitize_key( $id );
    if ( '' === $id || ! isset( $templates[ $id ] ) ) {
        $id = 'tpl_' . substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 8 );
    }

    $templates[ $id ] = array(
        'id'      => $id,
        'name'    => $name,
        'html'    => $html,
        'builtin' => ! empty( $templates[ $id ]['builtin'] ),
    );
    dkopt_templates_store( $templates );
    return $id;
}

function dkopt_delete_template( $id ) {
    $templates = dkopt_templates();
    if ( ! isset( $templates[ $id ] ) || ! empty( $templates[ $id ]['builtin'] ) ) {
        return false;
    }
    unset( $templates[ $id ] );
    dkopt_templates_store( $templates );

    if ( (string) dkopt_opt( 'active_template' ) === (string) $id ) {
        $o                     = get_option( DIVISKIT_OPTIN_OPTION, array() );
        $o['active_template']  = 'default';
        update_option( DIVISKIT_OPTIN_OPTION, $o );
    }
    return true;
}

/**
 * @return string|false new template id
 */
function dkopt_duplicate_template( $id ) {
    $src = dkopt_template( $id );
    if ( ! $src ) {
        return false;
    }
    return dkopt_save_template( '', $src['name'] . ' (Kopie)', $src['html'] );
}

/**
 * Drop the stored override of a builtin template — back to factory markup.
 */
function dkopt_reset_template( $id ) {
    $templates = dkopt_templates();
    if ( empty( $templates[ $id ]['builtin'] ) ) {
        return false;
    }
    $stored = get_option( DIVISKIT_OPTIN_TEMPLATES_OPTION, array() );
    if ( is_array( $stored ) && isset( $stored[ $id ] ) ) {
        unset( $stored[ $id ] );
        dkopt_templates_store( $stored );
    }
    return true;
}

function dkopt_activate_template( $id ) {
    if ( null === dkopt_template( $id ) ) {
        return false;
    }
    $o                    = get_option( DIVISKIT_OPTIN_OPTION, array() );
    $o['active_template'] = sanitize_key( $id );
    update_option( DIVISKIT_OPTIN_OPTION, $o );
    return true;
}

/* ==========================================================================
   RENDERING
   ========================================================================== */

/**
 * Placeholder map for the admin help box.
 * @return array placeholder => description
 */
function dkopt_template_placeholders() {
    return array(
        '{{confirm_url}}'  => 'Bestätigungslink (Pflicht — das DOI selbst)',
        '{{subject}}'      => 'Betreff aus den Einstellungen',
        '{{heading}}'      => 'Überschrift (Einstellungen → Bestätigungs-Mail)',
        '{{intro}}'        => 'Intro-Text (Einstellungen)',
        '{{button_text}}'  => 'Button-Label (Einstellungen)',
        '{{footer}}'       => 'Footer-Kleingedrucktes (Einstellungen)',
        '{{site_name}}'    => 'Blogname',
        '{{home_url}}'     => 'Home-URL',
        '{{policy_url}}'   => 'Datenschutz-URL (Einstellungen)',
        '{{imprint_url}}'  => 'Impressum-URL (Einstellungen)',
        '{{email}}'        => 'E-Mail-Adresse des Subscribers',
        '{{year}}'         => 'Aktuelles Jahr',
    );
}

/**
 * @return array placeholder => escaped replacement
 */
function dkopt_template_vars( $email, $confirm_url ) {
    $o = dkopt_options();
    return array(
        '{{confirm_url}}' => esc_url( $confirm_url ),
        '{{subject}}'     => esc_html( $o['mail_subject'] ),
        '{{heading}}'     => esc_html( $o['mail_heading'] ),
        '{{intro}}'       => esc_html( $o['mail_intro'] ),
        '{{button_text}}' => esc_html( $o['mail_button'] ),
        '{{footer}}'      => esc_html( $o['mail_footer'] ),
        '{{site_name}}'   => esc_html( get_bloginfo( 'name' ) ),
        '{{home_url}}'    => esc_url( home_url( '/' ) ),
        '{{policy_url}}'  => esc_url( $o['policy_url'] ),
        '{{imprint_url}}' => esc_url( $o['imprint_url'] ),
        '{{email}}'       => esc_html( $email ),
        '{{year}}'        => gmdate( 'Y' ),
    );
}

/**
 * Admin-authored HTML, stored raw (manage_options context — same trust as
 * the hardcoded template it replaces). Placeholders escape their values.
 */
function dkopt_render_template( $html, $email, $confirm_url ) {
    return strtr( $html, dkopt_template_vars( $email, $confirm_url ) );
}

/**
 * Factory markup for a builtin template. Used for the new-template
 * prefill and the "Werkseinstellung" reset.
 */
function dkopt_factory_template_html( $id = 'default' ) {
    $builtins = dkopt_builtin_templates();
    return isset( $builtins[ $id ] ) ? $builtins[ $id ]['html'] : $builtins['default']['html'];
}

/**
 * The previous hardcoded Vision layout, placeholders instead of inline
 * option reads.
 */
function dkopt_builtin_html_default() {
    return '<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background-color:#f1f1f1;font-family:-apple-system,\'Segoe UI\',Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f1f1;">
<tr><td align="center" style="padding:40px 16px;">

  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background-color:#ffffff;border:1px solid #e2e2e2;">

    <tr><td style="padding:28px 32px 0;">
      <p style="margin:0;font-family:\'Courier New\',monospace;font-size:11px;letter-spacing:2px;color:#b89e00;text-transform:uppercase;">{{site_name}}</p>
    </td></tr>

    <tr><td style="padding:16px 32px 0;">
      <h1 style="margin:0;font-size:26px;line-height:1.2;color:#17191a;font-weight:700;">{{heading}}</h1>
    </td></tr>

    <tr><td style="padding:16px 32px 0;">
      <p style="margin:0;font-size:15px;line-height:1.65;color:#3a3d3f;">{{intro}}</p>
    </td></tr>

    <tr><td style="padding:28px 32px;">
      <table role="presentation" cellpadding="0" cellspacing="0"><tr>
        <td style="background-color:#ffd400;">
          <a href="{{confirm_url}}"
             style="display:inline-block;padding:14px 28px;font-family:\'Courier New\',monospace;font-size:13px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#17191a;text-decoration:none;border:1px solid #17191a;">{{button_text}}</a>
        </td>
      </tr></table>
    </td></tr>

    <tr><td style="padding:0 32px 28px;">
      <p style="margin:0;font-size:12px;line-height:1.6;color:#8a8d8f;word-break:break-all;">
        Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:<br>
        <a href="{{confirm_url}}" style="color:#8a8d8f;">{{confirm_url}}</a>
      </p>
    </td></tr>

  </table>

  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">
    <tr><td style="padding:20px 8px;font-size:11px;line-height:1.6;color:#8a8d8f;text-align:center;">
      <p style="margin:0 0 8px;">{{footer}}</p>
      <p style="margin:0;">
        <a href="{{home_url}}" style="color:#8a8d8f;">{{site_name}}</a>
        · <a href="{{imprint_url}}" style="color:#8a8d8f;">Impressum</a>
        · <a href="{{policy_url}}" style="color:#8a8d8f;">Datenschutz</a>
      </p>
    </td></tr>
  </table>

</td></tr>
</table>
</body>
</html>';
}

/**
 * Diviskit brand look — primary #3854f4, rounded card, clean sans.
 */
function dkopt_builtin_html_diviskit() {
    return '<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:-apple-system,\'Segoe UI\',Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;">
<tr><td align="center" style="padding:40px 16px;">

  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background-color:#ffffff;border:1px solid #e4e4e7;border-radius:8px;">

    <tr><td style="padding:32px 32px 0;">
      <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:1.5px;color:#3854f4;text-transform:uppercase;">{{site_name}}</p>
    </td></tr>

    <tr><td style="padding:12px 32px 0;">
      <h1 style="margin:0;font-size:24px;line-height:1.3;color:#18181b;font-weight:600;">{{heading}}</h1>
    </td></tr>

    <tr><td style="padding:14px 32px 0;">
      <p style="margin:0;font-size:15px;line-height:1.6;color:#52525b;">{{intro}}</p>
    </td></tr>

    <tr><td style="padding:26px 32px 30px;">
      <table role="presentation" cellpadding="0" cellspacing="0"><tr>
        <td style="background-color:#3854f4;border-radius:6px;">
          <a href="{{confirm_url}}"
             style="display:inline-block;padding:13px 26px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">{{button_text}}</a>
        </td>
      </tr></table>
    </td></tr>

    <tr><td style="padding:0 32px 30px;">
      <p style="margin:0;font-size:12px;line-height:1.6;color:#a1a1aa;word-break:break-all;">
        Button funktioniert nicht? Diesen Link in den Browser kopieren:<br>
        <a href="{{confirm_url}}" style="color:#3854f4;">{{confirm_url}}</a>
      </p>
    </td></tr>

  </table>

  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">
    <tr><td style="padding:20px 8px;font-size:11px;line-height:1.6;color:#a1a1aa;text-align:center;">
      <p style="margin:0 0 8px;">{{footer}}</p>
      <p style="margin:0;">
        <a href="{{home_url}}" style="color:#a1a1aa;">{{site_name}}</a>
        · <a href="{{imprint_url}}" style="color:#a1a1aa;">Impressum</a>
        · <a href="{{policy_url}}" style="color:#a1a1aa;">Datenschutz</a>
      </p>
    </td></tr>
  </table>

</td></tr>
</table>
</body>
</html>';
}

/**
 * Schlicht — plain text feel: no card, serif body, outlined button.
 */
function dkopt_builtin_html_minimal() {
    return '<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background-color:#ffffff;font-family:Georgia,\'Times New Roman\',serif;color:#1a1a1a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center" style="padding:48px 16px;">

  <table role="presentation" width="520" cellpadding="0" cellspacing="0" style="max-width:520px;width:100%;">
    <tr><td>

      <h1 style="margin:0 0 20px;font-size:22px;line-height:1.35;font-weight:400;color:#1a1a1a;">{{heading}}</h1>

      <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#1a1a1a;">{{intro}}</p>

      <p style="margin:0 0 28px;font-family:Helvetica,Arial,sans-serif;">
        <a href="{{confirm_url}}"
           style="display:inline-block;padding:10px 22px;border:1px solid #1a1a1a;color:#1a1a1a;text-decoration:none;font-size:14px;">{{button_text}}</a>
      </p>

      <p style="margin:0 0 32px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:1.6;color:#777777;word-break:break-all;">
        Alternativ diesen Link in den Browser kopieren:<br>
        <a href="{{confirm_url}}" style="color:#777777;">{{confirm_url}}</a>
      </p>

      <hr style="border:none;border-top:1px solid #e5e5e5;margin:0 0 20px;">

      <p style="margin:0;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:1.6;color:#999999;">
        {{footer}}<br>
        <a href="{{home_url}}" style="color:#999999;">{{site_name}}</a>
        · <a href="{{imprint_url}}" style="color:#999999;">Impressum</a>
        · <a href="{{policy_url}}" style="color:#999999;">Datenschutz</a>
      </p>

    </td></tr>
  </table>

</td></tr>
</table>
</body>
</html>';
}

/**
 * Dark — Vision dark card: #0f1115 page, #17191a card, yellow accent.
 */
function dkopt_builtin_html_dark() {
    return '<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background-color:#0f1115;font-family:-apple-system,\'Segoe UI\',Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0f1115;">
<tr><td align="center" style="padding:40px 16px;">

  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background-color:#17191a;border:1px solid #2b2f31;">

    <tr><td style="padding:28px 32px 0;">
      <p style="margin:0;font-family:\'Courier New\',monospace;font-size:11px;letter-spacing:2px;color:#ffd400;text-transform:uppercase;">{{site_name}}</p>
    </td></tr>

    <tr><td style="padding:16px 32px 0;">
      <h1 style="margin:0;font-size:26px;line-height:1.2;color:#ffffff;font-weight:700;">{{heading}}</h1>
    </td></tr>

    <tr><td style="padding:16px 32px 0;">
      <p style="margin:0;font-size:15px;line-height:1.65;color:#b9bdc0;">{{intro}}</p>
    </td></tr>

    <tr><td style="padding:28px 32px;">
      <table role="presentation" cellpadding="0" cellspacing="0"><tr>
        <td style="background-color:#ffd400;">
          <a href="{{confirm_url}}"
             style="display:inline-block;padding:14px 28px;font-family:\'Courier New\',monospace;font-size:13px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#17191a;text-decoration:none;">{{button_text}}</a>
        </td>
      </tr></table>
    </td></tr>

    <tr><td style="padding:0 32px 28px;">
      <p style="margin:0;font-size:12px;line-height:1.6;color:#6b7074;word-break:break-all;">
        Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:<br>
        <a href="{{confirm_url}}" style="color:#6b7074;">{{confirm_url}}</a>
      </p>
    </td></tr>

  </table>

  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">
    <tr><td style="padding:20px 8px;font-size:11px;line-height:1.6;color:#6b7074;text-align:center;">
      <p style="margin:0 0 8px;">{{footer}}</p>
      <p style="margin:0;">
        <a href="{{home_url}}" style="color:#6b7074;">{{site_name}}</a>
        · <a href="{{imprint_url}}" style="color:#6b7074;">Impressum</a>
        · <a href="{{policy_url}}" style="color:#6b7074;">Datenschutz</a>
      </p>
    </td></tr>
  </table>

</td></tr>
</table>
</body>
</html>';
}
