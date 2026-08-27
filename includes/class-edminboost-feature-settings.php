<?php
/**
 * Feature settings defaults, sanitization, and enable checks.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Centralized feature option shape helpers.
 */
class EDMINBOOST_Feature_Settings {

	/**
	 * Default feature settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'hide_admin_notices'        => false,
			'hide_screen_help'          => false,
			'dashboard_widgets'         => array(
				'enabled'              => false,
				'remove_welcome_panel' => false,
				'remove_quick_press'   => false,
				'remove_activity'      => false,
				'remove_at_a_glance'   => false,
				'remove_site_health'   => false,
				'remove_wp_news'       => false,
			),
			'admin_footer'              => array(
				'enabled' => false,
				'text'    => '',
			),
			'disable_emojis'            => array(
				'enabled' => false,
				'scope'   => 'admin',
			),
			'post_duplicator'           => array(
				'enabled'    => false,
				'post_types' => array( 'post', 'page' ),
			),
			'classic_widgets'           => false,
			'disable_xmlrpc'            => false,
			'rest_api_hardening'        => array(
				'hide_head'      => false,
				'disable_guests' => false,
			),
			'disable_feeds'             => false,
			'login_redirects'           => array(
				'enabled'        => false,
				'login_roles'    => array(),
				'logout_roles'   => array(),
				'default_login'  => '',
				'default_logout' => '',
			),
			'remove_asset_versions'     => false,
			'remove_dashicons_frontend' => false,
			'heartbeat_control'         => array(
				'admin'    => 'default',
				'editor'   => 'default',
				'frontend' => 'disable',
			),
			'custom_admin_columns'      => array(
				'enabled' => false,
				'post'    => array(
					'thumbnail' => false,
					'id'        => false,
					'meta_key'  => '',
				),
				'page'    => array(
					'thumbnail' => false,
					'id'        => false,
					'meta_key'  => '',
				),
			),
			'menu_duplicator'           => false,
			'disable_comments'          => array(
				'enabled'    => false,
				'post_types' => array(),
			),
			'disable_embeds'            => false,
			'post_order'                => array(
				'enabled'    => false,
				'post_types' => array( 'post', 'page' ),
			),
		);
	}

	/**
	 * Normalize legacy feature shapes after merge.
	 *
	 * @param array $features Raw merged features.
	 * @return array
	 */
	public static function normalize( $features ) {
		$defaults = self::get_defaults();
		$features = wp_parse_args( $features, $defaults );

		foreach ( $defaults as $key => $default ) {
			if ( is_array( $default ) && isset( $features[ $key ] ) && is_array( $features[ $key ] ) ) {
				if ( 'dashboard_widgets' === $key && ! array_key_exists( 'enabled', $features[ $key ] ) ) {
					$any_widget = false;
					foreach ( $features[ $key ] as $widget_enabled ) {
						if ( ! empty( $widget_enabled ) ) {
							$any_widget = true;
							break;
						}
					}
					$features[ $key ]['enabled'] = $any_widget;
				}

				$features[ $key ] = wp_parse_args( $features[ $key ], $default );
			}
		}

		if ( is_bool( $features['disable_emojis'] ) || 1 === $features['disable_emojis'] || '1' === $features['disable_emojis'] ) {
			$features['disable_emojis'] = array(
				'enabled' => (bool) $features['disable_emojis'],
				'scope'   => 'admin',
			);
		}

		unset( $features['admin_bar'], $features['admin_menu'] );

		return $features;
	}

