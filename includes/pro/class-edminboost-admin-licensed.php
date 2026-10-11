<?php
/**
 * Premium build: licensed admin screens and assets (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Settings, White Label, and related hooks when Pro/Agency is active.
 */
class EDMINBOOST_Admin_Licensed {

	/**
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'edminboost_register_admin_menus', array( __CLASS__, 'register_submenus' ) );
		add_filter( 'edminboost_command_center_page_links', array( __CLASS__, 'filter_page_links' ) );
		add_filter( 'edminboost_command_center_nav_items', array( __CLASS__, 'filter_nav_items' ) );
		add_filter( 'edminboost_should_sanitize_white_label', array( __CLASS__, 'filter_should_sanitize_white_label' ) );
		add_filter( 'edminboost_admin_script_data', array( __CLASS__, 'filter_admin_script_data' ), 10, 2 );
		add_filter( 'edminboost_cc_nav_ajax_response', array( __CLASS__, 'filter_cc_nav_ajax_response' ), 10, 2 );
		add_action( 'edminboost_enqueue_licensed_admin_scripts', array( __CLASS__, 'enqueue_scripts' ), 10, 2 );
		add_filter( 'plugin_action_links_' . EDMINBOOST_PLUGIN_BASENAME, array( __CLASS__, 'add_settings_link' ) );
		add_action( 'wp_ajax_edminboost_export_settings', array( __CLASS__, 'ajax_export_settings' ) );
		add_action( 'wp_ajax_edminboost_import_settings', array( __CLASS__, 'ajax_import_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ), 15 );
	}

	/**
	 * @param EDMINBOOST_Admin $admin Admin instance.
	 * @return void
	 */
	public static function register_submenus( $admin ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return;
		}

