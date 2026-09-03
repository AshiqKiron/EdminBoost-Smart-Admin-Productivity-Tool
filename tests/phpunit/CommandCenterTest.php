<?php
/**
 * Command Center tests.
 *
 * @package EdminBoost
 */

/**
 * EDMINBOOST_Command_Center tests.
 */
class CommandCenterTest extends Edminboost_Test_Case {

	/**
	 * Defaults merge with stored settings.
	 */
	public function test_get_settings_merges_defaults() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'persona' => 'client',
				),
			)
		);

		$settings = EDMINBOOST_Command_Center::get_settings();

		$this->assertSame( 'client', $settings['persona'] );
		$this->assertSame( 'standard', $settings['behavior']['drawer_width'] );
		$this->assertSame(
			EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_DEFAULT,
			$settings['behavior']['drawer_width_custom']
		);
	}

	/**
	 * Animation speed options expose expected durations.
	 */
	public function test_get_animation_speed_options() {
		$options = EDMINBOOST_Command_Center::get_animation_speed_options();

		$this->assertSame( 150, $options['fast']['ms'] );
		$this->assertSame( 300, $options['normal']['ms'] );
		$this->assertSame( 500, $options['slow']['ms'] );
		$this->assertSame( 300, EDMINBOOST_Command_Center::get_animation_duration_ms( 'normal' ) );
		$this->assertSame( 150, EDMINBOOST_Command_Center::get_animation_duration_ms( 'fast' ) );
	}

	/**
	 * Personas catalog includes expected keys.
	 */
	public function test_get_personas() {
		$personas = EDMINBOOST_Command_Center::get_personas();

		$this->assertArrayHasKey( 'friend', $personas );
		$this->assertArrayHasKey( 'family', $personas );
		$this->assertArrayHasKey( 'client_site', $personas );
		$this->assertArrayHasKey( 'personal', $personas );
		$this->assertArrayHasKey( 'small_business', $personas );
		$this->assertArrayHasKey( 'nonprofit', $personas );
		$this->assertArrayHasKey( 'agency', $personas );
		$this->assertArrayHasKey( 'client', $personas );
		$this->assertArrayHasKey( 'ecommerce', $personas );
		$this->assertArrayHasKey( 'developer', $personas );
	}

	/**
	 * Scenario presets resolve top bar items.
	 */
	public function test_resolve_preset_top_bar_items_for_system_friend() {
		$items = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( 'system_friend' );

		$this->assertNotEmpty( $items );
		$slugs = wp_list_pluck( $items, 'slug' );
		$this->assertContains( 'edit.php', $slugs );
		$this->assertContains( 'edit-comments.php', $slugs );
	}

	/**
	 * System presets are included in all presets.
	 */
	public function test_get_all_presets_includes_system() {
		$presets = EDMINBOOST_Command_Center::get_all_presets();

		$this->assertArrayHasKey( 'system_friend', $presets );
		$this->assertArrayHasKey( 'system_family', $presets );
		$this->assertArrayHasKey( 'system_client_site', $presets );
		$this->assertArrayHasKey( 'system_agency', $presets );
		$this->assertArrayHasKey( 'system_client', $presets );
		$this->assertArrayHasKey( 'system_ecommerce', $presets );
		$this->assertArrayHasKey( 'system_developer', $presets );
	}

	/**
	 * Dashicon normalization handles class strings, bare slugs, and image URLs.
	 */
	public function test_normalize_dashicon_class() {
		$this->assertSame(
			'dashicons-admin-post',
			EDMINBOOST_Command_Center::normalize_dashicon_class( 'dashicons dashicons-admin-post' )
		);
		$this->assertSame(
			'dashicons-admin-post',
			EDMINBOOST_Command_Center::normalize_dashicon_class( 'admin-post' )
		);
		$this->assertSame(
			'dashicons-admin-generic',
			EDMINBOOST_Command_Center::normalize_dashicon_class( 'data:image/svg+xml;base64,abc' )
		);
		$this->assertSame(
			'dashicons-admin-generic',
			EDMINBOOST_Command_Center::normalize_dashicon_class( 'https://example.com/icon.png' )
		);
	}

	/**
	 * Preset resolution keeps definition icons when discovered menus use image URLs.
	 */
	public function test_resolve_preset_top_bar_items_keeps_definition_icon_for_image_menu_icons() {
		global $menu;

		$menu = array(
			array(
				'Posts',
				'edit_posts',
				'edit.php',
				'Posts',
				'menu-top',
				'menu-posts',
				'data:image/svg+xml;base64,abc',
			),
		);

		$items = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( 'system_client' );

		foreach ( $items as $item ) {
			if ( 'edit.php' === $item['slug'] ) {
				$this->assertSame( 'dashicons-admin-post', $item['icon'] );
				return;
			}
		}

		$this->fail( 'Expected edit.php preset item was not resolved.' );
	}

	/**
	 * Assignable roles mirror wp-admin/user-new.php editable roles.
	 */
	public function test_get_assignable_roles_matches_editable_roles() {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$editable = get_editable_roles();
		$roles    = EDMINBOOST_Command_Center::get_assignable_roles();

		$this->assertSame( array_keys( $editable ), array_keys( $roles ) );
	}

	/**
	 * Each editable role has a built-in workflow preset and layout definition.
	 */
	public function test_role_system_presets_cover_editable_roles() {
		$presets     = EDMINBOOST_Command_Center::get_role_system_presets();
		$definitions = EDMINBOOST_Command_Center::get_preset_layout_definitions();
		$roles       = EDMINBOOST_Command_Center::get_assignable_roles();

		foreach ( $roles as $role_key => $role_name ) {
			$preset_id = EDMINBOOST_Command_Center::get_role_system_preset_id( $role_key );

			$this->assertArrayHasKey( $preset_id, $presets );
			$this->assertSame( 'workflow', $presets[ $preset_id ]['category'] );
			$this->assertArrayHasKey( $preset_id, $definitions );
			$this->assertNotEmpty( $definitions[ $preset_id ] );
		}
	}

	/**
	 * Page links follow the canonical EdminBoost submenu order.
	 */
	public function test_get_page_links() {
		$items = EDMINBOOST_Command_Center::get_page_links();
		$slugs = wp_list_pluck( $items, 'slug' );
		$base  = EDMINBOOST_Admin::PAGE_SLUG;

		$this->assertSame(
			array(
				$base,
				$base . EDMINBOOST_Command_Center::PAGE_PRESETS,
				$base . EDMINBOOST_Command_Center::PAGE_APPEARANCE,
				$base . EDMINBOOST_Command_Center::PAGE_MAPPER,
				$base . EDMINBOOST_Command_Center::PAGE_MENU_STUDIO,
				$base . EDMINBOOST_Command_Center::PAGE_BILLING,
			),
			$slugs
		);
	}

	/**
	 * Tab navigation includes sidebar pages plus tab-only feature pages.
	 */
	public function test_get_nav_items() {
		$items = EDMINBOOST_Command_Center::get_nav_items();
		$slugs = wp_list_pluck( $items, 'slug' );
		$base  = EDMINBOOST_Admin::PAGE_SLUG;

		$this->assertSame(
			array(
				$base,
				$base . EDMINBOOST_Command_Center::PAGE_PRESETS,
				$base . EDMINBOOST_Command_Center::PAGE_APPEARANCE,
				$base . EDMINBOOST_Command_Center::PAGE_MAPPER,
				$base . EDMINBOOST_Command_Center::PAGE_MENU_STUDIO,
				$base . EDMINBOOST_Command_Center::PAGE_PRODUCTIVITY,
				$base . EDMINBOOST_Command_Center::PAGE_SECURITY,
				$base . EDMINBOOST_Command_Center::PAGE_PERFORMANCE,
				$base . EDMINBOOST_Command_Center::PAGE_WHITE_LABEL,
				$base . EDMINBOOST_Command_Center::PAGE_BILLING,
			),
			$slugs
		);
	}

	/**
	 * Billing plans include Free, Pro, and Agency tiers.
	 */
	public function test_get_billing_plans() {
		$plans = EDMINBOOST_Command_Center::get_billing_plans();

		$this->assertArrayHasKey( 'free', $plans );
		$this->assertArrayHasKey( 'pro', $plans );
		$this->assertArrayHasKey( 'agency', $plans );
		$this->assertSame( 0, $plans['free']['price'] );
		$this->assertSame( 49, $plans['pro']['price'] );
		$this->assertSame( 99, $plans['agency']['price'] );
		$this->assertSame( 0, $plans['free']['sites'] );
		$this->assertSame( 1, $plans['pro']['sites'] );
		$this->assertSame( 10, $plans['agency']['sites'] );
		$this->assertSame( 'free', EDMINBOOST_Command_Center::get_active_billing_plan() );
		$this->assertGreaterThanOrEqual( 9, count( $plans['pro']['features'] ) );
		$this->assertStringContainsString( '10', $plans['pro']['features'][1] );
		$this->assertStringContainsString( '20', $plans['pro']['features'][3] );
		$this->assertStringContainsString( '5', $plans['pro']['features'][5] );
	}

	/**
	 * System client preset resolves top bar items.
	 */
	public function test_resolve_preset_top_bar_items_for_system_client() {
		$items = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( 'system_client' );

		$this->assertNotEmpty( $items );
		$this->assertSame( 'index.php', $items[0]['slug'] );
	}

	/**
	 * Virtual default preset resolves to the stored site default.
	 */
	public function test_resolve_preset_top_bar_items_for_virtual_default() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'default_preset' => 'system_developer',
				),
			)
		);

		$default_items   = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( 'system_developer' );
		$resolved_items  = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( 'default' );

		$this->assertSame( $default_items, $resolved_items );
	}

	/**
	 * Virtual custom preset resolves to saved top bar items.
	 */
	public function test_resolve_preset_top_bar_items_for_virtual_custom() {
		$custom_items = array(
			array(
				'slug'         => 'edit.php',
				'label'        => 'Posts',
				'icon'         => 'dashicons-admin-post',
				'interaction'  => 'redirect',
				'badge_source' => '',
			),
		);

		$this->seed_settings(
			array(
				'command_center' => array(
					'top_bar_items' => $custom_items,
				),
			)
		);

		$resolved = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( 'custom' );

		$this->assertSame( $custom_items, $resolved );
	}

	/**
	 * Active layout detection falls back to custom for hand-built layouts.
	 */
	public function test_detect_active_layout_preset_falls_back_to_custom() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'default_preset' => 'system_client',
					'top_bar_items'  => array(
						array(
							'slug'         => 'plugins.php',
							'label'        => 'Plugins',
							'icon'         => 'dashicons-admin-plugins',
							'interaction'  => 'drawer',
							'badge_source' => '',
						),
					),
				),
			)
		);

		$this->assertSame( 'custom', EDMINBOOST_Command_Center::detect_active_layout_preset() );
	}

	/**
	 * Picker catalog includes virtual default and custom entries.
	 */
	public function test_get_picker_presets_includes_virtual_entries() {
		$presets = EDMINBOOST_Command_Center::get_picker_presets( true );

		$this->assertArrayHasKey( 'default', $presets );
		$this->assertArrayHasKey( 'custom', $presets );
		$this->assertTrue( ! empty( $presets['default']['virtual'] ) );
		$this->assertTrue( ! empty( $presets['custom']['virtual'] ) );
	}

	/**
	 * apply_preset writes layout and marks setup complete.
	 */
	public function test_apply_preset() {
		$this->seed_settings(
			array(
				'enabled'        => false,
				'command_center' => array(
					'top_bar_items'        => array(),
					'onboarding_completed' => false,
				),
			)
		);

		$applied = EDMINBOOST_Command_Center::apply_preset( 'system_client' );
		$this->assertTrue( $applied );

		$settings = EDMINBOOST_Settings::get();
		$this->assertTrue( $settings['enabled'] );
		$this->assertNotEmpty( $settings['command_center']['top_bar_items'] );
		$this->assertTrue( $settings['command_center']['onboarding_completed'] );
		$this->assertSame( 'system_client', $settings['command_center']['default_preset'] );
		$this->assertTrue( $settings['command_center']['menu_studio']['enabled'] );
		$this->assertNotEmpty( $settings['command_center']['menu_studio']['order'] );
	}

	/**
	 * resolve_preset_menu_studio enables Menu Studio for system presets.
	 */
	public function test_resolve_preset_menu_studio_for_system_client() {
		$menu_studio = EDMINBOOST_Command_Center::resolve_preset_menu_studio( 'system_client' );

		$this->assertTrue( $menu_studio['enabled'] );
		$this->assertContains( 'index.php', $menu_studio['order'] );
		$this->assertContains( 'edit.php', $menu_studio['order'] );
		$this->assertNotContains( 'themes.php', $menu_studio['order'] );
	}

	/**
	 * resolve_preset_sidebar_preview_items returns labeled sidebar rows.
	 */
	public function test_resolve_preset_sidebar_preview_items() {
		$items = EDMINBOOST_Command_Center::resolve_preset_sidebar_preview_items( 'system_client' );

		$this->assertNotEmpty( $items );
		$this->assertArrayHasKey( 'slug', $items[0] );
		$this->assertArrayHasKey( 'label', $items[0] );
		$this->assertArrayHasKey( 'icon', $items[0] );
	}

	/**
	 * Applying preset via sanitizer writes menu studio settings.
	 */
	public function test_sanitize_apply_preset_sets_menu_studio() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'enabled'        => 1,
				'command_center' => array(
					'_apply_preset' => 'system_client',
				),
			)
		);

		$this->assertTrue( $result['command_center']['menu_studio']['enabled'] );
		$this->assertNotEmpty( $result['command_center']['menu_studio']['order'] );
	}

	/**
	 * Look skins include clean, focused, and full.
	 */
	public function test_get_look_skins() {
		$skins = EDMINBOOST_Command_Center::get_look_skins();

		$this->assertArrayHasKey( 'clean', $skins );
		$this->assertArrayHasKey( 'focused', $skins );
		$this->assertArrayHasKey( 'full', $skins );
	}

	/**
	 * apply_look_skin updates behavior only.
	 */
	public function test_apply_look_skin() {
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

		$applied = EDMINBOOST_Command_Center::apply_look_skin( 'focused' );
		$this->assertTrue( $applied );

		$settings = EDMINBOOST_Settings::get();
		$this->assertSame( 'compact', $settings['command_center']['behavior']['drawer_width'] );
		$this->assertSame( 'focused', $settings['command_center']['look_skin'] );
		$this->assertCount( 1, $settings['command_center']['top_bar_items'] );
	}

	/**
	 * is_setup_complete detects saved layouts.
	 */
	public function test_is_setup_complete() {
		$this->assertFalse(
			EDMINBOOST_Command_Center::is_setup_complete(
				array(
					'onboarding_completed' => false,
					'top_bar_items'        => array(),
				)
			)
		);

		$this->assertTrue(
			EDMINBOOST_Command_Center::is_setup_complete(
				array(
					'onboarding_completed' => false,
					'top_bar_items'        => array(
						array( 'slug' => 'edit.php' ),
					),
				)
			)
		);
	}

	/**
	 * Role visibility sanitization stores hidden slugs.
	 */
	public function test_role_visibility_sanitization() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_layout_studio_save' => 1,
					'top_bar_items'       => array(
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
					'role_visibility'     => array(
						'editor' => array( 'upload.php' ),
					),
				),
			)
		);

		$hidden = $result['command_center']['role_visibility']['editor'];
		$this->assertContains( 'edit.php', $hidden );
		$this->assertNotContains( 'upload.php', $hidden );
	}

	/**
	 * Role preset assignments resolve Menu Studio sidebar settings per user.
	 */
	public function test_resolve_menu_studio_for_user_role_assignment() {
		$cc_settings = array(
			'default_preset'   => 'system_client',
			'top_bar_items'    => array(),
			'menu_studio'      => EDMINBOOST_Command_Center::get_menu_studio_defaults(),
			'role_assignments' => array(
				'editor' => 'system_agency',
			),
		);

		$editor = new WP_User( $this->factory->user->create( array( 'role' => 'editor' ) ) );
		wp_set_current_user( $editor->ID );

		$menu = EDMINBOOST_Command_Center::resolve_menu_studio_for_user( $cc_settings );

		$this->assertTrue( $menu['enabled'] );
		$this->assertContains( 'themes.php', $menu['order'] );
		$this->assertContains( 'plugins.php', $menu['order'] );
	}

	/**
	 * Applying preset via sanitizer writes top bar items.
	 */
	public function test_sanitize_apply_preset() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'enabled'        => 1,
				'command_center' => array(
					'_apply_preset' => 'system_client',
				),
			)
		);

		$this->assertNotEmpty( $result['command_center']['top_bar_items'] );
		$this->assertTrue( $result['command_center']['onboarding_completed'] );
		$this->assertSame( 'system_client', $result['command_center']['default_preset'] );
		$this->assertTrue( $result['command_center']['menu_studio']['enabled'] );
		$this->assertNotEmpty( $result['command_center']['menu_studio']['order'] );
		$this->assertNotSame(
			'custom',
			EDMINBOOST_Command_Center::detect_active_layout_preset( $result['command_center'] )
		);
	}

	/**
	 * Applying preset replaces prior custom Menu Studio config instead of merging it.
	 */
	public function test_sanitize_apply_preset_replaces_custom_menu_studio() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'menu_studio' => array(
						'enabled'       => true,
						'order'         => array( 'plugins.php', 'themes.php', 'index.php' ),
						'submenu_order' => array(
							'plugins.php' => array( 'plugin-install.php' ),
						),
						'hidden_items'  => array( 'edit.php' ),
						'custom_items'  => array(
							array(
								'id'    => 'custom_link',
								'label' => 'Custom',
								'path'  => 'edit.php?post_type=page',
								'icon'  => 'dashicons-admin-page',
							),
						),
					),
				),
			)
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'enabled'        => 1,
				'command_center' => array(
					'_apply_preset' => 'system_client',
				),
			)
		);

		$expected_menu = EDMINBOOST_Command_Center::resolve_preset_menu_studio( 'system_client', $result['command_center'] );

		$this->assertSame(
			$expected_menu['order'],
			$result['command_center']['menu_studio']['order']
		);
		$this->assertSame( array(), $result['command_center']['menu_studio']['submenu_order'] );
		$this->assertSame( array(), $result['command_center']['menu_studio']['custom_items'] );
		$this->assertNotSame(
			'custom',
			EDMINBOOST_Command_Center::detect_active_layout_preset( $result['command_center'] )
		);
	}

	/**
	 * Wizard save applies preset and marks setup complete when layout resolves.
	 */
	public function test_sanitize_setup_wizard_save() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'enabled'        => 1,
				'command_center' => array(
					'_setup_wizard_save' => 1,
					'_apply_preset'      => 'system_client',
					'theme'              => array(
						'preset' => 'default',
						'mode'   => 'light',
					),
				),
			)
		);

		$this->assertNotEmpty( $result['command_center']['top_bar_items'] );
		$this->assertTrue( $result['command_center']['onboarding_completed'] );
	}

	/**
	 * Discovered menu items rebuild when admin menu globals are empty (AJAX tab loads).
	 */
	public function test_get_discovered_menu_items_rebuilds_empty_menu() {
		if ( function_exists( '_add_themes_utility_last' ) ) {
			$this->markTestSkipped( 'Admin menu bootstrap already loaded in this PHP process.' );
		}

		EDMINBOOST_Command_Center::reset_static_caches();

		global $menu;
		$menu = null;

		$items = EDMINBOOST_Command_Center::get_discovered_menu_items();

		$this->assertNotEmpty( $items );

		$slugs = wp_list_pluck( $items, 'slug' );
		$this->assertContains( 'index.php', $slugs );
	}

	/**
	 * Mark setup complete without a layout must not flip onboarding flag.
	 */
	public function test_sanitize_mark_setup_complete_requires_layout() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_mark_setup_complete' => 1,
				),
			)
		);

		$this->assertFalse( $result['command_center']['onboarding_completed'] );
	}

	/**
	 * CC tab AJAX renders mark the requested page as the active nav tab.
	 */
	public function test_capture_cc_page_html_marks_active_nav_tab() {
		$admin      = new EDMINBOOST_Admin( new EDMINBOOST_Features() );
		$reflection = new ReflectionClass( $admin );
		$method     = $reflection->getMethod( 'capture_cc_page_html' );
		$method->setAccessible( true );

		$page = EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_APPEARANCE;
		$html = $method->invoke( $admin, $page );

		$this->assertMatchesRegularExpression(
			'/class="edminboost-cc-nav__link is-active"[^>]*data-edminboost-page="' . preg_quote( $page, '/' ) . '"/',
			$html
		);
	}

	/**
	 * Role matrix excludes menu slugs a role cannot access.
	 */
	public function test_get_role_matrix_menu_items_respects_role_capabilities() {
		if ( ! did_action( 'admin_menu' ) ) {
			do_action( 'admin_menu' );
		}

		$admin_items = wp_list_pluck( EDMINBOOST_Command_Center::get_role_matrix_menu_items(), 'slug' );
		$sub_items   = wp_list_pluck( EDMINBOOST_Command_Center::get_role_matrix_menu_items( 'subscriber' ), 'slug' );

		$this->assertNotEmpty( $admin_items );
		$this->assertContains( 'index.php', $sub_items );
		$this->assertNotContains( 'plugins.php', $sub_items );
		$this->assertLessThan( count( $admin_items ), count( $sub_items ) );
	}

	/**
	 * Protected slugs are role-aware.
	 */
	public function test_get_protected_slugs_for_role() {
		$admin_protected = EDMINBOOST_Command_Center::get_protected_slugs_for_role( 'administrator' );
		$sub_protected   = EDMINBOOST_Command_Center::get_protected_slugs_for_role( 'subscriber' );

		$this->assertContains( EDMINBOOST_Admin::PAGE_SLUG, $admin_protected );
		$this->assertNotContains( EDMINBOOST_Admin::PAGE_SLUG, $sub_protected );
	}

	/**
	 * Top bar items are filtered by the current user's capabilities.
	 */
	public function test_filter_top_bar_items_for_user_capabilities() {
		if ( ! did_action( 'admin_menu' ) ) {
			do_action( 'admin_menu' );
		}

		$subscriber_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber_id );

		$items = EDMINBOOST_Command_Center::filter_top_bar_items_for_user_capabilities(
			array(
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
			)
		);

		$slugs = wp_list_pluck( $items, 'slug' );

		$this->assertContains( 'index.php', $slugs );
		$this->assertNotContains( 'plugins.php', $slugs );
	}
}
