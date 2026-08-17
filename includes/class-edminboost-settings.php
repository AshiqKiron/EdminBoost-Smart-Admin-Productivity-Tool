<?php
/**
 * Centralized plugin settings — defaults, retrieval, sanitization.
 *
 * Purpose: Single source of truth for the edminboost_settings option.
 *          Uses wp_options (no custom tables).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings helper.
 */
class EDMINBOOST_Settings {

	/**
	 * Settings option name.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'edminboost_settings';

	/**
	 * Installed version option name.
	 *
	 * @var string
	 */
	const VERSION_OPTION = 'edminboost_version';

	/**
	 * Settings API group identifier.
	 *
	 * @var string
	 */
	const SETTINGS_GROUP = 'edminboost_settings_group';

	/**
	 * Required capability for settings access.
	 *
	 * @var string
	 */
	const CAPABILITY = 'manage_options';

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		$defaults = array(
			'enabled'  => true,
			'features' => array(
				'hide_admin_notices' => false,
				'dashboard_widgets'  => array(
					'remove_welcome_panel'   => false,
					'remove_quick_press'     => false,
					'remove_activity'        => false,
					'remove_at_a_glance'     => false,
					'remove_site_health'     => false,
					'remove_wp_news'         => false,
				),
				'admin_menu'         => array(
					'hidden_items' => array(),
				),
				'admin_footer'       => array(
					'enabled' => false,
					'text'    => '',
				),
				'disable_emojis'     => false,
				'admin_bar'          => array(
					'hide_wp_logo'    => false,
					'hide_comments'   => false,
					'hide_new_content' => false,
					'hide_customize'  => false,
				),
			),
		);

