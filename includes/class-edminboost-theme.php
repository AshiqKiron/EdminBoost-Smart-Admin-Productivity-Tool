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
			'light' => __( 'Light', 'edminboost-admin-customization' ),
			'dark'  => __( 'Dark', 'edminboost-admin-customization' ),
			'auto'  => __( 'Auto (system)', 'edminboost-admin-customization' ),
		);
	}

	/**
	 * Font stack options (system stacks only — no remote fonts).
	 *
	 * @return array<string, string>
	 */
	public static function get_fonts() {
		return array(
			'inherit'   => __( 'WordPress default', 'edminboost-admin-customization' ),
			'system'    => __( 'System UI', 'edminboost-admin-customization' ),
			'arial'     => __( 'Arial / Helvetica', 'edminboost-admin-customization' ),
			'verdana'   => __( 'Verdana', 'edminboost-admin-customization' ),
			'tahoma'    => __( 'Tahoma', 'edminboost-admin-customization' ),
			'trebuchet' => __( 'Trebuchet MS', 'edminboost-admin-customization' ),
			'lucida'    => __( 'Lucida Sans', 'edminboost-admin-customization' ),
			'palatino'  => __( 'Palatino', 'edminboost-admin-customization' ),
			'humanist'  => __( 'Humanist sans', 'edminboost-admin-customization' ),
			'mono'      => __( 'Monospace', 'edminboost-admin-customization' ),
			'serif'     => __( 'Serif', 'edminboost-admin-customization' ),
			'rounded'   => __( 'Rounded UI', 'edminboost-admin-customization' ),
		);
	}

	/**
	 * Labels for the five preset color tokens shown in the theme UI.
	 *
	 * @return array<string, string>
	 */
	public static function get_color_labels() {
		return array(
			'accent'  => __( 'Accent', 'edminboost-admin-customization' ),
			'surface' => __( 'Surface', 'edminboost-admin-customization' ),
			'text'    => __( 'Text', 'edminboost-admin-customization' ),
			'topbar'   => __( 'Top bar', 'edminboost-admin-customization' ),
			'sidebar'  => __( 'Sidebar', 'edminboost-admin-customization' ),
			'content'  => __( 'Content area', 'edminboost-admin-customization' ),
		);
	}

	/**
	 * Visual theme presets (colors only).
	 *
	 * @return array<string, array>
	 */
	public static function get_presets() {
		$presets = array(
			'default'     => self::build_preset(
				__( 'Default', 'edminboost-admin-customization' ),
				__( 'WordPress-aligned blues and neutrals.', 'edminboost-admin-customization' ),
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
				__( 'Midnight', 'edminboost-admin-customization' ),
				__( 'Dark neutral surfaces with soft violet accents.', 'edminboost-admin-customization' ),
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
				__( 'Terminal', 'edminboost-admin-customization' ),
				__( 'Matrix-inspired green on deep black.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#00ff41',
					'surface' => '#0a0f0a',
					'text'    => '#b8ffc8',
					'topbar'  => '#050805',
					'sidebar' => '#050805',
					'content' => '#0a0f0a',
				)
			),
			'custom'      => self::build_preset(
				__( 'Custom', 'edminboost-admin-customization' ),
				__( 'Define your own accent, surface, text, top bar, sidebar, and content area colors.', 'edminboost-admin-customization' ),
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

		/**
		 * Filter visual theme presets (premium builds merge extended skins).
		 *
		 * @param array<string, array> $presets Preset catalog.
		 */
		return apply_filters( 'edminboost_theme_presets', $presets );
	}

	/**
	 * Preset catalog for admin JavaScript (colors + labels).
	 *
	 * @return array<string, array>
	 */
	public static function get_presets_for_js() {
		$presets = array();

		foreach ( self::get_presets() as $preset_id => $preset ) {
			if ( ! EDMINBOOST_Plan::include_preset_in_ui( $preset_id, 'theme' ) ) {
				continue;
			}

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

		$preset_id = sanitize_key( $preset_id );
		$resolve_custom_palette = 'custom' === $preset_id
			|| ( ! empty( $theme['use_custom_colors'] ) && sanitize_key( $theme['preset'] ) === $preset_id );

		if ( $resolve_custom_palette ) {
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

		$mode = sanitize_key( $mode );
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
	public static function build_preset( $name, $description, $colors ) {
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

		self::attach_inline_styles();
	}

	/**
	 * Attach dynamic theme CSS via wp_add_inline_style().
	 *
	 * @return void
	 */
	private static function attach_inline_styles() {
		$chunks = array();

		$custom_rules = self::get_custom_color_override_rules();
		if ( '' !== $custom_rules ) {
			$chunks[] = $custom_rules;
		}

		if ( is_admin() ) {
			$bridge_rules = self::get_wp_admin_theme_bridge_rules();
			if ( '' !== $bridge_rules ) {
				$chunks[] = $bridge_rules;
			}

			$extras_rules = self::get_theme_extras_rules();
			if ( '' !== $extras_rules ) {
				$chunks[] = $extras_rules;
			}
		}

		if ( empty( $chunks ) ) {
			return;
		}

		wp_add_inline_style( 'edminboost-themes', wp_strip_all_tags( implode( '', $chunks ) ) );
	}

	/**
	 * Convert a hex color to an RGB triplet for WordPress admin CSS variables.
	 *
	 * @param string $hex Hex color.
	 * @return string Comma-separated R, G, B values or empty string.
	 */
	public static function hex_to_rgb_triplet( $hex ) {
		$hex = self::sanitize_hex_color( $hex );

		if ( ! $hex ) {
			return '';
		}

		$hex = ltrim( $hex, '#' );

		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if ( 6 !== strlen( $hex ) ) {
			return '';
		}

		return sprintf(
			'%d, %d, %d',
			hexdec( substr( $hex, 0, 2 ) ),
			hexdec( substr( $hex, 2, 2 ) ),
			hexdec( substr( $hex, 4, 2 ) )
		);
	}

	/**
	 * Build WordPress admin theme CSS variable declarations for a resolved accent pair.
	 *
	 * @param string $accent Accent hex color.
	 * @param string $hover  Hover/darker accent hex color.
	 * @return string CSS custom property declarations.
	 */
	public static function build_wp_admin_theme_var_rules( $accent, $hover ) {
		$accent = self::sanitize_hex_color( $accent );
		$hover  = self::sanitize_hex_color( $hover );

		if ( ! $accent ) {
			return '';
		}

		if ( ! $hover ) {
			$hover = self::darken_hex( $accent, 12 );
		}

		$rgb = self::hex_to_rgb_triplet( $accent );

		if ( ! $rgb ) {
			return '';
		}

		return '--wp-admin-theme-color:' . $accent . ';'
			. '--wp-admin-theme-color--rgb:' . $rgb . ';'
			. '--wp-admin-theme-color-darker-10:' . $hover . ';'
			. '--wp-admin-theme-color-darker-20:' . $hover . ';';
	}

	/**
	 * Resolve CSS rules bridging EdminBoost accents to WordPress admin theme variables.
	 *
	 * @param array|null $theme Optional merged theme settings.
	 * @return string CSS rules.
	 */
	public static function get_wp_admin_theme_bridge_rules( $theme = null ) {
		if ( null === $theme ) {
			$theme = self::get_settings();
		} else {
			$theme = wp_parse_args( $theme, self::get_defaults() );
		}

		$preset_id = sanitize_key( $theme['preset'] );
		$mode      = sanitize_key( $theme['mode'] );
		$rules     = array();

		if ( 'auto' === $mode ) {
			$light_colors = self::resolve_preview_colors( $preset_id, 'light', $theme );
			$dark_colors  = self::resolve_preview_colors( $preset_id, 'dark', $theme );
			$light_rules  = self::build_wp_admin_theme_var_rules(
				$light_colors['accent'],
				self::resolve_accent_hover_hex( $preset_id, 'light', $light_colors['accent'], $theme )
			);
			$dark_rules   = self::build_wp_admin_theme_var_rules(
				$dark_colors['accent'],
				self::resolve_accent_hover_hex( $preset_id, 'dark', $dark_colors['accent'], $theme )
			);

			if ( $light_rules ) {
				$rules[] = '@media (prefers-color-scheme:light),(prefers-color-scheme:no-preference){body.edminboost-theme-active{' . $light_rules . '}}';
			}

			if ( $dark_rules ) {
				$rules[] = '@media (prefers-color-scheme:dark){body.edminboost-theme-active{' . $dark_rules . '}}';
			}
		} else {
			$colors    = self::resolve_preview_colors( $preset_id, $mode, $theme );
			$var_rules = self::build_wp_admin_theme_var_rules(
				$colors['accent'],
				self::resolve_accent_hover_hex( $preset_id, $mode, $colors['accent'], $theme )
			);

			if ( $var_rules ) {
				$rules[] = 'body.edminboost-theme-active{' . $var_rules . '}';
			}
		}

		return implode( '', $rules );
	}

	/**
	 * Resolve the accent hover color for a preset and mode.
	 *
	 * @param string $preset_id Preset key.
	 * @param string $mode      light, dark, or auto.
	 * @param string $accent    Resolved accent hex color.
	 * @param array  $theme     Merged theme settings.
	 * @return string
	 */
	private static function resolve_accent_hover_hex( $preset_id, $mode, $accent, $theme ) {
		if ( self::uses_custom_colors( $theme ) ) {
			return self::darken_hex( $accent, 12 );
		}

		$tokens = self::get_css_tokens( $preset_id, $mode );

		if ( ! empty( $tokens['--eb-accent-hover'] ) ) {
			$hover = trim( $tokens['--eb-accent-hover'] );

			if ( preg_match( '/^#[0-9a-f]{3,8}$/i', $hover ) ) {
				return strtolower( $hover );
			}
		}

		return self::darken_hex( $accent, 12 );
	}

	/**
	 * Darken a hex color by a percentage.
	 *
	 * @param string $hex     Hex color.
	 * @param int    $percent Percentage to darken (0-100).
	 * @return string
	 */
	private static function darken_hex( $hex, $percent = 12 ) {
		$hex = self::sanitize_hex_color( $hex );

		if ( ! $hex ) {
			return '';
		}

		$hex     = ltrim( $hex, '#' );
		$percent = max( 0, min( 100, absint( $percent ) ) );
		$factor  = ( 100 - $percent ) / 100;

		return sprintf(
			'#%02x%02x%02x',
			max( 0, (int) round( hexdec( substr( $hex, 0, 2 ) ) * $factor ) ),
			max( 0, (int) round( hexdec( substr( $hex, 2, 2 ) ) * $factor ) ),
			max( 0, (int) round( hexdec( substr( $hex, 4, 2 ) ) * $factor ) )
		);
	}

	/**
	 * Resolve CSS rules for custom color overrides.
	 *
	 * @return string CSS rules.
	 */
	public static function get_custom_color_override_rules() {
		if ( ! self::is_active() ) {
			return '';
		}

		$theme = self::get_settings();
		if ( ! self::uses_custom_colors( $theme ) ) {
			return '';
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
			return '';
		}

		return 'body.edminboost-theme-active{' . implode( '', $rules ) . '}';
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
	 * Resolve status colors, background, font size, and scheduled dark mode CSS.
	 *
	 * @return string CSS rules.
	 */
	public static function get_theme_extras_rules() {
		if ( ! self::is_active() ) {
			return '';
		}

		$theme = self::get_settings();
		$rules = array();

		if ( ! empty( $theme['font_size'] ) ) {
			$rules[] = 'body.edminboost-theme-active{font-size:' . absint( $theme['font_size'] ) . 'px;}';
		}

		if ( ! empty( $theme['admin_bg_color'] ) ) {
			$rules[] = 'body.edminboost-theme-active,body.edminboost-theme-active #wpwrap{background-color:' . $theme['admin_bg_color'] . ';}';
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

		$css = empty( $rules ) ? '' : implode( '', $rules );

		/**
		 * Filter theme extras CSS (scheduled dark mode appended by premium package when active).
		 *
		 * @param string $css   CSS rules.
		 * @param array  $theme Theme settings.
		 */
		return apply_filters( 'edminboost_theme_extras_css_rules', $css, $theme );
	}
}
