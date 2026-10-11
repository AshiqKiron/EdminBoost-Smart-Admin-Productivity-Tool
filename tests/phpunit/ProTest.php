<?php
/**
 * Plan and Pro gating helpers.
 *
 * @package EdminBoost
 */

/**
 * Tests for EDMINBOOST_Plan and premium EDMINBOOST_Pro helpers.
 */
class ProTest extends Edminboost_Test_Case {

	/**
	 * WordPress.org build does not load premium bootstrap constants.
	 */
	public function test_is_direct_build_false_without_premium_constant() {
		if ( EDMINBOOST_Plan::is_direct_build() ) {
			$this->markTestSkipped( 'PHPUnit dev tree loads includes/pro/edminboost-premium.php; run with EDMINBOOST_TEST_WPORG=1 for wp.org simulation.' );
		}

		$this->assertFalse( EDMINBOOST_Plan::is_direct_build() );
	}

	/**
	 * Pro-only stored settings are stripped on read for the free build.
	 */
	public function test_strip_free_build_product_features_on_get() {
		if ( EDMINBOOST_Plan::is_direct_build() ) {
			$this->markTestSkipped( 'Direct-download build keeps Pro settings for Plan_Gating.' );
		}

		$this->seed_settings(
			array(
				'command_center' => array(
					'top_bar_items' => array(
						array(
							'slug'         => 'edit.php',
							'label'        => 'Posts',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'drawer',
							'badge_source' => 'comments',
						),
					),
					'menu_studio'   => array(
						'custom_items' => array(
							array(
								'id'    => 'custom_1',
								'label' => 'Custom',
								'path'  => 'edit.php',
								'icon'  => 'dashicons-admin-links',
							),
						),
						'display_mode' => 'icon',
					),
				),
				'features'        => array(
					'login_redirects' => array(
						'enabled' => true,
					),
				),
				'white_label'     => array(
					'enabled' => true,
				),
			)
		);

		$settings = EDMINBOOST_Settings::get();
		$item     = $settings['command_center']['top_bar_items'][0];

		$this->assertSame( 'redirect', $item['interaction'] );
		$this->assertSame( '', $item['badge_source'] );
		$this->assertSame( array(), $settings['command_center']['menu_studio']['custom_items'] );
		$this->assertSame( 'both', $settings['command_center']['menu_studio']['display_mode'] );
		$this->assertArrayNotHasKey( 'login_redirects', $settings['features'] );
		$this->assertArrayNotHasKey( 'white_label', $settings );
		$this->assertFalse( EDMINBOOST_Command_Center_Bar::has_drawer_items() );
	}

	/**
	 * Pro-only values are not persisted on save for the WordPress.org build.
	 */
	public function test_strip_free_build_product_features_on_sanitize() {
		if ( EDMINBOOST_Plan::is_direct_build() ) {
			$this->markTestSkipped( 'Direct-download build keeps Pro settings for Plan_Gating.' );
		}

		$saved = EDMINBOOST_Settings::sanitize(
			array(
				'enabled'        => 1,
				'command_center' => array(
					'_layout_studio_save' => 1,
					'top_bar_items'       => array(
						array(
							'slug'         => 'edit.php',
							'label'        => 'Posts',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'drawer',
							'badge_source' => 'comments',
						),
					),
					'behavior'            => array(
						'drawer_width'    => 'fullscreen',
						'animation_speed' => 'fast',
						'badge_style'     => 'dot',
						'glassmorphism'   => 1,
					),
					'theme'               => array(
						'preset'             => 'default',
						'schedule_dark_mode' => 1,
						'dark_mode_start'    => '20:00',
					),
				),
				'features'       => array(
					'login_redirects' => array(
						'enabled' => 1,
					),
				),
				'white_label'    => array(
					'enabled' => 1,
				),
			)
		);

		$item = $saved['command_center']['top_bar_items'][0];
		$this->assertSame( 'redirect', $item['interaction'] );
		$this->assertSame( '', $item['badge_source'] );
		$this->assertSame( 'standard', $saved['command_center']['behavior']['drawer_width'] );
		$this->assertArrayNotHasKey( 'schedule_dark_mode', $saved['command_center']['theme'] );
		$this->assertArrayNotHasKey( 'login_redirects', $saved['features'] );
		$this->assertArrayNotHasKey( 'white_label', $saved );
	}

