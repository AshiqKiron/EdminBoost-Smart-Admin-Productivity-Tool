<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package EdminBoost
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'edminboost_settings' );
delete_option( 'edminboost_version' );
