<?php
/**
 * SK MailerLite DOI — provider layer.
 *
 * Confirmed subscribers are pushed to the configured list provider with
 * status "active" — the plugin itself already performed the double opt-in.
 *
 * MailerLite: keep "Double opt-in for API and integrations" OFF
 * (Account settings → Subscribe settings), otherwise subscribers get a
 * second, unstyled MailerLite DOI mail on top.
 * Brevo: contacts are upserted with updateEnabled=true.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Provider registry. `fields` = option keys the admin form shows for that
 * provider only.
 */
function skml_providers() {
    return array(
        'mailerlite' => array(
            'label'  => 'MailerLite',
            'fields' => array(
                'ml_api_token' => 'API-Token',
                'ml_group_id'  => 'Group ID(s)',
            ),
            'hint'   => 'Token: MailerLite → Integrations → MailerLite API. WICHTIG: „Double opt-in for API and integrations" (Account settings → Subscribe settings) aus lassen — das Plugin macht das DOI selbst.',
        ),
        'brevo'      => array(
            'label'  => 'Brevo',
            'fields' => array(
                'brevo_api_key' => 'API-Key',
                'brevo_list_id' => 'List ID',
            ),
            'hint'   => 'API-Key: Brevo → SMTP & API → API Keys (v3). List ID findest du in der Listen-Übersicht (Zahl). Kontakte werden mit updateEnabled upserted.',
        ),
        'none'       => array(
            'label'  => 'Nur lokal speichern',
            'fields' => array(),
            'hint'   => 'Kein Provider — bestätigte Subscriber werden nur in der WP-Tabelle gehalten (CSV-Export). Gut zum Testen oder für einen späteren CSV-Import.',
        ),
    );
}

/* ------------------------------------------------------------------ */
/*  Dispatch                                                           */
/* ------------------------------------------------------------------ */

/**
 * @param string   $email
 * @param string[] $interests  Gewählte Interessen-Slugs (aus skml_interests()).
 * @return true|WP_Error
 */
function skml_push_subscriber( $email, $interests = array() ) {
    $interests = skml_sanitize_interests( $interests );
    switch ( skml_opt( 'provider' ) ) {
        case 'brevo':
            return skml_brevo_add_subscriber( $email );
        case 'none':
            return true;
        case 'mailerlite':
        default:
            return skml_ml_add_subscriber( $email, $interests );
    }
}

/**
 * Connectivity check for the settings page.
 * @return true|WP_Error
 */
function skml_provider_ping() {
    switch ( skml_opt( 'provider' ) ) {
        case 'brevo':
            return skml_brevo_ping();
        case 'none':
            return new WP_Error( 'skml_no_provider', 'Kein Provider konfiguriert — Sync deaktiviert.' );
        case 'mailerlite':
        default:
            return skml_ml_ping();
    }
}

/* ------------------------------------------------------------------ */
/*  MailerLite                                                         */
/* ------------------------------------------------------------------ */

/**
 * MailerLite sits behind Cloudflare, which 403s the default WordPress
 * HTTP user agent — always send a plugin UA.
 */
function skml_ml_headers( $token ) {
    return array(
        'Authorization' => 'Bearer ' . $token,
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
        'User-Agent'    => 'sk-mailerlite-doi/' . SKML_VERSION . ' (+WordPress)',
    );
}

/**
 * @return true|WP_Error
 */
