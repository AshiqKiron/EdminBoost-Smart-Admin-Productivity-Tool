<?php
/**
 * Disable RSS/Atom feeds.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disables public feeds and redirects feed URLs.
 */
class EDMINBOOST_Disable_Feeds extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'disable_feeds';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Disable Feeds';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Disable RSS, Atom, and RDF feeds and redirect feed URLs.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		$actions = array(
			'do_feed',
			'do_feed_rdf',
			'do_feed_rss',
			'do_feed_rss2',
			'do_feed_atom',
			'do_feed_rss2_comments',
			'do_feed_atom_comments',
		);

		foreach ( $actions as $action ) {
			add_action( $action, array( $this, 'redirect_feeds' ), 1 );
		}

		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}

	/**
	 * Redirect feed requests to the home page.
	 *
	 * @return void
	 */
	public function redirect_feeds() {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
