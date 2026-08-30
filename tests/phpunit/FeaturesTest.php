<?php
/**
 * Feature module tests.
 *
 * @package EdminBoost
 */

/**
 * EdminBoost feature tests.
 */
class FeaturesTest extends Edminboost_Test_Case {

	/**
	 * Custom sidebar links survive role preset resolution and appear in the admin menu.
	 */
	public function test_resolve_menu_studio_for_user_preserves_custom_items() {
		$custom_items = array(
			array(
				'id'     => 'custom_test_link',
				'label'  => 'All Pages',
				'path'   => 'edit.php?post_type=page',
				'icon'   => 'dashicons-admin-links',
				'parent' => '',
			),
		);

		$cc_settings = array(
			'default_preset'   => 'system_client',
			'menu_studio'      => array(
				'enabled'      => true,
				'custom_items' => $custom_items,
			),
			'role_assignments' => array(
				'administrator' => 'system_client',
			),
		);

		$menu = EDMINBOOST_Command_Center::resolve_menu_studio_for_user( $cc_settings );

		$this->assertSame( $custom_items, $menu['custom_items'] );
		$this->assertContains(
			EDMINBOOST_Menu_Studio::get_custom_menu_slug( 'custom_test_link' ),
			$menu['order']
		);
	}

	/**
	 * Custom sidebar links register on the admin menu when Menu Studio is active.
	 */
	public function test_menu_studio_registers_custom_sidebar_links() {
		global $menu;

		$custom_slug = EDMINBOOST_Menu_Studio::get_custom_menu_slug( 'custom_runtime_link' );

		$this->seed_settings(
			array(
				'command_center' => array(
					'menu_studio' => array(
						'enabled'      => true,
						'order'        => array( 'index.php', $custom_slug ),
						'custom_items' => array(
							array(
								'id'     => 'custom_runtime_link',
								'label'  => 'Reports',
								'path'   => 'edit.php',
								'icon'   => 'dashicons-admin-links',
								'parent' => '',
							),
						),
					),
				),
			)
		);

		set_current_screen( 'index.php' );
		do_action( 'admin_menu' );

		$slugs = array();
		foreach ( $menu as $item ) {
			if ( ! empty( $item[2] ) ) {
				$slugs[] = (string) $item[2];
			}
		}

		$this->assertContains( $custom_slug, $slugs );
	}

	/**
	 * Protected menu slugs list is stable.
	 */
	public function test_menu_studio_protected_slugs() {
		$protected = EDMINBOOST_Menu_Studio::get_protected_slugs();

		$this->assertContains( 'index.php', $protected );
		$this->assertContains( 'plugins.php', $protected );
		$this->assertContains( EDMINBOOST_Admin::PAGE_SLUG, $protected );
	}

	/**
	 * apply_menu_changes skips protected slugs when hiding items.
	 */
	public function test_menu_studio_skips_protected_slugs() {
		global $menu;

		$menu = array(
			array( 'Dashboard', 'read', 'index.php', '', 'menu-top', 'menu-dashboard', 'dashicons-dashboard' ),
			array( 'Posts', 'edit_posts', 'edit.php', '', 'menu-top', 'menu-posts', 'dashicons-admin-post' ),
		);

		$this->seed_settings(
			array(
				'command_center' => array(
					'menu_studio' => array(
						'enabled'      => true,
						'hidden_items' => array( 'index.php', 'edit.php' ),
					),
				),
			)
		);

		EDMINBOOST_Menu_Studio::apply_menu_changes();

		$slugs = array();
		foreach ( $menu as $item ) {
			if ( ! empty( $item[2] ) ) {
				$slugs[] = $item[2];
			}
		}

		$this->assertContains( 'index.php', $slugs );
		$this->assertNotContains( 'edit.php', $slugs );
	}

	/**
	 * Hide notices skips plugin screens.
	 */
	public function test_hide_notices_skips_plugin_screen() {
		$this->seed_settings(
			array(
				'features' => array(
					'hide_admin_notices' => true,
				),
			)
		);

		$feature = new EDMINBOOST_Hide_Notices();
		$feature->register_hooks();

		$hook = 'toplevel_page_' . EDMINBOOST_Admin::PAGE_SLUG;
		$feature->enqueue_hide_styles( $hook );

		$this->assertFalse( wp_style_is( 'edminboost-hide-notices', 'enqueued' ) );
	}

	/**
	 * Hide notices enqueues on other admin screens.
	 */
	public function test_hide_notices_enqueues_off_plugin_screen() {
		$this->seed_settings(
			array(
				'features' => array(
					'hide_admin_notices' => true,
				),
			)
		);

		$feature = new EDMINBOOST_Hide_Notices();
		$feature->enqueue_hide_styles( 'index.php' );

		$this->assertTrue( wp_style_is( 'edminboost-hide-notices', 'enqueued' ) );
	}

	/**
	 * Uninstall removes plugin options.
	 */
	public function test_uninstall_removes_options() {
		update_option( EDMINBOOST_Settings::OPTION_NAME, EDMINBOOST_Settings::get_defaults() );
		update_option( EDMINBOOST_Settings::VERSION_OPTION, EDMINBOOST_VERSION );

		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', true );
		}

		require dirname( __DIR__, 2 ) . '/uninstall.php';

		$this->assertFalse( get_option( EDMINBOOST_Settings::OPTION_NAME ) );
		$this->assertFalse( get_option( EDMINBOOST_Settings::VERSION_OPTION ) );
	}
}
