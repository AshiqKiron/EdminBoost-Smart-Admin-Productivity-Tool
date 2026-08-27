<?php
/**
 * Disable comments for selected post types.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disables comments and pingbacks for configured post types.
 */
class EDMINBOOST_Disable_Comments extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'disable_comments';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Disable Comments';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Disable comments and hide comment UI for selected post types.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', array( $this, 'disable_support' ) );
		add_filter( 'comments_open', array( $this, 'close_comments' ), 20, 2 );
		add_filter( 'pings_open', array( $this, 'close_comments' ), 20, 2 );
		add_filter( 'comments_array', array( $this, 'hide_comments' ), 20, 2 );
		add_action( 'admin_menu', array( $this, 'remove_comments_menu' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_comments' ) );
	}

	/**
	 * Remove comment support from post types.
	 *
	 * @return void
	 */
	public function disable_support() {
		foreach ( $this->get_post_types() as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}

	/**
	 * Close comments on the front end.
	 *
	 * @param bool $open    Whether comments are open.
	 * @param int  $post_id Post ID.
	 * @return bool
	 */
	public function close_comments( $open, $post_id ) {
		$post = get_post( $post_id );
		if ( $post && in_array( $post->post_type, $this->get_post_types(), true ) ) {
			return false;
		}

		return $open;
	}

	/**
	 * Hide existing comments on the front end.
	 *
	 * @param array $comments Comments array.
	 * @param int   $post_id  Post ID.
	 * @return array
	 */
	public function hide_comments( $comments, $post_id ) {
		$post = get_post( $post_id );
		if ( $post && in_array( $post->post_type, $this->get_post_types(), true ) ) {
			return array();
		}

		return $comments;
	}

	/**
	 * Remove Comments admin menu when all types are disabled.
	 *
	 * @return void
	 */
	public function remove_comments_menu() {
		remove_menu_page( 'edit-comments.php' );
	}

	/**
	 * Remove comments from admin bar.
	 *
	 * @return void
	 */
	public function remove_admin_bar_comments() {
		global $wp_admin_bar;
		if ( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'comments' );
		}
	}

	/**
	 * Get configured post types.
	 *
	 * @return string[]
	 */
	private function get_post_types() {
		$settings = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$types    = isset( $settings['post_types'] ) && is_array( $settings['post_types'] )
			? $settings['post_types']
			: array();

		return ! empty( $types ) ? $types : array( 'post' );
	}
}
