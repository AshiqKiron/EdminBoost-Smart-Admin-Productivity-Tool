<?php
/**
 * Plugin bootstrap — defines constants, loads dependencies, registers lifecycle hooks.
 *
 * Purpose: Single entry point. No business logic here; delegates to EDMINBOOST_Plugin.
 *
 * Plugin Name: EdminBoost - Smart Admin Productivity Tool
 * Plugin URI: https://asphaltthemes.com/edminboost
 * Description: Boost WordPress admin productivity with smart tools to simplify workflows, customize the dashboard, and streamline daily admin tasks.
 * Version: 1.4.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: ashiquzzaman
 * Author URI: https://asphaltthemes.com
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: edminboost-smart-admin-productivity-tool
 * Domain Path: /languages
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EDMINBOOST_VERSION', '1.4.0' );
define( 'EDMINBOOST_PLUGIN_FILE', __FILE__ );
define( 'EDMINBOOST_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EDMINBOOST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EDMINBOOST_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'EDMINBOOST_PLUGIN_SLUG', 'edminboost-smart-admin-productivity-tool' );
define( 'EDMINBOOST_TEXT_DOMAIN', EDMINBOOST_PLUGIN_SLUG );

require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-activator.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-deactivator.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-i18n.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-settings.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-feature-settings.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-command-center.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-theme.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-command-center-bar.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-menu-studio.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-white-label.php';
require_once EDMINBOOST_PLUGIN_DIR . 'includes/class-edminboost-features.php';
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
