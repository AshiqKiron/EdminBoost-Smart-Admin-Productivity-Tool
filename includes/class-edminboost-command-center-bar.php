<?php
/**
 * Command Center top bar — renders saved layout items on the WordPress admin bar.
 *
 * Purpose: Inject Layout Studio items into #wpadminbar for logged-in users.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders Command Center top bar items on the live admin bar.
 */
class EDMINBOOST_Command_Center_Bar {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'register_nodes' ), 80 );
		add_action( 'admin_bar_menu', array( __CLASS__, 'apply_declutter' ), 999 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * Whether the top bar should render for the current request.
	 *
	 * @return bool
	 */
	public static function is_active() {
		if ( ! EDMINBOOST_Settings::is_enabled() || ! is_user_logged_in() || ! is_admin_bar_showing() ) {
			return false;
		}

		return ! empty( self::get_items_for_current_user() );
	}

	/**
	 * Add configured nodes to the admin bar.
	 *
	 * @param WP_Admin_Bar $admin_bar Admin bar instance.
	 * @return void
	 */
	public static function register_nodes( $admin_bar ) {
		if ( ! self::is_active() ) {
			return;
		}

		$cc_settings = EDMINBOOST_Command_Center::get_settings();
		$badge_style = isset( $cc_settings['behavior']['badge_style'] ) ? $cc_settings['behavior']['badge_style'] : 'pill';

		foreach ( self::get_items_for_current_user() as $index => $item ) {
			$slug  = isset( $item['slug'] ) ? $item['slug'] : '';
			$label = isset( $item['label'] ) ? $item['label'] : $slug;
			$icon  = isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic';

			if ( '' === $slug ) {
				continue;
			}

			$badge_count = self::get_badge_count( isset( $item['badge_source'] ) ? $item['badge_source'] : '' );
			$title       = self::build_node_title( $icon, $label, $badge_count, $badge_style );

			$admin_bar->add_node(
				array(
					'id'    => self::get_node_id( $slug ),
					'title' => $title,
					'href'  => self::get_item_url( $slug ),
					'meta'  => array(
						'class' => 'edminboost-cc-bar-item',
						'title' => $label,
					),
				)
			);
		}
	}

	/**
	 * Apply Command Center declutter toggles to the admin bar.
	 *
	 * @param WP_Admin_Bar $admin_bar Admin bar instance.
	 * @return void
	 */
	public static function apply_declutter( $admin_bar ) {
		if ( ! EDMINBOOST_Settings::is_enabled() || ! is_user_logged_in() ) {
			return;
		}

		$behavior = EDMINBOOST_Command_Center::get_settings()['behavior'];

		if ( ! empty( $behavior['hide_wp_logo'] ) ) {
			$admin_bar->remove_node( 'wp-logo' );
		}

		if ( ! empty( $behavior['hide_comments'] ) ) {
			$admin_bar->remove_node( 'comments' );
		}

		if ( ! empty( $behavior['hide_howdy'] ) ) {
			$admin_bar->remove_node( 'my-account' );
		}

		if ( ! empty( $behavior['hide_update_counters'] ) ) {
			$admin_bar->remove_node( 'updates' );
		}
	}

	/**
	 * Enqueue admin bar assets.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		if ( ! self::is_active() ) {
			return;
		}

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style(
			'edminboost-command-center-bar',
			EDMINBOOST_PLUGIN_URL . 'admin/css/edminboost-command-center-bar.css',
			array( 'dashicons' ),
			EDMINBOOST_VERSION
		);
	}

	/**
	 * Get top bar items visible to the current user.
	 *
	 * @return array[]
	 */
	public static function get_items_for_current_user() {
		$cc_settings = EDMINBOOST_Command_Center::get_settings();
		$items       = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
			? $cc_settings['top_bar_items']
			: array();

		if ( empty( $items ) ) {
			return array();
		}

		$user = wp_get_current_user();
		if ( empty( $user->roles ) ) {
			return $items;
		}

		$role_visibility = isset( $cc_settings['role_visibility'] ) && is_array( $cc_settings['role_visibility'] )
			? $cc_settings['role_visibility']
			: array();

		$visible = array();

		foreach ( $items as $item ) {
			$slug = isset( $item['slug'] ) ? $item['slug'] : '';
			if ( '' === $slug || ! self::is_item_visible_for_user( $slug, $user->roles, $role_visibility ) ) {
				continue;
			}

			$visible[] = $item;
		}

		return $visible;
	}

	/**
	 * Whether a top bar item is visible for the user's roles.
	 *
	 * @param string   $slug            Menu slug.
	 * @param string[] $user_roles      Current user roles.
	 * @param array    $role_visibility Hidden slugs keyed by role.
	 * @return bool
	 */
	private static function is_item_visible_for_user( $slug, $user_roles, $role_visibility ) {
		foreach ( $user_roles as $role ) {
			$hidden_for_role = isset( $role_visibility[ $role ] ) && is_array( $role_visibility[ $role ] )
				? $role_visibility[ $role ]
				: array();

			if ( ! in_array( $slug, $hidden_for_role, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Build a stable admin bar node ID for a menu slug.
	 *
	 * @param string $slug Menu slug.
	 * @return string
	 */
	private static function get_node_id( $slug ) {
		return 'edminboost-cc-' . md5( $slug );
	}

	/**
	 * Resolve an admin URL for a discovered menu slug.
	 *
	 * @param string $slug Menu slug.
	 * @return string
	 */
	private static function get_item_url( $slug ) {
		if ( 0 === strpos( $slug, 'http://' ) || 0 === strpos( $slug, 'https://' ) ) {
			return esc_url( $slug );
		}

		return admin_url( $slug );
	}

	/**
	 * Build admin bar node title markup.
	 *
	 * @param string $icon        Dashicon class.
	 * @param string $label       Item label.
	 * @param int    $badge_count Badge count.
	 * @param string $badge_style Badge style key.
	 * @return string
	 */
	private static function build_node_title( $icon, $label, $badge_count, $badge_style ) {
		$title  = '<span class="ab-icon dashicons ' . esc_attr( $icon ) . '" aria-hidden="true"></span>';
		$title .= '<span class="ab-label">' . esc_html( $label ) . '</span>';

		if ( $badge_count > 0 ) {
			$title .= '<span class="edminboost-cc-bar-badge edminboost-cc-bar-badge--' . esc_attr( $badge_style ) . '">';
			$title .= esc_html( (string) $badge_count );
			$title .= '</span>';
		}

		return $title;
	}

	/**
	 * Get a local badge count for a configured source.
	 *
	 * @param string $source Badge source key.
	 * @return int
	 */
	private static function get_badge_count( $source ) {
		switch ( $source ) {
			case 'comments':
				$counts = wp_count_comments();
				return isset( $counts->moderated ) ? (int) $counts->moderated : 0;

			case 'updates':
				if ( ! function_exists( 'wp_get_update_data' ) ) {
					return 0;
				}
				$updates = wp_get_update_data();
				return isset( $updates['counts']['total'] ) ? (int) $updates['counts']['total'] : 0;

			case 'wc_orders':
				if ( ! function_exists( 'wc_orders_count' ) && ! class_exists( 'WooCommerce' ) ) {
					return 0;
				}
				return self::get_wc_processing_orders_count();

			case 'wc_reviews':
				if ( ! function_exists( 'wc_get_product_visibility_options' ) && ! class_exists( 'WooCommerce' ) ) {
					return 0;
				}
				return self::get_wc_pending_reviews_count();

			case 'forms_entries':
				return self::get_wpforms_unread_entries_count();

			default:
				return 0;
		}
	}

	/**
	 * Count WooCommerce orders awaiting processing.
	 *
	 * @return int
	 */
	private static function get_wc_processing_orders_count() {
		if ( function_exists( 'wc_orders_count' ) ) {
			return (int) wc_orders_count( 'processing' ) + (int) wc_orders_count( 'on-hold' );
		}

		return 0;
	}

	/**
	 * Count pending WooCommerce product reviews.
	 *
	 * @return int
	 */
	private static function get_wc_pending_reviews_count() {
		$counts = wp_count_comments();
		return isset( $counts->awaiting_moderation ) ? (int) $counts->awaiting_moderation : 0;
	}

	/**
	 * Count unread WPForms entries when available.
	 *
	 * @return int
	 */
	private static function get_wpforms_unread_entries_count() {
		if ( ! function_exists( 'wpforms' ) ) {
			return 0;
		}

		global $wpdb;

		$table = $wpdb->prefix . 'wpforms_entries';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE viewed = 0" );

		return $count ? (int) $count : 0;
	}
}