function skml_ml_add_subscriber( $email, $interests = array() ) {
    $token = trim( (string) skml_opt( 'ml_api_token' ) );
    if ( '' === $token ) {
        return new WP_Error( 'skml_ml_no_token', 'MailerLite API-Token fehlt (Einstellungen).' );
    }

    $body = array(
        'email'         => $email,
        'status'        => 'active',
        'subscribed_at' => current_time( 'mysql', true ),
    );

    $groups = array_filter( array_map( 'trim', explode( ',', (string) skml_opt( 'ml_group_id' ) ) ) );

    // Gewählte Produkt-Interessen → deren konfigurierte ML-Group-IDs dazu.
    $interest_map = skml_interests();
    foreach ( $interests as $slug ) {
        if ( isset( $interest_map[ $slug ] ) && '' !== $interest_map[ $slug ]['group'] ) {
            $groups[] = $interest_map[ $slug ]['group'];
        }
    }
    $groups = array_unique( $groups );
    if ( $groups ) {
        $body['groups'] = array_values( $groups );
    }

    $res = wp_remote_post( 'https://connect.mailerlite.com/api/subscribers', array(
        'timeout' => 15,
        'headers' => skml_ml_headers( $token ),
        'body'    => wp_json_encode( $body ),
    ) );

    if ( is_wp_error( $res ) ) {
        return $res;
    }

    $code = (int) wp_remote_retrieve_response_code( $res );
    if ( $code >= 200 && $code < 300 ) {
        return true;
    }

    $detail = json_decode( wp_remote_retrieve_body( $res ), true );
    $msg    = isset( $detail['message'] ) ? $detail['message'] : wp_remote_retrieve_body( $res );
    return new WP_Error( 'skml_ml_' . $code, 'MailerLite API ' . $code . ': ' . substr( wp_strip_all_tags( (string) $msg ), 0, 200 ) );
}

/**
 * @return true|WP_Error
 */
function skml_ml_ping() {
    $token = trim( (string) skml_opt( 'ml_api_token' ) );
    if ( '' === $token ) {
        return new WP_Error( 'skml_ml_no_token', 'MailerLite API-Token fehlt (Einstellungen).' );
    }
    $res = wp_remote_get( 'https://connect.mailerlite.com/api/groups?limit=1', array(
        'timeout' => 10,
        'headers' => skml_ml_headers( $token ),
    ) );
    if ( is_wp_error( $res ) ) {
        return $res;
    }
    $code = (int) wp_remote_retrieve_response_code( $res );
    return ( $code >= 200 && $code < 300 )
        ? true
        : new WP_Error( 'skml_ml_' . $code, 'MailerLite API HTTP ' . $code );
}

/* ------------------------------------------------------------------ */
/*  Brevo                                                              */
/* ------------------------------------------------------------------ */

function skml_brevo_headers( $key ) {
    return array(
        'api-key'      => $key,
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
        'User-Agent'   => 'sk-mailerlite-doi/' . SKML_VERSION . ' (+WordPress)',
    );
}

/**
 * @return true|WP_Error
 */
function skml_brevo_add_subscriber( $email ) {
    $key = trim( (string) skml_opt( 'brevo_api_key' ) );
    if ( '' === $key ) {
        return new WP_Error( 'skml_brevo_no_key', 'Brevo API-Key fehlt (Einstellungen).' );
    }

    $body = array(
        'email'         => $email,
        'updateEnabled' => true,
    );
    $list = (int) skml_opt( 'brevo_list_id' );
    if ( $list > 0 ) {
        $body['listIds'] = array( $list );
    }

    $res = wp_remote_post( 'https://api.brevo.com/v3/contacts', array(
        'timeout' => 15,
        'headers' => skml_brevo_headers( $key ),
        'body'    => wp_json_encode( $body ),
    ) );

    if ( is_wp_error( $res ) ) {
        return $res;
    }

    $code = (int) wp_remote_retrieve_response_code( $res );
    if ( $code >= 200 && $code < 300 ) {
        return true;
    }

    $detail = json_decode( wp_remote_retrieve_body( $res ), true );
    $msg    = isset( $detail['message'] ) ? $detail['message'] : wp_remote_retrieve_body( $res );
    return new WP_Error( 'skml_brevo_' . $code, 'Brevo API ' . $code . ': ' . substr( wp_strip_all_tags( (string) $msg ), 0, 200 ) );
}

/**
 * @return true|WP_Error
 */
function skml_brevo_ping() {
    $key = trim( (string) skml_opt( 'brevo_api_key' ) );
    if ( '' === $key ) {
        return new WP_Error( 'skml_brevo_no_key', 'Brevo API-Key fehlt (Einstellungen).' );
    }
    $res = wp_remote_get( 'https://api.brevo.com/v3/account', array(
        'timeout' => 10,
        'headers' => skml_brevo_headers( $key ),
    ) );
    if ( is_wp_error( $res ) ) {
        return $res;
    }
    $code = (int) wp_remote_retrieve_response_code( $res );
    return ( $code >= 200 && $code < 300 )
        ? true
        : new WP_Error( 'skml_brevo_' . $code, 'Brevo API HTTP ' . $code );
}
