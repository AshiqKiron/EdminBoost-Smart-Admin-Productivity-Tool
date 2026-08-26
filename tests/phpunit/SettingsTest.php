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
	 * Dashboard widgets enabled when any widget is removed.
	 */
	public function test_is_feature_enabled_dashboard_widgets() {
		$this->seed_settings(
			array(
				'features' => array(
					'dashboard_widgets' => array(
						'remove_welcome_panel' => true,
						'remove_quick_press'   => false,
					),
				),
			)
		);

		$this->assertTrue( EDMINBOOST_Settings::is_feature_enabled( 'dashboard_widgets' ) );
	}

	/**
	 * Admin menu feature requires hidden items.
	 */
	public function test_is_feature_enabled_admin_menu() {
		$this->seed_settings(
			array(
				'features' => array(
					'admin_menu' => array(
						'hidden_items' => array( 'edit.php' ),
					),
				),
			)
		);

		$this->assertTrue( EDMINBOOST_Settings::is_feature_enabled( 'admin_menu' ) );
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
	 * Sanitize strips protected menu slugs.
	 */
	public function test_sanitize_strips_protected_menu_slugs() {
		$admin_id = get_current_user_id();
		wp_set_current_user( $admin_id );

		$result = EDMINBOOST_Settings::sanitize(
			array(
				'features' => array(
					'admin_menu' => array(
						'hidden_items' => array(
							'index.php',
							EDMINBOOST_Admin::PAGE_SLUG,
							'edit.php',
						),
					),
				),
			)
		);

		$this->assertSame( array( 'edit.php' ), $result['features']['admin_menu']['hidden_items'] );
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
