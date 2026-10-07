<?php
/**
 * Diviskit Optin — options, defaults, sanitization.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function dkopt_defaults() {
    return array(
        // Provider
        'provider'             => 'mailerlite',
        'ml_api_token'         => '',
        'ml_group_id'          => '',
        'brevo_api_key'        => '',
        'brevo_list_id'        => '',
        'webhook_url'          => '',
        'webhook_secret'       => '',

        // Spam-Schutz (Keys + aktiviert, sonst nur Honeypot + Rate-Limit)
        'recaptcha_enabled'    => 1,
        'recaptcha_site_key'   => '',
        'recaptcha_secret_key' => '',

        // Einwilligung (wording is stored per signup as legal proof)
        'consent_text'         => 'Ich möchte News und Updates per E-Mail erhalten. Mit dem Absenden bestätige ich, dass meine Angaben gemäß der Datenschutzerklärung verarbeitet werden.',

        // Optionale Produkt-Interessen (Checkboxen über der Consent-Zeile).
        // Label leer = Interesse wird nicht angezeigt. group = optionale
        // MailerLite-Group-ID, die bestätigte Subscriber zusätzlich bekommen.
        'interests_enabled'    => 1,
        'interests_heading'    => 'Wofür interessierst du dich? (optional)',
        'int_sk_consent_label' => 'Diviskit Consent — Cookie-Consent für WordPress',
        'int_sk_consent_group' => '',
        'int_vendokit_label'   => 'Vendokit — Lizenz-Verkauf für WP-Produkte',
        'int_vendokit_group'   => '',
        'int_agent_label'      => 'Diviskit Agent — MCP für Divi 5',
        'int_agent_group'      => '',

        // Formular-Texte
        'form_heading'         => 'Newsletter',
        'form_subline'         => 'Updates zu neuen Releases, Features und Divi-5-Tipps — kein Spam.',
        'form_placeholder'     => 'E-Mail-Adresse',
        'form_button'          => 'Anmelden',
        'form_success'         => 'Fast geschafft! Bitte bestätige deine Anmeldung über den Link in der E-Mail, die wir dir gerade gesendet haben.',
        'form_success_heading' => 'Fast geschafft!',

        // Bestätigungs-Mail — Texte stehen den Mail-Templates als
        // {{subject}}, {{heading}}, {{intro}}, {{button_text}}, {{footer}}
        // zur Verfügung; das Layout kommt aus dem aktiven Template.
        'mail_subject'         => 'Bitte bestätige deine Newsletter-Anmeldung',
        'mail_heading'         => 'Fast geschafft!',
        'mail_intro'           => 'Bitte bestätige deine Anmeldung zu unserem Newsletter mit einem Klick auf den Button.',
        'mail_button'          => 'Anmeldung bestätigen',
        'mail_footer'          => 'Du hast dich nicht angemeldet? Dann kannst du diese E-Mail einfach ignorieren — es wird nichts weiter passieren.',

        // Aktives Mail-Template (ID aus dkopt_templates())
        'active_template'      => 'default',

        // Rechtsseiten (erscheinen in Mail + Formular)
        'policy_url'           => '/datenschutz/',
        'imprint_url'          => '/impressum/',

        // Redirects nach Klick auf den Bestätigungslink (leer = Startseite + ?optin=…)
        'redirect_confirm'     => '',
        'redirect_error'       => '',

        // Token-Lebensdauer in Stunden
        'token_ttl'            => 48,
    );
}

function dkopt_options() {
    return wp_parse_args( get_option( DIVISKIT_OPTIN_OPTION, array() ), dkopt_defaults() );
}

function dkopt_opt( $key ) {
    $o = dkopt_options();
    return isset( $o[ $key ] ) ? $o[ $key ] : '';
}

/**
 * reCAPTCHA is active only when the toggle is on AND both keys are set —
 * lets you keep prod keys stored while disabling the check on DDEV.
 */
function dkopt_recaptcha_active() {
    $o = dkopt_options();
    return ! empty( $o['recaptcha_enabled'] )
        && '' !== trim( (string) $o['recaptcha_site_key'] )
        && '' !== trim( (string) $o['recaptcha_secret_key'] );
}

