<?php
/**
 * Menu Studio — reorder wp-admin sidebar, custom links, and menu colors.
 *
 * Purpose: Apply saved Menu Studio configuration on admin screens.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu Studio runtime handler.
 */
class EDMINBOOST_Menu_Studio {

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'register_custom_items' ), 5 );
		add_action( 'admin_menu', array( __CLASS__, 'apply_menu_changes' ), 998 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_filter( 'admin_body_class', array( __CLASS__, 'filter_body_class' ) );
	}

	/**
	 * Menu slugs that must never be hidden or reordered away.
	 *
	 * @return string[]
	 */
	public static function get_protected_slugs() {
		return array(
			'index.php',
			'plugins.php',
			EDMINBOOST_Admin::PAGE_SLUG,
		);
	}

	/**
	 * Whether Menu Studio should apply on the current request.
	 *
	 * @return bool
	 */
	public static function is_active() {
		if ( ! EDMINBOOST_Settings::is_enabled() ) {
			return false;
		}

		$settings = self::get_settings();

		return ! empty( $settings['enabled'] );
	}

	/**
	 * Get merged Menu Studio settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$cc_settings = EDMINBOOST_Command_Center::get_settings();

		if ( ! isset( $cc_settings['menu_studio'] ) || ! is_array( $cc_settings['menu_studio'] ) ) {
			return EDMINBOOST_Command_Center::get_menu_studio_defaults();
		}

		$defaults = EDMINBOOST_Command_Center::get_menu_studio_defaults();
		$merged   = wp_parse_args( $cc_settings['menu_studio'], $defaults );

		if ( isset( $merged['colors'] ) && is_array( $merged['colors'] ) ) {
			$merged['colors'] = wp_parse_args( $merged['colors'], $defaults['colors'] );
		}

		return $merged;
	}

	/**
	 * Register custom sidebar menu links from saved settings.
	 *
	 * @return void
	 */
	public static function register_custom_items() {
		if ( ! self::is_active() ) {
			return;
		}

		$settings     = self::get_settings();
		$custom_items = isset( $settings['custom_items'] ) && is_array( $settings['custom_items'] )
			? $settings['custom_items']
			: array();

		foreach ( $custom_items as $item ) {
			if ( empty( $item['id'] ) || empty( $item['label'] ) || empty( $item['path'] ) ) {
				continue;
			}

			$menu_slug = self::get_custom_menu_slug( $item['id'] );
			$path      = self::normalize_admin_path( $item['path'] );
			$icon      = self::normalize_icon( isset( $item['icon'] ) ? $item['icon'] : '' );
			$parent    = isset( $item['parent'] ) ? (string) $item['parent'] : '';

			if ( '' === $path ) {
				continue;
			}

			if ( '' !== $parent ) {
				add_submenu_page(
					$parent,
					$item['label'],
					$item['label'],
					EDMINBOOST_Settings::CAPABILITY,
					$menu_slug,
					self::get_redirect_callback( $path )
				);
				continue;
			}

			add_menu_page(
				$item['label'],
				$item['label'],
				EDMINBOOST_Settings::CAPABILITY,
				$menu_slug,
				self::get_redirect_callback( $path ),
				$icon
			);
		}
	}

	/**
	 * Reorder and hide sidebar menu items.
	 *
	 * @return void
	 */
	public static function apply_menu_changes() {
		if ( ! self::is_active() ) {
			return;
		}

		global $menu, $submenu;

		$settings = self::get_settings();
		$hidden   = isset( $settings['hidden_items'] ) && is_array( $settings['hidden_items'] )
			? $settings['hidden_items']
			: array();
		$order    = isset( $settings['order'] ) && is_array( $settings['order'] )
			? $settings['order']
			: array();

		foreach ( $hidden as $menu_slug ) {
			if ( in_array( $menu_slug, self::get_protected_slugs(), true ) ) {
				continue;
			}

			remove_menu_page( $menu_slug );
		}

		if ( ! is_array( $menu ) || empty( $order ) ) {
			self::apply_submenu_order( $settings );
			return;
		}

		$menu_by_slug = array();
		$separators   = array();

		foreach ( $menu as $position => $item ) {
			if ( empty( $item[2] ) ) {
				continue;
			}

			$slug = (string) $item[2];

			if ( self::is_separator_slug( $slug ) ) {
				$separators[ $position ] = $item;
				continue;
			}

			$menu_by_slug[ $slug ] = $item;
		}

		$new_menu = array();
		$used     = array();

		foreach ( $order as $slug ) {
			$slug = (string) $slug;

			if ( ! isset( $menu_by_slug[ $slug ] ) || in_array( $slug, $hidden, true ) ) {
				continue;
			}

			$new_menu[]     = $menu_by_slug[ $slug ];
			$used[ $slug ] = true;
		}

		foreach ( $menu as $item ) {
			if ( empty( $item[2] ) ) {
				continue;
			}

			$slug = (string) $item[2];

			if ( self::is_separator_slug( $slug ) || ! empty( $used[ $slug ] ) || in_array( $slug, $hidden, true ) ) {
				continue;
			}

			$new_menu[] = $item;
		}

		$menu = $new_menu;

		self::apply_submenu_order( $settings );
	}

	/**
	 * Reorder submenu entries per parent slug.
	 *
	 * @param array $settings Menu Studio settings.
	 * @return void
	 */
	private static function apply_submenu_order( $settings ) {
		global $submenu;

		$submenu_order = isset( $settings['submenu_order'] ) && is_array( $settings['submenu_order'] )
			? $settings['submenu_order']
			: array();

		if ( empty( $submenu_order ) || ! is_array( $submenu ) ) {
			return;
		}

		foreach ( $submenu_order as $parent_slug => $slug_order ) {
			$parent_slug = (string) $parent_slug;

			if ( ! isset( $submenu[ $parent_slug ] ) || ! is_array( $submenu[ $parent_slug ] ) || ! is_array( $slug_order ) ) {
				continue;
			}

			$by_slug = array();
			foreach ( $submenu[ $parent_slug ] as $item ) {
				if ( empty( $item[2] ) ) {
					continue;
				}

				$by_slug[ (string) $item[2] ] = $item;
			}

			$new_submenu = array();
			$used        = array();

			foreach ( $slug_order as $slug ) {
				$slug = (string) $slug;

				if ( ! isset( $by_slug[ $slug ] ) ) {
					continue;
				}

				$new_submenu[]     = $by_slug[ $slug ];
				$used[ $slug ] = true;
			}

			foreach ( $submenu[ $parent_slug ] as $item ) {
				if ( empty( $item[2] ) ) {
					continue;
				}

				$slug = (string) $item[2];
				if ( empty( $used[ $slug ] ) ) {
					$new_submenu[] = $item;
				}
			}

			$submenu[ $parent_slug ] = $new_submenu;
		}
	}

	/**
	 * Enqueue scoped admin menu color overrides.
	 *
	 * @param string $hook_suffix Current admin hook suffix.
	 * @return void
	 */
	public static function enqueue_styles( $hook_suffix ) {
		unset( $hook_suffix );

		if ( ! self::is_active() ) {
			return;
		}

		$settings = self::get_settings();

		wp_enqueue_style(
			'edminboost-admin-menu',
			EDMINBOOST_PLUGIN_URL . 'admin/css/edminboost-admin-menu.css',
			array(),
			EDMINBOOST_VERSION
		);

		$css = self::build_inline_css( $settings );

		if ( '' !== $css ) {
			wp_add_inline_style( 'edminboost-admin-menu', $css );
		}
	}

	/**
	 * Append body class when menu colors are active.
	 *
	 * @param string $classes Space-separated body classes.
	 * @return string
	 */
	public static function filter_body_class( $classes ) {
		if ( ! self::is_active() ) {
			return $classes;
		}

		$settings = self::get_settings();

		$classes .= ' edminboost-menu-studio-active';

		if ( ! empty( $settings['use_colors'] ) ) {
			$classes .= ' edminboost-menu-studio-colors';
		}

		$mode = isset( $settings['display_mode'] ) ? sanitize_key( $settings['display_mode'] ) : 'both';
		if ( in_array( $mode, array( 'icon', 'text' ), true ) ) {
			$classes .= ' edminboost-menu-studio-display--' . $mode;
		}

		return $classes;
	}

	/**
	 * Build inline CSS for menu color and typography tokens.
	 *
	 * @param array $settings Menu Studio settings.
	 * @return string
	 */
	private static function build_inline_css( $settings ) {
		$colors = isset( $settings['colors'] ) ? $settings['colors'] : array();
		$map    = array(
			'parent_bg'         => '--eb-ms-parent-bg',
			'parent_text'       => '--eb-ms-parent-text',
			'parent_active'     => '--eb-ms-parent-active',
			'submenu_bg'        => '--eb-ms-submenu-bg',
			'submenu_text'      => '--eb-ms-submenu-text',
			'notification_bg'   => '--eb-ms-notification-bg',
			'notification_text' => '--eb-ms-notification-text',
		);

		$rules = array();

		if ( ! empty( $settings['menu_width'] ) ) {
			$rules[] = '--eb-ms-width:' . absint( $settings['menu_width'] ) . 'px';
		}
		if ( ! empty( $settings['font_size'] ) ) {
			$rules[] = '--eb-ms-font-size:' . absint( $settings['font_size'] ) . 'px';
		}
		if ( isset( $settings['line_height'] ) ) {
			$rules[] = '--eb-ms-line-height:' . absint( $settings['line_height'] ) . 'px';
		}
		if ( isset( $settings['letter_spacing'] ) ) {
			$rules[] = '--eb-ms-letter-spacing:' . (int) $settings['letter_spacing'] . 'px';
		}

		if ( ! empty( $settings['use_colors'] ) && is_array( $colors ) ) {
			foreach ( $map as $key => $var ) {
				if ( empty( $colors[ $key ] ) ) {
					continue;
				}

				$color = EDMINBOOST_Theme::sanitize_hex_color( $colors[ $key ] );
				if ( '' !== $color ) {
					$rules[] = $var . ':' . $color;
				}
			}
		}

		if ( isset( $settings['padding'] ) && is_array( $settings['padding'] ) ) {
			$pad_map = array(
				'wrapper_top'    => '--eb-ms-wrap-pt',
				'wrapper_right'  => '--eb-ms-wrap-pr',
				'wrapper_bottom' => '--eb-ms-wrap-pb',
				'wrapper_left'   => '--eb-ms-wrap-pl',
				'submenu_top'    => '--eb-ms-sub-pt',
				'submenu_right'  => '--eb-ms-sub-pr',
				'submenu_bottom' => '--eb-ms-sub-pb',
				'submenu_left'   => '--eb-ms-sub-pl',
			);
			foreach ( $pad_map as $key => $var ) {
				if ( isset( $settings['padding'][ $key ] ) ) {
					$rules[] = $var . ':' . absint( $settings['padding'][ $key ] ) . 'px';
				}
			}
		}

		if ( empty( $rules ) ) {
			return '';
		}

		return 'body.edminboost-menu-studio-active{' . implode( ';', $rules ) . ';}';
	}

	/**
	 * Build a redirect callback for a custom menu item.
	 *
	 * @param string $path Admin path.
	 * @return callable
	 */
	private static function get_redirect_callback( $path ) {
		return static function () use ( $path ) {
			wp_safe_redirect( admin_url( $path ) );
			exit;
		};
	}

	/**
	 * Normalize a custom item admin path.
	 *
	 * @param string $path Raw path.
	 * @return string
	 */
	private static function normalize_admin_path( $path ) {
		$path = trim( (string) wp_unslash( $path ) );
		$path = ltrim( $path, '/' );

		if ( '' === $path ) {
			return '';
		}

		if ( preg_match( '#^https?://#i', $path ) ) {
			return '';
		}

		if ( ! preg_match( '#^[a-zA-Z0-9_\-\./?=&%#]+$#', $path ) ) {
			return '';
		}

		return $path;
	}

	/**
	 * Normalize a dashicon class name.
	 *
	 * @param string $icon Raw icon value.
	 * @return string
	 */
	private static function normalize_icon( $icon ) {
		$icon = trim( (string) $icon );

		if ( '' === $icon ) {
			return 'dashicons-admin-links';
		}

		if ( false === strpos( $icon, 'dashicons-' ) ) {
			$icon = 'dashicons-' . $icon;
		}

		$allowed = EDMINBOOST_Command_Center::get_dashicon_options();

		if ( in_array( $icon, $allowed, true ) ) {
			return $icon;
		}

		return 'dashicons-admin-links';
	}

	/**
	 * Build the internal menu slug for a custom item.
	 *
	 * @param string $id Custom item id.
	 * @return string
	 */
	public static function get_custom_menu_slug( $id ) {
		return 'edminboost_ms_' . sanitize_key( $id );
	}

	/**
	 * Whether a menu slug is a separator entry.
	 *
	 * @param string $slug Menu slug.
	 * @return bool
	 */
	private static function is_separator_slug( $slug ) {
		return '' === $slug || '---' === $slug || 0 === strpos( $slug, 'separator' );
	}
}