		$admin->register_licensed_submenus();
	}

	/**
	 * @param array[] $items Sidebar page links.
	 * @return array[]
	 */
	public static function filter_page_links( $items ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return $items;
		}

		$items[] = array(
			'slug'  => EDMINBOOST_Admin::PAGE_SLUG . '-settings',
			'label' => __( 'Settings', 'edminboost-admin-customization' ),
		);

		return $items;
	}

	/**
	 * @param array[] $items Tab navigation items.
	 * @return array[]
	 */
	public static function filter_nav_items( $items ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return $items;
		}

		$base = EDMINBOOST_Admin::PAGE_SLUG;

		$items[] = array(
			'slug'  => $base . EDMINBOOST_Command_Center::PAGE_WHITE_LABEL,
			'label' => __( 'White Label', EDMINBOOST_TEXT_DOMAIN ),
		);

		$items[] = array(
			'slug'  => $base . '-settings',
			'label' => __( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
		);

		return $items;
	}

	/**
	 * @param bool $should Default false on the WordPress.org build.
	 * @return bool
	 */
	public static function filter_should_sanitize_white_label( $should ) {
		return EDMINBOOST_Pro::has_licensed_admin_ui();
	}

	/**
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public static function add_settings_link( $links ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return $links;
		}

		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . '-settings' ) ),
			esc_html__( 'Settings', 'edminboost-admin-customization' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * @param array  $localize    Localized script data.
	 * @param string $screen_page Current admin page slug query arg.
	 * @return array
	 */
	public static function filter_admin_script_data( $localize, $screen_page ) {
		unset( $screen_page );

		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return $localize;
		}

		$localize['hasLicensedAdminUi'] = true;
		$localize['roleMatrix']         = array(
			'protectedSlugs'        => EDMINBOOST_Menu_Studio::get_protected_slugs(),
			'protectedSlugsByRole'  => EDMINBOOST_Command_Center::get_protected_slugs_by_role(),
			'accessibleSlugsByRole' => EDMINBOOST_Command_Center::get_role_accessible_menu_slugs(),
		);
		$localize['drawerPreview']      = array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'action'       => 'edminboost_cc_drawer_preview',
			'nonce'        => wp_create_nonce( 'edminboost_cc_drawer_preview' ),
			'contextNonce' => wp_create_nonce( 'edminboost_cc_mapper_context' ),
		);
		$localize['strings']            = array_merge(
			isset( $localize['strings'] ) && is_array( $localize['strings'] ) ? $localize['strings'] : array(),
			array(
				'topBarOverviewEmptyLicensed' => __( 'Admin link shortcuts can appear in your WordPress top bar. Add links in the Top Bar editor and choose whether each opens directly or in a slide-out drawer.', 'edminboost-admin-customization' ),
				/* translators: %d: number of links that open in a slide-out drawer */
				'topBarOpensInDrawerOne'      => __( '%d opens in a slide-out drawer', 'edminboost-admin-customization' ),
				/* translators: %d: number of links that open in a slide-out drawer */
				'topBarOpensInDrawerMany'     => __( '%d open in a slide-out drawer', 'edminboost-admin-customization' ),
				'opensInDrawer'               => __( 'Opens in drawer', 'edminboost-admin-customization' ),
				'drawerPreviewFailed'         => __( 'Could not open the drawer preview.', 'edminboost-admin-customization' ),
				/* translators: 1: drawer width in pixels, 2: approximate percentage of a typical desktop screen width. */
				'drawerWidthPreviewCaption'    => __( 'Drawer uses %1$s px — about %2$s%% of a typical desktop screen.', 'edminboost-admin-customization' ),
				'drawerWidthPreviewFullscreen' => __( 'Drawer uses the full screen width.', 'edminboost-admin-customization' ),
				'drawerWidthPreviewLabel'      => __( 'Drawer width preview on a typical desktop screen.', 'edminboost-admin-customization' ),
				'exportFailed'                => __( 'Could not export settings. Please try again.', 'edminboost-admin-customization' ),
				'importJsonRequired'          => __( 'Paste exported JSON or choose a file to import.', 'edminboost-admin-customization' ),
				'importFileRequired'          => __( 'Choose a JSON file to import.', 'edminboost-admin-customization' ),
				'importReadFailed'            => __( 'Could not read the selected file.', 'edminboost-admin-customization' ),
				'importFailed'                => __( 'Could not import settings. Check the JSON and try again.', 'edminboost-admin-customization' ),
			)
		);

		return $localize;
	}

	/**
	 * @param array  $response AJAX success payload.
	 * @param string $page     Loaded admin page slug.
	 * @return array
	 */
	public static function filter_cc_nav_ajax_response( $response, $page ) {
		unset( $page );

		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return $response;
		}

		$response['drawerNonce']        = wp_create_nonce( 'edminboost_cc_drawer_preview' );
		$response['drawerContextNonce'] = wp_create_nonce( 'edminboost_cc_mapper_context' );

		return $response;
	}

	/**
	 * @param string $hook_suffix Current admin page hook.
	 * @param string $screen_page Current admin page slug query arg.
	 * @return void
	 */
	public static function enqueue_scripts( $hook_suffix, $screen_page ) {
		unset( $screen_page );

		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return;
		}

		if ( false === strpos( $hook_suffix, EDMINBOOST_Admin::PAGE_SLUG ) ) {
			return;
		}

		wp_enqueue_script(
			'edminboost-admin-pro',
			EDMINBOOST_PLUGIN_URL . 'admin/js/pro/edminboost-admin-pro.js',
			array( 'edminboost-admin' ),
			EDMINBOOST_VERSION,
			true
		);
	}

	/**
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public static function enqueue_styles( $hook_suffix ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return;
		}

		if ( false === strpos( $hook_suffix, EDMINBOOST_Admin::PAGE_SLUG ) ) {
			return;
		}

		wp_enqueue_style(
			'edminboost-admin-pro',
			EDMINBOOST_PLUGIN_URL . 'admin/css/pro/edminboost-admin-pro.css',
			array( 'edminboost-admin' ),
			EDMINBOOST_VERSION
		);
	}

	/**
	 * @return void
	 */
	public static function ajax_export_settings() {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'edminboost-admin-customization' ) ), 403 );
		}

		edminboost_run_plugin()->get_admin()->ajax_export_settings();
	}

	/**
	 * @return void
	 */
	public static function ajax_import_settings() {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'edminboost-admin-customization' ) ), 403 );
		}

		edminboost_run_plugin()->get_admin()->ajax_import_settings();
	}
}
