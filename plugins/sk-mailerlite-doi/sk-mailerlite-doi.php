<?php
/**
 * Plugin Name: SK MailerLite DOI
 * Description: Eigenes Double-Opt-In für Newsletter-Signups: gebrandete deutsche Bestätigungsmail, DSGVO-Einwilligungsnachweis mit Consent-Text, reCAPTCHA v3 + Honeypot. Bestätigte Subscriber werden per API an den gewählten Provider (MailerLite, Brevo) übergeben.
 * Version: 0.2.0
 * Author: Sascha Kohler
 * License: GPLv2 or later
 * Text Domain: sk-mailerlite-doi
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'SKML_VERSION' ) ) {
    define( 'SKML_VERSION', '0.2.0' );
}
if ( ! defined( 'SKML_DB_VERSION' ) ) {
    define( 'SKML_DB_VERSION', '1' );
}
if ( ! defined( 'SKML_PATH' ) ) {
    define( 'SKML_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'SKML_URL' ) ) {
    define( 'SKML_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'SKML_OPTION' ) ) {
    define( 'SKML_OPTION', 'skml_doi_options' );
}

require_once SKML_PATH . 'includes/options.php';
require_once SKML_PATH . 'includes/subscribers.php';
require_once SKML_PATH . 'includes/mailer.php';
require_once SKML_PATH . 'includes/providers.php';
require_once SKML_PATH . 'includes/rest.php';
require_once SKML_PATH . 'includes/form.php';

if ( is_admin() ) {
    require_once SKML_PATH . 'includes/admin.php';
}

/**
 * Plugin text domain.
 */
function skml_doi_load_textdomain() {
    load_plugin_textdomain( 'sk-mailerlite-doi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'skml_doi_load_textdomain' );

/**
 * On activation: create the subscriber table and schedule the daily
 * housekeeping event (expire stale pending rows, retry failed ML syncs).
 */
function skml_doi_activate() {
    skml_create_table();
    if ( ! wp_next_scheduled( 'skml_doi_daily' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'skml_doi_daily' );
    }
}
register_activation_hook( __FILE__, 'skml_doi_activate' );

function skml_doi_deactivate() {
    wp_clear_scheduled_hook( 'skml_doi_daily' );
}
register_deactivation_hook( __FILE__, 'skml_doi_deactivate' );

/**
 * Daily housekeeping: expire pending tokens past their TTL and retry
 * MailerLite sync for confirmed rows that never made it to the list.
 */
function skml_doi_daily_tasks() {
    skml_expire_pending();
    skml_retry_ml_sync();
}
add_action( 'skml_doi_daily', 'skml_doi_daily_tasks' );

/**
 * Upgrade path: create the table if the plugin was updated without
 * re-activation (e.g. files replaced via ZIP upload).
 */
function skml_doi_maybe_upgrade() {
    if ( get_option( 'skml_doi_db_version' ) !== SKML_DB_VERSION ) {
        skml_create_table();
    }
}
add_action( 'plugins_loaded', 'skml_doi_maybe_upgrade' );