	/**
	 * Whether a feature is enabled.
	 *
	 * @param string $feature_id Feature identifier.
	 * @param array  $features   Feature settings.
	 * @return bool
	 */
	public static function is_enabled( $feature_id, $features ) {
		$features = self::normalize( $features );

		switch ( $feature_id ) {
			case 'hide_admin_notices':
			case 'hide_screen_help':
			case 'classic_widgets':
			case 'disable_xmlrpc':
			case 'disable_feeds':
			case 'remove_asset_versions':
			case 'remove_dashicons_frontend':
			case 'menu_duplicator':
			case 'disable_embeds':
				return ! empty( $features[ $feature_id ] );

			case 'disable_emojis':
				return ! empty( $features['disable_emojis']['enabled'] );

			case 'post_duplicator':
				return ! empty( $features['post_duplicator']['enabled'] );

			case 'dashboard_widgets':
				return ! empty( $features['dashboard_widgets']['enabled'] );

			case 'admin_footer':
				return ! empty( $features['admin_footer']['enabled'] )
					&& '' !== trim( (string) $features['admin_footer']['text'] );

			case 'rest_api_hardening':
				return ! empty( $features['rest_api_hardening']['hide_head'] )
					|| ! empty( $features['rest_api_hardening']['disable_guests'] );

			case 'login_redirects':
				return ! empty( $features['login_redirects']['enabled'] );

			case 'heartbeat_control':
				$hb = $features['heartbeat_control'];
				return 'default' !== $hb['admin'] || 'default' !== $hb['editor'] || 'disable' !== $hb['frontend'];

			case 'custom_admin_columns':
				return ! empty( $features['custom_admin_columns']['enabled'] );

			case 'disable_comments':
				return ! empty( $features['disable_comments']['enabled'] );

			case 'post_order':
				return ! empty( $features['post_order']['enabled'] );

			default:
				return (bool) apply_filters( 'edminboost_is_feature_enabled', false, $feature_id, $features );
		}
	}

	/**
	 * Sanitize feature settings input.
	 *
	 * @param array $raw       Raw features input.
	 * @param array $sanitized Current sanitized settings.
	 * @return array
	 */
	public static function sanitize( $raw, $sanitized ) {
		$output = isset( $sanitized['features'] ) && is_array( $sanitized['features'] )
			? self::normalize( $sanitized['features'] )
			: self::get_defaults();

		if ( ! is_array( $raw ) ) {
			return $output;
		}

		$defaults = self::get_defaults();

		$output['hide_admin_notices']        = ! empty( $raw['hide_admin_notices'] );
		$output['hide_screen_help']          = ! empty( $raw['hide_screen_help'] );
		$output['classic_widgets']           = ! empty( $raw['classic_widgets'] );
		$output['disable_xmlrpc']            = ! empty( $raw['disable_xmlrpc'] );
		$output['disable_feeds']             = ! empty( $raw['disable_feeds'] );
		$output['remove_asset_versions']     = ! empty( $raw['remove_asset_versions'] );
		$output['remove_dashicons_frontend'] = ! empty( $raw['remove_dashicons_frontend'] );
		$output['menu_duplicator']           = ! empty( $raw['menu_duplicator'] );
		$output['disable_embeds']            = ! empty( $raw['disable_embeds'] );

		$output['dashboard_widgets']['enabled'] = ! empty( $raw['dashboard_widgets']['enabled'] );
		foreach ( array_keys( $defaults['dashboard_widgets'] ) as $widget_key ) {
			if ( 'enabled' === $widget_key ) {
				continue;
			}

			$output['dashboard_widgets'][ $widget_key ] = ! empty( $raw['dashboard_widgets'][ $widget_key ] );
		}

		$output['admin_footer']['enabled'] = ! empty( $raw['admin_footer']['enabled'] );
		$output['admin_footer']['text']    = isset( $raw['admin_footer']['text'] )
			? sanitize_text_field( wp_unslash( $raw['admin_footer']['text'] ) )
			: '';

		$output['disable_emojis']['enabled'] = ! empty( $raw['disable_emojis']['enabled'] )
			|| ! empty( $raw['disable_emojis'] );
		$scope = isset( $raw['disable_emojis']['scope'] ) ? sanitize_key( $raw['disable_emojis']['scope'] ) : 'admin';
		$output['disable_emojis']['scope'] = in_array( $scope, array( 'admin', 'frontend', 'both' ), true ) ? $scope : 'admin';

		$output['post_duplicator']['enabled']     = ! empty( $raw['post_duplicator']['enabled'] );
		$output['post_duplicator']['post_types']  = self::sanitize_post_types(
			isset( $raw['post_duplicator']['post_types'] ) ? $raw['post_duplicator']['post_types'] : array(),
			array( 'post', 'page' )
		);

		$output['rest_api_hardening']['hide_head']      = ! empty( $raw['rest_api_hardening']['hide_head'] );
		$output['rest_api_hardening']['disable_guests'] = ! empty( $raw['rest_api_hardening']['disable_guests'] );

		$output['login_redirects']['enabled']        = ! empty( $raw['login_redirects']['enabled'] );
		$output['login_redirects']['default_login']  = self::sanitize_redirect_url( $raw['login_redirects']['default_login'] ?? '' );
		$output['login_redirects']['default_logout'] = self::sanitize_redirect_url( $raw['login_redirects']['default_logout'] ?? '' );
		$output['login_redirects']['login_roles']    = self::sanitize_role_redirects( $raw['login_redirects']['login_roles'] ?? array() );
		$output['login_redirects']['logout_roles']   = self::sanitize_role_redirects( $raw['login_redirects']['logout_roles'] ?? array() );

		$hb_contexts = array( 'admin', 'editor', 'frontend' );
		$hb_allowed  = array( 'default', 'slow', 'disable' );
		foreach ( $hb_contexts as $ctx ) {
			$val = isset( $raw['heartbeat_control'][ $ctx ] ) ? sanitize_key( $raw['heartbeat_control'][ $ctx ] ) : 'default';
			$output['heartbeat_control'][ $ctx ] = in_array( $val, $hb_allowed, true ) ? $val : 'default';
		}

		$output['custom_admin_columns']['enabled'] = ! empty( $raw['custom_admin_columns']['enabled'] );
		foreach ( array( 'post', 'page' ) as $pt ) {
			$col_raw = isset( $raw['custom_admin_columns'][ $pt ] ) && is_array( $raw['custom_admin_columns'][ $pt ] )
				? $raw['custom_admin_columns'][ $pt ]
				: array();
			$output['custom_admin_columns'][ $pt ]['thumbnail'] = ! empty( $col_raw['thumbnail'] );
			$output['custom_admin_columns'][ $pt ]['id']        = ! empty( $col_raw['id'] );
			$output['custom_admin_columns'][ $pt ]['meta_key']  = isset( $col_raw['meta_key'] )
				? sanitize_key( $col_raw['meta_key'] )
				: '';
		}

		$output['disable_comments']['enabled']     = ! empty( $raw['disable_comments']['enabled'] );
		$output['disable_comments']['post_types']  = self::sanitize_post_types(
			isset( $raw['disable_comments']['post_types'] ) ? $raw['disable_comments']['post_types'] : array(),
			array()
		);

		$output['post_order']['enabled']     = ! empty( $raw['post_order']['enabled'] );
		$output['post_order']['post_types']  = self::sanitize_post_types(
			isset( $raw['post_order']['post_types'] ) ? $raw['post_order']['post_types'] : array(),
			array( 'post', 'page' )
		);

		return $output;
	}

