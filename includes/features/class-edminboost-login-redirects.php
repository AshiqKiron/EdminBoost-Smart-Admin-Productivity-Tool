<?php
/**
 * Role-based login and logout redirects.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Redirects users after login or logout based on role.
 */
class EDMINBOOST_Login_Redirects extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'login_redirects';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Login Redirects';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Set custom login and logout redirect URLs per user role.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'login_redirect', array( $this, 'filter_login_redirect' ), 10, 3 );
		add_filter( 'logout_redirect', array( $this, 'filter_logout_redirect' ), 10, 3 );
	}

	/**
	 * Filter login redirect URL.
	 *
	 * @param string           $redirect_to           Redirect destination.
	 * @param string           $requested_redirect_to Requested redirect.
	 * @param WP_User|WP_Error $user                  User object.
	 * @return string
	 */
	public function filter_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
		if ( is_wp_error( $user ) || empty( $user->roles ) ) {
			return $redirect_to;
		}

		$settings = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$url      = $this->resolve_role_url( $user->roles, $settings['login_roles'] ?? array() );

		if ( '' === $url && ! empty( $settings['default_login'] ) ) {
			$url = $settings['default_login'];
		}

		return '' !== $url ? $url : $redirect_to;
	}

	/**
	 * Filter logout redirect URL.
	 *
	 * @param string  $redirect_to           Redirect destination.
	 * @param string  $requested_redirect_to Requested redirect.
	 * @param WP_User $user                  User object.
	 * @return string
	 */
	public function filter_logout_redirect( $redirect_to, $requested_redirect_to, $user ) {
		if ( empty( $user->roles ) ) {
			return $redirect_to;
		}

		$settings = EDMINBOOST_Settings::get_feature_settings( $this->get_id() );
		$url      = $this->resolve_role_url( $user->roles, $settings['logout_roles'] ?? array() );

		if ( '' === $url && ! empty( $settings['default_logout'] ) ) {
			$url = $settings['default_logout'];
		}

		return '' !== $url ? $url : $redirect_to;
	}

	/**
	 * Resolve the first matching role URL.
	 *
	 * @param string[] $roles    User roles.
	 * @param array    $role_map Role => URL map.
	 * @return string
	 */
	private function resolve_role_url( $roles, $role_map ) {
		if ( ! is_array( $role_map ) ) {
			return '';
		}

		foreach ( $roles as $role ) {
			if ( ! empty( $role_map[ $role ] ) ) {
				return $role_map[ $role ];
			}
		}

		return '';
	}
}
