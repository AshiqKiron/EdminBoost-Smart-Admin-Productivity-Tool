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
		$this->assertFalse( EDMINBOOST_Pro::has_licensed_admin_ui() );
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
