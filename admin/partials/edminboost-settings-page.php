<?php
/**
 * Settings page — backup and import/export.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	</header>

	<section class="edminboost-card edminboost-cc-section" id="edminboost-backup-section">
		<h2><?php esc_html_e( 'Backup & restore', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
		<p class="description"><?php esc_html_e( 'Export or import all EdminBoost settings as JSON. Does not include media files referenced by attachment IDs.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
		<p class="edminboost-setting-inline">
			<?php EDMINBOOST_Setting_Help::echo_icon( 'export_settings' ); ?>
			<button type="button" class="button" id="edminboost-export-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edminboost_export_settings' ) ); ?>">
				<?php esc_html_e( 'Export settings', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
		</p>

		<fieldset class="edminboost-fieldset">
			<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'import_settings' ); ?><?php esc_html_e( 'Import settings', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
			<label class="edminboost-checkbox-row">
				<input type="radio" name="edminboost_import_method" id="edminboost-import-method-paste" value="paste" />
				<?php esc_html_e( 'Paste JSON', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>
			<label class="edminboost-checkbox-row">
				<input type="radio" name="edminboost_import_method" id="edminboost-import-method-file" value="file" checked />
				<?php esc_html_e( 'Import file', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>
		</fieldset>

		<div id="edminboost-import-paste-panel" class="edminboost-import-panel edminboost-import-panel--paste">
			<p>
				<label for="edminboost-import-json"><?php esc_html_e( 'JSON payload', EDMINBOOST_TEXT_DOMAIN ); ?></label><br />
				<textarea id="edminboost-import-json" class="large-text code" rows="8" placeholder="<?php esc_attr_e( 'Paste exported JSON here…', EDMINBOOST_TEXT_DOMAIN ); ?>"></textarea>
			</p>
		</div>

		<div id="edminboost-import-file-panel" class="edminboost-import-panel edminboost-import-panel--file">
			<p>
				<label for="edminboost-import-file"><?php esc_html_e( 'JSON file', EDMINBOOST_TEXT_DOMAIN ); ?></label><br />
				<input type="file" id="edminboost-import-file" accept=".json,application/json" />
			</p>
		</div>

		<p>
			<button type="button" class="button button-primary" id="edminboost-import-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edminboost_import_settings' ) ); ?>">
				<?php esc_html_e( 'Import settings', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
		</p>
	</section>
</div>
