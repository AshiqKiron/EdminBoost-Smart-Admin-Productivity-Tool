<?php
/**
 * Core plugin bootstrap.
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
	 * Constructor.
	 */
	public function __construct() {
		$this->i18n  = new EDMINBOOST_I18n();
		$this->admin = new EDMINBOOST_Admin();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'plugins_loaded', array( $this->i18n, 'load_plugin_textdomain' ) );
		add_action( 'admin_menu', array( $this->admin, 'register_menu' ) );
		add_action( 'admin_init', array( $this->admin, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_scripts' ) );
		add_filter( 'plugin_action_links_' . EDMINBOOST_PLUGIN_BASENAME, array( $this->admin, 'add_settings_link' ) );
	}
}
