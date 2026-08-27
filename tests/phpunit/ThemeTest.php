<?php
/**
 * Visual theme tests.
 *
 * @package EdminBoost
 */

/**
 * EDMINBOOST_Theme tests.
 */
class ThemeTest extends Edminboost_Test_Case {

	/**
	 * Theme presets include Phase 1 skins.
	 */
	public function test_get_presets() {
		$presets = EDMINBOOST_Theme::get_presets();

		$this->assertArrayHasKey( 'default', $presets );
		$this->assertArrayHasKey( 'midnight', $presets );
		$this->assertArrayHasKey( 'terminal', $presets );
		$this->assertArrayHasKey( 'neon-outrun', $presets );
		$this->assertArrayHasKey( 'vapor', $presets );
		$this->assertArrayHasKey( 'desert', $presets );
		$this->assertArrayHasKey( 'dracula', $presets );
		$this->assertArrayHasKey( 'nord', $presets );
		$this->assertArrayHasKey( 'solarized', $presets );
		$this->assertArrayHasKey( 'sakura', $presets );
		$this->assertArrayHasKey( 'ocean', $presets );
		$this->assertArrayHasKey( 'forest', $presets );
		$this->assertArrayHasKey( 'tron', $presets );
		$this->assertArrayHasKey( 'night-city', $presets );
		$this->assertArrayHasKey( 'pip-boy', $presets );
		$this->assertArrayHasKey( 'portal', $presets );
		$this->assertArrayHasKey( 'gotham', $presets );
		$this->assertArrayHasKey( 'citadel', $presets );
		$this->assertArrayHasKey( 'blade-noir', $presets );
		$this->assertArrayHasKey( 'hyrule', $presets );
		$this->assertArrayHasKey( 'custom', $presets );
		$this->assertArrayHasKey( 'colors', $presets['default'] );
		$this->assertSame(
			array( 'accent', 'surface', 'text', 'topbar', 'sidebar', 'content' ),
			array_keys( $presets['default']['colors'] )
		);
	}

	/**
	 * Theme settings sanitize whitelists values.
	 */
	public function test_sanitize_theme() {
		$sanitized = EDMINBOOST_Theme::sanitize(
			array(
				'preset'         => 'custom',
				'mode'           => 'dark',
				'font'           => 'mono',
				'custom_accent'  => '#00ff41',
				'custom_surface' => 'not-a-color',
				'custom_text'    => '#b8ffc8',
				'custom_top'     => '#0a0f0a',
				'custom_sidebar' => 'invalid',
				'custom_content' => '#f0f0f1',
			)
		);

		$this->assertSame( 'custom', $sanitized['preset'] );
		$this->assertSame( 'dark', $sanitized['mode'] );
		$this->assertSame( 'mono', $sanitized['font'] );
		$this->assertTrue( $sanitized['use_custom_colors'] );
		$this->assertSame( '#00ff41', $sanitized['custom_accent'] );
		$this->assertSame( '', $sanitized['custom_surface'] );
		$this->assertSame( '#b8ffc8', $sanitized['custom_text'] );
		$this->assertSame( '#0a0f0a', $sanitized['custom_top'] );
		$this->assertSame( '', $sanitized['custom_sidebar'] );
		$this->assertSame( '#f0f0f1', $sanitized['custom_content'] );
	}

	/**
	 * Legacy custom color checkbox maps to the custom preset.
	 */
	public function test_sanitize_legacy_custom_colors_flag() {
		$sanitized = EDMINBOOST_Theme::sanitize(
			array(
				'preset'            => 'terminal',
				'use_custom_colors' => '1',
			)
		);

		$this->assertSame( 'custom', $sanitized['preset'] );
		$this->assertTrue( $sanitized['use_custom_colors'] );
	}

	/**
	 * Invalid preset falls back to default.
	 */
	public function test_sanitize_invalid_preset() {
		$sanitized = EDMINBOOST_Theme::sanitize(
			array(
				'preset' => 'star-wars',
			)
		);

		$this->assertSame( 'default', $sanitized['preset'] );
	}

	/**
	 * Preview colors follow the active light/dark mode for each preset.
	 */
	public function test_resolve_preview_colors_tron_modes() {
		$light = EDMINBOOST_Theme::resolve_preview_colors( 'tron', 'light' );
		$dark  = EDMINBOOST_Theme::resolve_preview_colors( 'tron', 'dark' );

		$this->assertSame( '#d0f0ff', $light['content'] );
		$this->assertSame( '#0099cc', $light['accent'] );
		$this->assertSame( '#0a0a12', $dark['content'] );
		$this->assertSame( '#00d4ff', $dark['accent'] );
	}

	/**
	 * JS preset catalog includes mode-specific preview colors.
	 */
	public function test_get_presets_for_js_includes_colors_by_mode() {
		$presets = EDMINBOOST_Theme::get_presets_for_js();

		$this->assertArrayHasKey( 'colorsByMode', $presets['tron'] );
		$this->assertArrayHasKey( 'light', $presets['tron']['colorsByMode'] );
		$this->assertArrayHasKey( 'dark', $presets['tron']['colorsByMode'] );
		$this->assertSame( '#d0f0ff', $presets['tron']['colorsByMode']['light']['content'] );
	}

	/**
	 * Other presets keep their palette when custom is the active saved theme.
	 */
	public function test_resolve_preview_colors_ignores_active_custom_for_other_presets() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'theme' => array(
						'preset'        => 'custom',
						'custom_accent' => '#123456',
					),
				),
			)
		);

		$tron_dark = EDMINBOOST_Theme::resolve_preview_colors( 'tron', 'dark' );

		$this->assertSame( '#00d4ff', $tron_dark['accent'] );
		$this->assertSame( '#0a0a12', $tron_dark['content'] );
	}

	/**
	 * Body classes reflect stored theme.
	 */
	public function test_get_body_classes() {
		$this->seed_settings(
			array(
				'command_center' => array(
					'theme' => array(
						'preset' => 'vapor',
						'mode'   => 'auto',
						'font'   => 'rounded',
					),
				),
			)
		);

		$classes = EDMINBOOST_Theme::get_body_classes();

		$this->assertContains( 'edminboost-theme-active', $classes );
		$this->assertContains( 'edminboost-theme--vapor', $classes );
		$this->assertContains( 'edminboost-theme-mode--auto', $classes );
		$this->assertContains( 'edminboost-theme-font--rounded', $classes );
	}

	/**
	 * Theme is active on wp-admin when the plugin is enabled.
	 */
	public function test_is_active_in_admin() {
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true );
		}

		$this->seed_settings( array( 'enabled' => true ) );

		$this->assertTrue( EDMINBOOST_Theme::is_active() );
	}

	/**
	 * Theme is inactive when the plugin is disabled.
	 */
	public function test_is_active_when_plugin_disabled() {
		$this->seed_settings( array( 'enabled' => false ) );

		$this->assertFalse( EDMINBOOST_Theme::is_active() );
	}
}
