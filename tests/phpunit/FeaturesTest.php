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
