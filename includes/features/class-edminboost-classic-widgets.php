<?php
/**
 * Disable the block-based widgets editor.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Restores the classic widgets screen.
 */
class EDMINBOOST_Classic_Widgets extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'classic_widgets';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Classic Widgets';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Use the classic widgets screen instead of the block editor.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'use_widgets_block_editor', '__return_false' );
	}
}
