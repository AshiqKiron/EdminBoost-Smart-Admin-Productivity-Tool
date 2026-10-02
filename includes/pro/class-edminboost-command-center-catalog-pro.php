<?php
/**
 * Premium build: extra layout presets and definitions (omitted from WordPress.org zips).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDMINBOOST_Command_Center_Catalog_Pro {

	public static function register_hooks() {
		add_filter( 'edminboost_system_presets', array( __CLASS__, 'merge_scenario_presets' ), 10, 1 );
		add_filter( 'edminboost_preset_layout_definitions', array( __CLASS__, 'merge_layout_definitions' ), 10, 1 );
		add_filter( 'edminboost_command_center_defaults', array( __CLASS__, 'premium_default_preset' ), 10, 1 );
		add_filter( 'edminboost_personas', array( __CLASS__, 'merge_personas' ), 10, 1 );
		add_filter( 'edminboost_preset_menu_studio_definitions', array( __CLASS__, 'merge_menu_studio_definitions' ), 10, 1 );
	}

	public static function premium_default_preset( $defaults ) {
		if ( is_array( $defaults ) ) {
			$defaults['default_preset'] = 'system_client';
		}
		return $defaults;
	}

	public static function merge_scenario_presets( $presets ) {
		return array_merge( is_array( $presets ) ? $presets : array(), self::get_pro_scenario_presets() );
	}

	public static function merge_layout_definitions( $definitions ) {
		return array_merge( is_array( $definitions ) ? $definitions : array(), self::get_pro_layout_definitions() );
	}

	public static function merge_personas( $personas ) {
		return array_merge( is_array( $personas ) ? $personas : array(), self::get_pro_personas() );
	}

	public static function merge_menu_studio_definitions( $definitions ) {
		return array_merge( is_array( $definitions ) ? $definitions : array(), self::get_pro_menu_studio_definitions() );
	}

	public static function get_pro_scenario_presets() {
		return array(
'system_client_site' => array(
				'name'        => __( 'Client\'s Website', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Polished client handoff — content tools plus appearance in a slide-out panel.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'client_site',
			),
			'system_personal' => array(
				'name'        => __( 'Your Own Website', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Personal site workflow — write posts, upload media, and check comments.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'personal',
			),
			'system_small_business' => array(
				'name'        => __( 'Small Business Site', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Business pages, customer messages, and shop shortcuts when WooCommerce is active.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'small_business',
			),
			'system_nonprofit' => array(
				'name'        => __( 'Nonprofit / Community', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Share updates, manage pages, and respond to community comments.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'nonprofit',
			),
			'system_agency' => array(
				'name'        => __( 'Freelancer / Agency', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Manage client sites with plugins, themes, users, and settings in drawers.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'agency',
			),
			'system_client' => array(
				'name'        => __( 'Content Editor', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Clean top bar focused on content creation and media.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'client',
			),
			'system_ecommerce' => array(
				'name'        => __( 'Shop Manager', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'WooCommerce dashboards, orders, and product shortcuts.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'ecommerce',
			),
			'system_developer' => array(
				'name'        => __( 'Power User', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Full admin mapping with slide-out panels for deep screens.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'developer',
			)
		);
	}

	public static function get_pro_layout_definitions() {
		return array(
'system_client_site' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php',
					'label'        => __( 'Posts', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-post',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php?post_type=page',
					'label'        => __( 'Pages', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-page',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'upload.php',
					'label'        => __( 'Media', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-media',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'themes.php',
					'label'        => __( 'Appearance', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-appearance',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
			),
			'system_personal' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php',
					'label'        => __( 'Posts', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-post',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'upload.php',
					'label'        => __( 'Media', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-media',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit-comments.php',
					'label'        => __( 'Comments', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-comments',
					'interaction'  => 'redirect',
					'badge_source' => 'comments',
				),
			),
			'system_small_business' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php?post_type=page',
					'label'        => __( 'Pages', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-page',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit-comments.php',
					'label'        => __( 'Messages', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-email',
					'interaction'  => 'redirect',
					'badge_source' => 'comments',
				),
				array(
					'slug'         => 'edit.php?post_type=product',
					'label'        => __( 'Products', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-products',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php?post_type=shop_order',
					'label'        => __( 'Orders', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-list-view',
					'interaction'  => 'redirect',
					'badge_source' => 'wc_orders',
				),
			),
			'system_nonprofit' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php',
					'label'        => __( 'News', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-post',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php?post_type=page',
					'label'        => __( 'Pages', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-page',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit-comments.php',
					'label'        => __( 'Comments', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-comments',
					'interaction'  => 'redirect',
					'badge_source' => 'comments',
				),
			),
			'system_agency' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'plugins.php',
					'label'        => __( 'Plugins', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-plugins',
					'interaction'  => 'drawer',
					'badge_source' => 'updates',
				),
				array(
					'slug'         => 'themes.php',
					'label'        => __( 'Appearance', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-appearance',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
				array(
					'slug'         => 'users.php',
					'label'        => __( 'Users', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-users',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
				array(
					'slug'         => 'tools.php',
					'label'        => __( 'Tools', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-tools',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
				array(
					'slug'         => 'options-general.php',
					'label'        => __( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-settings',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
			),
			'system_client' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php',
					'label'        => __( 'Posts', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-post',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'upload.php',
					'label'        => __( 'Media', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-media',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php?post_type=page',
					'label'        => __( 'Pages', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-page',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit-comments.php',
					'label'        => __( 'Comments', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-comments',
					'interaction'  => 'redirect',
					'badge_source' => 'comments',
				),
			),
			'system_ecommerce' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'woocommerce',
					'label'        => __( 'WooCommerce', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-cart',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php?post_type=shop_order',
					'label'        => __( 'Orders', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-list-view',
					'interaction'  => 'redirect',
					'badge_source' => 'wc_orders',
				),
				array(
					'slug'         => 'edit.php?post_type=product',
					'label'        => __( 'Products', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-products',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'wc-admin',
					'label'        => __( 'Analytics', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-chart-bar',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
			),
			'system_developer' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'edit.php',
					'label'        => __( 'Posts', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-post',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
				array(
					'slug'         => 'plugins.php',
					'label'        => __( 'Plugins', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-plugins',
					'interaction'  => 'drawer',
					'badge_source' => 'updates',
				),
				array(
					'slug'         => 'themes.php',
					'label'        => __( 'Appearance', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-appearance',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
				array(
					'slug'         => 'tools.php',
					'label'        => __( 'Tools', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-tools',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
				array(
					'slug'         => 'options-general.php',
					'label'        => __( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-settings',
					'interaction'  => 'drawer',
					'badge_source' => '',
				),
			)
		);
	}

	public static function get_pro_personas() {
		return array(
'client_site' => array(
				'title'       => __( 'Client\'s Website', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Professional handoff when you build or maintain sites for paying clients — polished and distraction-free.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-businessperson',
				'preset'      => 'system_client_site',
			),
			'personal' => array(
				'title'       => __( 'Your Own Website', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Your personal blog, portfolio, or hobby site — write, publish, and manage media in one place.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-admin-home',
				'preset'      => 'system_personal',
			),
			'small_business' => array(
				'title'       => __( 'Small Business Site', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Local shop or service business — pages, customer messages, and WooCommerce shortcuts when installed.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-store',
				'preset'      => 'system_small_business',
			),
			'nonprofit' => array(
				'title'       => __( 'Nonprofit / Community', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Volunteer-run organizations — news, pages, and community comments without the technical noise.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-megaphone',
				'preset'      => 'system_nonprofit',
			),
			'agency' => array(
				'title'       => __( 'Freelancer / Agency', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'You juggle multiple client sites — plugins, themes, users, and settings in slide-out panels.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-building',
				'preset'      => 'system_agency',
			),
			'client' => array(
				'title'       => __( 'Content Editor', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Minimalist setup for writers and editors — posts, pages, media, and comment moderation only.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-edit',
				'preset'      => 'system_client',
			),
			'ecommerce' => array(
				'title'       => __( 'E-Commerce Manager', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'WooCommerce dashboards, live order counters, products, and analytics shortcuts.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-cart',
				'preset'      => 'system_ecommerce',
			),
			'developer' => array(
				'title'       => __( 'Power User', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Full admin mapping with slide-out panels for plugins, themes, tools, and settings.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-admin-tools',
				'preset'      => 'system_developer',
			)
		);
	}

	public static function get_pro_menu_studio_definitions() {
		return array(
'system_client_site' => array(
				'index.php',
				'edit.php',
				'edit.php?post_type=page',
				'upload.php',
				'themes.php',
			),
			'system_personal' => array(
				'index.php',
				'edit.php',
				'upload.php',
				'edit-comments.php',
			),
			'system_small_business' => array(
				'index.php',
				'edit.php?post_type=page',
				'edit-comments.php',
				'woocommerce',
			),
			'system_nonprofit' => array(
				'index.php',
				'edit.php',
				'edit.php?post_type=page',
				'edit-comments.php',
			),
			'system_agency' => array(
				'index.php',
				'plugins.php',
				'themes.php',
				'users.php',
				'tools.php',
				'options-general.php',
			),
			'system_client' => array(
				'index.php',
				'edit.php',
				'upload.php',
				'edit.php?post_type=page',
				'edit-comments.php',
			),
			'system_ecommerce' => array(
				'index.php',
				'woocommerce',
			),
			'system_developer' => array(
				'index.php',
				'edit.php',
				'upload.php',
				'edit.php?post_type=page',
				'edit-comments.php',
				'plugins.php',
				'themes.php',
				'tools.php',
				'options-general.php',
			)
		);
	}
}
