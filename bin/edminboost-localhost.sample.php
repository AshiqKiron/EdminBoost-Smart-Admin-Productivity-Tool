<?php
/**
 * Local MAMP/dev: premium build + licensed Pro (not shipped in release zips).
 *
 * Copy to plugin-root edminboost-localhost.php, or run bin/setup-qa-site.sh.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'EDMINBOOST_LOCALHOST_LOADED' ) ) {
	return;
}

define( 'EDMINBOOST_LOCALHOST_LOADED', true );

$premium_bootstrap = EDMINBOOST_PLUGIN_DIR . 'includes/pro/edminboost-premium.php';
if ( is_readable( $premium_bootstrap ) ) {
	require_once $premium_bootstrap;
}

add_filter( 'edminboost_is_pro_active', '__return_true', 1 );
add_filter(
	'edminboost_active_billing_plan',
	static function () {
		return 'pro';
	},
	1
);

