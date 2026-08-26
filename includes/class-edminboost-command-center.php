<?php
/**
 * Command Center — onboarding, layout studio, presets, and behavior helpers.
 *
 * Purpose: Shared data and defaults for EdminBoost admin configuration pages.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command Center helper.
 */
class EDMINBOOST_Command_Center {

	/**
	 * Onboarding wizard page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_ONBOARDING = '-onboarding';

	/**
	 * Layout mapper page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_MAPPER = '-mapper';

	/**
	 * Presets manager page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_PRESETS = '-presets';

	/**
	 * Behavior & styling page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_BEHAVIOR = '-behavior';

	/**
	 * Menu Studio page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_MENU_STUDIO = '-menu';

	/**
	 * Minimum custom drawer width in pixels.
	 *
	 * @var int
	 */
	const DRAWER_CUSTOM_WIDTH_MIN = 400;

	/**
	 * Maximum custom drawer width in pixels.
	 *
	 * @var int
	 */
	const DRAWER_CUSTOM_WIDTH_MAX = 800;

	/**
	 * Default custom drawer width in pixels.
	 *
	 * @var int
	 */
	const DRAWER_CUSTOM_WIDTH_DEFAULT = 600;

	/**
	 * Default Command Center settings shape.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'onboarding_completed' => false,
			'persona'                => '',
			'look_skin'              => '',
			'top_bar_items'          => array(),
			'presets'                => array(),
			'default_preset'         => 'system_client',
			'role_assignments'       => array(),
			'role_visibility'        => array(),
			'theme'                  => EDMINBOOST_Theme::get_defaults(),
			'behavior'               => array(
				'drawer_width'         => 'standard',
				'drawer_width_custom'  => self::DRAWER_CUSTOM_WIDTH_DEFAULT,
				'animation_speed'      => 'normal',
				'glassmorphism'        => false,
				'autosave_interval'    => 60,
				'badge_refresh_rate'   => 60,
				'badge_style'          => 'pill',
				'hide_wp_logo'         => false,
				'hide_update_counters' => false,
				'hide_howdy'           => false,
				'hide_comments'        => false,
			),
			'menu_studio'            => self::get_menu_studio_defaults(),
		);
	}

	/**
	 * Default Menu Studio settings shape.
	 *
	 * @return array
	 */
	public static function get_menu_studio_defaults() {
		return array(
			'enabled'        => false,
			'order'          => array(),
			'submenu_order'  => array(),
			'hidden_items'   => array(),
			'custom_items'   => array(),
			'use_colors'     => false,
			'colors'         => array(
				'parent_bg'         => '',
				'parent_text'       => '',
				'parent_active'     => '',
				'submenu_bg'        => '',
				'submenu_text'      => '',
				'notification_bg'   => '',
				'notification_text' => '',
			),
		);
	}