		/**
		 * Filter default plugin settings.
		 *
		 * @param array $defaults Default settings.
		 */
		return apply_filters( 'edminboost_settings_defaults', $defaults );
	}

	/**
	 * Get merged plugin settings.
	 *
	 * @return array
	 */
	public static function get() {
		$defaults = self::get_defaults();
		$settings = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$settings = wp_parse_args( $settings, $defaults );

		if ( isset( $settings['features'] ) && is_array( $settings['features'] ) ) {
			$settings['features'] = wp_parse_args( $settings['features'], $defaults['features'] );

			foreach ( $defaults['features'] as $feature_key => $feature_defaults ) {
				if ( is_array( $feature_defaults ) && isset( $settings['features'][ $feature_key ] ) ) {
					$settings['features'][ $feature_key ] = wp_parse_args(
						$settings['features'][ $feature_key ],
						$feature_defaults
					);
				}
			}
		} else {
			$settings['features'] = $defaults['features'];
		}

		/**
		 * Filter plugin settings after merge.
		 *
		 * @param array $settings Merged settings.
		 */
		return apply_filters( 'edminboost_settings', $settings );
	}

	/**
	 * Whether the plugin is globally enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$settings = self::get();

		return ! empty( $settings['enabled'] );
	}

	/**
	 * Whether a feature is enabled.
	 *
	 * @param string $feature_id Feature identifier.
	 * @return bool
	 */
	public static function is_feature_enabled( $feature_id ) {
		if ( ! self::is_enabled() ) {
			return false;
		}

		$settings = self::get();
		$features = isset( $settings['features'] ) ? $settings['features'] : array();

		switch ( $feature_id ) {
			case 'hide_admin_notices':
			case 'disable_emojis':
				return ! empty( $features[ $feature_id ] );

			case 'dashboard_widgets':
				if ( empty( $features['dashboard_widgets'] ) || ! is_array( $features['dashboard_widgets'] ) ) {
					return false;
				}
				foreach ( $features['dashboard_widgets'] as $enabled ) {
					if ( ! empty( $enabled ) ) {
						return true;
					}
				}
				return false;

			case 'admin_menu':
				return ! empty( $features['admin_menu']['hidden_items'] );

			case 'admin_footer':
				return ! empty( $features['admin_footer']['enabled'] )
					&& '' !== trim( (string) $features['admin_footer']['text'] );

			case 'admin_bar':
				if ( empty( $features['admin_bar'] ) || ! is_array( $features['admin_bar'] ) ) {
					return false;
				}
				foreach ( $features['admin_bar'] as $enabled ) {
					if ( ! empty( $enabled ) ) {
						return true;
					}
				}
				return false;

			default:
				/**
				 * Filter whether a custom feature is enabled.
				 *
				 * @param bool   $enabled    Whether the feature is enabled.
				 * @param string $feature_id Feature identifier.
				 * @param array  $features   Feature settings.
				 */
				return (bool) apply_filters(
					'edminboost_is_feature_enabled',
					false,
					$feature_id,
					$features
				);
		}
	}

	/**
	 * Get feature-specific settings.
	 *
	 * @param string $feature_id Feature identifier.
	 * @return array
	 */
	public static function get_feature_settings( $feature_id ) {
		$settings = self::get();
		$features = isset( $settings['features'] ) ? $settings['features'] : array();

		if ( isset( $features[ $feature_id ] ) && is_array( $features[ $feature_id ] ) ) {
			return $features[ $feature_id ];
		}

		return array();
	}

	/**
	 * Sanitize settings input from the Settings API.
	 *
	 * @param array $input Raw settings input.
	 * @return array
	 */
	public static function sanitize( $input ) {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			return self::get();
		}

		if ( ! is_array( $input ) ) {
			return self::get();
		}

		$input = wp_unslash( $input );

		$sanitized = self::get();
		$defaults  = self::get_defaults();

		if ( isset( $input['enabled'] ) ) {
			$sanitized['enabled'] = (bool) $input['enabled'];
		} else {
			$sanitized['enabled'] = false;
		}

		if ( ! isset( $input['features'] ) || ! is_array( $input['features'] ) ) {
			return $sanitized;
		}

		$raw_features = $input['features'];

		$sanitized['features']['hide_admin_notices'] = ! empty( $raw_features['hide_admin_notices'] );
		$sanitized['features']['disable_emojis']     = ! empty( $raw_features['disable_emojis'] );

		$widget_keys = array_keys( $defaults['features']['dashboard_widgets'] );
		foreach ( $widget_keys as $widget_key ) {
			$sanitized['features']['dashboard_widgets'][ $widget_key ] = ! empty(
				$raw_features['dashboard_widgets'][ $widget_key ]
			);
		}

		$hidden_items = array();
		if ( ! empty( $raw_features['admin_menu']['hidden_items'] ) && is_array( $raw_features['admin_menu']['hidden_items'] ) ) {
			foreach ( $raw_features['admin_menu']['hidden_items'] as $menu_slug ) {
				$menu_slug = sanitize_text_field( wp_unslash( $menu_slug ) );
				if ( '' !== $menu_slug && preg_match( '/^[a-zA-Z0-9_\-\.?=&]+$/', $menu_slug ) ) {
					$hidden_items[] = $menu_slug;
				}
			}
		}
		$sanitized['features']['admin_menu']['hidden_items'] = array_values(
			array_unique(
				array_diff( $hidden_items, EDMINBOOST_Admin_Menu::get_protected_slugs() )
			)
		);

		$sanitized['features']['admin_footer']['enabled'] = ! empty( $raw_features['admin_footer']['enabled'] );
		$sanitized['features']['admin_footer']['text']    = isset( $raw_features['admin_footer']['text'] )
			? sanitize_text_field( wp_unslash( $raw_features['admin_footer']['text'] ) )
			: '';

		$bar_keys = array_keys( $defaults['features']['admin_bar'] );
		foreach ( $bar_keys as $bar_key ) {
			$sanitized['features']['admin_bar'][ $bar_key ] = ! empty(
				$raw_features['admin_bar'][ $bar_key ]
			);
		}

		/**
		 * Filter sanitized settings before save.
		 *
		 * @param array $sanitized Sanitized settings.
		 * @param array $input     Raw input.
		 */
		return apply_filters( 'edminboost_sanitize_settings', $sanitized, $input );
	}
}
