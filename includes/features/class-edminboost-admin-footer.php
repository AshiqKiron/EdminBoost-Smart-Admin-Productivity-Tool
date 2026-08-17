<?php
/**
 * Custom admin footer text.
 *
 * Purpose: Replace default admin_footer_text when enabled and text is set.
 * Hook: admin_footer_text
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replaces the default admin footer text.
 */
class EDMINBOOST_Admin_Footer extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'admin_footer';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Admin Footer';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Replace the default WordPress admin footer text.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'admin_footer_text', array( $this, 'filter_footer_text' ) );
	}

	/**
	 * Replace admin footer text.
	 *
	 * @param string $text Default footer text.
	 * @return string
	 */
	public function filter_footer_text( $text ) {
		$settings = EDMINBOOST_Settings::get_feature_settings( 'admin_footer' );

		if ( empty( $settings['enabled'] ) || '' === trim( (string) $settings['text'] ) ) {
			return $text;
		}

		return esc_html( $settings['text'] );
	}
}