	/**
	 * Get merged Command Center settings from plugin options.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = EDMINBOOST_Settings::get();
		$defaults = self::get_defaults();

		if ( ! isset( $settings['command_center'] ) || ! is_array( $settings['command_center'] ) ) {
			return $defaults;
		}

		$merged = wp_parse_args( $settings['command_center'], $defaults );

		if ( isset( $merged['behavior'] ) && is_array( $merged['behavior'] ) ) {
			$merged['behavior'] = wp_parse_args( $merged['behavior'], $defaults['behavior'] );
		}

		if ( isset( $merged['theme'] ) && is_array( $merged['theme'] ) ) {
			$merged['theme'] = wp_parse_args( $merged['theme'], $defaults['theme'] );
		}

		if ( isset( $merged['menu_studio'] ) && is_array( $merged['menu_studio'] ) ) {
			$ms_defaults = self::get_menu_studio_defaults();
			$merged['menu_studio'] = wp_parse_args( $merged['menu_studio'], $ms_defaults );

			if ( isset( $merged['menu_studio']['colors'] ) && is_array( $merged['menu_studio']['colors'] ) ) {
				$merged['menu_studio']['colors'] = wp_parse_args(
					$merged['menu_studio']['colors'],
					$ms_defaults['colors']
				);
			}
		}

		return $merged;
	}

	/**
	 * Quick links to main plugin pages (Home hub, sidebar).
	 *
	 * @return array[]
	 */
	public static function get_page_links() {
		$base = EDMINBOOST_Admin::PAGE_SLUG;

		return array(
			array(
				'slug'  => $base,
				'label' => __( 'Home', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_MAPPER,
				'label' => __( 'Top Bar', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_PRESETS,
				'label' => __( 'Presets', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_MENU_STUDIO,
				'label' => __( 'Menu Studio', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . '-settings',
				'label' => __( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
			),
		);
	}

	/**
	 * Navigation items for Command Center pages (legacy helper).
	 *
	 * @return array[]
	 */
	public static function get_nav_items() {
		return self::get_page_links();
	}

	/**
	 * Whether the site has a saved top bar layout or completed setup.
	 *
	 * @param array|null $cc_settings Optional CC settings; loads from options when null.
	 * @return bool
	 */
	public static function is_setup_complete( $cc_settings = null ) {
		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		if ( ! empty( $cc_settings['onboarding_completed'] ) ) {
			return true;
		}

		return ! empty( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] );
	}

	/**
	 * Look-only skin presets (behavior bundles).
	 *
	 * @return array[]
	 */
	public static function get_look_skins() {
		$defaults = self::get_defaults()['behavior'];

		return array(
			'clean' => array(
				'name'        => __( 'Clean', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Standard panel, subtle badges, hides logo and profile text.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-art',
				'behavior'    => array_merge(
					$defaults,
					array(
						'drawer_width'         => 'standard',
						'animation_speed'      => 'normal',
						'badge_style'          => 'dot',
						'hide_wp_logo'         => true,
						'hide_howdy'           => true,
						'hide_update_counters' => false,
						'hide_comments'        => false,
					)
				),
			),
			'focused' => array(
				'name'        => __( 'Focused', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Compact panel, counter badges, minimal admin bar clutter.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-visibility',
				'behavior'    => array_merge(
					$defaults,
					array(
						'drawer_width'         => 'compact',
						'animation_speed'      => 'fast',
						'badge_style'          => 'pill',
						'hide_wp_logo'         => true,
						'hide_update_counters' => true,
						'hide_howdy'           => false,
						'hide_comments'        => true,
					)
				),
			),
			'full' => array(
				'name'        => __( 'Full', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Standard panel with accent badges and the full native admin bar.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-admin-site-alt3',
				'behavior'    => array_merge(
					$defaults,
					array(
						'drawer_width'         => 'standard',
						'animation_speed'      => 'normal',
						'badge_style'          => 'accent',
						'hide_wp_logo'         => false,
						'hide_update_counters' => false,
						'hide_howdy'           => false,
						'hide_comments'        => false,
					)
				),
			),
		);
	}

	/**
	 * Detect which look skin matches stored behavior settings.
	 *
	 * @param array|null $behavior Behavior settings.
	 * @return string Skin id or empty string.
	 */
	public static function detect_look_skin( $behavior = null ) {
		if ( null === $behavior ) {
			$behavior = self::get_settings()['behavior'];
		}

		foreach ( self::get_look_skins() as $skin_id => $skin ) {
			$matches = true;

			foreach ( $skin['behavior'] as $key => $value ) {
				if ( ! isset( $behavior[ $key ] ) ) {
					$matches = false;
					break;
				}

				if ( (bool) $behavior[ $key ] !== (bool) $value ) {
					$matches = false;
					break;
				}

				if ( ! is_bool( $value ) && (string) $behavior[ $key ] !== (string) $value ) {
					$matches = false;
					break;
				}
			}

			if ( $matches ) {
				return $skin_id;
			}
		}

		return '';
	}

	/**
	 * Persona preset cards for onboarding.
	 *
	 * @return array[]
	 */
	public static function get_personas() {
		return array(
			'client' => array(
				'title'       => __( 'The Client / Content Editor', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Minimalist setup. Hides advanced technical tools and exposes a clean top bar for content, media, and basic analytics.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-edit',
				'preset'      => 'system_client',
			),
			'ecommerce' => array(
				'title'       => __( 'The E-Commerce Manager', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Focused layout with auto-mapped WooCommerce dashboards, live order counters, and customer support shortcut badges.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-cart',
				'preset'      => 'system_ecommerce',
			),
			'developer' => array(
				'title'       => __( 'The Agency Developer / Power User', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Unlocks full multi-plugin auto-mapping, debugging toggles, and AJAX slide-out drawers.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-admin-tools',
				'preset'      => 'system_developer',
			),
		);
	}

	/**
	 * Built-in system presets.
	 *
	 * @return array[]
	 */
	public static function get_system_presets() {
		return array(
			'system_client' => array(
				'name'        => __( 'Content Editor', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Clean top bar focused on content creation and media.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'persona'     => 'client',
			),
			'system_ecommerce' => array(
				'name'        => __( 'Shop Manager', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'WooCommerce dashboards, orders, and product shortcuts.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'persona'     => 'ecommerce',
			),
			'system_developer' => array(
				'name'        => __( 'Power User', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Full admin mapping with slide-out panels for deep screens.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'persona'     => 'developer',
			),
		);
	}

	/**
	 * Preferred top bar slugs per system preset (resolved against discovered menus).
	 *
	 * @return array<string, array[]>
	 */
	public static function get_preset_layout_definitions() {
		return array(
			'system_client' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php',
					'label'        => __( 'Posts', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-post',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'upload.php',
					'label'        => __( 'Media', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-media',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php?post_type=page',
					'label'        => __( 'Pages', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-page',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit-comments.php',
					'label'        => __( 'Comments', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-comments',
					'interaction'  => 'redirect',
					'badge_source' => 'comments',
				),
			),
			'system_ecommerce' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'woocommerce',
					'label'        => __( 'WooCommerce', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-cart',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php?post_type=shop_order',
					'label'        => __( 'Orders', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-list-view',
					'interaction'  => 'redirect',
					'badge_source' => 'wc_orders',
				),
				array(
					'slug'         => 'edit.php?post_type=product',
					'label'        => __( 'Products', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-products',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'wc-admin',
					'label'        => __( 'Analytics', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-chart-bar',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
			),
			'system_developer' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php',
					'label'        => __( 'Posts', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-post',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'plugins.php',
					'label'        => __( 'Plugins', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-plugins',
					'interaction'  => 'drawer',
					'badge_source' => 'updates',
				),
				array(
					'slug'         => 'themes.php',
					'label'        => __( 'Appearance', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-appearance',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
				array(
					'slug'         => 'tools.php',
					'label'        => __( 'Tools', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-tools',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
				array(
					'slug'         => 'options-general.php',
					'label'        => __( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-settings',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
			),
		);
	}

	/**
	 * Resolve preset top bar items against the current admin menu.
	 *
	 * @param string $preset_id Preset identifier.
	 * @return array[]
	 */
	public static function resolve_preset_top_bar_items( $preset_id ) {
		$all_presets = self::get_all_presets();

		if ( isset( $all_presets[ $preset_id ]['top_bar_items'] ) && is_array( $all_presets[ $preset_id ]['top_bar_items'] ) ) {
			return $all_presets[ $preset_id ]['top_bar_items'];
		}

		$definitions = self::get_preset_layout_definitions();
		if ( ! isset( $definitions[ $preset_id ] ) ) {
			return array();
		}

		$discovered = self::get_discovered_menu_items();
		$by_slug    = array();

		foreach ( $discovered as $item ) {
			if ( empty( $item['slug'] ) ) {
				continue;
			}

			$by_slug[ $item['slug'] ] = $item;
		}

		$resolved = array();

		foreach ( $definitions[ $preset_id ] as $definition ) {
			$slug = isset( $definition['slug'] ) ? (string) $definition['slug'] : '';

			if ( '' === $slug ) {
				continue;
			}

			if ( isset( $by_slug[ $slug ] ) ) {
				$resolved[] = array_merge(
					$definition,
					array(
						'label' => $by_slug[ $slug ]['label'],
						'icon'  => $by_slug[ $slug ]['icon'],
					)
				);
				continue;
			}

			// Include core slugs even when discovery misses them (e.g. during PHPUnit).
			if ( self::is_core_admin_slug( $slug ) ) {
				$resolved[] = $definition;
			}
		}

		return $resolved;
	}

	/**
	 * Whether a slug is a common WordPress admin screen.
	 *
	 * @param string $slug Menu slug.
	 * @return bool
	 */
	private static function is_core_admin_slug( $slug ) {
		$core_slugs = array(
			'index.php',
			'edit.php',
			'upload.php',
			'edit.php?post_type=page',
			'edit-comments.php',
			'plugins.php',
			'themes.php',
			'tools.php',
			'options-general.php',
			'woocommerce',
			'edit.php?post_type=shop_order',
			'edit.php?post_type=product',
			'wc-admin',
		);

		return in_array( $slug, $core_slugs, true );
	}

	/**
	 * Apply a layout preset to plugin settings.
	 *
	 * @param string $preset_id Preset identifier.
	 * @param bool   $mark_setup_complete Whether to mark onboarding complete.
	 * @return bool True when applied.
	 */
	public static function apply_preset( $preset_id, $mark_setup_complete = true ) {
		$preset_id = sanitize_key( $preset_id );
		$all       = self::get_all_presets();

		if ( ! isset( $all[ $preset_id ] ) ) {
			return false;
		}

		$items = self::resolve_preset_top_bar_items( $preset_id );
		if ( empty( $items ) ) {
			return false;
		}

		$settings = EDMINBOOST_Settings::get();
		$cc       = isset( $settings['command_center'] ) && is_array( $settings['command_center'] )
			? $settings['command_center']
			: self::get_defaults();

		$cc['top_bar_items']   = $items;
		$cc['default_preset']  = $preset_id;
		$cc['onboarding_completed'] = $mark_setup_complete;

		if ( ! empty( $all[ $preset_id ]['persona'] ) ) {
			$cc['persona'] = sanitize_key( $all[ $preset_id ]['persona'] );
		}

		$settings['command_center'] = $cc;
		$settings['enabled']        = true;

		$sanitized = EDMINBOOST_Settings::sanitize( $settings );
		update_option( EDMINBOOST_Settings::OPTION_NAME, $sanitized, false );

		return true;
	}

	/**
	 * Apply a look skin (behavior only).
	 *
	 * @param string $skin_id Skin identifier.
	 * @param bool   $mark_setup_complete Whether to mark onboarding complete.
	 * @return bool True when applied.
	 */
	public static function apply_look_skin( $skin_id, $mark_setup_complete = true ) {
		$skin_id = sanitize_key( $skin_id );
		$skins   = self::get_look_skins();

		if ( ! isset( $skins[ $skin_id ] ) ) {
			return false;
		}

		$settings = EDMINBOOST_Settings::get();
		$cc       = isset( $settings['command_center'] ) && is_array( $settings['command_center'] )
			? $settings['command_center']
			: self::get_defaults();

		$cc['behavior']             = $skins[ $skin_id ]['behavior'];
		$cc['look_skin']            = $skin_id;
		$cc['onboarding_completed'] = $mark_setup_complete || ! empty( $cc['onboarding_completed'] );

		$settings['command_center'] = $cc;
		$settings['enabled']        = true;

		$sanitized = EDMINBOOST_Settings::sanitize( $settings );
		update_option( EDMINBOOST_Settings::OPTION_NAME, $sanitized, false );

		return true;
	}

	/**
	 * Human-readable label for the active default preset.
	 *
	 * @param array|null $cc_settings Optional CC settings.
	 * @return string
	 */
	public static function get_active_preset_label( $cc_settings = null ) {
		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		$preset_id = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : '';
		$all       = self::get_all_presets();

		if ( isset( $all[ $preset_id ]['name'] ) ) {
			return $all[ $preset_id ]['name'];
		}

		return __( 'Custom layout', EDMINBOOST_TEXT_DOMAIN );
	}

	/**
	 * All presets (system + user-saved).
	 *
	 * @return array[]
	 */
	public static function get_all_presets() {
		$cc_settings = self::get_settings();
		$system      = self::get_system_presets();
		$custom      = isset( $cc_settings['presets'] ) && is_array( $cc_settings['presets'] )
			? $cc_settings['presets']
			: array();

		return array_merge( $system, $custom );
	}

	/**
	 * Discover installed admin menu items for the layout studio.
	 *
	 * Includes top-level sidebar entries and submenu pages (e.g. taxonomy screens).
	 *
	 * @return array[] Each item: slug, label, icon, source (top|submenu).
	 */
	public static function get_discovered_menu_items() {
		global $menu, $submenu;

		$items      = array();
		$seen_slugs = array();
		$parents    = array();

		if ( is_array( $menu ) ) {
			foreach ( $menu as $menu_item ) {
				if ( empty( $menu_item[2] ) ) {
					continue;
				}

				$slug = (string) $menu_item[2];
				if ( self::is_ignorable_menu_slug( $slug ) ) {
					continue;
				}

				$label = wp_strip_all_tags( (string) $menu_item[0] );
				if ( '' === $label ) {
					$label = $slug;
				}

				$icon = self::normalize_menu_icon( isset( $menu_item[6] ) ? $menu_item[6] : '' );

				$parents[ $slug ] = array(
					'label' => $label,
					'icon'  => $icon,
				);

				$items[]      = array(
					'slug'   => $slug,
					'label'  => $label,
					'icon'   => $icon,
					'source' => 'top',
				);
				$seen_slugs[] = $slug;
			}
		}

		if ( is_array( $submenu ) ) {
			foreach ( $submenu as $parent_slug => $submenu_items ) {
				if ( ! is_array( $submenu_items ) ) {
					continue;
				}

				$parent_slug  = (string) $parent_slug;
				$parent_label = isset( $parents[ $parent_slug ]['label'] ) ? $parents[ $parent_slug ]['label'] : $parent_slug;
				$parent_icon  = isset( $parents[ $parent_slug ]['icon'] ) ? $parents[ $parent_slug ]['icon'] : 'dashicons-admin-generic';

				foreach ( $submenu_items as $submenu_item ) {
					if ( empty( $submenu_item[2] ) ) {
						continue;
					}

					$slug = (string) $submenu_item[2];
					if ( self::is_ignorable_menu_slug( $slug ) || $slug === $parent_slug ) {
						continue;
					}

					if ( in_array( $slug, $seen_slugs, true ) ) {
						continue;
					}

					$sub_label = wp_strip_all_tags( (string) $submenu_item[0] );
					if ( '' === $sub_label ) {
						$sub_label = $slug;
					}

					$items[] = array(
						'slug'   => $slug,
						'label'  => sprintf(
							/* translators: 1: parent menu label, 2: submenu label */
							__( '%1$s → %2$s', EDMINBOOST_TEXT_DOMAIN ),
							$parent_label,
							$sub_label
						),
						'icon'   => $parent_icon,
						'source' => 'submenu',
					);

					$seen_slugs[] = $slug;
				}
			}
		}

		usort(
			$items,
			function ( $a, $b ) {
				return strcasecmp( $a['label'], $b['label'] );
			}
		);

		return $items;
	}

	/**
	 * Discover admin menu as a hierarchical tree for Menu Studio.
	 *
	 * @return array[] Each item: slug, label, icon, children[].
	 */
	public static function get_discovered_menu_tree() {
		global $menu, $submenu;

		$tree = array();

		if ( ! is_array( $menu ) ) {
			return $tree;
		}

		foreach ( $menu as $menu_item ) {
			if ( empty( $menu_item[2] ) ) {
				continue;
			}

			$slug = (string) $menu_item[2];
			if ( self::is_ignorable_menu_slug( $slug ) ) {
				continue;
			}

			$label = wp_strip_all_tags( (string) $menu_item[0] );
			if ( '' === $label ) {
				$label = $slug;
			}

			$icon     = self::normalize_menu_icon( isset( $menu_item[6] ) ? $menu_item[6] : '' );
			$children = array();

			if ( is_array( $submenu ) && isset( $submenu[ $slug ] ) && is_array( $submenu[ $slug ] ) ) {
				foreach ( $submenu[ $slug ] as $submenu_item ) {
					if ( empty( $submenu_item[2] ) ) {
						continue;
					}

					$child_slug = (string) $submenu_item[2];
					if ( self::is_ignorable_menu_slug( $child_slug ) || $child_slug === $slug ) {
						continue;
					}

					$child_label = wp_strip_all_tags( (string) $submenu_item[0] );
					if ( '' === $child_label ) {
						$child_label = $child_slug;
					}

					$children[] = array(
						'slug'  => $child_slug,
						'label' => $child_label,
					);
				}
			}

			$tree[] = array(
				'slug'     => $slug,
				'label'    => $label,
				'icon'     => $icon,
				'children' => $children,
			);
		}

		return $tree;
	}

	/**
	 * Resolve Menu Studio sidebar order for the editor canvas.
	 *
	 * @param array|null $menu_studio Menu Studio settings.
	 * @return array[] Ordered top-level items with slug, label, icon, children.
	 */
	public static function resolve_menu_studio_order( $menu_studio = null ) {
		if ( null === $menu_studio ) {
			$cc_settings  = self::get_settings();
			$menu_studio  = isset( $cc_settings['menu_studio'] ) && is_array( $cc_settings['menu_studio'] )
				? $cc_settings['menu_studio']
				: self::get_menu_studio_defaults();
		}

		$tree         = self::get_discovered_menu_tree();
		$hidden       = isset( $menu_studio['hidden_items'] ) && is_array( $menu_studio['hidden_items'] )
			? $menu_studio['hidden_items']
			: array();
		$saved_order  = isset( $menu_studio['order'] ) && is_array( $menu_studio['order'] )
			? $menu_studio['order']
			: array();
		$submenu_map  = isset( $menu_studio['submenu_order'] ) && is_array( $menu_studio['submenu_order'] )
			? $menu_studio['submenu_order']
			: array();
		$custom_items = isset( $menu_studio['custom_items'] ) && is_array( $menu_studio['custom_items'] )
			? $menu_studio['custom_items']
			: array();

		$by_slug = array();
		foreach ( $tree as $item ) {
			$by_slug[ $item['slug'] ] = $item;
		}

		foreach ( $custom_items as $custom ) {
			if ( empty( $custom['id'] ) || empty( $custom['label'] ) ) {
				continue;
			}

			if ( ! empty( $custom['parent'] ) ) {
				continue;
			}

			$slug = 'edminboost_ms_' . sanitize_key( $custom['id'] );
			$by_slug[ $slug ] = array(
				'slug'     => $slug,
				'label'    => $custom['label'],
				'icon'     => isset( $custom['icon'] ) ? $custom['icon'] : 'dashicons-admin-links',
				'children' => array(),
				'custom'   => true,
			);
		}

		$ordered = array();
		$used    = array();

		foreach ( $saved_order as $slug ) {
			$slug = (string) $slug;
			if ( in_array( $slug, $hidden, true ) || ! isset( $by_slug[ $slug ] ) ) {
				continue;
			}

			$item = $by_slug[ $slug ];

			if ( ! empty( $item['children'] ) && isset( $submenu_map[ $slug ] ) && is_array( $submenu_map[ $slug ] ) ) {
				$item['children'] = self::reorder_submenu_children( $item['children'], $submenu_map[ $slug ] );
			}

			$ordered[]     = $item;
			$used[ $slug ] = true;
		}

		foreach ( $by_slug as $slug => $item ) {
			if ( ! empty( $used[ $slug ] ) || in_array( $slug, $hidden, true ) ) {
				continue;
			}

			if ( ! empty( $item['children'] ) && isset( $submenu_map[ $slug ] ) && is_array( $submenu_map[ $slug ] ) ) {
				$item['children'] = self::reorder_submenu_children( $item['children'], $submenu_map[ $slug ] );
			}

			$ordered[] = $item;
		}

		return $ordered;
	}

	/**
	 * Reorder submenu children according to a saved slug list.
	 *
	 * @param array  $children    Discovered children.
	 * @param string[] $slug_order Desired slug order.
	 * @return array[]
	 */
	private static function reorder_submenu_children( $children, $slug_order ) {
		$by_slug = array();
		foreach ( $children as $child ) {
			$by_slug[ $child['slug'] ] = $child;
		}

		$ordered = array();
		$used    = array();

		foreach ( $slug_order as $slug ) {
			$slug = (string) $slug;
			if ( ! isset( $by_slug[ $slug ] ) ) {
				continue;
			}

			$ordered[]     = $by_slug[ $slug ];
			$used[ $slug ] = true;
		}

		foreach ( $children as $child ) {
			if ( empty( $used[ $child['slug'] ] ) ) {
				$ordered[] = $child;
			}
		}

		return $ordered;
	}

	/**
	 * Whether a menu slug should be excluded from discovery.
	 *
	 * @param string $slug Menu slug.
	 * @return bool
	 */
	private static function is_ignorable_menu_slug( $slug ) {
		if ( '' === $slug || '---' === $slug ) {
			return true;
		}

		return 0 === strpos( $slug, 'separator' );
	}

	/**
	 * Normalize a WordPress admin menu icon value to a dashicons class.
	 *
	 * @param mixed $icon Raw menu icon value.
	 * @return string
	 */
	private static function normalize_menu_icon( $icon ) {
		if ( empty( $icon ) ) {
			return 'dashicons-admin-generic';
		}

		$icon = (string) $icon;

		if ( false !== strpos( $icon, 'dashicons-' ) ) {
			return $icon;
		}

		return 'dashicons-' . $icon;
	}

	/**
	 * Get WordPress roles for the role assignment matrix.
	 *
	 * @return array Associative role key => display name.
	 */
	public static function get_assignable_roles() {
		if ( ! function_exists( 'wp_roles' ) ) {
			return array();
		}

		return wp_roles()->get_names();
	}

	/**
	 * Badge telemetry source options.
	 *
	 * @return array
	 */
	public static function get_badge_sources() {
		return array(
			''              => __( 'None', EDMINBOOST_TEXT_DOMAIN ),
			'wc_orders'     => __( 'WooCommerce — unread orders', EDMINBOOST_TEXT_DOMAIN ),
			'wc_reviews'    => __( 'WooCommerce — pending reviews', EDMINBOOST_TEXT_DOMAIN ),
			'comments'      => __( 'WordPress — pending comments', EDMINBOOST_TEXT_DOMAIN ),
			'updates'       => __( 'WordPress — available updates', EDMINBOOST_TEXT_DOMAIN ),
			'forms_entries' => __( 'WPForms — unread entries', EDMINBOOST_TEXT_DOMAIN ),
		);
	}

	/**
	 * Popular dashicons for the icon picker.
	 *
	 * @return string[]
	 */
	public static function get_dashicon_options() {
		return array(
			'dashicons-admin-generic',
			'dashicons-admin-plugins',
			'dashicons-admin-settings',
			'dashicons-admin-tools',
			'dashicons-analytics',
			'dashicons-art',
			'dashicons-awards',
			'dashicons-backup',
			'dashicons-calendar-alt',
			'dashicons-cart',
			'dashicons-chart-bar',
			'dashicons-clipboard',
			'dashicons-cloud',
			'dashicons-dashboard',
			'dashicons-edit',
			'dashicons-email',
			'dashicons-forms',
			'dashicons-format-gallery',
			'dashicons-groups',
			'dashicons-hammer',
			'dashicons-images-alt2',
			'dashicons-layout',
			'dashicons-media-document',
			'dashicons-megaphone',
			'dashicons-performance',
			'dashicons-products',
			'dashicons-search',
			'dashicons-shield',
			'dashicons-store',
			'dashicons-tag',
			'dashicons-welcome-view-site',
		);
	}
}
