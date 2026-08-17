<?php
/**
 * Dashboard widget controls.
 *
 * Purpose: Remove selected core dashboard widgets and the welcome panel.
 * Hooks: wp_dashboard_setup, admin_init
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Removes selected core dashboard widgets.
 */
class EDMINBOOST_Dashboard extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'dashboard_widgets';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Dashboard Widgets';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Remove selected default dashboard widgets for a cleaner overview.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_dashboard_setup', array( $this, 'remove_widgets' ), 99 );
		add_action( 'admin_init', array( $this, 'remove_welcome_panel' ) );
	}

	/**
	 * Remove the welcome panel when configured.
	 *
	 * @return void
	 */
	public function remove_welcome_panel() {
		$settings = EDMINBOOST_Settings::get_feature_settings( 'dashboard_widgets' );

		if ( ! empty( $settings['remove_welcome_panel'] ) ) {
			remove_action( 'welcome_panel', 'wp_welcome_panel' );
		}
	}

	/**
	 * Remove configured dashboard meta boxes.
	 *
	 * @return void
	 */
	public function remove_widgets() {
		$settings = EDMINBOOST_Settings::get_feature_settings( 'dashboard_widgets' );

		if ( ! empty( $settings['remove_quick_press'] ) ) {
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		}

		if ( ! empty( $settings['remove_activity'] ) ) {
			remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
		}

		if ( ! empty( $settings['remove_at_a_glance'] ) ) {
			remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		}

		if ( ! empty( $settings['remove_site_health'] ) ) {
			remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		}

		if ( ! empty( $settings['remove_wp_news'] ) ) {
			remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		}
	}

	/**
	 * Get widget labels for the settings UI.
	 *
	 * @return array
	 */
	public static function get_widget_labels() {
		return array(
			'remove_welcome_panel' => __( 'Welcome panel', EDMINBOOST_TEXT_DOMAIN ),
			'remove_quick_press'   => __( 'Quick Draft', EDMINBOOST_TEXT_DOMAIN ),
			'remove_activity'      => __( 'Activity', EDMINBOOST_TEXT_DOMAIN ),
			'remove_at_a_glance'   => __( 'At a Glance', EDMINBOOST_TEXT_DOMAIN ),
			'remove_site_health'   => __( 'Site Health Status', EDMINBOOST_TEXT_DOMAIN ),
			'remove_wp_news'       => __( 'WordPress Events and News', EDMINBOOST_TEXT_DOMAIN ),
		);
	}
}
