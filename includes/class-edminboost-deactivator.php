<?php
/**
 * Plugin deactivation handler.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin deactivation.
 */
class EDMINBOOST_Deactivator {

	/**
	 * Run deactivation tasks.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Reserved for scheduled events or temporary cleanup.
	}
}
