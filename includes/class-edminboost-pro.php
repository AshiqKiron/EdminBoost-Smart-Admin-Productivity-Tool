<?php
/**
 * Pro feature gating for the freemium plan split.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central helpers for Free vs Pro capability checks and enforcement.
 */
class EDMINBOOST_Pro {

	/**
	 * Maximum saved custom layout presets on the free plan.
	 */
	const FREE_CUSTOM_PRESET_LIMIT = 1;

	/**
	 * Scenario layout presets included in the free plan.
	 */
	const FREE_SCENARIO_PRESETS = array(
		'system_friend',
		'system_family',
	);

	/**
	 * Visual theme presets included in the free plan (Default, Midnight, Terminal, Custom).
	 */
	const FREE_THEME_PRESETS = array(
		'default',
		'midnight',
		'terminal',
		'custom',
	);

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
	 * On WordPress.org (no premium bootstrap) pro controls are omitted entirely.
	 *
	 * @return bool
	 */
	public static function shows_pro_settings_ui() {
		return self::is_premium_build();
	}

	/**
	 * Whether a layout or theme preset should appear in admin pickers.
	 *
	 * @param string $preset_id Preset key.
	 * @param string $context   `layout` or `theme`.
	 * @return bool
	 */
	public static function include_preset_in_ui( $preset_id, $context = 'layout' ) {
		if ( self::is_premium_build() ) {
			return true;
		}

		return 'theme' === $context
			? self::is_theme_preset_available( $preset_id )
			: self::is_layout_preset_available( $preset_id );
	}

	/**
	 * Whether Pro (or Agency) is active on this site.
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
	 * Whether a system layout preset is available on the current plan.
	 *
	 * @param string $preset_id Preset identifier.
	 * @return bool
	 */
	public static function is_layout_preset_available( $preset_id ) {
		$preset_id = sanitize_key( $preset_id );

		if ( self::is_active() ) {
			return true;
		}

		if ( in_array( $preset_id, array( 'default', 'custom' ), true ) ) {
			return true;
		}

		if ( 0 === strpos( $preset_id, 'custom_' ) ) {
			return true;
		}

		if ( in_array( $preset_id, self::FREE_SCENARIO_PRESETS, true ) ) {
			return true;
		}

		if ( isset( EDMINBOOST_Command_Center::get_role_system_presets()[ $preset_id ] ) ) {
			return true;
		}

		$system = EDMINBOOST_Command_Center::get_system_presets();

		if ( ! isset( $system[ $preset_id ] ) ) {
			return true;
		}

		$category = isset( $system[ $preset_id ]['category'] ) ? $system[ $preset_id ]['category'] : '';

		return 'scenario' !== $category;
	}

	/**
	 * Whether a visual theme preset is available on the current plan.
	 *
	 * @param string $preset_id Theme preset key.
	 * @return bool
	 */
	public static function is_theme_preset_available( $preset_id ) {
		if ( self::is_active() ) {
			return true;
		}

		return in_array( sanitize_key( $preset_id ), self::FREE_THEME_PRESETS, true );
	}

