<?php
/**
 * SK MailerLite DOI — options, defaults, sanitization.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function skml_defaults() {
    return array(
        // Provider
        'provider'             => 'mailerlite',
        'ml_api_token'         => '',
        'ml_group_id'          => '',
        'brevo_api_key'        => '',
        'brevo_list_id'        => '',

        // Spam-Schutz (Keys + aktiviert, sonst nur Honeypot + Rate-Limit)
        'recaptcha_enabled'    => 1,
        'recaptcha_site_key'   => '',
        'recaptcha_secret_key' => '',

        // Einwilligung (wording is stored per signup as legal proof)
        'consent_text'         => 'Ich möchte News und Updates per E-Mail erhalten. Mit dem Absenden bestätige ich, dass meine Angaben gemäß der Datenschutzerklärung verarbeitet werden.',

        // Formular-Texte
        'form_heading'         => 'Newsletter',
        'form_subline'         => 'Updates zu neuen Releases, Features und Divi-5-Tipps — kein Spam.',
        'form_placeholder'     => 'E-Mail-Adresse',
        'form_button'          => 'Anmelden',
        'form_success'         => 'Fast geschafft! Bitte bestätige deine Anmeldung über den Link in der E-Mail, die wir dir gerade gesendet haben.',
        'form_success_heading' => 'Fast geschafft!',

        // Bestätigungs-Mail
        'mail_subject'         => 'Bitte bestätige deine Newsletter-Anmeldung',
        'mail_heading'         => 'Fast geschafft!',
        'mail_intro'           => 'Bitte bestätige deine Anmeldung zu unserem Newsletter mit einem Klick auf den Button.',
        'mail_button'          => 'Anmeldung bestätigen',
        'mail_footer'          => 'Du hast dich nicht angemeldet? Dann kannst du diese E-Mail einfach ignorieren — es wird nichts weiter passieren.',

        // Rechtsseiten (erscheinen in Mail + Formular)
        'policy_url'           => '/datenschutz/',
        'imprint_url'          => '/impressum/',

        // Redirects nach Klick auf den Bestätigungslink (leer = Startseite + ?skml=…)
        'redirect_confirm'     => '',
        'redirect_error'       => '',

        // Token-Lebensdauer in Stunden
        'token_ttl'            => 48,
    );
}

function skml_options() {
    return wp_parse_args( get_option( SKML_OPTION, array() ), skml_defaults() );
}

function skml_opt( $key ) {
    $o = skml_options();
    return isset( $o[ $key ] ) ? $o[ $key ] : '';
}

/**
 * reCAPTCHA is active only when the toggle is on AND both keys are set —
 * lets you keep prod keys stored while disabling the check on DDEV.
 */
function skml_recaptcha_active() {
    $o = skml_options();
    return ! empty( $o['recaptcha_enabled'] )
        && '' !== trim( (string) $o['recaptcha_site_key'] )
        && '' !== trim( (string) $o['recaptcha_secret_key'] );
}

/**
 * Whitelist sanitize for the single options array.
 */
function skml_sanitize_options( $in ) {
    $out = skml_options();
    if ( ! is_array( $in ) ) {
        return $out;
    }

    $provider = isset( $in['provider'] ) ? sanitize_key( $in['provider'] ) : '';
    if ( isset( skml_providers()[ $provider ] ) ) {
        $out['provider'] = $provider;
    }

    $text_keys = array(
        'ml_api_token', 'ml_group_id', 'brevo_api_key', 'brevo_list_id',
        'recaptcha_site_key', 'recaptcha_secret_key',
        'form_heading', 'form_subline', 'form_placeholder', 'form_button',
        'form_success', 'form_success_heading',
        'mail_subject', 'mail_heading', 'mail_intro', 'mail_button', 'mail_footer',
    );
    foreach ( $text_keys as $key ) {
        if ( isset( $in[ $key ] ) ) {
            $out[ $key ] = sanitize_text_field( $in[ $key ] );
        }
    }

    if ( isset( $in['consent_text'] ) ) {
        $out['consent_text'] = sanitize_textarea_field( $in['consent_text'] );
    }

    foreach ( array( 'policy_url', 'imprint_url', 'redirect_confirm', 'redirect_error' ) as $key ) {
        if ( isset( $in[ $key ] ) ) {
            $out[ $key ] = esc_url_raw( trim( $in[ $key ] ) );
        }
    }

    $out['recaptcha_enabled'] = empty( $in['recaptcha_enabled'] ) ? 0 : 1;

    $out['token_ttl'] = min( 168, max( 1, (int) ( isset( $in['token_ttl'] ) ? $in['token_ttl'] : 48 ) ) );

    return $out;
}
