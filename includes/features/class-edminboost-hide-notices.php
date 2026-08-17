<?php
/**
 * Hide distracting admin notices.
 *
 * Purpose: Inject inline CSS on non-plugin admin screens when enabled.
 * Hook: admin_enqueue_scripts
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Collapses admin notices outside Edmin Boost screens.
 */
class EDMINBOOST_Hide_Notices extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'hide_admin_notices';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Hide Admin Notices';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Hide routine admin notices on non-Edmin Boost screens while keeping errors and warnings visible.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_hide_styles' ) );
	}

	/**
	 * Add inline CSS to hide notices on non-plugin screens.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_hide_styles( $hook_suffix ) {
		if ( false !== strpos( $hook_suffix, EDMINBOOST_Admin::PAGE_SLUG ) ) {
			return;
		}

		$handle = 'edminboost-hide-notices';

		wp_register_style( $handle, false, array(), EDMINBOOST_VERSION );
		wp_enqueue_style( $handle );

		$css = '
			.notice:not(.notice-error):not(.notice-warning),
			.updated:not(.notice-error):not(.notice-warning) {
				display: none !important;
			}
		';

		wp_add_inline_style( $handle, $css );
	}
}