	/**
	 * CSS class for a pro-gated section wrapper.
	 *
	 * @param string $feature Optional feature key for data attribute.
	 * @return string
	 */
	public static function section_class( $feature = '' ) {
		unset( $feature );

		if ( ! self::shows_pro_settings_ui() ) {
			return '';
		}

		$classes = array( 'edminboost-pro-section' );

		if ( ! self::is_active() ) {
			$classes[] = 'is-pro-locked';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Data attribute for pro feature identification in JS.
	 *
	 * @param string $feature Feature key.
	 * @return string
	 */
	public static function feature_attr( $feature ) {
		if ( ! self::shows_pro_settings_ui() ) {
			return '';
		}

		return ' data-edminboost-pro-feature="' . esc_attr( sanitize_key( $feature ) ) . '"';
	}

	/**
	 * Echo the pro feature data attribute (legacy no-op).
	 *
	 * @param string $feature Feature key.
	 * @return void
	 */
	public static function echo_feature_attr( $feature ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in feature_attr().
		echo self::feature_attr( $feature );
	}

	/**
	 * Render a compact Pro badge for locked controls.
	 *
	 * @return void
	 */
	public static function render_badge() {
		if ( ! self::shows_pro_settings_ui() || self::is_active() ) {
			return;
		}

		echo '<span class="edminboost-pro-badge">' . esc_html__( 'Pro', 'edminboost-smart-admin-productivity-tool' ) . '</span>';
	}

	/**
	 * Render an upgrade prompt for a locked section.
	 *
	 * @return void
	 */
	public static function render_upgrade_prompt() {
		if ( ! self::shows_pro_settings_ui() || self::is_active() ) {
			return;
		}

		printf(
			'<p class="edminboost-pro-upgrade"><a href="%1$s">%2$s</a></p>',
			esc_url( self::get_billing_url() ),
			esc_html__( 'Upgrade to Pro to unlock this feature.', 'edminboost-smart-admin-productivity-tool' )
		);
	}

	/**
	 * Clamp saved settings to the active plan after sanitization (and on read).
	 *
	 * @param array $settings Sanitized settings.
	 * @return array
	 */
	public static function enforce_plan_limits( $settings ) {
		if ( self::is_active() || ! is_array( $settings ) ) {
			return $settings;
		}

		if ( isset( $settings['command_center'] ) && is_array( $settings['command_center'] ) ) {
			$settings['command_center'] = self::enforce_command_center_limits( $settings['command_center'] );
		}

		if ( isset( $settings['white_label'] ) && is_array( $settings['white_label'] ) ) {
			$settings['white_label'] = EDMINBOOST_White_Label::get_defaults();
		}

		if ( isset( $settings['features'] ) && is_array( $settings['features'] ) ) {
			$settings['features']['login_redirects'] = EDMINBOOST_Feature_Settings::get_defaults()['login_redirects'];
		}

		return $settings;
	}

	/**
	 * Clamp Command Center config to free-tier limits.
	 *
	 * @param array $cc Command Center settings.
	 * @return array
	 */
	public static function enforce_command_center_limits( $cc ) {
		$defaults = EDMINBOOST_Command_Center::get_defaults();

		$cc = wp_parse_args( $cc, $defaults );

		$cc['role_visibility'] = array();

		if ( isset( $cc['presets'] ) && is_array( $cc['presets'] ) ) {
			$cc['presets'] = self::limit_custom_presets( $cc['presets'] );
		}

		if ( ! self::is_layout_preset_available( $cc['default_preset'] ) ) {
			$cc['default_preset'] = $defaults['default_preset'];
		}

		if ( isset( $cc['role_assignments'] ) && is_array( $cc['role_assignments'] ) ) {
			foreach ( $cc['role_assignments'] as $role_key => $preset_id ) {
				if ( '' !== $preset_id && ! self::is_layout_preset_available( $preset_id ) ) {
					$cc['role_assignments'][ $role_key ] = '';
				}
			}
		}

		if ( isset( $cc['top_bar_items'] ) && is_array( $cc['top_bar_items'] ) ) {
			$cc['top_bar_items'] = self::strip_pro_top_bar_items( $cc['top_bar_items'] );
		}

		if ( isset( $cc['behavior'] ) && is_array( $cc['behavior'] ) ) {
			$cc['behavior'] = self::strip_pro_behavior( $cc['behavior'] );
		}

		if ( isset( $cc['theme'] ) && is_array( $cc['theme'] ) ) {
			$cc['theme'] = self::strip_pro_theme( $cc['theme'] );
		}

		if ( isset( $cc['menu_studio'] ) && is_array( $cc['menu_studio'] ) ) {
			$cc['menu_studio'] = self::strip_pro_menu_studio( $cc['menu_studio'] );
		}

		return $cc;
	}

	/**
	 * Keep only the first saved custom preset on free.
	 *
	 * @param array $presets Saved custom presets.
	 * @return array
	 */
	private static function limit_custom_presets( $presets ) {
		if ( count( $presets ) <= self::FREE_CUSTOM_PRESET_LIMIT ) {
			return $presets;
		}

		return array_slice( $presets, 0, self::FREE_CUSTOM_PRESET_LIMIT, true );
	}

	/**
	 * Remove pro-only top bar item options.
	 *
	 * @param array $items Top bar items.
	 * @return array
	 */
	public static function strip_pro_top_bar_items( $items ) {
		$stripped = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			if ( isset( $item['interaction'] ) && 'drawer' === $item['interaction'] ) {
				$item['interaction'] = 'redirect';
			}

			if ( ! empty( $item['badge_source'] ) ) {
				$item['badge_source'] = '';
			}

			$stripped[] = $item;
		}

		return $stripped;
	}

	/**
	 * Reset pro-only behavior fields to defaults.
	 *
	 * @param array $behavior Behavior settings.
	 * @return array
	 */
	public static function strip_pro_behavior( $behavior ) {
		$defaults = EDMINBOOST_Command_Center::get_defaults()['behavior'];
		$output   = wp_parse_args( $behavior, $defaults );

		if ( in_array( $output['drawer_width'], array( 'fullscreen', 'custom' ), true ) ) {
			$output['drawer_width'] = $defaults['drawer_width'];
		}

		$output['animation_speed']    = $defaults['animation_speed'];
		$output['badge_style']        = $defaults['badge_style'];
		$output['glassmorphism']      = false;
		$output['badge_refresh_rate'] = $defaults['badge_refresh_rate'];

		return $output;
	}

	/**
	 * Reset pro-only theme fields to defaults.
	 *
	 * @param array $theme Theme settings.
	 * @return array
	 */
	public static function strip_pro_theme( $theme ) {
		$defaults = EDMINBOOST_Theme::get_defaults();
		$output   = wp_parse_args( $theme, $defaults );

		if ( ! self::is_theme_preset_available( $output['preset'] ) ) {
			$output['preset']            = 'default';
			$output['use_custom_colors'] = false;
		}

		$output['schedule_dark_mode'] = false;
		$output['dark_mode_start']    = $defaults['dark_mode_start'];
		$output['dark_mode_end']      = $defaults['dark_mode_end'];

		return $output;
	}

	/**
	 * Remove pro-only Menu Studio options.
	 *
	 * @param array $menu_studio Menu Studio settings.
	 * @return array
	 */
	public static function strip_pro_menu_studio( $menu_studio ) {
		$defaults = EDMINBOOST_Command_Center::get_menu_studio_defaults();
		$output   = wp_parse_args( $menu_studio, $defaults );

		$output['custom_items'] = array();
		$output['display_mode'] = 'both';

		return $output;
	}

	/**
	 * Whether the site can save another custom layout preset.
	 *
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return bool
	 */
	public static function can_save_custom_preset( $cc_settings = null ) {
		if ( self::is_active() ) {
			return true;
		}

		if ( null === $cc_settings ) {
			$cc_settings = EDMINBOOST_Command_Center::get_settings();
		}

		$presets = isset( $cc_settings['presets'] ) && is_array( $cc_settings['presets'] )
			? $cc_settings['presets']
			: array();

		return count( $presets ) < self::FREE_CUSTOM_PRESET_LIMIT;
	}
}
