<?php
/**
 * Command Center — onboarding, layout studio, presets, and behavior helpers.
 *
 * Purpose: Shared data and defaults for EdminBoost admin configuration pages.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command Center helper.
 */
class EDMINBOOST_Command_Center {

	/**
	 * Snapshot of the admin menu captured before Menu Studio filters it.
	 *
	 * @var array|null
	 */
	private static $discovery_snapshot = null;

	/**
	 * Reset cached menu discovery data.
	 *
	 * Used by the test suite when admin menu globals are manipulated between tests.
	 *
	 * @return void
	 */
	public static function reset_static_caches() {
		self::$discovery_snapshot  = null;
		self::$menu_capability_map = null;
	}

	/**
	 * Onboarding wizard page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_ONBOARDING = '-onboarding';

	/**
	 * Layout mapper page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_MAPPER = '-mapper';

	/**
	 * Presets manager page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_PRESETS = '-presets';

	/**
	 * Behavior & styling page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_BEHAVIOR = '-behavior';

	/**
	 * Appearance settings page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_APPEARANCE = '-appearance';

	/**
	 * Menu Studio page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_MENU_STUDIO = '-menu';

	/**
	 * Productivity settings page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_PRODUCTIVITY = '-productivity';

	/**
	 * Security settings page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_SECURITY = '-security';

	/**
	 * Performance settings page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_PERFORMANCE = '-performance';

	/**
	 * White Label settings page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_WHITE_LABEL = '-white-label';

	/**
	 * Billing and plans page slug suffix.
	 *
	 * @var string
	 */
	const PAGE_BILLING = '-billing';

	/**
	 * Minimum custom drawer width in pixels.
	 *
	 * @var int
	 */
	const DRAWER_CUSTOM_WIDTH_MIN = 400;

	/**
	 * Maximum custom drawer width in pixels.
	 *
	 * @var int
	 */
	const DRAWER_CUSTOM_WIDTH_MAX = 800;

	/**
	 * Default custom drawer width in pixels.
	 *
	 * @var int
	 */
	const DRAWER_CUSTOM_WIDTH_DEFAULT = 600;

	/**
	 * Default Command Center settings shape.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'onboarding_completed' => false,
			'persona'                => '',
			'look_skin'              => '',
			'top_bar_items'          => array(),
			'presets'                => array(),
			'default_preset'         => 'system_client',
			'role_assignments'       => array(),
			'role_visibility'        => array(),
			'theme'                  => EDMINBOOST_Theme::get_defaults(),
			'behavior'               => array(
				'drawer_width'         => 'standard',
				'drawer_width_custom'  => self::DRAWER_CUSTOM_WIDTH_DEFAULT,
				'animation_speed'      => 'normal',
				'glassmorphism'        => false,
				'autosave_interval'    => 60,
				'badge_refresh_rate'   => 60,
				'badge_style'          => 'pill',
				'hide_wp_logo'         => false,
				'hide_update_counters' => false,
				'hide_howdy'           => false,
				'hide_comments'        => false,
				'hide_new_content'     => false,
				'hide_customize'       => false,
			),
			'menu_studio'            => self::get_menu_studio_defaults(),
		);
	}

	/**
	 * Default Menu Studio settings shape.
	 *
	 * @return array
	 */
	public static function get_menu_studio_defaults() {
		return array(
			'enabled'        => false,
			'order'          => array(),
			'submenu_order'  => array(),
			'hidden_items'   => array(),
			'custom_items'   => array(),
			'use_colors'     => false,
			'menu_width'     => 160,
			'font_size'      => 14,
			'line_height'    => 20,
			'letter_spacing' => 0,
			'display_mode'   => 'both',
			'padding'        => array(
				'wrapper_top'    => 0,
				'wrapper_right'  => 0,
				'wrapper_bottom' => 0,
				'wrapper_left'   => 0,
				'submenu_top'    => 0,
				'submenu_right'  => 0,
				'submenu_bottom' => 0,
				'submenu_left'   => 0,
			),
			'colors'         => array(
				'parent_bg'         => '',
				'parent_text'       => '',
				'parent_active'     => '',
				'submenu_bg'        => '',
				'submenu_text'      => '',
				'notification_bg'   => '',
				'notification_text' => '',
			),
		);
	}

	/**
	 * Animation speed options for drawer transitions (value => label + duration).
	 *
	 * @return array<string, array{label: string, ms: int}>
	 */
	public static function get_animation_speed_options() {
		return array(
			'fast'   => array(
				'label' => __( 'Fast (150ms)', EDMINBOOST_TEXT_DOMAIN ),
				'ms'    => 150,
			),
			'normal' => array(
				'label' => __( 'Normal (300ms)', EDMINBOOST_TEXT_DOMAIN ),
				'ms'    => 300,
			),
			'slow'   => array(
				'label' => __( 'Slow (500ms)', EDMINBOOST_TEXT_DOMAIN ),
				'ms'    => 500,
			),
		);
	}

	/**
	 * Drawer animation duration in milliseconds for a speed key.
	 *
	 * @param string $speed Speed key (fast, normal, slow). Empty uses saved behavior.
	 * @return int
	 */
	public static function get_animation_duration_ms( $speed = '' ) {
		$options = self::get_animation_speed_options();

		if ( '' === $speed ) {
			$behavior = self::get_settings()['behavior'];
			$speed    = isset( $behavior['animation_speed'] ) ? $behavior['animation_speed'] : 'normal';
		}

		if ( isset( $options[ $speed ]['ms'] ) ) {
			return (int) $options[ $speed ]['ms'];
		}

		return 300;
	}

