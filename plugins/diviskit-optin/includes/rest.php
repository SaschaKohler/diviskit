<?php
/**
 * Diviskit Optin — REST endpoints.
 *
 * POST /wp-json/diviskit-optin/v1/subscribe  { email, consent, website (honeypot), recaptcha }
 * GET  /wp-json/diviskit-optin/v1/confirm?token=…  → confirm + provider sync + redirect
 *
 * Legacy namespace skml/v1 (sk-mailerlite-doi <=0.3.0) stays registered:
 * confirmation links already sent by mail must keep working.
 *
 * Both endpoints are public by design — spam surface is covered by
 * honeypot, per-IP rate limit and optional reCAPTCHA v3 (score-based,
 * invisible).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function dkopt_rest_init() {
    foreach ( array( 'diviskit-optin/v1', 'skml/v1' ) as $ns ) {
        register_rest_route( $ns, '/subscribe', array(
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => 'dkopt_rest_subscribe',
            'args'                => array(
                'email'     => array( 'required' => true, 'type' => 'string' ),
                'consent'   => array( 'required' => true, 'type' => 'boolean' ),
                'interests' => array( 'type' => 'array', 'default' => array(), 'items' => array( 'type' => 'string' ) ),
                'website'   => array( 'type' => 'string', 'default' => '' ), // honeypot
                'recaptcha' => array( 'type' => 'string', 'default' => '' ),
            ),
        ) );

        register_rest_route( $ns, '/confirm', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => 'dkopt_rest_confirm',
            'args'                => array(
                'token' => array( 'required' => true, 'type' => 'string' ),
            ),
        ) );
    }
}
add_action( 'rest_api_init', 'dkopt_rest_init' );

/* ------------------------------------------------------------------ */
/*  POST /subscribe                                                    */
/* ------------------------------------------------------------------ */

function dkopt_rest_subscribe( WP_REST_Request $req ) {
    $success = array( 'ok' => true, 'message' => dkopt_opt( 'form_success' ) );

    // Honeypot: bots filling the hidden "website" field get a silent OK.
    if ( '' !== trim( (string) $req->get_param( 'website' ) ) ) {
        return $success;
    }

    // Per-IP rate limit: 5 submits / 10 min.
    $ip    = dkopt_client_ip();
    $key   = 'dkopt_rl_' . md5( $ip ? $ip : 'na' );
    $count = (int) get_transient( $key );
    if ( $count >= 5 ) {
        return new WP_Error( 'dkopt_rate', 'Zu viele Anfragen. Bitte später erneut versuchen.', array( 'status' => 429 ) );
    }
    set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

    $email = sanitize_email( $req->get_param( 'email' ) );
    if ( ! is_email( $email ) ) {
        return new WP_Error( 'dkopt_email', 'Bitte gib eine gültige E-Mail-Adresse ein.', array( 'status' => 400 ) );
    }

    if ( ! $req->get_param( 'consent' ) ) {
        return new WP_Error( 'dkopt_consent', 'Bitte bestätige die Einwilligung.', array( 'status' => 400 ) );
    }

    $captcha = dkopt_verify_recaptcha( (string) $req->get_param( 'recaptcha' ) );
    if ( is_wp_error( $captcha ) ) {
        return $captcha;
    }

    // Already fully subscribed → neutral success, no new mail, no enumeration.
    $existing = dkopt_find_by_email( $email );
    if ( $existing && 'confirmed' === $existing['status'] && $existing['ml_synced_at'] ) {
        return $success;
    }

    $interests = dkopt_sanitize_interests( (array) $req->get_param( 'interests' ) );

    $row = dkopt_upsert_pending( $email, (string) dkopt_opt( 'consent_text' ), $interests );
    if ( ! $row['id'] ) {
        return new WP_Error( 'dkopt_store', 'Speichern fehlgeschlagen. Bitte später erneut versuchen.', array( 'status' => 500 ) );
    }

    if ( ! dkopt_send_confirm_mail( $email, $row['token'] ) ) {
        return new WP_Error( 'dkopt_mail', 'Die Bestätigungs-E-Mail konnte nicht gesendet werden. Bitte später erneut versuchen.', array( 'status' => 500 ) );
    }

    return $success;
}

