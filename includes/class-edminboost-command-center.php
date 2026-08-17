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
	 * Default Command Center settings shape.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'onboarding_completed' => false,
			'persona'                => '',
			'top_bar_items'          => array(),
			'presets'                => array(),
			'default_preset'         => 'system_client',
			'role_assignments'       => array(),
			'role_visibility'        => array(),
			'behavior'               => array(
				'drawer_width'         => 'standard',
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

		return $merged;
	}

	/**
	 * Navigation items for Command Center pages.
	 *
	 * @return array[]
	 */
	public static function get_nav_items() {
		$base = EDMINBOOST_Admin::PAGE_SLUG;

		return array(
			array(
				'slug'  => $base . self::PAGE_ONBOARDING,
				'label' => __( 'Onboarding', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_MAPPER,
				'label' => __( 'Layout Studio', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_PRESETS,
				'label' => __( 'Presets & Roles', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_BEHAVIOR,
				'label' => __( 'Behavior & Style', EDMINBOOST_TEXT_DOMAIN ),
			),
		);
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
				'name'        => __( 'Client / Content Editor', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Clean top bar focused on content creation and media.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'persona'     => 'client',
			),
			'system_ecommerce' => array(
				'name'        => __( 'E-Commerce Manager', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'WooCommerce dashboards, orders, and support shortcuts.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'persona'     => 'ecommerce',
			),
			'system_developer' => array(
				'name'        => __( 'Agency Developer / Power User', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Full plugin mapping with debugging and slide-out drawers.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'persona'     => 'developer',
			),
		);
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
