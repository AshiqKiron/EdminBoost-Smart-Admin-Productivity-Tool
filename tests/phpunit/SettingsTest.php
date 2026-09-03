<?php
/**
 * Settings tests.
 *
 * @package EdminBoost
 */

/**
 * EDMINBOOST_Settings tests.
 */
class SettingsTest extends Edminboost_Test_Case {

	/**
	 * Global kill switch disables features.
	 */
	public function test_is_enabled_blocks_features_when_disabled() {
		$this->seed_settings(
			array(
				'enabled'  => false,
				'features' => array(
					'hide_admin_notices' => true,
				),
			)
		);

		$this->assertFalse( EDMINBOOST_Settings::is_enabled() );
		$this->assertFalse( EDMINBOOST_Settings::is_feature_enabled( 'hide_admin_notices' ) );
	}

	/**
	 * Boolean features respect their flags.
	 */
	public function test_is_feature_enabled_boolean_features() {
		$this->seed_settings(
			array(
				'features' => array(
					'hide_admin_notices' => true,
					'disable_emojis'     => false,
				),
			)
		);

		$this->assertTrue( EDMINBOOST_Settings::is_feature_enabled( 'hide_admin_notices' ) );
		$this->assertFalse( EDMINBOOST_Settings::is_feature_enabled( 'disable_emojis' ) );
	}

	/**
	 * Productivity notice and screen tab toggles default to off.
	 */
	public function test_productivity_feature_defaults_are_off() {
		$defaults = EDMINBOOST_Feature_Settings::get_defaults();

		$this->assertFalse( $defaults['hide_admin_notices'] );
		$this->assertFalse( $defaults['hide_screen_help'] );

		$form_defaults = EDMINBOOST_Settings::get_form_defaults();

		$this->assertFalse( $form_defaults['features']['hide_admin_notices'] );
		$this->assertFalse( $form_defaults['features']['hide_screen_help'] );
	}

	/**
	 * Dashboard widgets enabled when master toggle is on.
	 */
	public function test_is_feature_enabled_dashboard_widgets() {
		$this->seed_settings(
			array(
				'features' => array(
					'dashboard_widgets' => array(
						'enabled'              => true,
						'remove_welcome_panel' => true,
						'remove_quick_press'   => false,
					),
				),
			)
		);

		$this->assertTrue( EDMINBOOST_Settings::is_feature_enabled( 'dashboard_widgets' ) );
	}

	/**
	 * Dashboard widgets stay off when master toggle is off.
	 */
	public function test_is_feature_enabled_dashboard_widgets_disabled() {
		$this->seed_settings(
			array(
				'features' => array(
					'dashboard_widgets' => array(
						'enabled'              => false,
						'remove_welcome_panel' => true,
					),
				),
			)
		);

		$this->assertFalse( EDMINBOOST_Settings::is_feature_enabled( 'dashboard_widgets' ) );
	}

	/**
	 * Legacy dashboard widget selections infer the master toggle on read.
	 */
	public function test_normalize_dashboard_widgets_legacy_enabled() {
		$this->seed_settings(
			array(
				'features' => array(
					'dashboard_widgets' => array(
						'remove_welcome_panel' => true,
					),
				),
			)
		);

		$this->assertTrue( EDMINBOOST_Settings::is_feature_enabled( 'dashboard_widgets' ) );
	}

	/**
	 * Admin footer requires enabled flag and text.
	 */
	public function test_is_feature_enabled_admin_footer() {
		$this->seed_settings(
			array(
				'features' => array(
					'admin_footer' => array(
						'enabled' => true,
						'text'    => 'Custom footer',
					),
				),
			)
		);

		$this->assertTrue( EDMINBOOST_Settings::is_feature_enabled( 'admin_footer' ) );

		$this->seed_settings(
			array(
				'features' => array(
					'admin_footer' => array(
						'enabled' => true,
						'text'    => '',
					),
				),
			)
		);

		$this->assertFalse( EDMINBOOST_Settings::is_feature_enabled( 'admin_footer' ) );
	}

