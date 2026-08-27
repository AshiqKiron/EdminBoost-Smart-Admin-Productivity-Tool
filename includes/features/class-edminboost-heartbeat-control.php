<?php
/**
 * Heartbeat API interval control.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adjusts or disables Heartbeat in admin, editor, and front-end contexts.
 */
class EDMINBOOST_Heartbeat_Control extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'heartbeat_control';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Heartbeat Control';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Modify or disable the WordPress Heartbeat API by context.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'heartbeat_settings', array( $this, 'filter_settings' ) );
		add_action( 'init', array( $this, 'maybe_disable_heartbeat' ), 1 );
	}

	/**
	 * Disable heartbeat entirely in configured contexts.
	 *
	 * @return void
	 */
	public function maybe_disable_heartbeat() {
		$settings = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$context  = $this->get_current_context();

		if ( 'disable' === $this->get_mode_for_context( $settings, $context ) ) {
			wp_deregister_script( 'heartbeat' );
		}
	}

	/**
	 * Adjust heartbeat interval.
	 *
	 * @param array $settings Heartbeat settings.
	 * @return array
	 */
	public function filter_settings( $settings ) {
		$feature  = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$context  = $this->get_current_context();
		$mode     = $this->get_mode_for_context( $feature, $context );

		if ( 'slow' === $mode ) {
			$settings['interval'] = 60;
		}

		return $settings;
	}

	/**
	 * Determine current heartbeat context.
	 *
	 * @return string admin|editor|frontend
	 */
	private function get_current_context() {
		if ( ! is_admin() ) {
			return 'frontend';
		}

		global $pagenow;

		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return 'editor';
		}

		return 'admin';
	}

	/**
	 * Get configured mode for a context.
	 *
	 * @param array  $settings Feature settings.
	 * @param string $context  Context key.
	 * @return string
	 */
	private function get_mode_for_context( $settings, $context ) {
		return isset( $settings[ $context ] ) ? $settings[ $context ] : 'default';
	}
}
