<?php
/**
 * Premium build: extended visual theme presets (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pro theme preset catalog.
 */
class EDMINBOOST_Theme_Presets_Pro {

	/**
	 * Register preset catalog filter.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'edminboost_theme_presets', array( __CLASS__, 'merge_presets' ), 10, 1 );
	}

	/**
	 * @param array $presets Core theme presets from the free build.
	 * @return array
	 */
	public static function merge_presets( $presets ) {
		if ( ! is_array( $presets ) ) {
			$presets = array();
		}

		$extended = self::get_extended_presets();
		// Insert before custom so custom stays last.
		if ( isset( $presets['custom'] ) ) {
			$custom = $presets['custom'];
			unset( $presets['custom'] );
			return array_merge( $presets, $extended, array( 'custom' => $custom ) );
		}

		return array_merge( $presets, $extended );
	}

	/**
	 * @return array<string, array>
	 */
	public static function get_extended_presets() {
		return array(
'neon-outrun' => EDMINBOOST_Theme::build_preset(
				__( 'Neon Outrun', 'edminboost-admin-customization' ),
				__( 'Synthwave magenta and cyan on dark purple.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#ff2bd6',
					'surface' => '#1a0b2e',
					'text'    => '#00f0ff',
					'topbar'  => '#0f061a',
					'sidebar' => '#0f061a',
					'content' => '#1a0b2e',
				)
			),
			'vapor'       => EDMINBOOST_Theme::build_preset(
				__( 'Vapor', 'edminboost-admin-customization' ),
				__( 'Vaporwave pastels with readable contrast.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#ff6ad5',
					'surface' => '#e8e0ff',
					'text'    => '#2d1b4e',
					'topbar'  => '#2d1b4e',
					'sidebar' => '#2d1b4e',
					'content' => '#2d1b4e',
				)
			),
			'desert'      => EDMINBOOST_Theme::build_preset(
				__( 'Desert', 'edminboost-admin-customization' ),
				__( 'Warm sand tones with copper accents.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#c87941',
					'surface' => '#f5ebe0',
					'text'    => '#3d2914',
					'topbar'  => '#3d2914',
					'sidebar' => '#3d2914',
					'content' => '#2a1c0e',
				)
			),
			'dracula'     => EDMINBOOST_Theme::build_preset(
				__( 'Dracula', 'edminboost-admin-customization' ),
				__( 'Dracula-inspired purple accents on inky charcoal.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#bd93f9',
					'surface' => '#282a36',
					'text'    => '#f8f8f2',
					'topbar'  => '#21222c',
					'sidebar' => '#21222c',
					'content' => '#282a36',
				)
			),
			'nord'        => EDMINBOOST_Theme::build_preset(
				__( 'Nord', 'edminboost-admin-customization' ),
				__( 'Arctic frost blues on polar night surfaces.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#88c0d0',
					'surface' => '#2e3440',
					'text'    => '#eceff4',
					'topbar'  => '#242933',
					'sidebar' => '#242933',
					'content' => '#2e3440',
				)
			),
			'solarized'   => EDMINBOOST_Theme::build_preset(
				__( 'Solarized', 'edminboost-admin-customization' ),
				__( 'Solarized-inspired cream base with teal accents.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#268bd2',
					'surface' => '#fdf6e3',
					'text'    => '#657b83',
					'topbar'  => '#073642',
					'sidebar' => '#073642',
					'content' => '#002b36',
				)
			),
			'sakura'      => EDMINBOOST_Theme::build_preset(
				__( 'Sakura', 'edminboost-admin-customization' ),
				__( 'Cherry blossom pinks on soft blush surfaces.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#e8879a',
					'surface' => '#fff5f7',
					'text'    => '#4a2030',
					'topbar'  => '#4a2030',
					'sidebar' => '#4a2030',
					'content' => '#fce8ec',
				)
			),
			'ocean'       => EDMINBOOST_Theme::build_preset(
				__( 'Ocean', 'edminboost-admin-customization' ),
				__( 'Deep-sea navy with luminous aqua highlights.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#3dd6d0',
					'surface' => '#0a1628',
					'text'    => '#c8e6f5',
					'topbar'  => '#061018',
					'sidebar' => '#061018',
					'content' => '#0a1628',
				)
			),
			'forest'      => EDMINBOOST_Theme::build_preset(
				__( 'Forest', 'edminboost-admin-customization' ),
				__( 'Moss greens and woodland tones for a calm admin.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#6dbf6d',
					'surface' => '#1a2e1f',
					'text'    => '#d4e8d0',
					'topbar'  => '#0f1a12',
					'sidebar' => '#0f1a12',
					'content' => '#1a2e1f',
				)
			),
			'tron'        => EDMINBOOST_Theme::build_preset(
				__( 'Tron', 'edminboost-admin-customization' ),
				__( 'Tron-inspired electric cyan glowing on deep black grid.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#00d4ff',
					'surface' => '#0a0a12',
					'text'    => '#b8e8ff',
					'topbar'  => '#050508',
					'sidebar' => '#050508',
					'content' => '#0a0a12',
				)
			),
			'night-city'  => EDMINBOOST_Theme::build_preset(
				__( 'Night City', 'edminboost-admin-customization' ),
				__( 'Cyberpunk-inspired neon yellow and cyan on rain-soaked dark.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#fcee0a',
					'surface' => '#0d0d0d',
					'text'    => '#00f0ff',
					'topbar'  => '#080808',
					'sidebar' => '#080808',
					'content' => '#0d0d0d',
				)
			),
			'pip-boy'     => EDMINBOOST_Theme::build_preset(
				__( 'Pip-Boy', 'edminboost-admin-customization' ),
				__( 'Fallout-inspired amber CRT phosphor on wasteland green-black.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#ffb000',
					'surface' => '#1a2e1a',
					'text'    => '#b8d4a0',
					'topbar'  => '#0f1a0f',
					'sidebar' => '#0f1a0f',
					'content' => '#1a2e1a',
				)
			),
			'portal'      => EDMINBOOST_Theme::build_preset(
				__( 'Portal', 'edminboost-admin-customization' ),
				__( 'Portal-inspired Aperture orange with companion-core blue accents.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#ff7b00',
					'surface' => '#f5f5f5',
					'text'    => '#0099cc',
					'topbar'  => '#333333',
					'sidebar' => '#333333',
					'content' => '#eaeaea',
				)
			),
			'gotham'      => EDMINBOOST_Theme::build_preset(
				__( 'Gotham', 'edminboost-admin-customization' ),
				__( 'Batman-inspired charcoal shadows with striking gold highlights.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#f0c040',
					'surface' => '#1a1a1a',
					'text'    => '#e8e8e8',
					'topbar'  => '#0d0d0d',
					'sidebar' => '#0d0d0d',
					'content' => '#1a1a1a',
				)
			),
			'citadel'     => EDMINBOOST_Theme::build_preset(
				__( 'Citadel', 'edminboost-admin-customization' ),
				__( 'Mass Effect-inspired cerulean blues on deep space navy.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#4fc3f7',
					'surface' => '#0d1b2a',
					'text'    => '#b8d4e8',
					'topbar'  => '#061018',
					'sidebar' => '#061018',
					'content' => '#0d1b2a',
				)
			),
			'blade-noir'  => EDMINBOOST_Theme::build_preset(
				__( 'Blade Noir', 'edminboost-admin-customization' ),
				__( 'Blade Runner-inspired neon orange and teal in a rainy future city.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#ff6b35',
					'surface' => '#1a1a2e',
					'text'    => '#2ec4b6',
					'topbar'  => '#0f0f18',
					'sidebar' => '#0f0f18',
					'content' => '#1a1a2e',
				)
			),
			'hyrule'      => EDMINBOOST_Theme::build_preset(
				__( 'Hyrule', 'edminboost-admin-customization' ),
				__( 'Zelda-inspired hero green and Triforce gold on parchment stone.', 'edminboost-admin-customization' ),
				array(
					'accent'  => '#2d6a4f',
					'surface' => '#f5f0e0',
					'text'    => '#c8a032',
					'topbar'  => '#3d2914',
					'sidebar' => '#3d2914',
					'content' => '#e8e0c8',
				)
			)
		);
	}
}
