<?php
/**
 * Plugin activation handler.
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
		$defaults = array(
			'enabled' => true,
		);

		if ( false === get_option( 'edminboost_settings', false ) ) {
			add_option( 'edminboost_settings', $defaults, '', false );
		}

		update_option( 'edminboost_version', EDMINBOOST_VERSION, false );
	}
}
