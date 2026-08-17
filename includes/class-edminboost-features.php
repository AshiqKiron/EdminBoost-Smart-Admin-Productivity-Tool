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
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-dashboard.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-admin-menu.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-admin-footer.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-disable-emojis.php';
		require_once EDMINBOOST_PLUGIN_DIR . 'includes/features/class-edminboost-admin-bar.php';

		$feature_classes = array(
			'EDMINBOOST_Hide_Notices',
			'EDMINBOOST_Dashboard',
			'EDMINBOOST_Admin_Menu',
			'EDMINBOOST_Admin_Footer',
			'EDMINBOOST_Disable_Emojis',
			'EDMINBOOST_Admin_Bar',
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
		if ( ! is_admin() ) {
			return;
		}

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
