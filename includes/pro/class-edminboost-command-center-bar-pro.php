<?php
/**
 * Premium build: Command Center drawer, badges, and live bar JS (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pro top bar runtime — slide-out drawer and badge counts.
 */
class EDMINBOOST_Command_Center_Bar_Pro {

	/**
	 * Register Pro-only top bar hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_prepare_drawer_frame' ) );
		add_action( 'admin_footer', array( __CLASS__, 'render_drawer_shell' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render_drawer_shell' ) );
		add_action( 'wp_ajax_edminboost_cc_drawer_preview', array( __CLASS__, 'ajax_drawer_preview' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_drawer_assets' ), 20 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_drawer_assets' ), 20 );
	}

	/**
	 * Add configured nodes with drawer interaction and badges (Pro/Agency active).
	 *
	 * @param WP_Admin_Bar $admin_bar Admin bar instance.
	 * @return void
	 */
	public static function register_nodes( $admin_bar ) {
		if ( ! EDMINBOOST_Plan_Licensing::is_active() || ! EDMINBOOST_Command_Center_Bar::is_active() ) {
			return;
		}

		$cc_settings = EDMINBOOST_Command_Center::get_settings();
		$badge_style = isset( $cc_settings['behavior']['badge_style'] ) ? $cc_settings['behavior']['badge_style'] : 'pill';

		foreach ( EDMINBOOST_Command_Center_Bar::get_items_for_current_user() as $item ) {
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
					'id'    => EDMINBOOST_Command_Center_Bar::get_node_id( $slug, $anchor ),
					'title' => $title,
					'href'  => $is_drawer ? '#' : EDMINBOOST_Command_Center_Bar::get_item_url( $slug, $anchor ),
					'meta'  => array(
						'class' => $node_classes,
						'title' => esc_attr( $label ),
					),
				)
			);
		}
	}

	/**
	 * Enqueue premium admin bar styles (drawer shell, badges, drawer triggers).
	 *
	 * @return void
	 */
	public static function enqueue_pro_styles() {
		if ( ! wp_style_is( 'edminboost-command-center-bar', 'enqueued' ) ) {
			return;
		}

		wp_enqueue_style(
			'edminboost-command-center-bar-pro',
			EDMINBOOST_PLUGIN_URL . 'admin/css/pro/edminboost-command-center-bar-pro.css',
			array( 'edminboost-command-center-bar' ),
			EDMINBOOST_VERSION
		);
	}

	/**
	 * Enqueue drawer shell JavaScript when needed.
	 *
	 * @return void
	 */
	public static function enqueue_drawer_assets() {
		if ( ! EDMINBOOST_Settings::is_enabled() || ! is_user_logged_in() ) {
			return;
		}

		$mapper_screen = EDMINBOOST_Command_Center_Bar::is_mapper_screen();
		$plugin_screen = EDMINBOOST_Admin::is_plugin_admin_page();
		$load_drawer   = self::has_drawer_items() || $mapper_screen || $plugin_screen;

		if ( ! $load_drawer ) {
			return;
		}

		self::enqueue_pro_styles();

		if ( ! wp_script_is( 'edminboost-command-center-bar', 'registered' ) ) {
			wp_register_script(
				'edminboost-command-center-bar',
				EDMINBOOST_PLUGIN_URL . 'admin/js/pro/edminboost-command-center-bar.js',
				array(),
				EDMINBOOST_VERSION,
				true
			);
		}

		wp_enqueue_script( 'edminboost-command-center-bar' );

		wp_localize_script(
			'edminboost-command-center-bar',
			'edminboostCcBar',
			array(
				'drawerItems' => self::get_drawer_items_config(),
				'animationMs' => self::get_animation_duration_ms(),
				'iframeTitle' => __( 'Admin page preview', 'edminboost-admin-customization' ),
			)
		);
	}

	/**
	 * AJAX: return a signed drawer preview URL for Layout Studio.
	 *
	 * @return void
	 */
	public static function ajax_drawer_preview() {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to preview drawer items.', 'edminboost-admin-customization' ) ),
				403
			);
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'edminboost_cc_drawer_preview' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security check failed. Refresh the page and try again.', 'edminboost-admin-customization' ),
				),
				403
			);
		}

		if ( ! self::is_mapper_preview_context() ) {
			wp_send_json_error(
				array( 'message' => __( 'Drawer preview is only available in Layout Studio.', 'edminboost-admin-customization' ) ),
				400
			);
		}

		$slug   = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
		$anchor = isset( $_POST['anchor'] ) ? sanitize_text_field( wp_unslash( $_POST['anchor'] ) ) : '';
		$label  = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : $slug;
		$anchor = ltrim( $anchor, '#' );

		if ( ! self::is_valid_drawer_slug( $slug, $anchor ) ) {
			wp_send_json_error(
				array( 'message' => __( 'That admin path cannot be previewed in the drawer.', 'edminboost-admin-customization' ) ),
				400
			);
		}

		wp_send_json_success(
			array(
				'label'    => $label,
				'frameUrl' => self::get_drawer_frame_url( $slug, $anchor, true ),
				'openUrl'  => EDMINBOOST_Command_Center_Bar::get_item_url( $slug, $anchor ),
			)
		);
	}

	/**
	 * Strip admin chrome when a page is loaded inside the drawer iframe.
	 *
	 * @return void
	 */
	public static function maybe_prepare_drawer_frame() {
		$drawer_flag = isset( $_GET['edminboost_drawer'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			? sanitize_text_field( wp_unslash( $_GET['edminboost_drawer'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			: '';

		if ( '1' !== $drawer_flag ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'You must be logged in to view this page.', 'edminboost-admin-customization' ), 403 );
		}

		$slug   = isset( $_GET['edminboost_slug'] ) ? sanitize_text_field( wp_unslash( $_GET['edminboost_slug'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$anchor = isset( $_GET['edminboost_anchor'] ) ? sanitize_text_field( wp_unslash( $_GET['edminboost_anchor'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$anchor = ltrim( $anchor, '#' );

		if ( '' === $slug ) {
			wp_die( esc_html__( 'Invalid drawer request.', 'edminboost-admin-customization' ), 400 );
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! wp_verify_nonce( $nonce, self::get_drawer_nonce_action( $slug, $anchor ) ) ) {
			wp_die( esc_html__( 'Invalid drawer request.', 'edminboost-admin-customization' ), 403 );
		}

		$mapper_preview_flag = isset( $_GET['edminboost_mapper_preview'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			? sanitize_text_field( wp_unslash( $_GET['edminboost_mapper_preview'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			: '';
		$is_mapper_preview   = '1' === $mapper_preview_flag;

		if (
			! self::is_drawer_slug_allowed( $slug, $anchor )
			&& ! (
				$is_mapper_preview
				&& current_user_can( EDMINBOOST_Settings::CAPABILITY )
				&& self::is_valid_drawer_slug( $slug, $anchor )
			)
		) {
			wp_die( esc_html__( 'You cannot open this page in the drawer.', 'edminboost-admin-customization' ), 403 );
		}

		if ( ! $is_mapper_preview ) {
			$user = wp_get_current_user();
			if ( ! EDMINBOOST_Command_Center::user_roles_can_access_menu_slug( $slug, (array) $user->roles ) ) {
				wp_die( esc_html__( 'You cannot open this page in the drawer.', 'edminboost-admin-customization' ), 403 );
			}
		}

		add_filter( 'admin_body_class', array( __CLASS__, 'filter_drawer_frame_body_class' ) );
		add_filter( 'show_admin_bar', '__return_false' );
		add_filter( 'wp_auth_check_load', '__return_false' );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'dequeue_drawer_frame_assets' ), 9999 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_drawer_frame_styles' ), 9999 );
	}

	/**
	 * Mark admin pages loaded inside the drawer iframe.
	 *
	 * @param string $classes Space-separated admin body classes.
	 * @return string
	 */
	public static function filter_drawer_frame_body_class( $classes ) {
		return trim( $classes . ' edminboost-cc-drawer-frame' );
	}

	/**
	 * Enqueue CSS to hide admin chrome inside the drawer iframe.
	 *
	 * @return void
	 */
	public static function enqueue_drawer_frame_styles() {
		$handle = 'edminboost-cc-drawer-frame';

		wp_register_style( $handle, false, array(), EDMINBOOST_VERSION );
		wp_enqueue_style( $handle );
		wp_add_inline_style( $handle, self::get_drawer_frame_css() );
	}

	/**
	 * CSS rules that strip admin chrome inside the drawer iframe.
	 *
	 * @return string
	 */
	public static function get_drawer_frame_css() {
		return '#wpadminbar,#adminmenumain,#wpfooter,#screen-meta,#screen-meta-links,.update-nag{display:none!important;}'
			. 'html.wp-toolbar{padding-top:0!important;}'
			. '#wpcontent,#wpbody{margin-left:0!important;}'
			. '#wpcontent{padding:0 20px!important;}'
			. '#wpbody-content{padding-bottom:20px;}'
			. '.folded #wpcontent{margin-left:0!important;}';
	}

	/**
	 * Remove admin assets not needed inside the drawer iframe.
	 *
	 * @return void
	 */
	public static function dequeue_drawer_frame_assets() {
		wp_dequeue_script( 'wp-auth-check' );
		wp_deregister_script( 'wp-auth-check' );
		wp_dequeue_style( 'wp-auth-check' );
		wp_deregister_style( 'wp-auth-check' );
		wp_dequeue_script( 'heartbeat' );
		wp_deregister_script( 'heartbeat' );
		wp_dequeue_script( 'admin-bar' );
		wp_dequeue_style( 'admin-bar' );
	}

	/**
	 * Output the slide-out drawer shell markup.
	 *
	 * @return void
	 */
	public static function render_drawer_shell() {
		if ( ! EDMINBOOST_Settings::is_enabled() || ! is_user_logged_in() ) {
			return;
		}

		if ( ! self::has_drawer_items() && ! EDMINBOOST_Command_Center_Bar::is_mapper_screen() && ! EDMINBOOST_Admin::is_plugin_admin_page() ) {
			return;
		}

		$behavior           = EDMINBOOST_Command_Center::get_settings()['behavior'];
		$width_class        = self::get_drawer_width_class( $behavior );
		$panel_style        = self::get_drawer_panel_style( $behavior );
		$glass_class        = ! empty( $behavior['glassmorphism'] ) ? ' is-glass' : '';
		$duration_ms        = self::get_animation_duration_ms();
		$drawer_class       = 'edminboost-cc-drawer' . $glass_class;
		$drawer_style       = '--edminboost-cc-drawer-duration:' . (int) $duration_ms . 'ms';
		$drawer_panel_class = 'edminboost-cc-drawer__panel ' . $width_class;
		?>
		<div id="edminboost-cc-drawer" class="<?php echo esc_attr( $drawer_class ); ?>" hidden style="<?php echo esc_attr( $drawer_style ); ?>">
			<div class="edminboost-cc-drawer__backdrop" aria-hidden="true"></div>
			<aside
				class="<?php echo esc_attr( $drawer_panel_class ); ?>"
				role="dialog"
				aria-modal="true"
				aria-labelledby="edminboost-cc-drawer-title"
				<?php if ( $panel_style ) : ?>
					style="<?php echo esc_attr( $panel_style ); ?>"
				<?php endif; ?>
			>
				<header class="edminboost-cc-drawer__header">
					<h2 id="edminboost-cc-drawer-title" class="edminboost-cc-drawer__title"></h2>
					<a class="edminboost-cc-drawer__open-full" href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open full page', 'edminboost-admin-customization' ); ?></a>
					<button type="button" class="edminboost-cc-drawer__close" aria-label="<?php esc_attr_e( 'Close drawer', 'edminboost-admin-customization' ); ?>">&times;</button>
				</header>
				<div class="edminboost-cc-drawer__body">
					<div class="edminboost-cc-drawer__loading" aria-live="polite"><?php esc_html_e( 'Loading…', 'edminboost-admin-customization' ); ?></div>
					<?php foreach ( self::get_drawer_items_config() as $item ) : ?>
						<?php if ( empty( $item['frameUrl'] ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<iframe
							class="edminboost-cc-drawer__iframe"
							hidden
							data-edminboost-frame-url="<?php echo esc_attr( $item['frameUrl'] ); ?>"
							title="<?php esc_attr_e( 'Admin page preview', 'edminboost-admin-customization' ); ?>"
							src="about:blank"
						></iframe>
					<?php endforeach; ?>
				</div>
			</aside>
		</div>
		<?php
	}

	/**
	 * Whether the current user has drawer interaction items on the top bar.
	 *
	 * @return bool
	 */
	public static function has_drawer_items() {
		if ( ! EDMINBOOST_Plan_Licensing::is_active() ) {
			return false;
		}

		foreach ( EDMINBOOST_Command_Center_Bar::get_items_for_current_user() as $item ) {
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

		foreach ( EDMINBOOST_Command_Center_Bar::get_items_for_current_user() as $item ) {
			$slug   = isset( $item['slug'] ) ? $item['slug'] : '';
			$anchor = isset( $item['anchor'] ) ? $item['anchor'] : '';

			if ( '' === $slug || ! isset( $item['interaction'] ) || 'drawer' !== $item['interaction'] ) {
				continue;
			}

			$node_id = EDMINBOOST_Command_Center_Bar::get_node_id( $slug, $anchor );

			$config[ $node_id ] = array(
				'slug'     => $slug,
				'label'    => isset( $item['label'] ) ? $item['label'] : $slug,
				'frameUrl' => self::get_drawer_frame_url( $slug, $anchor ),
				'openUrl'  => EDMINBOOST_Command_Center_Bar::get_item_url( $slug, $anchor ),
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

		foreach ( EDMINBOOST_Command_Center_Bar::get_items_for_current_user() as $item ) {
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
		if ( EDMINBOOST_Command_Center_Bar::is_mapper_screen() ) {
			return true;
		}

		$context_nonce = isset( $_POST['mapper_context_nonce'] )
			? sanitize_text_field( wp_unslash( $_POST['mapper_context_nonce'] ) )
			: '';

		if ( '' === $context_nonce ) {
			return false;
		}

		return (bool) wp_verify_nonce( $context_nonce, 'edminboost_cc_mapper_context' );
	}

	/**
	 * Whether a slug and anchor are valid for drawer iframe loads.
	 *
	 * @param string $slug   Menu slug.
	 * @param string $anchor Optional URL fragment.
	 * @return bool
	 */
	private static function is_valid_drawer_slug( $slug, $anchor = '' ) {
		$anchor = ltrim( (string) $anchor, '#' );
		$slug   = self::resolve_admin_slug( $slug );

		if ( preg_match( '#^https?://#i', $slug ) ) {
			return false;
		}

		if ( '' === $slug || ! preg_match( '/^[a-zA-Z0-9_\-.\/?=&%]+$/', $slug ) ) {
			return false;
		}

		if ( '' !== $anchor && ! preg_match( '/^[a-zA-Z0-9_\-\.]+$/', $anchor ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Build a signed URL for loading an admin page inside the drawer iframe.
	 *
	 * @param string $slug           Menu slug.
	 * @param string $anchor         Optional URL fragment.
	 * @param bool   $mapper_preview Whether this is a Layout Studio preview request.
	 * @return string
	 */
	private static function get_drawer_frame_url( $slug, $anchor = '', $mapper_preview = false ) {
		$anchor = ltrim( (string) $anchor, '#' );

		$args = array(
			'edminboost_drawer' => '1',
			'edminboost_slug'   => $slug,
			'_wpnonce'          => wp_create_nonce( self::get_drawer_nonce_action( $slug, $anchor ) ),
		);

		if ( $mapper_preview ) {
			$args['edminboost_mapper_preview'] = '1';
		}

		if ( '' !== $anchor ) {
			$args['edminboost_anchor'] = $anchor;
		}

		$url = add_query_arg( $args, EDMINBOOST_Command_Center_Bar::get_item_url( $slug, '' ) );

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
		$allowed = array( 'compact', 'standard', 'fullscreen', 'custom' );

		if ( ! in_array( $width, $allowed, true ) ) {
			$width = 'standard';
		}

		return 'edminboost-cc-drawer__panel--' . $width;
	}

	/**
	 * Inline panel style for custom drawer width.
	 *
	 * @param array $behavior Behavior settings.
	 * @return string
	 */
	private static function get_drawer_panel_style( $behavior ) {
		$width = isset( $behavior['drawer_width'] ) ? $behavior['drawer_width'] : 'standard';

		if ( 'custom' !== $width ) {
			return '';
		}

		return sprintf(
			'--edminboost-cc-drawer-width:%dpx',
			self::get_drawer_custom_width_px( $behavior )
		);
	}

	/**
	 * Sanitized custom drawer width in pixels.
	 *
	 * @param array $behavior Behavior settings.
	 * @return int
	 */
	private static function get_drawer_custom_width_px( $behavior ) {
		$px = isset( $behavior['drawer_width_custom'] )
			? absint( $behavior['drawer_width_custom'] )
			: EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_DEFAULT;

		return max(
			EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MIN,
			min( EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MAX, $px )
		);
	}

	/**
	 * Animation duration in milliseconds from behavior settings.
	 *
	 * @return int
	 */
	private static function get_animation_duration_ms() {
		return EDMINBOOST_Command_Center::get_animation_duration_ms();
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
		$icon   = EDMINBOOST_Command_Center::normalize_dashicon_class( $icon );
		$title  = '<span class="edminboost-cc-bar-icon dashicons ' . esc_attr( $icon ) . '" aria-hidden="true"></span>';
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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$count = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM `' . esc_sql( $table ) . '` WHERE viewed = %d',
				0
			)
		);

		return $count ? (int) $count : 0;
	}
}
