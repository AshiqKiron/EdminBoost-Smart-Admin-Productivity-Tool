<?php
/**
 * Setting field help tooltips for admin UI.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders info icons with accessible tooltips on Command Center and feature pages,
 * and on the Dashboard first-run setup wizard.
 */
class EDMINBOOST_Setting_Help {

	/**
	 * Counter for unique tooltip element IDs.
	 *
	 * @var int
	 */
	protected static $instance = 0;

	/**
	 * Whether info icons should render on the current request.
	 *
	 * @return bool
	 */
	public static function should_show() {
		global $plugin_page;

		$page = ! empty( $plugin_page ) ? sanitize_key( $plugin_page ) : '';

		if ( '' === $page && isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if ( EDMINBOOST_Admin::PAGE_SLUG === $page ) {
			return ! EDMINBOOST_Command_Center::is_setup_complete();
		}

		return true;
	}

	/**
	 * Return tooltip copy for a setting key.
	 *
	 * @param string $key Setting identifier.
	 * @return string
	 */
	public static function get_text( $key ) {
		$tooltips = self::get_tooltips();

		return isset( $tooltips[ $key ] ) ? $tooltips[ $key ] : '';
	}

	/**
	 * Build info icon markup for a setting.
	 *
	 * @param string $key  Setting identifier.
	 * @param string $text Optional override tooltip text.
	 * @return string
	 */
	public static function render( $key, $text = '' ) {
		if ( ! self::should_show() ) {
			return '';
		}

		$text = '' !== $text ? $text : self::get_text( $key );

		if ( '' === $text ) {
			return '';
		}

		++self::$instance;
		$tooltip_id = 'edminboost-setting-info-' . self::$instance;

		$aria_label = sprintf(
			/* translators: %s: help tooltip text */
			__( 'More information: %s', 'edminboost' ),
			wp_strip_all_tags( $text )
		);

		return sprintf(
			'<span class="edminboost-setting-info"><button type="button" class="edminboost-setting-info__trigger" aria-label="%1$s" aria-describedby="%2$s"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span></button><span role="tooltip" id="%2$s" class="edminboost-setting-info__tooltip">%3$s</span></span>',
			esc_attr( $aria_label ),
			esc_attr( $tooltip_id ),
			esc_html( $text )
		);
	}

	/**
	 * Echo info icon markup for a setting.
	 *
	 * @param string $key  Setting identifier.
	 * @param string $text Optional override tooltip text.
	 * @return void
	 */
	public static function echo_icon( $key, $text = '' ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render() escapes output.
		echo self::render( $key, $text );
	}

	/**
	 * Tooltip registry keyed by setting identifier.
	 *
	 * @return array<string, string>
	 */
	protected static function get_tooltips() {
		return array(
			// Theme settings.
			'theme_preset'               => __( 'Choose a color palette for wp-admin, the Command Center bar, slide-out drawer, and EdminBoost screens.', 'edminboost' ),
			'theme_mode'                 => __( 'Force light or dark styling, follow the system preference, or use scheduled dark mode when enabled below.', 'edminboost' ),
			'theme_font'                 => __( 'System font stack applied to EdminBoost UI and themed wp-admin chrome. No remote fonts are loaded.', 'edminboost' ),
			'theme_custom_colors'        => __( 'Override the five core theme tokens when the Custom preset is selected.', 'edminboost' ),
			'theme_custom_accent'        => __( 'Primary accent used for buttons, highlights, and active states.', 'edminboost' ),
			'theme_custom_surface'       => __( 'Background surface color for cards, panels, and content areas.', 'edminboost' ),
			'theme_custom_text'          => __( 'Default text color on themed surfaces.', 'edminboost' ),
			'theme_custom_topbar'        => __( 'Background color for the WordPress admin bar and Command Center bar.', 'edminboost' ),
			'theme_custom_sidebar'       => __( 'Background color for the wp-admin sidebar menu.', 'edminboost' ),
			'theme_custom_content'       => __( 'Background color for main admin content areas.', 'edminboost' ),
			'theme_admin_favicon'        => __( 'Media library attachment ID used as the favicon on wp-admin screens.', 'edminboost' ),
			'theme_font_size'            => __( 'Base font size in pixels for themed admin UI. Allowed range: 12–20.', 'edminboost' ),
			'theme_admin_bg_color'       => __( 'Optional hex background color behind wp-admin content.', 'edminboost' ),
			'theme_admin_bg_image'       => __( 'Media library attachment ID for an optional admin background image.', 'edminboost' ),
			'theme_schedule_dark_mode'   => __( 'When color mode is Auto, switch to dark styling during the configured time window.', 'edminboost' ),
			'theme_dark_mode_start'      => __( 'Local site time when scheduled dark mode begins (24-hour HH:MM).', 'edminboost' ),
			'theme_dark_mode_end'        => __( 'Local site time when scheduled dark mode ends (24-hour HH:MM).', 'edminboost' ),
			'theme_status_colors'        => __( 'Optional hex colors for post list table rows by status (draft, pending, etc.).', 'edminboost' ),

			// Appearance — panel & badges.
			'drawer_width'               => __( 'Default width of the AJAX slide-out drawer when a top bar link opens in-panel instead of navigating away.', 'edminboost' ),
			'drawer_width_custom'        => __( 'Pixel width for the drawer when Custom is selected. Clamped between 400 and 800.', 'edminboost' ),
			'animation_speed'            => __( 'Transition speed when opening and closing the slide-out drawer.', 'edminboost' ),
			'glassmorphism'              => __( 'Adds a frosted-glass blur effect to the drawer backdrop.', 'edminboost' ),
			'autosave_interval'          => __( 'How often forms inside the slide-out drawer auto-save, in seconds (10–600).', 'edminboost' ),
			'badge_refresh_rate'         => __( 'How often live notification badges on the Command Center bar refresh, in seconds (15–600).', 'edminboost' ),
			'badge_style'                => __( 'Visual style for live counters bound to WooCommerce orders, comments, updates, and similar sources.', 'edminboost' ),

			// Appearance — admin bar cleanup.
			'hide_wp_logo'               => __( 'Remove the WordPress logo and its submenu from the native admin bar.', 'edminboost' ),
			'hide_update_counters'       => __( 'Hide plugin, theme, and core update notification bubbles on the admin bar.', 'edminboost' ),
			'hide_howdy'                 => __( 'Hide the “Howdy” greeting text in the admin bar profile area.', 'edminboost' ),
			'hide_comments'              => __( 'Remove the comments shortcut from the native admin bar.', 'edminboost' ),
			'hide_new_content'           => __( 'Remove the “New” content dropdown from the native admin bar.', 'edminboost' ),
			'hide_customize'             => __( 'Hide the Customize link when the Customizer is available.', 'edminboost' ),

			// Dashboard setup wizard.
			'setup_wizard_topbar'        => __( 'Read-only list of admin shortcuts from your layout preset. Use the full Top Bar editor to reorder links, change icons, set drawer interactions, or bind live badges.', 'edminboost' ),
			'setup_wizard_topbar_editor' => __( 'Opens the Top Bar editor where you can drag discovered admin pages, add custom links, and configure each item before or after setup.', 'edminboost' ),
			'setup_wizard_review'        => __( 'Summary of your layout, theme, sidebar, and top bar choices before saving. Saving applies the preset and completes Dashboard setup.', 'edminboost' ),
			'setup_review_sidebar'       => __( 'Sidebar menu items and visibility included with the selected layout preset. Customize order, colors, and hidden items later in Menu Studio.', 'edminboost' ),
			'setup_review_topbar'        => __( 'Top bar shortcuts included with the selected layout preset. Fine-tune labels, icons, and interactions in the Top Bar editor.', 'edminboost' ),
			'layout_sidebar_preview'     => __( 'Sample of top-level sidebar menu items included with the selected layout preset (not every submenu is shown).', 'edminboost' ),
			'layout_topbar_preview'      => __( 'Sample of Command Center top bar shortcuts included with the selected layout preset.', 'edminboost' ),
			'theme_extras'               => __( 'Optional admin typography, background, favicon, post list status colors, and scheduled dark mode window.', 'edminboost' ),

			// Layout presets.
			'layout_preset'              => __( 'Apply a built-in or saved template that configures the top bar and left sidebar menu. Use Top Bar and Menu Studio for custom granular control.', 'edminboost' ),
			'role_assignments'           => __( 'Assign a layout preset per user role. The first matching role for a logged-in user determines their top bar layout and sidebar menu visibility.', 'edminboost' ),
			'role_visibility'            => __( 'Hide specific admin menu items from selected roles. Checked items remain visible in the top bar and sidebar for that role, including individual submenu pages. Items outside the assigned preset start unchecked but can still be enabled. Items the role cannot access by default also start unchecked—you may enable them manually.', 'edminboost' ),

			// Top bar mapper.
			'discovered_pages'           => __( 'Admin menu pages scanned from your sidebar. Toggle or drag items onto the top bar canvas.', 'edminboost' ),
			'mapper_search'              => __( 'Filter the discovered list by menu or plugin name.', 'edminboost' ),
			'custom_topbar_path'         => __( 'Relative wp-admin path such as edit.php or admin.php?page=my-plugin. You may include a #fragment.', 'edminboost' ),
			'custom_topbar_label'        => __( 'Short label shown on the Command Center bar for the custom link.', 'edminboost' ),
			'custom_topbar_anchor'       => __( 'Optional page fragment to scroll to when the link opens. Can also be appended to the path above.', 'edminboost' ),
			'topbar_canvas'              => __( 'Drag to reorder icons. Click an item to configure icon, label, interaction, and badge binding.', 'edminboost' ),
			'item_icon'                  => __( 'Dashicon displayed on the Command Center bar for this link.', 'edminboost' ),
			'item_label'                 => __( 'Override the default menu label shown on the bar.', 'edminboost' ),
			'item_anchor'                => __( 'Scroll to this section ID when the link opens in redirect or drawer mode.', 'edminboost' ),
			'item_interaction'           => __( 'Open the admin page directly or load it inside the AJAX slide-out drawer.', 'edminboost' ),
			'item_badge_source'          => __( 'Optional live counter from local WordPress data (orders, comments, updates, etc.).', 'edminboost' ),

			// Menu Studio.
			'menu_studio_enabled'        => __( 'Apply sidebar reordering, hidden items, custom links, and styling on all wp-admin screens.', 'edminboost' ),
			'menu_discovered'            => __( 'Toggle visibility or drag admin menu items into the sidebar preview.', 'edminboost' ),
			'menu_search'                => __( 'Filter the admin menu item list.', 'edminboost' ),
			'custom_menu_path'           => __( 'Relative wp-admin path for a custom sidebar link.', 'edminboost' ),
			'custom_menu_label'          => __( 'Label shown in the wp-admin sidebar for the custom link.', 'edminboost' ),
			'custom_menu_parent'         => __( 'Nest the link under an existing top-level menu, or leave as top level.', 'edminboost' ),
			'menu_canvas'                => __( 'Drag top-level items to reorder. Expand a parent to reorder its submenus.', 'edminboost' ),
			'menu_width'                 => __( 'Sidebar width in pixels (120–300).', 'edminboost' ),
			'menu_font_size'             => __( 'Sidebar menu font size in pixels (10–24).', 'edminboost' ),
			'menu_line_height'           => __( 'Line height for sidebar menu items in pixels (12–36).', 'edminboost' ),
			'menu_letter_spacing'        => __( 'Letter spacing for sidebar labels in pixels (-2 to 6).', 'edminboost' ),
			'menu_display_mode'          => __( 'Show icons, text, or both on sidebar menu items.', 'edminboost' ),
			'menu_use_colors'            => __( 'Apply the custom color tokens below to the wp-admin sidebar.', 'edminboost' ),
			'menu_color_parent_bg'       => __( 'Background for top-level sidebar menu items.', 'edminboost' ),
			'menu_color_parent_text'     => __( 'Text color for top-level sidebar menu items.', 'edminboost' ),
			'menu_color_parent_active'   => __( 'Background for hovered or active top-level sidebar items.', 'edminboost' ),
			'menu_color_submenu_bg'      => __( 'Background for fly-out submenu panels.', 'edminboost' ),
			'menu_color_submenu_text'    => __( 'Text color for submenu links.', 'edminboost' ),
			'menu_color_submenu_hover_text' => __( 'Text color for submenu links on hover or focus.', 'edminboost' ),
			'menu_color_notification_bg' => __( 'Background for update count badges on menu items.', 'edminboost' ),
			'menu_color_notification_text' => __( 'Text color for update count badges.', 'edminboost' ),

			// Productivity features.
			'hide_admin_notices'         => __( 'Moves routine success and info notices out of view. Errors and warnings stay visible.', 'edminboost' ),
			'hide_screen_help'           => __( 'Hides the Screen Options and Help tabs in wp-admin.', 'edminboost' ),
			'dashboard_widgets_enabled'  => __( 'Remove selected core Dashboard widgets for all users. Choose which widgets to hide below.', 'edminboost' ),
			'admin_footer_enabled'       => __( 'Replace the default “Thank you for creating with WordPress” footer text in wp-admin.', 'edminboost' ),
			'admin_footer_text'          => __( 'Custom HTML-safe text shown in the admin footer when replacement is enabled.', 'edminboost' ),
			'post_duplicator'            => __( 'Adds a Duplicate row action to posts and pages in list tables.', 'edminboost' ),
			'classic_widgets'            => __( 'Restores the pre-block classic widgets screen under Appearance.', 'edminboost' ),
			'menu_duplicator'            => __( 'Adds a duplicate action when editing navigation menus.', 'edminboost' ),
			'custom_admin_columns'       => __( 'Adds optional columns to the Posts and Pages list tables in wp-admin. Enable the feature, then configure each post type below. New columns appear immediately after the Title column.', 'edminboost' ),
			'column_thumbnail'           => __( 'Shows a 40×40 featured image thumbnail after the Title column. Posts without a featured image leave the cell empty.', 'edminboost' ),
			'column_id'                  => __( 'Shows the numeric post ID after the Title column. Useful for shortcodes, support tickets, or database lookups.', 'edminboost' ),
			'column_meta_key'            => __( 'Shows the stored value for one post meta key after the Title column. Enter the exact meta key name (for example, _price). Only the first value is shown; serialized or array data may not display cleanly.', 'edminboost' ),
			'post_order'                 => __( 'Enables drag-and-drop ordering via an Order column on supported post types.', 'edminboost' ),

			// Security features.
			'security_hardening_note'    => __( 'These options may affect plugins or themes that rely on XML-RPC, feeds, or public REST access.', 'edminboost' ),
			'disable_xmlrpc'             => __( 'Blocks XML-RPC requests. Required by some remote apps and Jetpack features.', 'edminboost' ),
			'disable_feeds'              => __( 'Disables RSS/Atom feeds and redirects feed URLs to the home page.', 'edminboost' ),
			'rest_hide_head'             => __( 'Removes the REST API discovery link tag from HTML output.', 'edminboost' ),
			'rest_disable_guests'        => __( 'Returns an authentication error for unauthenticated REST API requests.', 'edminboost' ),
			'disable_comments'           => __( 'Turns off comments and comment UI for the selected public post types.', 'edminboost' ),
			'login_redirects_enabled'    => __( 'Send users to custom URLs after login or logout based on their role.', 'edminboost' ),
			'default_login_redirect'     => __( 'Fallback URL after login when no role-specific URL is set.', 'edminboost' ),
			'default_logout_redirect'    => __( 'Fallback URL after logout when no role-specific URL is set.', 'edminboost' ),
			'role_login_redirect'        => __( 'Login redirect URL for this user role. Leave empty to use the default.', 'edminboost' ),
			'role_logout_redirect'       => __( 'Logout redirect URL for this user role. Leave empty to use the default.', 'edminboost' ),

			// Performance features.
			'disable_emojis'             => __( 'Removes WordPress emoji detection scripts and related DNS prefetch hints.', 'edminboost' ),
			'disable_emojis_scope'       => __( 'Limit emoji removal to wp-admin, the front end, or both.', 'edminboost' ),
			'remove_asset_versions'      => __( 'Strips ?ver= query strings from enqueued script and style URLs. Can affect cache busting.', 'edminboost' ),
			'remove_dashicons_frontend'  => __( 'Dequeues Dashicons for visitors who are not logged in.', 'edminboost' ),
			'disable_embeds'             => __( 'Disables oEmbed discovery, embed rewrite rules, and the wp-embed script.', 'edminboost' ),
			'heartbeat_control'          => __( 'Slow or disable the Heartbeat API on admin screens, the post editor, or the front end.', 'edminboost' ),
			'heartbeat_admin'            => __( 'Applies to wp-admin screens outside the post editor, such as list tables and settings pages. Default keeps the standard ~15 second interval. Slow reduces polling to 60 seconds. Disable removes the Heartbeat script entirely.', 'edminboost' ),
			'heartbeat_editor'           => __( 'Applies to the post and page editor (post.php and post-new.php). WordPress uses Heartbeat for autosave, post locking, and revision checks. Slow reduces polling to 60 seconds. Disable may affect autosave and collaborative editing.', 'edminboost' ),
			'heartbeat_frontend'         => __( 'Applies when logged-in users browse the public site. WordPress uses Heartbeat for session checks and admin bar updates. Default keeps the standard interval. Slow reduces polling to 60 seconds. Disable removes the script on the front end.', 'edminboost' ),

			// White label.
			'wl_enabled'                 => __( 'Master switch for white-label branding in wp-admin: sidebar menu label, Plugins screen row, and admin footer options below.', 'edminboost' ),
			'wl_hide_credit'             => __( 'Removes the default WordPress version credit from the admin footer.', 'edminboost' ),
			'wl_show_ip'                 => __( 'Include the visitor IP address in the system status footer.', 'edminboost' ),
			'wl_show_php_version'        => __( 'Include the PHP version in the system status footer.', 'edminboost' ),
			'wl_show_wp_version'         => __( 'Include the WordPress version in the system status footer.', 'edminboost' ),
			'wl_show_memory_usage'       => __( 'Include current PHP memory usage in the system status footer.', 'edminboost' ),
			'wl_show_memory_limit'       => __( 'Include the PHP memory limit in the system status footer.', 'edminboost' ),
			'wl_show_memory_available'   => __( 'Include estimated available memory in the system status footer.', 'edminboost' ),
			'wl_plugin_name'             => __( 'Replaces the plugin name on the Plugins screen and in plugin row meta.', 'edminboost' ),
			'wl_plugin_description'    => __( 'Replaces the plugin description shown on the Plugins screen.', 'edminboost' ),
			'wl_plugin_author'           => __( 'Replaces the author name shown for this plugin.', 'edminboost' ),
			'wl_plugin_uri'              => __( 'Replaces the plugin website link on the Plugins screen.', 'edminboost' ),
			'wl_menu_label'              => __( 'Replaces the EdminBoost item label in the wp-admin sidebar menu.', 'edminboost' ),

			// Settings page.
			'enabled'                    => __( 'Master switch for EdminBoost features, the Command Center bar, Menu Studio, and visual theme.', 'edminboost' ),
			'export_settings'            => __( 'Download all EdminBoost options as JSON for backup or migration. Media files referenced by attachment IDs are not included.', 'edminboost' ),
			'import_settings'            => __( 'Restore settings from exported JSON by pasting the payload or uploading a .json file. Existing values are replaced after validation.', 'edminboost' ),
		);
	}
}
