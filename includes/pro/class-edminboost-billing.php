<?php
/**
 * Premium build: in-plugin Billing page catalog (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Billing page plan catalog and external upgrade URL.
 */
class EDMINBOOST_Billing {

public static function get_upgrade_url() {
		/**
		 * Filter the external upgrade / pricing page URL.
		 *
		 * @param string $url Default upgrade URL.
		 */
		$url = (string) apply_filters( 'edminboost_upgrade_url', EDMINBOOST_UPGRADE_URL );
		$url = esc_url_raw( $url );

		if ( '' === $url ) {
			return EDMINBOOST_UPGRADE_URL;
		}

		$scheme = wp_parse_url( $url, PHP_URL_SCHEME );
		if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
			return EDMINBOOST_UPGRADE_URL;
		}

		return $url;
	}

	/**
	 * Catalog counts for Billing page copy.
	 *
	 * @return array{scenario_presets: int, theme_skins: int, badge_sources: int}
	 */
	private static function get_catalog_counts() {
		$scenario_presets = 0;

		foreach ( EDMINBOOST_Command_Center::get_system_presets() as $preset ) {
			if ( isset( $preset['category'] ) && 'scenario' === $preset['category'] ) {
				++$scenario_presets;
			}
		}

		$theme_presets = EDMINBOOST_Theme::get_presets();
		$theme_skins   = count( $theme_presets );

		if ( isset( $theme_presets['custom'] ) ) {
			--$theme_skins;
		}

		$badge_source_options = EDMINBOOST_Command_Center::get_badge_sources();
		$badge_sources        = count( $badge_source_options );

		if ( isset( $badge_source_options[''] ) ) {
			--$badge_sources;
		}

		return array(
			'scenario_presets' => $scenario_presets,
			'theme_skins'      => max( 0, $theme_skins ),
			'badge_sources'    => max( 0, $badge_sources ),
		);
	}

	/**
	 * Available subscription plans for the Billing page.
	 *
	 * @return array<string, array{id: string, name: string, price: int, price_label: string, sites: int, sites_label: string, description: string, features: string[], featured: bool}> Sites is 0 for unlimited.
	 */
	public static function get_plans() {
		$counts = self::get_catalog_counts();

		return array(
			'free'   => array(
				'id'          => 'free',
				'name'        => __( 'Free', EDMINBOOST_TEXT_DOMAIN ),
				'price'       => 0,
				'price_label' => __( '$0', EDMINBOOST_TEXT_DOMAIN ),
				'sites'       => 0,
				'sites_label' => __( 'Unlimited sites', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Core Command Center tools on unlimited WordPress sites.', EDMINBOOST_TEXT_DOMAIN ),
				'features'    => array(
					__( 'Dashboard setup wizard', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Top bar builder (redirect links)', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Friend\'s Website and Family Member\'s Site layout presets', EDMINBOOST_TEXT_DOMAIN ),
					__( 'By-role layout presets', EDMINBOOST_TEXT_DOMAIN ),
					__( 'One saved custom layout preset', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Default, Midnight, and Terminal theme presets', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Menu Studio sidebar reorder and hide', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Productivity, security, and performance tools', EDMINBOOST_TEXT_DOMAIN ),
				),
				'featured'    => false,
			),
			'pro'    => array(
				'id'          => 'pro',
				'name'        => __( 'Pro', EDMINBOOST_TEXT_DOMAIN ),
				'price'       => 49,
				'price_label' => __( '$49', EDMINBOOST_TEXT_DOMAIN ),
				'sites'       => 1,
				'sites_label' => __( '1 site', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Premium admin customization for one production site.', EDMINBOOST_TEXT_DOMAIN ),
				'features'    => array(
					__( 'Everything in Free', EDMINBOOST_TEXT_DOMAIN ),
					sprintf(
						/* translators: %d: number of by use case layout presets beyond Friend and Family */
						__( '%d additional by use case layout presets', EDMINBOOST_TEXT_DOMAIN ),
						max( 0, $counts['scenario_presets'] - 2 )
					),
					__( 'Unlimited saved custom layouts', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Role-based menu visibility matrix', EDMINBOOST_TEXT_DOMAIN ),
					sprintf(
						/* translators: %d: number of visual theme skins beyond the free set */
						__( '%d additional visual theme skins', EDMINBOOST_TEXT_DOMAIN ),
						max( 0, $counts['theme_skins'] - 3 )
					),
					__( 'Scheduled dark mode', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Slide-out drawer panels and live badge counters', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Full-screen and custom drawer widths, badge style, animation speed, glassmorphism', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Role-based login and logout redirects', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Custom sidebar links and icon/text display modes', EDMINBOOST_TEXT_DOMAIN ),
					__( 'White-label branding', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Settings export and import', EDMINBOOST_TEXT_DOMAIN ),
				),
				'featured'    => true,
			),
			'agency' => array(
				'id'          => 'agency',
				'name'        => __( 'Agency', EDMINBOOST_TEXT_DOMAIN ),
				'price'       => 99,
				'price_label' => __( '$99', EDMINBOOST_TEXT_DOMAIN ),
				'sites'       => 10,
				'sites_label' => __( '10 sites', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Deploy EdminBoost across a client portfolio.', EDMINBOOST_TEXT_DOMAIN ),
				'features'    => array(
					__( 'Everything in Pro', EDMINBOOST_TEXT_DOMAIN ),
					__( '10 site license pack', EDMINBOOST_TEXT_DOMAIN ),
				),
				'featured'    => false,
			),
		);
	}

	/**
	 * Feature comparison rows for the Billing page table.
	 *
	 * Each row is either a section heading (`type` = heading) or a feature row
	 * (`type` = row) with `free`, `pro`, and `agency` cell values. Booleans render
	 * as included/not-included; strings render as text.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_comparison_rows() {
		$counts = self::get_catalog_counts();

		$additional_scenario_presets = max( 0, $counts['scenario_presets'] - 2 );
		$additional_theme_skins      = max( 0, $counts['theme_skins'] - 3 );

		return array(
			array(
				'type'  => 'heading',
				'label' => __( 'Plan basics', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Annual price', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => __( '$0', EDMINBOOST_TEXT_DOMAIN ),
				'pro'    => __( '$49', EDMINBOOST_TEXT_DOMAIN ),
				'agency' => __( '$99', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Site license', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => __( 'Unlimited', EDMINBOOST_TEXT_DOMAIN ),
				'pro'    => __( '1 site', EDMINBOOST_TEXT_DOMAIN ),
				'agency' => __( '10 sites', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Layout presets', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Friend & Family presets', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => __( 'Friend\'s Website and Family Member\'s Site layouts', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => true,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'By-role layout presets', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => true,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Use-case layout presets', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => sprintf(
					/* translators: %d: number of additional by use case layout presets */
					__( '%d scenario presets beyond Friend & Family', EDMINBOOST_TEXT_DOMAIN ),
					$additional_scenario_presets
				),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Saved custom layouts', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => __( '1', EDMINBOOST_TEXT_DOMAIN ),
				'pro'    => __( 'Unlimited', EDMINBOOST_TEXT_DOMAIN ),
				'agency' => __( 'Unlimited', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Role visibility matrix', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Top Bar', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Top bar (redirect links)', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => true,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Slide-out drawer panels', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Live badge counters', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => sprintf(
					/* translators: %d: number of live badge counter sources */
					__( '%d local counter sources', EDMINBOOST_TEXT_DOMAIN ),
					$counts['badge_sources']
				),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Custom drawer widths', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => __( 'Full-screen and custom panel sizes', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Badge style & effects', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => __( 'Badge style, animation speed, glassmorphism', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Menu Studio', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Sidebar reorder and hide', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => true,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Custom sidebar links', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Icon and text display modes', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Theme (Appearance)', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Core theme presets', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => __( 'Default, Midnight, and Terminal', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => true,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Premium theme skins', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => sprintf(
					/* translators: %d: number of additional visual theme skins */
					__( '%d additional skins beyond the free set', EDMINBOOST_TEXT_DOMAIN ),
					$additional_theme_skins
				),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Scheduled dark mode', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Tools and branding', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Utility tool modules', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => __( 'Productivity, security, and performance tools', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => true,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Login redirects', EDMINBOOST_TEXT_DOMAIN ),
				'detail' => __( 'Role-based login and logout redirects', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'White-label branding', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
			array(
				'type'   => 'row',
				'label'  => __( 'Settings export and import', EDMINBOOST_TEXT_DOMAIN ),
				'free'   => false,
				'pro'    => true,
				'agency' => true,
			),
		);
	}

	/**
	 * Active billing plan for the current site.
	 *
	 * @return string Plan ID (`free`, `pro`, or `agency`).
	 */
}
