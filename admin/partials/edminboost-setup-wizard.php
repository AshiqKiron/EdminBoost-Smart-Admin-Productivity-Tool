<?php
/**
 * First-run setup wizard (single-page stepper).
 *
 * @package EdminBoost
 *
 * @var string $option_name Settings option name.
 * @var array  $cc_settings Command Center settings.
 * @var string $theme_key   Form field prefix for theme.
 * @var array  $theme       Current theme settings.
 * @var string $mapper_url  Top Bar editor URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default_preset = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';
$all_presets    = EDMINBOOST_Command_Center::get_all_presets();
$wizard_preset  = isset( $all_presets[ $default_preset ] ) && ! empty( $all_presets[ $default_preset ]['system'] )
	? $default_preset
	: 'system_client';
$preview_items  = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $wizard_preset );
$preset_picker_mode = 'wizard';
?>
<form action="options.php" method="post" class="edminboost-cc-form edminboost-setup-wizard" id="edminboost-setup-wizard-form">
	<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
	<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />
	<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_setup_wizard_save]" value="1" />
	<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_apply_preset]" id="edminboost_wizard_apply_preset" value="<?php echo esc_attr( $wizard_preset ); ?>" />
	<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][default_preset]" id="edminboost_wizard_default_preset" value="<?php echo esc_attr( $wizard_preset ); ?>" />

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
			<h2 id="edminboost-setup-step-1-heading"><?php esc_html_e( 'Choose a layout preset', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
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
			<h2 id="edminboost-setup-step-3-heading"><?php esc_html_e( 'Review your top bar', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'These links will appear in your Command Center top bar. Open the full editor for fine-tuning.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
			<?php
			$top_bar_items = $preview_items;
			$summary_id    = 'edminboost-wizard-topbar-summary';
			include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-topbar-summary.php';
			?>
		</section>
	</div>

	<div class="edminboost-setup-step" id="edminboost-setup-step-4" data-step="4" role="tabpanel" aria-labelledby="edminboost-setup-step-4-heading" hidden>
		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-setup-step-4-heading">
			<h2 id="edminboost-setup-step-4-heading"><?php esc_html_e( 'Review and save', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
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
