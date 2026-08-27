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
		add_action( 'admin_head', array( $this, 'hide_help_tab' ) );
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
	 * Remove contextual help tabs via CSS fallback.
	 *
	 * @return void
	 */
	public function hide_help_tab() {
		if ( EDMINBOOST_Admin::is_plugin_admin_page() ) {
			return;
		}

		echo '<style id="edminboost-hide-screen-help">#screen-meta-links .show-settings,#contextual-help-link-wrap{display:none!important;}</style>';
	}
}