/**
 * Interessen-Registry: slug => label + optionale Provider-Group-ID.
 * Nur Einträge mit nicht-leerem Label werden im Formular gezeigt und
 * serverseitig akzeptiert.
 */
function dkopt_interest_registry() {
    return array(
        'sk_consent' => array( 'label_key' => 'int_sk_consent_label', 'group_key' => 'int_sk_consent_group' ),
        'vendokit'   => array( 'label_key' => 'int_vendokit_label',   'group_key' => 'int_vendokit_group' ),
        'agent'      => array( 'label_key' => 'int_agent_label',      'group_key' => 'int_agent_group' ),
    );
}

/**
 * @return array slug => array( 'label' => string, 'group' => string )
 */
function dkopt_interests() {
    $o   = dkopt_options();
    $out = array();
    foreach ( dkopt_interest_registry() as $slug => $keys ) {
        $label = trim( (string) $o[ $keys['label_key'] ] );
        if ( '' === $label ) {
            continue;
        }
        $out[ $slug ] = array(
            'label' => $label,
            'group' => trim( (string) $o[ $keys['group_key'] ] ),
        );
    }
    return $out;
}

function dkopt_interests_active() {
    $o = dkopt_options();
    return ! empty( $o['interests_enabled'] ) && array() !== dkopt_interests();
}

/**
 * Filtert eine Nutzer-Eingabe auf die bekannten Interessen-Slugs.
 * @return string[]
 */
function dkopt_sanitize_interests( $in ) {
    if ( ! is_array( $in ) ) {
        return array();
    }
    $known = array_keys( dkopt_interests() );
    return array_values( array_intersect( $known, array_map( 'sanitize_key', $in ) ) );
}

/**
 * Whitelist sanitize for the single options array.
 */
function dkopt_sanitize_options( $in ) {
    $out = dkopt_options();
    if ( ! is_array( $in ) ) {
        return $out;
    }

    $provider = isset( $in['provider'] ) ? sanitize_key( $in['provider'] ) : '';
    if ( isset( dkopt_providers()[ $provider ] ) ) {
        $out['provider'] = $provider;
    }

    $text_keys = array(
        'ml_api_token', 'ml_group_id', 'brevo_api_key', 'brevo_list_id',
        'webhook_secret',
        'recaptcha_site_key', 'recaptcha_secret_key',
        'form_heading', 'form_subline', 'form_placeholder', 'form_button',
        'form_success', 'form_success_heading',
        'mail_subject', 'mail_heading', 'mail_intro', 'mail_button', 'mail_footer',
        'interests_heading',
        'int_sk_consent_label', 'int_sk_consent_group',
        'int_vendokit_label', 'int_vendokit_group',
        'int_agent_label', 'int_agent_group',
    );
    foreach ( $text_keys as $key ) {
        if ( isset( $in[ $key ] ) ) {
            $out[ $key ] = sanitize_text_field( $in[ $key ] );
        }
    }

    if ( isset( $in['active_template'] ) && null !== dkopt_template( sanitize_key( $in['active_template'] ) ) ) {
        $out['active_template'] = sanitize_key( $in['active_template'] );
    }

    if ( isset( $in['consent_text'] ) ) {
        $out['consent_text'] = sanitize_textarea_field( $in['consent_text'] );
    }

    foreach ( array( 'policy_url', 'imprint_url', 'redirect_confirm', 'redirect_error', 'webhook_url' ) as $key ) {
        if ( isset( $in[ $key ] ) ) {
            $out[ $key ] = esc_url_raw( trim( $in[ $key ] ) );
        }
    }

    $out['recaptcha_enabled'] = empty( $in['recaptcha_enabled'] ) ? 0 : 1;
    $out['interests_enabled'] = empty( $in['interests_enabled'] ) ? 0 : 1;

    $out['token_ttl'] = min( 168, max( 1, (int) ( isset( $in['token_ttl'] ) ? $in['token_ttl'] : 48 ) ) );

    return $out;
}
