<?php
/**
 * SK MailerLite DOI — subscriber storage (Einwilligungsnachweis, DSGVO Art. 7).
 *
 * One row per email. Pending rows carry a sha256 token hash (the raw token
 * only ever exists in the confirmation URL). Stored proof: consent wording
 * shown at submit time, timestamp, IP and user agent — the standard set
 * needed to prove a double opt-in.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function skml_table() {
    global $wpdb;
    return $wpdb->prefix . 'skml_subscribers';
}

function skml_create_table() {
    global $wpdb;
    $table   = skml_table();
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        email varchar(191) NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        token_hash char(64) NOT NULL DEFAULT '',
        consent_text text NOT NULL,
        ip_address varchar(45) NOT NULL DEFAULT '',
        user_agent varchar(255) NOT NULL DEFAULT '',
        created_at datetime NOT NULL,
        confirmed_at datetime DEFAULT NULL,
        expires_at datetime NOT NULL,
        ml_synced_at datetime DEFAULT NULL,
        ml_error varchar(255) NOT NULL DEFAULT '',
        PRIMARY KEY  (id),
        UNIQUE KEY email (email),
        KEY status (status),
        KEY token_hash (token_hash)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    update_option( 'skml_doi_db_version', SKML_DB_VERSION );
}

function skml_client_ip() {
    return sanitize_text_field( wp_unslash( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' ) );
}

function skml_find_by_email( $email ) {
    global $wpdb;
    $table = skml_table();
    return $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$table} WHERE email = %s", $email
    ), ARRAY_A );
}

function skml_find_by_token( $token ) {
    global $wpdb;
    $table = skml_table();
    return $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$table} WHERE token_hash = %s", hash( 'sha256', $token )
    ), ARRAY_A );
}

/**
 * Insert or refresh a pending signup. Re-subscribing a pending/confirmed
 * email rotates the token and rewrites the consent proof.
 * Returns array( 'id' => int, 'token' => raw token ).
 */
function skml_upsert_pending( $email, $consent_text ) {
    global $wpdb;
    $token   = bin2hex( random_bytes( 32 ) );
    $ttl     = max( 1, (int) skml_opt( 'token_ttl' ) );
    $now     = current_time( 'mysql' );
    $expires = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) + $ttl * HOUR_IN_SECONDS );

    $wpdb->replace( skml_table(), array(
        'email'        => $email,
        'status'       => 'pending',
        'token_hash'   => hash( 'sha256', $token ),
        'consent_text' => $consent_text,
        'ip_address'   => skml_client_ip(),
        'user_agent'   => substr( sanitize_text_field( wp_unslash( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' ) ), 0, 255 ),
        'created_at'   => $now,
        'confirmed_at' => null,
        'expires_at'   => $expires,
        'ml_synced_at' => null,
        'ml_error'     => '',
    ) );

    return array( 'id' => (int) $wpdb->insert_id, 'token' => $token );
}

function skml_confirm_row( $id ) {
    global $wpdb;
    $wpdb->update( skml_table(), array(
        'status'       => 'confirmed',
        'confirmed_at' => current_time( 'mysql' ),
        'token_hash'   => '',
    ), array( 'id' => (int) $id ) );
}

function skml_mark_ml_result( $id, $result ) {
    global $wpdb;
    if ( is_wp_error( $result ) ) {
        $wpdb->update( skml_table(), array(
            'ml_error' => substr( $result->get_error_message(), 0, 255 ),
        ), array( 'id' => (int) $id ) );
    } else {
        $wpdb->update( skml_table(), array(
            'ml_synced_at' => current_time( 'mysql' ),
            'ml_error'     => '',
        ), array( 'id' => (int) $id ) );
    }
}

/**
 * Daily cron: pending rows past their TTL are marked expired
 * (kept as rows — an expired attempt is still part of the audit trail).
 */
function skml_expire_pending() {
    global $wpdb;
    $table = skml_table();
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
        return;
    }
    $wpdb->query( $wpdb->prepare(
        "UPDATE {$table} SET status = 'expired', token_hash = '' WHERE status = 'pending' AND expires_at < %s",
        current_time( 'mysql' )
    ) );
}

/**
 * Daily cron: retry the MailerLite sync for confirmed rows where the API
 * call failed (or the token was missing). Bounded so a dead API doesn't
 * hammer itself forever — gives up after 7 days.
 */
function skml_retry_ml_sync() {
    global $wpdb;
    $table = skml_table();
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
        return;
    }
    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT id, email FROM {$table}
         WHERE status = 'confirmed' AND ml_synced_at IS NULL AND confirmed_at > %s
         LIMIT 20",
        gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - 7 * DAY_IN_SECONDS )
    ), ARRAY_A );

    foreach ( $rows as $row ) {
        skml_mark_ml_result( $row['id'], skml_push_subscriber( $row['email'] ) );
    }
}

function skml_subscriber_counts() {
    global $wpdb;
    $table = skml_table();
    $empty = array( 'pending' => 0, 'confirmed' => 0, 'expired' => 0 );
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
        return $empty;
    }
    $rows  = $wpdb->get_results( "SELECT status, COUNT(*) AS n FROM {$table} GROUP BY status", ARRAY_A );
    $counts = $empty;
    foreach ( $rows as $row ) {
        if ( isset( $counts[ $row['status'] ] ) ) {
            $counts[ $row['status'] ] = (int) $row['n'];
        }
    }
    return $counts;
}

function skml_subscriber_entries( $limit = 100 ) {
    global $wpdb;
    $table = skml_table();
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
        return array();
    }
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT id, email, status, consent_text, ip_address, created_at, confirmed_at, ml_synced_at, ml_error
         FROM {$table} ORDER BY created_at DESC LIMIT %d", (int) $limit
    ), ARRAY_A );
}
