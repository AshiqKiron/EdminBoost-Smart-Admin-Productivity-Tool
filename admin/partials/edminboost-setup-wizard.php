<?php
/**
 * First-run setup wizard (single-page stepper).
 *
 * @package EdminBoost
 *
 * @var string $edminboost_option_name Settings option name (prefixed include variable).
 * @var array  $cc_settings            Command Center settings.
 * @var string $edminboost_theme_key   Form field prefix for theme.
 * @var array  $edminboost_theme       Current theme settings.
 * @var string $edminboost_mapper_url  Top Bar editor URL.
 * @var string $edminboost_preset_picker_mode Picker mode passed to preset picker (`wizard`).
 * @var string $edminboost_wizard_preset     Initial layout preset for the wizard.
 * @var array  $edminboost_preview_items     Top bar items preview for wizard step 3.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $edminboost_option_name ) ) {
	$edminboost_option_name = EDMINBOOST_Settings::OPTION_NAME;
}
if ( ! isset( $edminboost_theme ) ) {
	$edminboost_theme = EDMINBOOST_Theme::get_settings( $cc_settings );
}
if ( ! isset( $edminboost_theme_key ) ) {
	$edminboost_theme_key = $edminboost_option_name . '[command_center][theme]';
}
if ( ! isset( $edminboost_mapper_url ) ) {
	$edminboost_mapper_url = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER );
}

$edminboost_default_preset     = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';
$edminboost_all_presets        = EDMINBOOST_Command_Center::get_all_presets();
$edminboost_wizard_preset      = isset( $edminboost_all_presets[ $edminboost_default_preset ] ) && ! empty( $edminboost_all_presets[ $edminboost_default_preset ]['system'] )
	? $edminboost_default_preset
	: 'system_client';
$edminboost_preview_items      = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $edminboost_wizard_preset );
$edminboost_preset_picker_mode = 'wizard';
?>
<form action="options.php" method="post" class="edminboost-cc-form edminboost-setup-wizard" id="edminboost-setup-wizard-form">
	<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
	<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[enabled]" value="1" />
	<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_setup_wizard_save]" value="1" />
	<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_apply_preset]" id="edminboost_wizard_apply_preset" value="<?php echo esc_attr( $edminboost_wizard_preset ); ?>" />
	<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][default_preset]" id="edminboost_wizard_default_preset" value="<?php echo esc_attr( $edminboost_wizard_preset ); ?>" />

	<nav class="edminboost-setup-stepper" aria-label="<?php esc_attr_e( 'Setup progress', EDMINBOOST_TEXT_DOMAIN ); ?>">
		<ol class="edminboost-setup-stepper__list">
			<li class="edminboost-setup-stepper__item is-active" data-step="1">
				<span class="edminboost-setup-stepper__number" aria-hidden="true">1</span>
				<span class="edminboost-setup-stepper__label"><?php esc_html_e( 'Layout', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			</li>
			<li class="edminboost-setup-stepper__item" data-step="2">
				<span class="edminboost-setup-stepper__number" aria-hidden="true">2</span>
				<span class="edminboost-setup-stepper__label"><?php esc_html_e( 'Color theme', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			</li>
			<li class="edminboost-setup-stepper__item" data-step="3">
				<span class="edminboost-setup-stepper__number" aria-hidden="true">3</span>
				<span class="edminboost-setup-stepper__label"><?php esc_html_e( 'Top bar', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			</li>
			<li class="edminboost-setup-stepper__item" data-step="4">
				<span class="edminboost-setup-stepper__number" aria-hidden="true">4</span>
				<span class="edminboost-setup-stepper__label"><?php esc_html_e( 'Review', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			</li>
		</ol>
	</nav>

	<div class="edminboost-setup-step is-active" id="edminboost-setup-step-1" data-step="1" role="tabpanel" aria-labelledby="edminboost-setup-step-1-heading">
		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-setup-step-1-heading">
			<h2 id="edminboost-setup-step-1-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'layout_preset' ); ?><?php esc_html_e( 'Choose a layout preset', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Pick a scenario or role-based template for which admin links appear in your top bar.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
			<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-preset-picker.php'; ?>
		</section>
	</div>

	<div class="edminboost-setup-step" id="edminboost-setup-step-2" data-step="2" role="tabpanel" aria-labelledby="edminboost-setup-step-2-heading" hidden>
		<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-theme-settings.php'; ?>
	</div>

	<div class="edminboost-setup-step" id="edminboost-setup-step-3" data-step="3" role="tabpanel" aria-labelledby="edminboost-setup-step-3-heading" hidden>
		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-setup-step-3-heading">
			<h2 id="edminboost-setup-step-3-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'setup_wizard_topbar' ); ?><?php esc_html_e( 'Review your top bar', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'These links will appear in your Command Center top bar. Open the full editor for fine-tuning.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
			<?php
			$edminboost_top_bar_items = $edminboost_preview_items;
			$edminboost_summary_id    = 'edminboost-wizard-topbar-summary';
			include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-topbar-summary.php';
			?>
		</section>
	</div>

	<div class="edminboost-setup-step" id="edminboost-setup-step-4" data-step="4" role="tabpanel" aria-labelledby="edminboost-setup-step-4-heading" hidden>
		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-setup-step-4-heading">
			<h2 id="edminboost-setup-step-4-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'setup_wizard_review' ); ?><?php esc_html_e( 'Review and save', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Confirm your choices, then save to launch your Command Center.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
			<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-setup-review.php'; ?>
		</section>
	</div>

	<footer class="edminboost-setup-wizard__footer">
		<button type="button" class="button" id="edminboost-setup-back" hidden>
			<?php esc_html_e( 'Back', EDMINBOOST_TEXT_DOMAIN ); ?>
		</button>
		<button type="button" class="button button-primary" id="edminboost-setup-next">
			<?php esc_html_e( 'Next', EDMINBOOST_TEXT_DOMAIN ); ?>
		</button>
		<?php submit_button( __( 'Save and launch', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false, array( 'id' => 'edminboost-setup-submit', 'style' => 'display:none;' ) ); ?>
	</footer>
</form>
