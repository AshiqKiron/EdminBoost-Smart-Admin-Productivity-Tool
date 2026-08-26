<?php
/**
 * Visual theme presets for Command Center UI, drawer, and plugin screens.
 *
 * Purpose: CSS-token-based skins with optional custom colors, fonts, and color mode.
 * Does not change behavior settings (see look_skin for behavior bundles).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme helper and asset hooks.
 */
class EDMINBOOST_Theme {

	/**
	 * Default theme settings shape.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'preset'            => 'default',
			'mode'              => 'light',
			'font'              => 'inherit',
			'use_custom_colors' => false,
			'custom_accent'     => '',
			'custom_surface'    => '',
			'custom_text'       => '',
		);
	}

	/**
	 * Allowed color modes.
	 *
	 * @return array<string, string>
	 */
	public static function get_modes() {
		return array(
			'light' => __( 'Light', EDMINBOOST_TEXT_DOMAIN ),
			'dark'  => __( 'Dark', EDMINBOOST_TEXT_DOMAIN ),
			'auto'  => __( 'Auto (system)', EDMINBOOST_TEXT_DOMAIN ),
		);
	}

	/**
	 * Font stack options (system stacks only — no remote fonts).
	 *
	 * @return array<string, string>
	 */
	public static function get_fonts() {
		return array(
			'inherit' => __( 'WordPress default', EDMINBOOST_TEXT_DOMAIN ),
			'system'  => __( 'System UI', EDMINBOOST_TEXT_DOMAIN ),
			'mono'    => __( 'Monospace', EDMINBOOST_TEXT_DOMAIN ),
			'serif'   => __( 'Serif', EDMINBOOST_TEXT_DOMAIN ),
			'rounded' => __( 'Rounded UI', EDMINBOOST_TEXT_DOMAIN ),
		);
	}

