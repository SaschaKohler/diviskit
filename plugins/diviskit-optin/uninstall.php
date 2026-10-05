<?php
/**
 * SK MailerLite DOI — uninstall.
 *
 * Drops the subscriber table and removes all plugin data.
 * NOTE: the subscriber table is the legal DOI record — deleting it destroys
 * your Einwilligungsnachweise. Export CSV (admin → SK MailerLite DOI)
 * BEFORE uninstalling if you need to keep the proof.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'skml_doi_options' );
delete_option( 'skml_doi_db_version' );
wp_clear_scheduled_hook( 'skml_doi_daily' );

global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}skml_subscribers" );
