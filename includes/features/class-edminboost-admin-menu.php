<?php
/**
 * Admin menu customization.
 *
 * Purpose: Hide selected top-level admin menu items via remove_menu_page().
 * Hook: admin_menu (priority 999)
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hides selected admin menu items.
 */
class EDMINBOOST_Admin_Menu extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'admin_menu';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Admin Menu';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Hide admin menu items you do not use.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'hide_menu_items' ), 999 );
	}

	/**
	 * Menu slugs that must never be hidden.
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
	 * Remove configured menu pages.
	 *
	 * @return void
	 */
	public function hide_menu_items() {
		$settings     = EDMINBOOST_Settings::get_feature_settings( 'admin_menu' );
		$hidden_items = isset( $settings['hidden_items'] ) ? $settings['hidden_items'] : array();

		if ( empty( $hidden_items ) || ! is_array( $hidden_items ) ) {
			return;
		}

		foreach ( $hidden_items as $menu_slug ) {
			if ( in_array( $menu_slug, self::get_protected_slugs(), true ) ) {
				continue;
			}

			remove_menu_page( $menu_slug );
		}
	}

	/**
	 * Get available top-level admin menu items for the settings UI.
	 *
	 * @return array Associative array of slug => label.
	 */
	public static function get_available_menu_items() {
		global $menu;

		$items = array();

		if ( ! is_array( $menu ) ) {
			return $items;
		}

		foreach ( $menu as $menu_item ) {
			if ( empty( $menu_item[2] ) ) {
				continue;
			}

			$slug  = (string) $menu_item[2];
			$label = wp_strip_all_tags( (string) $menu_item[0] );

			if ( '' === $label ) {
				$label = $slug;
			}

			$items[ $slug ] = $label;
		}

		asort( $items );

		return $items;
	}
}
