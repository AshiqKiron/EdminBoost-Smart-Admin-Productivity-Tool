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
			'features'        => EDMINBOOST_Feature_Settings::get_defaults(),
			'white_label'     => EDMINBOOST_White_Label::get_defaults(),
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
		$settings = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return self::merge_settings( $settings );
	}

	/**
	 * Get normalized plugin defaults for admin form reset.
	 *
	 * @return array
	 */
	public static function get_form_defaults() {
		return self::merge_settings( array(), false );
	}

	/**
	 * Merge stored settings with plugin defaults.
	 *
	 * @param array $settings     Stored or empty settings.
	 * @param bool  $apply_filter Whether to run the edminboost_settings filter.
	 * @return array
	 */
	private static function merge_settings( $settings, $apply_filter = true ) {
		$defaults = self::get_defaults();

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
			$settings['features'] = EDMINBOOST_Feature_Settings::normalize(
				wp_parse_args( $settings['features'], $defaults['features'] )
			);
		} else {
			$settings['features'] = $defaults['features'];
		}

		if ( isset( $settings['white_label'] ) && is_array( $settings['white_label'] ) ) {
			$settings['white_label'] = wp_parse_args( $settings['white_label'], $defaults['white_label'] );
		} else {
			$settings['white_label'] = $defaults['white_label'];
		}

		$settings = self::migrate_legacy_admin_bar( $settings );

		if ( ! $apply_filter ) {
			return $settings;
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

		return EDMINBOOST_Feature_Settings::is_enabled( $feature_id, $features );
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
		$features = EDMINBOOST_Feature_Settings::normalize( $features );

		if ( isset( $features[ $feature_id ] ) && is_array( $features[ $feature_id ] ) ) {
			return $features[ $feature_id ];
		}

		if ( isset( $features[ $feature_id ] ) ) {
			return array( 'enabled' => (bool) $features[ $feature_id ] );
		}

		return array();
	}

	/**
	 * Migrate legacy admin_bar feature toggles into Command Center behavior.
	 *
	 * @param array $settings Plugin settings.
	 * @return array
	 */
	private static function migrate_legacy_admin_bar( $settings ) {
		if ( empty( $settings['features']['admin_bar'] ) || ! is_array( $settings['features']['admin_bar'] ) ) {
			return $settings;
		}

		$legacy   = $settings['features']['admin_bar'];
		$behavior = isset( $settings['command_center']['behavior'] ) && is_array( $settings['command_center']['behavior'] )
			? $settings['command_center']['behavior']
			: array();

		if ( ! empty( $legacy['hide_wp_logo'] ) ) {
			$behavior['hide_wp_logo'] = true;
		}
		if ( ! empty( $legacy['hide_comments'] ) ) {
			$behavior['hide_comments'] = true;
		}
		if ( ! empty( $legacy['hide_new_content'] ) ) {
			$behavior['hide_new_content'] = true;
		}
		if ( ! empty( $legacy['hide_customize'] ) ) {
			$behavior['hide_customize'] = true;
		}

		$settings['command_center']['behavior'] = wp_parse_args(
			$behavior,
			EDMINBOOST_Command_Center::get_defaults()['behavior']
		);

		return $settings;
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

		if ( isset( $input['white_label'] ) && is_array( $input['white_label'] ) ) {
			$sanitized['white_label'] = EDMINBOOST_White_Label::sanitize( $input['white_label'] );
		}

		if ( ! isset( $input['features'] ) || ! is_array( $input['features'] ) ) {
			return $sanitized;
		}

		$sanitized['features'] = EDMINBOOST_Feature_Settings::sanitize( $input['features'], $sanitized );

		/**
		 * Filter sanitized settings before save.
		 *
		 * @param array $sanitized Sanitized settings.
		 * @param array $input     Raw input.
		 */
		$sanitized = apply_filters( 'edminboost_sanitize_settings', $sanitized, $input );

		return $sanitized;
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
		$output              = wp_parse_args( $current, $defaults );
		$preset_menu_applied = false;
		$preset_layout_applied = false;

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

		if ( ! empty( $raw['_setup_wizard_save'] ) && ! empty( $raw['_apply_preset'] ) ) {
			$preset_id = sanitize_key( $raw['_apply_preset'] );
			$items     = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $preset_id );
			if ( ! empty( $items ) ) {
				$output['top_bar_items']  = self::sanitize_top_bar_items( $items );
				$output['default_preset'] = $preset_id;
				$output['menu_studio']    = self::sanitize_menu_studio(
					EDMINBOOST_Command_Center::resolve_preset_menu_studio( $preset_id, $output ),
					null
				);
				$preset_menu_applied      = true;
				$preset_layout_applied    = true;

				$all_presets = EDMINBOOST_Command_Center::get_all_presets();
				if ( ! empty( $all_presets[ $preset_id ]['persona'] ) ) {
					$persona = sanitize_key( $all_presets[ $preset_id ]['persona'] );
					if ( in_array( $persona, $allowed_personas, true ) ) {
						$output['persona'] = $persona;
					}
				}
			}
		}

		if ( ! empty( $raw['_apply_preset'] ) && empty( $raw['_setup_wizard_save'] ) ) {
			$preset_id = sanitize_key( $raw['_apply_preset'] );

			if ( 'default' === $preset_id ) {
				$preset_id = EDMINBOOST_Command_Center::resolve_effective_preset_id( 'default', $output );
			}

			$items = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $preset_id, $output );
			if ( ! empty( $items ) ) {
				$output['top_bar_items']        = self::sanitize_top_bar_items( $items );
				$output['default_preset']       = $preset_id;
				$output['onboarding_completed'] = true;
				$output['menu_studio']          = self::sanitize_menu_studio(
					EDMINBOOST_Command_Center::resolve_preset_menu_studio( $preset_id, $output ),
					null
				);
				$preset_menu_applied            = true;
				$preset_layout_applied          = true;

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
			$default_preset = sanitize_key( $raw['default_preset'] );
			if ( 'default' !== $default_preset ) {
				$output['default_preset'] = $default_preset;
			}
		}

		if ( ! empty( $raw['_layout_studio_save'] ) ) {
			$output['top_bar_items'] = isset( $raw['top_bar_items'] ) && is_array( $raw['top_bar_items'] )
				? self::sanitize_top_bar_items( $raw['top_bar_items'] )
				: array();
		} elseif ( isset( $raw['top_bar_items'] ) && is_array( $raw['top_bar_items'] ) && ! $preset_layout_applied ) {
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
				EDMINBOOST_Command_Center::get_role_matrix_menu_items()
			);
		}

		if ( isset( $raw['behavior'] ) && is_array( $raw['behavior'] ) ) {
			$output['behavior'] = self::sanitize_behavior( $raw['behavior'] );
		}

		if ( isset( $raw['theme'] ) && is_array( $raw['theme'] ) ) {
			$existing_theme = EDMINBOOST_Theme::get_settings( $current );
			$merged_theme   = array_merge( $existing_theme, $raw['theme'] );

			// Dashboard and partial theme saves submit a preset but omit use_custom_colors.
			// Drop stale custom state so an explicit built-in preset is not forced back to custom.
			if ( isset( $raw['theme']['preset'] ) ) {
				$incoming_preset = sanitize_key( $raw['theme']['preset'] );
				if ( 'custom' !== $incoming_preset && in_array( $incoming_preset, array_keys( EDMINBOOST_Theme::get_presets() ), true ) ) {
					$merged_theme['use_custom_colors'] = false;
				}
			}

			$output['theme'] = EDMINBOOST_Theme::sanitize( $merged_theme );
		}

		if ( isset( $raw['presets'] ) && is_array( $raw['presets'] ) ) {
			$output['presets'] = self::sanitize_custom_presets( $raw['presets'] );
		}

		if ( ! empty( $raw['_save_custom_preset'] ) && is_array( $raw['_save_custom_preset'] ) ) {
			$output['presets'] = self::sanitize_custom_presets(
				array_merge(
					isset( $output['presets'] ) && is_array( $output['presets'] ) ? $output['presets'] : array(),
					self::build_custom_preset_from_request( $raw['_save_custom_preset'], $output['top_bar_items'], $output )
				)
			);
		}

		if ( ! empty( $raw['_rename_custom_preset'] ) && is_array( $raw['_rename_custom_preset'] ) ) {
			$output['presets'] = self::rename_custom_preset(
				$raw['_rename_custom_preset'],
				isset( $output['presets'] ) && is_array( $output['presets'] ) ? $output['presets'] : array()
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

		if ( ! empty( $raw['_setup_wizard_save'] ) && ! empty( $output['top_bar_items'] ) ) {
			$output['onboarding_completed'] = true;
		}

		if ( ! empty( $raw['_mark_setup_complete'] ) && ! empty( $output['top_bar_items'] ) ) {
			$output['onboarding_completed'] = true;
		}

		if ( ! empty( $raw['_menu_studio_save'] ) ) {
			$output['menu_studio'] = self::sanitize_menu_studio(
				isset( $raw['menu_studio'] ) && is_array( $raw['menu_studio'] ) ? $raw['menu_studio'] : array(),
				isset( $output['menu_studio'] ) && is_array( $output['menu_studio'] ) ? $output['menu_studio'] : null
			);
		} elseif ( isset( $raw['menu_studio'] ) && is_array( $raw['menu_studio'] ) && ! $preset_menu_applied ) {
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
			if ( ! $output['use_colors'] ) {
				$output['colors'] = $defaults['colors'];
			}
		}

		if ( isset( $raw['menu_width'] ) ) {
			$output['menu_width'] = max( 120, min( 300, absint( $raw['menu_width'] ) ) );
		}

		if ( isset( $raw['font_size'] ) ) {
			$output['font_size'] = max( 10, min( 24, absint( $raw['font_size'] ) ) );
		}

		if ( isset( $raw['line_height'] ) ) {
			$output['line_height'] = max( 12, min( 36, absint( $raw['line_height'] ) ) );
		}

		if ( isset( $raw['letter_spacing'] ) ) {
			$output['letter_spacing'] = max( -2, min( 6, (int) $raw['letter_spacing'] ) );
		}

		if ( isset( $raw['display_mode'] ) ) {
			$mode = sanitize_key( $raw['display_mode'] );
			$output['display_mode'] = in_array( $mode, array( 'both', 'icon', 'text' ), true ) ? $mode : 'both';
		}

		if ( isset( $raw['padding'] ) && is_array( $raw['padding'] ) ) {
			$padding_keys = array_keys( $defaults['padding'] );
			foreach ( $padding_keys as $pad_key ) {
				if ( isset( $raw['padding'][ $pad_key ] ) ) {
					$output['padding'][ $pad_key ] = max( 0, min( 40, absint( $raw['padding'][ $pad_key ] ) ) );
				}
			}
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
				if ( '' === $slug || in_array( $slug, EDMINBOOST_Menu_Studio::get_protected_slugs(), true ) ) {
					continue;
				}
				if ( ! in_array( $slug, $hidden, true ) ) {
					$hidden[] = $slug;
				}
			}
			$output['hidden_items'] = $hidden;
		}

		if ( isset( $raw['submenu_parents'] ) && is_array( $raw['submenu_parents'] )
			&& isset( $raw['submenu_order'] ) && is_array( $raw['submenu_order'] ) ) {
			$submenu_order = array();

			foreach ( $raw['submenu_parents'] as $parent_index => $parent_slug ) {
				$parent_slug = sanitize_text_field( wp_unslash( (string) $parent_slug ) );
				if ( '' === $parent_slug || ! isset( $raw['submenu_order'][ $parent_index ] ) || ! is_array( $raw['submenu_order'][ $parent_index ] ) ) {
					continue;
				}

				$child_order = array();
				foreach ( $raw['submenu_order'][ $parent_index ] as $child_slug ) {
					$child_slug = sanitize_text_field( wp_unslash( (string) $child_slug ) );
					if ( '' !== $child_slug && ! in_array( $child_slug, $child_order, true ) ) {
						$child_order[] = $child_slug;
					}
				}

				$submenu_order[ $parent_slug ] = $child_order;
			}

			$output['submenu_order'] = $submenu_order;
		} elseif ( isset( $raw['submenu_order'] ) && is_array( $raw['submenu_order'] ) ) {
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

			if ( ! preg_match( '~^[a-zA-Z0-9_\-\./?=&%#]+$~', $path ) ) {
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
	 * @param array $cc_output     Current sanitized command_center output.
	 * @return array
	 */
	private static function build_custom_preset_from_request( $raw, $top_bar_items, $cc_output = array() ) {
		$name = isset( $raw['name'] ) ? sanitize_text_field( wp_unslash( $raw['name'] ) ) : '';

		if ( '' === $name || empty( $top_bar_items ) ) {
			return array();
		}

		$id = 'custom_' . wp_generate_password( 8, false, false );
		$menu_studio = isset( $cc_output['menu_studio'] ) && is_array( $cc_output['menu_studio'] )
			? $cc_output['menu_studio']
			: EDMINBOOST_Command_Center::get_menu_studio_defaults();

		return array(
			$id => array(
				'name'          => $name,
				'description'   => isset( $raw['description'] ) ? sanitize_text_field( wp_unslash( $raw['description'] ) ) : '',
				'system'        => false,
				'top_bar_items' => $top_bar_items,
				'menu_studio'   => self::sanitize_menu_studio( $menu_studio ),
			),
		);
	}

	/**
	 * Rename a saved custom preset.
	 *
	 * @param array $raw     Raw rename payload.
	 * @param array $presets Existing custom presets.
	 * @return array
	 */
	private static function rename_custom_preset( $raw, $presets ) {
		$preset_id = isset( $raw['id'] ) ? sanitize_key( $raw['id'] ) : '';
		$name      = isset( $raw['name'] ) ? sanitize_text_field( wp_unslash( $raw['name'] ) ) : '';

		if ( '' === $preset_id || '' === $name || ! isset( $presets[ $preset_id ] ) ) {
			return self::sanitize_custom_presets( $presets );
		}

		$presets[ $preset_id ]['name'] = $name;

		return self::sanitize_custom_presets( $presets );
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
		$menu_studio = isset( $source['menu_studio'] ) && is_array( $source['menu_studio'] )
			? $source['menu_studio']
			: EDMINBOOST_Command_Center::resolve_preset_menu_studio( $source_id, $output );

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
				'menu_studio'   => self::sanitize_menu_studio( $menu_studio ),
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

			if ( isset( $preset['menu_studio'] ) && is_array( $preset['menu_studio'] ) ) {
				$sanitized[ $preset_id ]['menu_studio'] = self::sanitize_menu_studio( $preset['menu_studio'] );
			}
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
			$icon = EDMINBOOST_Command_Center::normalize_dashicon_class( $icon );
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
	 * @param array $matrix_items Role matrix menu items.
	 * @return array
	 */
	private static function sanitize_role_visibility( $raw_visibility, $matrix_items ) {
		unset( $matrix_items );

		$hidden_by_role = array();

		foreach ( $raw_visibility as $role_key => $visible_slugs ) {
			$role_key = sanitize_key( $role_key );

			if ( '' === $role_key || ! is_array( $visible_slugs ) ) {
				continue;
			}

			$role_matrix = EDMINBOOST_Command_Center::get_role_matrix_menu_items();
			$all_slugs   = array();

			foreach ( $role_matrix as $item ) {
				if ( ! empty( $item['slug'] ) ) {
					$all_slugs[] = $item['slug'];
				}
			}

			$visible = array();
			foreach ( $visible_slugs as $slug ) {
				$slug = sanitize_text_field( wp_unslash( $slug ) );
				if ( in_array( $slug, $all_slugs, true ) ) {
					$visible[] = $slug;
				}
			}

			$hidden = array_values( array_diff( $all_slugs, $visible ) );
			$hidden = array_values(
				array_diff( $hidden, EDMINBOOST_Command_Center::get_protected_slugs_for_role( $role_key ) )
			);

			$hidden_by_role[ $role_key ] = $hidden;
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
		$output['hide_new_content']     = ! empty( $raw['hide_new_content'] );
		$output['hide_customize']       = ! empty( $raw['hide_customize'] );

		return $output;
	}
}
