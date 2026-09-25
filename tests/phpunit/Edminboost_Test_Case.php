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
	 * Whether the current test enabled the Pro plan filter.
	 *
	 * @var bool
	 */
	private $pro_plan_enabled = false;

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
		if ( $this->pro_plan_enabled ) {
			remove_filter( 'edminboost_is_pro_active', '__return_true' );
			remove_filter( 'edminboost_active_billing_plan', array( $this, 'filter_active_billing_plan_pro' ) );
			$this->pro_plan_enabled = false;
		}

		parent::tearDown();
	}

	/**
	 * Enable Pro plan behavior for tests that cover Pro-only features.
	 *
	 * @return void
	 */
	protected function enable_pro_plan() {
		if ( $this->pro_plan_enabled ) {
			return;
		}

		add_filter( 'edminboost_is_pro_active', '__return_true' );
		add_filter( 'edminboost_active_billing_plan', array( $this, 'filter_active_billing_plan_pro' ) );
		$this->pro_plan_enabled = true;
	}

	/**
	 * Filter callback for active billing plan in tests.
	 *
	 * @return string
	 */
	public function filter_active_billing_plan_pro() {
		return 'pro';
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
