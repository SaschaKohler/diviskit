<?php
/**
 * Diviskit Optin — provider layer.
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
function dkopt_providers() {
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
        'webhook'    => array(
            'label'  => 'Webhook (generisch)',
            'fields' => array(
                'webhook_url'    => 'Webhook-URL',
                'webhook_secret' => 'Secret (optional)',
            ),
            'hint'   => 'POST mit JSON-Body { event, email, interests, confirmed_at, site } an die URL — Bridge zu Zapier, Make, n8n, Mailchimp oder eigenen Integrationen. Mit Secret wird ein X-Dkopt-Signature-Header (HMAC-SHA256 über den Body) mitgeschickt. HTTP 2xx gilt als Erfolg.',
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
 * @param string[] $interests  Gewählte Interessen-Slugs (aus dkopt_interests()).
 * @return true|WP_Error
 */
function dkopt_push_subscriber( $email, $interests = array() ) {
    $interests = dkopt_sanitize_interests( $interests );
    switch ( dkopt_opt( 'provider' ) ) {
        case 'brevo':
            return dkopt_brevo_add_subscriber( $email );
        case 'webhook':
            return dkopt_webhook_add_subscriber( $email, $interests );
        case 'none':
            return true;
        case 'mailerlite':
        default:
            return dkopt_ml_add_subscriber( $email, $interests );
    }
}

/**
 * Connectivity check for the settings page.
 * @return true|WP_Error
 */
function dkopt_provider_ping() {
    switch ( dkopt_opt( 'provider' ) ) {
        case 'brevo':
            return dkopt_brevo_ping();
        case 'webhook':
            return dkopt_webhook_ping();
        case 'none':
            return new WP_Error( 'dkopt_no_provider', 'Kein Provider konfiguriert — Sync deaktiviert.' );
        case 'mailerlite':
        default:
            return dkopt_ml_ping();
    }
}

/* ------------------------------------------------------------------ */
/*  MailerLite                                                         */
/* ------------------------------------------------------------------ */

/**
 * MailerLite sits behind Cloudflare, which 403s the default WordPress
 * HTTP user agent — always send a plugin UA.
 */
function dkopt_ml_headers( $token ) {
    return array(
        'Authorization' => 'Bearer ' . $token,
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
        'User-Agent'    => 'diviskit-optin/' . DIVISKIT_OPTIN_VERSION . ' (+WordPress)',
    );
}

/**
 * @return true|WP_Error
 */
function dkopt_ml_add_subscriber( $email, $interests = array() ) {
    $token = trim( (string) dkopt_opt( 'ml_api_token' ) );
    if ( '' === $token ) {
        return new WP_Error( 'dkopt_ml_no_token', 'MailerLite API-Token fehlt (Einstellungen).' );
    }

    $body = array(
        'email'         => $email,
        'status'        => 'active',
        'subscribed_at' => current_time( 'mysql', true ),
    );

    $groups = array_filter( array_map( 'trim', explode( ',', (string) dkopt_opt( 'ml_group_id' ) ) ) );

    // Gewählte Produkt-Interessen → deren konfigurierte ML-Group-IDs dazu.
    $interest_map = dkopt_interests();
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
        'headers' => dkopt_ml_headers( $token ),
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
    return new WP_Error( 'dkopt_ml_' . $code, 'MailerLite API ' . $code . ': ' . substr( wp_strip_all_tags( (string) $msg ), 0, 200 ) );
}

/**
 * @return true|WP_Error
 */
function dkopt_ml_ping() {
    $token = trim( (string) dkopt_opt( 'ml_api_token' ) );
    if ( '' === $token ) {
        return new WP_Error( 'dkopt_ml_no_token', 'MailerLite API-Token fehlt (Einstellungen).' );
    }
    $res = wp_remote_get( 'https://connect.mailerlite.com/api/groups?limit=1', array(
        'timeout' => 10,
        'headers' => dkopt_ml_headers( $token ),
    ) );
    if ( is_wp_error( $res ) ) {
        return $res;
    }
    $code = (int) wp_remote_retrieve_response_code( $res );
    return ( $code >= 200 && $code < 300 )
        ? true
        : new WP_Error( 'dkopt_ml_' . $code, 'MailerLite API HTTP ' . $code );
}

/* ------------------------------------------------------------------ */
/*  Brevo                                                              */
/* ------------------------------------------------------------------ */

function dkopt_brevo_headers( $key ) {
    return array(
        'api-key'      => $key,
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
        'User-Agent'   => 'diviskit-optin/' . DIVISKIT_OPTIN_VERSION . ' (+WordPress)',
    );
}

/**
 * @return true|WP_Error
 */