/**
 * reCAPTCHA v3 verify: token must be fresh, action "subscribe", and
 * Google's score must clear 0.5. Keys empty → check skipped entirely.
 */
function dkopt_verify_recaptcha( $token ) {
    if ( ! dkopt_recaptcha_active() ) {
        return true; // disabled or keys missing → honeypot + rate limit only
    }
    $secret = trim( (string) dkopt_opt( 'recaptcha_secret_key' ) );
    if ( '' === $token ) {
        return new WP_Error( 'dkopt_captcha', 'reCAPTCHA fehlt. Bitte Seite neu laden.', array( 'status' => 400 ) );
    }
    $res = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
        'timeout' => 10,
        'body'    => array(
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => dkopt_client_ip(),
        ),
    ) );
    if ( is_wp_error( $res ) ) {
        return new WP_Error( 'dkopt_captcha_net', 'reCAPTCHA-Prüfung derzeit nicht möglich.', array( 'status' => 502 ) );
    }
    $data = json_decode( wp_remote_retrieve_body( $res ), true );
    if ( empty( $data['success'] ) ) {
        return new WP_Error( 'dkopt_captcha', 'reCAPTCHA-Prüfung fehlgeschlagen.', array( 'status' => 400 ) );
    }
    if ( isset( $data['action'] ) && 'subscribe' !== $data['action'] ) {
        return new WP_Error( 'dkopt_captcha_action', 'reCAPTCHA-Action ungültig.', array( 'status' => 400 ) );
    }
    if ( isset( $data['score'] ) && (float) $data['score'] < 0.5 ) {
        return new WP_Error( 'dkopt_captcha_score', 'Anmeldung wurde als Spam eingestuft.', array( 'status' => 400 ) );
    }
    return true;
}

/* ------------------------------------------------------------------ */
/*  GET /confirm                                                       */
/* ------------------------------------------------------------------ */

function dkopt_redirect_url( $option_key, $fallback_status ) {
    $url = trim( (string) dkopt_opt( $option_key ) );
    if ( '' === $url ) {
        $url = add_query_arg( 'optin', $fallback_status, home_url( '/' ) );
    }
    return $url;
}

function dkopt_rest_confirm( WP_REST_Request $req ) {
    $ok_url  = dkopt_redirect_url( 'redirect_confirm', 'confirmed' );
    $err_url = dkopt_redirect_url( 'redirect_error', 'error' );

    $token = sanitize_text_field( $req->get_param( 'token' ) );
    if ( ! preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
        wp_safe_redirect( $err_url );
        exit;
    }

    $row = dkopt_find_by_token( $token );

    // Unknown token OR already consumed (token_hash cleared on confirm).
    if ( ! $row || 'pending' !== $row['status'] ) {
        wp_safe_redirect( $err_url );
        exit;
    }

    if ( strtotime( $row['expires_at'] ) < current_time( 'timestamp' ) ) {
        global $wpdb;
        $wpdb->update( dkopt_table(), array( 'status' => 'expired', 'token_hash' => '' ), array( 'id' => (int) $row['id'] ) );
        wp_safe_redirect( $err_url );
        exit;
    }

    dkopt_confirm_row( (int) $row['id'] );

    // Push to the configured provider; failures are stored on the row and
    // retried by the daily cron.
    $interests = isset( $row['interests'] ) && '' !== (string) $row['interests']
        ? explode( ',', $row['interests'] )
        : array();
    dkopt_mark_sync_result( (int) $row['id'], dkopt_push_subscriber( $row['email'], $interests ) );

    wp_safe_redirect( $ok_url );
    exit;
}
