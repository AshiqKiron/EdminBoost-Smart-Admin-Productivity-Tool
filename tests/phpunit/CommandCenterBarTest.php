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
		$this->enable_pro_plan();
		$this->skip_if_pro_package_unloaded();

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
	 * Capability filter removes inaccessible slugs even when role visibility allows them.
	 */
	public function test_get_items_for_current_user_respects_saved_role_visibility_override() {
		$this->enable_pro_plan();
		$this->skip_if_pro_package_unloaded();

		$subscriber_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber_id );

		$this->seed_settings(
			array(
				'command_center' => array(
					'top_bar_items' => array(
						array(
							'slug'         => 'index.php',
							'label'        => 'Dashboard',
							'icon'         => 'dashicons-dashboard',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
						array(
							'slug'         => 'plugins.php',
							'label'        => 'Plugins',
							'icon'         => 'dashicons-admin-plugins',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
					),
					'role_visibility' => array(
						'subscriber' => array(),
					),
				),
			)
		);

		$items = EDMINBOOST_Command_Center_Bar::get_items_for_current_user();
		$slugs = wp_list_pluck( $items, 'slug' );

		$this->assertContains( 'index.php', $slugs );
		$this->assertNotContains( 'plugins.php', $slugs );
	}

	/**
	 * has_drawer_items detects drawer interaction.
	 */
	public function test_has_drawer_items() {
		$this->enable_pro_plan();
		$this->skip_if_pro_package_unloaded();

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
	 * Mapper preview AJAX requires a valid mapper context nonce.
	 */
	public function test_is_mapper_preview_context_requires_mapper_context_nonce() {
		$this->assertFalse( EDMINBOOST_Command_Center_Bar::is_mapper_preview_context() );

		$_POST['mapper_context_nonce'] = wp_create_nonce( 'edminboost_cc_mapper_context' );

		$this->assertTrue( EDMINBOOST_Command_Center_Bar::is_mapper_preview_context() );
	}

	/**
	 * Unlicensed premium builds still register redirect-only bar nodes (same as WordPress.org).
	 */
	public function test_register_nodes_redirect_only_when_pro_inactive_on_premium_build() {
		if ( ! EDMINBOOST_Plan::is_direct_build() ) {
			$this->markTestSkipped( 'Premium build only.' );
		}

		$this->assertFalse( EDMINBOOST_Pro::has_licensed_admin_ui() );

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

		$admin_bar = new class() {
			/** @var array<string, array<string, mixed>> */
			public $nodes = array();

			/**
			 * @param array<string, mixed> $args Node args.
			 * @return void
			 */
			public function add_node( $args ) {
				if ( ! empty( $args['id'] ) ) {
					$this->nodes[ $args['id'] ] = $args;
				}
			}

			/**
			 * @param string $id Node ID.
			 * @return object|null
			 */
			public function get_node( $id ) {
				return isset( $this->nodes[ $id ] ) ? (object) $this->nodes[ $id ] : null;
			}
		};

		EDMINBOOST_Command_Center_Bar::register_nodes( $admin_bar );

		$node_id = EDMINBOOST_Command_Center_Bar::get_node_id( 'edit.php', '' );
		$this->assertNotNull( $admin_bar->get_node( $node_id ) );
	}

	/**
	 * External top bar URLs fall back to the admin dashboard at runtime.
	 */
	public function test_get_item_url_rejects_external_urls() {
		$method = new ReflectionMethod( 'EDMINBOOST_Command_Center_Bar', 'get_item_url' );
		$method->setAccessible( true );

		$url = $method->invoke( null, 'https://evil.example/phish' );

		$this->assertSame( admin_url(), $url );
	}
}
