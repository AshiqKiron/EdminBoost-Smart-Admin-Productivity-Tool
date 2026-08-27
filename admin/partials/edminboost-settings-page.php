<?php
/**
 * Settings page — global options, backup, and import/export.
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
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Global plugin options, settings backup, and import/export.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-settings-form edminboost-cc-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>

		<section class="edminboost-card edminboost-cc-section">
			<h2><?php esc_html_e( 'Global', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<label class="edminboost-checkbox-row" for="edminboost_enabled">
				<input type="checkbox" id="edminboost_enabled" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" <?php checked( ! empty( $settings['enabled'] ) ); ?> />
				<?php esc_html_e( 'Enable EdminBoost features and Command Center.', EDMINBOOST_TEXT_DOMAIN ); ?>
				<?php EDMINBOOST_Setting_Help::echo_icon( 'enabled' ); ?>
			</label>
		</section>

		<p class="submit"><?php submit_button( __( 'Save settings', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?></p>
	</form>

	<section class="edminboost-card edminboost-cc-section" id="edminboost-backup-section">
		<h2><?php esc_html_e( 'Backup & restore', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
		<p class="description"><?php esc_html_e( 'Export or import all EdminBoost settings as JSON. Does not include media files referenced by attachment IDs.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
		<p>
			<button type="button" class="button" id="edminboost-export-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edminboost_export_settings' ) ); ?>">
				<?php esc_html_e( 'Export settings', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
			<?php EDMINBOOST_Setting_Help::echo_icon( 'export_settings' ); ?>
		</p>
		<p>
			<label for="edminboost-import-json"><?php esc_html_e( 'Import JSON', EDMINBOOST_TEXT_DOMAIN ); ?><?php EDMINBOOST_Setting_Help::echo_icon( 'import_settings' ); ?></label><br />
			<textarea id="edminboost-import-json" class="large-text code" rows="8" placeholder="<?php esc_attr_e( 'Paste exported JSON here…', EDMINBOOST_TEXT_DOMAIN ); ?>"></textarea>
		</p>
		<p>
			<button type="button" class="button button-primary" id="edminboost-import-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edminboost_import_settings' ) ); ?>">
				<?php esc_html_e( 'Import settings', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
		</p>
	</section>
</div>
