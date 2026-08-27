<?php
/**
 * REST API hardening options.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hides REST API links and optionally blocks guest access.
 */
class EDMINBOOST_Rest_Api_Hardening extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'rest_api_hardening';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'REST API Hardening';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Hide REST API discovery links and restrict guest access.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		$settings = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );

		if ( ! empty( $settings['hide_head'] ) ) {
			remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
			remove_action( 'template_redirect', 'rest_output_link_header', 11 );
		}

		if ( ! empty( $settings['disable_guests'] ) ) {
			add_filter( 'rest_authentication_errors', array( $this, 'restrict_guest_access' ) );
		}
	}

	/**
	 * Block REST API access for non-authenticated users.
	 *
	 * @param WP_Error|null|true $result Authentication result.
	 * @return WP_Error|null|true
	 */
	public function restrict_guest_access( $result ) {
		if ( ! empty( $result ) ) {
			return $result;
		}

		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'REST API restricted to authenticated users.', EDMINBOOST_TEXT_DOMAIN ),
				array( 'status' => 401 )
			);
		}

		return $result;
	}
}
