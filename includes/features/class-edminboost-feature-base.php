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
				return __( 'Hide Admin Notices', EDMINBOOST_TEXT_DOMAIN );
			case 'hide_screen_help':
				return __( 'Hide Screen Options & Help', EDMINBOOST_TEXT_DOMAIN );
			case 'dashboard_widgets':
				return __( 'Dashboard Widgets', EDMINBOOST_TEXT_DOMAIN );
			case 'admin_footer':
				return __( 'Admin Footer', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_emojis':
				return __( 'Disable Emojis', EDMINBOOST_TEXT_DOMAIN );
			case 'post_duplicator':
				return __( 'Post Duplicator', EDMINBOOST_TEXT_DOMAIN );
			case 'classic_widgets':
				return __( 'Classic Widgets', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_xmlrpc':
				return __( 'Disable XML-RPC', EDMINBOOST_TEXT_DOMAIN );
			case 'rest_api_hardening':
				return __( 'REST API Hardening', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_feeds':
				return __( 'Disable Feeds', EDMINBOOST_TEXT_DOMAIN );
			case 'login_redirects':
				return __( 'Login Redirects', EDMINBOOST_TEXT_DOMAIN );
			case 'remove_asset_versions':
				return __( 'Remove Asset Versions', EDMINBOOST_TEXT_DOMAIN );
			case 'remove_dashicons_frontend':
				return __( 'Remove Front-end Dashicons', EDMINBOOST_TEXT_DOMAIN );
			case 'heartbeat_control':
				return __( 'Heartbeat Control', EDMINBOOST_TEXT_DOMAIN );
			case 'custom_admin_columns':
				return __( 'Custom Admin Columns', EDMINBOOST_TEXT_DOMAIN );
			case 'menu_duplicator':
				return __( 'Menu Duplicator', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_comments':
				return __( 'Disable Comments', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_embeds':
				return __( 'Disable Embeds', EDMINBOOST_TEXT_DOMAIN );
			case 'post_order':
				return __( 'Post Order', EDMINBOOST_TEXT_DOMAIN );
			case 'admin_bar':
				return __( 'Admin Bar', EDMINBOOST_TEXT_DOMAIN );
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
				return __( 'Hide routine admin notices on non-EdminBoost screens while keeping errors and warnings visible.', EDMINBOOST_TEXT_DOMAIN );
			case 'hide_screen_help':
				return __( 'Hide the Screen Options and Help tabs on admin pages.', EDMINBOOST_TEXT_DOMAIN );
			case 'dashboard_widgets':
				return __( 'Remove selected default dashboard widgets for a cleaner overview.', EDMINBOOST_TEXT_DOMAIN );
			case 'admin_footer':
				return __( 'Replace the default WordPress admin footer text.', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_emojis':
				return __( 'Remove emoji detection scripts from the admin area for a lighter page load.', EDMINBOOST_TEXT_DOMAIN );
			case 'post_duplicator':
				return __( 'Add a duplicate action to post and page list tables.', EDMINBOOST_TEXT_DOMAIN );
			case 'classic_widgets':
				return __( 'Use the classic widgets screen instead of the block editor.', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_xmlrpc':
				return __( 'Disable the XML-RPC interface to reduce attack surface.', EDMINBOOST_TEXT_DOMAIN );
			case 'rest_api_hardening':
				return __( 'Hide REST API discovery links and restrict guest access.', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_feeds':
				return __( 'Disable RSS, Atom, and RDF feeds and redirect feed URLs.', EDMINBOOST_TEXT_DOMAIN );
			case 'login_redirects':
				return __( 'Set custom login and logout redirect URLs per user role.', EDMINBOOST_TEXT_DOMAIN );
			case 'remove_asset_versions':
				return __( 'Remove version query strings from script and style URLs.', EDMINBOOST_TEXT_DOMAIN );
			case 'remove_dashicons_frontend':
				return __( 'Stop loading Dashicons for visitors who do not need the admin bar.', EDMINBOOST_TEXT_DOMAIN );
			case 'heartbeat_control':
				return __( 'Modify or disable the WordPress Heartbeat API by context.', EDMINBOOST_TEXT_DOMAIN );
			case 'custom_admin_columns':
				return __( 'Show featured image, post ID, or a custom meta field in list tables.', EDMINBOOST_TEXT_DOMAIN );
			case 'menu_duplicator':
				return __( 'Duplicate an existing navigation menu with one click.', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_comments':
				return __( 'Disable comments and hide comment UI for selected post types.', EDMINBOOST_TEXT_DOMAIN );
			case 'disable_embeds':
				return __( 'Disable WordPress embeds and oEmbed discovery.', EDMINBOOST_TEXT_DOMAIN );
			case 'post_order':
				return __( 'Enable manual ordering via the Order column in list tables.', EDMINBOOST_TEXT_DOMAIN );
			case 'admin_bar':
				return __( 'Hide selected items from the WordPress admin bar.', EDMINBOOST_TEXT_DOMAIN );
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
