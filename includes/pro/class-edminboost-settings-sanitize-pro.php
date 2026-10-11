<?php
/**
 * Premium build: licensed-only settings sanitization (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize Pro-shaped Command Center, theme, and feature fields when licensed admin UI is active.
 */
class EDMINBOOST_Settings_Sanitize_Pro {

	/**
	 * Register Pro-only sanitize hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'edminboost_sanitize_command_center', array( __CLASS__, 'filter_sanitize_command_center' ), 20, 2 );
		add_filter( 'edminboost_sanitize_custom_presets', array( __CLASS__, 'filter_sanitize_custom_presets' ), 10, 2 );
		add_filter( 'edminboost_sanitize_features', array( __CLASS__, 'filter_sanitize_features' ), 10, 2 );
	}

	/**
	 * Apply licensed Command Center field sanitization from raw request input.
	 *
	 * @param array $output Sanitized Command Center settings from core.
	 * @param array $raw    Raw Command Center input.
	 * @return array
	 */
	public static function filter_sanitize_command_center( $output, $raw ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() || ! is_array( $output ) || ! is_array( $raw ) ) {
			return $output;
		}

		$preset_applied = ! empty( $raw['_apply_preset'] );

		if ( $preset_applied ) {
			$preset_id = sanitize_key( $raw['_apply_preset'] );
			if ( 'default' === $preset_id ) {
				$preset_id = EDMINBOOST_Command_Center::resolve_effective_preset_id( 'default', $output );
			}

			if ( EDMINBOOST_Plan::is_layout_preset_available( $preset_id ) ) {
				$items = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $preset_id, $output );
				if ( ! empty( $items ) ) {
					$output['top_bar_items'] = self::sanitize_top_bar_items( $items );
				}

				if ( isset( $output['menu_studio'] ) ) {
					$output['menu_studio'] = self::apply_menu_studio_pro_fields(
						$output['menu_studio'],
						EDMINBOOST_Command_Center::resolve_preset_menu_studio( $preset_id, $output )
					);
				}
			}
		} elseif ( ! empty( $raw['_layout_studio_save'] ) ) {
			$output['top_bar_items'] = self::sanitize_top_bar_items(
				isset( $raw['top_bar_items'] ) && is_array( $raw['top_bar_items'] ) ? $raw['top_bar_items'] : array()
			);
		} elseif ( isset( $raw['top_bar_items'] ) && is_array( $raw['top_bar_items'] ) ) {
			$output['top_bar_items'] = self::sanitize_top_bar_items( $raw['top_bar_items'] );
		}

		if ( ! empty( $raw['_apply_look_skin'] ) && empty( $raw['_keep_advanced_behavior'] ) ) {
			$skin_id = sanitize_key( $raw['_apply_look_skin'] );
			$skins   = EDMINBOOST_Command_Center::get_look_skins();
			if ( isset( $skins[ $skin_id ]['behavior'] ) && is_array( $skins[ $skin_id ]['behavior'] ) ) {
				$output['behavior'] = self::sanitize_behavior( $skins[ $skin_id ]['behavior'] );
			}
		} elseif ( isset( $raw['behavior'] ) && is_array( $raw['behavior'] ) ) {
			$output['behavior'] = self::sanitize_behavior( $raw['behavior'] );
		}

		if ( isset( $raw['theme'] ) && is_array( $raw['theme'] ) && isset( $output['theme'] ) && is_array( $output['theme'] ) ) {
			$output['theme'] = self::sanitize_theme_scheduling( $raw['theme'], $output['theme'] );
		}

		if ( ! $preset_applied ) {
			$menu_raw = null;
			if ( ! empty( $raw['_menu_studio_save'] ) && isset( $raw['menu_studio'] ) && is_array( $raw['menu_studio'] ) ) {
				$menu_raw = $raw['menu_studio'];
			} elseif ( isset( $raw['menu_studio'] ) && is_array( $raw['menu_studio'] ) ) {
				$menu_raw = $raw['menu_studio'];
			}

			if ( is_array( $menu_raw ) && isset( $output['menu_studio'] ) && is_array( $output['menu_studio'] ) ) {
				$output['menu_studio'] = self::apply_menu_studio_pro_fields( $output['menu_studio'], $menu_raw );
			}
		}

		return $output;
	}

	/**
	 * Re-sanitize saved custom presets with licensed top bar and Menu Studio fields.
	 *
	 * @param array $sanitized Sanitized custom presets from core.
	 * @param array $raw       Raw presets input.
	 * @return array
	 */
	public static function filter_sanitize_custom_presets( $sanitized, $raw ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() || ! is_array( $sanitized ) || ! is_array( $raw ) ) {
			return $sanitized;
		}

		foreach ( $sanitized as $preset_id => $preset ) {
			if ( ! is_array( $preset ) || empty( $preset['top_bar_items'] ) ) {
				continue;
			}

			$raw_preset = isset( $raw[ $preset_id ] ) && is_array( $raw[ $preset_id ] ) ? $raw[ $preset_id ] : array();
			$raw_items  = isset( $raw_preset['top_bar_items'] ) && is_array( $raw_preset['top_bar_items'] )
				? $raw_preset['top_bar_items']
				: $preset['top_bar_items'];

			$sanitized[ $preset_id ]['top_bar_items'] = self::sanitize_top_bar_items( $raw_items );

			if ( isset( $preset['menu_studio'] ) && is_array( $preset['menu_studio'] ) ) {
				$raw_menu = isset( $raw_preset['menu_studio'] ) && is_array( $raw_preset['menu_studio'] )
					? $raw_preset['menu_studio']
					: $preset['menu_studio'];
				$sanitized[ $preset_id ]['menu_studio'] = self::apply_menu_studio_pro_fields( $preset['menu_studio'], $raw_menu );
			}
		}

		return $sanitized;
	}

	/**
	 * Sanitize login redirects when licensed admin UI is active.
	 *
	 * @param array $output Sanitized feature settings from core.
	 * @param array $raw    Raw features input.
	 * @return array
	 */
	public static function filter_sanitize_features( $output, $raw ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() || ! is_array( $output ) || ! is_array( $raw ) ) {
			return $output;
		}

		if ( ! isset( $raw['login_redirects'] ) || ! is_array( $raw['login_redirects'] ) ) {
			return $output;
		}

		$output['login_redirects']['enabled']       = ! empty( $raw['login_redirects']['enabled'] );
		$output['login_redirects']['default_login']  = self::sanitize_redirect_url( $raw['login_redirects']['default_login'] ?? '' );
		$output['login_redirects']['default_logout'] = self::sanitize_redirect_url( $raw['login_redirects']['default_logout'] ?? '' );
		$output['login_redirects']['login_roles']    = self::sanitize_role_redirects( $raw['login_redirects']['login_roles'] ?? array() );
		$output['login_redirects']['logout_roles']   = self::sanitize_role_redirects( $raw['login_redirects']['logout_roles'] ?? array() );

		return $output;
	}

	/**
	 * Sanitize top bar items including drawer interaction and badge sources.
	 *
	 * @param array $items Raw items.
	 * @return array
	 */
	private static function sanitize_top_bar_items( $items ) {
		$sanitized     = array();
		$allowed_icons = EDMINBOOST_Command_Center::get_dashicon_options();
		$badge_sources = array_keys( EDMINBOOST_Command_Center::get_badge_sources() );
		$seen_slugs    = array();

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
	 * Sanitize behavior settings including drawer panel and badge styling.
	 *
	 * @param array $raw Raw behavior input.
	 * @return array
	 */
	private static function sanitize_behavior( $raw ) {
		$defaults = EDMINBOOST_Command_Center::get_defaults()['behavior'];
		$output   = $defaults;

		$allowed_widths = array( 'compact', 'standard', 'fullscreen', 'custom' );
		if ( isset( $raw['drawer_width'] ) ) {
			$drawer_width = sanitize_key( $raw['drawer_width'] );
			if ( in_array( $drawer_width, $allowed_widths, true ) ) {
				$output['drawer_width'] = $drawer_width;
			}
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
		if ( isset( $raw['animation_speed'] ) ) {
			$animation_speed = sanitize_key( $raw['animation_speed'] );
			if ( in_array( $animation_speed, $allowed_speeds, true ) ) {
				$output['animation_speed'] = $animation_speed;
			}
		}

		$output['glassmorphism'] = ! empty( $raw['glassmorphism'] );

		if ( isset( $raw['autosave_interval'] ) ) {
			$output['autosave_interval'] = max( 10, min( 600, absint( $raw['autosave_interval'] ) ) );
		}

		if ( isset( $raw['badge_refresh_rate'] ) ) {
			$output['badge_refresh_rate'] = max( 15, min( 600, absint( $raw['badge_refresh_rate'] ) ) );
		}

		$allowed_styles = array( 'dot', 'pill', 'accent' );
		if ( isset( $raw['badge_style'] ) ) {
			$badge_style = sanitize_key( $raw['badge_style'] );
			if ( in_array( $badge_style, $allowed_styles, true ) ) {
				$output['badge_style'] = $badge_style;
			}
		}

		$output['hide_wp_logo']         = ! empty( $raw['hide_wp_logo'] );
		$output['hide_update_counters'] = ! empty( $raw['hide_update_counters'] );
		$output['hide_howdy']           = ! empty( $raw['hide_howdy'] );
		$output['hide_comments']        = ! empty( $raw['hide_comments'] );
		$output['hide_new_content']     = ! empty( $raw['hide_new_content'] );
		$output['hide_customize']       = ! empty( $raw['hide_customize'] );

		return $output;
	}

	/**
	 * Merge licensed Menu Studio display mode and custom links onto core-sanitized output.
	 *
	 * @param array $output Current menu_studio settings.
	 * @param array $raw    Raw menu_studio input.
	 * @return array
	 */
	private static function apply_menu_studio_pro_fields( $output, $raw ) {
		if ( isset( $raw['display_mode'] ) ) {
			$mode = sanitize_key( $raw['display_mode'] );
			$output['display_mode'] = in_array( $mode, array( 'both', 'icon', 'text' ), true ) ? $mode : 'both';
		}

		if ( isset( $raw['custom_items'] ) && is_array( $raw['custom_items'] ) ) {
			$output['custom_items'] = self::sanitize_menu_studio_custom_items( $raw['custom_items'] );
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
		$sanitized       = array();
		$allowed_icons   = EDMINBOOST_Command_Center::get_dashicon_options();
		$allowed_parents = array();
		$seen_ids        = array();

		foreach ( EDMINBOOST_Command_Center::get_discovered_menu_tree() as $menu_item ) {
			if ( ! empty( $menu_item['slug'] ) ) {
				$allowed_parents[] = $menu_item['slug'];
			}
		}

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
			if ( '' !== $parent && ! in_array( $parent, $allowed_parents, true ) ) {
				$parent = '';
			}

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
	 * Sanitize scheduled dark mode theme fields.
	 *
	 * @param array $raw    Raw theme input.
	 * @param array $output Core-sanitized theme output.
	 * @return array
	 */
	private static function sanitize_theme_scheduling( $raw, $output ) {
		$output['schedule_dark_mode'] = ! empty( $raw['schedule_dark_mode'] );
		$output['dark_mode_start']    = self::sanitize_time( $raw['dark_mode_start'] ?? '18:00' );
		$output['dark_mode_end']      = self::sanitize_time( $raw['dark_mode_end'] ?? '06:00' );

		return $output;
	}

	/**
	 * Sanitize HH:MM time string.
	 *
	 * @param string $time Raw time.
	 * @return string
	 */
	private static function sanitize_time( $time ) {
		$time = sanitize_text_field( wp_unslash( (string) $time ) );
		return preg_match( '/^\d{2}:\d{2}$/', $time ) ? $time : '00:00';
	}

	/**
	 * Sanitize a redirect URL (relative admin path or same-site URL).
	 *
	 * @param string $url Raw URL.
	 * @return string
	 */
	private static function sanitize_redirect_url( $url ) {
		$url = esc_url_raw( wp_unslash( (string) $url ) );
		if ( '' === $url ) {
			return '';
		}

		if ( 0 === strpos( $url, '//' ) ) {
			return '';
		}

		$home = home_url( '/' );
		if ( 0 !== strpos( $url, $home ) && 0 !== strpos( $url, '/' ) ) {
			return '';
		}

		$validated = wp_validate_redirect( $url, false );

		return $validated ? $validated : '';
	}

	/**
	 * Sanitize role => URL map.
	 *
	 * @param array $raw Raw map.
	 * @return array
	 */
	private static function sanitize_role_redirects( $raw ) {
		if ( ! is_array( $raw ) ) {
			return array();
		}

		$map = array();
		foreach ( $raw as $role => $url ) {
			$role = sanitize_key( $role );
			$url  = self::sanitize_redirect_url( $url );
			if ( '' !== $role && '' !== $url ) {
				$map[ $role ] = $url;
			}
		}

		return $map;
	}
}
