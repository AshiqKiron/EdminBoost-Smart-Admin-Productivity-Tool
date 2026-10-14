<?php
/**
 * PHPUnit bootstrap for EdminBoost.
 *
 * @package EdminBoost
 */

define( 'WP_TESTS_CONFIG_FILE_PATH', __DIR__ . '/wp-tests-config.php' );

if ( ! file_exists( WP_TESTS_CONFIG_FILE_PATH ) ) {
	echo 'Copy tests/wp-tests-config.sample.php to tests/wp-tests-config.php and adjust paths.' . PHP_EOL;
	exit( 1 );
}

$_tests_dir = dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit/includes';

require_once $_tests_dir . '/functions.php';

/**
 * Load the plugin before tests run.
 *
 * Simulates the premium direct-download build when includes/pro/ is present (PHPUnit dev tree).
 * WordPress.org packages omit includes/pro/ and never define EDMINBOOST_PREMIUM_BUILD.
 */
function _edminboost_manually_load_plugin() {
	$plugin_root = dirname( __DIR__ );
	$premium     = $plugin_root . '/includes/pro/edminboost-premium.php';

	if ( '1' !== getenv( 'EDMINBOOST_TEST_WPORG' ) && is_readable( $premium ) ) {
		require_once $premium;
	}

	if ( ! defined( 'EDMINBOOST_SKIP_AUTO_RUN' ) ) {
		define( 'EDMINBOOST_SKIP_AUTO_RUN', true );
	}

	if ( ! defined( 'EDMINBOOST_DISABLE_LOCALHOST' ) ) {
		define( 'EDMINBOOST_DISABLE_LOCALHOST', true );
	}

	require $plugin_root . '/edminboost-admin-customization.php';

	edminboost_run_plugin();
}

tests_add_filter( 'muplugins_loaded', '_edminboost_manually_load_plugin' );

require $_tests_dir . '/bootstrap.php';
