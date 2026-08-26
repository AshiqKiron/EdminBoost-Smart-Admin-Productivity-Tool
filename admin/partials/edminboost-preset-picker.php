<?php
/**
 * Layout preset dropdown picker (wizard or full library).
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
} elseif ( ! isset( $all_presets[ $selected_preset ] ) ) {
	$selected_preset = 'system_client';
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

$selected_config = isset( $all_presets[ $selected_preset ] ) ? $all_presets[ $selected_preset ] : array();
$selected_name   = isset( $selected_config['name'] ) ? $selected_config['name'] : $selected_preset;
$selected_desc   = isset( $selected_config['description'] ) ? $selected_config['description'] : '';
$selected_system = ! empty( $selected_config['system'] );
$preview_items   = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $selected_preset );
$preview_id      = 'wizard' === $preset_picker_mode
	? 'edminboost-wizard-layout-preset-preview'
	: 'edminboost-layout-preset-preview';
$preview_aria    = sprintf(
	/* translators: %s: layout preset name */
	__( 'Top bar preview for the %s layout preset', EDMINBOOST_TEXT_DOMAIN ),
	$selected_name
);
?>
<div class="edminboost-preset-picker edminboost-preset-picker--<?php echo esc_attr( $preset_picker_mode ); ?>">
	<fieldset class="edminboost-fieldset edminboost-layout-preset-fieldset">
		<legend><?php esc_html_e( 'Layout preset', EDMINBOOST_TEXT_DOMAIN ); ?></legend>

		<select
			id="edminboost_layout_preset"
			class="screen-reader-text"
			tabindex="-1"
			aria-hidden="true"
		>
			<?php foreach ( $preset_display_order as $category_id ) : ?>
				<?php
				if ( empty( $grouped_presets[ $category_id ] ) ) {
					continue;
				}
				$category_label = isset( $preset_categories[ $category_id ] ) ? $preset_categories[ $category_id ] : $category_id;
				?>
				<optgroup label="<?php echo esc_attr( $category_label ); ?>">
					<?php foreach ( $grouped_presets[ $category_id ] as $preset_id => $preset ) : ?>
						<option
							value="<?php echo esc_attr( $preset_id ); ?>"
							data-system="<?php echo ! empty( $preset['system'] ) ? '1' : '0'; ?>"
							<?php selected( $selected_preset, $preset_id ); ?>
						>
							<?php echo esc_html( isset( $preset['name'] ) ? $preset['name'] : $preset_id ); ?>
						</option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>
		</select>

		<div class="edminboost-layout-preset-picker" id="edminboost-layout-preset-picker">
			<button
				type="button"
				class="edminboost-layout-preset-picker__toggle"
				id="edminboost_layout_preset_toggle"
				aria-expanded="false"
				aria-controls="edminboost-layout-preset-list"
				aria-haspopup="listbox"
			>
				<span class="edminboost-layout-preset-picker__label">
					<span class="edminboost-layout-preset-picker__name" id="edminboost-layout-preset-name">
						<?php echo esc_html( $selected_name ); ?>
					</span>
					<span class="edminboost-layout-preset-picker__badge" id="edminboost-layout-preset-badge">
						<?php echo $selected_system ? esc_html__( 'Built-in', EDMINBOOST_TEXT_DOMAIN ) : esc_html__( 'Saved', EDMINBOOST_TEXT_DOMAIN ); ?>
					</span>
				</span>
				<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
			</button>

			<ul
				class="edminboost-layout-preset-picker__list"
				id="edminboost-layout-preset-list"
				role="listbox"
				aria-label="<?php esc_attr_e( 'Layout preset', EDMINBOOST_TEXT_DOMAIN ); ?>"
				hidden
			>
				<?php foreach ( $preset_display_order as $category_id ) : ?>
					<?php
					if ( empty( $grouped_presets[ $category_id ] ) ) {
						continue;
					}
					$category_label = isset( $preset_categories[ $category_id ] ) ? $preset_categories[ $category_id ] : $category_id;
					?>
					<li class="edminboost-layout-preset-picker__group" role="presentation">
						<span class="edminboost-layout-preset-picker__group-label" id="edminboost-layout-preset-group-<?php echo esc_attr( sanitize_html_class( $category_id ) ); ?>">
							<?php echo esc_html( $category_label ); ?>
						</span>
						<ul class="edminboost-layout-preset-picker__group-list" role="group" aria-labelledby="edminboost-layout-preset-group-<?php echo esc_attr( sanitize_html_class( $category_id ) ); ?>">
							<?php foreach ( $grouped_presets[ $category_id ] as $preset_id => $preset ) : ?>
								<?php
								$is_system   = ! empty( $preset['system'] );
								$is_selected = ( $selected_preset === $preset_id );
								$preset_name = isset( $preset['name'] ) ? $preset['name'] : $preset_id;
								$preset_desc = isset( $preset['description'] ) ? $preset['description'] : '';
								$badge_label = $is_system ? __( 'Built-in', EDMINBOOST_TEXT_DOMAIN ) : __( 'Saved', EDMINBOOST_TEXT_DOMAIN );
								?>
								<li
									class="edminboost-layout-preset-picker__option<?php echo $is_selected ? ' is-selected' : ''; ?>"
									role="option"
									tabindex="-1"
									data-value="<?php echo esc_attr( $preset_id ); ?>"
									data-system="<?php echo $is_system ? '1' : '0'; ?>"
									aria-selected="<?php echo $is_selected ? 'true' : 'false'; ?>"
								>
									<span class="edminboost-layout-preset-picker__option-main">
										<span class="edminboost-layout-preset-picker__option-name"><?php echo esc_html( $preset_name ); ?></span>
										<span class="edminboost-layout-preset-picker__option-badge"><?php echo esc_html( $badge_label ); ?></span>
									</span>
									<?php if ( '' !== $preset_desc ) : ?>
										<span class="edminboost-layout-preset-picker__option-desc"><?php echo esc_html( $preset_desc ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<p class="description" id="edminboost-layout-preset-desc"><?php echo esc_html( $selected_desc ); ?></p>
	</fieldset>

	<?php
	$show_interaction = false;
	include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-topbar-preview.php';
	?>

	<?php if ( 'full' === $preset_picker_mode ) : ?>
		<input
			type="hidden"
			name="<?php echo esc_attr( $option_name ); ?>[command_center][default_preset]"
			id="edminboost_layout_default_preset"
			value="<?php echo esc_attr( $default_preset ); ?>"
		/>
		<div class="edminboost-layout-preset-actions">
			<button type="button" class="button button-primary edminboost-preset-apply" id="edminboost-preset-apply-btn">
				<?php esc_html_e( 'Apply preset', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
			<label class="edminboost-checkbox-row">
				<input
					type="checkbox"
					id="edminboost_layout_preset_default_checkbox"
					<?php checked( $default_preset, $selected_preset ); ?>
				/>
				<?php esc_html_e( 'Set as site default', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>
			<button
				type="button"
				class="button button-small edminboost-preset-duplicate"
				id="edminboost-preset-duplicate-btn"
				<?php echo $selected_system ? 'disabled' : ''; ?>
			>
				<?php esc_html_e( 'Duplicate', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
			<button type="button" class="button button-small edminboost-preset-export" id="edminboost-preset-export-btn">
				<?php esc_html_e( 'Export JSON', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
		</div>
	<?php endif; ?>
</div>
