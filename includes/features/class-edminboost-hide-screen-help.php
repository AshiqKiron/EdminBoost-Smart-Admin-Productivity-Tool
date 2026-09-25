<?php
/**
 * Hide Screen Options and Help tabs.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hides Screen Options and contextual Help tabs in wp-admin.
 */
class EDMINBOOST_Hide_Screen_Help extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'hide_screen_help';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Hide Screen Options & Help';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Hide the Screen Options and Help tabs on admin pages.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'screen_options_show_screen', array( $this, 'filter_screen_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_hide_styles' ) );
	}

	/**
	 * Hide Screen Options on non-EdminBoost admin pages.
	 *
	 * @param bool $show Whether to show screen options.
	 * @return bool
	 */
	public function filter_screen_options( $show ) {
		if ( EDMINBOOST_Admin::is_plugin_admin_page() ) {
			return $show;
		}

		return false;
	}

	/**
	 * Add inline CSS to hide Screen Options and Help tabs on non-plugin screens.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_hide_styles( $hook_suffix ) {
		unset( $hook_suffix );

		if ( EDMINBOOST_Admin::is_plugin_admin_page() ) {
			return;
		}

		$handle = 'edminboost-hide-screen-help';

		wp_register_style( $handle, false, array(), EDMINBOOST_VERSION );
		wp_enqueue_style( $handle );
		wp_add_inline_style(
			$handle,
			'#screen-meta-links .show-settings,#contextual-help-link-wrap{display:none!important;}'
		);
	}
}
