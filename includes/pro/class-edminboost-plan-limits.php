<?php
/**
 * Billing-plan limits for unlicensed premium installs (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enforces Free vs Pro capability when Pro/Agency is inactive on the premium build.
 */
class EDMINBOOST_Plan_Limits {

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
	 * Whether login redirects may run (Pro/Agency only).
	 *
	 * @param bool $enabled Stored enabled flag.
	 * @return bool
	 */
	public static function is_login_redirects_enabled( $enabled ) {
		return $enabled && EDMINBOOST_Plan_Licensing::is_active();
	}

	/**
	 * Whether a layout or theme preset should appear in admin pickers.
	 *
	 * @param string $preset_id Preset key.
	 * @param string $context   `layout` or `theme`.
	 * @return bool
	 */
	public static function include_preset_in_ui( $preset_id, $context = 'layout' ) {
		if ( EDMINBOOST_Plan_Licensing::is_active() ) {
			return true;
		}

		return 'theme' === $context
			? self::is_theme_preset_available( $preset_id )
			: self::is_layout_preset_available( $preset_id );
	}

	/**
	 * Whether a system layout preset is available on the current plan.
	 *
	 * @param string $preset_id Preset identifier.
	 * @return bool
	 */
	public static function is_layout_preset_available( $preset_id ) {
		$preset_id = sanitize_key( $preset_id );

		if ( EDMINBOOST_Plan_Licensing::is_active() ) {
			return true;
		}

		if ( in_array( $preset_id, array( 'default', 'custom' ), true ) ) {
			return true;
		}

		if ( 0 === strpos( $preset_id, 'custom_' ) ) {
			return EDMINBOOST_Plan_Licensing::is_active();
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
		if ( EDMINBOOST_Plan_Licensing::is_active() ) {
			return true;
		}

		return in_array( sanitize_key( $preset_id ), self::FREE_THEME_PRESETS, true );
	}

	/**
	 * Effective Command Center config when Pro/Agency is inactive.
	 *
	 * @param array $cc Stored Command Center settings.
	 * @return array
	 */
	public static function resolve_command_center( $cc ) {
		if ( EDMINBOOST_Plan_Licensing::is_active() || ! is_array( $cc ) ) {
			return $cc;
		}

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
			$cc['top_bar_items'] = self::resolve_top_bar_items_for_inactive_plan( $cc['top_bar_items'] );
		}

		if ( isset( $cc['behavior'] ) && is_array( $cc['behavior'] ) ) {
			$cc['behavior'] = self::resolve_behavior_for_inactive_plan( $cc['behavior'] );
		}

		if ( isset( $cc['theme'] ) && is_array( $cc['theme'] ) ) {
			$cc['theme'] = self::resolve_theme_for_inactive_plan( $cc['theme'] );
		}

		if ( isset( $cc['menu_studio'] ) && is_array( $cc['menu_studio'] ) ) {
			$cc['menu_studio'] = self::resolve_menu_studio_for_inactive_plan( $cc['menu_studio'] );
		}

		return $cc;
	}

	/**
	 * Clamp saved settings to the active plan after sanitization (and on read).
	 *
	 * @param array $settings Sanitized settings.
	 * @return array
	 */
	public static function enforce_plan_limits( $settings ) {
		if ( EDMINBOOST_Plan_Licensing::is_active() || ! is_array( $settings ) ) {
			return $settings;
		}

		if ( isset( $settings['command_center'] ) && is_array( $settings['command_center'] ) ) {
			$settings['command_center'] = self::resolve_command_center( $settings['command_center'] );
		}

		if ( isset( $settings['white_label'] ) && is_array( $settings['white_label'] ) ) {
			$settings['white_label'] = EDMINBOOST_Settings::get_white_label_defaults();
		}

		if ( isset( $settings['features'] ) && is_array( $settings['features'] ) ) {
			$settings['features']['login_redirects'] = EDMINBOOST_Feature_Settings::get_defaults()['login_redirects'];
		}

		return $settings;
	}

	/**
	 * Saved custom layout presets are Pro/Agency only.
	 *
	 * @param array $presets Saved custom presets.
	 * @return array
	 */
	private static function limit_custom_presets( $presets ) {
		return array();
	}

	/**
	 * Top bar items available on the free billing plan.
	 *
	 * @param array $items Top bar items.
	 * @return array
	 */
	private static function resolve_top_bar_items_for_inactive_plan( $items ) {
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
	 * Behavior settings available on the free billing plan.
	 *
	 * @param array $behavior Behavior settings.
	 * @return array
	 */
	private static function resolve_behavior_for_inactive_plan( $behavior ) {
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
	 * Theme settings available on the free billing plan.
	 *
	 * @param array $theme Theme settings.
	 * @return array
	 */
	private static function resolve_theme_for_inactive_plan( $theme ) {
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
	 * Menu Studio settings available on the free billing plan.
	 *
	 * @param array $menu_studio Menu Studio settings.
	 * @return array
	 */
	private static function resolve_menu_studio_for_inactive_plan( $menu_studio ) {
		$defaults = EDMINBOOST_Command_Center::get_menu_studio_defaults();
		$output   = wp_parse_args( $menu_studio, $defaults );

		$output['custom_items'] = array();
		$output['display_mode'] = 'both';

		return $output;
	}
}
