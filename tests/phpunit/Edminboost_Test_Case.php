<?php
/**
 * Base test case for EdminBoost.
 *
 * @package EdminBoost
 */

/**
 * EdminBoost test case.
 */
abstract class Edminboost_Test_Case extends WP_UnitTestCase {

	/**
	 * Reset plugin options before each test.
	 */
	public function setUp(): void {
		parent::setUp();
		delete_option( EDMINBOOST_Settings::OPTION_NAME );
		delete_option( EDMINBOOST_Settings::VERSION_OPTION );
		EDMINBOOST_Command_Center::reset_static_caches();
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
		EDMINBOOST_Command_Center::ensure_discovery_menu_snapshot();
	}

	/**
	 * Tear down filters between tests.
	 */
	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * Seed default plugin settings.
	 *
	 * @param array $overrides Settings overrides.
	 * @return array Saved settings.
	 */
	protected function seed_settings( array $overrides = array() ) {
		$settings = wp_parse_args( $overrides, EDMINBOOST_Settings::get_defaults() );
		update_option( EDMINBOOST_Settings::OPTION_NAME, $settings );

		return $settings;
	}
}
