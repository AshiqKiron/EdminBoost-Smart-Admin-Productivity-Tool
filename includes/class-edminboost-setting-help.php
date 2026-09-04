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
 * Renders info icons with accessible tooltips on non-Dashboard settings pages.
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

		return EDMINBOOST_Admin::PAGE_SLUG !== $page;
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
			__( 'More information: %s', EDMINBOOST_TEXT_DOMAIN ),
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
			'theme_preset'               => __( 'Choose a color palette for wp-admin, the Command Center bar, slide-out drawer, and EdminBoost screens.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_mode'                 => __( 'Force light or dark styling, follow the system preference, or use scheduled dark mode when enabled below.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_font'                 => __( 'System font stack applied to EdminBoost UI and themed wp-admin chrome. No remote fonts are loaded.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_custom_colors'        => __( 'Override the five core theme tokens when the Custom preset is selected.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_custom_accent'        => __( 'Primary accent used for buttons, highlights, and active states.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_custom_surface'       => __( 'Background surface color for cards, panels, and content areas.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_custom_text'          => __( 'Default text color on themed surfaces.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_custom_topbar'        => __( 'Background color for the WordPress admin bar and Command Center bar.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_custom_sidebar'       => __( 'Background color for the wp-admin sidebar menu.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_custom_content'       => __( 'Background color for main admin content areas.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_admin_favicon'        => __( 'Media library attachment ID used as the favicon on wp-admin screens.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_font_size'            => __( 'Base font size in pixels for themed admin UI. Allowed range: 12–20.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_admin_bg_color'       => __( 'Optional hex background color behind wp-admin content.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_admin_bg_image'       => __( 'Media library attachment ID for an optional admin background image.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_schedule_dark_mode'   => __( 'When color mode is Auto, switch to dark styling during the configured time window.', EDMINBOOST_TEXT_DOMAIN ),
			'theme_dark_mode_start'      => __( 'Local site time when scheduled dark mode begins (24-hour HH:MM).', EDMINBOOST_TEXT_DOMAIN ),
			'theme_dark_mode_end'        => __( 'Local site time when scheduled dark mode ends (24-hour HH:MM).', EDMINBOOST_TEXT_DOMAIN ),
			'theme_status_colors'        => __( 'Optional hex colors for post list table rows by status (draft, pending, etc.).', EDMINBOOST_TEXT_DOMAIN ),

			// Appearance — panel & badges.
			'drawer_width'               => __( 'Default width of the AJAX slide-out drawer when a top bar link opens in-panel instead of navigating away.', EDMINBOOST_TEXT_DOMAIN ),
			'drawer_width_custom'        => __( 'Pixel width for the drawer when Custom is selected. Clamped between 400 and 800.', EDMINBOOST_TEXT_DOMAIN ),
			'animation_speed'            => __( 'Transition speed when opening and closing the slide-out drawer.', EDMINBOOST_TEXT_DOMAIN ),
			'glassmorphism'              => __( 'Adds a frosted-glass blur effect to the drawer backdrop.', EDMINBOOST_TEXT_DOMAIN ),
			'autosave_interval'          => __( 'How often forms inside the slide-out drawer auto-save, in seconds (10–600).', EDMINBOOST_TEXT_DOMAIN ),
			'badge_refresh_rate'         => __( 'How often live notification badges on the Command Center bar refresh, in seconds (15–600).', EDMINBOOST_TEXT_DOMAIN ),
			'badge_style'                => __( 'Visual style for live counters bound to WooCommerce orders, comments, updates, and similar sources.', EDMINBOOST_TEXT_DOMAIN ),

			// Appearance — admin bar cleanup.
			'hide_wp_logo'               => __( 'Remove the WordPress logo and its submenu from the native admin bar.', EDMINBOOST_TEXT_DOMAIN ),
			'hide_update_counters'       => __( 'Hide plugin, theme, and core update notification bubbles on the admin bar.', EDMINBOOST_TEXT_DOMAIN ),
			'hide_howdy'                 => __( 'Hide the “Howdy” greeting text in the admin bar profile area.', EDMINBOOST_TEXT_DOMAIN ),
			'hide_comments'              => __( 'Remove the comments shortcut from the native admin bar.', EDMINBOOST_TEXT_DOMAIN ),
			'hide_new_content'           => __( 'Remove the “New” content dropdown from the native admin bar.', EDMINBOOST_TEXT_DOMAIN ),
			'hide_customize'             => __( 'Hide the Customize link when the Customizer is available.', EDMINBOOST_TEXT_DOMAIN ),

			// Layout presets.
			'layout_preset'              => __( 'Apply a built-in or saved template that configures the top bar and left sidebar menu. Use Top Bar and Menu Studio for custom granular control.', EDMINBOOST_TEXT_DOMAIN ),
			'role_assignments'           => __( 'Assign a layout preset per user role. The first matching role for a logged-in user determines their top bar layout and sidebar menu visibility.', EDMINBOOST_TEXT_DOMAIN ),
			'role_visibility'            => __( 'Hide specific admin menu items from selected roles. Checked items remain visible in the top bar and sidebar for that role, including individual submenu pages. Items outside the assigned preset start unchecked but can still be enabled. Items the role cannot access by default also start unchecked—you may enable them manually.', EDMINBOOST_TEXT_DOMAIN ),

			// Top bar mapper.
			'discovered_pages'           => __( 'Admin menu pages scanned from your sidebar. Toggle or drag items onto the top bar canvas.', EDMINBOOST_TEXT_DOMAIN ),
			'mapper_search'              => __( 'Filter the discovered list by menu or plugin name.', EDMINBOOST_TEXT_DOMAIN ),
			'custom_topbar_path'         => __( 'Relative wp-admin path such as edit.php or admin.php?page=my-plugin. You may include a #fragment.', EDMINBOOST_TEXT_DOMAIN ),
			'custom_topbar_label'        => __( 'Short label shown on the Command Center bar for the custom link.', EDMINBOOST_TEXT_DOMAIN ),
			'custom_topbar_anchor'       => __( 'Optional page fragment to scroll to when the link opens. Can also be appended to the path above.', EDMINBOOST_TEXT_DOMAIN ),
			'topbar_canvas'              => __( 'Drag to reorder icons. Click an item to configure icon, label, interaction, and badge binding.', EDMINBOOST_TEXT_DOMAIN ),
			'item_icon'                  => __( 'Dashicon displayed on the Command Center bar for this link.', EDMINBOOST_TEXT_DOMAIN ),
			'item_label'                 => __( 'Override the default menu label shown on the bar.', EDMINBOOST_TEXT_DOMAIN ),
			'item_anchor'                => __( 'Scroll to this section ID when the link opens in redirect or drawer mode.', EDMINBOOST_TEXT_DOMAIN ),
			'item_interaction'           => __( 'Open the admin page directly or load it inside the AJAX slide-out drawer.', EDMINBOOST_TEXT_DOMAIN ),
			'item_badge_source'          => __( 'Optional live counter from local WordPress data (orders, comments, updates, etc.).', EDMINBOOST_TEXT_DOMAIN ),

			// Menu Studio.
			'menu_studio_enabled'        => __( 'Apply sidebar reordering, hidden items, custom links, and styling on all wp-admin screens.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_discovered'            => __( 'Toggle visibility or drag admin menu items into the sidebar preview.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_search'                => __( 'Filter the admin menu item list.', EDMINBOOST_TEXT_DOMAIN ),
			'custom_menu_path'           => __( 'Relative wp-admin path for a custom sidebar link.', EDMINBOOST_TEXT_DOMAIN ),
			'custom_menu_label'          => __( 'Label shown in the wp-admin sidebar for the custom link.', EDMINBOOST_TEXT_DOMAIN ),
			'custom_menu_parent'         => __( 'Nest the link under an existing top-level menu, or leave as top level.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_canvas'                => __( 'Drag top-level items to reorder. Expand a parent to reorder its submenus.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_width'                 => __( 'Sidebar width in pixels (120–300).', EDMINBOOST_TEXT_DOMAIN ),
			'menu_font_size'             => __( 'Sidebar menu font size in pixels (10–24).', EDMINBOOST_TEXT_DOMAIN ),
			'menu_line_height'           => __( 'Line height for sidebar menu items in pixels (12–36).', EDMINBOOST_TEXT_DOMAIN ),
			'menu_letter_spacing'        => __( 'Letter spacing for sidebar labels in pixels (-2 to 6).', EDMINBOOST_TEXT_DOMAIN ),
			'menu_display_mode'          => __( 'Show icons, text, or both on sidebar menu items.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_use_colors'            => __( 'Apply the custom color tokens below to the wp-admin sidebar.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_color_parent_bg'       => __( 'Background for top-level sidebar menu items.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_color_parent_text'     => __( 'Text color for top-level sidebar menu items.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_color_parent_active'   => __( 'Background for hovered or active top-level sidebar items.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_color_submenu_bg'      => __( 'Background for fly-out submenu panels.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_color_submenu_text'    => __( 'Text color for submenu links.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_color_submenu_hover_text' => __( 'Text color for submenu links on hover or focus.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_color_notification_bg' => __( 'Background for update count badges on menu items.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_color_notification_text' => __( 'Text color for update count badges.', EDMINBOOST_TEXT_DOMAIN ),

			// Productivity features.
			'hide_admin_notices'         => __( 'Moves routine success and info notices out of view. Errors and warnings stay visible.', EDMINBOOST_TEXT_DOMAIN ),
			'hide_screen_help'           => __( 'Hides the Screen Options and Help tabs in wp-admin.', EDMINBOOST_TEXT_DOMAIN ),
			'dashboard_widgets_enabled'  => __( 'Remove selected core Dashboard widgets for all users. Choose which widgets to hide below.', EDMINBOOST_TEXT_DOMAIN ),
			'admin_footer_enabled'       => __( 'Replace the default “Thank you for creating with WordPress” footer text in wp-admin.', EDMINBOOST_TEXT_DOMAIN ),
			'admin_footer_text'          => __( 'Custom HTML-safe text shown in the admin footer when replacement is enabled.', EDMINBOOST_TEXT_DOMAIN ),
			'post_duplicator'            => __( 'Adds a Duplicate row action to posts and pages in list tables.', EDMINBOOST_TEXT_DOMAIN ),
			'classic_widgets'            => __( 'Restores the pre-block classic widgets screen under Appearance.', EDMINBOOST_TEXT_DOMAIN ),
			'menu_duplicator'            => __( 'Adds a duplicate action when editing navigation menus.', EDMINBOOST_TEXT_DOMAIN ),
			'custom_admin_columns'       => __( 'Adds optional columns to the Posts and Pages list tables in wp-admin. Enable the feature, then configure each post type below. New columns appear immediately after the Title column.', EDMINBOOST_TEXT_DOMAIN ),
			'column_thumbnail'           => __( 'Shows a 40×40 featured image thumbnail after the Title column. Posts without a featured image leave the cell empty.', EDMINBOOST_TEXT_DOMAIN ),
			'column_id'                  => __( 'Shows the numeric post ID after the Title column. Useful for shortcodes, support tickets, or database lookups.', EDMINBOOST_TEXT_DOMAIN ),
			'column_meta_key'            => __( 'Shows the stored value for one post meta key after the Title column. Enter the exact meta key name (for example, _price). Only the first value is shown; serialized or array data may not display cleanly.', EDMINBOOST_TEXT_DOMAIN ),
			'post_order'                 => __( 'Enables drag-and-drop ordering via an Order column on supported post types.', EDMINBOOST_TEXT_DOMAIN ),

			// Security features.
			'security_hardening_note'    => __( 'These options may affect plugins or themes that rely on XML-RPC, feeds, or public REST access.', EDMINBOOST_TEXT_DOMAIN ),
			'disable_xmlrpc'             => __( 'Blocks XML-RPC requests. Required by some remote apps and Jetpack features.', EDMINBOOST_TEXT_DOMAIN ),
			'disable_feeds'              => __( 'Disables RSS/Atom feeds and redirects feed URLs to the home page.', EDMINBOOST_TEXT_DOMAIN ),
			'rest_hide_head'             => __( 'Removes the REST API discovery link tag from HTML output.', EDMINBOOST_TEXT_DOMAIN ),
			'rest_disable_guests'        => __( 'Returns an authentication error for unauthenticated REST API requests.', EDMINBOOST_TEXT_DOMAIN ),
			'disable_comments'           => __( 'Turns off comments and comment UI for the selected public post types.', EDMINBOOST_TEXT_DOMAIN ),
			'login_redirects_enabled'    => __( 'Send users to custom URLs after login or logout based on their role.', EDMINBOOST_TEXT_DOMAIN ),
			'default_login_redirect'     => __( 'Fallback URL after login when no role-specific URL is set.', EDMINBOOST_TEXT_DOMAIN ),
			'default_logout_redirect'    => __( 'Fallback URL after logout when no role-specific URL is set.', EDMINBOOST_TEXT_DOMAIN ),
			'role_login_redirect'        => __( 'Login redirect URL for this user role. Leave empty to use the default.', EDMINBOOST_TEXT_DOMAIN ),
			'role_logout_redirect'       => __( 'Logout redirect URL for this user role. Leave empty to use the default.', EDMINBOOST_TEXT_DOMAIN ),

			// Performance features.
			'disable_emojis'             => __( 'Removes WordPress emoji detection scripts and related DNS prefetch hints.', EDMINBOOST_TEXT_DOMAIN ),
			'disable_emojis_scope'       => __( 'Limit emoji removal to wp-admin, the front end, or both.', EDMINBOOST_TEXT_DOMAIN ),
			'remove_asset_versions'      => __( 'Strips ?ver= query strings from enqueued script and style URLs. Can affect cache busting.', EDMINBOOST_TEXT_DOMAIN ),
			'remove_dashicons_frontend'  => __( 'Dequeues Dashicons for visitors who are not logged in.', EDMINBOOST_TEXT_DOMAIN ),
			'disable_embeds'             => __( 'Disables oEmbed discovery, embed rewrite rules, and the wp-embed script.', EDMINBOOST_TEXT_DOMAIN ),
			'heartbeat_control'          => __( 'Slow or disable the Heartbeat API on admin screens, the post editor, or the front end.', EDMINBOOST_TEXT_DOMAIN ),
			'heartbeat_admin'            => __( 'Applies to wp-admin screens outside the post editor, such as list tables and settings pages. Default keeps the standard ~15 second interval. Slow reduces polling to 60 seconds. Disable removes the Heartbeat script entirely.', EDMINBOOST_TEXT_DOMAIN ),
			'heartbeat_editor'           => __( 'Applies to the post and page editor (post.php and post-new.php). WordPress uses Heartbeat for autosave, post locking, and revision checks. Slow reduces polling to 60 seconds. Disable may affect autosave and collaborative editing.', EDMINBOOST_TEXT_DOMAIN ),
			'heartbeat_frontend'         => __( 'Applies when logged-in users browse the public site. WordPress uses Heartbeat for session checks and admin bar updates. Default keeps the standard interval. Slow reduces polling to 60 seconds. Disable removes the script on the front end.', EDMINBOOST_TEXT_DOMAIN ),

			// White label.
			'wl_enabled'                 => __( 'Master switch for white-label branding in wp-admin: sidebar menu label, Plugins screen row, and admin footer options below.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_hide_credit'             => __( 'Removes the default WordPress version credit from the admin footer.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_show_ip'                 => __( 'Include the visitor IP address in the system status footer.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_show_php_version'        => __( 'Include the PHP version in the system status footer.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_show_wp_version'         => __( 'Include the WordPress version in the system status footer.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_show_memory_usage'       => __( 'Include current PHP memory usage in the system status footer.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_show_memory_limit'       => __( 'Include the PHP memory limit in the system status footer.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_show_memory_available'   => __( 'Include estimated available memory in the system status footer.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_plugin_name'             => __( 'Replaces the plugin name on the Plugins screen and in plugin row meta.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_plugin_description'    => __( 'Replaces the plugin description shown on the Plugins screen.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_plugin_author'           => __( 'Replaces the author name shown for this plugin.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_plugin_uri'              => __( 'Replaces the plugin website link on the Plugins screen.', EDMINBOOST_TEXT_DOMAIN ),
			'wl_menu_label'              => __( 'Replaces the EdminBoost item label in the wp-admin sidebar menu.', EDMINBOOST_TEXT_DOMAIN ),

			// Settings page.
			'enabled'                    => __( 'Master switch for EdminBoost features, the Command Center bar, Menu Studio, and visual theme.', EDMINBOOST_TEXT_DOMAIN ),
			'export_settings'            => __( 'Download all EdminBoost options as JSON for backup or migration. Media files referenced by attachment IDs are not included.', EDMINBOOST_TEXT_DOMAIN ),
			'import_settings'            => __( 'Restore settings from exported JSON by pasting the payload or uploading a .json file. Existing values are replaced after validation.', EDMINBOOST_TEXT_DOMAIN ),
		);
	}
}
