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
	}

	/**
	 * Theme settings sanitize whitelists values.
	 */
	public function test_sanitize_theme() {
		$sanitized = EDMINBOOST_Theme::sanitize(
			array(
				'preset'            => 'terminal',
				'mode'              => 'dark',
				'font'              => 'mono',
				'use_custom_colors' => '1',
				'custom_accent'     => '#00ff41',
				'custom_surface'    => 'not-a-color',
				'custom_text'       => '#b8ffc8',
			)
		);

		$this->assertSame( 'terminal', $sanitized['preset'] );
		$this->assertSame( 'dark', $sanitized['mode'] );
		$this->assertSame( 'mono', $sanitized['font'] );
		$this->assertTrue( $sanitized['use_custom_colors'] );
		$this->assertSame( '#00ff41', $sanitized['custom_accent'] );
		$this->assertSame( '', $sanitized['custom_surface'] );
		$this->assertSame( '#b8ffc8', $sanitized['custom_text'] );
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
}
