<?php
/**
 * Visual theme presets for Command Center UI, drawer, and plugin screens.
 *
 * Purpose: CSS-token-based skins with optional custom colors, fonts, and color mode.
 * Applies to wp-admin top bar, sidebar menu, Command Center UI, drawer, and plugin screens.
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
			'font_size'         => 14,
			'use_custom_colors' => false,
			'custom_accent'     => '',
			'custom_surface'    => '',
			'custom_text'       => '',
			'custom_top'        => '',
			'custom_sidebar'    => '',
			'custom_content'    => '',
			'admin_favicon_id'  => 0,
			'admin_bg_color'    => '',
			'admin_bg_image_id' => 0,
			'schedule_dark_mode' => false,
			'dark_mode_start'   => '18:00',
			'dark_mode_end'     => '06:00',
			'status_colors'     => array(
				'publish' => '',
				'pending' => '',
				'future'  => '',
				'private' => '',
				'draft'   => '',
				'trash'   => '',
			),
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
			'inherit'   => __( 'WordPress default', EDMINBOOST_TEXT_DOMAIN ),
			'system'    => __( 'System UI', EDMINBOOST_TEXT_DOMAIN ),
			'arial'     => __( 'Arial / Helvetica', EDMINBOOST_TEXT_DOMAIN ),
			'verdana'   => __( 'Verdana', EDMINBOOST_TEXT_DOMAIN ),
			'tahoma'    => __( 'Tahoma', EDMINBOOST_TEXT_DOMAIN ),
			'trebuchet' => __( 'Trebuchet MS', EDMINBOOST_TEXT_DOMAIN ),
			'lucida'    => __( 'Lucida Sans', EDMINBOOST_TEXT_DOMAIN ),
			'palatino'  => __( 'Palatino', EDMINBOOST_TEXT_DOMAIN ),
			'humanist'  => __( 'Humanist sans', EDMINBOOST_TEXT_DOMAIN ),
			'mono'      => __( 'Monospace', EDMINBOOST_TEXT_DOMAIN ),
			'serif'     => __( 'Serif', EDMINBOOST_TEXT_DOMAIN ),
			'rounded'   => __( 'Rounded UI', EDMINBOOST_TEXT_DOMAIN ),
		);
	}

	/**
	 * Labels for the five preset color tokens shown in the theme UI.
	 *
	 * @return array<string, string>
	 */
	public static function get_color_labels() {
		return array(
			'accent'  => __( 'Accent', EDMINBOOST_TEXT_DOMAIN ),
			'surface' => __( 'Surface', EDMINBOOST_TEXT_DOMAIN ),
			'text'    => __( 'Text', EDMINBOOST_TEXT_DOMAIN ),
			'topbar'   => __( 'Top bar', EDMINBOOST_TEXT_DOMAIN ),
			'sidebar'  => __( 'Sidebar', EDMINBOOST_TEXT_DOMAIN ),
			'content'  => __( 'Content area', EDMINBOOST_TEXT_DOMAIN ),
		);
	}

	/**
	 * Visual theme presets (colors only).
	 *
	 * @return array<string, array>
	 */
	public static function get_presets() {
		return array(
			'default'     => self::build_preset(
				__( 'Default', EDMINBOOST_TEXT_DOMAIN ),
				__( 'WordPress-aligned blues and neutrals.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#2271b1',
					'surface' => '#ffffff',
					'text'    => '#1d2327',
					'topbar'  => '#1d2327',
					'sidebar' => '#1d2327',
					'content' => '#f0f0f1',
				)
			),
			'midnight'    => self::build_preset(
				__( 'Midnight', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Dark neutral surfaces with soft violet accents.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#8b9cff',
					'surface' => '#1a1d24',
					'text'    => '#e8eaed',
					'topbar'  => '#12151c',
					'sidebar' => '#12151c',
					'content' => '#1a1d24',
				)
			),
			'terminal'    => self::build_preset(
				__( 'Terminal', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Matrix-inspired green on deep black.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#00ff41',
					'surface' => '#0a0f0a',
					'text'    => '#b8ffc8',
					'topbar'  => '#050805',
					'sidebar' => '#050805',
					'content' => '#0a0f0a',
				)
			),
			'neon-outrun' => self::build_preset(
				__( 'Neon Outrun', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Synthwave magenta and cyan on dark purple.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#ff2bd6',
					'surface' => '#1a0b2e',
					'text'    => '#00f0ff',
					'topbar'  => '#0f061a',
					'sidebar' => '#0f061a',
					'content' => '#1a0b2e',
				)
			),
			'vapor'       => self::build_preset(
				__( 'Vapor', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Vaporwave pastels with readable contrast.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#ff6ad5',
					'surface' => '#e8e0ff',
					'text'    => '#2d1b4e',
					'topbar'  => '#2d1b4e',
					'sidebar' => '#2d1b4e',
					'content' => '#2d1b4e',
				)
			),
			'desert'      => self::build_preset(
				__( 'Desert', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Warm sand tones with copper accents.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#c87941',
					'surface' => '#f5ebe0',
					'text'    => '#3d2914',
					'topbar'  => '#3d2914',
					'sidebar' => '#3d2914',
					'content' => '#2a1c0e',
				)
			),
			'dracula'     => self::build_preset(
				__( 'Dracula', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Dracula-inspired purple accents on inky charcoal.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#bd93f9',
					'surface' => '#282a36',
					'text'    => '#f8f8f2',
					'topbar'  => '#21222c',
					'sidebar' => '#21222c',
					'content' => '#282a36',
				)
			),
			'nord'        => self::build_preset(
				__( 'Nord', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Arctic frost blues on polar night surfaces.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#88c0d0',
					'surface' => '#2e3440',
					'text'    => '#eceff4',
					'topbar'  => '#242933',
					'sidebar' => '#242933',
					'content' => '#2e3440',
				)
			),
			'solarized'   => self::build_preset(
				__( 'Solarized', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Solarized-inspired cream base with teal accents.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#268bd2',
					'surface' => '#fdf6e3',
					'text'    => '#657b83',
					'topbar'  => '#073642',
					'sidebar' => '#073642',
					'content' => '#002b36',
				)
			),
			'sakura'      => self::build_preset(
				__( 'Sakura', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Cherry blossom pinks on soft blush surfaces.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#e8879a',
					'surface' => '#fff5f7',
					'text'    => '#4a2030',
					'topbar'  => '#4a2030',
					'sidebar' => '#4a2030',
					'content' => '#fce8ec',
				)
			),
			'ocean'       => self::build_preset(
				__( 'Ocean', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Deep-sea navy with luminous aqua highlights.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#3dd6d0',
					'surface' => '#0a1628',
					'text'    => '#c8e6f5',
					'topbar'  => '#061018',
					'sidebar' => '#061018',
					'content' => '#0a1628',
				)
			),
			'forest'      => self::build_preset(
				__( 'Forest', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Moss greens and woodland tones for a calm admin.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#6dbf6d',
					'surface' => '#1a2e1f',
					'text'    => '#d4e8d0',
					'topbar'  => '#0f1a12',
					'sidebar' => '#0f1a12',
					'content' => '#1a2e1f',
				)
			),
			'tron'        => self::build_preset(
				__( 'Tron', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Tron-inspired electric cyan glowing on deep black grid.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#00d4ff',
					'surface' => '#0a0a12',
					'text'    => '#b8e8ff',
					'topbar'  => '#050508',
					'sidebar' => '#050508',
					'content' => '#0a0a12',
				)
			),
			'night-city'  => self::build_preset(
				__( 'Night City', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Cyberpunk-inspired neon yellow and cyan on rain-soaked dark.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#fcee0a',
					'surface' => '#0d0d0d',
					'text'    => '#00f0ff',
					'topbar'  => '#080808',
					'sidebar' => '#080808',
					'content' => '#0d0d0d',
				)
			),
			'pip-boy'     => self::build_preset(
				__( 'Pip-Boy', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Fallout-inspired amber CRT phosphor on wasteland green-black.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#ffb000',
					'surface' => '#1a2e1a',
					'text'    => '#b8d4a0',
					'topbar'  => '#0f1a0f',
					'sidebar' => '#0f1a0f',
					'content' => '#1a2e1a',
				)
			),
			'portal'      => self::build_preset(
				__( 'Portal', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Portal-inspired Aperture orange with companion-core blue accents.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#ff7b00',
					'surface' => '#f5f5f5',
					'text'    => '#0099cc',
					'topbar'  => '#333333',
					'sidebar' => '#333333',
					'content' => '#eaeaea',
				)
			),
			'gotham'      => self::build_preset(
				__( 'Gotham', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Batman-inspired charcoal shadows with striking gold highlights.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#f0c040',
					'surface' => '#1a1a1a',
					'text'    => '#e8e8e8',
					'topbar'  => '#0d0d0d',
					'sidebar' => '#0d0d0d',
					'content' => '#1a1a1a',
				)
			),
			'citadel'     => self::build_preset(
				__( 'Citadel', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Mass Effect-inspired cerulean blues on deep space navy.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#4fc3f7',
					'surface' => '#0d1b2a',
					'text'    => '#b8d4e8',
					'topbar'  => '#061018',
					'sidebar' => '#061018',
					'content' => '#0d1b2a',
				)
			),
			'blade-noir'  => self::build_preset(
				__( 'Blade Noir', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Blade Runner-inspired neon orange and teal in a rainy future city.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#ff6b35',
					'surface' => '#1a1a2e',
					'text'    => '#2ec4b6',
					'topbar'  => '#0f0f18',
					'sidebar' => '#0f0f18',
					'content' => '#1a1a2e',
				)
			),
			'hyrule'      => self::build_preset(
				__( 'Hyrule', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Zelda-inspired hero green and Triforce gold on parchment stone.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#2d6a4f',
					'surface' => '#f5f0e0',
					'text'    => '#c8a032',
					'topbar'  => '#3d2914',
					'sidebar' => '#3d2914',
					'content' => '#e8e0c8',
				)
			),
			'custom'      => self::build_preset(
				__( 'Custom', EDMINBOOST_TEXT_DOMAIN ),
				__( 'Define your own accent, surface, text, top bar, sidebar, and content area colors.', EDMINBOOST_TEXT_DOMAIN ),
				array(
					'accent'  => '#2271b1',
					'surface' => '#ffffff',
					'text'    => '#1d2327',
					'topbar'  => '#1d2327',
					'sidebar' => '#1d2327',
					'content' => '#f0f0f1',
				)
			),
		);
	}

	/**
	 * Preset catalog for admin JavaScript (colors + labels).
	 *
	 * @return array<string, array>
	 */
	public static function get_presets_for_js() {
		$presets = array();

		foreach ( self::get_presets() as $preset_id => $preset ) {
			$presets[ $preset_id ] = array(
				'name'         => $preset['name'],
				'description'  => $preset['description'],
				'colors'       => $preset['colors'],
				'colorsByMode' => array(
					'light' => self::resolve_preview_colors( $preset_id, 'light' ),
					'dark'  => self::resolve_preview_colors( $preset_id, 'dark' ),
				),
			);
		}

		return $presets;
	}

	/**
	 * Resolve preview swatch colors for a preset and color mode.
	 *
	 * @param string     $preset_id Preset key.
	 * @param string     $mode      light, dark, or auto.
	 * @param array|null $theme     Optional merged theme settings for custom colors.
	 * @return array{accent:string,surface:string,text:string,topbar:string,sidebar:string,content:string}
	 */
	public static function resolve_preview_colors( $preset_id, $mode = 'light', $theme = null ) {
		$defaults = array(
			'accent'  => '#2271b1',
			'surface' => '#ffffff',
			'text'    => '#1d2327',
			'topbar'  => '#1d2327',
			'sidebar' => '#1d2327',
			'content' => '#f0f0f1',
		);

		if ( null === $theme ) {
			$theme = self::get_settings();
		} else {
			$theme = wp_parse_args( $theme, self::get_defaults() );
		}

		if ( self::uses_custom_colors( $theme ) ) {
			$presets = self::get_presets();
			$colors  = isset( $presets['custom']['colors'] ) && is_array( $presets['custom']['colors'] )
				? $presets['custom']['colors']
				: array();

			$custom_map = array(
				'accent'  => 'custom_accent',
				'surface' => 'custom_surface',
				'text'    => 'custom_text',
				'topbar'  => 'custom_top',
				'sidebar' => 'custom_sidebar',
				'content' => 'custom_content',
			);

			foreach ( $custom_map as $color_key => $theme_key ) {
				if ( ! empty( $theme[ $theme_key ] ) ) {
					$colors[ $color_key ] = self::sanitize_hex_color( $theme[ $theme_key ] );
				}
			}

			return wp_parse_args( array_filter( $colors ), $defaults );
		}

		$preset_id = sanitize_key( $preset_id );
		$mode      = sanitize_key( $mode );
		$presets   = self::get_presets();

		if ( ! isset( $presets[ $preset_id ] ) ) {
			$preset_id = 'default';
		}

		if ( 'auto' === $mode ) {
			$auto_tokens = self::get_css_tokens( $preset_id, 'auto' );
			if ( ! empty( $auto_tokens ) ) {
				return wp_parse_args( self::tokens_to_preview_colors( $auto_tokens ), $defaults );
			}
		} elseif ( in_array( $mode, array( 'light', 'dark' ), true ) ) {
			$mode_tokens = self::get_css_tokens( $preset_id, $mode );
			if ( ! empty( $mode_tokens ) ) {
				return wp_parse_args( self::tokens_to_preview_colors( $mode_tokens ), $defaults );
			}
		}

		$canonical = isset( $presets[ $preset_id ]['colors'] ) && is_array( $presets[ $preset_id ]['colors'] )
			? $presets[ $preset_id ]['colors']
			: $presets['default']['colors'];

		return wp_parse_args( $canonical, $defaults );
	}

	/**
	 * Map parsed CSS custom properties to preview color tokens.
	 *
	 * @param array<string, string> $tokens Parsed --eb-* properties.
	 * @return array{accent:string,surface:string,text:string,topbar:string,sidebar:string,content:string}
	 */
	private static function tokens_to_preview_colors( array $tokens ) {
		$topbar = self::resolve_token_value( $tokens, '--eb-top-bar-bg', '--eb-drawer-header-bg', '#1d2327' );
		$sidebar = self::resolve_token_value( $tokens, '--eb-sidebar-bg', '--eb-drawer-header-bg', $topbar );
		$content = self::resolve_token_value( $tokens, '--eb-content-bg', '--eb-drawer-panel-bg', '#f0f0f1' );

		return array(
			'accent'  => self::resolve_token_value( $tokens, '--eb-accent', '', '#2271b1' ),
			'surface' => self::resolve_token_value( $tokens, '--eb-surface', '', '#ffffff' ),
			'text'    => self::resolve_token_value( $tokens, '--eb-text', '', '#1d2327' ),
			'topbar'  => $topbar,
			'sidebar' => $sidebar,
			'content' => $content,
		);
	}

	/**
	 * Resolve a CSS token, following one var() fallback when needed.
	 *
	 * @param array<string, string> $tokens   Token map.
	 * @param string                $primary  Primary token name.
	 * @param string                $fallback Fallback token name.
	 * @param string                $default  Default hex when unresolved.
	 * @return string
	 */
	private static function resolve_token_value( array $tokens, $primary, $fallback = '', $default = '' ) {
		$value = isset( $tokens[ $primary ] ) ? trim( $tokens[ $primary ] ) : '';

		if ( '' !== $value && 0 !== strpos( $value, 'var(' ) ) {
			return $value;
		}

		if ( $fallback && isset( $tokens[ $fallback ] ) ) {
			$fallback_value = trim( $tokens[ $fallback ] );
			if ( '' !== $fallback_value && 0 !== strpos( $fallback_value, 'var(' ) ) {
				return $fallback_value;
			}
		}

		return $default;
	}

	/**
	 * Get CSS custom properties for a preset and mode from edminboost-themes.css.
	 *
	 * @param string $preset_id Preset key.
	 * @param string $mode      light, dark, or auto.
	 * @return array<string, string>
	 */
	private static function get_css_tokens( $preset_id, $mode ) {
		$map = self::get_css_token_map();

		return isset( $map[ $preset_id ][ $mode ] ) ? $map[ $preset_id ][ $mode ] : array();
	}

	/**
	 * Parse preset/mode CSS custom properties from the theme stylesheet.
	 *
	 * @return array<string, array<string, array<string, string>>>
	 */
	private static function get_css_token_map() {
		static $map = null;

		if ( null !== $map ) {
			return $map;
		}

		$map  = array();
		$file = EDMINBOOST_PLUGIN_DIR . 'admin/css/edminboost-themes.css';

		if ( ! is_readable( $file ) ) {
			return $map;
		}

		$css = file_get_contents( $file );

		if ( ! is_string( $css ) || '' === $css ) {
			return $map;
		}

		if ( ! preg_match_all( '/((?:body\.edminboost-theme--[^{]+)+)\{([^}]*)\}/s', $css, $matches, PREG_SET_ORDER ) ) {
			return $map;
		}

		foreach ( $matches as $match ) {
			$selectors = $match[1];
			$body      = $match[2];

			if ( false === strpos( $selectors, 'edminboost-theme-mode--' ) ) {
				continue;
			}

			$props = array();

			if ( preg_match_all( '/(--eb-[a-z0-9-]+)\s*:\s*([^;]+);/', $body, $prop_matches, PREG_SET_ORDER ) ) {
				foreach ( $prop_matches as $prop_match ) {
					$props[ $prop_match[1] ] = trim( $prop_match[2] );
				}
			}

			if ( empty( $props ) ) {
				continue;
			}

			if ( ! preg_match_all(
				'/edminboost-theme--([a-z0-9-]+)\.edminboost-theme-mode--(light|dark|auto)/',
				$selectors,
				$selector_matches,
				PREG_SET_ORDER
			) ) {
				continue;
			}

			foreach ( $selector_matches as $selector_match ) {
				$map[ $selector_match[1] ][ $selector_match[2] ] = $props;
			}
		}

		return $map;
	}

	/**
	 * Whether the active theme uses user-defined custom colors.
	 *
	 * @param array|null $theme Optional merged theme settings.
	 * @return bool
	 */
	public static function uses_custom_colors( $theme = null ) {
		if ( null === $theme ) {
			$theme = self::get_settings();
		}

		return 'custom' === sanitize_key( $theme['preset'] ) || ! empty( $theme['use_custom_colors'] );
	}

	/**
	 * Build a preset definition with five canonical color tokens.
	 *
	 * @param string $name        Preset label.
	 * @param string $description Preset description.
	 * @param array  $colors      accent, surface, text, topbar, sidebar, content hex values.
	 * @return array
	 */
	private static function build_preset( $name, $description, $colors ) {
		return array(
			'name'        => $name,
			'description' => $description,
			'colors'      => $colors,
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

		$theme = wp_parse_args( $theme, self::get_defaults() );

		if ( isset( $theme['status_colors'] ) && is_array( $theme['status_colors'] ) ) {
			$theme['status_colors'] = wp_parse_args( $theme['status_colors'], self::get_defaults()['status_colors'] );
		}

		if ( ! empty( $theme['use_custom_colors'] ) && 'custom' !== sanitize_key( $theme['preset'] ) ) {
			$theme['preset'] = 'custom';
		}

		return $theme;
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
			return true;
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
		add_action( 'admin_head', array( __CLASS__, 'print_theme_extras' ), 25 );
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
		if ( ! self::uses_custom_colors( $theme ) ) {
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

		$top = self::sanitize_hex_color( $theme['custom_top'] );
		if ( $top ) {
			$rules[] = '--eb-top-bar-bg: ' . $top . ';';
		}

		$sidebar = self::sanitize_hex_color( $theme['custom_sidebar'] );
		if ( $sidebar ) {
			$rules[] = '--eb-sidebar-bg: ' . $sidebar . ';';
		}

		$content = self::sanitize_hex_color( $theme['custom_content'] );
		if ( $content ) {
			$rules[] = '--eb-content-bg: ' . $content . ';';
			$rules[] = '--eb-drawer-panel-bg: ' . $content . ';';
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
		} elseif ( ! empty( $raw['use_custom_colors'] ) ) {
			$output['preset'] = 'custom';
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

		if ( ! empty( $raw['use_custom_colors'] ) && 'custom' !== $output['preset'] ) {
			$output['preset'] = 'custom';
		}

		$output['use_custom_colors'] = ( 'custom' === $output['preset'] );
		$output['custom_accent']     = self::sanitize_hex_color( isset( $raw['custom_accent'] ) ? $raw['custom_accent'] : '' );
		$output['custom_surface']    = self::sanitize_hex_color( isset( $raw['custom_surface'] ) ? $raw['custom_surface'] : '' );
		$output['custom_text']       = self::sanitize_hex_color( isset( $raw['custom_text'] ) ? $raw['custom_text'] : '' );
		$output['custom_top']        = self::sanitize_hex_color( isset( $raw['custom_top'] ) ? $raw['custom_top'] : '' );
		$output['custom_sidebar']    = self::sanitize_hex_color( isset( $raw['custom_sidebar'] ) ? $raw['custom_sidebar'] : '' );
		$output['custom_content']    = self::sanitize_hex_color( isset( $raw['custom_content'] ) ? $raw['custom_content'] : '' );
		$output['admin_favicon_id']  = absint( $raw['admin_favicon_id'] ?? 0 );
		$output['admin_bg_color']    = self::sanitize_hex_color( $raw['admin_bg_color'] ?? '' );
		$output['admin_bg_image_id'] = absint( $raw['admin_bg_image_id'] ?? 0 );
		$output['font_size']         = max( 12, min( 20, absint( $raw['font_size'] ?? 14 ) ) );
		$output['schedule_dark_mode'] = ! empty( $raw['schedule_dark_mode'] );
		$output['dark_mode_start']   = self::sanitize_time( $raw['dark_mode_start'] ?? '18:00' );
		$output['dark_mode_end']     = self::sanitize_time( $raw['dark_mode_end'] ?? '06:00' );

		if ( isset( $raw['status_colors'] ) && is_array( $raw['status_colors'] ) ) {
			foreach ( array_keys( $output['status_colors'] ) as $status_key ) {
				if ( isset( $raw['status_colors'][ $status_key ] ) ) {
					$output['status_colors'][ $status_key ] = self::sanitize_hex_color( $raw['status_colors'][ $status_key ] );
				}
			}
		}

		return $output;
	}

	/**
	 * Sanitize HH:MM time string.
	 *
	 * @param string $time Raw time.
	 * @return string
	 */
	private static function sanitize_time( $time ) {
		$time = sanitize_text_field( wp_unslash( (string) $time ) );
		return preg_match( '/^\d{2}:\d{2}$/', $time ) ? $time : '00:00';
	}

	/**
	 * Print status colors, background, font size, and scheduled dark mode CSS.
	 *
	 * @return void
	 */
	public static function print_theme_extras() {
		if ( ! self::is_active() ) {
			return;
		}

		$theme = self::get_settings();
		$rules = array();

		if ( ! empty( $theme['font_size'] ) ) {
			$rules[] = 'body.edminboost-theme-active{font-size:' . absint( $theme['font_size'] ) . 'px;}';
		}

		if ( ! empty( $theme['admin_bg_color'] ) ) {
			$rules[] = 'body.edminboost-theme-active{background-color:' . $theme['admin_bg_color'] . ';}';
		}

		if ( ! empty( $theme['admin_bg_image_id'] ) ) {
			$url = wp_get_attachment_image_url( $theme['admin_bg_image_id'], 'full' );
			if ( $url ) {
				$rules[] = 'body.edminboost-theme-active{background-image:url(' . esc_url( $url ) . ');background-size:cover;background-attachment:fixed;}';
			}
		}

		foreach ( $theme['status_colors'] as $status => $color ) {
			if ( $color ) {
				$rules[] = 'body.edminboost-theme-active .wp-list-table tr.status-' . sanitize_key( $status ) . '{background-color:' . $color . ';}';
			}
		}

		if ( ! empty( $theme['schedule_dark_mode'] ) ) {
			$start = $theme['dark_mode_start'];
			$end   = $theme['dark_mode_end'];
			$rules[] = '@media (prefers-color-scheme: no-preference),(prefers-color-scheme: light){body.edminboost-theme-mode--auto.edminboost-theme-active{} }';
			$rules[] = 'body.edminboost-theme-mode--auto.edminboost-theme-active{--eb-schedule-start:' . esc_attr( $start ) . ';--eb-schedule-end:' . esc_attr( $end ) . ';}';
		}

		if ( empty( $rules ) ) {
			return;
		}

		echo '<style id="edminboost-theme-extras">' . wp_strip_all_tags( implode( '', $rules ) ) . '</style>';
	}
}