	/**
	 * Free tier excludes Pro theme skins from pickers.
	 */
	public function test_include_preset_in_ui_excludes_pro_theme_when_not_active() {
		if ( ! class_exists( 'EDMINBOOST_Plan_Limits', false ) ) {
			$this->markTestSkipped( 'Plan limits ship only in the premium package.' );
		}

		$this->assertFalse(
			EDMINBOOST_Plan::include_preset_in_ui( 'neon-outrun', 'theme' )
		);
		$this->assertTrue(
			EDMINBOOST_Plan::include_preset_in_ui( 'default', 'theme' )
		);
	}

	/**
	 * Active Pro includes all theme skins in pickers.
	 */
	public function test_include_preset_in_ui_includes_pro_theme_when_active() {
		$this->enable_pro_plan();

		$this->assertTrue(
			EDMINBOOST_Plan::include_preset_in_ui( 'neon-outrun', 'theme' )
		);
	}

	/**
	 * Licensed admin package marker exists in the premium dev tree.
	 */
	public function test_has_licensed_admin_package_in_dev_tree() {
		if ( ! class_exists( 'EDMINBOOST_Pro', false ) ) {
			$this->markTestSkipped( 'Premium admin UI class is not loaded.' );
		}

		$this->assertTrue( EDMINBOOST_Pro::has_licensed_admin_package() );
	}

	/**
	 * Licensed admin UI is off when Pro is not active.
	 */
	public function test_has_licensed_admin_ui_false_when_pro_inactive() {
		$this->assertFalse( EDMINBOOST_Plan::has_licensed_admin_ui() );
	}

	/**
	 * Licensed admin UI requires the package and active Pro.
	 */
	public function test_has_licensed_admin_ui_true_when_pro_active() {
		if ( ! class_exists( 'EDMINBOOST_Pro', false ) ) {
			$this->markTestSkipped( 'Premium admin UI class is not loaded.' );
		}

		$this->enable_pro_plan();

		$this->assertTrue( EDMINBOOST_Pro::has_licensed_admin_ui() );
	}

	/**
	 * Plan helpers ship on all builds; Pro runtime gating ships only in the premium package.
	 */
	public function test_plan_helpers_and_pro_gating_package() {
		$this->assertTrue( class_exists( 'EDMINBOOST_Plan', false ) );

		if ( EDMINBOOST_Plan::is_direct_build() ) {
			$this->assertTrue( class_exists( 'EDMINBOOST_Plan_Gating', false ) );
			$this->assertTrue( class_exists( 'EDMINBOOST_Plan_Limits', false ) );
		} else {
			$this->assertFalse( class_exists( 'EDMINBOOST_Plan_Gating', false ) );
			$this->assertFalse( class_exists( 'EDMINBOOST_Plan_Limits', false ) );
		}
	}

	/**
	 * enforce_plan_limits downgrades drawer interaction on the free plan.
	 */
	public function test_enforce_plan_limits_strips_drawer_on_free_plan() {
		if ( ! class_exists( 'EDMINBOOST_Plan_Limits', false ) ) {
			$this->markTestSkipped( 'Plan limits ship only in the premium package.' );
		}

		$this->seed_settings(
			array(
				'command_center' => array(
					'top_bar_items' => array(
						array(
							'slug'         => 'edit.php',
							'label'        => 'Posts',
							'icon'         => 'dashicons-admin-post',
							'interaction'  => 'drawer',
							'badge_source' => 'comments',
						),
					),
				),
			)
		);

		$settings = EDMINBOOST_Settings::get();
		$item     = $settings['command_center']['top_bar_items'][0];

		$this->assertSame( 'redirect', $item['interaction'] );
		$this->assertSame( '', $item['badge_source'] );
		$this->assertFalse( EDMINBOOST_Command_Center_Bar::has_drawer_items() );
	}

	/**
	 * Pro-only features stay off when the billing plan is free.
	 */
	public function test_pro_only_features_inactive_when_plan_is_free() {
		$this->seed_settings(
			array(
				'features'    => array(
					'login_redirects' => array(
						'enabled'        => true,
						'default_login'  => 'https://example.com/login',
						'default_logout' => '',
						'login_roles'    => array(),
						'logout_roles'   => array(),
					),
				),
				'white_label' => array(
					'enabled' => true,
				),
			)
		);

		$this->assertFalse( EDMINBOOST_Settings::is_feature_enabled( 'login_redirects' ) );

		if ( ! class_exists( 'EDMINBOOST_Plan_Gating', false ) ) {
			$this->markTestSkipped( 'Pro runtime gating is not loaded in this build.' );
		}

		EDMINBOOST_Plan_Gating::register_pro_runtime_modules();
		$this->assertFalse(
			has_action( 'admin_footer_text', array( 'EDMINBOOST_White_Label', 'filter_admin_footer_text' ) )
		);
	}
}
