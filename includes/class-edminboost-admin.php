<?php
/**
 * Admin area functionality.
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
	 * Settings option name.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'edminboost_settings';

	/**
	 * Admin page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'edminboost-smart-admin-roductivity-tool';

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			esc_html__( 'Edmin Boost', EDMINBOOST_TEXT_DOMAIN ),
			esc_html__( 'Edmin Boost', EDMINBOOST_TEXT_DOMAIN ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_admin_page' ),
			'dashicons-performance',
			72
		);

		add_submenu_page(
			self::PAGE_SLUG,
			esc_html__( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
			esc_html__( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_admin_page' )
		);

		add_submenu_page(
			self::PAGE_SLUG,
			esc_html__( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
			esc_html__( 'Settings', EDMINBOOST_TEXT_DOMAIN ),
			'manage_options',
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
			'edminboost_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(
					'enabled' => true,
				),
			)
		);

		add_settings_section(
			'edminboost_general_section',
			esc_html__( 'General', EDMINBOOST_TEXT_DOMAIN ),
			array( $this, 'render_general_section' ),
			self::PAGE_SLUG . '-settings'
		);

		add_settings_field(
			'edminboost_enabled',
			esc_html__( 'Enable Edmin Boost', EDMINBOOST_TEXT_DOMAIN ),
			array( $this, 'render_enabled_field' ),
			self::PAGE_SLUG . '-settings',
			'edminboost_general_section'
		);
	}

	/**
	 * Sanitize settings input.
	 *
	 * @param array $input Raw settings input.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$sanitized = $this->get_settings();

		if ( isset( $input['enabled'] ) ) {
			$sanitized['enabled'] = (bool) $input['enabled'];
		}

		return $sanitized;
	}

	/**
	 * Get plugin settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		$defaults = array(
			'enabled' => true,
		);

		$settings = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Render the dashboard page.
	 *
	 * @return void
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = $this->get_settings();

		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-admin-page.php';
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap edminboost-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'edminboost_settings_group' );
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
		echo '<p>' . esc_html__( 'Configure core Edmin Boost behavior.', EDMINBOOST_TEXT_DOMAIN ) . '</p>';
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
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[enabled]"
				value="1"
				<?php checked( ! empty( $settings['enabled'] ) ); ?>
			/>
			<?php esc_html_e( 'Turn on smart admin productivity features.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</label>
		<?php
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

		wp_localize_script(
			'edminboost-admin',
			'edminBoostData',
			array(
				'version' => EDMINBOOST_VERSION,
				'strings' => array(
					'ready' => esc_html__( 'Edmin Boost is ready.', EDMINBOOST_TEXT_DOMAIN ),
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
