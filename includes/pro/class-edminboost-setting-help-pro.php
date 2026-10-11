<?php
/**
 * Licensed-only setting help tooltips (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Merges Pro-only tooltip copy when licensed admin UI is active.
 */
class EDMINBOOST_Setting_Help_Pro {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'edminboost_setting_help_tooltips', array( __CLASS__, 'merge_licensed_tooltips' ) );
	}

	/**
	 * Append licensed tooltip strings.
	 *
	 * @param array<string, string> $tooltips Base tooltips from core.
	 * @return array<string, string>
	 */
	public static function merge_licensed_tooltips( $tooltips ) {
		if ( ! EDMINBOOST_Pro::has_licensed_admin_ui() ) {
			return $tooltips;
		}

		return array_merge( $tooltips, self::get_licensed_tooltips() );
	}

	/**
	 * Tooltip registry for licensed-only settings UI.
	 *
	 * @return array<string, string>
	 */
	protected static function get_licensed_tooltips() {
		return array(
			// Appearance — panel & badges.
			'drawer_width'               => __( 'Default width of the AJAX slide-out drawer when a top bar link opens in-panel instead of navigating away.', 'edminboost-admin-customization' ),
			'drawer_width_custom'        => __( 'Pixel width for the drawer when Custom is selected. Clamped between 400 and 800.', 'edminboost-admin-customization' ),
			'animation_speed'            => __( 'Transition speed when opening and closing the slide-out drawer.', 'edminboost-admin-customization' ),
			'glassmorphism'              => __( 'Adds a frosted-glass blur effect to the drawer backdrop.', 'edminboost-admin-customization' ),
			'autosave_interval'          => __( 'How often forms inside the slide-out drawer auto-save, in seconds (10–600).', 'edminboost-admin-customization' ),
			'badge_refresh_rate'         => __( 'How often live notification badges on the Command Center bar refresh, in seconds (15–600).', 'edminboost-admin-customization' ),
			'badge_style'                => __( 'Visual style for live counters bound to WooCommerce orders, comments, updates, and similar sources.', 'edminboost-admin-customization' ),

			// Dashboard setup wizard (licensed top bar features).
			'setup_wizard_topbar'        => __( 'Read-only list of admin shortcuts from your layout preset. Use the full Top Bar editor to reorder links, change icons, set drawer interactions, or bind live badges.', 'edminboost-admin-customization' ),
			'setup_review_topbar'        => __( 'Top bar shortcuts included with the selected layout preset. Fine-tune labels, icons, and interactions in the Top Bar editor.', 'edminboost-admin-customization' ),

			// Top bar mapper.
			'topbar_canvas'              => __( 'Drag to reorder icons. Click an item to configure icon, label, interaction, and badge binding.', 'edminboost-admin-customization' ),
			'item_interaction'           => __( 'Open the admin page directly or load it inside the AJAX slide-out drawer.', 'edminboost-admin-customization' ),
			'item_badge_source'          => __( 'Optional live counter from local WordPress data (orders, comments, updates, etc.).', 'edminboost-admin-customization' ),

			// Layout presets — role visibility matrix.
			'role_visibility'            => __( 'Hide specific admin menu items from selected roles. Checked items remain visible in the top bar and sidebar for that role, including individual submenu pages. Items outside the assigned preset start unchecked but can still be enabled. Items the role cannot access by default also start unchecked—you may enable them manually.', 'edminboost-admin-customization' ),

			// Menu Studio (licensed panels).
			'menu_display_mode'          => __( 'Show icons, text, or both on sidebar menu items.', 'edminboost-admin-customization' ),
			'custom_menu_path'           => __( 'Relative wp-admin path for a custom sidebar link.', 'edminboost-admin-customization' ),
			'custom_menu_label'          => __( 'Label shown in the wp-admin sidebar for the custom link.', 'edminboost-admin-customization' ),
			'custom_menu_parent'         => __( 'Nest the link under an existing top-level menu, or leave as top level.', 'edminboost-admin-customization' ),

			// Theme scheduled dark mode (licensed UI).
			'theme_schedule_dark_mode'   => __( 'When color mode is Auto, switch to dark styling during the configured time window.', 'edminboost-admin-customization' ),
			'theme_dark_mode_start'      => __( 'Local site time when scheduled dark mode begins (24-hour HH:MM).', 'edminboost-admin-customization' ),
			'theme_dark_mode_end'        => __( 'Local site time when scheduled dark mode ends (24-hour HH:MM).', 'edminboost-admin-customization' ),
			'theme_extras'               => __( 'Optional admin typography, background, favicon, post list status colors, and scheduled dark mode window.', 'edminboost-admin-customization' ),

			// Security — login redirects.
			'login_redirects_enabled'    => __( 'Send users to custom URLs after login or logout based on their role.', 'edminboost-admin-customization' ),
			'default_login_redirect'     => __( 'Fallback URL after login when no role-specific URL is set.', 'edminboost-admin-customization' ),
			'default_logout_redirect'    => __( 'Fallback URL after logout when no role-specific URL is set.', 'edminboost-admin-customization' ),
			'role_login_redirect'        => __( 'Login redirect URL for this user role. Leave empty to use the default.', 'edminboost-admin-customization' ),
			'role_logout_redirect'       => __( 'Logout redirect URL for this user role. Leave empty to use the default.', 'edminboost-admin-customization' ),

			// White label.
			'wl_enabled'                 => __( 'Master switch for white-label branding in wp-admin: sidebar menu label, Plugins screen row, and admin footer options below.', 'edminboost-admin-customization' ),
			'wl_hide_credit'             => __( 'Removes the default WordPress version credit from the admin footer.', 'edminboost-admin-customization' ),
			'wl_show_ip'                 => __( 'Include the visitor IP address in the system status footer.', 'edminboost-admin-customization' ),
			'wl_show_php_version'        => __( 'Include the PHP version in the system status footer.', 'edminboost-admin-customization' ),
			'wl_show_wp_version'         => __( 'Include the WordPress version in the system status footer.', 'edminboost-admin-customization' ),
			'wl_show_memory_usage'       => __( 'Include current PHP memory usage in the system status footer.', 'edminboost-admin-customization' ),
			'wl_show_memory_limit'       => __( 'Include the PHP memory limit in the system status footer.', 'edminboost-admin-customization' ),
			'wl_show_memory_available'   => __( 'Include estimated available memory in the system status footer.', 'edminboost-admin-customization' ),
			'wl_plugin_name'             => __( 'Replaces the plugin name on the Plugins screen and in plugin row meta.', 'edminboost-admin-customization' ),
			'wl_plugin_description'      => __( 'Replaces the plugin description shown on the Plugins screen.', 'edminboost-admin-customization' ),
			'wl_plugin_author'           => __( 'Replaces the author name shown for this plugin.', 'edminboost-admin-customization' ),
			'wl_plugin_uri'              => __( 'Replaces the plugin website link on the Plugins screen.', 'edminboost-admin-customization' ),
			'wl_menu_label'              => __( 'Replaces the EdminBoost item label in the wp-admin sidebar menu.', 'edminboost-admin-customization' ),

			// Settings backup/import.
			'export_settings'            => __( 'Download all EdminBoost options as JSON for backup or migration. Media files referenced by attachment IDs are not included.', 'edminboost-admin-customization' ),
			'import_settings'            => __( 'Restore settings from exported JSON by pasting the payload or uploading a .json file. Existing values are replaced after validation.', 'edminboost-admin-customization' ),
		);
	}
}
