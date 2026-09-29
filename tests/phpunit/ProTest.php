<?php
/**
 * Pro plan helpers.
 *
 * @package EdminBoost
 */

/**
 * Tests for EDMINBOOST_Pro gating helpers.
 */
class ProTest extends Edminboost_Test_Case {

	/**
	 * Free tier excludes Pro theme skins from pickers.
	 */
	public function test_include_preset_in_ui_excludes_pro_theme_when_not_active() {
		$this->assertFalse(
			EDMINBOOST_Pro::include_preset_in_ui( 'neon-outrun', 'theme' )
		);
		$this->assertTrue(
			EDMINBOOST_Pro::include_preset_in_ui( 'default', 'theme' )
		);
	}

	/**
	 * Active Pro includes all theme skins in pickers.
	 */
	public function test_include_preset_in_ui_includes_pro_theme_when_active() {
		$this->enable_pro_plan();

		$this->assertTrue(
			EDMINBOOST_Pro::include_preset_in_ui( 'neon-outrun', 'theme' )
		);
	}

	/**
	 * Pro settings UI is off when Pro is not active.
	 */
	public function test_shows_pro_settings_ui_false_when_pro_inactive() {
		$this->assertFalse( EDMINBOOST_Pro::shows_pro_settings_ui() );
	}

	/**
	 * Pro settings UI requires premium build and active Pro.
	 */
	public function test_shows_pro_settings_ui_true_when_premium_and_pro_active() {
		if ( ! EDMINBOOST_Pro::is_premium_build() ) {
			$this->markTestSkipped( 'Premium bootstrap not loaded in this test environment.' );
		}

		$this->enable_pro_plan();

		$this->assertTrue( EDMINBOOST_Pro::shows_pro_settings_ui() );
	}
}
