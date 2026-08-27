<?php
/**
 * Remove Dashicons on the front end for visitors.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dequeues Dashicons for logged-out front-end visitors.
 */
class EDMINBOOST_Remove_Dashicons_Frontend extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'remove_dashicons_frontend';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Remove Front-end Dashicons';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Stop loading Dashicons for visitors who do not need the admin bar.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_dashicons' ), 100 );
	}

	/**
	 * Dequeue dashicons when the admin bar is not shown.
	 *
	 * @return void
	 */
	public function dequeue_dashicons() {
		if ( is_admin() || is_admin_bar_showing() ) {
			return;
		}

		wp_dequeue_style( 'dashicons' );
		wp_deregister_style( 'dashicons' );
	}
}
