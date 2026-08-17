<?php
/**
 * Admin bar customization.
 *
 * Purpose: Remove selected WP_Admin_Bar nodes when enabled.
 * Hook: admin_bar_menu (priority 999)
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hides selected admin bar nodes.
 */
class EDMINBOOST_Admin_Bar extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'admin_bar';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Admin Bar';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Hide selected items from the WordPress admin bar.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_bar_menu', array( $this, 'customize_admin_bar' ), 999 );
	}

	/**
	 * Remove configured admin bar nodes.
	 *
	 * @param WP_Admin_Bar $admin_bar Admin bar instance.
	 * @return void
	 */
	public function customize_admin_bar( $admin_bar ) {
		$settings = EDMINBOOST_Settings::get_feature_settings( 'admin_bar' );

		if ( ! empty( $settings['hide_wp_logo'] ) ) {
			$admin_bar->remove_node( 'wp-logo' );
		}

		if ( ! empty( $settings['hide_comments'] ) ) {
			$admin_bar->remove_node( 'comments' );
		}

		if ( ! empty( $settings['hide_new_content'] ) ) {
			$admin_bar->remove_node( 'new-content' );
		}

		if ( ! empty( $settings['hide_customize'] ) ) {
			$admin_bar->remove_node( 'customize' );
		}
	}

	/**
	 * Get admin bar option labels for the settings UI.
	 *
	 * @return array
	 */
	public static function get_option_labels() {
		return array(
			'hide_wp_logo'     => __( 'WordPress logo', EDMINBOOST_TEXT_DOMAIN ),
			'hide_comments'    => __( 'Comments', EDMINBOOST_TEXT_DOMAIN ),
			'hide_new_content' => __( 'New content menu', EDMINBOOST_TEXT_DOMAIN ),
			'hide_customize'   => __( 'Customize link', EDMINBOOST_TEXT_DOMAIN ),
		);
	}
}
