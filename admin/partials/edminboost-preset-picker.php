<?php
/**
 * Layout preset dropdown picker (wizard or full library).
 *
 * @package EdminBoost
 *
 * @var string $edminboost_preset_picker_mode Picker mode: `wizard`, `full`, or `overview`.
 * @var string $edminboost_option_name                  Settings option name (legacy include variable).
 * @var array  $cc_settings                  Command Center settings (legacy include variable).
 * @var string $preset_picker_mode           Legacy include variable from parent partials.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $edminboost_preset_picker_mode ) ) {
	$edminboost_preset_picker_mode = isset( $preset_picker_mode ) ? $preset_picker_mode : 'full';
}

$edminboost_include_virtual    = 'wizard' !== $edminboost_preset_picker_mode;
$edminboost_all_presets        = EDMINBOOST_Command_Center::get_picker_presets( $edminboost_include_virtual );
$edminboost_preset_categories  = EDMINBOOST_Command_Center::get_preset_categories();
$edminboost_grouped_presets    = array();
$edminboost_default_preset     = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';

if ( 'wizard' === $edminboost_preset_picker_mode ) {
	$edminboost_wizard_preset = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';
	if ( isset( $edminboost_all_presets[ $edminboost_wizard_preset ] ) && ! empty( $edminboost_all_presets[ $edminboost_wizard_preset ]['system'] ) ) {
		$edminboost_selected_preset = $edminboost_wizard_preset;
	} else {
		$edminboost_selected_preset = 'system_client';
	}
} else {
	$edminboost_selected_preset = EDMINBOOST_Command_Center::detect_active_layout_preset( $cc_settings );
	if ( ! isset( $edminboost_all_presets[ $edminboost_selected_preset ] ) ) {
		$edminboost_selected_preset = isset( $edminboost_all_presets[ $edminboost_default_preset ] ) ? $edminboost_default_preset : 'default';
	}
}

foreach ( $edminboost_all_presets as $edminboost_preset_id => $edminboost_preset ) {
	if ( ! empty( $edminboost_preset['virtual'] ) ) {
		if ( ! isset( $edminboost_grouped_presets['source'] ) ) {
			$edminboost_grouped_presets['source'] = array();
		}
		$edminboost_grouped_presets['source'][ $edminboost_preset_id ] = $edminboost_preset;
		continue;
	}

	if ( empty( $edminboost_preset['system'] ) ) {
		if ( 'wizard' === $edminboost_preset_picker_mode ) {
			continue;
		}
		if ( ! isset( $edminboost_grouped_presets['saved'] ) ) {
			$edminboost_grouped_presets['saved'] = array();
		}
		$edminboost_grouped_presets['saved'][ $edminboost_preset_id ] = $edminboost_preset;
		continue;
	}

	$edminboost_category = isset( $edminboost_preset['category'] ) ? $edminboost_preset['category'] : 'workflow';
	if ( ! isset( $edminboost_grouped_presets[ $edminboost_category ] ) ) {
		$edminboost_grouped_presets[ $edminboost_category ] = array();
	}
	$edminboost_grouped_presets[ $edminboost_category ][ $edminboost_preset_id ] = $edminboost_preset;
}

$edminboost_preset_display_order = 'wizard' === $edminboost_preset_picker_mode
	? array( 'scenario', 'workflow' )
	: array( 'source', 'scenario', 'workflow', 'saved' );

$edminboost_show_preset_preview = 'overview' !== $edminboost_preset_picker_mode;

$edminboost_selected_config = isset( $edminboost_all_presets[ $edminboost_selected_preset ] ) ? $edminboost_all_presets[ $edminboost_selected_preset ] : array();
$edminboost_selected_name     = isset( $edminboost_selected_config['name'] ) ? $edminboost_selected_config['name'] : $edminboost_selected_preset;
$edminboost_selected_desc     = isset( $edminboost_selected_config['description'] ) ? $edminboost_selected_config['description'] : '';
$edminboost_selected_system   = ! empty( $edminboost_selected_config['system'] );
$edminboost_selected_virtual  = ! empty( $edminboost_selected_config['virtual'] );
$edminboost_preview_items     = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $edminboost_selected_preset, $cc_settings );
$edminboost_preview_id        = 'wizard' === $edminboost_preset_picker_mode
	? 'edminboost-wizard-layout-preset-preview'
	: 'edminboost-layout-preset-preview';
$edminboost_preview_aria      = sprintf(
	/* translators: %s: layout preset name */
	__( 'Top bar preview for the %s layout preset', 'edminboost-admin-customization' ),
	$edminboost_selected_name
);
?>
<div class="edminboost-preset-picker edminboost-preset-picker--<?php echo esc_attr( $edminboost_preset_picker_mode ); ?>">
	<fieldset class="edminboost-fieldset edminboost-layout-preset-fieldset">
		<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'layout_preset' ); ?><?php esc_html_e( 'Layout preset', 'edminboost-admin-customization' ); ?></legend>

		<select
			id="edminboost_layout_preset"
			class="screen-reader-text"
			tabindex="-1"
			aria-hidden="true"
		>
			<?php foreach ( $edminboost_preset_display_order as $edminboost_category_id ) : ?>
				<?php
				if ( empty( $edminboost_grouped_presets[ $edminboost_category_id ] ) ) {
					continue;
				}
				$edminboost_category_label = isset( $edminboost_preset_categories[ $edminboost_category_id ] ) ? $edminboost_preset_categories[ $edminboost_category_id ] : $edminboost_category_id;
				?>
				<optgroup label="<?php echo esc_attr( $edminboost_category_label ); ?>">
					<?php foreach ( $edminboost_grouped_presets[ $edminboost_category_id ] as $edminboost_preset_id => $edminboost_preset ) : ?>
						<option
							value="<?php echo esc_attr( $edminboost_preset_id ); ?>"
							data-system="<?php echo ! empty( $edminboost_preset['system'] ) ? '1' : '0'; ?>"
							<?php selected( $edminboost_selected_preset, $edminboost_preset_id ); ?>
						>
							<?php echo esc_html( isset( $edminboost_preset['name'] ) ? $edminboost_preset['name'] : $edminboost_preset_id ); ?>
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
						<?php echo esc_html( $edminboost_selected_name ); ?>
					</span>
					<span class="edminboost-layout-preset-picker__badge" id="edminboost-layout-preset-badge">
						<?php
						if ( $edminboost_selected_virtual ) {
							esc_html_e( 'Layout', 'edminboost-admin-customization' );
						} elseif ( $edminboost_selected_system ) {
							esc_html_e( 'Built-in', 'edminboost-admin-customization' );
						} else {
							esc_html_e( 'Saved', 'edminboost-admin-customization' );
						}
						?>
					</span>
				</span>
				<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
			</button>

			<ul
				class="edminboost-layout-preset-picker__list"
				id="edminboost-layout-preset-list"
				role="listbox"
				aria-label="<?php esc_attr_e( 'Layout preset', 'edminboost-admin-customization' ); ?>"
				hidden
			>
				<?php foreach ( $edminboost_preset_display_order as $edminboost_category_id ) : ?>
					<?php
					if ( empty( $edminboost_grouped_presets[ $edminboost_category_id ] ) ) {
						continue;
					}
					$edminboost_category_label = isset( $edminboost_preset_categories[ $edminboost_category_id ] ) ? $edminboost_preset_categories[ $edminboost_category_id ] : $edminboost_category_id;
					?>
					<li class="edminboost-layout-preset-picker__group" role="presentation">
						<span class="edminboost-layout-preset-picker__group-label" id="edminboost-layout-preset-group-<?php echo esc_attr( sanitize_html_class( $edminboost_category_id ) ); ?>">
							<?php echo esc_html( $edminboost_category_label ); ?>
						</span>
						<ul class="edminboost-layout-preset-picker__group-list" role="group" aria-labelledby="edminboost-layout-preset-group-<?php echo esc_attr( sanitize_html_class( $edminboost_category_id ) ); ?>">
							<?php foreach ( $edminboost_grouped_presets[ $edminboost_category_id ] as $edminboost_preset_id => $edminboost_preset ) : ?>
								<?php
								if ( ! EDMINBOOST_Pro::include_preset_in_ui( $edminboost_preset_id, 'layout' ) ) {
									continue;
								}
								$edminboost_is_system   = ! empty( $edminboost_preset['system'] );
								$edminboost_is_virtual  = ! empty( $edminboost_preset['virtual'] );
								$edminboost_is_selected = ( $edminboost_selected_preset === $edminboost_preset_id );
								$edminboost_preset_name = isset( $edminboost_preset['name'] ) ? $edminboost_preset['name'] : $edminboost_preset_id;
								$edminboost_preset_desc = isset( $edminboost_preset['description'] ) ? $edminboost_preset['description'] : '';
								if ( $edminboost_is_virtual ) {
									$edminboost_badge_label = __( 'Layout', 'edminboost-admin-customization' );
								} elseif ( $edminboost_is_system ) {
									$edminboost_badge_label = __( 'Built-in', 'edminboost-admin-customization' );
								} else {
									$edminboost_badge_label = __( 'Saved', 'edminboost-admin-customization' );
								}
								$edminboost_option_classes = 'edminboost-layout-preset-picker__option';
								if ( $edminboost_is_selected ) {
									$edminboost_option_classes .= ' is-selected';
								}
								?>
								<li
									class="<?php echo esc_attr( $edminboost_option_classes ); ?>"
									role="option"
									tabindex="-1"
									data-value="<?php echo esc_attr( $edminboost_preset_id ); ?>"
									data-system="<?php echo $edminboost_is_system ? '1' : '0'; ?>"
									aria-selected="<?php echo $edminboost_is_selected ? 'true' : 'false'; ?>"
								>
									<span class="edminboost-layout-preset-picker__option-main">
										<span class="edminboost-layout-preset-picker__option-name"><?php echo esc_html( $edminboost_preset_name ); ?></span>
										<span class="edminboost-layout-preset-picker__option-badge"><?php echo esc_html( $edminboost_badge_label ); ?></span>
									</span>
									<?php if ( '' !== $edminboost_preset_desc ) : ?>
										<span class="edminboost-layout-preset-picker__option-desc"><?php echo esc_html( $edminboost_preset_desc ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<p class="description" id="edminboost-layout-preset-desc"><?php echo esc_html( $edminboost_selected_desc ); ?></p>
	</fieldset>

	<?php if ( $edminboost_show_preset_preview ) : ?>
		<div class="edminboost-layout-preset-previews">
			<div class="edminboost-layout-preset-preview-block">
				<p class="edminboost-layout-preset-preview-label edminboost-setting-inline">
					<?php EDMINBOOST_Setting_Help::echo_icon( 'layout_sidebar_preview' ); ?>
					<?php esc_html_e( 'Sidebar preview', 'edminboost-admin-customization' ); ?>
				</p>
			<?php
			$edminboost_sidebar_items      = EDMINBOOST_Command_Center::resolve_preset_sidebar_preview_items( $edminboost_selected_preset, $cc_settings );
			$edminboost_preview_limit      = 5;
			$edminboost_preview_id         = 'wizard' === $edminboost_preset_picker_mode
				? 'edminboost-wizard-layout-sidebar-preview'
				: 'edminboost-layout-sidebar-preview';
			$edminboost_preview_aria_label = sprintf(
				/* translators: %s: layout preset name */
				__( 'Sidebar menu preview for the %s layout preset', 'edminboost-admin-customization' ),
				$edminboost_selected_name
			);

			include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-sidebar-preview.php';
			?>
			</div>
			<div class="edminboost-layout-preset-preview-block">
				<p class="edminboost-layout-preset-preview-label edminboost-setting-inline">
					<?php EDMINBOOST_Setting_Help::echo_icon( 'layout_topbar_preview' ); ?>
					<?php esc_html_e( 'Top bar preview', 'edminboost-admin-customization' ); ?>
				</p>
			<?php
			$edminboost_preview_id         = 'wizard' === $edminboost_preset_picker_mode
				? 'edminboost-wizard-layout-preset-preview'
				: 'edminboost-layout-preset-preview';
			$edminboost_preview_aria_label = $edminboost_preview_aria;
			$edminboost_show_interaction   = false;

			include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-topbar-preview.php';
			?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( 'full' === $edminboost_preset_picker_mode ) : ?>
		<input
			type="hidden"
			name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][default_preset]"
			id="edminboost_layout_default_preset"
			value="<?php echo esc_attr( $edminboost_default_preset ); ?>"
		/>
		<div class="edminboost-layout-preset-actions">
			<button type="button" class="button button-primary edminboost-preset-apply" id="edminboost-preset-apply-btn">
				<?php esc_html_e( 'Apply preset', 'edminboost-admin-customization' ); ?>
			</button>
			<label class="edminboost-checkbox-row">
				<input
					type="checkbox"
					id="edminboost_layout_preset_default_checkbox"
					<?php checked( $edminboost_default_preset, $edminboost_selected_preset ); ?>
				/>
				<?php esc_html_e( 'Set as site default', 'edminboost-admin-customization' ); ?>
			</label>
			<button type="button" class="button button-small edminboost-preset-export" id="edminboost-preset-export-btn">
				<?php esc_html_e( 'Export JSON', 'edminboost-admin-customization' ); ?>
			</button>
			<div class="edminboost-rename-preset" id="edminboost-rename-preset" hidden>
				<label class="edminboost-save-preset__label" for="edminboost_rename_preset_name_input">
					<?php esc_html_e( 'Preset name', 'edminboost-admin-customization' ); ?>
				</label>
				<input
					type="text"
					class="regular-text edminboost-save-preset__input"
					id="edminboost_rename_preset_name_input"
					placeholder="<?php echo esc_attr__( 'Enter a name for your preset', 'edminboost-admin-customization' ); ?>"
					autocomplete="off"
				/>
				<button type="button" class="button button-primary" id="edminboost-rename-preset-confirm-btn">
					<?php esc_html_e( 'Rename preset', 'edminboost-admin-customization' ); ?>
				</button>
				<button type="button" class="button" id="edminboost-rename-preset-cancel-btn">
					<?php esc_html_e( 'Cancel', 'edminboost-admin-customization' ); ?>
				</button>
			</div>
			<button type="button" class="button button-small edminboost-preset-rename" id="edminboost-preset-rename-btn" hidden>
				<?php esc_html_e( 'Rename preset', 'edminboost-admin-customization' ); ?>
			</button>
		</div>
	<?php endif; ?>
</div>
