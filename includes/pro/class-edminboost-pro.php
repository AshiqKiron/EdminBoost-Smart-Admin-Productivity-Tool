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
	 * Command Center markup slot keys => partial basename under admin/partials/pro/.
	 */
	const LICENSED_PARTIAL_FILTERS = array(
		'edminboost_mapper_after_canvas_partials'           => 'edminboost-mapper-look-section.php',
		'edminboost_presets_role_matrix_head_partials'      => 'edminboost-presets-role-matrix-head.php',
		'edminboost_presets_role_matrix_row_partials'       => 'edminboost-presets-role-matrix-cells.php',
		'edminboost_presets_role_matrix_after_table_partials' => 'edminboost-presets-role-visibility-help.php',
		'edminboost_theme_settings_schedule_partials'       => 'edminboost-theme-schedule-dark-mode.php',
		'edminboost_theme_extras_schedule_preview_partials' => 'edminboost-theme-extras-schedule-preview.php',
		'edminboost_security_page_extra_partials'           => 'edminboost-security-login-redirects.php',
		'edminboost_menu_studio_custom_link_partials'       => 'edminboost-menu-custom-link.php',
		'edminboost_menu_studio_display_mode_partials'      => 'edminboost-menu-display-mode.php',
	);

	/**
	 * Register premium admin UI hooks (called from edminboost-pro-package.php).
	 *
	 * @return void
	 */
	public static function register_admin_ui_hooks() {
		add_filter( 'edminboost_should_register_licensed_admin_menus', array( __CLASS__, 'filter_should_register_licensed_admin_menus' ) );
		add_filter( 'edminboost_should_enqueue_licensed_admin_assets', array( __CLASS__, 'filter_should_enqueue_licensed_admin_assets' ) );
		add_filter( 'edminboost_show_topbar_interaction_ui', array( __CLASS__, 'filter_show_topbar_interaction_ui' ) );
		add_filter( 'edminboost_allows_custom_preset_actions', array( __CLASS__, 'filter_allows_custom_preset_actions' ) );
		add_filter( 'edminboost_login_redirects_enabled', array( __CLASS__, 'filter_login_redirects_enabled' ), 10, 2 );
		add_filter( 'edminboost_mapper_item_sidebar_partial', array( __CLASS__, 'filter_mapper_item_sidebar_partial' ) );

		foreach ( self::LICENSED_PARTIAL_FILTERS as $slot => $basename ) {
			add_filter(
				'edminboost_command_center_partial_paths',
				function ( $paths, $partial_slot ) use ( $slot, $basename ) {
					if ( $partial_slot !== $slot ) {
						return $paths;
					}

					return self::merge_licensed_partial_path( $paths, $basename );
				},
				10,
				2
			);
		}
	}

	/**
	 * Whether licensed premium admin screens and partials should load.
	 *
	 * @return bool
	 */
	public static function has_licensed_admin_ui() {
		if ( ! self::has_licensed_admin_package() ) {
			return false;
		}

		return EDMINBOOST_Plan_Licensing::is_active();
	}

	/**
	 * @param bool $register Default false on the WordPress.org build.
	 * @return bool
	 */
	public static function filter_should_register_licensed_admin_menus( $register ) {
		if ( $register ) {
			return true;
		}

		return self::has_licensed_admin_ui();
	}

	/**
	 * @param bool $enqueue Default false on the WordPress.org build.
	 * @return bool
	 */
	public static function filter_should_enqueue_licensed_admin_assets( $enqueue ) {
		if ( $enqueue ) {
			return true;
		}

		return self::has_licensed_admin_ui();
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
	 * @param bool $show Default false on the WordPress.org build.
	 * @return bool
	 */
	public static function filter_show_topbar_interaction_ui( $show ) {
		if ( $show ) {
			return true;
		}

		return self::has_licensed_admin_ui();
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
	 * Append a licensed partial path when the package is active.
	 *
	 * @param string[] $paths    Existing paths.
	 * @param string   $basename Filename under admin/partials/pro/.
	 * @return string[]
	 */
	public static function merge_licensed_partial_path( $paths, $basename ) {
		if ( ! self::has_licensed_admin_ui() ) {
			return is_array( $paths ) ? $paths : array();
		}

		$path = self::get_licensed_partial_path( $basename );
		if ( ! is_readable( $path ) ) {
			return is_array( $paths ) ? $paths : array();
		}

		if ( ! is_array( $paths ) ) {
			$paths = array();
		}

		$paths[] = $path;

		return $paths;
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
