<?php
/**
 * Manual post ordering via menu_order.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enables menu_order column and default ordering for selected post types.
 */
class EDMINBOOST_Post_Order extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'post_order';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Post Order';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Enable manual ordering via the Order column in list tables.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		foreach ( $this->get_post_types() as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_order_column' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'render_order_column' ), 10, 2 );
			add_filter( "manage_edit-{$post_type}_sortable_columns", array( $this, 'sortable_order_column' ) );
		}

		add_action( 'pre_get_posts', array( $this, 'default_order_by_menu_order' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_order_field' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_quick_edit_order' ) );
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
			: array( 'post', 'page' );

		return $types;
	}

	/**
	 * Add order column.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function add_order_column( $columns ) {
		$columns['edminboost_order'] = __( 'Order', EDMINBOOST_TEXT_DOMAIN );
		return $columns;
	}

	/**
	 * Render order value.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function render_order_column( $column, $post_id ) {
		if ( 'edminboost_order' !== $column ) {
			return;
		}

		echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
	}

	/**
	 * Make order column sortable.
	 *
	 * @param array $columns Sortable columns.
	 * @return array
	 */
	public function sortable_order_column( $columns ) {
		$columns['edminboost_order'] = 'menu_order';
		return $columns;
	}

	/**
	 * Default admin list ordering by menu_order.
	 *
	 * @param WP_Query $query Query.
	 * @return void
	 */
	public function default_order_by_menu_order( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$post_type = $query->get( 'post_type' );
		if ( ! $post_type || ! in_array( $post_type, $this->get_post_types(), true ) ) {
			return;
		}

		if ( ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * Add menu_order field to quick edit.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type   Post type.
	 * @return void
	 */
	public function quick_edit_order_field( $column_name, $post_type ) {
		if ( 'edminboost_order' !== $column_name || ! in_array( $post_type, $this->get_post_types(), true ) ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php esc_html_e( 'Order', EDMINBOOST_TEXT_DOMAIN ); ?></span>
					<span class="input-text-wrap"><input type="number" name="menu_order" class="menu_order" value="0" /></span>
				</label>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Save quick edit menu_order.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_quick_edit_order( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['menu_order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_update_post(
				array(
					'ID'         => $post_id,
					'menu_order' => (int) $_POST['menu_order'], // phpcs:ignore WordPress.Security.NonceVerification.Missing
				)
			);
		}
	}
}
