<?php
/**
 * Base class for EdminBoost features.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract feature base — shared contract for all productivity modules.
 *
 * Purpose: Define get_id(), is_enabled(), and register_hooks() interface.
 *
 * @package EdminBoost
 */
abstract class EDMINBOOST_Feature_Base {

	/**
	 * Unique feature identifier.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Human-readable feature name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Get feature ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get feature name.
	 *
	 * @return string
	 */
	public function get_name() {
		switch ( $this->id ) {
			case 'hide_admin_notices':
				return __( 'Hide Admin Notices', 'edminboost-admin-customization' );
			case 'hide_screen_help':
				return __( 'Hide Screen Options & Help', 'edminboost-admin-customization' );
			case 'dashboard_widgets':
				return __( 'Dashboard Widgets', 'edminboost-admin-customization' );
			case 'admin_footer':
				return __( 'Admin Footer', 'edminboost-admin-customization' );
			case 'disable_emojis':
				return __( 'Disable Emojis', 'edminboost-admin-customization' );
			case 'post_duplicator':
				return __( 'Post Duplicator', 'edminboost-admin-customization' );
			case 'classic_widgets':
				return __( 'Classic Widgets', 'edminboost-admin-customization' );
			case 'disable_xmlrpc':
				return __( 'Disable XML-RPC', 'edminboost-admin-customization' );
			case 'rest_api_hardening':
				return __( 'REST API Hardening', 'edminboost-admin-customization' );
			case 'disable_feeds':
				return __( 'Disable Feeds', 'edminboost-admin-customization' );
			case 'login_redirects':
				return __( 'Login Redirects', 'edminboost-admin-customization' );
			case 'remove_asset_versions':
				return __( 'Remove Asset Versions', 'edminboost-admin-customization' );
			case 'remove_dashicons_frontend':
				return __( 'Remove Front-end Dashicons', 'edminboost-admin-customization' );
			case 'heartbeat_control':
				return __( 'Heartbeat Control', 'edminboost-admin-customization' );
			case 'custom_admin_columns':
				return __( 'Custom Admin Columns', 'edminboost-admin-customization' );
			case 'menu_duplicator':
				return __( 'Menu Duplicator', 'edminboost-admin-customization' );
			case 'disable_comments':
				return __( 'Disable Comments', 'edminboost-admin-customization' );
			case 'disable_embeds':
				return __( 'Disable Embeds', 'edminboost-admin-customization' );
			case 'post_order':
				return __( 'Post Order', 'edminboost-admin-customization' );
			case 'admin_bar':
				return __( 'Admin Bar', 'edminboost-admin-customization' );
			default:
				return '';
		}
	}

	/**
	 * Get feature description.
	 *
	 * @return string
	 */
	public function get_description() {
		switch ( $this->id ) {
			case 'hide_admin_notices':
				return __( 'Hide routine admin notices on non-EdminBoost screens while keeping errors and warnings visible.', 'edminboost-admin-customization' );
			case 'hide_screen_help':
				return __( 'Hide the Screen Options and Help tabs on admin pages.', 'edminboost-admin-customization' );
			case 'dashboard_widgets':
				return __( 'Remove selected default dashboard widgets for a cleaner overview.', 'edminboost-admin-customization' );
			case 'admin_footer':
				return __( 'Replace the default WordPress admin footer text.', 'edminboost-admin-customization' );
			case 'disable_emojis':
				return __( 'Remove emoji detection scripts from the admin area for a lighter page load.', 'edminboost-admin-customization' );
			case 'post_duplicator':
				return __( 'Add a duplicate action to post and page list tables.', 'edminboost-admin-customization' );
			case 'classic_widgets':
				return __( 'Use the classic widgets screen instead of the block editor.', 'edminboost-admin-customization' );
			case 'disable_xmlrpc':
				return __( 'Disable the XML-RPC interface to reduce attack surface.', 'edminboost-admin-customization' );
			case 'rest_api_hardening':
				return __( 'Hide REST API discovery links and restrict guest access.', 'edminboost-admin-customization' );
			case 'disable_feeds':
				return __( 'Disable RSS, Atom, and RDF feeds and redirect feed URLs.', 'edminboost-admin-customization' );
			case 'login_redirects':
				return __( 'Set custom login and logout redirect URLs per user role.', 'edminboost-admin-customization' );
			case 'remove_asset_versions':
				return __( 'Remove version query strings from script and style URLs.', 'edminboost-admin-customization' );
			case 'remove_dashicons_frontend':
				return __( 'Stop loading Dashicons for visitors who do not need the admin bar.', 'edminboost-admin-customization' );
			case 'heartbeat_control':
				return __( 'Modify or disable the WordPress Heartbeat API by context.', 'edminboost-admin-customization' );
			case 'custom_admin_columns':
				return __( 'Show featured image, post ID, or a custom meta field in list tables.', 'edminboost-admin-customization' );
			case 'menu_duplicator':
				return __( 'Duplicate an existing navigation menu with one click.', 'edminboost-admin-customization' );
			case 'disable_comments':
				return __( 'Disable comments and hide comment UI for selected post types.', 'edminboost-admin-customization' );
			case 'disable_embeds':
				return __( 'Disable WordPress embeds and oEmbed discovery.', 'edminboost-admin-customization' );
			case 'post_order':
				return __( 'Enable manual ordering via the Order column in list tables.', 'edminboost-admin-customization' );
			case 'admin_bar':
				return __( 'Hide selected items from the WordPress admin bar.', 'edminboost-admin-customization' );
			default:
				return '';
		}
	}

	/**
	 * Whether this feature is currently enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return EDMINBOOST_Settings::is_feature_enabled( $this->id );
	}

	/**
	 * Register WordPress hooks for this feature.
	 *
	 * @return void
	 */
	abstract public function register_hooks();
}
