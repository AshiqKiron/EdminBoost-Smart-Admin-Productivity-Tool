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
	 * Personas catalog includes expected keys.
	 */
	public function test_get_personas() {
		$personas = EDMINBOOST_Command_Center::get_personas();

		$this->assertArrayHasKey( 'client', $personas );
		$this->assertArrayHasKey( 'ecommerce', $personas );
		$this->assertArrayHasKey( 'developer', $personas );
	}

	/**
	 * System presets are included in all presets.
	 */
	public function test_get_all_presets_includes_system() {
		$presets = EDMINBOOST_Command_Center::get_all_presets();

		$this->assertArrayHasKey( 'system_client', $presets );
		$this->assertArrayHasKey( 'system_ecommerce', $presets );
		$this->assertArrayHasKey( 'system_developer', $presets );
	}

	/**
	 * Page links include Home, Top Bar, Presets, and Settings.
	 */
	public function test_get_page_links() {
		$items = EDMINBOOST_Command_Center::get_page_links();
		$slugs = wp_list_pluck( $items, 'slug' );
		$base  = EDMINBOOST_Admin::PAGE_SLUG;

		$this->assertContains( $base, $slugs );
		$this->assertContains( $base . EDMINBOOST_Command_Center::PAGE_MAPPER, $slugs );
		$this->assertContains( $base . EDMINBOOST_Command_Center::PAGE_PRESETS, $slugs );
		$this->assertContains( $base . '-settings', $slugs );
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
	}
}
