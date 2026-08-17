<?php
/**
 * Plugin deactivation handler.
 *
 * Purpose: Reserved for clearing scheduled hooks or transient cleanup on deactivate.
 *          Currently no cron events are registered.
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
		// No scheduled events to clear at this time.
	}
}
