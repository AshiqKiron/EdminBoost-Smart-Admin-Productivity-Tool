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
		return __( $this->name, EDMINBOOST_TEXT_DOMAIN );
	}

	/**
	 * Get feature description.
	 *
	 * @return string
	 */
	public function get_description() {
		return __( $this->description, EDMINBOOST_TEXT_DOMAIN );
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
