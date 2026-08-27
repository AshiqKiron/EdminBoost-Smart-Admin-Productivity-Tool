<?php
/**
 * Disable navigation menu duplication handler.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds duplicate action on the nav menus screen.
 */
class EDMINBOOST_Menu_Duplicator extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'menu_duplicator';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Menu Duplicator';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Duplicate an existing navigation menu with one click.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_footer-nav-menus.php', array( $this, 'render_duplicate_button' ) );
		add_action( 'admin_action_edminboost_duplicate_menu', array( $this, 'handle_duplicate' ) );
	}

	/**
	 * Render duplicate button on nav menus screen.
	 *
	 * @return void
	 */
	public function render_duplicate_button() {
		$menu_id = isset( $_GET['menu'] ) ? absint( $_GET['menu'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! $menu_id ) {
			return;
		}

		$url = wp_nonce_url(
			admin_url( 'admin.php?action=edminboost_duplicate_menu&menu_id=' . $menu_id ),
			'edminboost_duplicate_menu_' . $menu_id
		);
		?>
		<script id="edminboost-menu-duplicator">
		document.addEventListener('DOMContentLoaded', function () {
			var wrap = document.querySelector('.nav-tab-wrapper');
			if (!wrap) { return; }
			var link = document.createElement('a');
			link.className = 'button';
			link.style.marginLeft = '8px';
			link.href = <?php echo wp_json_encode( $url ); ?>;
			link.textContent = <?php echo wp_json_encode( __( 'Duplicate menu', EDMINBOOST_TEXT_DOMAIN ) ); ?>;
			wrap.appendChild(link);
		});
		</script>
		<?php
	}

	/**
	 * Duplicate a nav menu term and its items.
	 *
	 * @return void
	 */
	public function handle_duplicate() {
		$menu_id = isset( $_GET['menu_id'] ) ? absint( $_GET['menu_id'] ) : 0;

		if ( ! $menu_id || ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'You cannot duplicate this menu.', EDMINBOOST_TEXT_DOMAIN ) );
		}

		check_admin_referer( 'edminboost_duplicate_menu_' . $menu_id );

		$menu = wp_get_nav_menu_object( $menu_id );
		if ( ! $menu ) {
			wp_die( esc_html__( 'Menu not found.', EDMINBOOST_TEXT_DOMAIN ) );
		}

		$new_id = wp_create_nav_menu( $menu->name . ' ' . __( '(Copy)', EDMINBOOST_TEXT_DOMAIN ) );
		if ( is_wp_error( $new_id ) ) {
			wp_die( esc_html( $new_id->get_error_message() ) );
		}

		$items  = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );
		$id_map = array();

		if ( $items ) {
			foreach ( $items as $item ) {
				$parent = ( $item->menu_item_parent && isset( $id_map[ $item->menu_item_parent ] ) )
					? $id_map[ $item->menu_item_parent ]
					: 0;

				$new_item_id = wp_update_nav_menu_item(
					$new_id,
					0,
					array(
						'menu-item-title'     => $item->title,
						'menu-item-url'       => $item->url,
						'menu-item-status'    => $item->post_status,
						'menu-item-type'      => $item->type,
						'menu-item-object'    => $item->object,
						'menu-item-object-id' => $item->object_id,
						'menu-item-parent-id' => $parent,
					)
				);

				if ( ! is_wp_error( $new_item_id ) ) {
					$id_map[ $item->ID ] = $new_item_id;
				}
			}
		}

		wp_safe_redirect( admin_url( 'nav-menus.php?action=edit&menu=' . $new_id ) );
		exit;
	}
}