	/**
	 * Visual theme presets (colors only).
	 *
	 * @return array<string, array>
	 */
	public static function get_presets() {
		return array(
			'default' => array(
				'name'        => __( 'Default', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'WordPress-aligned blues and neutrals.', EDMINBOOST_TEXT_DOMAIN ),
				'swatch'      => array( '#2271b1', '#f0f0f1', '#1d2327' ),
			),
			'midnight' => array(
				'name'        => __( 'Midnight', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Dark neutral surfaces with soft violet accents.', EDMINBOOST_TEXT_DOMAIN ),
				'swatch'      => array( '#8b9cff', '#1a1d24', '#e8eaed' ),
			),
			'terminal' => array(
				'name'        => __( 'Terminal', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Matrix-inspired green on deep black.', EDMINBOOST_TEXT_DOMAIN ),
				'swatch'      => array( '#00ff41', '#0a0f0a', '#b8ffc8' ),
			),
			'neon-outrun' => array(
				'name'        => __( 'Neon Outrun', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Synthwave magenta and cyan on dark purple.', EDMINBOOST_TEXT_DOMAIN ),
				'swatch'      => array( '#ff2bd6', '#1a0b2e', '#00f0ff' ),
			),
			'vapor' => array(
				'name'        => __( 'Vapor', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Vaporwave pastels with readable contrast.', EDMINBOOST_TEXT_DOMAIN ),
				'swatch'      => array( '#ff6ad5', '#e8e0ff', '#2d1b4e' ),
			),
			'desert' => array(
				'name'        => __( 'Desert', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Warm sand tones with copper accents.', EDMINBOOST_TEXT_DOMAIN ),
				'swatch'      => array( '#c87941', '#f5ebe0', '#3d2914' ),
			),
		);
	}

	/**
	 * Merge stored theme settings with defaults.
	 *
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return array
	 */
	public static function get_settings( $cc_settings = null ) {
		if ( null === $cc_settings ) {
			$cc_settings = EDMINBOOST_Command_Center::get_settings();
		}

		$theme = isset( $cc_settings['theme'] ) && is_array( $cc_settings['theme'] )
			? $cc_settings['theme']
			: array();

		return wp_parse_args( $theme, self::get_defaults() );
	}

	/**
	 * Whether the visual theme should load on the current request.
	 *
	 * @return bool
	 */
	public static function is_active() {
		if ( ! EDMINBOOST_Settings::is_enabled() || ! is_user_logged_in() ) {
			return false;
		}

		if ( is_admin() ) {
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $page && false !== strpos( $page, EDMINBOOST_Admin::PAGE_SLUG ) ) {
				return true;
			}
		}

		return EDMINBOOST_Command_Center_Bar::is_active() || EDMINBOOST_Command_Center_Bar::is_mapper_screen();
	}

	/**
	 * Register theme hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'admin_body_class', array( __CLASS__, 'filter_admin_body_class' ), 20 );
		add_filter( 'body_class', array( __CLASS__, 'filter_body_class' ), 20 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 5 );
		add_action( 'admin_head', array( __CLASS__, 'print_custom_color_overrides' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'print_custom_color_overrides' ), 20 );
	}

	/**
	 * Append theme classes in wp-admin.
	 *
	 * @param string $classes Space-separated admin body classes.
	 * @return string
	 */
	public static function filter_admin_body_class( $classes ) {
		if ( ! self::is_active() ) {
			return $classes;
		}

		return trim( $classes . ' ' . implode( ' ', self::get_body_classes() ) );
	}

	/**
	 * Append theme classes on the front end (admin bar + drawer).
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function filter_body_class( $classes ) {
		if ( ! self::is_active() ) {
			return $classes;
		}

		return array_merge( $classes, self::get_body_classes() );
	}

	/**
	 * Body class list for the active theme.
	 *
	 * @return string[]
	 */
	public static function get_body_classes() {
		$theme   = self::get_settings();
		$preset  = sanitize_key( $theme['preset'] );
		$presets = self::get_presets();

		if ( ! isset( $presets[ $preset ] ) ) {
			$preset = 'default';
		}

		$mode = sanitize_key( $theme['mode'] );
		if ( ! isset( self::get_modes()[ $mode ] ) ) {
			$mode = 'light';
		}

		$font = sanitize_key( $theme['font'] );
		if ( ! isset( self::get_fonts()[ $font ] ) ) {
			$font = 'inherit';
		}

		return array(
			'edminboost-theme-active',
			'edminboost-theme--' . $preset,
			'edminboost-theme-mode--' . $mode,
			'edminboost-theme-font--' . $font,
		);
	}

	/**
	 * Enqueue theme stylesheet when active.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		if ( ! self::is_active() ) {
			return;
		}

		wp_enqueue_style(
			'edminboost-themes',
			EDMINBOOST_PLUGIN_URL . 'admin/css/edminboost-themes.css',
			array(),
			EDMINBOOST_VERSION
		);
	}

	/**
	 * Print inline CSS for custom color overrides.
	 *
	 * @return void
	 */
	public static function print_custom_color_overrides() {
		if ( ! self::is_active() ) {
			return;
		}

		$theme = self::get_settings();
		if ( empty( $theme['use_custom_colors'] ) ) {
			return;
		}

		$rules = array();

		$accent = self::sanitize_hex_color( $theme['custom_accent'] );
		if ( $accent ) {
			$rules[] = '--eb-accent: ' . $accent . ';';
			$rules[] = '--eb-badge-accent: ' . $accent . ';';
		}

		$surface = self::sanitize_hex_color( $theme['custom_surface'] );
		if ( $surface ) {
			$rules[] = '--eb-surface: ' . $surface . ';';
			$rules[] = '--eb-drawer-panel-bg: ' . $surface . ';';
			$rules[] = '--eb-surface-alt: ' . $surface . ';';
		}

		$text = self::sanitize_hex_color( $theme['custom_text'] );
		if ( $text ) {
			$rules[] = '--eb-text: ' . $text . ';';
			$rules[] = '--eb-drawer-header-text: ' . $text . ';';
		}

		if ( empty( $rules ) ) {
			return;
		}

		echo '<style id="edminboost-theme-custom">';
		echo 'body.edminboost-theme-active{';
		// Values are sanitized hex colors from sanitize_hex_color().
		echo wp_strip_all_tags( implode( '', $rules ) );
		echo '}';
		echo '</style>';
	}

	/**
	 * Sanitize a hex color value.
	 *
	 * @param string $color Raw color.
	 * @return string Sanitized hex or empty string.
	 */
	public static function sanitize_hex_color( $color ) {
		$color = sanitize_hex_color( wp_unslash( (string) $color ) );
		return $color ? $color : '';
	}

	/**
	 * Sanitize theme settings input.
	 *
	 * @param array $raw Raw theme input.
	 * @return array
	 */
	public static function sanitize( $raw ) {
		$output  = self::get_defaults();
		$presets = array_keys( self::get_presets() );
		$modes   = array_keys( self::get_modes() );
		$fonts   = array_keys( self::get_fonts() );

		if ( isset( $raw['preset'] ) ) {
			$preset = sanitize_key( $raw['preset'] );
			if ( in_array( $preset, $presets, true ) ) {
				$output['preset'] = $preset;
			}
		}

		if ( isset( $raw['mode'] ) ) {
			$mode = sanitize_key( $raw['mode'] );
			if ( in_array( $mode, $modes, true ) ) {
				$output['mode'] = $mode;
			}
		}

		if ( isset( $raw['font'] ) ) {
			$font = sanitize_key( $raw['font'] );
			if ( in_array( $font, $fonts, true ) ) {
				$output['font'] = $font;
			}
		}

		$output['use_custom_colors'] = ! empty( $raw['use_custom_colors'] );
		$output['custom_accent']     = self::sanitize_hex_color( isset( $raw['custom_accent'] ) ? $raw['custom_accent'] : '' );
		$output['custom_surface']    = self::sanitize_hex_color( isset( $raw['custom_surface'] ) ? $raw['custom_surface'] : '' );
		$output['custom_text']       = self::sanitize_hex_color( isset( $raw['custom_text'] ) ? $raw['custom_text'] : '' );

		return $output;
	}
}
