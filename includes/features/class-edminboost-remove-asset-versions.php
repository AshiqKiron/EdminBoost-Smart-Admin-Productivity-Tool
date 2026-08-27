<?php
/**
 * Remove version query strings from assets.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Strips ver query args from enqueued scripts and styles.
 */
class EDMINBOOST_Remove_Asset_Versions extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'remove_asset_versions';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Remove Asset Versions';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Remove version query strings from script and style URLs.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'script_loader_src', array( $this, 'strip_version' ), 15, 1 );
		add_filter( 'style_loader_src', array( $this, 'strip_version' ), 15, 1 );
	}

	/**
	 * Remove ver query arg from a URL.
	 *
	 * @param string $src Asset URL.
	 * @return string
	 */
	public function strip_version( $src ) {
		if ( strpos( $src, 'ver=' ) !== false ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}
}
