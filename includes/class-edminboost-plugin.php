<?php
/**
 * Core plugin orchestrator — wires all hooks in one place.
 *
 * Purpose: Instantiate dependencies and register WordPress actions/filters.
 *
 * Architecture map
 * -----------------
 * Admin pages:
 *   - edminboost-smart-admin-productivity-tool (Dashboard)
 *
 * Settings (wp_options):
 *   - edminboost_settings  (array, Settings API group: edminboost_settings_group)
 *   - edminboost_version   (string, schema version tracker)
 *
 * Core hooks (this class):
 *   - plugins_loaded              → load text domain
 *   - admin_menu                  → register admin pages
 *   - admin_init                  → register settings + feature hooks
 *   - admin_enqueue_scripts (×2)  → enqueue CSS/JS on plugin screens
 *   - plugin_action_links_{basename} → Settings shortcut on Plugins screen
 *   - plugin_row_meta                  → Docs link on Plugins screen
 *
 * Feature hooks (registered by EDMINBOOST_Features when enabled):
 *   - hide_admin_notices → admin_enqueue_scripts
 *   - dashboard_widgets  → wp_dashboard_setup, admin_init
 *   - admin_footer       → admin_footer_text
 *   - disable_emojis     → admin_init
 *   - admin_bar          → admin_bar_menu (priority 999)
 *   - command_center_bar → admin_bar_menu (priority 80), wp_enqueue_scripts
 *
 * REST endpoints:  none (not required; all features use core hooks)
 * Cron events:     none (no scheduled tasks)
 * Blocks:          none (admin-only plugin)
 * Custom tables:   none (options API only)
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class EDMINBOOST_Plugin {

	/**
	 * Admin handler.
	 *
	 * @var EDMINBOOST_Admin
	 */
	protected $admin;

	/**
	 * Internationalization handler.
	 *
	 * @var EDMINBOOST_I18n
	 */
	protected $i18n;

	/**
	 * Feature manager.
	 *
	 * @var EDMINBOOST_Features
	 */
	protected $features;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->i18n     = new EDMINBOOST_I18n();
		$this->features = new EDMINBOOST_Features();
		$this->admin    = new EDMINBOOST_Admin( $this->features );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'plugins_loaded', array( $this->i18n, 'load_plugin_textdomain' ) );
		add_action( 'admin_menu', array( $this->admin, 'register_menu' ) );
		add_action( 'admin_menu', array( $this->admin, 'normalize_plugin_submenu' ), 999 );
		add_action( 'admin_init', array( $this->admin, 'register_settings' ) );
		add_action( 'admin_init', array( $this->admin, 'maybe_activation_redirect' ) );
		add_action( 'wp_ajax_edminboost_save_settings', array( $this->admin, 'ajax_save_settings' ) );
		add_action( 'wp_ajax_edminboost_load_cc_page', array( $this->admin, 'ajax_load_cc_page' ) );
		add_action( 'wp_ajax_edminboost_export_settings', array( $this->admin, 'ajax_export_settings' ) );
		add_action( 'wp_ajax_edminboost_import_settings', array( $this->admin, 'ajax_import_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_scripts' ) );
		add_filter( 'admin_body_class', array( $this->admin, 'filter_admin_body_class' ) );
		add_filter( 'plugin_action_links_' . EDMINBOOST_PLUGIN_BASENAME, array( $this->admin, 'add_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this->admin, 'add_plugin_row_meta' ), 10, 2 );
		add_action( 'init', array( $this->features, 'register_hooks' ) );
		EDMINBOOST_Command_Center_Bar::register_hooks();
		EDMINBOOST_Command_Center::register_hooks();
		EDMINBOOST_Menu_Studio::register_hooks();
		EDMINBOOST_Theme::register_hooks();
		EDMINBOOST_White_Label::register_hooks();
	}
}
