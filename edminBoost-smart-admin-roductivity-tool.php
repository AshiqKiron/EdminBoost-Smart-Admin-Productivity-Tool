<?php
/**
 * Plugin Name: Edmin - Boost Smart Admin Productivity Tool
 * Plugin URI: https://asphaltthemes.com/edminboost
 * Description: Boost WordPress admin productivity with smart tools to simplify workflows, customize the dashboard, and streamline daily admin tasks.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: ashiquzzaman
 * Author URI: https://asphaltthemes.com
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: edminBoost-smart-admin-roductivity-tool
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EDMINBOOST_VERSION', '1.0.0' );
define( 'EDMINBOOST_PLUGIN_FILE', __FILE__ );
define( 'EDMINBOOST_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EDMINBOOST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EDMINBOOST_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'EDMINBOOST_TEXT_DOMAIN', 'edminBoost-smart-admin-roductivity-tool' );

require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-activator.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-deactivator.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-i18n.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-admin.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-plugin.php';

register_activation_hook( __FILE__, array( 'EDMINBOOST_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'EDMINBOOST_Deactivator', 'deactivate' ) );

/**
 * Initialize and run the plugin.
 *
 * @return EDMINBOOST_Plugin
 */
function edminboost_run_plugin() {
	static $plugin = null;

	if ( null === $plugin ) {
		$plugin = new EDMINBOOST_Plugin();
		$plugin->run();
	}

	return $plugin;
}

edminboost_run_plugin();
