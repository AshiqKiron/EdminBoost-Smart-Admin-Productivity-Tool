<?php
/**
 * Duplicate posts and pages from list tables.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a duplicate row action for supported post types.
 */
class EDMINBOOST_Post_Duplicator extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'post_duplicator';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Post Duplicator';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Add a duplicate action to post and page list tables.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'post_row_actions', array( $this, 'add_row_action' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'add_row_action' ), 10, 2 );
		add_action( 'admin_action_edminboost_duplicate_post', array( $this, 'handle_duplicate' ) );
	}

	/**
	 * Add duplicate link to row actions.
	 *
	 * @param array   $actions Row actions.
	 * @param WP_Post $post    Post object.
	 * @return array
	 */
	public function add_row_action( $actions, $post ) {
		$settings = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$types    = isset( $settings['post_types'] ) && is_array( $settings['post_types'] )
			? $settings['post_types']
			: array( 'post', 'page' );

		if ( ! in_array( $post->post_type, $types, true ) || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url( 'admin.php?action=edminboost_duplicate_post&post=' . absint( $post->ID ) ),
			'edminboost_duplicate_post_' . $post->ID
		);

		$actions['edminboost_duplicate'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			esc_url( $url ),
			esc_attr(
				sprintf(
					/* translators: %s: post title */
					__( 'Duplicate "%s"', EDMINBOOST_TEXT_DOMAIN ),
					get_the_title( $post )
				)
			),
			esc_html__( 'Duplicate', EDMINBOOST_TEXT_DOMAIN )
		);

		return $actions;
	}

	/**
	 * Handle duplicate action.
	 *
	 * @return void
	 */
	public function handle_duplicate() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'You cannot duplicate this item.', EDMINBOOST_TEXT_DOMAIN ) );
		}

		check_admin_referer( 'edminboost_duplicate_post_' . $post_id );

		$post = get_post( $post_id );
		if ( ! $post ) {
			wp_die( esc_html__( 'Post not found.', EDMINBOOST_TEXT_DOMAIN ) );
		}

		$new_id = wp_insert_post(
			array(
				'post_title'   => $post->post_title . ' ' . __( '(Copy)', EDMINBOOST_TEXT_DOMAIN ),
				'post_content' => $post->post_content,
				'post_excerpt' => $post->post_excerpt,
				'post_status'  => 'draft',
				'post_type'    => $post->post_type,
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $new_id ) ) {
			wp_die( esc_html( $new_id->get_error_message() ) );
		}

		wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
		exit;
	}
}
