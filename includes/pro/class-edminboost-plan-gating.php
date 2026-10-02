<?php
/**
 * Premium build: register Pro runtime modules when licensed (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pro runtime registration — not shipped in the WordPress.org free package.
 */
class EDMINBOOST_Plan_Gating {

	/**
	 * Register premium-only runtime hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'init', array( __CLASS__, 'register_pro_runtime_modules' ), 5 );

		add_filter( 'edminboost_settings', array( 'EDMINBOOST_Plan_Limits', 'enforce_plan_limits' ), 20 );
		add_filter(
			'edminboost_sanitize_settings',
			static function ( $sanitized ) {
				return EDMINBOOST_Plan_Limits::enforce_plan_limits( $sanitized );
			},
			20
		);

		add_filter( 'edminboost_command_center_merged_settings', array( __CLASS__, 'filter_command_center_merged_settings' ), 10, 1 );

		add_filter( 'edminboost_include_preset_in_ui', array( __CLASS__, 'filter_include_preset_in_ui' ), 10, 3 );
		add_filter( 'edminboost_is_layout_preset_available', array( __CLASS__, 'filter_is_layout_preset_available' ), 10, 2 );
		add_filter( 'edminboost_is_theme_preset_available', array( __CLASS__, 'filter_is_theme_preset_available' ), 10, 2 );
	}

	/**
	 * @param array $merged Merged Command Center settings.
	 * @return array
	 */
	public static function filter_command_center_merged_settings( $merged ) {
		if ( EDMINBOOST_Plan_Licensing::is_active() || ! is_array( $merged ) ) {
			return $merged;
		}

		return EDMINBOOST_Plan_Limits::resolve_command_center( $merged );
	}

	/**
	 * @param bool   $include   Default include flag.
	 * @param string $preset_id Preset key.
	 * @param string $context   `layout` or `theme`.
	 * @return bool
	 */
	public static function filter_include_preset_in_ui( $include, $preset_id, $context ) {
		if ( EDMINBOOST_Plan_Licensing::is_active() ) {
			return $include;
		}

		return EDMINBOOST_Plan_Limits::include_preset_in_ui( $preset_id, $context );
	}

	/**
	 * @param bool   $available Default availability.
	 * @param string $preset_id Preset identifier.
	 * @return bool
	 */
	public static function filter_is_layout_preset_available( $available, $preset_id ) {
		if ( EDMINBOOST_Plan_Licensing::is_active() ) {
			return $available;
		}

		return EDMINBOOST_Plan_Limits::is_layout_preset_available( $preset_id );
	}

	/**
	 * @param bool   $available Default availability.
	 * @param string $preset_id Theme preset key.
	 * @return bool
	 */
	public static function filter_is_theme_preset_available( $available, $preset_id ) {
		if ( EDMINBOOST_Plan_Licensing::is_active() ) {
			return $available;
		}

		return EDMINBOOST_Plan_Limits::is_theme_preset_available( $preset_id );
	}

	/**
	 * Register Pro-only modules when licensed.
	 *
	 * @return void
	 */
	public static function register_pro_runtime_modules() {
		if ( ! EDMINBOOST_Plan_Licensing::is_active() ) {
			return;
		}

		if ( class_exists( 'EDMINBOOST_White_Label', false ) ) {
			EDMINBOOST_White_Label::register_hooks();
		}
	}
}
