<?php
/**
 * Build detection and shared Command Center helpers (WordPress.org + premium).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared build helpers for Command Center admin UI.
 */
class EDMINBOOST_Plan {

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
	 * Whether a layout or theme preset should appear in admin pickers.
	 *
	 * @param string $preset_id Preset key.
	 * @param string $context   `layout` or `theme`.
	 * @return bool
	 */
	public static function include_preset_in_ui( $preset_id, $context = 'layout' ) {
		/**
		 * Filter whether a preset appears in admin pickers.
		 *
		 * @param bool   $include   Default true on the WordPress.org build.
		 * @param string $preset_id Preset key.
		 * @param string $context   `layout` or `theme`.
		 */
		return (bool) apply_filters( 'edminboost_include_preset_in_ui', true, $preset_id, $context );
	}

	/**
	 * Whether a system layout preset is available for the current site.
	 *
	 * @param string $preset_id Preset identifier.
	 * @return bool
	 */
	public static function is_layout_preset_available( $preset_id ) {
		/**
		 * Filter layout preset availability (premium unlicensed installs may restrict).
		 *
		 * @param bool   $available Default true on the WordPress.org build.
		 * @param string $preset_id Preset identifier.
		 */
		return (bool) apply_filters( 'edminboost_is_layout_preset_available', true, $preset_id );
	}

	/**
	 * Whether a visual theme preset is available for the current site.
	 *
	 * @param string $preset_id Theme preset key.
	 * @return bool
	 */
	public static function is_theme_preset_available( $preset_id ) {
		/**
		 * Filter theme preset availability (premium unlicensed installs may restrict).
		 *
		 * @param bool   $available Default true on the WordPress.org build.
		 * @param string $preset_id Theme preset key.
		 */
		return (bool) apply_filters( 'edminboost_is_theme_preset_available', true, $preset_id );
	}

	/**
	 * Whether licensed premium admin screens and partials should load.
	 *
	 * @return bool
	 */
	public static function has_licensed_admin_ui() {
		if ( class_exists( 'EDMINBOOST_Pro', false ) ) {
			return EDMINBOOST_Pro::has_licensed_admin_ui();
		}

		return (bool) apply_filters( 'edminboost_has_licensed_admin_ui', false );
	}

	/**
	 * Whether custom preset save/rename/duplicate actions are allowed on this build.
	 *
	 * @return bool
	 */
	public static function allows_custom_preset_actions() {
		if ( class_exists( 'EDMINBOOST_Pro', false ) ) {
			return EDMINBOOST_Pro::allows_custom_preset_actions();
		}

		return true;
	}

	/**
	 * Whether login redirects may run for the stored enabled flag.
	 *
	 * @param bool $enabled Stored enabled flag.
	 * @return bool
	 */
	public static function is_login_redirects_enabled( $enabled ) {
		if ( class_exists( 'EDMINBOOST_Plan_Limits', false ) ) {
			return EDMINBOOST_Plan_Limits::is_login_redirects_enabled( $enabled );
		}

		return (bool) apply_filters( 'edminboost_login_redirects_enabled', false, $enabled );
	}

	/**
	 * Include a licensed admin partial for an extension slot (no-op when unlicensed).
	 *
	 * @param string $slot Extension slot identifier.
	 * @return void
	 */
	public static function render_admin_extension( $slot ) {
		/**
		 * Render a licensed admin UI extension slot.
		 *
		 * @param string $slot Extension slot identifier.
		 */
		do_action( 'edminboost_admin_extension', $slot );
	}
}
