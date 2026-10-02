<?php
/**
 * Premium build: license and billing plan state (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Licensed Pro/Agency detection for the direct-download build.
 */
class EDMINBOOST_Plan_Licensing {

	/**
	 * Whether Pro (or Agency) is active on this site.
	 *
	 * @return bool
	 */
	public static function is_active() {
		$plan = self::get_active_billing_plan();

		return (bool) apply_filters(
			'edminboost_is_pro_active',
			in_array( $plan, array( 'pro', 'agency' ), true )
		);
	}

	/**
	 * In-plugin Billing page URL (premium build only).
	 *
	 * @return string
	 */
	public static function get_billing_url() {
		return admin_url(
			'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_BILLING
		);
	}

	/**
	 * Active billing plan for the current site.
	 *
	 * @return string Plan ID (`free`, `pro`, or `agency`).
	 */
	public static function get_active_billing_plan() {
		/**
		 * Filter the active billing plan ID.
		 *
		 * @param string $plan Default plan ID.
		 */
		$plan = apply_filters( 'edminboost_active_billing_plan', 'free' );

		return in_array( $plan, array( 'free', 'pro', 'agency' ), true ) ? $plan : 'free';
	}
}
