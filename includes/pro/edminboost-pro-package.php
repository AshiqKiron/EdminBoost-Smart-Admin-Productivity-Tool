<?php
/**
 * Premium package loader — Pro runtime modules (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__ ) . '/features/class-edminboost-feature-base.php';
require_once __DIR__ . '/class-edminboost-white-label.php';
require_once __DIR__ . '/features/class-edminboost-login-redirects.php';
require_once __DIR__ . '/class-edminboost-command-center-bar-pro.php';
require_once __DIR__ . '/class-edminboost-theme-pro.php';
require_once __DIR__ . '/class-edminboost-plan-licensing.php';
require_once __DIR__ . '/class-edminboost-billing.php';
require_once __DIR__ . '/class-edminboost-admin-billing.php';
require_once __DIR__ . '/class-edminboost-pro.php';
require_once __DIR__ . '/class-edminboost-plan-limits.php';
require_once __DIR__ . '/class-edminboost-plan-gating.php';
require_once __DIR__ . '/class-edminboost-admin-licensed.php';

EDMINBOOST_Pro::register_admin_ui_hooks();
EDMINBOOST_Admin_Licensed::register_hooks();
require_once __DIR__ . '/class-edminboost-command-center-catalog-pro.php';
require_once __DIR__ . '/class-edminboost-theme-presets-pro.php';

EDMINBOOST_Plan_Gating::register_hooks();
EDMINBOOST_Admin_Billing::register_hooks();
EDMINBOOST_Command_Center_Catalog_Pro::register_hooks();
EDMINBOOST_Theme_Presets_Pro::register_hooks();

add_filter(
	'edminboost_feature_classes',
	static function ( $feature_classes ) {
		$feature_classes[] = 'EDMINBOOST_Login_Redirects';

		return $feature_classes;
	}
);

EDMINBOOST_Command_Center_Bar_Pro::register_hooks();
EDMINBOOST_Theme_Pro::register_hooks();
