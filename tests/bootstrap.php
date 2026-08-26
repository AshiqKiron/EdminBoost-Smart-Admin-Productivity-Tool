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
 */
function _edminboost_manually_load_plugin() {
	require dirname( __DIR__ ) . '/edminboost-smart-admin-productivity-tool.php';
}

tests_add_filter( 'muplugins_loaded', '_edminboost_manually_load_plugin' );

require $_tests_dir . '/bootstrap.php';
