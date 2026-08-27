<?php
/**
 * Feature registry and loader.
 *
 * Purpose: Load feature classes, instantiate them, and register hooks for enabled features.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages productivity features.
 */
class EDMINBOOST_Features {

	/**
	 * Registered feature instances.
	 *
	 * @var EDMINBOOST_Feature_Base[]
	 */
	protected $features = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_features();
	}

	/**
	 * Load feature class files and instantiate them.
	 *
	 * @return void
	 */
	protected function load_features() {
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-feature-base.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-hide-notices.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-hide-screen-help.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-dashboard.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-admin-footer.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-disable-emojis.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-post-duplicator.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-classic-widgets.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-disable-xmlrpc.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-rest-api-hardening.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-disable-feeds.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-login-redirects.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-remove-asset-versions.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-remove-dashicons-frontend.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-heartbeat-control.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-custom-admin-columns.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-menu-duplicator.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-disable-comments.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-disable-embeds.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-post-order.php';

		$feature_classes = array(
			'EDMINBOOST_Hide_Notices',
			'EDMINBOOST_Hide_Screen_Help',
			'EDMINBOOST_Dashboard',
			'EDMINBOOST_Admin_Footer',
			'EDMINBOOST_Disable_Emojis',
			'EDMINBOOST_Post_Duplicator',
			'EDMINBOOST_Classic_Widgets',
			'EDMINBOOST_Disable_Xmlrpc',
			'EDMINBOOST_Rest_Api_Hardening',
			'EDMINBOOST_Disable_Feeds',
			'EDMINBOOST_Login_Redirects',
			'EDMINBOOST_Remove_Asset_Versions',
			'EDMINBOOST_Remove_Dashicons_Frontend',
			'EDMINBOOST_Heartbeat_Control',
			'EDMINBOOST_Custom_Admin_Columns',
			'EDMINBOOST_Menu_Duplicator',
			'EDMINBOOST_Disable_Comments',
			'EDMINBOOST_Disable_Embeds',
			'EDMINBOOST_Post_Order',
		);

		/**
		 * Filter feature class names before registration.
		 *
		 * @param string[] $feature_classes Feature class names.
		 */
		$feature_classes = apply_filters( 'edminboost_feature_classes', $feature_classes );

		foreach ( $feature_classes as $class_name ) {
			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			$feature = new $class_name();

			if ( $feature instanceof EDMINBOOST_Feature_Base ) {
				$this->features[ $feature->get_id() ] = $feature;
			}
		}

		/**
		 * Fires after features are loaded.
		 *
		 * @param EDMINBOOST_Feature_Base[] $features Feature instances keyed by ID.
		 */
		do_action( 'edminboost_features_loaded', $this->features );
	}

	/**
	 * Register hooks for all enabled features.
	 *
	 * @return void
	 */
	public function register_hooks() {
		foreach ( $this->features as $feature ) {
			if ( $feature->is_enabled() ) {
				$feature->register_hooks();
			}
		}
	}

	/**
	 * Get all registered features.
	 *
	 * @return EDMINBOOST_Feature_Base[]
	 */
	public function get_features() {
		return $this->features;
	}

	/**
	 * Get a single feature by ID.
	 *
	 * @param string $feature_id Feature identifier.
	 * @return EDMINBOOST_Feature_Base|null
	 */
	public function get_feature( $feature_id ) {
		return isset( $this->features[ $feature_id ] ) ? $this->features[ $feature_id ] : null;
	}
}
