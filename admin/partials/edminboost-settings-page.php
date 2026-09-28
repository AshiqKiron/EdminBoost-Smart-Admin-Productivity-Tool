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

	<?php if ( EDMINBOOST_Pro::shows_pro_settings_ui() ) : ?>
	<section class="edminboost-card edminboost-cc-section <?php echo esc_attr( EDMINBOOST_Pro::section_class() ); ?>" id="edminboost-backup-section"<?php EDMINBOOST_Pro::echo_feature_attr( 'export_import' ); ?>>
		<h2><?php esc_html_e( 'Backup & restore', 'edminboost-smart-admin-productivity-tool' ); ?> <?php EDMINBOOST_Pro::render_badge(); ?></h2>
		<p class="description"><?php esc_html_e( 'Export or import all EdminBoost settings as JSON. Does not include media files referenced by attachment IDs.', 'edminboost-smart-admin-productivity-tool' ); ?></p>
		<p class="edminboost-setting-inline">
			<?php EDMINBOOST_Setting_Help::echo_icon( 'export_settings' ); ?>
			<button type="button" class="button" id="edminboost-export-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edminboost_export_settings' ) ); ?>">
				<?php esc_html_e( 'Export settings', 'edminboost-smart-admin-productivity-tool' ); ?>
			</button>
		</p>

		<fieldset class="edminboost-fieldset">
			<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'import_settings' ); ?><?php esc_html_e( 'Import settings', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
			<label class="edminboost-checkbox-row">
				<input type="radio" name="edminboost_import_method" id="edminboost-import-method-paste" value="paste" />
				<?php esc_html_e( 'Paste JSON', 'edminboost-smart-admin-productivity-tool' ); ?>
			</label>
			<label class="edminboost-checkbox-row">
				<input type="radio" name="edminboost_import_method" id="edminboost-import-method-file" value="file" checked />
				<?php esc_html_e( 'Import file', 'edminboost-smart-admin-productivity-tool' ); ?>
			</label>
		</fieldset>

		<div id="edminboost-import-paste-panel" class="edminboost-import-panel edminboost-import-panel--paste">
			<p>
				<label for="edminboost-import-json"><?php esc_html_e( 'JSON payload', 'edminboost-smart-admin-productivity-tool' ); ?></label><br />
				<textarea id="edminboost-import-json" class="large-text code" rows="8" placeholder="<?php esc_attr_e( 'Paste exported JSON here…', 'edminboost-smart-admin-productivity-tool' ); ?>"></textarea>
			</p>
		</div>

		<div id="edminboost-import-file-panel" class="edminboost-import-panel edminboost-import-panel--file">
			<p>
				<label for="edminboost-import-file"><?php esc_html_e( 'JSON file', 'edminboost-smart-admin-productivity-tool' ); ?></label><br />
				<input type="file" id="edminboost-import-file" accept=".json,application/json" />
			</p>
		</div>

		<p>
			<button type="button" class="button button-primary" id="edminboost-import-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edminboost_import_settings' ) ); ?>">
				<?php esc_html_e( 'Import settings', 'edminboost-smart-admin-productivity-tool' ); ?>
			</button>
		</p>
		<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-pro-upgrade.php'; ?>
	</section>
	<?php endif; ?>
</div>
