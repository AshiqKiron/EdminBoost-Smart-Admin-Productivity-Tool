<?php
/**
 * Layout preset card grid (wizard or full library).
 *
 * @package EdminBoost
 *
 * @var string $option_name       Settings option name.
 * @var array  $cc_settings       Command Center settings.
 * @var string $preset_picker_mode `wizard` or `full`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preset_picker_mode = isset( $preset_picker_mode ) ? $preset_picker_mode : 'full';
$all_presets        = EDMINBOOST_Command_Center::get_all_presets();
$preset_categories  = EDMINBOOST_Command_Center::get_preset_categories();
$grouped_presets    = array();
$default_preset     = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';
$selected_preset    = $default_preset;

if ( 'wizard' === $preset_picker_mode ) {
	$wizard_preset = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';
	if ( isset( $all_presets[ $wizard_preset ] ) && ! empty( $all_presets[ $wizard_preset ]['system'] ) ) {
		$selected_preset = $wizard_preset;
	} else {
		$selected_preset = 'system_client';
	}
}

foreach ( $all_presets as $preset_id => $preset ) {
	if ( empty( $preset['system'] ) ) {
		if ( 'wizard' === $preset_picker_mode ) {
			continue;
		}
		if ( ! isset( $grouped_presets['saved'] ) ) {
			$grouped_presets['saved'] = array();
		}
		$grouped_presets['saved'][ $preset_id ] = $preset;
		continue;
	}

	$category = isset( $preset['category'] ) ? $preset['category'] : 'workflow';
	if ( ! isset( $grouped_presets[ $category ] ) ) {
		$grouped_presets[ $category ] = array();
	}
	$grouped_presets[ $category ][ $preset_id ] = $preset;
}

$preset_display_order = 'wizard' === $preset_picker_mode
	? array( 'scenario', 'workflow' )
	: array( 'scenario', 'workflow', 'saved' );
?>
<div class="edminboost-preset-picker edminboost-preset-picker--<?php echo esc_attr( $preset_picker_mode ); ?>">
	<?php foreach ( $preset_display_order as $category_id ) : ?>
		<?php
		if ( empty( $grouped_presets[ $category_id ] ) ) {
			continue;
		}
		$category_label = isset( $preset_categories[ $category_id ] ) ? $preset_categories[ $category_id ] : $category_id;
		$heading_id     = 'edminboost-presets-' . sanitize_html_class( $category_id );
		?>
		<div class="edminboost-preset-group" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
			<h3 class="edminboost-preset-group__title" id="<?php echo esc_attr( $heading_id ); ?>">
				<?php echo esc_html( $category_label ); ?>
			</h3>
			<div class="edminboost-preset-grid" role="<?php echo 'wizard' === $preset_picker_mode ? 'radiogroup' : 'presentation'; ?>"<?php echo 'wizard' === $preset_picker_mode ? ' aria-label="' . esc_attr__( 'Layout preset', EDMINBOOST_TEXT_DOMAIN ) . '"' : ''; ?>>
				<?php foreach ( $grouped_presets[ $category_id ] as $preset_id => $preset ) : ?>
					<?php
					$is_system   = ! empty( $preset['system'] );
					$is_default  = ( $default_preset === $preset_id );
					$is_selected = 'wizard' === $preset_picker_mode && $selected_preset === $preset_id;
					$preset_name = isset( $preset['name'] ) ? $preset['name'] : $preset_id;
					$preset_desc = isset( $preset['description'] ) ? $preset['description'] : '';
					$input_id    = 'edminboost_layout_preset_' . sanitize_html_class( $preset_id );
					?>
					<article class="edminboost-preset-card<?php echo $is_default ? ' is-default' : ''; ?><?php echo $is_selected ? ' is-selected' : ''; ?>" data-preset-id="<?php echo esc_attr( $preset_id ); ?>">
						<header class="edminboost-preset-card__header">
							<h4 class="edminboost-preset-card__title"><?php echo esc_html( $preset_name ); ?></h4>
							<?php if ( $is_system ) : ?>
								<span class="edminboost-preset-card__badge"><?php esc_html_e( 'Built-in', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							<?php else : ?>
								<span class="edminboost-preset-card__badge edminboost-preset-card__badge--custom"><?php esc_html_e( 'Saved', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							<?php endif; ?>
							<?php if ( $is_default && 'full' === $preset_picker_mode ) : ?>
								<span class="edminboost-preset-card__default"><?php esc_html_e( 'Default', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							<?php endif; ?>
						</header>
						<p class="edminboost-preset-card__desc"><?php echo esc_html( $preset_desc ); ?></p>
						<div class="edminboost-preset-card__actions">
							<?php if ( 'wizard' === $preset_picker_mode ) : ?>
								<label class="edminboost-preset-card__select" for="<?php echo esc_attr( $input_id ); ?>">
									<input
										type="radio"
										class="edminboost-wizard-preset-radio screen-reader-text"
										name="edminboost_wizard_layout_preset"
										id="<?php echo esc_attr( $input_id ); ?>"
										value="<?php echo esc_attr( $preset_id ); ?>"
										<?php checked( $is_selected ); ?>
									/>
									<span class="button button-secondary"><?php esc_html_e( 'Select', EDMINBOOST_TEXT_DOMAIN ); ?></span>
								</label>
							<?php else : ?>
								<button type="button" class="button button-primary edminboost-preset-apply" data-preset-id="<?php echo esc_attr( $preset_id ); ?>">
									<?php esc_html_e( 'Apply preset', EDMINBOOST_TEXT_DOMAIN ); ?>
								</button>
								<label class="screen-reader-text" for="edminboost_preset_default_<?php echo esc_attr( $preset_id ); ?>">
									<?php esc_html_e( 'Set as default preset', EDMINBOOST_TEXT_DOMAIN ); ?>
								</label>
								<label class="edminboost-checkbox-row">
									<input
										type="radio"
										name="<?php echo esc_attr( $option_name ); ?>[command_center][default_preset]"
										id="edminboost_preset_default_<?php echo esc_attr( $preset_id ); ?>"
										value="<?php echo esc_attr( $preset_id ); ?>"
										<?php checked( $is_default ); ?>
									/>
									<?php esc_html_e( 'Set as default', EDMINBOOST_TEXT_DOMAIN ); ?>
								</label>
								<?php if ( ! $is_system ) : ?>
									<button type="button" class="button button-small edminboost-preset-duplicate" data-preset-id="<?php echo esc_attr( $preset_id ); ?>">
										<?php esc_html_e( 'Duplicate', EDMINBOOST_TEXT_DOMAIN ); ?>
									</button>
								<?php endif; ?>
								<button type="button" class="button button-small edminboost-preset-export" data-preset-id="<?php echo esc_attr( $preset_id ); ?>">
									<?php esc_html_e( 'Export JSON', EDMINBOOST_TEXT_DOMAIN ); ?>
								</button>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