	/**
	 * Sanitize post type slug list.
	 *
	 * @param mixed $raw      Raw input.
	 * @param array $fallback Fallback when empty.
	 * @return array
	 */
	private static function sanitize_post_types( $raw, $fallback ) {
		if ( ! is_array( $raw ) ) {
			return $fallback;
		}

		$types = array();
		foreach ( $raw as $type ) {
			$type = sanitize_key( $type );
			if ( '' !== $type && post_type_exists( $type ) && ! in_array( $type, $types, true ) ) {
				$types[] = $type;
			}
		}

		return ! empty( $types ) ? $types : $fallback;
	}

	/**
	 * Sanitize a redirect URL (relative admin path or same-site URL).
	 *
	 * @param string $url Raw URL.
	 * @return string
	 */
	private static function sanitize_redirect_url( $url ) {
		$url = esc_url_raw( wp_unslash( (string) $url ) );
		if ( '' === $url ) {
			return '';
		}

		$home = home_url( '/' );
		if ( 0 === strpos( $url, $home ) || 0 === strpos( $url, '/' ) ) {
			return $url;
		}

		return '';
	}

	/**
	 * Sanitize role => URL map.
	 *
	 * @param array $raw Raw map.
	 * @return array
	 */
	private static function sanitize_role_redirects( $raw ) {
		if ( ! is_array( $raw ) ) {
			return array();
		}

		$map = array();
		foreach ( $raw as $role => $url ) {
			$role = sanitize_key( $role );
			$url  = self::sanitize_redirect_url( $url );
			if ( '' !== $role && '' !== $url ) {
				$map[ $role ] = $url;
			}
		}

		return $map;
	}
}
