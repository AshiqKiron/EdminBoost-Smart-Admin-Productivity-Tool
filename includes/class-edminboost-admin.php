<?php
/**
 * Admin area — menus, settings pages, asset enqueuing.
 *
 * Purpose: Register admin UI via admin_menu and Settings API.
 *          Render dashboard/settings partials. Enqueue assets only on plugin screens.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin handler.
 */
class EDMINBOOST_Admin {

	/**
	 * Admin page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = EDMINBOOST_PLUGIN_SLUG;

	/**
	 * Top-level admin menu order (core Tools uses 75).
	 *
	 * @var int
	 */
	const ADMIN_MENU_POSITION = 76;

	/**
	 * Feature manager.
	 *
	 * @var EDMINBOOST_Features
	 */
	protected $features;

	/**
	 * Constructor.
	 *
	 * @param EDMINBOOST_Features $features Feature manager.
	 */
	public function __construct( EDMINBOOST_Features $features ) {
		$this->features = $features;
	}

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public function register_menu() {
		$dashboard_title = __( 'Dashboard', 'edminboost-admin-customization' );

		$this->bind_admin_page_title(
			add_menu_page(
				$dashboard_title,
				__( 'EdminBoost', 'edminboost-admin-customization' ),
				EDMINBOOST_Settings::CAPABILITY,
				self::PAGE_SLUG,
				array( $this, 'render_admin_page' ),
				'dashicons-performance',
				self::ADMIN_MENU_POSITION
			),
			$dashboard_title
		);

		$this->register_plugin_submenu_page(
			self::PAGE_SLUG,
			$dashboard_title,
			$dashboard_title,
			self::PAGE_SLUG,
			array( $this, 'render_admin_page' )
		);

		$this->register_plugin_submenu_page(
			self::PAGE_SLUG,
			__( 'Layouts', 'edminboost-admin-customization' ),
			__( 'Layouts', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRESETS,
			array( $this, 'render_presets_page' )
		);

		$this->register_plugin_submenu_page(
			self::PAGE_SLUG,
			__( 'Theme', 'edminboost-admin-customization' ),
			__( 'Theme', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_APPEARANCE,
			array( $this, 'render_appearance_page' )
		);

		$this->register_plugin_submenu_page(
			self::PAGE_SLUG,
			__( 'Top Bar', 'edminboost-admin-customization' ),
			__( 'Top Bar', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER,
			array( $this, 'render_mapper_page' )
		);

		$this->register_plugin_submenu_page(
			self::PAGE_SLUG,
			__( 'Menu Studio', 'edminboost-admin-customization' ),
			__( 'Menu Studio', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MENU_STUDIO,
			array( $this, 'render_menu_studio_page' )
		);

		// Tab-only pages — registered but not shown in the sidebar.
		$this->register_plugin_submenu_page(
			null,
			__( 'Productivity', 'edminboost-admin-customization' ),
			__( 'Productivity', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRODUCTIVITY,
			array( $this, 'render_productivity_page' )
		);

		$this->register_plugin_submenu_page(
			null,
			__( 'Security', 'edminboost-admin-customization' ),
			__( 'Security', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_SECURITY,
			array( $this, 'render_security_page' )
		);

		$this->register_plugin_submenu_page(
			null,
			__( 'Performance', 'edminboost-admin-customization' ),
			__( 'Performance', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PERFORMANCE,
			array( $this, 'render_performance_page' )
		);

		if ( apply_filters( 'edminboost_should_register_licensed_admin_menus', false ) ) {
			/**
			 * Register optional admin menus (premium package adds Settings and White Label when licensed).
			 *
			 * @param EDMINBOOST_Admin $admin Admin instance.
			 */
			do_action( 'edminboost_register_admin_menus', $this );
		}

		// Legacy slugs — redirect to Dashboard (not shown in sidebar).
		$this->register_plugin_submenu_page(
			null,
			$dashboard_title,
			$dashboard_title,
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_ONBOARDING,
			array( $this, 'render_onboarding_page' )
		);

		$this->register_plugin_submenu_page(
			null,
			__( 'Theme', 'edminboost-admin-customization' ),
			__( 'Theme', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_BEHAVIOR,
			array( $this, 'render_behavior_page' )
		);
	}

	/**
	 * Register a plugin submenu page and bind its document title.
	 *
	 * @param string|null        $parent_slug Parent menu slug (null for tab-only pages).
	 * @param string             $page_title  Document title for the screen.
	 * @param string             $menu_title  Sidebar menu label.
	 * @param string             $menu_slug   Unique page slug.
	 * @param callable           $callback    Page render callback.
	 * @return void
	 */
	private function register_plugin_submenu_page( $parent_slug, $page_title, $menu_title, $menu_slug, $callback ) {
		$this->bind_admin_page_title(
			add_submenu_page(
				$parent_slug,
				$page_title,
				$menu_title,
				EDMINBOOST_Settings::CAPABILITY,
				$menu_slug,
				$callback
			),
			$page_title
		);
	}

	/**
	 * Ensure admin-header.php receives a string page title (PHP 8.1+).
	 *
	 * Hidden submenu pages (null parent) are not resolved by get_admin_page_title().
	 *
	 * @param string|false $hook_suffix Hook suffix from add_menu_page / add_submenu_page.
	 * @param string       $page_title  Document title when the screen loads.
	 * @return void
	 */
	private function bind_admin_page_title( $hook_suffix, $page_title ) {
		if ( ! $hook_suffix ) {
			return;
		}

		add_action(
			"load-{$hook_suffix}",
			static function () use ( $page_title ) {
				global $title;
				$title = $page_title;
			}
		);
	}

	/**
	 * Enforce canonical EdminBoost submenu order after Menu Studio reordering.
	 *
	 * @return void
	 */
	public function normalize_plugin_submenu() {
		global $submenu;

		if ( empty( $submenu[ self::PAGE_SLUG ] ) || ! is_array( $submenu[ self::PAGE_SLUG ] ) ) {
			return;
		}

		$dashboard_label = __( 'Dashboard', 'edminboost-admin-customization' );
		$items_by_slug   = array();

		foreach ( $submenu[ self::PAGE_SLUG ] as $item ) {
			if ( empty( $item[2] ) ) {
				continue;
			}

			$slug = (string) $item[2];
			if ( ! isset( $items_by_slug[ $slug ] ) ) {
				$items_by_slug[ $slug ] = $item;
			}
		}

		$ordered = array();
		$seen    = array();

		foreach ( EDMINBOOST_Command_Center::get_page_links() as $link ) {
			$slug = (string) $link['slug'];
			if ( ! isset( $items_by_slug[ $slug ] ) ) {
				continue;
			}

			$item = $items_by_slug[ $slug ];
			if ( self::PAGE_SLUG === $slug ) {
				$item[0] = $dashboard_label;
			}

			$ordered[]     = $item;
			$seen[ $slug ] = true;
		}

		foreach ( $submenu[ self::PAGE_SLUG ] as $item ) {
			if ( empty( $item[2] ) ) {
				$ordered[] = $item;
				continue;
			}

			$slug = (string) $item[2];
			if ( isset( $seen[ $slug ] ) ) {
				continue;
			}

			$ordered[] = $item;
		}

		$submenu[ self::PAGE_SLUG ] = $ordered;
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			EDMINBOOST_Settings::SETTINGS_GROUP,
			EDMINBOOST_Settings::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'EDMINBOOST_Settings', 'sanitize' ),
				'default'           => EDMINBOOST_Settings::get_defaults(),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Get plugin settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		return EDMINBOOST_Settings::get();
	}

	/**
	 * Render the Dashboard hub page.
	 *
	 * @return void
	 */
	public function render_admin_page() {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		$cc_settings  = EDMINBOOST_Command_Center::get_settings();
		$current_page = self::PAGE_SLUG;

		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-admin-page.php';
	}

	/**
	 * Redirect legacy onboarding URL to Dashboard.
	 *
	 * @return void
	 */
	public function render_onboarding_page() {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) );
		exit;
	}

	/**
	 * Render the layout mapper page.
	 *
	 * @return void
	 */
	public function render_mapper_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-mapper-page.php' );
	}

	/**
	 * Render the presets & roles page.
	 *
	 * @return void
	 */
	public function render_presets_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-presets-page.php' );
	}

	/**
	 * Render the Menu Studio page.
	 *
	 * @return void
	 */
	public function render_menu_studio_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-menu-studio-page.php' );
	}

	/**
	 * Redirect legacy behavior URL to Appearance.
	 *
	 * @return void
	 */
	public function render_behavior_page() {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_APPEARANCE ) );
		exit;
	}

