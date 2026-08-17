<?php
/**
 * Disable emoji scripts in admin.
 *
 * Purpose: Remove WordPress emoji detection hooks from the admin area.
 * Hook: admin_init
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Removes WordPress emoji detection scripts from the admin area.
 */
class EDMINBOOST_Disable_Emojis extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'disable_emojis';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Disable Emojis';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Remove emoji detection scripts from the admin area for a lighter page load.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', array( $this, 'disable_emojis' ) );
	}

	/**
	 * Remove emoji scripts and styles from admin.
	 *
	 * @return void
	 */
	public function disable_emojis() {
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	}
}
