<?php
/**
 * Disable XML-RPC.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disables the WordPress XML-RPC interface.
 */
class EDMINBOOST_Disable_Xmlrpc extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'disable_xmlrpc';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Disable XML-RPC';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Disable the XML-RPC interface to reduce attack surface.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'xmlrpc_enabled', '__return_false' );
	}
}