	/**
	 * Render the Appearance settings page.
	 *
	 * @return void
	 */
	public function render_appearance_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-appearance-page.php' );
	}

	/**
	 * Render the Productivity settings page.
	 *
	 * @return void
	 */
	public function render_productivity_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-productivity-page.php' );
	}

	/**
	 * Render the Security settings page.
	 *
	 * @return void
	 */
	public function render_security_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-security-page.php' );
	}

	/**
	 * Render the Performance settings page.
	 *
	 * @return void
	 */
	public function render_performance_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-performance-page.php' );
	}

	/**
	 * Render a Command Center partial.
	 *
	 * @param string $partial Relative path under the plugin directory.
	 * @return void
	 */
	private function render_command_center_page( $partial ) {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		$cc_settings  = EDMINBOOST_Command_Center::get_settings();
		$current_page = $this->get_current_cc_page_slug();

		include EDMINBOOST_PLUGIN_DIR . $partial;
	}

	/**
	 * Render a Command Center partial (used by premium licensed screens and CC tab AJAX).
	 *
	 * @param string $partial Relative path under the plugin directory.
	 * @return void
	 */
	public function render_command_center_partial( $partial ) {
		$this->render_command_center_page( $partial );
	}

	/**
	 * Include optional admin partial paths supplied by filters (empty on WordPress.org).
	 *
	 * When the first argument is an associative array, its keys are extracted into the
	 * partial include scope (partials included from a static method do not inherit the caller scope).
	 *
	 * @param string $slot    Markup slot key (edminboost_*); premium package maps slots to partials.
	 * @param mixed  ...$args Optional variable context array, then extra filter arguments for the slot filter.
	 * @return void
	 */
	public static function include_filtered_partials( $slot, ...$args ) {
		$include_context = array();
		$filter_args     = $args;

		if ( ! empty( $args ) && is_array( $args[0] ) && self::is_partial_include_context( $args[0] ) ) {
			$include_context = $args[0];
			$filter_args     = array_slice( $args, 1 );
		}

		/**
		 * Filter optional admin partial paths to include at a Command Center markup slot.
		 *
		 * @param string[] $partial_paths Absolute paths; default empty array.
		 * @param string   $slot          Markup slot key (same value passed to include_filtered_partials()).
		 */
		$partial_paths = apply_filters( 'edminboost_command_center_partial_paths', array(), $slot, ...$filter_args );

		if ( ! is_array( $partial_paths ) ) {
			return;
		}

		foreach ( $partial_paths as $partial_path ) {
			if ( ! is_string( $partial_path ) || ! is_readable( $partial_path ) ) {
				continue;
			}

			if ( ! empty( $include_context ) ) {
				// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- scoped partial variables from caller.
				extract( $include_context, EXTR_SKIP );
			}

			include $partial_path;
		}
	}

	/**
	 * Whether an array is a variable context map for partial includes (not a numeric filter argument list).
	 *
	 * @param array $context Candidate context array.
	 * @return bool
	 */
	private static function is_partial_include_context( $context ) {
		if ( ! is_array( $context ) || array() === $context ) {
			return false;
		}

		foreach ( array_keys( $context ) as $key ) {
			if ( is_string( $key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Resolve the active Command Center admin page slug for nav highlighting.
	 *
	 * Uses the global $plugin_page set by wp-admin (or primed during CC tab AJAX).
	 *
	 * @return string
	 */
	private function get_current_cc_page_slug() {
		global $plugin_page;

		if ( ! empty( $plugin_page ) ) {
			return sanitize_key( $plugin_page );
		}

		if ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return '';
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {
		if ( ! $this->is_plugin_screen( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_style( 'dashicons' );

		wp_enqueue_style(
			'edminboost-themes',
			EDMINBOOST_PLUGIN_URL . 'admin/css/edminboost-themes.css',
			array(),
			EDMINBOOST_VERSION
		);

		wp_enqueue_style(
			'edminboost-admin',
			EDMINBOOST_PLUGIN_URL . 'admin/css/edminboost-admin.css',
			array( 'dashicons', 'edminboost-themes' ),
			EDMINBOOST_VERSION
		);

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix ) {
		if ( ! $this->is_plugin_screen( $hook_suffix ) ) {
			return;
		}

		$screen_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		/**
		 * Filter script dependencies for the plugin admin bundle.
		 *
		 * Premium package may enqueue drawer assets and append handles (e.g. edminboost-command-center-bar).
		 *
		 * @param string[] $deps        Script handle dependencies.
		 * @param string   $hook_suffix Current admin page hook.
		 * @param string   $screen_page Current admin page slug query arg.
		 */
		$admin_script_deps = apply_filters( 'edminboost_admin_script_deps', array(), $hook_suffix, $screen_page );

		wp_enqueue_script(
			'edminboost-admin',
			EDMINBOOST_PLUGIN_URL . 'admin/js/edminboost-admin.js',
			$admin_script_deps,
			EDMINBOOST_VERSION,
			true
		);

		$localize = array(
			'version'              => EDMINBOOST_VERSION,
			'isPremiumBuild'       => EDMINBOOST_Plan::is_direct_build(),
			'currentPage'          => $screen_page,
			'optionName'           => EDMINBOOST_Settings::OPTION_NAME,
			'strings'              => array(
				'ready'           => __( 'EdminBoost is ready.', 'edminboost-admin-customization' ),
				'configureItem'   => __( 'Configure', 'edminboost-admin-customization' ),
				'removeFromTopBar' => __( 'Remove from top bar', 'edminboost-admin-customization' ),
				'emptyCanvas'     => __( 'Toggle items from the left panel or drag them here to build your top bar.', 'edminboost-admin-customization' ),
				'customLinkPathRequired'  => __( 'Enter an admin path.', 'edminboost-admin-customization' ),
				'customLinkLabelRequired' => __( 'Enter a label.', 'edminboost-admin-customization' ),
				'customLinkPathInvalid'   => __( 'Use a relative admin path such as edit.php?post_type=page.', 'edminboost-admin-customization' ),
				'customLinkAnchorInvalid' => __( 'Use letters, numbers, hyphens, underscores, or dots in the anchor.', 'edminboost-admin-customization' ),
				'customLinkDuplicate'     => __( 'That link is already on your top bar.', 'edminboost-admin-customization' ),
				'topBarOverviewEmpty'       => __( 'Admin link shortcuts can appear in your WordPress top bar. Add links in the Top Bar editor.', 'edminboost-admin-customization' ),
				'topBarOverviewHasLinks'    => __( 'Admin link shortcuts appear in your WordPress top bar.', 'edminboost-admin-customization' ),
				'settingsSaved'             => __( 'Settings saved.', 'edminboost-admin-customization' ),
				'settingsSaveFailed'        => __( 'Could not save settings. Please try again.', 'edminboost-admin-customization' ),
				'sessionExpired'            => __( 'Your session expired. Refresh the page and log in again.', 'edminboost-admin-customization' ),
				'presetApplied'             => __( 'Preset applied.', 'edminboost-admin-customization' ),
				'presetNameRequired'        => __( 'Enter a name for your preset.', 'edminboost-admin-customization' ),
				'presetSaved'               => __( 'Preset saved.', 'edminboost-admin-customization' ),
				'presetRenamed'             => __( 'Preset renamed.', 'edminboost-admin-customization' ),
				'presetDuplicated'          => __( 'Preset duplicated.', 'edminboost-admin-customization' ),
				'emptyMenuCanvas'           => __( 'Drag menu items here to reorder your admin sidebar.', 'edminboost-admin-customization' ),
				'removeFromSidebar'         => __( 'Remove from sidebar', 'edminboost-admin-customization' ),
				'selectLayoutPreset'        => __( 'Select a layout preset to continue.', 'edminboost-admin-customization' ),
				'saveAndLaunch'             => __( 'Save and launch', 'edminboost-admin-customization' ),
				'presetBadgeBuiltIn'        => __( 'Built-in', 'edminboost-admin-customization' ),
				'presetBadgeSaved'          => __( 'Saved', 'edminboost-admin-customization' ),
				'presetBadgeVirtual'        => __( 'Layout', 'edminboost-admin-customization' ),
				'emptyLayoutPreview'        => __( 'No links in this preview yet.', 'edminboost-admin-customization' ),
				'emptySidebarPreview'       => __( 'No sidebar items in this preview yet.', 'edminboost-admin-customization' ),
				'previewWordPressLogo'      => __( 'WordPress', 'edminboost-admin-customization' ),
				'previewProfile'            => __( 'My account', 'edminboost-admin-customization' ),
				'pageLoading'               => __( 'Loading…', 'edminboost-admin-customization' ),
				'pageLoadFailed'            => __( 'Could not load that page. Please try again.', 'edminboost-admin-customization' ),
				'formResetConfirm'          => __( 'Reset all fields on this page to their default values? Your saved settings are not changed until you click Save.', 'edminboost-admin-customization' ),
				'formResetConfirmYes'       => __( 'Yes, reset to defaults', 'edminboost-admin-customization' ),
				'formResetCancel'           => __( 'Cancel', 'edminboost-admin-customization' ),
			),
			'presets'          => self::get_presets_for_js(),
			'presetCategories' => EDMINBOOST_Command_Center::get_preset_categories(),
			'themePresets'     => EDMINBOOST_Theme::get_presets_for_js(),
			'themeColorLabels' => EDMINBOOST_Theme::get_color_labels(),
			'themeSettings'    => EDMINBOOST_Theme::get_settings(),
			'settingsSave' => array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => 'edminboost_save_settings',
			),
			'ccNav'        => array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => 'edminboost_load_cc_page',
				'nonce'   => wp_create_nonce( 'edminboost_cc_nav' ),
			),
		);

		/**
		 * Filter localized data for plugin admin JavaScript.
		 *
		 * @param array  $localize    Localized script data.
		 * @param string $screen_page Current admin page slug query arg.
		 */
		$localize = apply_filters( 'edminboost_admin_script_data', $localize, $screen_page );

		wp_localize_script(
			'edminboost-admin',
			'edminboostData',
			$localize
		);

		if ( apply_filters( 'edminboost_should_enqueue_licensed_admin_assets', false ) ) {
			/**
			 * Enqueue licensed admin scripts after the base plugin script.
			 *
			 * @param string $hook_suffix Current admin page hook.
			 * @param string $screen_page Current admin page slug query arg.
			 */
			do_action( 'edminboost_enqueue_licensed_admin_scripts', $hook_suffix, $screen_page );
		}
	}

	/**
	 * Build preset catalog for admin JavaScript (includes resolved layouts).
	 *
	 * @return array
	 */
	private function get_presets_for_js() {
		$presets = array();

		foreach ( EDMINBOOST_Command_Center::get_picker_presets( true ) as $preset_id => $preset ) {
			if ( ! EDMINBOOST_Plan::include_preset_in_ui( $preset_id, 'layout' ) ) {
				continue;
			}

			$presets[ $preset_id ] = array(
				'name'           => isset( $preset['name'] ) ? $preset['name'] : $preset_id,
				'description'    => isset( $preset['description'] ) ? $preset['description'] : '',
				'system'         => ! empty( $preset['system'] ),
				'virtual'        => ! empty( $preset['virtual'] ),
				'category'       => ! empty( $preset['virtual'] )
					? 'source'
					: ( ! empty( $preset['system'] )
						? ( isset( $preset['category'] ) ? $preset['category'] : 'workflow' )
						: 'saved' ),
				'top_bar_items'      => EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $preset_id ),
				'sidebar_items'      => EDMINBOOST_Command_Center::resolve_preset_sidebar_preview_items( $preset_id ),
				'menu_studio'        => EDMINBOOST_Command_Center::resolve_preset_menu_studio( $preset_id ),
				'visible_menu_slugs' => EDMINBOOST_Command_Center::get_preset_visible_menu_slugs( $preset_id ),
				'visible_top_level_menu_slugs' => EDMINBOOST_Command_Center::get_preset_visible_top_level_menu_slugs( $preset_id ),
			);
		}

		return $presets;
	}

	/**
	 * Redirect to the Dashboard setup wizard after activation.
	 *
	 * @return void
	 */
	public function maybe_activation_redirect() {
		if ( ! is_user_logged_in() || ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		if ( ! get_transient( 'edminboost_activation_redirect' ) ) {
			return;
		}

		delete_transient( 'edminboost_activation_redirect' );

		if ( EDMINBOOST_Command_Center::is_setup_complete() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- core activation flow.
		if ( isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- admin page routing.
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		if ( self::PAGE_SLUG === $page ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) );
		exit;
	}

	/**
	 * Add a documentation link on the plugins screen.
	 *
	 * @param array  $links       Plugin row meta links.
	 * @param string $plugin_file Plugin basename.
	 * @return array
	 */
	public function add_plugin_row_meta( $links, $plugin_file ) {
		if ( EDMINBOOST_PLUGIN_BASENAME !== $plugin_file ) {
			return $links;
		}

		$links[] = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( EDMINBOOST_PLUGIN_DOCS_URL ),
			esc_html__( 'Docs', 'edminboost-admin-customization' )
		);

		return $links;
	}

	/**
	 * Whether the given admin page slug belongs to this plugin.
	 *
	 * @param string|null $page Optional page slug; reads $_GET['page'] when null.
	 * @return bool
	 */
	public static function is_plugin_admin_page( $page = null ) {
		if ( null === $page ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- routing helper.
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		}

		if ( '' === $page ) {
			return false;
		}

		return 0 === strpos( $page, self::PAGE_SLUG );
	}

	/**
	 * Output buffer depth before an AJAX handler starts its capture buffer.
	 *
	 * @var int
	 */
	private static $ajax_json_buffer_depth = 0;

	/**
	 * Whether the early EdminBoost admin-ajax output buffer was started.
	 *
	 * @var bool
	 */
	private static $ajax_prime_buffer_started = false;

	/**
	 * Start output buffering as early as possible for EdminBoost admin-ajax actions.
	 *
	 * @return void
	 */
	public function prime_edminboost_ajax_output_buffer() {
		if ( ! wp_doing_ajax() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- routing only.
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
		if ( '' === $action || 0 !== strpos( $action, 'edminboost_' ) ) {
			return;
		}

		if ( self::$ajax_prime_buffer_started ) {
			return;
		}

		self::$ajax_prime_buffer_started = true;
		self::$ajax_json_buffer_depth    = ob_get_level();
		ob_start();
	}

	/**
	 * Begin capturing stray output for an EdminBoost admin-ajax handler.
	 *
	 * @return void
	 */
	public static function begin_ajax_json_response() {
		self::begin_ajax_json_buffer();
	}

	/**
	 * @return void
	 */
	private static function begin_ajax_json_buffer() {
		if ( ! self::$ajax_prime_buffer_started ) {
			self::$ajax_json_buffer_depth = ob_get_level();
			ob_start();
			return;
		}

		ob_start();
	}

	/**
	 * Discard captured AJAX output so the response body is valid JSON only.
	 *
	 * @return void
	 */
	private static function discard_ajax_json_buffer() {
		while ( ob_get_level() > self::$ajax_json_buffer_depth ) {
			ob_end_clean();
		}
	}

	/**
	 * @param mixed $data Success payload.
	 * @return void
	 */
	public static function send_ajax_json_success( $data ) {
		self::discard_ajax_json_buffer();
		wp_send_json_success( $data );
	}

	/**
	 * @param mixed    $data        Error payload.
	 * @param int|null $status_code Optional HTTP status code.
	 * @return void
	 */
	public static function send_ajax_json_error( $data, $status_code = null ) {
		self::discard_ajax_json_buffer();
		if ( null !== $status_code ) {
			wp_send_json_error( $data, $status_code );
		}

		wp_send_json_error( $data );
	}

	/**
	 * AJAX: load a Command Center tab without a full page reload.
	 *
	 * @return void
	 */
	public function ajax_load_cc_page() {
		self::begin_ajax_json_buffer();

		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'You do not have permission to view this page.', 'edminboost-admin-customization' ),
				),
				403
			);
		}

		if ( ! check_ajax_referer( 'edminboost_cc_nav', 'nonce', false ) ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'Security check failed. Refresh the page and try again.', 'edminboost-admin-customization' ),
				),
				403
			);
		}

		$page = isset( $_POST['page'] ) ? sanitize_key( wp_unslash( $_POST['page'] ) ) : '';
		$use_form_defaults = ! empty( $_POST['form_defaults'] );

		if ( ! $this->is_valid_cc_nav_page( $page ) ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'Unknown Command Center page.', 'edminboost-admin-customization' ),
				),
				400
			);
		}

		$html = $this->capture_cc_page_html( $page, $use_form_defaults );

		if ( '' === $html ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'Could not load that page.', 'edminboost-admin-customization' ),
				),
				500
			);
		}

		$page_title = $this->get_cc_page_title( $page );

		$response = array(
			'html'          => $html,
			'page'          => $page,
			'title'         => $page_title,
			'documentTitle' => sprintf(
				/* translators: 1: page title, 2: site name */
				__( '%1$s ‹ %2$s — WordPress', 'edminboost-admin-customization' ),
				$page_title,
				get_bloginfo( 'name' )
			),
			'ccNavNonce' => wp_create_nonce( 'edminboost_cc_nav' ),
		);

		/**
		 * Filter CC tab navigation AJAX payload (premium adds drawer preview nonces when licensed).
		 *
		 * @param array  $response AJAX success payload.
		 * @param string $page     Loaded admin page slug.
		 */
		$response = apply_filters( 'edminboost_cc_nav_ajax_response', $response, $page );

		self::send_ajax_json_success( $response );
	}

	/**
	 * Whether a page slug is a Command Center navigation target.
	 *
	 * @param string $page Admin page slug.
	 * @return bool
	 */
	private function is_valid_cc_nav_page( $page ) {
		foreach ( EDMINBOOST_Command_Center::get_nav_items() as $item ) {
			if ( isset( $item['slug'] ) && $page === $item['slug'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Render a Command Center page and return its HTML.
	 *
	 * @param string $page              Admin page slug.
	 * @param bool   $use_form_defaults When true, render forms with plugin defaults (not saved values).
	 * @return string
	 */
	private function capture_cc_page_html( $page, $use_form_defaults = false ) {
		$form_defaults_filter = null;

		if ( $use_form_defaults ) {
			$form_defaults_filter = static function () {
				return EDMINBOOST_Settings::get_form_defaults();
			};
			add_filter( 'edminboost_settings', $form_defaults_filter, 9999 );
		}

		$this->prime_cc_page_context( $page );

		ob_start();

		$rendered = apply_filters( 'edminboost_render_cc_page', false, $page, $this );
		if ( $rendered ) {
			return (string) ob_get_clean();
		}

		switch ( $page ) {
			case self::PAGE_SLUG:
				$this->render_admin_page();
				break;
			case self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_APPEARANCE:
				$this->render_appearance_page();
				break;
			case self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER:
				$this->render_mapper_page();
				break;
			case self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRESETS:
				$this->render_presets_page();
				break;
			case self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MENU_STUDIO:
				$this->render_menu_studio_page();
				break;
			case self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRODUCTIVITY:
				$this->render_productivity_page();
				break;
			case self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_SECURITY:
				$this->render_security_page();
				break;
			case self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PERFORMANCE:
				$this->render_performance_page();
				break;
			default:
				ob_end_clean();
				if ( $form_defaults_filter ) {
					remove_filter( 'edminboost_settings', $form_defaults_filter, 9999 );
				}
				return '';
		}

		$html = (string) ob_get_clean();

		if ( $form_defaults_filter ) {
			remove_filter( 'edminboost_settings', $form_defaults_filter, 9999 );
		}

		return $html;
	}

	/**
	 * Prime globals used by admin page partials during AJAX renders.
	 *
	 * @param string $page Admin page slug.
	 * @return void
	 */
	private function prime_cc_page_context( $page ) {
		global $plugin_page, $title;

		$plugin_page = $page;
		$title       = $this->get_cc_page_title( $page );

		EDMINBOOST_Command_Center::ensure_discovery_menu_snapshot();
	}

	/**
	 * Human-readable title for a Command Center page slug.
	 *
	 * @param string $page Admin page slug.
	 * @return string
	 */
	private function get_cc_page_title( $page ) {
		/**
		 * Filter the Command Center page title for extension screens.
		 *
		 * @param string $title Page title.
		 * @param string $page  Admin page slug.
		 */
		$filtered = apply_filters( 'edminboost_cc_page_title', '', $page );
		if ( '' !== $filtered ) {
			return $filtered;
		}

		$titles = array(
			self::PAGE_SLUG                                              => __( 'Dashboard', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_APPEARANCE => __( 'Theme', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER     => __( 'Top Bar', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRESETS    => __( 'Layouts', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MENU_STUDIO => __( 'Menu Studio', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRODUCTIVITY => __( 'Productivity', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_SECURITY     => __( 'Security', 'edminboost-admin-customization' ),
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PERFORMANCE  => __( 'Performance', 'edminboost-admin-customization' ),
		);

		return isset( $titles[ $page ] ) ? $titles[ $page ] : '';
	}

	/**
	 * AJAX: save Command Center settings without a full page reload.
	 *
	 * @return void
	 */
	public function ajax_save_settings() {
		self::begin_ajax_json_buffer();

		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'You do not have permission to save these settings.', 'edminboost-admin-customization' ),
				),
				403
			);
		}

		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, EDMINBOOST_Settings::SETTINGS_GROUP . '-options' ) ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'Security check failed. Refresh the page and try again.', 'edminboost-admin-customization' ),
				),
				403
			);
		}

		$raw = isset( $_POST[ EDMINBOOST_Settings::OPTION_NAME ] )
			? wp_unslash( $_POST[ EDMINBOOST_Settings::OPTION_NAME ] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via EDMINBOOST_Settings::sanitize().
			: array();

		if ( ! is_array( $raw ) ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'Invalid settings payload.', 'edminboost-admin-customization' ),
				),
				400
			);
		}

		$before_cc = EDMINBOOST_Command_Center::get_settings();
		$before_custom_preset_ids = isset( $before_cc['presets'] ) && is_array( $before_cc['presets'] )
			? array_keys( $before_cc['presets'] )
			: array();

		$sanitized = EDMINBOOST_Settings::sanitize( $raw );
		update_option( EDMINBOOST_Settings::OPTION_NAME, $sanitized, false );

		$cc_raw = isset( $raw['command_center'] ) && is_array( $raw['command_center'] )
			? $raw['command_center']
			: array();

		EDMINBOOST_Command_Center::ensure_discovery_menu_snapshot();

		self::send_ajax_json_success(
			$this->build_ajax_save_response( $sanitized, $cc_raw, $before_custom_preset_ids )
		);
	}

	/**
	 * Build the AJAX save success payload for admin JavaScript.
	 *
	 * @param array $sanitized               Sanitized settings after save.
	 * @param array $cc_raw                  Raw command_center input.
	 * @param array $before_custom_preset_ids Custom preset ids before save.
	 * @return array
	 */
	private function build_ajax_save_response( $sanitized, $cc_raw, $before_custom_preset_ids ) {
		$cc = isset( $sanitized['command_center'] ) && is_array( $sanitized['command_center'] )
			? $sanitized['command_center']
			: array();

		$after_custom = isset( $cc['presets'] ) && is_array( $cc['presets'] )
			? array_keys( $cc['presets'] )
			: array();
		$new_custom_ids = array_values( array_diff( $after_custom, $before_custom_preset_ids ) );

		$selected_preset = '';
		$message         = __( 'Settings saved.', 'edminboost-admin-customization' );

		if ( ! empty( $cc_raw['_setup_wizard_save'] ) ) {
			$message = __( 'Command Center launched.', 'edminboost-admin-customization' );
		} elseif ( ! empty( $cc_raw['_apply_preset'] ) ) {
			$selected_preset = sanitize_key( $cc_raw['_apply_preset'] );
			$message         = __( 'Preset applied.', 'edminboost-admin-customization' );
		} elseif ( ! empty( $cc_raw['_duplicate_preset'] ) ) {
			$selected_preset = ! empty( $new_custom_ids ) ? (string) reset( $new_custom_ids ) : '';
			$message         = __( 'Preset duplicated.', 'edminboost-admin-customization' );
		} elseif (
			! empty( $cc_raw['_save_custom_preset'] )
			&& is_array( $cc_raw['_save_custom_preset'] )
			&& ! empty( $cc_raw['_save_custom_preset']['name'] )
		) {
			$selected_preset = ! empty( $new_custom_ids ) ? (string) reset( $new_custom_ids ) : '';
			$message         = __( 'Preset saved.', 'edminboost-admin-customization' );
		} elseif (
			! empty( $cc_raw['_rename_custom_preset'] )
			&& is_array( $cc_raw['_rename_custom_preset'] )
			&& ! empty( $cc_raw['_rename_custom_preset']['id'] )
			&& ! empty( $cc_raw['_rename_custom_preset']['name'] )
		) {
			$selected_preset = sanitize_key( $cc_raw['_rename_custom_preset']['id'] );
			$message         = __( 'Preset renamed.', 'edminboost-admin-customization' );
		}

		$response = array(
			'message'              => $message,
			'presets'              => $this->get_presets_for_js(),
			'selected_preset'      => $selected_preset,
			'active_layout_preset' => EDMINBOOST_Command_Center::detect_active_layout_preset( $cc ),
			'default_preset'       => isset( $cc['default_preset'] ) ? (string) $cc['default_preset'] : '',
			'theme'                => EDMINBOOST_Theme::get_settings( $cc ),
			'setup_complete'       => EDMINBOOST_Command_Center::is_setup_complete( $cc ),
		);

		/**
		 * Filter the AJAX settings save success payload for admin JavaScript.
		 *
		 * @param array $response Success payload.
		 * @param array $cc_raw   Raw command_center input from the save request.
		 */
		return apply_filters( 'edminboost_ajax_save_response', $response, $cc_raw );
	}

	/**
	 * Determine whether the current screen belongs to this plugin.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return bool
	 */
	private function is_plugin_screen( $hook_suffix ) {
		return false !== strpos( $hook_suffix, self::PAGE_SLUG );
	}

	/**
	 * AJAX: export plugin settings as JSON.
	 *
	 * @return void
	 */
	public function ajax_export_settings() {
		self::begin_ajax_json_buffer();

		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			self::send_ajax_json_error( array( 'message' => __( 'Permission denied.', 'edminboost-admin-customization' ) ), 403 );
		}

		if ( ! check_ajax_referer( 'edminboost_export_settings', 'nonce', false ) ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'Security check failed. Refresh the page and try again.', 'edminboost-admin-customization' ),
				),
				403
			);
		}

		self::send_ajax_json_success(
			array(
				'json' => wp_json_encode( EDMINBOOST_Settings::get(), JSON_PRETTY_PRINT ),
			)
		);
	}

	/**
	 * AJAX: import plugin settings from JSON.
	 *
	 * @return void
	 */
	public function ajax_import_settings() {
		self::begin_ajax_json_buffer();

		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			self::send_ajax_json_error( array( 'message' => __( 'Permission denied.', 'edminboost-admin-customization' ) ), 403 );
		}

		if ( ! check_ajax_referer( 'edminboost_import_settings', 'nonce', false ) ) {
			self::send_ajax_json_error(
				array(
					'message' => __( 'Security check failed. Refresh the page and try again.', 'edminboost-admin-customization' ),
				),
				403
			);
		}

		$json = isset( $_POST['json'] ) ? sanitize_textarea_field( wp_unslash( $_POST['json'] ) ) : '';
		$data = json_decode( $json, true );

		if ( ! is_array( $data ) ) {
			self::send_ajax_json_error( array( 'message' => __( 'Invalid JSON payload.', 'edminboost-admin-customization' ) ), 400 );
		}

		$sanitized = EDMINBOOST_Settings::sanitize( $data );
		update_option( EDMINBOOST_Settings::OPTION_NAME, $sanitized, false );

		self::send_ajax_json_success( array( 'message' => __( 'Settings imported.', 'edminboost-admin-customization' ) ) );
	}

	/**
	 * Add a body class on plugin admin screens for layout scoping.
	 *
	 * @param string $classes Space-separated admin body classes.
	 * @return string
	 */
	public function filter_admin_body_class( $classes ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || empty( $screen->id ) || false === strpos( $screen->id, self::PAGE_SLUG ) ) {
			return $classes;
		}

		return trim( $classes . ' edminboost-plugin-screen' );
	}
}
