<?php
/**
 * PHPUnit helper — bootstrap WordPress core admin menu globals.
 *
 * Production code must not require wp-admin/menu.php; tests need populated
 * $menu / $submenu before discovery snapshots are captured.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Backup of core admin menu globals (menu.php can only load once per process).
 *
 * @var array{menu: array, submenu: array}|null
 */
$edminboost_test_admin_menu_backup = null;

/**
 * Whether the admin menu includes the Dashboard entry.
 *
 * @param array $menu Global admin menu.
 * @return bool
 */
function edminboost_test_admin_menu_has_dashboard( $menu ) {
	if ( ! is_array( $menu ) ) {
		return false;
	}

	foreach ( $menu as $menu_item ) {
		if ( isset( $menu_item[2] ) && 'index.php' === (string) $menu_item[2] ) {
			return true;
		}
	}

	return false;
}

/**
 * Load core admin menu definitions when PHPUnit runs without a full wp-admin bootstrap.
 *
 * @return void
 */
function edminboost_test_bootstrap_admin_menu_globals() {
	global $menu, $submenu, $pagenow, $_wp_submenu_nopriv, $_wp_menu_nopriv, $edminboost_test_admin_menu_backup;

	if ( is_array( $menu ) && ! empty( $menu ) && edminboost_test_admin_menu_has_dashboard( $menu ) ) {
		if ( null === $edminboost_test_admin_menu_backup ) {
			$edminboost_test_admin_menu_backup = array(
				'menu'    => $menu,
				'submenu' => is_array( $submenu ) ? $submenu : array(),
			);
		}
		return;
	}

	if ( is_array( $edminboost_test_admin_menu_backup ) && edminboost_test_admin_menu_has_dashboard( $edminboost_test_admin_menu_backup['menu'] ) ) {
		$menu    = $edminboost_test_admin_menu_backup['menu'];
		$submenu = $edminboost_test_admin_menu_backup['submenu'];
		return;
	}

	if ( function_exists( '_add_themes_utility_last' ) ) {
		return;
	}

	if ( empty( $pagenow ) ) {
		$pagenow = 'admin.php';
	}

	if ( ! is_array( $_wp_submenu_nopriv ) ) {
		$_wp_submenu_nopriv = array();
	}

	if ( ! is_array( $_wp_menu_nopriv ) ) {
		$_wp_menu_nopriv = array();
	}

	if ( defined( 'WP_NETWORK_ADMIN' ) && WP_NETWORK_ADMIN ) {
		require ABSPATH . 'wp-admin/network/menu.php';
	} elseif ( defined( 'WP_USER_ADMIN' ) && WP_USER_ADMIN ) {
		require ABSPATH . 'wp-admin/user/menu.php';
	} else {
		require ABSPATH . 'wp-admin/menu.php';
	}

	if ( is_array( $menu ) && edminboost_test_admin_menu_has_dashboard( $menu ) && null === $edminboost_test_admin_menu_backup ) {
		$edminboost_test_admin_menu_backup = array(
			'menu'    => $menu,
			'submenu' => is_array( $submenu ) ? $submenu : array(),
		);
	}
}
