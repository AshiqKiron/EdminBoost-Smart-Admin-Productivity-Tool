<?php
/**
 * White Label settings page.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name = EDMINBOOST_Settings::OPTION_NAME;
$settings    = EDMINBOOST_Settings::get();
$wl          = $settings['white_label'];
$wl_key      = $option_name . '[white_label]';
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead"><?php esc_html_e( 'Agency branding, login customization, and system status footer.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form edminboost-settings-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />

		<section class="edminboost-card edminboost-cc-section">
			<h2><?php esc_html_e( 'White label', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<label class="edminboost-checkbox-row" for="edminboost_wl_enabled">
				<input type="checkbox" id="edminboost_wl_enabled" name="<?php echo esc_attr( $wl_key ); ?>[enabled]" value="1" <?php checked( ! empty( $wl['enabled'] ) ); ?> />
				<?php EDMINBOOST_Setting_Help::echo_icon( 'wl_enabled' ); ?>
				<?php esc_html_e( 'Enable white-label branding.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>
			<label class="edminboost-checkbox-row" for="edminboost_wl_hide_credit">
				<input type="checkbox" id="edminboost_wl_hide_credit" name="<?php echo esc_attr( $wl_key ); ?>[hide_wp_footer_credit]" value="1" <?php checked( ! empty( $wl['hide_wp_footer_credit'] ) ); ?> />
				<?php EDMINBOOST_Setting_Help::echo_icon( 'wl_hide_credit' ); ?>
				<?php esc_html_e( 'Hide default WordPress footer credit.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>
		</section>

		<section class="edminboost-card edminboost-cc-section">
			<h2><?php esc_html_e( 'System status footer', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<?php
			$status_fields = array(
				'show_ip'               => array(
					'label' => __( 'Show IP address', EDMINBOOST_TEXT_DOMAIN ),
					'help'  => 'wl_show_ip',
				),
				'show_php_version'      => array(
					'label' => __( 'Show PHP version', EDMINBOOST_TEXT_DOMAIN ),
					'help'  => 'wl_show_php_version',
				),
				'show_wp_version'       => array(
					'label' => __( 'Show WordPress version', EDMINBOOST_TEXT_DOMAIN ),
					'help'  => 'wl_show_wp_version',
				),
				'show_memory_usage'     => array(
					'label' => __( 'Show memory usage', EDMINBOOST_TEXT_DOMAIN ),
					'help'  => 'wl_show_memory_usage',
				),
				'show_memory_limit'     => array(
					'label' => __( 'Show memory limit', EDMINBOOST_TEXT_DOMAIN ),
					'help'  => 'wl_show_memory_limit',
				),
				'show_memory_available' => array(
					'label' => __( 'Show memory available', EDMINBOOST_TEXT_DOMAIN ),
					'help'  => 'wl_show_memory_available',
				),
			);
			foreach ( $status_fields as $field_key => $field_meta ) :
				?>
				<label class="edminboost-checkbox-row" for="edminboost_wl_<?php echo esc_attr( $field_key ); ?>">
					<input type="checkbox" id="edminboost_wl_<?php echo esc_attr( $field_key ); ?>" name="<?php echo esc_attr( $wl_key ); ?>[<?php echo esc_attr( $field_key ); ?>]" value="1" <?php checked( ! empty( $wl[ $field_key ] ) ); ?> />
					<?php EDMINBOOST_Setting_Help::echo_icon( $field_meta['help'] ); ?>
					<?php echo esc_html( $field_meta['label'] ); ?>
				</label>
			<?php endforeach; ?>
		</section>

		<section class="edminboost-card edminboost-cc-section">
			<h2><?php esc_html_e( 'Login branding', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p>
				<label for="edminboost_wl_login_logo_id"><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_login_logo' ); ?><?php esc_html_e( 'Login logo attachment ID', EDMINBOOST_TEXT_DOMAIN ); ?>
					<input type="number" class="small-text" id="edminboost_wl_login_logo_id" name="<?php echo esc_attr( $wl_key ); ?>[login_logo_id]" value="<?php echo esc_attr( $wl['login_logo_id'] ?? 0 ); ?>" min="0" />
				</label>
			</p>
			<p>
				<label for="edminboost_wl_login_bg"><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_login_bg' ); ?><?php esc_html_e( 'Login background color', EDMINBOOST_TEXT_DOMAIN ); ?>
					<input type="text" class="regular-text" id="edminboost_wl_login_bg" name="<?php echo esc_attr( $wl_key ); ?>[login_bg_color]" value="<?php echo esc_attr( $wl['login_bg_color'] ?? '' ); ?>" placeholder="#f0f0f1" />
				</label>
			</p>
		</section>

		<section class="edminboost-card edminboost-cc-section">
			<h2><?php esc_html_e( 'Plugin rebranding', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p><label><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_plugin_name' ); ?><?php esc_html_e( 'Plugin name', EDMINBOOST_TEXT_DOMAIN ); ?> <input type="text" class="regular-text" name="<?php echo esc_attr( $wl_key ); ?>[plugin_name]" value="<?php echo esc_attr( $wl['plugin_name'] ?? '' ); ?>" /></label></p>
			<p><label><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_plugin_description' ); ?><?php esc_html_e( 'Plugin description', EDMINBOOST_TEXT_DOMAIN ); ?> <textarea class="large-text" rows="3" name="<?php echo esc_attr( $wl_key ); ?>[plugin_description]"><?php echo esc_textarea( $wl['plugin_description'] ?? '' ); ?></textarea></label></p>
			<p><label><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_plugin_author' ); ?><?php esc_html_e( 'Author / agency name', EDMINBOOST_TEXT_DOMAIN ); ?> <input type="text" class="regular-text" name="<?php echo esc_attr( $wl_key ); ?>[plugin_author]" value="<?php echo esc_attr( $wl['plugin_author'] ?? '' ); ?>" /></label></p>
			<p><label><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_plugin_uri' ); ?><?php esc_html_e( 'Plugin URL', EDMINBOOST_TEXT_DOMAIN ); ?> <input type="url" class="regular-text" name="<?php echo esc_attr( $wl_key ); ?>[plugin_uri]" value="<?php echo esc_attr( $wl['plugin_uri'] ?? '' ); ?>" /></label></p>
			<p><label><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_menu_label' ); ?><?php esc_html_e( 'Admin menu label', EDMINBOOST_TEXT_DOMAIN ); ?> <input type="text" class="regular-text" name="<?php echo esc_attr( $wl_key ); ?>[menu_label]" value="<?php echo esc_attr( $wl['menu_label'] ?? '' ); ?>" /></label></p>
			<label class="edminboost-checkbox-row" for="edminboost_wl_lock">
				<input type="checkbox" id="edminboost_wl_lock" name="<?php echo esc_attr( $wl_key ); ?>[lock_white_label]" value="1" <?php checked( ! empty( $wl['lock_white_label'] ) ); ?> />
				<?php EDMINBOOST_Setting_Help::echo_icon( 'wl_lock' ); ?>
				<?php esc_html_e( 'Lock white-label settings (hide this page until plugin is reactivated).', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>
		</section>

		<p class="submit"><?php submit_button( __( 'Save white label settings', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?></p>
	</form>
</div>