function dkopt_brevo_add_subscriber( $email ) {
    $key = trim( (string) dkopt_opt( 'brevo_api_key' ) );
    if ( '' === $key ) {
        return new WP_Error( 'dkopt_brevo_no_key', 'Brevo API-Key fehlt (Einstellungen).' );
    }

    $body = array(
        'email'         => $email,
        'updateEnabled' => true,
    );
    $list = (int) dkopt_opt( 'brevo_list_id' );
    if ( $list > 0 ) {
        $body['listIds'] = array( $list );
    }

    $res = wp_remote_post( 'https://api.brevo.com/v3/contacts', array(
        'timeout' => 15,
        'headers' => dkopt_brevo_headers( $key ),
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
    return new WP_Error( 'dkopt_brevo_' . $code, 'Brevo API ' . $code . ': ' . substr( wp_strip_all_tags( (string) $msg ), 0, 200 ) );
}

/**
 * @return true|WP_Error
 */
function dkopt_brevo_ping() {
    $key = trim( (string) dkopt_opt( 'brevo_api_key' ) );
    if ( '' === $key ) {
        return new WP_Error( 'dkopt_brevo_no_key', 'Brevo API-Key fehlt (Einstellungen).' );
    }
    $res = wp_remote_get( 'https://api.brevo.com/v3/account', array(
        'timeout' => 10,
        'headers' => dkopt_brevo_headers( $key ),
    ) );
    if ( is_wp_error( $res ) ) {
        return $res;
    }
    $code = (int) wp_remote_retrieve_response_code( $res );
    return ( $code >= 200 && $code < 300 )
        ? true
        : new WP_Error( 'dkopt_brevo_' . $code, 'Brevo API HTTP ' . $code );
}

/* ------------------------------------------------------------------ */
/*  Generic webhook                                                    */
/* ------------------------------------------------------------------ */

/**
 * With a secret configured the body is signed GitHub-style:
 * X-Dkopt-Signature: sha256=<hmac(body, secret)>.
 */
function dkopt_webhook_headers( $body_json ) {
    $headers = array(
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
        'User-Agent'   => 'diviskit-optin/' . DIVISKIT_OPTIN_VERSION . ' (+WordPress)',
    );
    $secret = trim( (string) dkopt_opt( 'webhook_secret' ) );
    if ( '' !== $secret ) {
        $headers['X-Dkopt-Signature'] = 'sha256=' . hash_hmac( 'sha256', $body_json, $secret );
    }
    return $headers;
}

function dkopt_webhook_post( $url, $payload, $timeout = 15 ) {
    $body = wp_json_encode( $payload );
    return wp_remote_post( $url, array(
        'timeout' => $timeout,
        'headers' => dkopt_webhook_headers( $body ),
        'body'    => $body,
    ) );
}

/**
 * @return true|WP_Error
 */
function dkopt_webhook_add_subscriber( $email, $interests = array() ) {
    $url = trim( (string) dkopt_opt( 'webhook_url' ) );
    if ( '' === $url ) {
        return new WP_Error( 'dkopt_webhook_no_url', 'Webhook-URL fehlt (Einstellungen).' );
    }

    $res = dkopt_webhook_post( $url, array(
        'event'        => 'subscriber.confirmed',
        'email'        => $email,
        'interests'    => array_values( $interests ),
        'confirmed_at' => current_time( 'mysql', true ),
        'site'         => home_url(),
    ) );

    if ( is_wp_error( $res ) ) {
        return $res;
    }

    $code = (int) wp_remote_retrieve_response_code( $res );
    if ( $code >= 200 && $code < 300 ) {
        return true;
    }
    return new WP_Error( 'dkopt_webhook_' . $code, 'Webhook HTTP ' . $code . ': ' . substr( wp_strip_all_tags( (string) wp_remote_retrieve_body( $res ) ), 0, 200 ) );
}

/**
 * Connectivity check — fires a ping event (no real subscriber data).
 * @return true|WP_Error
 */
function dkopt_webhook_ping() {
    $url = trim( (string) dkopt_opt( 'webhook_url' ) );
    if ( '' === $url ) {
        return new WP_Error( 'dkopt_webhook_no_url', 'Webhook-URL fehlt (Einstellungen).' );
    }
    $res = dkopt_webhook_post( $url, array(
        'event' => 'ping',
        'site'  => home_url(),
    ), 10 );
    if ( is_wp_error( $res ) ) {
        return $res;
    }
    $code = (int) wp_remote_retrieve_response_code( $res );
    return ( $code >= 200 && $code < 300 )
        ? true
        : new WP_Error( 'dkopt_webhook_' . $code, 'Webhook HTTP ' . $code );
}
