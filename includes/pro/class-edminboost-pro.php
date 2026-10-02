<?php
/**
 * Premium build: licensed admin UI partial loading (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Licensed admin UI helpers for the direct-download build.
 */
class EDMINBOOST_Pro {

	/**
	 * Relative path to the licensed admin partials directory (omitted from WordPress.org zips).
	 */
	const LICENSED_ADMIN_PARTIALS_DIR = 'admin/partials/pro/';

	/**
	 * Marker file that must exist when the licensed admin UI package ships with the build.
	 */
	const LICENSED_ADMIN_PACKAGE_MARKER = 'admin/partials/pro/index.php';

	/**
	 * Admin extension slot => partial basename under admin/partials/pro/.
	 */
	const ADMIN_EXTENSION_PARTIALS = array(
		'settings_backup'               => 'edminboost-settings-backup.php',
		'mapper_look_section'           => 'edminboost-mapper-look-section.php',
		'security_login_redirects'      => 'edminboost-security-login-redirects.php',
		'menu_custom_link'              => 'edminboost-menu-custom-link.php',
		'menu_display_mode'             => 'edminboost-menu-display-mode.php',
		'presets_role_matrix_head'      => 'edminboost-presets-role-matrix-head.php',
		'presets_role_matrix_cells'     => 'edminboost-presets-role-matrix-cells.php',
		'presets_role_visibility_help'  => 'edminboost-presets-role-visibility-help.php',
		'theme_extras_schedule_preview' => 'edminboost-theme-extras-schedule-preview.php',
		'theme_schedule_dark_mode'      => 'edminboost-theme-schedule-dark-mode.php',
	);

	/**
	 * Register premium admin UI hooks (called from edminboost-pro-package.php).
	 *
	 * @return void
	 */
	public static function register_admin_ui_hooks() {
		add_filter( 'edminboost_has_licensed_admin_ui', array( __CLASS__, 'filter_has_licensed_admin_ui' ) );
		add_filter( 'edminboost_allows_custom_preset_actions', array( __CLASS__, 'filter_allows_custom_preset_actions' ) );
		add_filter( 'edminboost_login_redirects_enabled', array( __CLASS__, 'filter_login_redirects_enabled' ), 10, 2 );
		add_filter( 'edminboost_mapper_item_sidebar_partial', array( __CLASS__, 'filter_mapper_item_sidebar_partial' ) );
		add_action( 'edminboost_admin_extension', array( __CLASS__, 'render_admin_extension' ) );
	}

	/**
	 * Whether licensed premium admin screens and partials should load.
	 *
	 * @return bool
	 */
	public static function has_licensed_admin_ui() {
		return (bool) apply_filters( 'edminboost_has_licensed_admin_ui', false );
	}

	/**
	 * Whether custom preset save/rename/duplicate actions are allowed on this build.
	 *
	 * @return bool
	 */
	public static function allows_custom_preset_actions() {
		if ( ! EDMINBOOST_Plan::is_direct_build() ) {
			return true;
		}

		return (bool) apply_filters( 'edminboost_allows_custom_preset_actions', false );
	}

	/**
	 * Whether this build ships the licensed admin UI partials package.
	 *
	 * @return bool
	 */
	public static function has_licensed_admin_package() {
		return file_exists( EDMINBOOST_PLUGIN_DIR . self::LICENSED_ADMIN_PACKAGE_MARKER );
	}

	/**
	 * @param bool $active Default from WordPress.org build.
	 * @return bool
	 */
	public static function filter_has_licensed_admin_ui( $active ) {
		if ( $active ) {
			return true;
		}

		if ( ! self::has_licensed_admin_package() ) {
			return false;
		}

		return EDMINBOOST_Plan_Licensing::is_active();
	}

	/**
	 * @param bool $allowed Default false on direct-download builds.
	 * @return bool
	 */
	public static function filter_allows_custom_preset_actions( $allowed ) {
		if ( $allowed ) {
			return true;
		}

		return EDMINBOOST_Plan_Licensing::is_active();
	}

	/**
	 * @param bool $enabled Default false on direct-download builds.
	 * @param bool $stored  Stored feature enabled flag.
	 * @return bool
	 */
	public static function filter_login_redirects_enabled( $enabled, $stored ) {
		unset( $enabled );

		return $stored && EDMINBOOST_Plan_Licensing::is_active();
	}

	/**
	 * @param string $path Default mapper sidebar partial path.
	 * @return string
	 */
	public static function filter_mapper_item_sidebar_partial( $path ) {
		if ( ! self::has_licensed_admin_ui() ) {
			return $path;
		}

		$licensed = EDMINBOOST_PLUGIN_DIR . self::LICENSED_ADMIN_PARTIALS_DIR . 'edminboost-mapper-item-sidebar.php';
		if ( file_exists( $licensed ) ) {
			return $licensed;
		}

		return $path;
	}

	/**
	 * Include a licensed admin partial for an extension slot.
	 *
	 * @param string $slot Extension slot identifier.
	 * @return void
	 */
	public static function render_admin_extension( $slot ) {
		if ( ! self::has_licensed_admin_ui() ) {
			return;
		}

		if ( ! isset( self::ADMIN_EXTENSION_PARTIALS[ $slot ] ) ) {
			return;
		}

		self::include_licensed_partial( self::ADMIN_EXTENSION_PARTIALS[ $slot ] );
	}

	/**
	 * Absolute path to a licensed admin partial file.
	 *
	 * @param string $basename Filename under admin/partials/pro/.
	 * @return string
	 */
	public static function get_licensed_partial_path( $basename ) {
		return EDMINBOOST_PLUGIN_DIR . self::LICENSED_ADMIN_PARTIALS_DIR . ltrim( $basename, '/' );
	}

	/**
	 * Include a licensed admin partial when the package is licensed and present.
	 *
	 * @param string $basename Filename under admin/partials/pro/.
	 * @return void
	 */
	public static function include_licensed_partial( $basename ) {
		if ( ! self::has_licensed_admin_ui() ) {
			return;
		}

		$path = self::get_licensed_partial_path( $basename );
		if ( ! file_exists( $path ) ) {
			return;
		}

		include $path;
	}
}
