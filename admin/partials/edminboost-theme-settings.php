<?php
/**
 * Visual theme settings (presets, mode, font, custom colors).
 *
 * @package EdminBoost
 *
 * @var string $option_name Settings option name.
 * @var array  $theme       Current theme settings.
 * @var string $theme_key   Form field prefix for theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme_presets  = EDMINBOOST_Theme::get_presets();
$theme_modes    = EDMINBOOST_Theme::get_modes();
$theme_fonts    = EDMINBOOST_Theme::get_fonts();
$color_labels   = EDMINBOOST_Theme::get_color_labels();
$active_preset  = isset( $theme['preset'] ) ? $theme['preset'] : 'default';
$is_custom      = EDMINBOOST_Theme::uses_custom_colors( $theme );
$theme_mode     = isset( $theme['mode'] ) ? $theme['mode'] : 'light';
$preset_colors  = EDMINBOOST_Theme::resolve_preview_colors(
	$active_preset,
	$theme_mode,
	$theme
);
$custom_colors  = isset( $theme_presets['custom']['colors'] ) ? $theme_presets['custom']['colors'] : array();
?>
<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-theme-heading">
	<h2 id="edminboost-theme-heading"><?php esc_html_e( 'Visual theme', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Colors and fonts for the admin top bar, sidebar menu, Command Center bar, slide-out drawer, and EdminBoost admin screens. Does not change behavior or top bar layout.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</p>

	<fieldset class="edminboost-fieldset">
		<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_preset' ); ?><?php esc_html_e( 'Theme preset', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
		<select
			name="<?php echo esc_attr( $theme_key ); ?>[preset]"
			id="edminboost_theme_preset"
			class="screen-reader-text"
			tabindex="-1"
			aria-hidden="true"
		>
			<?php foreach ( $theme_presets as $preset_id => $preset ) : ?>
				<option value="<?php echo esc_attr( $preset_id ); ?>" <?php selected( $active_preset, $preset_id ); ?>>
					<?php echo esc_html( $preset['name'] ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<div class="edminboost-theme-preset-picker" id="edminboost-theme-preset-picker">
			<button
				type="button"
				class="edminboost-theme-preset-picker__toggle"
				id="edminboost_theme_preset_toggle"
				aria-expanded="false"
				aria-controls="edminboost-theme-preset-list"
				aria-haspopup="listbox"
			>
				<span class="edminboost-theme-preset-picker__label">
					<span class="edminboost-theme-preset-picker__name" id="edminboost-theme-preset-name">
						<?php echo esc_html( $theme_presets[ $active_preset ]['name'] ); ?>
					</span>
					<span class="edminboost-theme-preset-picker__swatches" id="edminboost-theme-preset-toggle-swatches" aria-hidden="true">
						<?php foreach ( $color_labels as $color_key => $color_label ) : ?>
							<?php
							$chip_color = isset( $preset_colors[ $color_key ] ) ? $preset_colors[ $color_key ] : '#ffffff';
							?>
							<span
								class="edminboost-theme-preset-picker__chip"
								style="background-color: <?php echo esc_attr( $chip_color ); ?>;"
								title="<?php echo esc_attr( $color_label ); ?>"
							></span>
						<?php endforeach; ?>
					</span>
				</span>
				<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
			</button>

			<ul
				class="edminboost-theme-preset-picker__list"
				id="edminboost-theme-preset-list"
				role="listbox"
				aria-label="<?php esc_attr_e( 'Theme preset', EDMINBOOST_TEXT_DOMAIN ); ?>"
				hidden
			>
				<?php foreach ( $theme_presets as $preset_id => $preset ) : ?>
					<?php
					$option_colors = EDMINBOOST_Theme::resolve_preview_colors(
						$preset_id,
						$theme_mode,
						'custom' === $preset_id ? $theme : null
					);
					$is_selected   = ( $active_preset === $preset_id );
					$option_class  = 'edminboost-theme-preset-picker__option';
					if ( $is_selected ) {
						$option_class .= ' is-selected';
					}
					?>
					<li
						class="<?php echo esc_attr( $option_class ); ?>"
						role="option"
						tabindex="-1"
						data-value="<?php echo esc_attr( $preset_id ); ?>"
						aria-selected="<?php echo $is_selected ? 'true' : 'false'; ?>"
					>
						<span class="edminboost-theme-preset-picker__option-main">
							<span class="edminboost-theme-preset-picker__option-name"><?php echo esc_html( $preset['name'] ); ?></span>
							<span class="edminboost-theme-preset-picker__swatches" aria-hidden="true">
								<?php foreach ( $color_labels as $color_key => $color_label ) : ?>
									<?php
									$chip_color = isset( $option_colors[ $color_key ] ) ? $option_colors[ $color_key ] : '#ffffff';
									?>
									<span
										class="edminboost-theme-preset-picker__chip"
										style="background-color: <?php echo esc_attr( $chip_color ); ?>;"
										title="<?php echo esc_attr( $color_label ); ?>"
									></span>
								<?php endforeach; ?>
							</span>
						</span>
						<span class="edminboost-theme-preset-picker__option-desc"><?php echo esc_html( $preset['description'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<p class="description" id="edminboost-theme-preset-desc">
			<?php
			echo esc_html(
				isset( $theme_presets[ $active_preset ]['description'] )
					? $theme_presets[ $active_preset ]['description']
					: ''
			);
			?>
		</p>
	</fieldset>

	<fieldset class="edminboost-fieldset">
		<legend for="edminboost_theme_mode"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_mode' ); ?><?php esc_html_e( 'Color mode', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
		<select name="<?php echo esc_attr( $theme_key ); ?>[mode]" id="edminboost_theme_mode">
			<?php foreach ( $theme_modes as $mode_id => $mode_label ) : ?>
				<option value="<?php echo esc_attr( $mode_id ); ?>" <?php selected( $theme['mode'], $mode_id ); ?>>
					<?php echo esc_html( $mode_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</fieldset>

	<fieldset class="edminboost-fieldset">
		<legend for="edminboost_theme_font"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_font' ); ?><?php esc_html_e( 'Font', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
		<select name="<?php echo esc_attr( $theme_key ); ?>[font]" id="edminboost_theme_font">
			<?php foreach ( $theme_fonts as $font_id => $font_label ) : ?>
				<option value="<?php echo esc_attr( $font_id ); ?>" <?php selected( $theme['font'], $font_id ); ?>>
					<?php echo esc_html( $font_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</fieldset>

	<div
		class="edminboost-theme-custom-colors"
		id="edminboost-theme-custom-colors"
		<?php echo $is_custom ? '' : 'hidden'; ?>
	>
		<p class="description"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_colors' ); ?><?php esc_html_e( 'Custom colors', EDMINBOOST_TEXT_DOMAIN ); ?></p>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_accent"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_accent' ); ?><?php echo esc_html( $color_labels['accent'] ); ?></label>
			<input type="color" id="edminboost_custom_accent_picker" value="<?php echo esc_attr( $theme['custom_accent'] ? $theme['custom_accent'] : $custom_colors['accent'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_accent"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_accent]"
				value="<?php echo esc_attr( $theme['custom_accent'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $custom_colors['accent'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_surface"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_surface' ); ?><?php echo esc_html( $color_labels['surface'] ); ?></label>
			<input type="color" id="edminboost_custom_surface_picker" value="<?php echo esc_attr( $theme['custom_surface'] ? $theme['custom_surface'] : $custom_colors['surface'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_surface"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_surface]"
				value="<?php echo esc_attr( $theme['custom_surface'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $custom_colors['surface'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_text"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_text' ); ?><?php echo esc_html( $color_labels['text'] ); ?></label>
			<input type="color" id="edminboost_custom_text_picker" value="<?php echo esc_attr( $theme['custom_text'] ? $theme['custom_text'] : $custom_colors['text'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_text"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_text]"
				value="<?php echo esc_attr( $theme['custom_text'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $custom_colors['text'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_top"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_topbar' ); ?><?php echo esc_html( $color_labels['topbar'] ); ?></label>
			<input type="color" id="edminboost_custom_top_picker" value="<?php echo esc_attr( $theme['custom_top'] ? $theme['custom_top'] : $custom_colors['topbar'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_top"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_top]"
				value="<?php echo esc_attr( $theme['custom_top'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $custom_colors['topbar'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_sidebar"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_sidebar' ); ?><?php echo esc_html( $color_labels['sidebar'] ); ?></label>
			<input type="color" id="edminboost_custom_sidebar_picker" value="<?php echo esc_attr( $theme['custom_sidebar'] ? $theme['custom_sidebar'] : $custom_colors['sidebar'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_sidebar"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_sidebar]"
				value="<?php echo esc_attr( $theme['custom_sidebar'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $custom_colors['sidebar'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_content"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_content' ); ?><?php echo esc_html( $color_labels['content'] ); ?></label>
			<input type="color" id="edminboost_custom_content_picker" value="<?php echo esc_attr( $theme['custom_content'] ? $theme['custom_content'] : $custom_colors['content'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_content"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_content]"
				value="<?php echo esc_attr( $theme['custom_content'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $custom_colors['content'] ); ?>"
			/>
		</div>
	</div>
</section>

<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-theme-extras-heading">
	<h2 id="edminboost-theme-extras-heading"><?php esc_html_e( 'Appearance extras', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Fine-tune admin typography, background, favicon, post list colors, and optional scheduled dark mode.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</p>

	<div class="edminboost-theme-extras-layout">
		<div class="edminboost-theme-extras-fields">
			<fieldset class="edminboost-fieldset edminboost-theme-extras-group">
				<legend><?php esc_html_e( 'Typography & background', EDMINBOOST_TEXT_DOMAIN ); ?></legend>

				<div class="edminboost-theme-extras-row">
					<label for="edminboost_font_size"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_font_size' ); ?><?php esc_html_e( 'Admin font size', EDMINBOOST_TEXT_DOMAIN ); ?></label>
					<div class="edminboost-theme-extras-control">
						<input
							type="range"
							class="edminboost-theme-extras-range"
							id="edminboost_font_size_range"
							min="12"
							max="20"
							step="1"
							value="<?php echo esc_attr( $theme['font_size'] ?? 14 ); ?>"
							aria-describedby="edminboost_font_size_value"
						/>
						<input
							type="number"
							class="small-text edminboost-theme-extras-number"
							id="edminboost_font_size"
							name="<?php echo esc_attr( $theme_key ); ?>[font_size]"
							value="<?php echo esc_attr( $theme['font_size'] ?? 14 ); ?>"
							min="12"
							max="20"
						/>
						<span class="edminboost-theme-extras-unit" id="edminboost_font_size_value">px</span>
					</div>
				</div>

				<div class="edminboost-theme-extras-row edminboost-theme-extras-color-row">
					<label for="edminboost_admin_bg_color"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_admin_bg_color' ); ?><?php esc_html_e( 'Admin background', EDMINBOOST_TEXT_DOMAIN ); ?></label>
					<div class="edminboost-theme-extras-control edminboost-theme-extras-color-controls">
						<?php
						$admin_bg_value = $theme['admin_bg_color'] ?? '';
						$admin_bg_picker = $admin_bg_value ? $admin_bg_value : $preset_colors['content'];
						?>
						<input type="color" id="edminboost_admin_bg_color_picker" value="<?php echo esc_attr( $admin_bg_picker ); ?>" />
						<input
							type="text"
							class="small-text"
							id="edminboost_admin_bg_color"
							name="<?php echo esc_attr( $theme_key ); ?>[admin_bg_color]"
							value="<?php echo esc_attr( $admin_bg_value ); ?>"
							placeholder="<?php echo esc_attr( $preset_colors['content'] ); ?>"
							pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
						/>
					</div>
				</div>

				<div class="edminboost-theme-extras-row">
					<label for="edminboost_admin_bg_image_id"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_admin_bg_image' ); ?><?php esc_html_e( 'Background image ID', EDMINBOOST_TEXT_DOMAIN ); ?></label>
					<div class="edminboost-theme-extras-control">
						<input
							type="number"
							class="small-text edminboost-theme-extras-number"
							id="edminboost_admin_bg_image_id"
							name="<?php echo esc_attr( $theme_key ); ?>[admin_bg_image_id]"
							value="<?php echo esc_attr( $theme['admin_bg_image_id'] ?? 0 ); ?>"
							min="0"
						/>
					</div>
				</div>
			</fieldset>

			<fieldset class="edminboost-fieldset edminboost-theme-extras-group">
				<legend><?php esc_html_e( 'Browser chrome', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
				<div class="edminboost-theme-extras-row">
					<label for="edminboost_admin_favicon_id"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_admin_favicon' ); ?><?php esc_html_e( 'Admin favicon ID', EDMINBOOST_TEXT_DOMAIN ); ?></label>
					<input
						type="number"
						class="small-text edminboost-theme-extras-number"
						id="edminboost_admin_favicon_id"
						name="<?php echo esc_attr( $theme_key ); ?>[admin_favicon_id]"
						value="<?php echo esc_attr( $theme['admin_favicon_id'] ?? 0 ); ?>"
						min="0"
					/>
				</div>
			</fieldset>

			<fieldset class="edminboost-fieldset edminboost-theme-extras-group">
				<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_schedule_dark_mode' ); ?><?php esc_html_e( 'Scheduled dark mode', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
				<label class="edminboost-checkbox-row" for="edminboost_schedule_dark_mode">
					<input type="checkbox" id="edminboost_schedule_dark_mode" name="<?php echo esc_attr( $theme_key ); ?>[schedule_dark_mode]" value="1" <?php checked( ! empty( $theme['schedule_dark_mode'] ) ); ?> />
					<?php esc_html_e( 'Enable scheduled dark mode window for Auto color mode.', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<div
					class="edminboost-dependent-section edminboost-theme-extras-schedule<?php echo empty( $theme['schedule_dark_mode'] ) ? ' is-disabled' : ''; ?>"
					id="edminboost-theme-schedule-options"
					aria-disabled="<?php echo empty( $theme['schedule_dark_mode'] ) ? 'true' : 'false'; ?>"
				>
					<div class="edminboost-theme-extras-schedule-grid">
						<div class="edminboost-theme-extras-row">
							<label for="edminboost_dark_mode_start"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_dark_mode_start' ); ?><?php esc_html_e( 'Dark mode start', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input
								type="time"
								class="edminboost-theme-extras-time"
								id="edminboost_dark_mode_start"
								name="<?php echo esc_attr( $theme_key ); ?>[dark_mode_start]"
								value="<?php echo esc_attr( $theme['dark_mode_start'] ?? '18:00' ); ?>"
							/>
						</div>
						<div class="edminboost-theme-extras-row">
							<label for="edminboost_dark_mode_end"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_dark_mode_end' ); ?><?php esc_html_e( 'Dark mode end', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input
								type="time"
								class="edminboost-theme-extras-time"
								id="edminboost_dark_mode_end"
								name="<?php echo esc_attr( $theme_key ); ?>[dark_mode_end]"
								value="<?php echo esc_attr( $theme['dark_mode_end'] ?? '06:00' ); ?>"
							/>
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset class="edminboost-fieldset edminboost-theme-extras-group">
				<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_status_colors' ); ?><?php esc_html_e( 'Post status row colors', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
				<p class="description"><?php esc_html_e( 'Optional hex colors for post list table rows by status. Leave blank to use the default table styling.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
				<div class="edminboost-theme-extras-status-grid">
					<?php
					$status_labels = array(
						'publish' => _x( 'Published', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'pending' => _x( 'Pending', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'future'  => _x( 'Scheduled', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'private' => _x( 'Private', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'draft'   => _x( 'Draft', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'trash'   => _x( 'Trash', 'post status', EDMINBOOST_TEXT_DOMAIN ),
					);
					foreach ( ( $theme['status_colors'] ?? array() ) as $status => $color ) :
						$status_label = isset( $status_labels[ $status ] ) ? $status_labels[ $status ] : ucfirst( $status );
						$picker_value = $color ? $color : '#ffffff';
						?>
						<div class="edminboost-theme-extras-status-row" data-status="<?php echo esc_attr( $status ); ?>">
							<label for="edminboost_status_<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status_label ); ?></label>
							<div class="edminboost-theme-extras-color-controls">
								<input type="color" id="edminboost_status_<?php echo esc_attr( $status ); ?>_picker" value="<?php echo esc_attr( $picker_value ); ?>" />
								<input
									type="text"
									class="small-text edminboost-theme-extras-status-input"
									id="edminboost_status_<?php echo esc_attr( $status ); ?>"
									name="<?php echo esc_attr( $theme_key ); ?>[status_colors][<?php echo esc_attr( $status ); ?>]"
									value="<?php echo esc_attr( $color ); ?>"
									placeholder="<?php esc_attr_e( 'Default', EDMINBOOST_TEXT_DOMAIN ); ?>"
									pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
								/>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</fieldset>
		</div>

		<?php
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-theme-extras-preview.php';
		?>
	</div>
</section>
