<?php
/**
 * Command Center bar tests.
 *
 * @package EdminBoost
 */

/**
 * EDMINBOOST_Command_Center_Bar tests.
 */
class CommandCenterBarTest extends Edminboost_Test_Case {

	/**
	 * Normalize relative admin slug.
	 */
	public function test_normalize_item_slug_relative_path() {
		$this->assertSame( 'edit.php', EDMINBOOST_Command_Center_Bar::normalize_item_slug( 'edit.php' ) );
	}

	/**
	 * Normalize full admin URL to relative path.
	 */
	public function test_normalize_item_slug_full_admin_url() {
		$url      = admin_url( 'edit.php?post_type=page' );
		$expected = 'edit.php?post_type=page';

		$this->assertSame( $expected, EDMINBOOST_Command_Center_Bar::normalize_item_slug( $url ) );
	}

	/**
	 * Normalize external wp-admin path from full URL.
	 */
	public function test_normalize_item_slug_external_style_url() {
		$site_url = site_url( '/wp-admin/plugins.php' );
		$this->assertSame( 'plugins.php', EDMINBOOST_Command_Center_Bar::normalize_item_slug( $site_url ) );
	}

	/**
	 * Bar inactive when plugin disabled.
	 */
	public function test_is_active_when_plugin_disabled() {
		$this->seed_settings(
			array(
				'enabled'        => false,
				'command_center' => array(
					'top_bar_items' => array(
						array(
							'slug'         => 'edit.php',
							'label'        => 'Posts',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
					),
				),
			)
		);

		$this->assertFalse( EDMINBOOST_Command_Center_Bar::is_active() );
	}

	/**
	 * Bar active when items exist and plugin enabled.
	 */
	public function test_is_active_with_top_bar_items() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'top_bar_items' => array(
						array(
							'slug'         => 'edit.php',
							'label'        => 'Posts',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
					),
				),
			)
		);

		$this->assertTrue( EDMINBOOST_Command_Center_Bar::is_active() );
	}

	/**
	 * Role visibility filters items for current user.
	 */
	public function test_get_items_for_current_user_role_visibility() {
		$editor_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $editor_id );

		$this->seed_settings(
			array(
				'command_center' => array(
					'top_bar_items' => array(
						array(
							'slug'         => 'edit.php',
							'label'        => 'Posts',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
						array(
							'slug'         => 'upload.php',
							'label'        => 'Media',
							'icon'         => 'dashicons-admin-media',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
					),
					'role_visibility' => array(
						'editor' => array( 'edit.php' ),
					),
				),
			)
		);

		$items = EDMINBOOST_Command_Center_Bar::get_items_for_current_user();
		$slugs = wp_list_pluck( $items, 'slug' );

		$this->assertContains( 'upload.php', $slugs );
		$this->assertNotContains( 'edit.php', $slugs );
	}

	/**
	 * has_drawer_items detects drawer interaction.
	 */
	public function test_has_drawer_items() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'top_bar_items' => array(
						array(
							'slug'         => 'edit.php',
							'label'        => 'Posts',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'drawer',
							'badge_source' => '',
						),
					),
				),
			)
		);

		$this->assertTrue( EDMINBOOST_Command_Center_Bar::has_drawer_items() );
	}

	/**
	 * Drawer frame body class filter adds marker class.
	 */
	public function test_filter_drawer_frame_body_class() {
		$classes = EDMINBOOST_Command_Center_Bar::filter_drawer_frame_body_class( 'wp-admin' );
		$this->assertStringContainsString( 'edminboost-cc-drawer-frame', $classes );
	}

	/**
	 * Mapper preview AJAX accepts referer from Layout Studio.
	 */
	public function test_is_mapper_preview_context_from_referer() {
		$_SERVER['HTTP_REFERER'] = admin_url(
			'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER
		);

		$this->assertTrue( EDMINBOOST_Command_Center_Bar::is_mapper_preview_context() );
	}
}
