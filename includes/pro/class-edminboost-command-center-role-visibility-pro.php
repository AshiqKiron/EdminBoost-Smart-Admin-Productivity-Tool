<?php
/**
 * Premium build: per-role Command Center visibility (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Role visibility matrix — sanitize, Menu Studio, and top bar runtime.
 */
class EDMINBOOST_Command_Center_Role_Visibility_Pro {

	/**
	 * Register Pro-only role visibility hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'edminboost_sanitize_command_center', array( __CLASS__, 'filter_sanitize_command_center' ), 10, 2 );
		add_filter( 'edminboost_menu_studio_for_user', array( __CLASS__, 'filter_menu_studio_for_user' ), 10, 3 );
		add_filter( 'edminboost_top_bar_items_for_user', array( __CLASS__, 'filter_top_bar_items_for_user' ), 10, 3 );
	}

	/**
	 * Sanitize role visibility when licensed admin UI is active.
	 *
	 * @param array $output Sanitized Command Center settings.
	 * @param array $raw    Raw Command Center input.
	 * @return array
	 */
	public static function filter_sanitize_command_center( $output, $raw ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() || ! is_array( $output ) || ! is_array( $raw ) ) {
			return $output;
		}

		if ( ! isset( $raw['role_visibility'] ) || ! is_array( $raw['role_visibility'] ) ) {
			return $output;
		}

		$output['role_visibility'] = self::sanitize_role_visibility( $raw['role_visibility'] );

		return $output;
	}

	/**
	 * Apply role visibility to resolved Menu Studio settings.
	 *
	 * @param array    $menu_studio Resolved Menu Studio settings.
	 * @param array    $cc_settings Command Center settings.
	 * @param WP_User  $user        Current user.
	 * @return array
	 */
	public static function filter_menu_studio_for_user( $menu_studio, $cc_settings, $user ) {
		if ( ! self::should_apply_role_visibility( $cc_settings ) || ! $user instanceof WP_User || empty( $user->roles ) ) {
			return $menu_studio;
		}

		return self::apply_to_menu_studio( $menu_studio, $user->roles, $cc_settings );
	}

	/**
	 * Apply role visibility to resolved top bar items.
	 *
	 * @param array[]  $items       Top bar items.
	 * @param array    $cc_settings Command Center settings.
	 * @param WP_User  $user        Current user.
	 * @return array[]
	 */
	public static function filter_top_bar_items_for_user( $items, $cc_settings, $user ) {
		if ( ! self::should_apply_role_visibility( $cc_settings ) || ! $user instanceof WP_User || empty( $user->roles ) || empty( $items ) ) {
			return $items;
		}

		$role_visibility = self::get_role_visibility_from_settings( $cc_settings );
		$visible         = array();

		foreach ( $items as $item ) {
			$slug = isset( $item['slug'] ) ? $item['slug'] : '';
			if ( '' === $slug || ! self::is_slug_visible_for_roles( $slug, $user->roles, $role_visibility ) ) {
				continue;
			}

			$visible[] = $item;
		}

		return $visible;
	}

	/**
	 * Whether role visibility should run for the current build and settings.
	 *
	 * @param array $cc_settings Command Center settings.
	 * @return bool
	 */
	private static function should_apply_role_visibility( $cc_settings ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return false;
		}

		$role_visibility = self::get_role_visibility_from_settings( $cc_settings );

		return ! empty( $role_visibility );
	}

	/**
	 * @param array $cc_settings Command Center settings.
	 * @return array
	 */
	private static function get_role_visibility_from_settings( $cc_settings ) {
		if ( ! is_array( $cc_settings ) ) {
			return array();
		}

		return isset( $cc_settings['role_visibility'] ) && is_array( $cc_settings['role_visibility'] )
			? $cc_settings['role_visibility']
			: array();
	}

	/**
	 * Sanitize per-role icon visibility (stores hidden slugs).
	 *
	 * @param array $raw_visibility Submitted visible slugs per role.
	 * @return array
	 */
	public static function sanitize_role_visibility( $raw_visibility ) {
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
	 * Apply per-role visibility overrides to Menu Studio settings.
	 *
	 * @param array    $menu_studio Base Menu Studio settings.
	 * @param string[] $user_roles  Current user roles.
	 * @param array    $cc_settings Command Center settings.
	 * @return array
	 */
	public static function apply_to_menu_studio( $menu_studio, $user_roles, $cc_settings ) {
		$role_visibility = self::get_role_visibility_from_settings( $cc_settings );

		if ( empty( $role_visibility ) || empty( $user_roles ) ) {
			return $menu_studio;
		}

		$defaults    = EDMINBOOST_Command_Center::get_menu_studio_defaults();
		$menu_studio = wp_parse_args( $menu_studio, $defaults );

		if ( empty( $menu_studio['enabled'] ) ) {
			$menu_studio['enabled'] = true;
		}

		if ( empty( $menu_studio['order'] ) ) {
			foreach ( EDMINBOOST_Command_Center::get_discovered_menu_tree() as $item ) {
				if ( ! empty( $item['slug'] ) ) {
					$menu_studio['order'][] = (string) $item['slug'];
				}
			}
		}

		$hidden = isset( $menu_studio['hidden_items'] ) && is_array( $menu_studio['hidden_items'] )
			? $menu_studio['hidden_items']
			: array();
		$order  = isset( $menu_studio['order'] ) && is_array( $menu_studio['order'] )
			? $menu_studio['order']
			: array();

		foreach ( EDMINBOOST_Command_Center::get_role_matrix_menu_items() as $item ) {
			if ( empty( $item['slug'] ) ) {
				continue;
			}

			$slug   = (string) $item['slug'];
			$source = isset( $item['source'] ) ? (string) $item['source'] : 'top';

			if ( self::is_slug_visible_for_roles( $slug, $user_roles, $role_visibility ) ) {
				continue;
			}

			if ( 'submenu' === $source ) {
				if ( ! in_array( $slug, $hidden, true ) ) {
					$hidden[] = $slug;
				}
				continue;
			}

			$top_slug = $slug;
			if ( in_array( $top_slug, EDMINBOOST_Menu_Studio::get_protected_slugs(), true ) ) {
				continue;
			}

			if ( ! in_array( $top_slug, $hidden, true ) ) {
				$hidden[] = $top_slug;
			}

			$order = array_values( array_diff( $order, array( $top_slug ) ) );
		}

		$menu_studio['hidden_items'] = $hidden;
		$menu_studio['order']        = $order;

		return $menu_studio;
	}

	/**
	 * Whether a menu slug is visible for at least one of the user's roles.
	 *
	 * @param string   $slug            Menu slug.
	 * @param string[] $user_roles      Role slugs.
	 * @param array    $role_visibility Hidden slugs keyed by role.
	 * @return bool
	 */
	public static function is_slug_visible_for_roles( $slug, $user_roles, $role_visibility ) {
		foreach ( $user_roles as $role ) {
			$hidden_for_role = isset( $role_visibility[ $role ] ) && is_array( $role_visibility[ $role ] )
				? $role_visibility[ $role ]
				: array();

			if ( ! in_array( $slug, $hidden_for_role, true ) ) {
				return true;
			}
		}

		return false;
	}
}