	/**
	 * Get merged Command Center settings from plugin options.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = EDMINBOOST_Settings::get();
		$defaults = self::get_defaults();

		if ( ! isset( $settings['command_center'] ) || ! is_array( $settings['command_center'] ) ) {
			return $defaults;
		}

		$merged = wp_parse_args( $settings['command_center'], $defaults );

		if ( isset( $merged['behavior'] ) && is_array( $merged['behavior'] ) ) {
			$merged['behavior'] = wp_parse_args( $merged['behavior'], $defaults['behavior'] );
		}

		if ( isset( $merged['theme'] ) && is_array( $merged['theme'] ) ) {
			$merged['theme'] = wp_parse_args( $merged['theme'], $defaults['theme'] );
		}

		if ( isset( $merged['menu_studio'] ) && is_array( $merged['menu_studio'] ) ) {
			$ms_defaults = self::get_menu_studio_defaults();
			$merged['menu_studio'] = wp_parse_args( $merged['menu_studio'], $ms_defaults );

			if ( isset( $merged['menu_studio']['colors'] ) && is_array( $merged['menu_studio']['colors'] ) ) {
				$merged['menu_studio']['colors'] = wp_parse_args(
					$merged['menu_studio']['colors'],
					$ms_defaults['colors']
				);
			}
		}

		return $merged;
	}

	/**
	 * Quick links to main plugin pages (Dashboard hub, sidebar).
	 *
	 * @return array[]
	 */
	public static function get_page_links() {
		$base = EDMINBOOST_Admin::PAGE_SLUG;

		return array(
			array(
				'slug'  => $base,
				'label' => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_PRESETS,
				'label' => __( 'Layouts', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_APPEARANCE,
				'label' => __( 'Theme', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_MAPPER,
				'label' => __( 'Top Bar', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_MENU_STUDIO,
				'label' => __( 'Menu Studio', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_BILLING,
				'label' => __( 'Billing', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . '-settings',
				'label' => __( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
			),
		);
	}

	/**
	 * Command Center tab navigation (includes pages hidden from the sidebar).
	 *
	 * @return array[]
	 */
	public static function get_nav_items() {
		$base = EDMINBOOST_Admin::PAGE_SLUG;

		return array(
			array(
				'slug'  => $base,
				'label' => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_PRESETS,
				'label' => __( 'Layouts', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_APPEARANCE,
				'label' => __( 'Theme', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_MAPPER,
				'label' => __( 'Top Bar', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_MENU_STUDIO,
				'label' => __( 'Menu Studio', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_PRODUCTIVITY,
				'label' => __( 'Productivity', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_SECURITY,
				'label' => __( 'Security', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_PERFORMANCE,
				'label' => __( 'Performance', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_WHITE_LABEL,
				'label' => __( 'White Label', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . self::PAGE_BILLING,
				'label' => __( 'Billing', EDMINBOOST_TEXT_DOMAIN ),
			),
			array(
				'slug'  => $base . '-settings',
				'label' => __( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
			),
		);
	}

	/**
	 * Available subscription plans for the Billing page.
	 *
	 * @return array<string, array{id: string, name: string, price: int, price_label: string, sites: int, sites_label: string, description: string, features: string[], featured: bool}> Sites is 0 for unlimited.
	 */
	public static function get_billing_plans() {
		return array(
			'free' => array(
				'id'           => 'free',
				'name'         => __( 'Free', EDMINBOOST_TEXT_DOMAIN ),
				'price'        => 0,
				'price_label'  => __( '$0', EDMINBOOST_TEXT_DOMAIN ),
				'sites'        => 0,
				'sites_label'  => __( 'Unlimited sites', EDMINBOOST_TEXT_DOMAIN ),
				'description'  => __( 'Core Command Center tools on unlimited WordPress sites.', EDMINBOOST_TEXT_DOMAIN ),
				'features'     => array(
					__( 'Dashboard setup wizard', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Top bar builder', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Default and by-role layout presets', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Default, Midnight, Terminal, and Custom theme presets', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Productivity, security, and performance tools', EDMINBOOST_TEXT_DOMAIN ),
				),
				'featured'     => false,
			),
			'pro'  => array(
				'id'           => 'pro',
				'name'         => __( 'Pro', EDMINBOOST_TEXT_DOMAIN ),
				'price'        => 49,
				'price_label'  => __( '$49', EDMINBOOST_TEXT_DOMAIN ),
				'sites'        => 1,
				'sites_label'  => __( '1 site', EDMINBOOST_TEXT_DOMAIN ),
				'description'  => __( 'Premium admin customization for one production site.', EDMINBOOST_TEXT_DOMAIN ),
				'features'     => array(
					__( 'Everything in Free', EDMINBOOST_TEXT_DOMAIN ),
					__( 'By use case layout presets and saved layouts', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Full visual theme library (20+ skins)', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Menu Studio sidebar builder', EDMINBOOST_TEXT_DOMAIN ),
					__( 'White-label branding and login screen', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Priority email support', EDMINBOOST_TEXT_DOMAIN ),
				),
				'featured'     => true,
			),
			'agency' => array(
				'id'           => 'agency',
				'name'         => __( 'Agency', EDMINBOOST_TEXT_DOMAIN ),
				'price'        => 99,
				'price_label'  => __( '$99', EDMINBOOST_TEXT_DOMAIN ),
				'sites'        => 10,
				'sites_label'  => __( '10 sites', EDMINBOOST_TEXT_DOMAIN ),
				'description'  => __( 'Deploy EdminBoost across a client portfolio.', EDMINBOOST_TEXT_DOMAIN ),
				'features'     => array(
					__( 'Everything in Pro', EDMINBOOST_TEXT_DOMAIN ),
					__( '10 site license pack', EDMINBOOST_TEXT_DOMAIN ),
					__( 'Settings export and import per site', EDMINBOOST_TEXT_DOMAIN ),
				),
				'featured'     => false,
			),
		);
	}

	/**
	 * Active billing plan for the current site.
	 *
	 * @return string Plan ID (`free`, `pro`, or `agency`).
	 */
	public static function get_active_billing_plan() {
		return 'free';
	}

	/**
	 * Whether the site has a saved top bar layout or completed setup.
	 *
	 * @param array|null $cc_settings Optional CC settings; loads from options when null.
	 * @return bool
	 */
	public static function is_setup_complete( $cc_settings = null ) {
		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		if ( ! empty( $cc_settings['onboarding_completed'] ) ) {
			return true;
		}

		return ! empty( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] );
	}

	/**
	 * Look-only skin presets (behavior bundles).
	 *
	 * @return array[]
	 */
	public static function get_look_skins() {
		$defaults = self::get_defaults()['behavior'];

		return array(
			'clean' => array(
				'name'        => __( 'Clean', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Standard panel, subtle badges, hides logo and profile text.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-art',
				'behavior'    => array_merge(
					$defaults,
					array(
						'drawer_width'         => 'standard',
						'animation_speed'      => 'normal',
						'badge_style'          => 'dot',
						'hide_wp_logo'         => true,
						'hide_howdy'           => true,
						'hide_update_counters' => false,
						'hide_comments'        => false,
					)
				),
			),
			'focused' => array(
				'name'        => __( 'Focused', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Compact panel, counter badges, minimal admin bar clutter.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-visibility',
				'behavior'    => array_merge(
					$defaults,
					array(
						'drawer_width'         => 'compact',
						'animation_speed'      => 'fast',
						'badge_style'          => 'pill',
						'hide_wp_logo'         => true,
						'hide_update_counters' => true,
						'hide_howdy'           => false,
						'hide_comments'        => true,
					)
				),
			),
			'full' => array(
				'name'        => __( 'Full', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Standard panel with accent badges and the full native admin bar.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-admin-site-alt3',
				'behavior'    => array_merge(
					$defaults,
					array(
						'drawer_width'         => 'standard',
						'animation_speed'      => 'normal',
						'badge_style'          => 'accent',
						'hide_wp_logo'         => false,
						'hide_update_counters' => false,
						'hide_howdy'           => false,
						'hide_comments'        => false,
					)
				),
			),
		);
	}

	/**
	 * Detect which look skin matches stored behavior settings.
	 *
	 * @param array|null $behavior Behavior settings.
	 * @return string Skin id or empty string.
	 */
	public static function detect_look_skin( $behavior = null ) {
		if ( null === $behavior ) {
			$behavior = self::get_settings()['behavior'];
		}

		foreach ( self::get_look_skins() as $skin_id => $skin ) {
			$matches = true;

			foreach ( $skin['behavior'] as $key => $value ) {
				if ( ! isset( $behavior[ $key ] ) ) {
					$matches = false;
					break;
				}

				if ( (bool) $behavior[ $key ] !== (bool) $value ) {
					$matches = false;
					break;
				}

				if ( ! is_bool( $value ) && (string) $behavior[ $key ] !== (string) $value ) {
					$matches = false;
					break;
				}
			}

			if ( $matches ) {
				return $skin_id;
			}
		}

		return '';
	}

	/**
	 * Persona preset cards for onboarding.
	 *
	 * @return array[]
	 */
	public static function get_personas() {
		return array(
			'friend' => array(
				'title'       => __( 'Friend\'s Website', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'You help a friend keep their blog or portfolio updated — content-first shortcuts without technical clutter.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-groups',
				'preset'      => 'system_friend',
			),
			'family' => array(
				'title'       => __( 'Family Member\'s Site', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'A gentle setup for parents or relatives who only need pages, photos, and the basics.', EDMINBOOST_TEXT_DOMAIN ),
				'icon'        => 'dashicons-heart',
				'preset'      => 'system_family',
			),
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
			),
		);
	}

	/**
	 * Built-in preset category labels for the preset library UI.
	 *
	 * @return array<string, string>
	 */
	public static function get_preset_categories() {
		return array(
			'source'   => __( 'Current layout', EDMINBOOST_TEXT_DOMAIN ),
			'scenario' => __( 'By use case', EDMINBOOST_TEXT_DOMAIN ),
			'workflow' => __( 'By role', EDMINBOOST_TEXT_DOMAIN ),
			'saved'    => __( 'Your saved layouts', EDMINBOOST_TEXT_DOMAIN ),
		);
	}

	/**
	 * Virtual layout presets shown at the top of picker dropdowns.
	 *
	 * @return array<string, array>
	 */
	public static function get_virtual_layout_presets() {
		return array(
			'default' => array(
				'name'        => __( 'Default', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Your site\'s default layout preset.', EDMINBOOST_TEXT_DOMAIN ),
				'virtual'     => true,
				'category'    => 'source',
			),
			'custom'  => array(
				'name'        => __( 'Custom', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Fine-tune the top bar and sidebar in Top Bar and Menu Studio', EDMINBOOST_TEXT_DOMAIN ),
				'virtual'     => true,
				'category'    => 'source',
			),
		);
	}

	/**
	 * Preset catalog for picker UIs (virtual entries + system + saved).
	 *
	 * @param bool $include_virtual Whether to prepend Default and Custom options.
	 * @return array<string, array>
	 */
	public static function get_picker_presets( $include_virtual = true ) {
		$presets = self::get_all_presets();

		if ( ! $include_virtual ) {
			return $presets;
		}

		return array_merge( self::get_virtual_layout_presets(), $presets );
	}

	/**
	 * Resolve a virtual preset id to a concrete preset id.
	 *
	 * @param string     $preset_id   Preset identifier (may be virtual).
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return string
	 */
	public static function resolve_effective_preset_id( $preset_id, $cc_settings = null ) {
		$preset_id = sanitize_key( $preset_id );

		if ( 'default' !== $preset_id ) {
			return $preset_id;
		}

		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		$fallback = self::get_defaults()['default_preset'];

		return isset( $cc_settings['default_preset'] ) && '' !== $cc_settings['default_preset']
			? sanitize_key( $cc_settings['default_preset'] )
			: $fallback;
	}

	/**
	 * Fingerprint top bar items for layout comparison.
	 *
	 * @param array $item Top bar item.
	 * @return string
	 */
	private static function get_top_bar_item_fingerprint( $item ) {
		if ( ! is_array( $item ) || empty( $item['slug'] ) ) {
			return '';
		}

		$slug        = isset( $item['slug'] ) ? (string) $item['slug'] : '';
		$anchor      = isset( $item['anchor'] ) ? (string) $item['anchor'] : '';
		$interaction = isset( $item['interaction'] ) ? (string) $item['interaction'] : 'redirect';
		$badge       = isset( $item['badge_source'] ) ? (string) $item['badge_source'] : '';

		return $slug . "\0" . $anchor . "\0" . $interaction . "\0" . $badge;
	}

	/**
	 * Whether two top bar item lists are structurally equivalent.
	 *
	 * @param array $left  First item list.
	 * @param array $right Second item list.
	 * @return bool
	 */
	public static function top_bar_items_match( $left, $right ) {
		$left_fps  = array();
		$right_fps = array();

		foreach ( (array) $left as $item ) {
			$fp = self::get_top_bar_item_fingerprint( $item );
			if ( '' !== $fp ) {
				$left_fps[] = $fp;
			}
		}

		foreach ( (array) $right as $item ) {
			$fp = self::get_top_bar_item_fingerprint( $item );
			if ( '' !== $fp ) {
				$right_fps[] = $fp;
			}
		}

		return $left_fps === $right_fps;
	}

	/**
	 * Detect which picker preset matches the saved top bar layout.
	 *
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return string Preset id, including virtual `default` or `custom`.
	 */
	public static function detect_active_layout_preset( $cc_settings = null ) {
		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		$current = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
			? $cc_settings['top_bar_items']
			: array();

		if ( empty( $current ) ) {
			return 'default';
		}

		$current_menu = isset( $cc_settings['menu_studio'] ) && is_array( $cc_settings['menu_studio'] )
			? $cc_settings['menu_studio']
			: self::get_menu_studio_defaults();

		$default_preset = self::resolve_effective_preset_id( 'default', $cc_settings );
		$default_items  = self::resolve_preset_top_bar_items( $default_preset, $cc_settings );
		$default_menu   = self::resolve_preset_menu_studio( $default_preset, $cc_settings );

		if (
			self::top_bar_items_match( $current, $default_items )
			&& self::menu_studio_settings_match( $current_menu, $default_menu )
		) {
			return 'default';
		}

		foreach ( self::get_all_presets() as $preset_id => $preset ) {
			unset( $preset );
			$items = self::resolve_preset_top_bar_items( $preset_id, $cc_settings );
			$menu  = self::resolve_preset_menu_studio( $preset_id, $cc_settings );

			if (
				self::top_bar_items_match( $current, $items )
				&& self::menu_studio_settings_match( $current_menu, $menu )
			) {
				return $preset_id;
			}
		}

		return 'custom';
	}

	/**
	 * Whether two Menu Studio configuration snapshots are equivalent for preset matching.
	 *
	 * @param array $left  First menu_studio settings.
	 * @param array $right Second menu_studio settings.
	 * @return bool
	 */
	public static function menu_studio_settings_match( $left, $right ) {
		$left_enabled  = ! empty( $left['enabled'] );
		$right_enabled = ! empty( $right['enabled'] );

		if ( $left_enabled !== $right_enabled ) {
			return false;
		}

		if ( ! $left_enabled ) {
			return true;
		}

		$left_order   = isset( $left['order'] ) && is_array( $left['order'] ) ? $left['order'] : array();
		$right_order  = isset( $right['order'] ) && is_array( $right['order'] ) ? $right['order'] : array();
		$left_hidden  = isset( $left['hidden_items'] ) && is_array( $left['hidden_items'] ) ? $left['hidden_items'] : array();
		$right_hidden = isset( $right['hidden_items'] ) && is_array( $right['hidden_items'] ) ? $right['hidden_items'] : array();
		$left_sub     = isset( $left['submenu_order'] ) && is_array( $left['submenu_order'] ) ? $left['submenu_order'] : array();
		$right_sub    = isset( $right['submenu_order'] ) && is_array( $right['submenu_order'] ) ? $right['submenu_order'] : array();
		$left_custom  = isset( $left['custom_items'] ) && is_array( $left['custom_items'] ) ? $left['custom_items'] : array();
		$right_custom = isset( $right['custom_items'] ) && is_array( $right['custom_items'] ) ? $right['custom_items'] : array();

		$left_custom_ids  = array();
		$right_custom_ids = array();

		foreach ( $left_custom as $item ) {
			if ( ! empty( $item['id'] ) ) {
				$left_custom_ids[] = sanitize_key( $item['id'] );
			}
		}

		foreach ( $right_custom as $item ) {
			if ( ! empty( $item['id'] ) ) {
				$right_custom_ids[] = sanitize_key( $item['id'] );
			}
		}

		return $left_order === $right_order
			&& $left_hidden === $right_hidden
			&& $left_sub === $right_sub
			&& $left_custom_ids === $right_custom_ids;
	}

	/**
	 * Built-in system presets.
	 *
	 * @return array[]
	 */
	public static function get_system_presets() {
		$presets = array(
			'system_friend' => array(
				'name'        => __( 'Friend\'s Website', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Help a friend publish posts, update pages, and moderate comments — no dev tools.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'friend',
			),
			'system_family' => array(
				'name'        => __( 'Family Member\'s Site', EDMINBOOST_TEXT_DOMAIN ),
				'description' => __( 'Ultra-simple bar with dashboard, pages, and media for non-technical relatives.', EDMINBOOST_TEXT_DOMAIN ),
				'system'      => true,
				'category'    => 'scenario',
				'persona'     => 'family',
			),
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
			),
		);

		return array_merge( $presets, self::get_role_system_presets() );
	}

	/**
	 * Preferred top bar slugs per system preset (resolved against discovered menus).
	 *
	 * @return array<string, array[]>
	 */
	public static function get_preset_layout_definitions() {
		$definitions = array(
			'system_friend' => array(
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
					'slug'         => 'edit-comments.php',
					'label'        => __( 'Comments', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-comments',
					'interaction'  => 'redirect',
					'badge_source' => 'comments',
				),
			),
			'system_family' => array(
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
					'slug'         => 'upload.php',
					'label'        => __( 'Media', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-admin-media',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
			),
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
			),
		);

		return array_merge( $definitions, self::get_role_preset_layout_definitions() );
	}

	/**
	 * Resolve top bar items for the current user, honoring role preset assignments.
	 *
	 * @param array|null $cc_settings Optional CC settings.
	 * @return array[]
	 */
	public static function resolve_top_bar_items_for_user( $cc_settings = null ) {
		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		$items = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
			? $cc_settings['top_bar_items']
			: array();

		$user = wp_get_current_user();
		if ( empty( $user->roles ) ) {
			return $items;
		}

		$assignments = isset( $cc_settings['role_assignments'] ) && is_array( $cc_settings['role_assignments'] )
			? $cc_settings['role_assignments']
			: array();

		foreach ( $user->roles as $role ) {
			if ( empty( $assignments[ $role ] ) ) {
				continue;
			}

			$preset_items = self::resolve_preset_top_bar_items( $assignments[ $role ], $cc_settings );
			if ( ! empty( $preset_items ) ) {
				return $preset_items;
			}
		}

		return $items;
	}

	/**
	 * Resolve Menu Studio settings for the current user, honoring role preset assignments.
	 *
	 * @param array|null $cc_settings Optional CC settings.
	 * @return array
	 */
	public static function resolve_menu_studio_for_user( $cc_settings = null ) {
		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		$defaults    = self::get_menu_studio_defaults();
		$menu_studio = isset( $cc_settings['menu_studio'] ) && is_array( $cc_settings['menu_studio'] )
			? wp_parse_args( $cc_settings['menu_studio'], $defaults )
			: $defaults;

		$user = wp_get_current_user();
		if ( empty( $user->roles ) ) {
			return $menu_studio;
		}

		$assignments = isset( $cc_settings['role_assignments'] ) && is_array( $cc_settings['role_assignments'] )
			? $cc_settings['role_assignments']
			: array();

		foreach ( $user->roles as $role ) {
			if ( empty( $assignments[ $role ] ) ) {
				continue;
			}

			$menu_studio = self::resolve_preset_menu_studio( $assignments[ $role ], $cc_settings );
			break;
		}

		return self::apply_role_visibility_to_menu_studio( $menu_studio, $user->roles, $cc_settings );
	}

	/**
	 * Cached map of admin menu slug => required capability.
	 *
	 * @var array<string, string>|null
	 */
	private static $menu_capability_map = null;

	/**
	 * Build a map of admin menu slugs to their required capabilities.
	 *
	 * @return array<string, string>
	 */
	public static function get_menu_capability_map() {
		if ( null !== self::$menu_capability_map && ! empty( self::$menu_capability_map ) ) {
			return self::$menu_capability_map;
		}

		$globals = self::get_discovery_menu_globals();
		$menu    = $globals['menu'];
		$submenu = $globals['submenu'];

		$map = array();

		if ( is_array( $menu ) ) {
			foreach ( $menu as $menu_item ) {
				if ( empty( $menu_item[2] ) ) {
					continue;
				}

				$slug = (string) $menu_item[2];
				if ( self::is_ignorable_menu_slug( $slug ) ) {
					continue;
				}

				$map[ $slug ] = isset( $menu_item[1] ) ? (string) $menu_item[1] : '';
			}
		}

		if ( is_array( $submenu ) ) {
			foreach ( $submenu as $parent_slug => $submenu_items ) {
				if ( ! is_array( $submenu_items ) ) {
					continue;
				}

				foreach ( $submenu_items as $submenu_item ) {
					if ( empty( $submenu_item[2] ) ) {
						continue;
					}

					$slug = (string) $submenu_item[2];
					if ( self::is_ignorable_menu_slug( $slug ) ) {
						continue;
					}

					$map[ $slug ] = isset( $submenu_item[1] ) ? (string) $submenu_item[1] : '';
				}
			}
		}

		if ( empty( $map ) ) {
			self::$menu_capability_map = null;

			return $map;
		}

		self::$menu_capability_map = $map;

		return self::$menu_capability_map;
	}

	/**
	 * Required capability for an admin menu slug.
	 *
	 * @param string $slug Menu slug or admin URL.
	 * @return string Empty when unknown (custom links).
	 */
	public static function get_menu_slug_capability( $slug ) {
		$slug = self::normalize_admin_menu_slug( $slug );
		if ( '' === $slug ) {
			return '';
		}

		$map = self::get_menu_capability_map();

		if ( isset( $map[ $slug ] ) ) {
			return $map[ $slug ];
		}

		$top_slug = self::resolve_top_level_menu_slug( $slug );
		if ( '' !== $top_slug && isset( $map[ $top_slug ] ) ) {
			return $map[ $top_slug ];
		}

		return '';
	}

	/**
	 * Normalize a menu slug or admin URL to a relative wp-admin path.
	 *
	 * @param string $slug Menu slug or admin URL.
	 * @return string
	 */
	public static function normalize_admin_menu_slug( $slug ) {
		return EDMINBOOST_Command_Center_Bar::normalize_item_slug( $slug );
	}

	/**
	 * Whether a WordPress role can access an admin menu slug.
	 *
	 * @param string $role_key Role slug.
	 * @param string $slug     Menu slug or admin URL.
	 * @return bool
	 */
	public static function role_can_access_menu_slug( $role_key, $slug ) {
		$role_key = sanitize_key( $role_key );
		if ( '' === $role_key ) {
			return false;
		}

		$capability = self::get_menu_slug_capability( $slug );
		if ( '' === $capability ) {
			return true;
		}

		return self::role_has_capability( $role_key, $capability );
	}

	/**
	 * Whether any of the user's roles can access an admin menu slug.
	 *
	 * @param string   $slug       Menu slug or admin URL.
	 * @param string[] $user_roles Role slugs.
	 * @return bool
	 */
	public static function user_roles_can_access_menu_slug( $slug, $user_roles ) {
		if ( empty( $user_roles ) || ! is_array( $user_roles ) ) {
			return false;
		}

		foreach ( $user_roles as $role ) {
			if ( self::role_can_access_menu_slug( $role, $slug ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether a role has a capability (runs through WordPress meta-cap mapping).
	 *
	 * @param string $role_key   Role slug.
	 * @param string $capability Capability to check.
	 * @return bool
	 */
	public static function role_has_capability( $role_key, $capability ) {
		$role_key   = sanitize_key( $role_key );
		$capability = (string) $capability;

		if ( '' === $role_key || '' === $capability ) {
			return false;
		}

		$role = get_role( $role_key );
		if ( ! $role ) {
			return false;
		}

		return $role->has_cap( $capability );
	}

	/**
	 * Protected sidebar slugs that a role is allowed to access.
	 *
	 * @param string $role_key Role slug.
	 * @return string[]
	 */
	public static function get_protected_slugs_for_role( $role_key ) {
		$protected = array();

		foreach ( EDMINBOOST_Menu_Studio::get_protected_slugs() as $slug ) {
			if ( self::role_can_access_menu_slug( $role_key, $slug ) ) {
				$protected[] = $slug;
			}
		}

		return $protected;
	}

	/**
	 * Accessible top-level menu slugs keyed by assignable role.
	 *
	 * @return array<string, string[]>
	 */
	public static function get_role_accessible_menu_slugs() {
		$accessible = array();

		foreach ( self::get_assignable_roles() as $role_key => $role_name ) {
			unset( $role_name );
			$accessible[ $role_key ] = wp_list_pluck( self::get_role_matrix_menu_items( $role_key ), 'slug' );
		}

		return $accessible;
	}

	/**
	 * Protected sidebar slugs keyed by assignable role.
	 *
	 * @return array<string, string[]>
	 */
	public static function get_protected_slugs_by_role() {
		$protected = array();

		foreach ( self::get_assignable_roles() as $role_key => $role_name ) {
			unset( $role_name );
			$protected[ $role_key ] = self::get_protected_slugs_for_role( $role_key );
		}

		return $protected;
	}

	/**
	 * Remove top bar items the current user cannot access.
	 *
	 * @param array[] $items Top bar item definitions.
	 * @return array[]
	 */
	public static function filter_top_bar_items_for_user_capabilities( $items ) {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return array();
		}

		$user = wp_get_current_user();
		if ( empty( $user->roles ) ) {
			return $items;
		}

		$filtered = array();

		foreach ( $items as $item ) {
			$slug = isset( $item['slug'] ) ? (string) $item['slug'] : '';
			if ( '' === $slug ) {
				continue;
			}

			if ( self::user_roles_can_access_menu_slug( $slug, $user->roles ) ) {
				$filtered[] = $item;
			}
		}

		return $filtered;
	}

	/**
	 * Top-level admin menu items shown in the role visibility matrix.
	 *
	 * @param string $role_key Optional role slug; when set, only items that role can access are returned.
	 * @return array[]
	 */
	public static function get_role_matrix_menu_items( $role_key = '' ) {
		$items = array();

		foreach ( self::get_discovered_menu_tree() as $item ) {
			if ( empty( $item['slug'] ) ) {
				continue;
			}

			$slug = (string) $item['slug'];
			if ( '' !== $role_key && ! self::role_can_access_menu_slug( $role_key, $slug ) ) {
				continue;
			}

			$items[] = array(
				'slug'     => $slug,
				'label'    => isset( $item['label'] ) ? (string) $item['label'] : $slug,
				'icon'     => isset( $item['icon'] ) ? (string) $item['icon'] : 'dashicons-admin-generic',
				'icon_raw' => '',
				'source'   => 'top',
			);
		}

		return $items;
	}

	/**
	 * Whether a menu slug is visible for at least one of the user's roles.
	 *
	 * @param string   $slug            Menu slug.
	 * @param string[] $user_roles      Role slugs.
	 * @param array    $role_visibility Hidden slugs keyed by role.
	 * @return bool
	 */
	public static function is_item_visible_for_user_roles( $slug, $user_roles, $role_visibility ) {
		foreach ( $user_roles as $role ) {
			$hidden_for_role = isset( $role_visibility[ $role ] ) && is_array( $role_visibility[ $role ] )
				? $role_visibility[ $role ]
				: array();

			if ( ! in_array( $slug, $hidden_for_role, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Visible menu slugs for a Menu Studio configuration.
	 *
	 * @param array $menu_studio Menu Studio settings.
	 * @return string[]
	 */
	public static function get_visible_menu_slugs_for_menu_studio( $menu_studio ) {
		$defaults    = self::get_menu_studio_defaults();
		$menu_studio = wp_parse_args( $menu_studio, $defaults );
		$visible     = array();

		if ( empty( $menu_studio['enabled'] ) ) {
			foreach ( self::get_discovered_menu_items() as $item ) {
				if ( ! empty( $item['slug'] ) ) {
					$visible[] = (string) $item['slug'];
				}
			}

			return $visible;
		}

		$order  = isset( $menu_studio['order'] ) && is_array( $menu_studio['order'] )
			? $menu_studio['order']
			: array();
		$hidden = isset( $menu_studio['hidden_items'] ) && is_array( $menu_studio['hidden_items'] )
			? $menu_studio['hidden_items']
			: array();

		foreach ( self::get_discovered_menu_tree() as $item ) {
			if ( empty( $item['slug'] ) ) {
				continue;
			}

			$top_slug = (string) $item['slug'];
			if ( ! in_array( $top_slug, $order, true ) || in_array( $top_slug, $hidden, true ) ) {
				continue;
			}

			$visible[] = $top_slug;

			if ( empty( $item['children'] ) || ! is_array( $item['children'] ) ) {
				continue;
			}

			foreach ( $item['children'] as $child ) {
				if ( ! empty( $child['slug'] ) ) {
					$visible[] = (string) $child['slug'];
				}
			}
		}

		return array_values( array_unique( $visible ) );
	}

	/**
	 * Visible menu slugs for a layout preset.
	 *
	 * @param string     $preset_id   Preset identifier.
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return string[]
	 */
	public static function get_preset_visible_menu_slugs( $preset_id, $cc_settings = null ) {
		$menu_studio = self::resolve_preset_menu_studio( $preset_id, $cc_settings );

		return self::get_visible_menu_slugs_for_menu_studio( $menu_studio );
	}

	/**
	 * Visible top-level menu slugs for a layout preset (role matrix).
	 *
	 * @param string     $preset_id   Preset identifier.
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return string[]
	 */
	public static function get_preset_visible_top_level_menu_slugs( $preset_id, $cc_settings = null ) {
		$matrix_slugs = wp_list_pluck( self::get_role_matrix_menu_items(), 'slug' );
		$top_level    = array();

		foreach ( self::get_preset_visible_menu_slugs( $preset_id, $cc_settings ) as $slug ) {
			$parent = self::resolve_top_level_menu_slug( $slug );
			if ( '' !== $parent && in_array( $parent, $matrix_slugs, true ) ) {
				$top_level[] = $parent;
			}
		}

		foreach ( self::resolve_preset_top_bar_items( $preset_id, $cc_settings ) as $item ) {
			if ( empty( $item['slug'] ) ) {
				continue;
			}

			$parent = self::resolve_top_level_menu_slug( (string) $item['slug'] );
			if ( '' !== $parent && in_array( $parent, $matrix_slugs, true ) ) {
				$top_level[] = $parent;
			}
		}

		return array_values( array_unique( $top_level ) );
	}

	/**
	 * Apply per-role visibility overrides to Menu Studio settings.
	 *
	 * @param array    $menu_studio     Base Menu Studio settings.
	 * @param string[] $user_roles      Current user roles.
	 * @param array    $cc_settings     Command Center settings.
	 * @return array
	 */
	public static function apply_role_visibility_to_menu_studio( $menu_studio, $user_roles, $cc_settings ) {
		$role_visibility = isset( $cc_settings['role_visibility'] ) && is_array( $cc_settings['role_visibility'] )
			? $cc_settings['role_visibility']
			: array();

		if ( empty( $role_visibility ) || empty( $user_roles ) ) {
			return $menu_studio;
		}

		$defaults    = self::get_menu_studio_defaults();
		$menu_studio = wp_parse_args( $menu_studio, $defaults );

		if ( empty( $menu_studio['enabled'] ) ) {
			$menu_studio['enabled'] = true;
		}

		if ( empty( $menu_studio['order'] ) ) {
			foreach ( self::get_discovered_menu_tree() as $item ) {
				if ( ! empty( $item['slug'] ) ) {
					$menu_studio['order'][] = (string) $item['slug'];
				}
			}
		}

		$hidden = isset( $menu_studio['hidden_items'] ) && is_array( $menu_studio['hidden_items'] )
			? $menu_studio['hidden_items']
			: array();
		$order  = isset( $menu_studio['order'] ) && is_array( $menu_studio['order'] )
			? $menu_studio['order']
			: array();

		foreach ( self::get_role_matrix_menu_items() as $item ) {
			if ( empty( $item['slug'] ) ) {
				continue;
			}

			$slug     = (string) $item['slug'];
			$top_slug = self::resolve_top_level_menu_slug( $slug );
			if ( '' === $top_slug ) {
				continue;
			}

			if ( self::is_item_visible_for_user_roles( $slug, $user_roles, $role_visibility ) ) {
				continue;
			}

			if ( in_array( $top_slug, EDMINBOOST_Menu_Studio::get_protected_slugs(), true ) ) {
				continue;
			}

			if ( ! in_array( $top_slug, $hidden, true ) ) {
				$hidden[] = $top_slug;
			}

			$order = array_values( array_diff( $order, array( $top_slug ) ) );
		}

		$menu_studio['hidden_items'] = $hidden;
		$menu_studio['order']        = $order;

		return $menu_studio;
	}

	/**
	 * Resolve preset top bar items against the current admin menu.
	 *
	 * @param string $preset_id Preset identifier.
	 * @return array[]
	 */
	public static function resolve_preset_top_bar_items( $preset_id, $cc_settings = null ) {
		$preset_id = sanitize_key( $preset_id );

		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		if ( 'custom' === $preset_id ) {
			return isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
				? $cc_settings['top_bar_items']
				: array();
		}

		if ( 'default' === $preset_id ) {
			$preset_id = self::resolve_effective_preset_id( 'default', $cc_settings );
		}

		$all_presets = self::get_all_presets();

		if ( isset( $all_presets[ $preset_id ]['top_bar_items'] ) && is_array( $all_presets[ $preset_id ]['top_bar_items'] ) ) {
			return $all_presets[ $preset_id ]['top_bar_items'];
		}

		$definitions = self::get_preset_layout_definitions();
		if ( ! isset( $definitions[ $preset_id ] ) ) {
			return array();
		}

		$discovered = self::get_discovered_menu_items();
		$by_slug    = array();

		foreach ( $discovered as $item ) {
			if ( empty( $item['slug'] ) ) {
				continue;
			}

			$by_slug[ $item['slug'] ] = $item;
		}

		$resolved = array();

		foreach ( $definitions[ $preset_id ] as $definition ) {
			$slug = isset( $definition['slug'] ) ? (string) $definition['slug'] : '';

			if ( '' === $slug ) {
				continue;
			}

			if ( isset( $by_slug[ $slug ] ) ) {
				$definition_icon = self::normalize_dashicon_class(
					isset( $definition['icon'] ) ? $definition['icon'] : ''
				);
				$raw_discovered  = isset( $by_slug[ $slug ]['icon_raw'] ) ? (string) $by_slug[ $slug ]['icon_raw'] : '';
				$use_discovered  = self::is_dashicon_menu_source( $raw_discovered );
				$discovered_icon = self::normalize_dashicon_class( $raw_discovered, '' );

				$resolved[] = array_merge(
					$definition,
					array(
						'label' => $by_slug[ $slug ]['label'],
						'icon'  => $use_discovered ? $discovered_icon : $definition_icon,
					)
				);
				continue;
			}

			// Include core slugs even when discovery misses them (e.g. during PHPUnit).
			if ( self::is_core_admin_slug( $slug ) ) {
				$resolved[] = $definition;
			}
		}

		return $resolved;
	}

	/**
	 * Preferred sidebar menu slugs per layout preset.
	 *
	 * @return array<string, string[]>
	 */
	public static function get_preset_menu_studio_definitions() {
		$definitions = array(
			'system_friend' => array(
				'index.php',
				'edit.php',
				'edit.php?post_type=page',
				'upload.php',
				'edit-comments.php',
			),
			'system_family' => array(
				'index.php',
				'edit.php?post_type=page',
				'upload.php',
			),
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
			),
		);

		return array_merge( $definitions, self::get_role_preset_menu_studio_definitions() );
	}

	/**
	 * Sidebar menu definitions for per-role system presets.
	 *
	 * @return array<string, string[]>
	 */
	private static function get_role_preset_menu_studio_definitions() {
		$definitions = array();
		$templates   = self::get_role_layout_templates();

		foreach ( self::get_editable_roles_list() as $role_key => $role_details ) {
			$role_key     = sanitize_key( $role_key );
			$template_key = self::resolve_role_layout_template_key( $role_key, $role_details );
			$template_key = isset( $templates[ $template_key ] ) ? $template_key : 'subscriber';
			$slugs        = array();

			foreach ( $templates[ $template_key ] as $item ) {
				if ( empty( $item['slug'] ) ) {
					continue;
				}

				$slug = (string) $item['slug'];
				if ( self::role_can_access_menu_slug( $role_key, $slug ) ) {
					$slugs[] = $slug;
				}
			}

			$definitions[ self::get_role_system_preset_id( $role_key ) ] = $slugs;
		}

		return $definitions;
	}

	/**
	 * Resolve Menu Studio settings for a layout preset.
	 *
	 * @param string     $preset_id   Preset identifier.
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return array
	 */
	public static function resolve_preset_menu_studio( $preset_id, $cc_settings = null ) {
		$preset_id = sanitize_key( $preset_id );
		$defaults  = self::get_menu_studio_defaults();

		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		$current_menu = isset( $cc_settings['menu_studio'] ) && is_array( $cc_settings['menu_studio'] )
			? wp_parse_args( $cc_settings['menu_studio'], $defaults )
			: $defaults;

		if ( 'custom' === $preset_id ) {
			return $current_menu;
		}

		if ( 'default' === $preset_id ) {
			$preset_id = self::resolve_effective_preset_id( 'default', $cc_settings );
		}

		$all_presets = self::get_all_presets();

		if ( isset( $all_presets[ $preset_id ]['menu_studio'] ) && is_array( $all_presets[ $preset_id ]['menu_studio'] ) ) {
			return wp_parse_args( $all_presets[ $preset_id ]['menu_studio'], $defaults );
		}

		$definitions = self::get_preset_menu_studio_definitions();
		$visible     = array();

		if ( isset( $definitions[ $preset_id ] ) ) {
			$visible = $definitions[ $preset_id ];
		} else {
			foreach ( self::resolve_preset_top_bar_items( $preset_id, $cc_settings ) as $item ) {
				if ( ! empty( $item['slug'] ) ) {
					$visible[] = (string) $item['slug'];
				}
			}
		}

		if ( empty( $visible ) ) {
			return $current_menu;
		}

		return self::build_menu_studio_from_visible_order( $visible, $current_menu );
	}

	/**
	 * Ordered sidebar preview items for a layout preset.
	 *
	 * @param string     $preset_id   Preset identifier.
	 * @param array|null $cc_settings Optional Command Center settings.
	 * @return array[]
	 */
	public static function resolve_preset_sidebar_preview_items( $preset_id, $cc_settings = null ) {
		$menu_studio = self::resolve_preset_menu_studio( $preset_id, $cc_settings );

		if ( empty( $menu_studio['enabled'] ) ) {
			$items = array();
			$limit = 8;

			foreach ( self::get_discovered_menu_tree() as $item ) {
				if ( count( $items ) >= $limit ) {
					break;
				}

				$items[] = array(
					'slug'  => $item['slug'],
					'label' => $item['label'],
					'icon'  => $item['icon'],
				);
			}

			return $items;
		}

		$ordered = self::resolve_menu_studio_order( $menu_studio );
		$items   = array();

		foreach ( $ordered as $item ) {
			$items[] = array(
				'slug'  => $item['slug'],
				'label' => $item['label'],
				'icon'  => isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic',
			);
		}

		return $items;
	}

	/**
	 * Build Menu Studio settings that show an ordered subset of sidebar menus.
	 *
	 * @param string[] $visible_slugs Slugs to show (top-level or submenu).
	 * @param array    $base          Existing menu_studio settings to preserve styling fields from.
	 * @return array
	 */
	private static function build_menu_studio_from_visible_order( $visible_slugs, $base = array() ) {
		$defaults  = self::get_menu_studio_defaults();
		$merged    = wp_parse_args( $base, $defaults );
		$tree      = self::get_discovered_menu_tree();
		$protected = EDMINBOOST_Menu_Studio::get_protected_slugs();
		$top_slugs = array();

		foreach ( $tree as $item ) {
			if ( ! empty( $item['slug'] ) ) {
				$top_slugs[] = (string) $item['slug'];
			}
		}

		$resolved_visible = array();

		foreach ( $visible_slugs as $slug ) {
			$slug = (string) $slug;
			if ( '' === $slug ) {
				continue;
			}

			$top_slug = self::resolve_top_level_menu_slug( $slug, $tree );
			if ( '' === $top_slug || in_array( $top_slug, $resolved_visible, true ) ) {
				continue;
			}

			$resolved_visible[] = $top_slug;
		}

		foreach ( $protected as $slug ) {
			if ( ! in_array( $slug, $resolved_visible, true ) && in_array( $slug, $top_slugs, true ) ) {
				$resolved_visible[] = $slug;
			}
		}

		$hidden = array();

		foreach ( $top_slugs as $slug ) {
			if ( in_array( $slug, $protected, true ) ) {
				continue;
			}

			if ( ! in_array( $slug, $resolved_visible, true ) ) {
				$hidden[] = $slug;
			}
		}

		$merged['enabled']        = true;
		$merged['order']          = $resolved_visible;
		$merged['hidden_items']   = $hidden;
		$merged['submenu_order']  = array();
		$merged['custom_items']   = array();

		return $merged;
	}

	/**
	 * Map a menu slug to its top-level parent in the discovered tree.
	 *
	 * @param string $slug Menu slug.
	 * @param array  $tree Optional discovered menu tree.
	 * @return string
	 */
	private static function resolve_top_level_menu_slug( $slug, $tree = null ) {
		$slug = (string) $slug;

		if ( '' === $slug ) {
			return '';
		}

		if ( null === $tree ) {
			$tree = self::get_discovered_menu_tree();
		}

		foreach ( $tree as $item ) {
			if ( empty( $item['slug'] ) ) {
				continue;
			}

			if ( $item['slug'] === $slug ) {
				return (string) $item['slug'];
			}

			if ( ! empty( $item['children'] ) && is_array( $item['children'] ) ) {
				foreach ( $item['children'] as $child ) {
					if ( isset( $child['slug'] ) && $child['slug'] === $slug ) {
						return (string) $item['slug'];
					}
				}
			}
		}

		$parent_map = array(
			'edit.php?post_type=shop_order' => 'woocommerce',
			'edit.php?post_type=product'   => 'woocommerce',
			'wc-admin'                     => 'woocommerce',
		);

		if ( isset( $parent_map[ $slug ] ) ) {
			return $parent_map[ $slug ];
		}

		if ( self::is_core_admin_slug( $slug ) ) {
			return $slug;
		}

		return '';
	}

	/**
	 * Whether a slug is a common WordPress admin screen.
	 *
	 * @param string $slug Menu slug.
	 * @return bool
	 */
	private static function is_core_admin_slug( $slug ) {
		$core_slugs = array(
			'index.php',
			'edit.php',
			'upload.php',
			'edit.php?post_type=page',
			'edit-comments.php',
			'plugins.php',
			'themes.php',
			'tools.php',
			'options-general.php',
			'users.php',
			'woocommerce',
			'edit.php?post_type=shop_order',
			'edit.php?post_type=product',
			'wc-admin',
		);

		return in_array( $slug, $core_slugs, true );
	}

	/**
	 * Apply a layout preset to plugin settings.
	 *
	 * @param string $preset_id Preset identifier.
	 * @param bool   $mark_setup_complete Whether to mark onboarding complete.
	 * @return bool True when applied.
	 */
	public static function apply_preset( $preset_id, $mark_setup_complete = true ) {
		$preset_id = sanitize_key( $preset_id );

		if ( 'custom' === $preset_id ) {
			return false;
		}

		if ( 'default' === $preset_id ) {
			$preset_id = self::resolve_effective_preset_id( 'default' );
		}

		$all = self::get_all_presets();

		if ( ! isset( $all[ $preset_id ] ) ) {
			return false;
		}

		$items = self::resolve_preset_top_bar_items( $preset_id );
		if ( empty( $items ) ) {
			return false;
		}

		$settings = EDMINBOOST_Settings::get();
		$cc       = isset( $settings['command_center'] ) && is_array( $settings['command_center'] )
			? $settings['command_center']
			: self::get_defaults();

		$preserve_onboarding = ! $mark_setup_complete && empty( $cc['onboarding_completed'] );

		$settings['command_center']               = $cc;
		$settings['command_center']['_apply_preset'] = $preset_id;
		$settings['enabled']                    = true;

		$sanitized = EDMINBOOST_Settings::sanitize( $settings );

		if ( $preserve_onboarding ) {
			$sanitized['command_center']['onboarding_completed'] = false;
		}

		update_option( EDMINBOOST_Settings::OPTION_NAME, $sanitized, false );

		return true;
	}

	/**
	 * Apply a look skin (behavior only).
	 *
	 * @param string $skin_id Skin identifier.
	 * @param bool   $mark_setup_complete Whether to mark onboarding complete.
	 * @return bool True when applied.
	 */
	public static function apply_look_skin( $skin_id, $mark_setup_complete = true ) {
		$skin_id = sanitize_key( $skin_id );
		$skins   = self::get_look_skins();

		if ( ! isset( $skins[ $skin_id ] ) ) {
			return false;
		}

		$settings = EDMINBOOST_Settings::get();
		$cc       = isset( $settings['command_center'] ) && is_array( $settings['command_center'] )
			? $settings['command_center']
			: self::get_defaults();

		$cc['behavior']             = $skins[ $skin_id ]['behavior'];
		$cc['look_skin']            = $skin_id;
		$cc['onboarding_completed'] = $mark_setup_complete || ! empty( $cc['onboarding_completed'] );

		$settings['command_center'] = $cc;
		$settings['enabled']        = true;

		$sanitized = EDMINBOOST_Settings::sanitize( $settings );
		update_option( EDMINBOOST_Settings::OPTION_NAME, $sanitized, false );

		return true;
	}

	/**
	 * Human-readable label for the active default preset.
	 *
	 * @param array|null $cc_settings Optional CC settings.
	 * @return string
	 */
	public static function get_active_preset_label( $cc_settings = null ) {
		if ( null === $cc_settings ) {
			$cc_settings = self::get_settings();
		}

		$active = self::detect_active_layout_preset( $cc_settings );
		$picker = self::get_picker_presets( true );

		if ( isset( $picker[ $active ]['name'] ) ) {
			return $picker[ $active ]['name'];
		}

		return __( 'Custom layout', EDMINBOOST_TEXT_DOMAIN );
	}

	/**
	 * All presets (system + user-saved).
	 *
	 * @return array[]
	 */
	public static function get_all_presets() {
		$cc_settings = self::get_settings();
		$system      = self::get_system_presets();
		$custom      = isset( $cc_settings['presets'] ) && is_array( $cc_settings['presets'] )
			? $cc_settings['presets']
			: array();

		return array_merge( $system, $custom );
	}

	/**
	 * Register WordPress hooks for Command Center helpers.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'cache_admin_menu_snapshot' ), 900 );
	}

	/**
	 * Capture the full admin menu before Menu Studio hides or reorders items.
	 *
	 * @return void
	 */
	public static function cache_admin_menu_snapshot() {
		if ( null !== self::$discovery_snapshot ) {
			return;
		}

		global $menu, $submenu;

		if ( ! is_array( $menu ) || empty( $menu ) ) {
			return;
		}

		$menu_copy    = array_values( $menu );
		$submenu_copy = is_array( $submenu ) ? $submenu : array();

		self::$discovery_snapshot = array(
			'menu'    => $menu_copy,
			'submenu' => $submenu_copy,
			'tree'    => self::build_discovered_menu_tree_from_globals( $menu_copy, $submenu_copy ),
		);
	}

	/**
	 * Ensure a discovery snapshot exists (AJAX tab loads skip admin_menu by default).
	 *
	 * @return void
	 */
	public static function ensure_discovery_menu_snapshot() {
		if ( null !== self::$discovery_snapshot ) {
			return;
		}

		if ( ! did_action( 'admin_menu' ) ) {
			do_action( 'admin_menu' );
		}

		self::cache_admin_menu_snapshot();

		if ( null !== self::$discovery_snapshot ) {
			return;
		}

		self::ensure_admin_menu_globals();
		self::cache_admin_menu_snapshot();
	}

	/**
	 * Menu globals used for discovery UIs (full sidebar before Menu Studio filters).
	 *
	 * @return array{menu: array, submenu: array}
	 */
	private static function get_discovery_menu_globals() {
		self::ensure_discovery_menu_snapshot();

		if ( null !== self::$discovery_snapshot ) {
			return array(
				'menu'    => self::$discovery_snapshot['menu'],
				'submenu' => self::$discovery_snapshot['submenu'],
			);
		}

		self::ensure_admin_menu_globals();

		global $menu, $submenu;

		return array(
			'menu'    => is_array( $menu ) ? $menu : array(),
			'submenu' => is_array( $submenu ) ? $submenu : array(),
		);
	}

	/**
	 * Build a discovered menu tree from raw admin menu globals.
	 *
	 * @param array $menu    Global admin menu.
	 * @param array $submenu Global admin submenu.
	 * @return array[]
	 */
	private static function build_discovered_menu_tree_from_globals( $menu, $submenu ) {
		$tree = array();

		if ( ! is_array( $menu ) ) {
			return $tree;
		}

		foreach ( $menu as $menu_item ) {
			if ( empty( $menu_item[2] ) ) {
				continue;
			}

			$slug = (string) $menu_item[2];
			if ( self::is_ignorable_menu_slug( $slug ) ) {
				continue;
			}

			$label = wp_strip_all_tags( (string) $menu_item[0] );
			if ( '' === $label ) {
				$label = $slug;
			}

			$icon     = self::normalize_menu_icon( isset( $menu_item[6] ) ? $menu_item[6] : '' );
			$children = array();

			if ( is_array( $submenu ) && isset( $submenu[ $slug ] ) && is_array( $submenu[ $slug ] ) ) {
				foreach ( $submenu[ $slug ] as $submenu_item ) {
					if ( empty( $submenu_item[2] ) ) {
						continue;
					}

					$child_slug = (string) $submenu_item[2];
					if ( self::is_ignorable_menu_slug( $child_slug ) || $child_slug === $slug ) {
						continue;
					}

					$child_label = wp_strip_all_tags( (string) $submenu_item[0] );
					if ( '' === $child_label ) {
						$child_label = $child_slug;
					}

					$children[] = array(
						'slug'  => $child_slug,
						'label' => $child_label,
					);
				}
			}

			$tree[] = array(
				'slug'     => $slug,
				'label'    => $label,
				'icon'     => $icon,
				'children' => $children,
			);
		}

		return $tree;
	}

	/**
	 * Ensure global admin menu arrays are populated for discovery.
	 *
	 * Command Center tab AJAX loads run through admin-ajax.php without wp-admin/menu.php,
	 * so $menu and $submenu are empty unless built explicitly.
	 *
	 * @return void
	 */
	private static function ensure_admin_menu_globals() {
		global $menu, $pagenow, $_wp_submenu_nopriv, $_wp_menu_nopriv;

		if ( is_array( $menu ) && ! empty( $menu ) && self::admin_menu_has_dashboard( $menu ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		// wp-admin/menu.php defines helpers at load time; including it twice is fatal.
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
			return;
		}

		if ( defined( 'WP_USER_ADMIN' ) && WP_USER_ADMIN ) {
			require ABSPATH . 'wp-admin/user/menu.php';
			return;
		}

		require ABSPATH . 'wp-admin/menu.php';
	}

	/**
	 * Whether the global admin menu includes the Dashboard entry.
	 *
	 * @param array $menu Global admin menu array.
	 * @return bool
	 */
	private static function admin_menu_has_dashboard( $menu ) {
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
	 * Discover installed admin menu items for the layout studio.
	 *
	 * Includes top-level sidebar entries and submenu pages (e.g. taxonomy screens).
	 *
	 * @return array[] Each item: slug, label, icon, source (top|submenu).
	 */
	public static function get_discovered_menu_items() {
		$globals  = self::get_discovery_menu_globals();
		$menu     = $globals['menu'];
		$submenu  = $globals['submenu'];

		$items      = array();
		$seen_slugs = array();
		$parents    = array();

		if ( is_array( $menu ) ) {
			foreach ( $menu as $menu_item ) {
				if ( empty( $menu_item[2] ) ) {
					continue;
				}

				$slug = (string) $menu_item[2];
				if ( self::is_ignorable_menu_slug( $slug ) ) {
					continue;
				}

				$label = wp_strip_all_tags( (string) $menu_item[0] );
				if ( '' === $label ) {
					$label = $slug;
				}

				$icon_raw = isset( $menu_item[6] ) ? (string) $menu_item[6] : '';
				$icon     = self::normalize_menu_icon( $icon_raw );

				$parents[ $slug ] = array(
					'label'    => $label,
					'icon'     => $icon,
					'icon_raw' => $icon_raw,
				);

				$items[]      = array(
					'slug'     => $slug,
					'label'    => $label,
					'icon'     => $icon,
					'icon_raw' => $icon_raw,
					'source'   => 'top',
				);
				$seen_slugs[] = $slug;
			}
		}

		if ( is_array( $submenu ) ) {
			foreach ( $submenu as $parent_slug => $submenu_items ) {
				if ( ! is_array( $submenu_items ) ) {
					continue;
				}

				$parent_slug   = (string) $parent_slug;
				$parent_label  = isset( $parents[ $parent_slug ]['label'] ) ? $parents[ $parent_slug ]['label'] : $parent_slug;
				$parent_icon   = isset( $parents[ $parent_slug ]['icon'] ) ? $parents[ $parent_slug ]['icon'] : 'dashicons-admin-generic';
				$parent_raw    = isset( $parents[ $parent_slug ]['icon_raw'] ) ? $parents[ $parent_slug ]['icon_raw'] : '';

				foreach ( $submenu_items as $submenu_item ) {
					if ( empty( $submenu_item[2] ) ) {
						continue;
					}

					$slug = (string) $submenu_item[2];
					if ( self::is_ignorable_menu_slug( $slug ) || $slug === $parent_slug ) {
						continue;
					}

					if ( in_array( $slug, $seen_slugs, true ) ) {
						continue;
					}

					$sub_label = wp_strip_all_tags( (string) $submenu_item[0] );
					if ( '' === $sub_label ) {
						$sub_label = $slug;
					}

					$items[] = array(
						'slug'     => $slug,
						'label'    => sprintf(
							/* translators: 1: parent menu label, 2: submenu label */
							__( '%1$s → %2$s', EDMINBOOST_TEXT_DOMAIN ),
							$parent_label,
							$sub_label
						),
						'icon'     => $parent_icon,
						'icon_raw' => $parent_raw,
						'source'   => 'submenu',
					);

					$seen_slugs[] = $slug;
				}
			}
		}

		usort(
			$items,
			function ( $a, $b ) {
				return strcasecmp( $a['label'], $b['label'] );
			}
		);

		return $items;
	}

	/**
	 * Discover admin menu as a hierarchical tree for Menu Studio.
	 *
	 * @return array[] Each item: slug, label, icon, children[].
	 */
	public static function get_discovered_menu_tree() {
		self::ensure_discovery_menu_snapshot();

		if ( null !== self::$discovery_snapshot && ! empty( self::$discovery_snapshot['tree'] ) ) {
			return self::$discovery_snapshot['tree'];
		}

		$globals = self::get_discovery_menu_globals();

		return self::build_discovered_menu_tree_from_globals( $globals['menu'], $globals['submenu'] );
	}

	/**
	 * Resolve Menu Studio sidebar order for the editor canvas.
	 *
	 * @param array|null $menu_studio Menu Studio settings.
	 * @return array[] Ordered top-level items with slug, label, icon, children.
	 */
	public static function resolve_menu_studio_order( $menu_studio = null ) {
		if ( null === $menu_studio ) {
			$cc_settings  = self::get_settings();
			$menu_studio  = isset( $cc_settings['menu_studio'] ) && is_array( $cc_settings['menu_studio'] )
				? $cc_settings['menu_studio']
				: self::get_menu_studio_defaults();
		}

		$tree         = self::get_discovered_menu_tree();
		$hidden       = isset( $menu_studio['hidden_items'] ) && is_array( $menu_studio['hidden_items'] )
			? $menu_studio['hidden_items']
			: array();
		$saved_order  = isset( $menu_studio['order'] ) && is_array( $menu_studio['order'] )
			? $menu_studio['order']
			: array();
		$submenu_map  = isset( $menu_studio['submenu_order'] ) && is_array( $menu_studio['submenu_order'] )
			? $menu_studio['submenu_order']
			: array();
		$custom_items = isset( $menu_studio['custom_items'] ) && is_array( $menu_studio['custom_items'] )
			? $menu_studio['custom_items']
			: array();

		$by_slug = array();
		foreach ( $tree as $item ) {
			$by_slug[ $item['slug'] ] = $item;
		}

		foreach ( $custom_items as $custom ) {
			if ( empty( $custom['id'] ) || empty( $custom['label'] ) ) {
				continue;
			}

			if ( ! empty( $custom['parent'] ) ) {
				continue;
			}

			$slug = 'edminboost_ms_' . sanitize_key( $custom['id'] );
			$by_slug[ $slug ] = array(
				'slug'     => $slug,
				'label'    => $custom['label'],
				'icon'     => isset( $custom['icon'] ) ? $custom['icon'] : 'dashicons-admin-links',
				'children' => array(),
				'custom'   => true,
			);
		}

		$ordered = array();
		$used    = array();

		foreach ( $saved_order as $slug ) {
			$slug = (string) $slug;
			if ( in_array( $slug, $hidden, true ) || ! isset( $by_slug[ $slug ] ) ) {
				continue;
			}

			$item = $by_slug[ $slug ];

			if ( ! empty( $item['children'] ) && isset( $submenu_map[ $slug ] ) && is_array( $submenu_map[ $slug ] ) ) {
				$item['children'] = self::reorder_submenu_children( $item['children'], $submenu_map[ $slug ] );
			}

			$ordered[]     = $item;
			$used[ $slug ] = true;
		}

		foreach ( $by_slug as $slug => $item ) {
			if ( ! empty( $used[ $slug ] ) || in_array( $slug, $hidden, true ) ) {
				continue;
			}

			if ( ! empty( $item['children'] ) && isset( $submenu_map[ $slug ] ) && is_array( $submenu_map[ $slug ] ) ) {
				$item['children'] = self::reorder_submenu_children( $item['children'], $submenu_map[ $slug ] );
			}

			$ordered[] = $item;
		}

		return $ordered;
	}

	/**
	 * Reorder submenu children according to a saved slug list.
	 *
	 * @param array  $children    Discovered children.
	 * @param string[] $slug_order Desired slug order.
	 * @return array[]
	 */
	private static function reorder_submenu_children( $children, $slug_order ) {
		$by_slug = array();
		foreach ( $children as $child ) {
			$by_slug[ $child['slug'] ] = $child;
		}

		$ordered = array();
		$used    = array();

		foreach ( $slug_order as $slug ) {
			$slug = (string) $slug;
			if ( ! isset( $by_slug[ $slug ] ) ) {
				continue;
			}

			$ordered[]     = $by_slug[ $slug ];
			$used[ $slug ] = true;
		}

		foreach ( $children as $child ) {
			if ( empty( $used[ $child['slug'] ] ) ) {
				$ordered[] = $child;
			}
		}

		return $ordered;
	}

	/**
	 * Whether a menu slug should be excluded from discovery.
	 *
	 * @param string $slug Menu slug.
	 * @return bool
	 */
	private static function is_ignorable_menu_slug( $slug ) {
		if ( '' === $slug || '---' === $slug ) {
			return true;
		}

		return 0 === strpos( $slug, 'separator' );
	}

	/**
	 * Whether a raw admin menu icon value is a dashicon slug/class (not an image URL).
	 *
	 * @param string $icon Raw menu icon value.
	 * @return bool
	 */
	private static function is_dashicon_menu_source( $icon ) {
		$icon = trim( (string) $icon );

		if ( '' === $icon || 'none' === $icon ) {
			return false;
		}

		if ( false !== strpos( $icon, '://' ) || 0 === strpos( $icon, 'data:' ) ) {
			return false;
		}

		return (bool) preg_match( '/\bdashicons-[a-z0-9-]+\b/', $icon ) || (bool) preg_match( '/^[a-z0-9-]+$/', $icon );
	}

	/**
	 * Normalize a dashicon class name.
	 *
	 * @param mixed  $icon     Raw icon value.
	 * @param string $fallback Fallback dashicon class.
	 * @return string
	 */
	public static function normalize_dashicon_class( $icon, $fallback = 'dashicons-admin-generic' ) {
		if ( empty( $icon ) ) {
			return $fallback;
		}

		$icon = trim( (string) $icon );

		if ( 'none' === $icon || false !== strpos( $icon, '://' ) || 0 === strpos( $icon, 'data:' ) ) {
			return $fallback;
		}

		if ( preg_match( '/\bdashicons-([a-z0-9-]+)\b/', $icon, $matches ) ) {
			return 'dashicons-' . $matches[1];
		}

		$icon = preg_replace( '/\s+/', '', $icon );
		if ( preg_match( '/^[a-z0-9-]+$/', $icon ) ) {
			return 'dashicons-' . $icon;
		}

		return $fallback;
	}

	/**
	 * Normalize a WordPress admin menu icon value to a dashicons class.
	 *
	 * @param mixed $icon Raw menu icon value.
	 * @return string
	 */
	private static function normalize_menu_icon( $icon ) {
		return self::normalize_dashicon_class( $icon );
	}

	/**
	 * Editable roles list (same source as wp-admin/user-new.php).
	 *
	 * @return array<string, array>
	 */
	private static function get_editable_roles_list() {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		if ( ! function_exists( 'get_editable_roles' ) ) {
			return array();
		}

		return get_editable_roles();
	}

	/**
	 * System preset id for a WordPress role key.
	 *
	 * @param string $role_key Role slug.
	 * @return string
	 */
	public static function get_role_system_preset_id( $role_key ) {
		return 'system_role_' . sanitize_key( $role_key );
	}

	/**
	 * Built-in layout presets for each editable WordPress role.
	 *
	 * @return array<string, array>
	 */
	public static function get_role_system_presets() {
		$presets = array();

		foreach ( self::get_editable_roles_list() as $role_key => $role_details ) {
			$role_key  = sanitize_key( $role_key );
			$role_name = isset( $role_details['name'] ) ? $role_details['name'] : $role_key;
			$label     = translate_user_role( $role_name );
			$preset_id = self::get_role_system_preset_id( $role_key );

			$presets[ $preset_id ] = array(
				'name'        => $label,
				'description' => sprintf(
					/* translators: %s: WordPress user role name */
					__( 'Top bar layout tuned for the %s role.', EDMINBOOST_TEXT_DOMAIN ),
					$label
				),
				'system'      => true,
				'category'    => 'workflow',
				'role'        => $role_key,
			);
		}

		return $presets;
	}

	/**
	 * Pick a layout template key for a role.
	 *
	 * @param string $role_key    Role slug.
	 * @param array  $role_details Role data from get_editable_roles().
	 * @return string
	 */
	private static function resolve_role_layout_template_key( $role_key, $role_details ) {
		$known_roles = array(
			'administrator' => 'administrator',
			'editor'        => 'editor',
			'author'        => 'author',
			'contributor'   => 'contributor',
			'subscriber'    => 'subscriber',
			'shop_manager'  => 'shop_manager',
			'customer'      => 'customer',
		);

		if ( isset( $known_roles[ $role_key ] ) ) {
			return $known_roles[ $role_key ];
		}

		$caps = isset( $role_details['capabilities'] ) && is_array( $role_details['capabilities'] )
			? $role_details['capabilities']
			: array();

		if ( ! empty( $caps['manage_options'] ) ) {
			return 'administrator';
		}

		if ( ! empty( $caps['edit_others_posts'] ) ) {
			return 'editor';
		}

		if ( ! empty( $caps['upload_files'] ) ) {
			return 'author';
		}

		if ( ! empty( $caps['edit_posts'] ) ) {
			return 'contributor';
		}

		return 'subscriber';
	}

	/**
	 * Top bar layout templates keyed by role workflow.
	 *
	 * @return array<string, array[]>
	 */
	private static function get_role_layout_templates() {
		return array(
			'administrator' => array(
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
			),
			'editor' => array(
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
			'author' => array(
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
			'contributor' => array(
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
			),
			'subscriber' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
			),
			'shop_manager' => array(
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
			'customer' => array(
				array(
					'slug'         => 'index.php',
					'label'        => __( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
					'icon'         => 'dashicons-dashboard',
					'interaction'  => 'redirect',
					'badge_source' => '',
				),
			),
		);
	}

	/**
	 * Layout definitions for per-role system presets.
	 *
	 * @return array<string, array[]>
	 */
	private static function get_role_preset_layout_definitions() {
		$definitions = array();
		$templates   = self::get_role_layout_templates();

		foreach ( self::get_editable_roles_list() as $role_key => $role_details ) {
			$role_key      = sanitize_key( $role_key );
			$template_key  = self::resolve_role_layout_template_key( $role_key, $role_details );
			$template_key  = isset( $templates[ $template_key ] ) ? $template_key : 'subscriber';
			$definitions[ self::get_role_system_preset_id( $role_key ) ] = $templates[ $template_key ];
		}

		return $definitions;
	}

	/**
	 * Get WordPress roles for the role assignment matrix.
	 *
	 * @return array Associative role key => display name.
	 */
	public static function get_assignable_roles() {
		$roles = array();

		foreach ( self::get_editable_roles_list() as $role_key => $role_details ) {
			$role_key = sanitize_key( $role_key );

			if ( '' === $role_key ) {
				continue;
			}

			$roles[ $role_key ] = isset( $role_details['name'] ) ? $role_details['name'] : $role_key;
		}

		return $roles;
	}

	/**
	 * Badge telemetry source options.
	 *
	 * @return array
	 */
	public static function get_badge_sources() {
		return array(
			''              => __( 'None', EDMINBOOST_TEXT_DOMAIN ),
			'wc_orders'     => __( 'WooCommerce — unread orders', EDMINBOOST_TEXT_DOMAIN ),
			'wc_reviews'    => __( 'WooCommerce — pending reviews', EDMINBOOST_TEXT_DOMAIN ),
			'comments'      => __( 'WordPress — pending comments', EDMINBOOST_TEXT_DOMAIN ),
			'updates'       => __( 'WordPress — available updates', EDMINBOOST_TEXT_DOMAIN ),
			'forms_entries' => __( 'WPForms — unread entries', EDMINBOOST_TEXT_DOMAIN ),
		);
	}

	/**
	 * Popular dashicons for the icon picker.
	 *
	 * @return string[]
	 */
	public static function get_dashicon_options() {
		return array(
			'dashicons-admin-generic',
			'dashicons-admin-plugins',
			'dashicons-admin-settings',
			'dashicons-admin-tools',
			'dashicons-analytics',
			'dashicons-art',
			'dashicons-awards',
			'dashicons-backup',
			'dashicons-calendar-alt',
			'dashicons-cart',
			'dashicons-chart-bar',
			'dashicons-clipboard',
			'dashicons-cloud',
			'dashicons-dashboard',
			'dashicons-edit',
			'dashicons-email',
			'dashicons-forms',
			'dashicons-format-gallery',
			'dashicons-groups',
			'dashicons-hammer',
			'dashicons-images-alt2',
			'dashicons-layout',
			'dashicons-media-document',
			'dashicons-megaphone',
			'dashicons-performance',
			'dashicons-products',
			'dashicons-search',
			'dashicons-shield',
			'dashicons-store',
			'dashicons-tag',
			'dashicons-welcome-view-site',
		);
	}
}
