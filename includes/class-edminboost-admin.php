<?php
/**
 * Admin area — menus, settings pages, asset enqueuing.
 *
 * Purpose: Register admin UI via admin_menu and Settings API.
 *          Render dashboard/settings partials. Enqueue assets only on plugin screens.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin handler.
 */
class EDMINBOOST_Admin {

	/**
	 * Admin page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = EDMINBOOST_PLUGIN_SLUG;

	/**
	 * Feature manager.
	 *
	 * @var EDMINBOOST_Features
	 */
	protected $features;

	/**
	 * Constructor.
	 *
	 * @param EDMINBOOST_Features $features Feature manager.
	 */
	public function __construct( EDMINBOOST_Features $features ) {
		$this->features = $features;
	}

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'EdminBoost', EDMINBOOST_TEXT_DOMAIN ),
			__( 'EdminBoost', EDMINBOOST_TEXT_DOMAIN ),
			EDMINBOOST_Settings::CAPABILITY,
			self::PAGE_SLUG,
			array( $this, 'render_admin_page' ),
			'dashicons-performance',
			72
		);

		add_submenu_page(
			self::PAGE_SLUG,
			__( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
			__( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
			EDMINBOOST_Settings::CAPABILITY,
			self::PAGE_SLUG,
			array( $this, 'render_admin_page' )
		);

		add_submenu_page(
			self::PAGE_SLUG,
			__( 'Onboarding', EDMINBOOST_TEXT_DOMAIN ),
			__( 'Onboarding', EDMINBOOST_TEXT_DOMAIN ),
			EDMINBOOST_Settings::CAPABILITY,
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_ONBOARDING,
			array( $this, 'render_onboarding_page' )
		);

		add_submenu_page(
			self::PAGE_SLUG,
			__( 'Layout Studio', EDMINBOOST_TEXT_DOMAIN ),
			__( 'Layout Studio', EDMINBOOST_TEXT_DOMAIN ),
			EDMINBOOST_Settings::CAPABILITY,
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER,
			array( $this, 'render_mapper_page' )
		);

		add_submenu_page(
			self::PAGE_SLUG,
			__( 'Presets & Roles', EDMINBOOST_TEXT_DOMAIN ),
			__( 'Presets & Roles', EDMINBOOST_TEXT_DOMAIN ),
			EDMINBOOST_Settings::CAPABILITY,
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRESETS,
			array( $this, 'render_presets_page' )
		);

		add_submenu_page(
			self::PAGE_SLUG,
			__( 'Behavior & Style', EDMINBOOST_TEXT_DOMAIN ),
			__( 'Behavior & Style', EDMINBOOST_TEXT_DOMAIN ),
			EDMINBOOST_Settings::CAPABILITY,
			self::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_BEHAVIOR,
			array( $this, 'render_behavior_page' )
		);

		add_submenu_page(
			self::PAGE_SLUG,
			__( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
			__( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
			EDMINBOOST_Settings::CAPABILITY,
			self::PAGE_SLUG . '-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			EDMINBOOST_Settings::SETTINGS_GROUP,
			EDMINBOOST_Settings::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'EDMINBOOST_Settings', 'sanitize' ),
				'default'           => EDMINBOOST_Settings::get_defaults(),
				'show_in_rest'      => false,
			)
		);

		add_settings_section(
			'edminboost_general_section',
			__( 'General', EDMINBOOST_TEXT_DOMAIN ),
			array( $this, 'render_general_section' ),
			self::PAGE_SLUG . '-settings'
		);

		add_settings_field(
			'edminboost_enabled',
			__( 'Enable EdminBoost', EDMINBOOST_TEXT_DOMAIN ),
			array( $this, 'render_enabled_field' ),
			self::PAGE_SLUG . '-settings',
			'edminboost_general_section'
		);

		add_settings_section(
			'edminboost_features_section',
			__( 'Productivity Features', EDMINBOOST_TEXT_DOMAIN ),
			array( $this, 'render_features_section' ),
			self::PAGE_SLUG . '-settings'
		);

		add_settings_field(
			'edminboost_features',
			__( 'Feature Controls', EDMINBOOST_TEXT_DOMAIN ),
			array( $this, 'render_features_field' ),
			self::PAGE_SLUG . '-settings',
			'edminboost_features_section'
		);
	}

	/**
	 * Get plugin settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		return EDMINBOOST_Settings::get();
	}

	/**
	 * Render the dashboard page.
	 *
	 * @return void
	 */
	public function render_admin_page() {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		$settings = $this->get_settings();
		$features = $this->features->get_features();

		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-admin-page.php';
	}

	/**
	 * Render the onboarding wizard page.
	 *
	 * @return void
	 */
	public function render_onboarding_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-onboarding-page.php' );
	}

	/**
	 * Render the layout mapper page.
	 *
	 * @return void
	 */
	public function render_mapper_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-mapper-page.php' );
	}

	/**
	 * Render the presets & roles page.
	 *
	 * @return void
	 */
	public function render_presets_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-presets-page.php' );
	}

	/**
	 * Render the behavior & styling page.
	 *
	 * @return void
	 */
	public function render_behavior_page() {
		$this->render_command_center_page( 'admin/partials/edminboost-behavior-page.php' );
	}

	/**
	 * Render a Command Center partial.
	 *
	 * @param string $partial Relative path under the plugin directory.
	 * @return void
	 */
	private function render_command_center_page( $partial ) {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		$cc_settings  = EDMINBOOST_Command_Center::get_settings();
		$current_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		include EDMINBOOST_PLUGIN_DIR . $partial;
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( EDMINBOOST_Settings::CAPABILITY ) ) {
			return;
		}

		?>
		<div class="wrap edminboost-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP );
				do_settings_sections( self::PAGE_SLUG . '-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the general settings section description.
	 *
	 * @return void
	 */
	public function render_general_section() {
		echo '<p>' . esc_html__( 'Configure core EdminBoost behavior.', EDMINBOOST_TEXT_DOMAIN ) . '</p>';
	}

	/**
	 * Render the features settings section description.
	 *
	 * @return void
	 */
	public function render_features_section() {
		echo '<p>' . esc_html__( 'Enable individual productivity tools. All features apply only in the WordPress admin area.', EDMINBOOST_TEXT_DOMAIN ) . '</p>';
	}

	/**
	 * Render the enabled checkbox field.
	 *
	 * @return void
	 */
	public function render_enabled_field() {
		$settings = $this->get_settings();
		?>
		<label for="edminboost_enabled">
			<input
				type="checkbox"
				id="edminboost_enabled"
				name="<?php echo esc_attr( EDMINBOOST_Settings::OPTION_NAME ); ?>[enabled]"
				value="1"
				<?php checked( ! empty( $settings['enabled'] ) ); ?>
			/>
			<?php esc_html_e( 'Turn on smart admin productivity features.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</label>
		<?php
	}

	/**
	 * Render feature control fields.
	 *
	 * @return void
	 */
	public function render_features_field() {
		$settings = $this->get_settings();

		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-settings-features.php';
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {
		if ( ! $this->is_plugin_screen( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_style(
			'edminboost-admin',
			EDMINBOOST_PLUGIN_URL . 'admin/css/edminboost-admin.css',
			array(),
			EDMINBOOST_VERSION
		);
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix ) {
		if ( ! $this->is_plugin_screen( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_script(
			'edminboost-admin',
			EDMINBOOST_PLUGIN_URL . 'admin/js/edminboost-admin.js',
			array(),
			EDMINBOOST_VERSION,
			true
		);

		$screen_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		wp_localize_script(
			'edminboost-admin',
			'edminboostData',
			array(
				'version'    => EDMINBOOST_VERSION,
				'currentPage' => $screen_page,
				'optionName' => EDMINBOOST_Settings::OPTION_NAME,
				'strings'    => array(
					'ready'           => __( 'EdminBoost is ready.', EDMINBOOST_TEXT_DOMAIN ),
					'configureItem'   => __( 'Configure', EDMINBOOST_TEXT_DOMAIN ),
					'removeFromTopBar' => __( 'Remove from top bar', EDMINBOOST_TEXT_DOMAIN ),
					'emptyCanvas'     => __( 'Toggle items from the left panel or drag them here to build your top bar.', EDMINBOOST_TEXT_DOMAIN ),
					'exportSuccess'   => __( 'Preset exported.', EDMINBOOST_TEXT_DOMAIN ),
					'customLinkPathRequired'  => __( 'Enter an admin path.', EDMINBOOST_TEXT_DOMAIN ),
					'customLinkLabelRequired' => __( 'Enter a label.', EDMINBOOST_TEXT_DOMAIN ),
					'customLinkPathInvalid'   => __( 'Use a relative admin path such as edit.php?post_type=page.', EDMINBOOST_TEXT_DOMAIN ),
					'customLinkDuplicate'     => __( 'That link is already on your top bar.', EDMINBOOST_TEXT_DOMAIN ),
				),
			)
		);
	}

	/**
	 * Add a settings link on the plugins screen.
	 *
	 * @param array $links Existing plugin action links.
	 * @return array
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG . '-settings' ) ),
			esc_html__( 'Settings', EDMINBOOST_TEXT_DOMAIN )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Determine whether the current screen belongs to this plugin.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return bool
	 */
	private function is_plugin_screen( $hook_suffix ) {
		return false !== strpos( $hook_suffix, self::PAGE_SLUG );
	}
}
