<?php
/**
 * Premium build: scheduled dark mode theme extras (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pro visual theme runtime helpers.
 */
class EDMINBOOST_Theme_Pro {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'edminboost_theme_extras_css_rules', array( __CLASS__, 'append_scheduled_dark_mode_rules' ), 10, 2 );
	}

	/**
	 * Append scheduled dark mode CSS when enabled.
	 *
	 * @param string $css   Accumulated CSS rules.
	 * @param array  $theme Theme settings.
	 * @return string
	 */
	public static function append_scheduled_dark_mode_rules( $css, $theme ) {
		if ( empty( $theme['schedule_dark_mode'] ) || ! EDMINBOOST_Plan_Licensing::is_active() ) {
			return $css;
		}

		$start = isset( $theme['dark_mode_start'] ) ? $theme['dark_mode_start'] : '18:00';
		$end   = isset( $theme['dark_mode_end'] ) ? $theme['dark_mode_end'] : '06:00';

		$css .= '@media (prefers-color-scheme: no-preference),(prefers-color-scheme: light){body.edminboost-theme-mode--auto.edminboost-theme-active{} }';
		$css .= 'body.edminboost-theme-mode--auto.edminboost-theme-active{--eb-schedule-start:' . esc_attr( $start ) . ';--eb-schedule-end:' . esc_attr( $end ) . ';}';

		return $css;
	}
}
