<?php
/**
 * Plugin activation handler.
 *
 * Purpose: Seed default options on first install and record plugin version.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin activation.
 */
class EDMINBOOST_Activator {

	/**
	 * Run activation tasks.
	 *
	 * @return void
	 */
	public static function activate() {
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-settings.php';

		$defaults = EDMINBOOST_Settings::get_defaults();

		if ( false === get_option( EDMINBOOST_Settings::OPTION_NAME, false ) ) {
			add_option( EDMINBOOST_Settings::OPTION_NAME, $defaults, '', false );
		}

		update_option( EDMINBOOST_Settings::VERSION_OPTION, EDMINBOOST_VERSION, false );

		set_transient( 'edminboost_activation_redirect', 1, 30 );
	}
}
