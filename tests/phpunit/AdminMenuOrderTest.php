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
	 * Dashboard is the first EdminBoost submenu item.
	 */
	public function test_dashboard_is_first_plugin_submenu() {
		global $submenu;

		set_current_screen( 'toplevel_page_' . EDMINBOOST_Admin::PAGE_SLUG );
		do_action( 'admin_menu' );

		$slug = EDMINBOOST_Admin::PAGE_SLUG;

		$this->assertIsArray( $submenu[ $slug ] ?? null );
		$this->assertNotEmpty( $submenu[ $slug ] );

		$first = reset( $submenu[ $slug ] );

		$this->assertSame( $slug, $first[2] );
		$this->assertSame( __( 'Dashboard', 'edminboost' ), wp_strip_all_tags( $first[0] ) );

		$labels = array();
		foreach ( $submenu[ $slug ] as $item ) {
			$labels[] = wp_strip_all_tags( (string) $item[0] ) . ' (' . $item[2] . ')';
		}

		$this->assertSame(
			array(
				__( 'Dashboard', 'edminboost' ) . ' (' . $slug . ')',
				__( 'Layouts', 'edminboost' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_PRESETS . ')',
				__( 'Theme', 'edminboost' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_APPEARANCE . ')',
				__( 'Top Bar', 'edminboost' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_MAPPER . ')',
				__( 'Menu Studio', 'edminboost' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_MENU_STUDIO . ')',
				__( 'Billing', 'edminboost' ) . ' (' . $slug . EDMINBOOST_Command_Center::PAGE_BILLING . ')',
				__( 'Settings', 'edminboost' ) . ' (' . $slug . '-settings)',
			),
			$labels
		);
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
		do_action( 'admin_menu' );

		$first = reset( $submenu[ $slug ] );

		$this->assertSame( $slug, $first[2] );
		$this->assertSame( __( 'Dashboard', 'edminboost' ), wp_strip_all_tags( $first[0] ) );
	}

	/**
	 * Tab-only CC pages set a string admin title before admin-header loads.
	 */
	public function test_tab_only_pages_bind_admin_title() {
		global $title;

		$title = null;
		do_action( 'admin_menu' );

		$slug = EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRODUCTIVITY;
		$hook = get_plugin_page_hookname( $slug, null );

		$title = null;
		do_action( "load-{$hook}" );

		$this->assertSame( __( 'Productivity', 'edminboost' ), $title );
	}
}
