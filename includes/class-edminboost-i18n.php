<?php
/**
 * Internationalization handler.
 *
 * Purpose: Load translation files from the /languages directory on plugins_loaded.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads the plugin text domain.
 */
class EDMINBOOST_I18n {

	/**
	 * Load plugin text domain.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			EDMINBOOST_TEXT_DOMAIN,
			false,
			dirname( EDMINBOOST_PLUGIN_BASENAME ) . '/languages'
		);
	}
}
