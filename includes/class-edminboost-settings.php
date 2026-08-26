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
			'enabled'         => true,
			'command_center'  => EDMINBOOST_Command_Center::get_defaults(),
			'features'        => array(
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

		if ( isset( $settings['command_center'] ) && is_array( $settings['command_center'] ) ) {
			$settings['command_center'] = wp_parse_args(
				$settings['command_center'],
				$defaults['command_center']
			);

			if ( isset( $settings['command_center']['behavior'] ) && is_array( $settings['command_center']['behavior'] ) ) {
				$settings['command_center']['behavior'] = wp_parse_args(
					$settings['command_center']['behavior'],
					$defaults['command_center']['behavior']
				);
			}
		} else {
			$settings['command_center'] = $defaults['command_center'];
		}

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
		}

		if ( isset( $input['command_center'] ) && is_array( $input['command_center'] ) ) {
			$sanitized['command_center'] = self::sanitize_command_center(
				$input['command_center'],
				$sanitized
			);
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

	/**
	 * Sanitize Command Center settings input.
	 *
	 * @param array $raw       Raw command_center input.
	 * @param array $sanitized Current sanitized settings (for merge context).
	 * @return array
	 */
	private static function sanitize_command_center( $raw, $sanitized ) {
		$defaults = EDMINBOOST_Command_Center::get_defaults();
		$current  = isset( $sanitized['command_center'] ) && is_array( $sanitized['command_center'] )
			? $sanitized['command_center']
			: $defaults;
		$output   = wp_parse_args( $current, $defaults );

		if ( isset( $raw['onboarding_completed'] ) ) {
			$output['onboarding_completed'] = ! empty( $raw['onboarding_completed'] );
		}

		$allowed_personas = array_keys( EDMINBOOST_Command_Center::get_personas() );
		if ( isset( $raw['persona'] ) ) {
			$persona = sanitize_key( $raw['persona'] );
			$output['persona'] = in_array( $persona, $allowed_personas, true ) ? $persona : '';
		}

		$allowed_skins = array_keys( EDMINBOOST_Command_Center::get_look_skins() );

		if ( ! empty( $raw['_apply_look_skin'] ) && empty( $raw['_keep_advanced_behavior'] ) ) {
			$skin_id = sanitize_key( $raw['_apply_look_skin'] );
			$skins   = EDMINBOOST_Command_Center::get_look_skins();
			if ( isset( $skins[ $skin_id ] ) ) {
				$output['behavior']  = self::sanitize_behavior( $skins[ $skin_id ]['behavior'] );
				$output['look_skin'] = $skin_id;
				$output['onboarding_completed'] = true;
			}
		}

		if ( isset( $raw['look_skin'] ) ) {
			$look_skin = sanitize_key( $raw['look_skin'] );
			$output['look_skin'] = in_array( $look_skin, $allowed_skins, true ) ? $look_skin : '';
		}

		if ( ! empty( $raw['_apply_preset'] ) ) {
			$preset_id = sanitize_key( $raw['_apply_preset'] );
			$items     = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $preset_id );
			if ( ! empty( $items ) ) {
				$output['top_bar_items']        = self::sanitize_top_bar_items( $items );
				$output['default_preset']       = $preset_id;
				$output['onboarding_completed'] = true;

				$all_presets = EDMINBOOST_Command_Center::get_all_presets();
				if ( ! empty( $all_presets[ $preset_id ]['persona'] ) ) {
					$persona = sanitize_key( $all_presets[ $preset_id ]['persona'] );
					if ( in_array( $persona, $allowed_personas, true ) ) {
						$output['persona'] = $persona;
					}
				}
			}
		}

		if ( isset( $raw['default_preset'] ) ) {
			$output['default_preset'] = sanitize_key( $raw['default_preset'] );
		}

		if ( ! empty( $raw['_layout_studio_save'] ) ) {
			$output['top_bar_items'] = isset( $raw['top_bar_items'] ) && is_array( $raw['top_bar_items'] )
				? self::sanitize_top_bar_items( $raw['top_bar_items'] )
				: array();
		} elseif ( isset( $raw['top_bar_items'] ) && is_array( $raw['top_bar_items'] ) ) {
			$output['top_bar_items'] = self::sanitize_top_bar_items( $raw['top_bar_items'] );
		}

		if ( isset( $raw['role_assignments'] ) && is_array( $raw['role_assignments'] ) ) {
			$assignments = array();
			$all_presets = array_keys( EDMINBOOST_Command_Center::get_all_presets() );

			foreach ( $raw['role_assignments'] as $role_key => $preset_id ) {
				$role_key  = sanitize_key( $role_key );
				$preset_id = sanitize_key( $preset_id );

				if ( '' === $role_key ) {
					continue;
				}

				if ( '' === $preset_id || in_array( $preset_id, $all_presets, true ) ) {
					$assignments[ $role_key ] = $preset_id;
				}
			}

			$output['role_assignments'] = $assignments;
		}

		if ( isset( $raw['role_visibility'] ) && is_array( $raw['role_visibility'] ) ) {
			$output['role_visibility'] = self::sanitize_role_visibility(
				$raw['role_visibility'],
				$output['top_bar_items']
			);
		}

		if ( isset( $raw['behavior'] ) && is_array( $raw['behavior'] ) ) {
			if ( empty( $raw['_apply_look_skin'] ) || ! empty( $raw['_keep_advanced_behavior'] ) ) {
				$output['behavior'] = self::sanitize_behavior( $raw['behavior'] );

				if ( ! empty( $raw['_keep_advanced_behavior'] ) ) {
					$output['look_skin'] = '';
					$output['onboarding_completed'] = true;
				}
			}
		}

		if ( isset( $raw['theme'] ) && is_array( $raw['theme'] ) ) {
			$output['theme'] = EDMINBOOST_Theme::sanitize( $raw['theme'] );
		}

		if ( isset( $raw['presets'] ) && is_array( $raw['presets'] ) ) {
			$output['presets'] = self::sanitize_custom_presets( $raw['presets'] );
		}

		if ( ! empty( $raw['_save_custom_preset'] ) && is_array( $raw['_save_custom_preset'] ) ) {
			$output['presets'] = self::sanitize_custom_presets(
				array_merge(
					isset( $output['presets'] ) && is_array( $output['presets'] ) ? $output['presets'] : array(),
					self::build_custom_preset_from_request( $raw['_save_custom_preset'], $output['top_bar_items'] )
				)
			);
		}

		if ( ! empty( $raw['_duplicate_preset'] ) ) {
			$source_id = sanitize_key( $raw['_duplicate_preset'] );
			$duplicate = self::duplicate_custom_preset( $source_id, $output );
			if ( ! empty( $duplicate ) ) {
				$existing = isset( $output['presets'] ) && is_array( $output['presets'] ) ? $output['presets'] : array();
				$output['presets'] = self::sanitize_custom_presets( array_merge( $existing, $duplicate ) );
			}
		}

		if ( ! empty( $raw['_mark_setup_complete'] ) ) {
			$output['onboarding_completed'] = true;
		}

		if ( ! empty( $raw['_menu_studio_save'] ) ) {
			$output['menu_studio'] = self::sanitize_menu_studio(
				isset( $raw['menu_studio'] ) && is_array( $raw['menu_studio'] ) ? $raw['menu_studio'] : array()
			);
		} elseif ( isset( $raw['menu_studio'] ) && is_array( $raw['menu_studio'] ) ) {
			$output['menu_studio'] = self::sanitize_menu_studio( $raw['menu_studio'], $output['menu_studio'] ?? null );
		}

		return $output;
	}

	/**
	 * Sanitize Menu Studio settings.
	 *
	 * @param array      $raw     Raw menu_studio input.
	 * @param array|null $current Existing menu_studio settings for partial merge.
	 * @return array
	 */
	private static function sanitize_menu_studio( $raw, $current = null ) {
		$defaults = EDMINBOOST_Command_Center::get_menu_studio_defaults();
		$output   = wp_parse_args( is_array( $current ) ? $current : array(), $defaults );

		if ( isset( $raw['enabled'] ) ) {
			$output['enabled'] = ! empty( $raw['enabled'] );
		}

		if ( isset( $raw['use_colors'] ) ) {
			$output['use_colors'] = ! empty( $raw['use_colors'] );
		}

		if ( isset( $raw['order'] ) && is_array( $raw['order'] ) ) {
			$order = array();
			foreach ( $raw['order'] as $slug ) {
				$slug = sanitize_text_field( wp_unslash( (string) $slug ) );
				if ( '' !== $slug && ! in_array( $slug, $order, true ) ) {
					$order[] = $slug;
				}
			}
			$output['order'] = $order;
		}

		if ( isset( $raw['hidden_items'] ) && is_array( $raw['hidden_items'] ) ) {
			$hidden = array();
			foreach ( $raw['hidden_items'] as $slug ) {
				$slug = sanitize_text_field( wp_unslash( (string) $slug ) );
				if ( '' === $slug || in_array( $slug, EDMINBOOST_Admin_Menu::get_protected_slugs(), true ) ) {
					continue;
				}
				if ( ! in_array( $slug, $hidden, true ) ) {
					$hidden[] = $slug;
				}
			}
			$output['hidden_items'] = $hidden;
		}

		if ( isset( $raw['submenu_order'] ) && is_array( $raw['submenu_order'] ) ) {
			$submenu_order = array();
			foreach ( $raw['submenu_order'] as $parent_slug => $children ) {
				$parent_slug = sanitize_text_field( wp_unslash( (string) $parent_slug ) );
				if ( '' === $parent_slug || ! is_array( $children ) ) {
					continue;
				}

				$child_order = array();
				foreach ( $children as $child_slug ) {
					$child_slug = sanitize_text_field( wp_unslash( (string) $child_slug ) );
					if ( '' !== $child_slug && ! in_array( $child_slug, $child_order, true ) ) {
						$child_order[] = $child_slug;
					}
				}

				$submenu_order[ $parent_slug ] = $child_order;
			}
			$output['submenu_order'] = $submenu_order;
		}

		if ( isset( $raw['custom_items'] ) && is_array( $raw['custom_items'] ) ) {
			$output['custom_items'] = self::sanitize_menu_studio_custom_items( $raw['custom_items'] );
		}

		if ( isset( $raw['colors'] ) && is_array( $raw['colors'] ) ) {
			$colors  = wp_parse_args( $output['colors'], $defaults['colors'] );
			$allowed = array_keys( $defaults['colors'] );

			foreach ( $allowed as $color_key ) {
				if ( ! isset( $raw['colors'][ $color_key ] ) ) {
					continue;
				}

				$colors[ $color_key ] = EDMINBOOST_Theme::sanitize_hex_color( $raw['colors'][ $color_key ] );
			}

			$output['colors'] = $colors;
		}

		return $output;
	}

	/**
	 * Sanitize Menu Studio custom sidebar links.
	 *
	 * @param array $items Raw custom items.
	 * @return array
	 */
	private static function sanitize_menu_studio_custom_items( $items ) {
		$sanitized     = array();
		$allowed_icons = EDMINBOOST_Command_Center::get_dashicon_options();
		$seen_ids      = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$id = isset( $item['id'] ) ? sanitize_key( $item['id'] ) : '';
			if ( '' === $id ) {
				$id = 'custom_' . wp_generate_password( 8, false, false );
			}

			if ( in_array( $id, $seen_ids, true ) ) {
				continue;
			}

			$label = isset( $item['label'] ) ? sanitize_text_field( wp_unslash( $item['label'] ) ) : '';
			$path  = isset( $item['path'] ) ? sanitize_text_field( wp_unslash( $item['path'] ) ) : '';
			$path  = ltrim( $path, '/' );

			if ( '' === $label || '' === $path || preg_match( '#^https?://#i', $path ) ) {
				continue;
			}

			if ( ! preg_match( '#^[a-zA-Z0-9_\-\./?=&%#]+$#', $path ) ) {
				continue;
			}

			$icon = isset( $item['icon'] ) ? sanitize_text_field( wp_unslash( $item['icon'] ) ) : 'dashicons-admin-links';
			if ( false === strpos( $icon, 'dashicons-' ) ) {
				$icon = 'dashicons-' . $icon;
			}
			if ( ! in_array( $icon, $allowed_icons, true ) ) {
				$icon = 'dashicons-admin-links';
			}

			$parent = isset( $item['parent'] ) ? sanitize_text_field( wp_unslash( $item['parent'] ) ) : '';

			$sanitized[] = array(
				'id'     => $id,
				'label'  => $label,
				'path'   => $path,
				'icon'   => $icon,
				'parent' => $parent,
			);

			$seen_ids[] = $id;
		}

		return $sanitized;
	}

	/**
	 * Build a custom preset entry from a save request.
	 *
	 * @param array $raw           Raw save payload.
	 * @param array $top_bar_items Current top bar items.
	 * @return array
	 */
	private static function build_custom_preset_from_request( $raw, $top_bar_items ) {
		$name = isset( $raw['name'] ) ? sanitize_text_field( wp_unslash( $raw['name'] ) ) : '';

		if ( '' === $name || empty( $top_bar_items ) ) {
			return array();
		}

		$id = 'custom_' . wp_generate_password( 8, false, false );

		return array(
			$id => array(
				'name'          => $name,
				'description'   => isset( $raw['description'] ) ? sanitize_text_field( wp_unslash( $raw['description'] ) ) : '',
				'system'        => false,
				'top_bar_items' => $top_bar_items,
			),
		);
	}

	/**
	 * Duplicate a custom preset.
	 *
	 * @param string $source_id Source preset id.
	 * @param array  $output    Current CC output.
	 * @return array
	 */
	private static function duplicate_custom_preset( $source_id, $output ) {
		$custom = isset( $output['presets'] ) && is_array( $output['presets'] ) ? $output['presets'] : array();
		$all    = array_merge( EDMINBOOST_Command_Center::get_system_presets(), $custom );

		if ( ! isset( $all[ $source_id ] ) || ! empty( $all[ $source_id ]['system'] ) ) {
			return array();
		}

		$source = $all[ $source_id ];
		$id     = 'custom_' . wp_generate_password( 8, false, false );
		$name   = isset( $source['name'] ) ? $source['name'] : $source_id;

		return array(
			$id => array(
				'name'          => sprintf(
					/* translators: %s: preset name */
					__( '%s (Copy)', EDMINBOOST_TEXT_DOMAIN ),
					$name
				),
				'description'   => isset( $source['description'] ) ? $source['description'] : '',
				'system'        => false,
				'top_bar_items' => isset( $source['top_bar_items'] ) && is_array( $source['top_bar_items'] )
					? $source['top_bar_items']
					: ( isset( $output['top_bar_items'] ) ? $output['top_bar_items'] : array() ),
			),
		);
	}

	/**
	 * Sanitize saved custom presets.
	 *
	 * @param array $presets Raw presets.
	 * @return array
	 */
	private static function sanitize_custom_presets( $presets ) {
		$sanitized = array();

		foreach ( $presets as $preset_id => $preset ) {
			if ( ! is_array( $preset ) || ! empty( $preset['system'] ) ) {
				continue;
			}

			$preset_id = sanitize_key( $preset_id );
			if ( '' === $preset_id || 0 !== strpos( $preset_id, 'custom_' ) ) {
				continue;
			}

			$name = isset( $preset['name'] ) ? sanitize_text_field( wp_unslash( $preset['name'] ) ) : '';
			if ( '' === $name ) {
				continue;
			}

			$items = isset( $preset['top_bar_items'] ) && is_array( $preset['top_bar_items'] )
				? self::sanitize_top_bar_items( $preset['top_bar_items'] )
				: array();

			if ( empty( $items ) ) {
				continue;
			}

			$sanitized[ $preset_id ] = array(
				'name'          => $name,
				'description'   => isset( $preset['description'] ) ? sanitize_text_field( wp_unslash( $preset['description'] ) ) : '',
				'system'        => false,
				'top_bar_items' => $items,
			);
		}

		return $sanitized;
	}

	/**
	 * Sanitize top bar item configuration.
	 *
	 * @param array $items Raw items.
	 * @return array
	 */
	private static function sanitize_top_bar_items( $items ) {
		$sanitized      = array();
		$allowed_icons  = EDMINBOOST_Command_Center::get_dashicon_options();
		$badge_sources  = array_keys( EDMINBOOST_Command_Center::get_badge_sources() );
		$seen_slugs     = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) || empty( $item['slug'] ) ) {
				continue;
			}

			$slug   = sanitize_text_field( wp_unslash( $item['slug'] ) );
			$anchor = isset( $item['anchor'] ) ? sanitize_text_field( wp_unslash( $item['anchor'] ) ) : '';

			if ( false !== strpos( $slug, '#' ) ) {
				$slug_parts = explode( '#', $slug, 2 );
				$slug       = $slug_parts[0];
				if ( '' === $anchor && ! empty( $slug_parts[1] ) ) {
					$anchor = $slug_parts[1];
				}
			}

			$slug   = EDMINBOOST_Command_Center_Bar::normalize_item_slug( $slug );
			$anchor = ltrim( $anchor, '#' );

			if ( preg_match( '#^https?://#i', $slug ) ) {
				continue;
			}

			if ( '' === $slug || ! preg_match( '/^[a-zA-Z0-9_\-.\/?=&%]+$/', $slug ) ) {
				continue;
			}

			if ( '' !== $anchor && ! preg_match( '/^[a-zA-Z0-9_\-\.]+$/', $anchor ) ) {
				continue;
			}

			$item_key = $slug . "\0" . $anchor;

			if ( in_array( $item_key, $seen_slugs, true ) ) {
				continue;
			}

			$seen_slugs[] = $item_key;

			$icon = isset( $item['icon'] ) ? sanitize_text_field( wp_unslash( $item['icon'] ) ) : 'dashicons-admin-generic';
			if ( ! in_array( $icon, $allowed_icons, true ) && 0 !== strpos( $icon, 'dashicons-' ) ) {
				$icon = 'dashicons-admin-generic';
			}

			$interaction = isset( $item['interaction'] ) ? sanitize_key( $item['interaction'] ) : 'redirect';
			if ( ! in_array( $interaction, array( 'redirect', 'drawer' ), true ) ) {
				$interaction = 'redirect';
			}

			$badge_source = isset( $item['badge_source'] ) ? sanitize_key( $item['badge_source'] ) : '';
			if ( ! in_array( $badge_source, $badge_sources, true ) ) {
				$badge_source = '';
			}

			$sanitized[] = array(
				'slug'         => $slug,
				'anchor'       => $anchor,
				'label'        => isset( $item['label'] ) ? sanitize_text_field( wp_unslash( $item['label'] ) ) : $slug,
				'icon'         => $icon,
				'interaction'  => $interaction,
				'badge_source' => $badge_source,
			);
		}

		return $sanitized;
	}

	/**
	 * Sanitize per-role icon visibility (stores hidden slugs).
	 *
	 * @param array $raw_visibility Submitted visible slugs per role.
	 * @param array $top_bar_items  Current top bar items.
	 * @return array
	 */
	private static function sanitize_role_visibility( $raw_visibility, $top_bar_items ) {
		$all_slugs = array();
		foreach ( $top_bar_items as $item ) {
			if ( ! empty( $item['slug'] ) ) {
				$all_slugs[] = $item['slug'];
			}
		}

		$hidden_by_role = array();

		foreach ( $raw_visibility as $role_key => $visible_slugs ) {
			$role_key = sanitize_key( $role_key );

			if ( '' === $role_key || ! is_array( $visible_slugs ) ) {
				continue;
			}

			$visible = array();
			foreach ( $visible_slugs as $slug ) {
				$slug = sanitize_text_field( wp_unslash( $slug ) );
				if ( in_array( $slug, $all_slugs, true ) ) {
					$visible[] = $slug;
				}
			}

			$hidden_by_role[ $role_key ] = array_values( array_diff( $all_slugs, $visible ) );
		}

		return $hidden_by_role;
	}

	/**
	 * Sanitize behavior & styling settings.
	 *
	 * @param array $raw Raw behavior input.
	 * @return array
	 */
	private static function sanitize_behavior( $raw ) {
		$defaults = EDMINBOOST_Command_Center::get_defaults()['behavior'];
		$output   = $defaults;

		$allowed_widths = array( 'compact', 'standard', 'fullscreen', 'custom' );
		if ( isset( $raw['drawer_width'] ) && in_array( $raw['drawer_width'], $allowed_widths, true ) ) {
			$output['drawer_width'] = $raw['drawer_width'];
		}

		if ( isset( $raw['drawer_width_custom'] ) ) {
			$output['drawer_width_custom'] = max(
				EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MIN,
				min(
					EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MAX,
					absint( $raw['drawer_width_custom'] )
				)
			);
		}

		$allowed_speeds = array( 'fast', 'normal', 'slow' );
		if ( isset( $raw['animation_speed'] ) && in_array( $raw['animation_speed'], $allowed_speeds, true ) ) {
			$output['animation_speed'] = $raw['animation_speed'];
		}

		$output['glassmorphism'] = ! empty( $raw['glassmorphism'] );

		if ( isset( $raw['autosave_interval'] ) ) {
			$output['autosave_interval'] = max( 10, min( 600, absint( $raw['autosave_interval'] ) ) );
		}

		if ( isset( $raw['badge_refresh_rate'] ) ) {
			$output['badge_refresh_rate'] = max( 15, min( 600, absint( $raw['badge_refresh_rate'] ) ) );
		}

		$allowed_styles = array( 'dot', 'pill', 'accent' );
		if ( isset( $raw['badge_style'] ) && in_array( $raw['badge_style'], $allowed_styles, true ) ) {
			$output['badge_style'] = $raw['badge_style'];
		}

		$output['hide_wp_logo']         = ! empty( $raw['hide_wp_logo'] );
		$output['hide_update_counters'] = ! empty( $raw['hide_update_counters'] );
		$output['hide_howdy']           = ! empty( $raw['hide_howdy'] );
		$output['hide_comments']        = ! empty( $raw['hide_comments'] );

		return $output;
	}
}
