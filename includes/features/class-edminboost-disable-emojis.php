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
		$settings = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$scope    = isset( $settings['scope'] ) ? $settings['scope'] : 'admin';

		if ( in_array( $scope, array( 'admin', 'both' ), true ) ) {
			add_action( 'admin_init', array( $this, 'disable_admin_emojis' ) );
		}

		if ( in_array( $scope, array( 'frontend', 'both' ), true ) ) {
			add_action( 'init', array( $this, 'disable_frontend_emojis' ) );
		}
	}

	/**
	 * Remove emoji scripts and styles from admin.
	 *
	 * @return void
	 */
	public function disable_admin_emojis() {
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	}

	/**
	 * Remove emoji scripts and styles from the front end.
	 *
	 * @return void
	 */
	public function disable_frontend_emojis() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}
}
