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
