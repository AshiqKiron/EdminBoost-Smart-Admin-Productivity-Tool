<?php
/**
 * Command Center top bar — renders saved layout items on the WordPress admin bar.
 *
 * Purpose: Inject Layout Studio items into #wpadminbar for logged-in users.
 * Pro drawer/badges hook via edminboost_cc_bar_* filters (premium package only).
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
	 * Normalize a saved top-bar slug or admin URL to a relative wp-admin path.
	 *
	 * @param string $slug Menu slug or full admin URL.
	 * @return string
	 */
	public static function normalize_item_slug( $slug ) {
		return self::resolve_admin_slug( $slug );
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
	 * Whether the current user has drawer interaction items on the top bar.
	 *
	 * @return bool
	 */
	public static function has_drawer_items() {
		/**
		 * Filter whether the live top bar has drawer interaction items.
		 *
		 * @param bool $has_drawer_items Default false on the WordPress.org build.
		 */
		return (bool) apply_filters( 'edminboost_cc_bar_has_drawer_items', false );
	}

	/**
	 * Whether the current request is the Layout Studio mapper screen.
	 *
	 * @return bool
	 */
	public static function is_mapper_screen() {
		if ( ! is_admin() ) {
			return false;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER === $page;
	}

	/**
	 * Whether the request is a Layout Studio screen or preview AJAX from it.
	 *
	 * @return bool
	 */
	public static function is_mapper_preview_context() {
		if ( self::is_mapper_screen() ) {
			return true;
		}

		/**
		 * Filter Layout Studio preview AJAX context (premium drawer preview).
		 *
		 * @param bool $is_mapper_preview_context Default false on the WordPress.org build.
		 */
		return (bool) apply_filters( 'edminboost_cc_bar_mapper_preview_context', false );
	}

	/**
	 * Mark admin pages loaded inside the drawer iframe.
	 *
	 * @param string $classes Space-separated admin body classes.
	 * @return string
	 */
	public static function filter_drawer_frame_body_class( $classes ) {
		/**
		 * Filter admin body classes for drawer iframe loads.
		 *
		 * @param string $classes Space-separated admin body classes.
		 */
		return apply_filters( 'edminboost_cc_bar_drawer_frame_body_class', $classes );
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

		/**
		 * Filter whether premium top bar nodes replace the redirect-only renderer.
		 *
		 * @param bool $use_pro_nodes Default false on the WordPress.org build.
		 */
		if ( apply_filters( 'edminboost_cc_bar_use_pro_register_nodes', false ) ) {
			/**
			 * Register premium top bar nodes (drawer triggers, badges).
			 *
			 * @param WP_Admin_Bar $admin_bar Admin bar instance.
			 */
			do_action( 'edminboost_cc_bar_register_pro_nodes', $admin_bar );
			return;
		}

		foreach ( self::get_items_for_current_user() as $item ) {
			$slug   = isset( $item['slug'] ) ? $item['slug'] : '';
			$label  = isset( $item['label'] ) ? $item['label'] : $slug;
			$icon   = isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic';
			$anchor = isset( $item['anchor'] ) ? $item['anchor'] : '';

			if ( '' === $slug ) {
				continue;
			}

			$icon  = EDMINBOOST_Command_Center::normalize_dashicon_class( $icon );
			$title = '<span class="edminboost-cc-bar-icon dashicons ' . esc_attr( $icon ) . '" aria-hidden="true"></span>';
			$title .= '<span class="ab-label">' . esc_html( $label ) . '</span>';

			$admin_bar->add_node(
				array(
					'id'    => self::get_node_id( $slug, $anchor ),
					'title' => $title,
					'href'  => self::get_item_url( $slug, $anchor ),
					'meta'  => array(
						'class' => 'edminboost-cc-bar-item',
						'title' => esc_attr( $label ),
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

		if ( ! empty( $behavior['hide_new_content'] ) ) {
			$admin_bar->remove_node( 'new-content' );
		}

		if ( ! empty( $behavior['hide_customize'] ) ) {
			$admin_bar->remove_node( 'customize' );
		}
	}

	/**
	 * Enqueue admin bar styles (drawer JS is premium-only).
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		if ( ! EDMINBOOST_Settings::is_enabled() || ! is_user_logged_in() ) {
			return;
		}

		$mapper_screen = self::is_mapper_screen();
		$plugin_screen = EDMINBOOST_Admin::is_plugin_admin_page();

		if ( ! self::is_active() && ! $mapper_screen && ! $plugin_screen ) {
			return;
		}

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style(
			'edminboost-command-center-bar',
			EDMINBOOST_PLUGIN_URL . 'admin/css/edminboost-command-center-bar.css',
			array( 'dashicons', 'edminboost-themes' ),
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

		$items = EDMINBOOST_Command_Center::resolve_top_bar_items_for_user( $cc_settings );

		if ( empty( $items ) ) {
			return array();
		}

		$user = wp_get_current_user();

		/**
		 * Filter top bar items resolved for the current user (after role preset assignment).
		 *
		 * @param array[] $items       Top bar items.
		 * @param array   $cc_settings Command Center settings.
		 * @param WP_User $user        Current user.
		 */
		$items = apply_filters( 'edminboost_top_bar_items_for_user', $items, $cc_settings, $user );

		EDMINBOOST_Command_Center::ensure_discovery_menu_snapshot();

		return EDMINBOOST_Command_Center::filter_top_bar_items_for_user_capabilities( $items );
	}

	/**
	 * Build a stable admin bar node ID for a menu slug.
	 *
	 * @param string $slug   Menu slug.
	 * @param string $anchor Optional URL fragment.
	 * @return string
	 */
	public static function get_node_id( $slug, $anchor = '' ) {
		return 'edminboost-cc-' . md5( $slug . "\0" . $anchor );
	}

	/**
	 * Resolve an admin URL for a discovered menu slug.
	 *
	 * @param string $slug   Menu slug or full URL.
	 * @param string $anchor Optional URL fragment (without leading #).
	 * @return string
	 */
	public static function get_item_url( $slug, $anchor = '' ) {
		$anchor = ltrim( (string) $anchor, '#' );
		$url    = self::build_admin_url_from_slug( $slug );

		if ( '' !== $anchor && false === strpos( $url, '#' ) ) {
			$url .= '#' . rawurlencode( $anchor );
		}

		return $url;
	}

	/**
	 * Normalize a menu slug or admin URL to a relative wp-admin path.
	 *
	 * @param string $slug Menu slug or full admin URL.
	 * @return string
	 */
	private static function resolve_admin_slug( $slug ) {
		$slug = html_entity_decode( (string) $slug, ENT_QUOTES, 'UTF-8' );
		$slug = trim( $slug );

		if ( '' === $slug ) {
			return '';
		}

		if ( preg_match( '#^https?://#i', $slug ) ) {
			$admin_prefix = admin_url();
			if ( 0 === strpos( $slug, $admin_prefix ) ) {
				return ltrim( substr( $slug, strlen( $admin_prefix ) ), '/' );
			}

			$parsed = wp_parse_url( $slug );
			if ( ! empty( $parsed['path'] ) && preg_match( '#/wp-admin/(.+)$#', $parsed['path'], $matches ) ) {
				$relative = $matches[1];
				if ( ! empty( $parsed['query'] ) ) {
					$relative .= '?' . $parsed['query'];
				}

				return $relative;
			}

			return $slug;
		}

		$slug = preg_replace( '#^\/?wp-admin/#', '', $slug );

		return ltrim( $slug, '/' );
	}

	/**
	 * Build an admin URL from a relative menu slug.
	 *
	 * @param string $slug Relative wp-admin path.
	 * @return string
	 */
	private static function build_admin_url_from_slug( $slug ) {
		$slug = self::resolve_admin_slug( $slug );

		if ( preg_match( '#^https?://#i', $slug ) ) {
			return admin_url();
		}

		if ( '' === $slug ) {
			return admin_url();
		}

		$path  = $slug;
		$query = array();

		if ( false !== strpos( $slug, '?' ) ) {
			list( $path, $query_string ) = explode( '?', $slug, 2 );
			parse_str( $query_string, $query );
		}

		$url = admin_url( $path );

		if ( ! empty( $query ) ) {
			$url = add_query_arg( $query, $url );
		}

		return $url;
	}
}