	/**
	 * Menu Studio sanitize strips protected menu slugs from hidden items.
	 */
	public function test_sanitize_menu_studio_strips_protected_slugs() {
		$admin_id = get_current_user_id();
		wp_set_current_user( $admin_id );

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_menu_studio_save' => 1,
					'menu_studio'       => array(
						'enabled'      => 1,
						'hidden_items' => array(
							'index.php',
							EDMINBOOST_Admin::PAGE_SLUG,
							'edit.php',
						),
					),
				),
			)
		);

		$this->assertSame(
			array( 'edit.php' ),
			$result['command_center']['menu_studio']['hidden_items']
		);
	}

	/**
	 * Menu Studio save with use_colors disabled clears custom sidebar colors.
	 */
	public function test_sanitize_menu_studio_disabling_colors_clears_tokens() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'menu_studio' => array(
						'enabled'    => true,
						'use_colors' => true,
						'colors'     => array(
							'parent_bg'   => '#111111',
							'parent_text' => '#eeeeee',
						),
					),
				),
			)
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_menu_studio_save' => 1,
					'menu_studio'       => array(
						'enabled'    => 1,
						'use_colors' => 0,
					),
				),
			)
		);

		$menu_studio = $result['command_center']['menu_studio'];

		$this->assertFalse( $menu_studio['use_colors'] );
		$this->assertSame(
			EDMINBOOST_Command_Center::get_menu_studio_defaults()['colors'],
			$menu_studio['colors']
		);
	}

	/**
	 * Layout Studio save with empty items clears top bar.
	 */
	public function test_sanitize_layout_studio_empty_clears_top_bar() {
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

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_layout_studio_save' => 1,
					'top_bar_items'       => array(),
				),
			)
		);

		$this->assertSame( array(), $result['command_center']['top_bar_items'] );
	}

	/**
	 * Invalid persona is rejected on sanitize.
	 */
	public function test_sanitize_rejects_invalid_persona() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'persona' => 'invalid_persona',
				),
			)
		);

		$this->assertSame( '', $result['command_center']['persona'] );
	}

	/**
	 * Valid persona is preserved.
	 */
	public function test_sanitize_accepts_valid_persona() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'persona' => 'developer',
				),
			)
		);

		$this->assertSame( 'developer', $result['command_center']['persona'] );
	}

	/**
	 * Top bar items dedupe slug plus anchor pairs.
	 */
	public function test_sanitize_dedupes_slug_anchor_pairs() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_layout_studio_save' => 1,
					'top_bar_items'       => array(
						array(
							'slug'         => 'edit.php',
							'anchor'       => 'section-a',
							'label'        => 'Posts A',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
						array(
							'slug'         => 'edit.php',
							'anchor'       => 'section-a',
							'label'        => 'Duplicate',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
						array(
							'slug'         => 'edit.php',
							'anchor'       => 'section-b',
							'label'        => 'Posts B',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
					),
				),
			)
		);

		$this->assertCount( 2, $result['command_center']['top_bar_items'] );
	}

	/**
	 * Slug hash fragment splits into anchor when anchor empty.
	 */
	public function test_sanitize_splits_hash_in_slug() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_layout_studio_save' => 1,
					'top_bar_items'       => array(
						array(
							'slug'         => 'options-general.php#site-title',
							'label'        => 'Settings',
							'icon'         => 'dashicons-admin-settings',
							'interaction'  => 'redirect',
							'badge_source' => '',
						),
					),
				),
			)
		);

		$item = $result['command_center']['top_bar_items'][0];
		$this->assertSame( 'options-general.php', $item['slug'] );
		$this->assertSame( 'site-title', $item['anchor'] );
	}

	/**
	 * Custom drawer width is clamped to the allowed pixel range.
	 */
	public function test_sanitize_clamps_custom_drawer_width() {
		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'behavior' => array(
						'drawer_width'        => 'custom',
						'drawer_width_custom' => 950,
					),
				),
			)
		);

		$this->assertSame( 'custom', $result['command_center']['behavior']['drawer_width'] );
		$this->assertSame(
			EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MAX,
			$result['command_center']['behavior']['drawer_width_custom']
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'behavior' => array(
						'drawer_width'        => 'custom',
						'drawer_width_custom' => 250,
					),
				),
			)
		);

		$this->assertSame(
			EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MIN,
			$result['command_center']['behavior']['drawer_width_custom']
		);
	}

	/**
	 * Menu Studio save persists top-level order and indexed submenu parents.
	 */
	public function test_sanitize_menu_studio_order_and_submenu_parents() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'menu_studio' => array(
						'enabled' => true,
						'order'   => array( 'index.php', 'edit.php' ),
					),
				),
			)
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_menu_studio_save' => 1,
					'menu_studio'       => array(
						'enabled'         => 1,
						'order'           => array(
							'upload.php',
							'index.php',
							'edit.php',
						),
						'submenu_parents' => array(
							'edit.php?post_type=page',
						),
						'submenu_order'   => array(
							array(
								'edit.php?post_type=page',
								'post-new.php?post_type=page',
							),
						),
					),
				),
			)
		);

		$menu_studio = $result['command_center']['menu_studio'];

		$this->assertTrue( $menu_studio['enabled'] );
		$this->assertSame(
			array( 'upload.php', 'index.php', 'edit.php' ),
			$menu_studio['order']
		);
		$this->assertSame(
			array(
				'edit.php?post_type=page' => array(
					'edit.php?post_type=page',
					'post-new.php?post_type=page',
				),
			),
			$menu_studio['submenu_order']
		);
	}

	/**
	 * Dashboard overview theme saves merge with existing theme settings.
	 */
	public function test_sanitize_dashboard_theme_save_preserves_existing_theme_fields() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'theme' => array(
						'preset'           => 'default',
						'mode'             => 'dark',
						'font'             => 'mono',
						'font_size'        => 18,
						'admin_favicon_id' => 42,
					),
				),
			)
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'theme' => array(
						'preset' => 'midnight',
						'mode'   => 'dark',
						'font'   => 'mono',
					),
				),
			)
		);

		$theme = $result['command_center']['theme'];

		$this->assertSame( 'midnight', $theme['preset'] );
		$this->assertSame( 'dark', $theme['mode'] );
		$this->assertSame( 'mono', $theme['font'] );
		$this->assertSame( 18, $theme['font_size'] );
		$this->assertSame( 42, $theme['admin_favicon_id'] );
	}

	/**
	 * Dashboard theme preset change clears stale custom-color state from merged saves.
	 */
	public function test_sanitize_dashboard_theme_preset_clears_stale_custom_flag() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'theme' => array(
						'preset'            => 'custom',
						'use_custom_colors' => true,
						'mode'              => 'light',
						'font'              => 'inherit',
						'custom_accent'     => '#ff0000',
					),
				),
			)
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'theme' => array(
						'preset' => 'midnight',
						'mode'   => 'light',
						'font'   => 'inherit',
					),
				),
			)
		);

		$theme = $result['command_center']['theme'];

		$this->assertSame( 'midnight', $theme['preset'] );
		$this->assertFalse( $theme['use_custom_colors'] );
		$this->assertSame( '#ff0000', $theme['custom_accent'] );
	}

	/**
	 * Dashboard overview AJAX save omits features but still saves theme and layout apply.
	 */
	public function test_sanitize_dashboard_overview_payload_without_features() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'theme' => array(
						'preset' => 'default',
						'mode'   => 'light',
						'font'   => 'inherit',
					),
				),
			)
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'enabled'        => 1,
				'command_center' => array(
					'_apply_preset' => 'system_client',
					'theme'         => array(
						'preset' => 'midnight',
						'mode'   => 'light',
						'font'   => 'inherit',
					),
				),
			)
		);

		$this->assertSame( 'midnight', $result['command_center']['theme']['preset'] );
		$this->assertNotEmpty( $result['command_center']['top_bar_items'] );
		$this->assertSame( 'system_client', $result['command_center']['default_preset'] );
	}

	/**
	 * Saving a custom layout preset stores the user-provided name and sidebar config.
	 */
	public function test_sanitize_save_custom_preset_with_name() {
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
							'anchor'       => '',
						),
					),
					'menu_studio'   => array(
						'enabled'      => true,
						'order'        => array( 'index.php' ),
						'hidden_items' => array( 'edit.php' ),
					),
				),
			)
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_save_custom_preset' => array(
						'name' => 'Agency Client Layout',
					),
				),
			)
		);

		$presets = $result['command_center']['presets'];

		$this->assertCount( 1, $presets );
		$preset = reset( $presets );
		$this->assertSame( 'Agency Client Layout', $preset['name'] );
		$this->assertSame( 'index.php', $preset['top_bar_items'][0]['slug'] );
		$this->assertTrue( $preset['menu_studio']['enabled'] );
		$this->assertContains( 'edit.php', $preset['menu_studio']['hidden_items'] );
	}

	/**
	 * Renaming a saved custom preset updates the stored label.
	 */
	public function test_sanitize_rename_custom_preset() {
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
							'anchor'       => '',
						),
					),
					'presets'       => array(
						'custom_test1234' => array(
							'name'          => 'Old Name',
							'system'        => false,
							'top_bar_items' => array(
								array(
									'slug'         => 'index.php',
									'label'        => 'Dashboard',
									'icon'         => 'dashicons-dashboard',
									'interaction'  => 'redirect',
									'badge_source' => '',
									'anchor'       => '',
								),
							),
						),
					),
				),
			)
		);

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'command_center' => array(
					'_rename_custom_preset' => array(
						'id'   => 'custom_test1234',
						'name' => 'Updated Layout Name',
					),
				),
			)
		);

		$this->assertSame(
			'Updated Layout Name',
			$result['command_center']['presets']['custom_test1234']['name']
		);
	}

	/**
	 * Users without capability cannot change settings via sanitize.
	 */
	public function test_sanitize_requires_capability() {
		$editor_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $editor_id );

		$this->seed_settings( array( 'enabled' => true ) );

		$result = EDMINBOOST_Settings::sanitize( array( 'enabled' => false ) );

		$this->assertTrue( $result['enabled'] );
	}
}
