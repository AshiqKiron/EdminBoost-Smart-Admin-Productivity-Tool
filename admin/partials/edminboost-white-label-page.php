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

$edminboost_option_name        = EDMINBOOST_Settings::OPTION_NAME;
$edminboost_settings           = EDMINBOOST_Settings::get();
$edminboost_wl                 = $edminboost_settings['white_label'];
$edminboost_wl_key             = $edminboost_option_name . '[white_label]';
$edminboost_wl_plugin_defaults = EDMINBOOST_White_Label::get_plugin_header_defaults();
$edminboost_wl_enabled         = ! empty( $edminboost_wl['enabled'] );
$edminboost_wl_section_class   = 'edminboost-card edminboost-cc-section' . ( $edminboost_wl_enabled ? '' : ' is-disabled' );
$edminboost_wl_section_aria    = $edminboost_wl_enabled ? 'false' : 'true';
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead"><?php esc_html_e( 'Agency branding and system status footer.', 'edminboost' ); ?></p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form edminboost-settings-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[enabled]" value="1" />

		<section class="edminboost-card edminboost-cc-section">
			<h2><?php esc_html_e( 'White label', 'edminboost' ); ?></h2>
			<label class="edminboost-checkbox-row" for="edminboost_wl_enabled">
				<input type="checkbox" id="edminboost_wl_enabled" name="<?php echo esc_attr( $edminboost_wl_key ); ?>[enabled]" value="1" <?php checked( ! empty( $edminboost_wl['enabled'] ) ); ?> />
				<?php EDMINBOOST_Setting_Help::echo_icon( 'wl_enabled' ); ?>
				<?php esc_html_e( 'Enable white-label branding.', 'edminboost' ); ?>
			</label>
			<label class="edminboost-checkbox-row" for="edminboost_wl_hide_credit">
				<input type="checkbox" id="edminboost_wl_hide_credit" name="<?php echo esc_attr( $edminboost_wl_key ); ?>[hide_wp_footer_credit]" value="1" <?php checked( ! empty( $edminboost_wl['hide_wp_footer_credit'] ) ); ?> />
				<?php EDMINBOOST_Setting_Help::echo_icon( 'wl_hide_credit' ); ?>
				<?php esc_html_e( 'Hide default WordPress footer credit.', 'edminboost' ); ?>
			</label>
		</section>

		<section id="edminboost-wl-status-section" class="<?php echo esc_attr( $edminboost_wl_section_class ); ?>" aria-disabled="<?php echo esc_attr( $edminboost_wl_section_aria ); ?>">
			<h2><?php esc_html_e( 'System status footer', 'edminboost' ); ?></h2>
			<div class="edminboost-wl-status-layout">
				<div class="edminboost-wl-status-fields">
					<?php
					$edminboost_status_fields = array(
						'show_ip'               => array(
							'label' => __( 'Show IP address', 'edminboost' ),
							'help'  => 'wl_show_ip',
						),
						'show_php_version'      => array(
							'label' => __( 'Show PHP version', 'edminboost' ),
							'help'  => 'wl_show_php_version',
						),
						'show_wp_version'       => array(
							'label' => __( 'Show WordPress version', 'edminboost' ),
							'help'  => 'wl_show_wp_version',
						),
						'show_memory_usage'     => array(
							'label' => __( 'Show memory usage', 'edminboost' ),
							'help'  => 'wl_show_memory_usage',
						),
						'show_memory_limit'     => array(
							'label' => __( 'Show memory limit', 'edminboost' ),
							'help'  => 'wl_show_memory_limit',
						),
						'show_memory_available' => array(
							'label' => __( 'Show memory available', 'edminboost' ),
							'help'  => 'wl_show_memory_available',
						),
					);
					foreach ( $edminboost_status_fields as $edminboost_field_key => $edminboost_field_meta ) :
						?>
						<label class="edminboost-checkbox-row" for="edminboost_wl_<?php echo esc_attr( $edminboost_field_key ); ?>">
							<input type="checkbox" id="edminboost_wl_<?php echo esc_attr( $edminboost_field_key ); ?>" name="<?php echo esc_attr( $edminboost_wl_key ); ?>[<?php echo esc_attr( $edminboost_field_key ); ?>]" value="1" <?php checked( ! empty( $edminboost_wl[ $edminboost_field_key ] ) ); ?> />
							<?php EDMINBOOST_Setting_Help::echo_icon( $edminboost_field_meta['help'] ); ?>
							<?php echo esc_html( $edminboost_field_meta['label'] ); ?>
						</label>
					<?php endforeach; ?>
				</div>
				<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-white-label-status-preview.php'; ?>
			</div>
		</section>

		<section id="edminboost-wl-rebrand-section" class="<?php echo esc_attr( $edminboost_wl_section_class ); ?>" aria-disabled="<?php echo esc_attr( $edminboost_wl_section_aria ); ?>">
			<h2><?php esc_html_e( 'Plugin rebranding', 'edminboost' ); ?></h2>
			<div class="edminboost-wl-rebrand-layout">
				<div class="edminboost-wl-rebrand-fields">
					<div class="edminboost-wl-rebrand-fields__row">
						<p class="edminboost-wl-rebrand-fields__field">
							<label for="edminboost_wl_plugin_name"><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_plugin_name' ); ?><?php esc_html_e( 'Plugin name', 'edminboost' ); ?>
								<input type="text" class="regular-text" id="edminboost_wl_plugin_name" name="<?php echo esc_attr( $edminboost_wl_key ); ?>[plugin_name]" value="<?php echo esc_attr( $edminboost_wl['plugin_name'] ?? '' ); ?>" />
							</label>
						</p>
						<p class="edminboost-wl-rebrand-fields__field">
							<label for="edminboost_wl_plugin_author"><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_plugin_author' ); ?><?php esc_html_e( 'Author / agency name', 'edminboost' ); ?>
								<input type="text" class="regular-text" id="edminboost_wl_plugin_author" name="<?php echo esc_attr( $edminboost_wl_key ); ?>[plugin_author]" value="<?php echo esc_attr( $edminboost_wl['plugin_author'] ?? '' ); ?>" />
							</label>
						</p>
					</div>
					<p class="edminboost-wl-rebrand-fields__field">
						<label for="edminboost_wl_plugin_description"><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_plugin_description' ); ?><?php esc_html_e( 'Plugin description', 'edminboost' ); ?>
							<textarea class="large-text" rows="3" id="edminboost_wl_plugin_description" name="<?php echo esc_attr( $edminboost_wl_key ); ?>[plugin_description]"><?php echo esc_textarea( $edminboost_wl['plugin_description'] ?? '' ); ?></textarea>
						</label>
					</p>
					<div class="edminboost-wl-rebrand-fields__row">
						<p class="edminboost-wl-rebrand-fields__field">
							<label for="edminboost_wl_plugin_uri"><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_plugin_uri' ); ?><?php esc_html_e( 'Plugin URL', 'edminboost' ); ?>
								<input type="url" class="regular-text" id="edminboost_wl_plugin_uri" name="<?php echo esc_attr( $edminboost_wl_key ); ?>[plugin_uri]" value="<?php echo esc_attr( $edminboost_wl['plugin_uri'] ?? '' ); ?>" />
							</label>
						</p>
						<p class="edminboost-wl-rebrand-fields__field">
							<label for="edminboost_wl_menu_label"><?php EDMINBOOST_Setting_Help::echo_icon( 'wl_menu_label' ); ?><?php esc_html_e( 'Admin menu label', 'edminboost' ); ?>
								<input type="text" class="regular-text" id="edminboost_wl_menu_label" name="<?php echo esc_attr( $edminboost_wl_key ); ?>[menu_label]" value="<?php echo esc_attr( $edminboost_wl['menu_label'] ?? '' ); ?>" />
							</label>
						</p>
					</div>
				</div>
				<?php
				$edminboost_defaults = $edminboost_wl_plugin_defaults;
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-white-label-plugin-preview.php';
				?>
			</div>
		</section>

		<?php
		$edminboost_save_label = __( 'Save white label settings', 'edminboost' );
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-form-actions.php';
		?>
	</form>
</div>
