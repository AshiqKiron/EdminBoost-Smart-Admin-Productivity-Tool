<?php
/**
 * Build detection and Billing helpers (no feature gating).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium build flag and Billing page helpers. All plugin features are available without a license.
 */
class EDMINBOOST_Pro {

	/**
	 * Whether this install is the direct-download build (includes/pro/ bootstrap).
	 *
	 * WordPress.org packages omit includes/pro/ and never define EDMINBOOST_PREMIUM_BUILD.
	 *
	 * @return bool
	 */
	public static function is_direct_build() {
		return defined( 'EDMINBOOST_PREMIUM_BUILD' ) && EDMINBOOST_PREMIUM_BUILD;
	}

	/**
	 * Alias for is_direct_build() (legacy callers).
	 *
	 * @return bool
	 */
	public static function is_premium_build() {
		return self::is_direct_build();
	}

	/**
	 * Whether pro-only settings and upgrade prompts should appear in wp-admin.
	 *
	 * @return bool
	 */
	public static function shows_pro_settings_ui() {
		return false;
	}

	/**
	 * Whether a layout or theme preset should appear in admin pickers.
	 *
	 * @param string $preset_id Preset key.
	 * @param string $context   `layout` or `theme`.
	 * @return bool
	 */
	public static function include_preset_in_ui( $preset_id, $context = 'layout' ) {
		unset( $preset_id, $context );
		return true;
	}

	/**
	 * Whether a paid Pro/Agency plan is active (Billing display only — does not gate features).
	 *
	 * @return bool
	 */
	public static function is_active() {
		$plan = EDMINBOOST_Command_Center::get_active_billing_plan();

		return (bool) apply_filters(
			'edminboost_is_pro_active',
			in_array( $plan, array( 'pro', 'agency' ), true )
		);
	}

	/**
	 * Billing page URL for upgrade prompts.
	 *
	 * @return string
	 */
	public static function get_billing_url() {
		return admin_url(
			'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_BILLING
		);
	}

	/**
	 * Whether a system layout preset is available.
	 *
	 * @param string $preset_id Preset identifier.
	 * @return bool
	 */
	public static function is_layout_preset_available( $preset_id ) {
		unset( $preset_id );
		return true;
	}

	/**
	 * Whether a visual theme preset is available.
	 *
	 * @param string $preset_id Theme preset key.
	 * @return bool
	 */
	public static function is_theme_preset_available( $preset_id ) {
		unset( $preset_id );
		return true;
	}

	/**
	 * CSS class for a section wrapper (legacy — no pro lock classes).
	 *
	 * @param string $feature Optional feature key for data attribute.
	 * @return string
	 */
	public static function section_class( $feature = '' ) {
		unset( $feature );
		return '';
	}

	/**
	 * Data attribute for feature identification in JS (legacy no-op).
	 *
	 * @param string $feature Feature key.
	 * @return string
	 */
	public static function feature_attr( $feature ) {
		unset( $feature );
		return '';
	}

	/**
	 * Echo the pro feature data attribute (legacy no-op).
	 *
	 * @param string $feature Feature key.
	 * @return void
	 */
	public static function echo_feature_attr( $feature ) {
		unset( $feature );
	}

	/**
	 * Render a compact Pro badge (legacy no-op).
	 *
	 * @return void
	 */
	public static function render_badge() {
	}

	/**
	 * Render an upgrade prompt for a section (legacy no-op).
	 *
	 * @return void
	 */
	public static function render_upgrade_prompt() {
	}

	/**
	 * Pass-through after sanitization (no plan limits).
	 *
	 * @param array $settings Sanitized settings.
	 * @return array
	 */
	public static function enforce_plan_limits( $settings ) {
		return is_array( $settings ) ? $settings : array();
	}

	/**
	 * Pass-through Command Center config (no plan limits).
	 *
	 * @param array $cc Command Center settings.
	 * @return array
	 */
	public static function enforce_command_center_limits( $cc ) {
		return is_array( $cc ) ? $cc : EDMINBOOST_Command_Center::get_defaults();
	}

	/**
	 * Pass-through top bar items (no plan limits).
	 *
	 * @param array $items Top bar items.
	 * @return array
	 */
	public static function strip_pro_top_bar_items( $items ) {
		return is_array( $items ) ? $items : array();
	}

	/**
	 * Pass-through behavior settings (no plan limits).
	 *
	 * @param array $behavior Behavior settings.
	 * @return array
	 */
	public static function strip_pro_behavior( $behavior ) {
		return is_array( $behavior ) ? $behavior : EDMINBOOST_Command_Center::get_defaults()['behavior'];
	}

	/**
	 * Pass-through theme settings (no plan limits).
	 *
	 * @param array $theme Theme settings.
	 * @return array
	 */
	public static function strip_pro_theme( $theme ) {
		return is_array( $theme ) ? $theme : EDMINBOOST_Theme::get_defaults();
	}

	/**
	 * Pass-through Menu Studio settings (no plan limits).
	 *
	 * @param array $menu_studio Menu Studio settings.
	 * @return array
	 */
	public static function strip_pro_menu_studio( $menu_studio ) {
		return is_array( $menu_studio ) ? $menu_studio : EDMINBOOST_Command_Center::get_menu_studio_defaults();
	}

	/**
	 * Whether the site can save another custom layout preset.
	 *
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return bool
	 */
	public static function can_save_custom_preset( $cc_settings = null ) {
		unset( $cc_settings );
		return true;
	}
}
