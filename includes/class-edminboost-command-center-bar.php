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
		add_action( 'admin_init', array( __CLASS__, 'maybe_prepare_drawer_frame' ) );
		add_action( 'admin_bar_menu', array( __CLASS__, 'register_nodes' ), 80 );
		add_action( 'admin_bar_menu', array( __CLASS__, 'apply_declutter' ), 999 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_footer', array( __CLASS__, 'render_drawer_shell' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render_drawer_shell' ) );
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
			$slug   = isset( $item['slug'] ) ? $item['slug'] : '';
			$label  = isset( $item['label'] ) ? $item['label'] : $slug;
			$icon   = isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic';
			$anchor = isset( $item['anchor'] ) ? $item['anchor'] : '';

			if ( '' === $slug ) {
				continue;
			}

			$badge_count  = self::get_badge_count( isset( $item['badge_source'] ) ? $item['badge_source'] : '' );
			$title        = self::build_node_title( $icon, $label, $badge_count, $badge_style );
			$interaction  = isset( $item['interaction'] ) ? $item['interaction'] : 'redirect';
			$is_drawer    = 'drawer' === $interaction;
			$node_classes = 'edminboost-cc-bar-item';

			if ( $is_drawer ) {
				$node_classes .= ' edminboost-cc-bar-drawer-trigger';
			}

			$admin_bar->add_node(
				array(
					'id'    => self::get_node_id( $slug, $anchor ),
					'title' => $title,
					'href'  => $is_drawer ? '#' : self::get_item_url( $slug, $anchor ),
					'meta'  => array(
						'class' => $node_classes,
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

		if ( ! self::has_drawer_items() ) {
			return;
		}

		wp_enqueue_script(
			'edminboost-command-center-bar',
			EDMINBOOST_PLUGIN_URL . 'admin/js/edminboost-command-center-bar.js',
			array(),
			EDMINBOOST_VERSION,
			true
		);

		wp_localize_script(
			'edminboost-command-center-bar',
			'edminboostCcBar',
			array(
				'drawerItems' => self::get_drawer_items_config(),
				'animationMs' => self::get_animation_duration_ms(),
			)
		);
	}

	/**
	 * Strip admin chrome when a page is loaded inside the drawer iframe.
	 *
	 * @return void
	 */
	public static function maybe_prepare_drawer_frame() {
		if ( empty( $_GET['edminboost_drawer'] ) || '1' !== $_GET['edminboost_drawer'] ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'You must be logged in to view this page.', EDMINBOOST_TEXT_DOMAIN ), 403 );
		}

		$slug   = isset( $_GET['edminboost_slug'] ) ? sanitize_text_field( wp_unslash( $_GET['edminboost_slug'] ) ) : '';
		$anchor = isset( $_GET['edminboost_anchor'] ) ? sanitize_text_field( wp_unslash( $_GET['edminboost_anchor'] ) ) : '';
		$anchor = ltrim( $anchor, '#' );

		if ( '' === $slug ) {
			wp_die( esc_html__( 'Invalid drawer request.', EDMINBOOST_TEXT_DOMAIN ), 400 );
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, self::get_drawer_nonce_action( $slug, $anchor ) ) ) {
			wp_die( esc_html__( 'Invalid drawer request.', EDMINBOOST_TEXT_DOMAIN ), 403 );
		}

		if ( ! self::is_drawer_slug_allowed( $slug, $anchor ) ) {
			wp_die( esc_html__( 'You cannot open this page in the drawer.', EDMINBOOST_TEXT_DOMAIN ), 403 );
		}

		add_action( 'admin_head', array( __CLASS__, 'print_drawer_frame_styles' ), 9999 );
	}

	/**
	 * Print CSS to hide admin chrome inside the drawer iframe.
	 *
	 * @return void
	 */
	public static function print_drawer_frame_styles() {
		echo '<style id="edminboost-cc-drawer-frame">';
		echo '#wpadminbar,#adminmenumain,#wpfooter,#screen-meta,#screen-meta-links{display:none!important;}';
		echo 'html.wp-toolbar{padding-top:0!important;}';
		echo '#wpcontent,#wpbody{margin-left:0!important;padding-left:0!important;}';
		echo '#wpbody-content{padding-bottom:0;}';
		echo '.folded #wpcontent{margin-left:0!important;}';
		echo '</style>';
	}

	/**
	 * Output the slide-out drawer shell markup.
	 *
	 * @return void
	 */
	public static function render_drawer_shell() {
		if ( ! self::is_active() || ! self::has_drawer_items() ) {
			return;
		}

		$behavior     = EDMINBOOST_Command_Center::get_settings()['behavior'];
		$width_class  = self::get_drawer_width_class( $behavior );
		$glass_class  = ! empty( $behavior['glassmorphism'] ) ? ' is-glass' : '';
		$duration_ms  = self::get_animation_duration_ms();
		$drawer_class = 'edminboost-cc-drawer' . $glass_class;

		printf(
			'<div id="edminboost-cc-drawer" class="%1$s" hidden style="--edminboost-cc-drawer-duration:%2$dms">',
			esc_attr( $drawer_class ),
			(int) $duration_ms
		);
		echo '<div class="edminboost-cc-drawer__backdrop" aria-hidden="true"></div>';
		printf(
			'<aside class="edminboost-cc-drawer__panel %1$s" role="dialog" aria-modal="true" aria-labelledby="edminboost-cc-drawer-title">',
			esc_attr( $width_class )
		);
		echo '<header class="edminboost-cc-drawer__header">';
		echo '<h2 id="edminboost-cc-drawer-title" class="edminboost-cc-drawer__title"></h2>';
		printf(
			'<a class="edminboost-cc-drawer__open-full" href="#" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_html__( 'Open full page', EDMINBOOST_TEXT_DOMAIN )
		);
		printf(
			'<button type="button" class="edminboost-cc-drawer__close" aria-label="%s">&times;</button>',
			esc_attr__( 'Close drawer', EDMINBOOST_TEXT_DOMAIN )
		);
		echo '</header>';
		echo '<div class="edminboost-cc-drawer__body">';
		printf(
			'<div class="edminboost-cc-drawer__loading" aria-live="polite">%s</div>',
			esc_html__( 'Loading…', EDMINBOOST_TEXT_DOMAIN )
		);
		echo '<iframe id="edminboost-cc-drawer-iframe" class="edminboost-cc-drawer__iframe" title="' . esc_attr__( 'Admin page preview', EDMINBOOST_TEXT_DOMAIN ) . '" src="about:blank"></iframe>';
		echo '</div>';
		echo '</aside>';
		echo '</div>';
	}

	/**
	 * Whether the current user has drawer interaction items on the top bar.
	 *
	 * @return bool
	 */
	public static function has_drawer_items() {
		foreach ( self::get_items_for_current_user() as $item ) {
			if ( isset( $item['interaction'] ) && 'drawer' === $item['interaction'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Drawer item config for localized script data.
	 *
	 * @return array<string, array<string, string>>
	 */
	private static function get_drawer_items_config() {
		$config = array();

		foreach ( self::get_items_for_current_user() as $item ) {
			$slug   = isset( $item['slug'] ) ? $item['slug'] : '';
			$anchor = isset( $item['anchor'] ) ? $item['anchor'] : '';

			if ( '' === $slug || ! isset( $item['interaction'] ) || 'drawer' !== $item['interaction'] ) {
				continue;
			}

			$node_id = self::get_node_id( $slug, $anchor );

			$config[ $node_id ] = array(
				'slug'     => $slug,
				'label'    => isset( $item['label'] ) ? $item['label'] : $slug,
				'frameUrl' => self::get_drawer_frame_url( $slug, $anchor ),
				'openUrl'  => self::get_item_url( $slug, $anchor ),
			);
		}

		return $config;
	}

	/**
	 * Whether a slug is allowed to load inside the drawer iframe.
	 *
	 * @param string $slug   Menu slug.
	 * @param string $anchor Optional URL fragment.
	 * @return bool
	 */
	private static function is_drawer_slug_allowed( $slug, $anchor = '' ) {
		$anchor = ltrim( (string) $anchor, '#' );

		foreach ( self::get_items_for_current_user() as $item ) {
			$item_anchor = isset( $item['anchor'] ) ? ltrim( (string) $item['anchor'], '#' ) : '';

			if (
				isset( $item['slug'], $item['interaction'] )
				&& $item['slug'] === $slug
				&& $item_anchor === $anchor
				&& 'drawer' === $item['interaction']
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Build a signed URL for loading an admin page inside the drawer iframe.
	 *
	 * @param string $slug   Menu slug.
	 * @param string $anchor Optional URL fragment.
	 * @return string
	 */
	private static function get_drawer_frame_url( $slug, $anchor = '' ) {
		$anchor = ltrim( (string) $anchor, '#' );

		$args = array(
			'edminboost_drawer' => '1',
			'edminboost_slug'   => $slug,
			'_wpnonce'          => wp_create_nonce( self::get_drawer_nonce_action( $slug, $anchor ) ),
		);

		if ( '' !== $anchor ) {
			$args['edminboost_anchor'] = $anchor;
		}

		$url = add_query_arg( $args, self::get_item_url( $slug, '' ) );

		if ( '' !== $anchor ) {
			$url .= '#' . rawurlencode( $anchor );
		}

		return $url;
	}

	/**
	 * Nonce action for a drawer slug.
	 *
	 * @param string $slug   Menu slug.
	 * @param string $anchor Optional URL fragment.
	 * @return string
	 */
	private static function get_drawer_nonce_action( $slug, $anchor = '' ) {
		return 'edminboost_cc_drawer_' . md5( $slug . "\0" . $anchor );
	}

	/**
	 * Drawer panel width class from behavior settings.
	 *
	 * @param array $behavior Behavior settings.
	 * @return string
	 */
	private static function get_drawer_width_class( $behavior ) {
		$width   = isset( $behavior['drawer_width'] ) ? $behavior['drawer_width'] : 'standard';
		$allowed = array( 'compact', 'standard', 'fullscreen' );

		if ( ! in_array( $width, $allowed, true ) ) {
			$width = 'standard';
		}

		return 'edminboost-cc-drawer__panel--' . $width;
	}

	/**
	 * Animation duration in milliseconds from behavior settings.
	 *
	 * @return int
	 */
	private static function get_animation_duration_ms() {
		$behavior = EDMINBOOST_Command_Center::get_settings()['behavior'];
		$speed    = isset( $behavior['animation_speed'] ) ? $behavior['animation_speed'] : 'normal';

		switch ( $speed ) {
			case 'fast':
				return 150;
			case 'slow':
				return 500;
			default:
				return 300;
		}
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
	private static function get_node_id( $slug, $anchor = '' ) {
		return 'edminboost-cc-' . md5( $slug . "\0" . $anchor );
	}

	/**
	 * Resolve an admin URL for a discovered menu slug.
	 *
	 * @param string $slug   Menu slug or full URL.
	 * @param string $anchor Optional URL fragment (without leading #).
	 * @return string
	 */
	private static function get_item_url( $slug, $anchor = '' ) {
		$anchor = ltrim( (string) $anchor, '#' );

		if ( 0 === strpos( $slug, 'http://' ) || 0 === strpos( $slug, 'https://' ) ) {
			$url = esc_url( $slug );

			if ( '' !== $anchor && false === strpos( $slug, '#' ) ) {
				$url .= '#' . rawurlencode( $anchor );
			}

			return $url;
		}

		$url = admin_url( $slug );

		if ( '' !== $anchor ) {
			$url .= '#' . rawurlencode( $anchor );
		}

		return $url;
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
