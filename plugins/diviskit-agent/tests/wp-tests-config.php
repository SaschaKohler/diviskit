<?php
/**
 * wp-tests-config for the Diviskit Agent suite (wp-phpunit).
 *
 * DDEV defaults — everything is overridable via env vars
 * (DK_TEST_DB_*, DK_TEST_DB_HOST, DK_TESTS_ABSPATH, DK_TESTS_DOMAIN)
 * so the same file works outside DDEV (CI, wp-env, …).
 *
 * The suite installs a fresh WordPress into the `test` database
 * (created by DDEV alongside `db`). It drops/recreates wp_* tables
 * there — the dev database `db` is never touched.
 */

define( 'DB_NAME', getenv( 'DK_TEST_DB_NAME' ) ?: 'test' );
define( 'DB_USER', getenv( 'DK_TEST_DB_USER' ) ?: 'db' );
define( 'DB_PASSWORD', getenv( 'DK_TEST_DB_PASS' ) ?: 'db' );
define( 'DB_HOST', getenv( 'DK_TEST_DB_HOST' ) ?: 'db' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wp_';

define( 'ABSPATH', rtrim( getenv( 'DK_TESTS_ABSPATH' ) ?: '/var/www/html/', '/' ) . '/' );

define( 'WP_TESTS_DOMAIN', getenv( 'DK_TESTS_DOMAIN' ) ?: 'diviskit-test.ddev.site' );
define( 'WP_TESTS_EMAIL', 'diviskit-tests@example.org' );
define( 'WP_TESTS_TITLE', 'Diviskit Agent Tests' );

define( 'WP_PHP_BINARY', PHP_BINARY );

define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills' );

define( 'WP_DEBUG', true );
