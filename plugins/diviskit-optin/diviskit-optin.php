<?php
/**
 * Plugin Name: Diviskit Optin
 * Description: Eigenes Double-Opt-In für Newsletter-Signups: gebrandete deutsche Bestätigungsmail aus frei editierbaren Mail-Templates, DSGVO-Einwilligungsnachweis mit Consent-Text, reCAPTCHA v3 + Honeypot. Bestätigte Subscriber werden an den gewählten Provider (MailerLite, Brevo, generischer Webhook) übergeben — deren kostenpflichtiges DOI wird umgangen.
 * Version: 0.5.0
 * Author: Sascha Kohler
 * License: GPLv2 or later
 * Text Domain: diviskit-optin
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'DIVISKIT_OPTIN_VERSION' ) ) {
    define( 'DIVISKIT_OPTIN_VERSION', '0.5.0' );
}
if ( ! defined( 'DIVISKIT_OPTIN_DB_VERSION' ) ) {
    define( 'DIVISKIT_OPTIN_DB_VERSION', '2' );
}
if ( ! defined( 'DIVISKIT_OPTIN_PATH' ) ) {
    define( 'DIVISKIT_OPTIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'DIVISKIT_OPTIN_URL' ) ) {
    define( 'DIVISKIT_OPTIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'DIVISKIT_OPTIN_OPTION' ) ) {
    define( 'DIVISKIT_OPTIN_OPTION', 'diviskit_optin_options' );
}
if ( ! defined( 'DIVISKIT_OPTIN_TEMPLATES_OPTION' ) ) {
    define( 'DIVISKIT_OPTIN_TEMPLATES_OPTION', 'diviskit_optin_templates' );
}

require_once DIVISKIT_OPTIN_PATH . 'includes/options.php';
require_once DIVISKIT_OPTIN_PATH . 'includes/subscribers.php';
require_once DIVISKIT_OPTIN_PATH . 'includes/templates.php';
require_once DIVISKIT_OPTIN_PATH . 'includes/mailer.php';
require_once DIVISKIT_OPTIN_PATH . 'includes/providers.php';
require_once DIVISKIT_OPTIN_PATH . 'includes/rest.php';
require_once DIVISKIT_OPTIN_PATH . 'includes/form.php';

if ( is_admin() ) {
    require_once DIVISKIT_OPTIN_PATH . 'includes/admin.php';
}

/**
 * Plugin text domain.
 */
function dkopt_load_textdomain() {
    load_plugin_textdomain( 'diviskit-optin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'dkopt_load_textdomain' );

/**
 * On activation: create the subscriber table, seed the templates option
 * (autoload off — template HTML can be sizeable) and schedule the daily
 * housekeeping event (expire stale pending rows, retry failed syncs).
 */
function dkopt_activate() {
    dkopt_migrate_from_skml();
    dkopt_create_table();
    dkopt_templates_init();
    if ( ! wp_next_scheduled( 'diviskit_optin_daily' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'diviskit_optin_daily' );
    }
    set_transient( 'diviskit_optin_activated', 1, MINUTE_IN_SECONDS );
}
register_activation_hook( __FILE__, 'dkopt_activate' );

function dkopt_deactivate() {
    wp_clear_scheduled_hook( 'diviskit_optin_daily' );
}
register_deactivation_hook( __FILE__, 'dkopt_deactivate' );

/**
 * Daily housekeeping: expire pending tokens past their TTL and retry the
 * provider sync for confirmed rows that never made it to the list.
 */
function dkopt_daily_tasks() {
    dkopt_expire_pending();
    dkopt_retry_provider_sync();
}
add_action( 'diviskit_optin_daily', 'dkopt_daily_tasks' );

/**
 * Migration from sk-mailerlite-doi (<=0.3.0): copies the options, renames
 * the subscriber table and moves the cron hook. Runs on every load but is
 * a no-op once the legacy options/table are gone.
 */
function dkopt_migrate_from_skml() {
    $old_options = get_option( 'skml_doi_options' );
    if ( false !== $old_options ) {
        if ( false === get_option( DIVISKIT_OPTIN_OPTION ) ) {
            add_option( DIVISKIT_OPTIN_OPTION, $old_options );
        }
        delete_option( 'skml_doi_options' );
    }

    $old_db_version = get_option( 'skml_doi_db_version' );
    if ( false !== $old_db_version ) {
        if ( false === get_option( 'diviskit_optin_db_version' ) ) {
            add_option( 'diviskit_optin_db_version', $old_db_version );
        }
        delete_option( 'skml_doi_db_version' );
    }

    global $wpdb;
    $old_table = $wpdb->prefix . 'skml_subscribers';
    $new_table = dkopt_table();
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $old_table ) ) === $old_table ) {
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $new_table ) ) !== $new_table ) {
            $wpdb->query( "RENAME TABLE {$old_table} TO {$new_table}" );
        } else {
            // Both exist (e.g. new table created before migration ran):
            // merge rows by PK/email uniqueness, then drop the old table.
            $wpdb->query( "INSERT IGNORE INTO {$new_table} SELECT * FROM {$old_table}" );
            $wpdb->query( "DROP TABLE {$old_table}" );
        }
    }

    if ( wp_next_scheduled( 'skml_doi_daily' ) ) {
        wp_clear_scheduled_hook( 'skml_doi_daily' );
        if ( ! wp_next_scheduled( 'diviskit_optin_daily' ) ) {
            wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'diviskit_optin_daily' );
        }
    }
}
add_action( 'plugins_loaded', 'dkopt_migrate_from_skml', 1 );

/**
 * Upgrade path: create the table if the plugin was updated without
 * re-activation (e.g. files replaced via ZIP upload).
 */
function dkopt_maybe_upgrade() {
    if ( get_option( 'diviskit_optin_db_version' ) !== DIVISKIT_OPTIN_DB_VERSION ) {
        dkopt_create_table();
    }
}
add_action( 'plugins_loaded', 'dkopt_maybe_upgrade' );
