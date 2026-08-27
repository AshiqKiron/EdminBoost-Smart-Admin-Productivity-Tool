<?php
/**
 * White-label branding and system status footer.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Agency white-label runtime handler.
 */
class EDMINBOOST_White_Label {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'admin_footer_text', array( __CLASS__, 'filter_admin_footer_text' ), 100 );
		add_filter( 'update_footer', array( __CLASS__, 'filter_update_footer' ), 100 );
		add_filter( 'all_plugins', array( __CLASS__, 'filter_plugin_row' ) );
		add_filter( 'admin_menu', array( __CLASS__, 'filter_menu_label' ), 999 );
		add_action( 'admin_head', array( __CLASS__, 'print_admin_favicon' ) );
	}

	/**
	 * Default white-label settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'enabled'              => false,
			'hide_wp_footer_credit' => false,
			'show_ip'              => false,
			'show_php_version'     => false,
			'show_wp_version'      => false,
			'show_memory_usage'    => false,
			'show_memory_limit'    => false,
			'show_memory_available' => false,
			'admin_logo_light_id'  => 0,
			'admin_logo_dark_id'   => 0,
			'plugin_name'          => '',
			'plugin_description'   => '',
			'plugin_author'        => '',
			'plugin_uri'           => '',
			'menu_label'           => '',
		);
	}

	/**
	 * Get merged white-label settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = EDMINBOOST_Settings::get();
		$wl       = isset( $settings['white_label'] ) && is_array( $settings['white_label'] )
			? $settings['white_label']
			: array();

		return wp_parse_args( $wl, self::get_defaults() );
	}

	/**
	 * Whether white-label is active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return EDMINBOOST_Settings::is_enabled() && ! empty( self::get_settings()['enabled'] );
	}

	/**
	 * Default plugin metadata from the plugin header and admin menu label.
	 *
	 * Used by the White Label rebranding preview when custom fields are empty.
	 *
	 * @return array{plugin_name:string,plugin_description:string,plugin_author:string,plugin_uri:string,menu_label:string}
	 */
	public static function get_plugin_header_defaults() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$data = get_plugin_data( EDMINBOOST_PLUGIN_FILE, false, false );

		return array(
			'plugin_name'        => isset( $data['Name'] ) ? (string) $data['Name'] : '',
			'plugin_description' => isset( $data['Description'] ) ? (string) $data['Description'] : '',
			'plugin_author'      => isset( $data['AuthorName'] ) ? (string) $data['AuthorName'] : '',
			'plugin_uri'         => isset( $data['PluginURI'] ) && '' !== $data['PluginURI']
				? (string) $data['PluginURI']
				: ( isset( $data['AuthorURI'] ) ? (string) $data['AuthorURI'] : '' ),
			'menu_label'         => __( 'EdminBoost', EDMINBOOST_TEXT_DOMAIN ),
		);
	}

	/**
	 * Sanitize white-label input.
	 *
	 * @param array $raw Raw input.
	 * @return array
	 */
	public static function sanitize( $raw ) {
		$output = self::get_defaults();

		if ( ! is_array( $raw ) ) {
			return $output;
		}

		$output['enabled']               = ! empty( $raw['enabled'] );
		$output['hide_wp_footer_credit']  = ! empty( $raw['hide_wp_footer_credit'] );
		$output['show_ip']               = ! empty( $raw['show_ip'] );
		$output['show_php_version']      = ! empty( $raw['show_php_version'] );
		$output['show_wp_version']       = ! empty( $raw['show_wp_version'] );
		$output['show_memory_usage']     = ! empty( $raw['show_memory_usage'] );
		$output['show_memory_limit']     = ! empty( $raw['show_memory_limit'] );
		$output['show_memory_available'] = ! empty( $raw['show_memory_available'] );
		$output['admin_logo_light_id']   = absint( $raw['admin_logo_light_id'] ?? 0 );
		$output['admin_logo_dark_id']    = absint( $raw['admin_logo_dark_id'] ?? 0 );
		$output['plugin_name']           = sanitize_text_field( wp_unslash( $raw['plugin_name'] ?? '' ) );
		$output['plugin_description']    = sanitize_textarea_field( wp_unslash( $raw['plugin_description'] ?? '' ) );
		$output['plugin_author']         = sanitize_text_field( wp_unslash( $raw['plugin_author'] ?? '' ) );
		$output['plugin_uri']            = esc_url_raw( wp_unslash( $raw['plugin_uri'] ?? '' ) );
		$output['menu_label']            = sanitize_text_field( wp_unslash( $raw['menu_label'] ?? '' ) );

		return $output;
	}

	/**
	 * Filter left admin footer text.
	 *
	 * @param string $text Footer text.
	 * @return string
	 */
	public static function filter_admin_footer_text( $text ) {
		if ( ! self::is_active() ) {
			return $text;
		}

		$wl = self::get_settings();
		if ( ! empty( $wl['hide_wp_footer_credit'] ) ) {
			$text = '';
		}

		return $text;
	}

	/**
	 * Append system status to the right admin footer.
	 *
	 * @param string $text Footer version text.
	 * @return string
	 */
	public static function filter_update_footer( $text ) {
		if ( ! self::is_active() ) {
			return $text;
		}

		$wl     = self::get_settings();
		$parts  = array();
		$memory = self::get_memory_stats();

		if ( ! empty( $wl['show_ip'] ) ) {
			$parts[] = sprintf(
				/* translators: %s: IP address */
				__( 'IP: %s', EDMINBOOST_TEXT_DOMAIN ),
				isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '—'
			);
		}

		if ( ! empty( $wl['show_php_version'] ) ) {
			$parts[] = 'PHP ' . PHP_VERSION;
		}

		if ( ! empty( $wl['show_wp_version'] ) ) {
			global $wp_version;
			$parts[] = 'WP ' . $wp_version;
		}

		if ( ! empty( $wl['show_memory_usage'] ) ) {
			$parts[] = sprintf(
				/* translators: 1: used memory, 2: limit, 3: percent */
				__( 'Memory: %1$s of %2$s (%3$s%%)', EDMINBOOST_TEXT_DOMAIN ),
				$memory['used'],
				$memory['limit'],
				$memory['percent']
			);
		}

		if ( ! empty( $wl['show_memory_limit'] ) ) {
			$parts[] = sprintf(
				/* translators: %s: memory limit */
				__( 'Limit: %s', EDMINBOOST_TEXT_DOMAIN ),
				$memory['limit']
			);
		}

		if ( ! empty( $wl['show_memory_available'] ) ) {
			$parts[] = sprintf(
				/* translators: %s: available memory */
				__( 'Available: %s', EDMINBOOST_TEXT_DOMAIN ),
				$memory['available']
			);
		}

		if ( empty( $parts ) ) {
			return $text;
		}

		return implode( ' | ', $parts );
	}

	/**
	 * Get memory usage stats.
	 *
	 * @return array{used:string,limit:string,available:string,percent:int}
	 */
	private static function get_memory_stats() {
		$used_bytes = function_exists( 'memory_get_usage' ) ? memory_get_usage( true ) : 0;
		$limit      = ini_get( 'memory_limit' );
		$limit_bytes = wp_convert_hr_to_bytes( $limit );
		$available  = max( 0, $limit_bytes - $used_bytes );

		return array(
			'used'      => size_format( $used_bytes ),
			'limit'     => size_format( $limit_bytes ),
			'available' => size_format( $available ),
			'percent'   => $limit_bytes > 0 ? (int) round( ( $used_bytes / $limit_bytes ) * 100 ) : 0,
		);
	}

	/**
	 * Rebrand plugin row on Plugins screen.
	 *
	 * @param array $plugins All plugins.
	 * @return array
	 */
	public static function filter_plugin_row( $plugins ) {
		if ( ! self::is_active() ) {
			return $plugins;
		}

		$basename = EDMINBOOST_PLUGIN_BASENAME;
		if ( ! isset( $plugins[ $basename ] ) ) {
			return $plugins;
		}

		$wl = self::get_settings();

		if ( '' !== $wl['plugin_name'] ) {
			$plugins[ $basename ]['Name'] = $wl['plugin_name'];
		}
		if ( '' !== $wl['plugin_description'] ) {
			$plugins[ $basename ]['Description'] = $wl['plugin_description'];
		}
		if ( '' !== $wl['plugin_author'] ) {
			$plugins[ $basename ]['Author'] = $wl['plugin_author'];
		}
		if ( '' !== $wl['plugin_uri'] ) {
			$plugins[ $basename ]['PluginURI'] = $wl['plugin_uri'];
			$plugins[ $basename ]['AuthorURI'] = $wl['plugin_uri'];
		}

		return $plugins;
	}

	/**
	 * Rename EdminBoost menu label.
	 *
	 * @return void
	 */
	public static function filter_menu_label() {
		if ( ! self::is_active() ) {
			return;
		}

		global $menu;
		$wl = self::get_settings();

		if ( '' === $wl['menu_label'] || empty( $menu ) ) {
			return;
		}

		foreach ( $menu as $index => $item ) {
			if ( isset( $item[2] ) && EDMINBOOST_Admin::PAGE_SLUG === $item[2] ) {
				$menu[ $index ][0] = $wl['menu_label'];
			}
		}
	}

	/**
	 * Output admin favicon from theme settings.
	 *
	 * @return void
	 */
	public static function print_admin_favicon() {
		$theme = EDMINBOOST_Theme::get_settings();
		$id    = isset( $theme['admin_favicon_id'] ) ? absint( $theme['admin_favicon_id'] ) : 0;

		if ( ! $id ) {
			return;
		}

		$url = wp_get_attachment_image_url( $id, 'full' );
		if ( $url ) {
			printf(
				'<link rel="icon" href="%s" />',
				esc_url( $url )
			);
		}
	}
}
