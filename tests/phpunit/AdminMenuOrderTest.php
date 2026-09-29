<?php
/**
 * Admin menu order tests.
 *
 * @package EdminBoost
 */

/**
 * EDMINBOOST_Admin menu registration tests.
 */
class AdminMenuOrderTest extends Edminboost_Test_Case {

	/**
	 * Ensure EdminBoost submenu entries exist (Freemius may clear $submenu on admin_menu).
	 *
	 * @return void
	 */
	private function ensure_edminboost_submenu() {
		global $submenu;

		$slug = EDMINBOOST_Admin::PAGE_SLUG;

		if ( ! empty( $submenu[ $slug ] ) && is_array( $submenu[ $slug ] ) ) {
			return;
		}

		$admin = new EDMINBOOST_Admin( new EDMINBOOST_Features() );
		$admin->register_menu();
		$admin->normalize_plugin_submenu();
	}

	/**
	 * Dashboard is the first EdminBoost submenu item.
	 */
	public function test_dashboard_is_first_plugin_submenu() {
		global $submenu;

		set_current_screen( 'toplevel_page_' . EDMINBOOST_Admin::PAGE_SLUG );
		$this->ensure_edminboost_submenu();

		$slug = EDMINBOOST_Admin::PAGE_SLUG;

		$this->assertIsArray( $submenu[ $slug ] ?? null );
		$this->assertNotEmpty( $submenu[ $slug ] );

		$first = reset( $submenu[ $slug ] );

		$this->assertSame( $slug, $first[2] );
		$this->assertSame( __( 'Dashboard', 'edminboost-admin-customization' ), wp_strip_all_tags( $first[0] ) );

		$labels = array();
		foreach ( $submenu[ $slug ] as $item ) {
			$labels[] = wp_strip_all_tags( (string) $item[0] ) . ' (' . $item[2] . ')';
		}

		$expected = array(
			__( 'Dashboard', 'edminboost-admin-customization' ) . ' (' . $slug . ')',
			__( 'Layouts', 'edminboost-admin-customization' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_PRESETS . ')',
			__( 'Theme', 'edminboost-admin-customization' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_APPEARANCE . ')',
			__( 'Top Bar', 'edminboost-admin-customization' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_MAPPER . ')',
			__( 'Menu Studio', 'edminboost-admin-customization' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_MENU_STUDIO . ')',
			__( 'Billing', 'edminboost-admin-customization' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_BILLING . ')',
		);

		if ( EDMINBOOST_Pro::shows_pro_settings_ui() ) {
			$expected[] = __( 'Settings', 'edminboost-admin-customization' ) . ' (' . $slug . '-settings)';
		}

		$this->assertSame( $expected, $labels );
	}

	/**
	 * Menu Studio submenu reorder keeps Dashboard first under EdminBoost.
	 */
	public function test_menu_studio_submenu_order_keeps_dashboard_first() {
		global $submenu;

		$slug = EDMINBOOST_Admin::PAGE_SLUG;

		$this->seed_settings(
			array(
				'command_center' => array(
					'menu_studio' => array(
						'enabled'       => true,
						'submenu_order' => array(
							$slug => array(
								$slug . EDMINBOOST_Command_Center::PAGE_MAPPER,
								$slug,
								$slug . EDMINBOOST_Command_Center::PAGE_PRESETS,
							),
						),
					),
				),
			)
		);

		set_current_screen( 'toplevel_page_' . $slug );
		$this->ensure_edminboost_submenu();
		EDMINBOOST_Menu_Studio::apply_menu_changes();

		$admin = new EDMINBOOST_Admin( new EDMINBOOST_Features() );
		$admin->normalize_plugin_submenu();

		$first = reset( $submenu[ $slug ] );

		$this->assertSame( $slug, $first[2] );
		$this->assertSame( __( 'Dashboard', 'edminboost-admin-customization' ), wp_strip_all_tags( $first[0] ) );
	}

	/**
	 * Tab-only CC pages set a string admin title before admin-header loads.
	 */
	public function test_tab_only_pages_bind_admin_title() {
		global $title;

		$title = null;
		$this->ensure_edminboost_submenu();

		$slug = EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRODUCTIVITY;
		$hook = get_plugin_page_hookname( $slug, null );

		$title = null;
		do_action( "load-{$hook}" );

		$this->assertSame( __( 'Productivity', 'edminboost-admin-customization' ), $title );
	}
}
