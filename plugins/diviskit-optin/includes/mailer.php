<?php
/**
 * Diviskit Optin — confirmation mail.
 *
 * Renders the active mail template (admin → Diviskit Optin → Mail-Templates)
 * and sends it via wp_mail — use an SMTP plugin / mail service for reliable
 * delivery. Template markup should stay table-based with inline styles:
 * email clients don't do modern CSS.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function dkopt_confirm_url( $token ) {
    return rest_url( 'diviskit-optin/v1/confirm?token=' . rawurlencode( $token ) );
}

/**
 * @return bool wp_mail result
 */
function dkopt_send_confirm_mail( $email, $token ) {
    $o   = dkopt_options();
    $tpl = dkopt_active_template();

    $host    = wp_parse_url( home_url(), PHP_URL_HOST );
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo( 'name' ) . ' <noreply@' . $host . '>',
    );

    return wp_mail(
        $email,
        strtr( $o['mail_subject'], dkopt_template_vars( $email, dkopt_confirm_url( $token ) ) ),
        dkopt_render_template( $tpl['html'], $email, dkopt_confirm_url( $token ) ),
        $headers
    );
}

/**
 * Test mail from the templates admin screen — renders the given template
 * with a dummy token so the confirm link looks exactly like production.
 *
 * @return bool wp_mail result
 */
function dkopt_send_test_mail( $template_id, $to ) {
    $tpl = dkopt_template( $template_id );
    if ( ! $tpl || ! is_email( $to ) ) {
        return false;
    }
    $o       = dkopt_options();
    $url     = dkopt_confirm_url( str_repeat( 'a', 64 ) );
    $host    = wp_parse_url( home_url(), PHP_URL_HOST );
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo( 'name' ) . ' <noreply@' . $host . '>',
    );

    return wp_mail(
        $to,
        '[TEST] ' . strtr( $o['mail_subject'], dkopt_template_vars( $to, $url ) ),
        dkopt_render_template( $tpl['html'], $to, $url ),
        $headers
    );
}
