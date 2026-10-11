<?php
/**
 * PHPUnit bootstrap — WP test suite (wp-phpunit) + diviskit-agent as the
 * only active plugin, against the dedicated `test` database.
 *
 * Run (inside the diviskit-test DDEV project):
 *   ddev exec "cd /var/www/html/wp-content/plugins/diviskit-agent && composer install"
 *   ddev exec "cd /var/www/html/wp-content/plugins/diviskit-agent && vendor/bin/phpunit"
 */

$_plugin_dir = dirname( __DIR__ );
$_tests_dir  = $_plugin_dir . '/vendor/wp-phpunit/wp-phpunit';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	fwrite( STDERR, "wp-phpunit is not installed. Run:\n" );
	fwrite( STDERR, "  ddev exec \"cd /var/www/html/wp-content/plugins/diviskit-agent && composer install\"\n" );
	exit( 1 );
}

if ( ! defined( 'WP_TESTS_CONFIG_FILE_PATH' ) ) {
	define( 'WP_TESTS_CONFIG_FILE_PATH', __DIR__ . '/wp-tests-config.php' );
}

require_once $_plugin_dir . '/vendor/autoload.php';
require_once $_tests_dir . '/includes/functions.php';

tests_add_filter( 'muplugins_loaded', static function () use ( $_plugin_dir ): void {
	require $_plugin_dir . '/diviskit-agent.php';
} );

require_once $_tests_dir . '/includes/bootstrap.php';

require_once __DIR__ . '/DK_TestCase.php';
