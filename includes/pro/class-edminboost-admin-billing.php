<?php
/**
 * Premium build: Billing admin screen (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders the in-plugin Billing page on the direct-download build.
 */
class EDMINBOOST_Admin_Billing {

	/**
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'register_submenu' ), 20 );
		add_filter( 'edminboost_render_cc_page', array( __CLASS__, 'filter_render_cc_page' ), 10, 3 );
		add_filter( 'edminboost_cc_page_title', array( __CLASS__, 'filter_cc_page_title' ), 10, 2 );
	}

	/**
	 * @return string
	 */
	private static function get_page_slug() {
		return EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_BILLING;
	}

	/**
	 * @return void
	 */
	public static function register_submenu() {
		add_submenu_page(
			EDMINBOOST_Admin::PAGE_SLUG,
			__( 'Billing', 'edminboost-admin-customization' ),
			__( 'Billing', 'edminboost-admin-customization' ),
			EDMINBOOST_Settings::CAPABILITY,
			self::get_page_slug(),
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * @param bool             $handled Whether the page was rendered.
	 * @param string           $page    Page slug.
	 * @param EDMINBOOST_Admin $admin   Admin instance (unused).
	 * @return bool
	 */
	public static function filter_render_cc_page( $handled, $page, $admin ) {
		unset( $admin );

		if ( $handled || self::get_page_slug() !== $page ) {
			return $handled;
		}

		self::render_page();

		return true;
	}

	/**
	 * @param string $title Page title.
	 * @param string $page  Page slug.
	 * @return string
	 */
	public static function filter_cc_page_title( $title, $page ) {
		if ( self::get_page_slug() === $page ) {
			return __( 'Billing', 'edminboost-admin-customization' );
		}

		return $title;
	}

	/**
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		$cc_settings  = EDMINBOOST_Command_Center::get_settings();
		$current_page = self::get_current_page_slug();

		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/pro/edminboost-billing-page.php';
	}

	/**
	 * @return string
	 */
	private static function get_current_page_slug() {
		global $plugin_page;

		if ( ! empty( $plugin_page ) ) {
			return sanitize_key( $plugin_page );
		}

		if ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return '';
	}
}
