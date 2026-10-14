<?php
/**
 * Seed rich EdminBoost settings for WordPress.org screenshot capture (local QA only).
 *
 * Usage: wp eval-file bin/seed-wporg-screenshot-demo.php --path=/path/to/wordpress
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EDMINBOOST_Settings' ) ) {
	WP_CLI::error( 'EdminBoost is not loaded. Activate the plugin first.' );
}

$admin_users = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
		'fields' => 'ID',
	)
);
if ( ! empty( $admin_users ) ) {
	wp_set_current_user( (int) $admin_users[0] );
}

/**
 * Reset onboarding so the setup wizard can be captured.
 *
 * @return void
 */
function edminboost_wporg_screenshots_reset_wizard() {
	$settings = EDMINBOOST_Settings::get();
	$settings['enabled']                      = true;
	$settings['command_center']['onboarding_completed'] = false;
	$settings['command_center']['top_bar_items']          = array();

	update_option( EDMINBOOST_Settings::OPTION_NAME, EDMINBOOST_Settings::sanitize( $settings ) );

	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::success( 'Wizard state seeded (onboarding incomplete).' );
	}
}

/**
 * Apply demo Command Center + feature settings for post-wizard screenshots.
 *
 * @return void
 */
function edminboost_wporg_screenshots_seed_demo() {
	if ( ! EDMINBOOST_Command_Center::apply_preset( 'system_client', true ) ) {
		EDMINBOOST_Command_Center::apply_preset( 'default', true );
	}

	$settings = EDMINBOOST_Settings::get();
	$settings['enabled'] = true;

	$cc = isset( $settings['command_center'] ) && is_array( $settings['command_center'] )
		? $settings['command_center']
		: EDMINBOOST_Command_Center::get_defaults();

	$cc['onboarding_completed'] = true;
	$cc['default_preset']       = 'system_client';
	$cc['persona']              = 'client';

	$cc['theme'] = wp_parse_args(
		array(
			'preset'    => 'ocean',
			'mode'      => 'light',
			'font'      => 'inherit',
			'font_size' => 14,
		),
		isset( $cc['theme'] ) && is_array( $cc['theme'] ) ? $cc['theme'] : EDMINBOOST_Theme::get_defaults()
	);

	$cc['behavior'] = wp_parse_args(
		array(
			'hide_howdy'         => true,
			'hide_wp_logo'       => false,
			'hide_update_counters' => false,
		),
		isset( $cc['behavior'] ) && is_array( $cc['behavior'] ) ? $cc['behavior'] : array()
	);

	$menu_defaults = EDMINBOOST_Command_Center::get_menu_studio_defaults();
	$cc['menu_studio'] = wp_parse_args(
		array(
			'enabled'    => true,
			'use_colors' => true,
			'menu_width' => 200,
			'font_size'  => 14,
		),
		isset( $cc['menu_studio'] ) && is_array( $cc['menu_studio'] ) ? $cc['menu_studio'] : $menu_defaults
	);

	$settings['command_center'] = $cc;

	$features = wp_parse_args(
		$settings['features'] ?? array(),
		EDMINBOOST_Feature_Settings::get_defaults()
	);

	$features['hide_admin_notices'] = true;
	$features['hide_screen_help']   = true;
	$features['dashboard_widgets']  = array(
		'enabled'              => true,
		'remove_welcome_panel' => true,
		'remove_quick_press'   => true,
		'remove_activity'      => true,
		'remove_at_a_glance'   => false,
		'remove_site_health'   => false,
		'remove_wp_news'       => true,
	);
	$features['admin_footer'] = array(
		'enabled' => true,
		'text'    => 'Crafted with EdminBoost — your admin, your way.',
	);
	$features['disable_emojis'] = array(
		'enabled' => true,
		'scope'   => 'both',
	);
	$features['disable_xmlrpc']            = true;
	$features['disable_feeds']             = true;
	$features['remove_asset_versions']     = true;
	$features['remove_dashicons_frontend'] = true;
	$features['disable_embeds']            = true;
	$features['post_duplicator']           = array(
		'enabled'    => true,
		'post_types' => array( 'post', 'page' ),
	);
	$features['heartbeat_control'] = array(
		'admin'    => 'slow',
		'editor'   => 'default',
		'frontend' => 'disable',
	);

	$settings['features'] = $features;

	update_option( EDMINBOOST_Settings::OPTION_NAME, EDMINBOOST_Settings::sanitize( $settings ) );

	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::success( 'Demo settings seeded for WordPress.org screenshots.' );
	}
}

$mode = getenv( 'EDMINBOOST_SCREENSHOT_SEED' );
if ( 'wizard' === $mode ) {
	edminboost_wporg_screenshots_reset_wizard();
} else {
	edminboost_wporg_screenshots_seed_demo();
}
