<?php
/**
 * Premium build bootstrap (direct download / Freemius). Omitted from WordPress.org zips.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'EDMINBOOST_PREMIUM_BUILD' ) ) {
	define( 'EDMINBOOST_PREMIUM_BUILD', true );
}

if ( ! defined( 'EDMINBOOST_UPGRADE_URL' ) ) {
	define( 'EDMINBOOST_UPGRADE_URL', 'https://asphaltthemes.com/edminboost' );
}

/** Freemius product ID. */
const EDMINBOOST_FREEMIUS_PRODUCT_ID = 40023;

/** Freemius public key (safe to ship in the plugin). */
const EDMINBOOST_FREEMIUS_PUBLIC_KEY = 'pk_7002db6d2d9fd6bcf148befc9f57d';

/** Freemius plan IDs — must match the Freemius dashboard. */
const EDMINBOOST_FREEMIUS_PLAN_PRO    = 68858;
const EDMINBOOST_FREEMIUS_PLAN_AGENCY = 68859;

/**
 * Resolve the path to the Freemius SDK start.php.
 *
 * @return string Empty when the SDK is not present.
 */
function edminboost_premium_freemius_sdk_path() {
	$plugin_dir = dirname( __DIR__, 2 );

	$candidates = array(
		__DIR__ . '/freemius/start.php',
		__DIR__ . '/vendor/freemius/start.php',
		$plugin_dir . '/vendor/pro/freemius/start.php',
	);

	foreach ( $candidates as $path ) {
		if ( is_readable( $path ) ) {
			return $path;
		}
	}

	return '';
}

/**
 * Freemius SDK instance for the premium build.
 *
 * @return Freemius|null
 */
function edminboost_fs() {
	global $edminboost_fs;

	if ( isset( $edminboost_fs ) ) {
		return $edminboost_fs;
	}

	$sdk_path = edminboost_premium_freemius_sdk_path();
	if ( '' === $sdk_path ) {
		return null;
	}

	require_once $sdk_path;

	$plugin_slug = defined( 'EDMINBOOST_PLUGIN_SLUG' ) ? EDMINBOOST_PLUGIN_SLUG : 'edminboost-admin-customization';
	$main_file   = defined( 'EDMINBOOST_PLUGIN_FILE' ) ? EDMINBOOST_PLUGIN_FILE : dirname( __DIR__, 2 ) . '/edminboost-admin-customization.php';

	$edminboost_fs = fs_dynamic_init(
		array(
			'id'                  => EDMINBOOST_FREEMIUS_PRODUCT_ID,
			'slug'                => $plugin_slug,
			'type'                => 'plugin',
			'public_key'          => EDMINBOOST_FREEMIUS_PUBLIC_KEY,
			'is_premium'          => true,
			'premium_suffix'      => 'premium',
			'has_premium_version' => true,
			'has_paid_plans'      => true,
			'menu'                => array(
				'slug'       => $plugin_slug,
				'first-path' => 'admin.php?page=' . $plugin_slug,
				'account'    => true,
				'contact'    => false,
				'support'    => false,
			),
			'plugin_main_file_path' => $main_file,
		)
	);

	return $edminboost_fs;
}

/**
 * Map an active Freemius license to EdminBoost billing plan IDs.
 *
 * @param string $plan Default plan from earlier filters.
 * @return string `free`, `pro`, or `agency`.
 */
function edminboost_premium_active_billing_plan( $plan ) {
	$fs = edminboost_fs();
	if ( ! is_object( $fs ) || ! $fs->is_registered() ) {
		return 'free';
	}

	if ( ! $fs->can_use_premium_code() ) {
		return 'free';
	}

	$fs_plan = $fs->get_plan();
	if ( ! is_object( $fs_plan ) || ! isset( $fs_plan->id ) ) {
		return 'pro';
	}

	$plan_id = (int) $fs_plan->id;

	if ( EDMINBOOST_FREEMIUS_PLAN_AGENCY === $plan_id ) {
		return 'agency';
	}

	if ( EDMINBOOST_FREEMIUS_PLAN_PRO === $plan_id ) {
		return 'pro';
	}

	return 'pro';
}

/**
 * Optional upgrade URL from Freemius when the SDK is active.
 *
 * @param string $url Default external pricing URL.
 * @return string
 */
function edminboost_premium_upgrade_url( $url ) {
	$fs = edminboost_fs();
	if ( is_object( $fs ) && $fs->is_registered() ) {
		$pricing = $fs->get_upgrade_url();
		if ( is_string( $pricing ) && '' !== $pricing ) {
			return $pricing;
		}
	}

	return $url;
}

/**
 * Remove plugin data on delete (premium build — Freemius after_uninstall; no uninstall.php).
 */
function edminboost_premium_uninstall_cleanup() {
	$settings_file = dirname( __DIR__ ) . '/class-edminboost-settings.php';
	if ( ! is_readable( $settings_file ) ) {
		return;
	}

	require_once $settings_file;

	EDMINBOOST_Settings::uninstall();
}

add_filter( 'edminboost_active_billing_plan', 'edminboost_premium_active_billing_plan' );
add_filter( 'edminboost_upgrade_url', 'edminboost_premium_upgrade_url' );

if ( '' !== edminboost_premium_freemius_sdk_path() ) {
	$fs = edminboost_fs();
	if ( is_object( $fs ) ) {
		$fs->add_action( 'after_uninstall', 'edminboost_premium_uninstall_cleanup' );
	}
}

