<?php
/**
 * Custom admin list table columns.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds optional thumbnail, ID, and meta columns to post/page lists.
 */
class EDMINBOOST_Custom_Admin_Columns extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'custom_admin_columns';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Custom Admin Columns';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Show featured image, post ID, or a custom meta field in list tables.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		foreach ( array( 'post', 'page' ) as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_columns' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'render_column' ), 10, 2 );
		}
	}

	/**
	 * Add configured columns.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function add_columns( $columns ) {
		$screen   = get_current_screen();
		$post_type = $screen ? $screen->post_type : 'post';
		$settings = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$config   = isset( $settings[ $post_type ] ) ? $settings[ $post_type ] : array();

		$new = array();

		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;

			if ( 'title' === $key ) {
				if ( ! empty( $config['thumbnail'] ) ) {
					$new['edminboost_thumb'] = __( 'Image', EDMINBOOST_TEXT_DOMAIN );
				}
				if ( ! empty( $config['id'] ) ) {
					$new['edminboost_id'] = __( 'ID', EDMINBOOST_TEXT_DOMAIN );
				}
				if ( ! empty( $config['meta_key'] ) ) {
					$new['edminboost_meta'] = __( 'Meta', EDMINBOOST_TEXT_DOMAIN );
				}
			}
		}

		return $new;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function render_column( $column, $post_id ) {
		$post_type = get_post_type( $post_id );
		$settings  = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$config    = isset( $settings[ $post_type ] ) ? $settings[ $post_type ] : array();

		switch ( $column ) {
			case 'edminboost_thumb':
				echo get_the_post_thumbnail( $post_id, array( 40, 40 ) );
				break;

			case 'edminboost_id':
				echo esc_html( (string) $post_id );
				break;

			case 'edminboost_meta':
				if ( ! empty( $config['meta_key'] ) ) {
					echo esc_html( (string) get_post_meta( $post_id, $config['meta_key'], true ) );
				}
				break;
		}
	}
}
