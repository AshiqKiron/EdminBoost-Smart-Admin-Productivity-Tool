<?php
/**
 * Settings backup and import (Pro/Agency).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="edminboost-card edminboost-cc-section" id="edminboost-backup-section">
	<h2><?php esc_html_e( 'Backup & restore', 'edminboost-admin-customization' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Export or import all EdminBoost settings as JSON. Does not include media files referenced by attachment IDs.', 'edminboost-admin-customization' ); ?></p>
	<p class="edminboost-setting-inline">
		<?php EDMINBOOST_Setting_Help::echo_icon( 'export_settings' ); ?>
		<button type="button" class="button" id="edminboost-export-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edminboost_export_settings' ) ); ?>">
			<?php esc_html_e( 'Export settings', 'edminboost-admin-customization' ); ?>
		</button>
	</p>

	<fieldset class="edminboost-fieldset">
		<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'import_settings' ); ?><?php esc_html_e( 'Import settings', 'edminboost-admin-customization' ); ?></legend>
		<label class="edminboost-checkbox-row">
			<input type="radio" name="edminboost_import_method" id="edminboost-import-method-paste" value="paste" />
			<?php esc_html_e( 'Paste JSON', 'edminboost-admin-customization' ); ?>
		</label>
		<label class="edminboost-checkbox-row">
			<input type="radio" name="edminboost_import_method" id="edminboost-import-method-file" value="file" checked />
			<?php esc_html_e( 'Import file', 'edminboost-admin-customization' ); ?>
		</label>
	</fieldset>

	<div id="edminboost-import-paste-panel" class="edminboost-import-panel edminboost-import-panel--paste">
		<p>
			<label for="edminboost-import-json"><?php esc_html_e( 'JSON payload', 'edminboost-admin-customization' ); ?></label><br />
			<textarea id="edminboost-import-json" class="large-text code" rows="8" placeholder="<?php esc_attr_e( 'Paste exported JSON here…', 'edminboost-admin-customization' ); ?>"></textarea>
		</p>
	</div>

	<div id="edminboost-import-file-panel" class="edminboost-import-panel edminboost-import-panel--file">
		<p>
			<label for="edminboost-import-file"><?php esc_html_e( 'JSON file', 'edminboost-admin-customization' ); ?></label><br />
			<input type="file" id="edminboost-import-file" accept=".json,application/json" />
		</p>
	</div>

	<p>
		<button type="button" class="button button-primary" id="edminboost-import-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edminboost_import_settings' ) ); ?>">
			<?php esc_html_e( 'Import settings', 'edminboost-admin-customization' ); ?>
		</button>
	</p>
</section>
