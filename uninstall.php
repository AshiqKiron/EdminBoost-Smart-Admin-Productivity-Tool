<?php
/**
 * Uninstall cleanup.
 *
 * Purpose: Remove all plugin options when the plugin is deleted (not deactivated).
 *
 * @package EdminBoost
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-edminboost-settings.php';

delete_option( EDMINBOOST_Settings::OPTION_NAME );
delete_option( EDMINBOOST_Settings::VERSION_OPTION );
