<?php
/**
 * Visual theme settings (presets, mode, font, custom colors).
 *
 * @package EdminBoost
 *
 * @var string $edminboost_option_name Settings option name.
 * @var array  $edminboost_theme       Current theme settings.
 * @var string $edminboost_theme_key   Form field prefix for theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_theme_presets  = EDMINBOOST_Theme::get_presets();
$edminboost_theme_modes    = EDMINBOOST_Theme::get_modes();
$edminboost_theme_fonts    = EDMINBOOST_Theme::get_fonts();
$edminboost_color_labels   = EDMINBOOST_Theme::get_color_labels();
$edminboost_active_preset  = isset( $edminboost_theme['preset'] ) ? $edminboost_theme['preset'] : 'default';
$edminboost_is_custom      = EDMINBOOST_Theme::uses_custom_colors( $edminboost_theme );
$edminboost_theme_mode     = isset( $edminboost_theme['mode'] ) ? $edminboost_theme['mode'] : 'light';
$edminboost_preset_colors  = EDMINBOOST_Theme::resolve_preview_colors(
	$edminboost_active_preset,
	$edminboost_theme_mode,
	$edminboost_theme
);
$edminboost_custom_colors  = isset( $edminboost_theme_presets['custom']['colors'] ) ? $edminboost_theme_presets['custom']['colors'] : array();
?>
<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-theme-heading">
	<h2 id="edminboost-theme-heading"><?php esc_html_e( 'Visual theme', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Colors and fonts for the admin top bar, sidebar menu, Command Center bar, slide-out drawer, and EdminBoost admin screens. Does not change behavior or top bar layout.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</p>

	<fieldset class="edminboost-fieldset">
		<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_preset' ); ?><?php esc_html_e( 'Theme preset', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
		<select
			name="<?php echo esc_attr( $edminboost_theme_key ); ?>[preset]"
			id="edminboost_theme_preset"
			class="screen-reader-text"
			tabindex="-1"
			aria-hidden="true"
		>
			<?php foreach ( $edminboost_theme_presets as $edminboost_preset_id => $edminboost_preset ) : ?>
				<option value="<?php echo esc_attr( $edminboost_preset_id ); ?>" <?php selected( $edminboost_active_preset, $edminboost_preset_id ); ?>>
					<?php echo esc_html( $edminboost_preset['name'] ); ?>
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
						<?php echo esc_html( $edminboost_theme_presets[ $edminboost_active_preset ]['name'] ); ?>
					</span>
					<span class="edminboost-theme-preset-picker__swatches" id="edminboost-theme-preset-toggle-swatches" aria-hidden="true">
						<?php foreach ( $edminboost_color_labels as $edminboost_color_key => $edminboost_color_label ) : ?>
							<?php
							$edminboost_chip_color = isset( $edminboost_preset_colors[ $edminboost_color_key ] ) ? $edminboost_preset_colors[ $edminboost_color_key ] : '#ffffff';
							?>
							<span
								class="edminboost-theme-preset-picker__chip"
								style="background-color: <?php echo esc_attr( $edminboost_chip_color ); ?>;"
								title="<?php echo esc_attr( $edminboost_color_label ); ?>"
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
				<?php foreach ( $edminboost_theme_presets as $edminboost_preset_id => $edminboost_preset ) : ?>
					<?php
					$edminboost_option_colors = EDMINBOOST_Theme::resolve_preview_colors(
						$edminboost_preset_id,
						$edminboost_theme_mode,
						'custom' === $edminboost_preset_id ? $edminboost_theme : null
					);
					$edminboost_is_selected   = ( $edminboost_active_preset === $edminboost_preset_id );
					$edminboost_is_pro_theme  = ! EDMINBOOST_Pro::is_theme_preset_available( $edminboost_preset_id );
					$edminboost_option_class  = 'edminboost-theme-preset-picker__option';
					if ( $edminboost_is_selected ) {
						$edminboost_option_class .= ' is-selected';
					}
					if ( $edminboost_is_pro_theme ) {
						$edminboost_option_class .= ' is-pro-locked';
					}
					?>
					<li
						class="<?php echo esc_attr( $edminboost_option_class ); ?>"
						role="option"
						tabindex="-1"
						data-value="<?php echo esc_attr( $edminboost_preset_id ); ?>"
						data-requires-pro="<?php echo $edminboost_is_pro_theme ? '1' : '0'; ?>"
						aria-selected="<?php echo $edminboost_is_selected ? 'true' : 'false'; ?>"
					>
						<span class="edminboost-theme-preset-picker__option-main">
							<span class="edminboost-theme-preset-picker__option-name"><?php echo esc_html( $edminboost_preset['name'] ); ?></span>
							<?php if ( $edminboost_is_pro_theme ) : ?>
								<?php EDMINBOOST_Pro::render_badge(); ?>
							<?php endif; ?>
							<span class="edminboost-theme-preset-picker__swatches" aria-hidden="true">
								<?php foreach ( $edminboost_color_labels as $edminboost_color_key => $edminboost_color_label ) : ?>
									<?php
									$edminboost_chip_color = isset( $edminboost_option_colors[ $edminboost_color_key ] ) ? $edminboost_option_colors[ $edminboost_color_key ] : '#ffffff';
									?>
									<span
										class="edminboost-theme-preset-picker__chip"
										style="background-color: <?php echo esc_attr( $edminboost_chip_color ); ?>;"
										title="<?php echo esc_attr( $edminboost_color_label ); ?>"
									></span>
								<?php endforeach; ?>
							</span>
						</span>
						<span class="edminboost-theme-preset-picker__option-desc"><?php echo esc_html( $edminboost_preset['description'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<p class="description" id="edminboost-theme-preset-desc">
			<?php
			echo esc_html(
				isset( $edminboost_theme_presets[ $edminboost_active_preset ]['description'] )
					? $edminboost_theme_presets[ $edminboost_active_preset ]['description']
					: ''
			);
			?>
		</p>
	</fieldset>

	<fieldset class="edminboost-fieldset">
		<legend for="edminboost_theme_mode"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_mode' ); ?><?php esc_html_e( 'Color mode', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
		<select name="<?php echo esc_attr( $edminboost_theme_key ); ?>[mode]" id="edminboost_theme_mode">
			<?php foreach ( $edminboost_theme_modes as $edminboost_mode_id => $edminboost_mode_label ) : ?>
				<option value="<?php echo esc_attr( $edminboost_mode_id ); ?>" <?php selected( $edminboost_theme['mode'], $edminboost_mode_id ); ?>>
					<?php echo esc_html( $edminboost_mode_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</fieldset>

	<fieldset class="edminboost-fieldset">
		<legend for="edminboost_theme_font"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_font' ); ?><?php esc_html_e( 'Font', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
		<select name="<?php echo esc_attr( $edminboost_theme_key ); ?>[font]" id="edminboost_theme_font">
			<?php foreach ( $edminboost_theme_fonts as $edminboost_font_id => $edminboost_font_label ) : ?>
				<option value="<?php echo esc_attr( $edminboost_font_id ); ?>" <?php selected( $edminboost_theme['font'], $edminboost_font_id ); ?>>
					<?php echo esc_html( $edminboost_font_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</fieldset>

	<div
		class="edminboost-theme-custom-colors"
		id="edminboost-theme-custom-colors"
		<?php echo $edminboost_is_custom ? '' : 'hidden'; ?>
	>
		<p class="description"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_colors' ); ?><?php esc_html_e( 'Custom colors', EDMINBOOST_TEXT_DOMAIN ); ?></p>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_accent"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_accent' ); ?><?php echo esc_html( $edminboost_color_labels['accent'] ); ?></label>
			<input type="color" id="edminboost_custom_accent_picker" value="<?php echo esc_attr( $edminboost_theme['custom_accent'] ? $edminboost_theme['custom_accent'] : $edminboost_custom_colors['accent'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_accent"
				name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_accent]"
				value="<?php echo esc_attr( $edminboost_theme['custom_accent'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $edminboost_custom_colors['accent'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_surface"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_surface' ); ?><?php echo esc_html( $edminboost_color_labels['surface'] ); ?></label>
			<input type="color" id="edminboost_custom_surface_picker" value="<?php echo esc_attr( $edminboost_theme['custom_surface'] ? $edminboost_theme['custom_surface'] : $edminboost_custom_colors['surface'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_surface"
				name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_surface]"
				value="<?php echo esc_attr( $edminboost_theme['custom_surface'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $edminboost_custom_colors['surface'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_text"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_text' ); ?><?php echo esc_html( $edminboost_color_labels['text'] ); ?></label>
			<input type="color" id="edminboost_custom_text_picker" value="<?php echo esc_attr( $edminboost_theme['custom_text'] ? $edminboost_theme['custom_text'] : $edminboost_custom_colors['text'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_text"
				name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_text]"
				value="<?php echo esc_attr( $edminboost_theme['custom_text'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $edminboost_custom_colors['text'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_top"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_topbar' ); ?><?php echo esc_html( $edminboost_color_labels['topbar'] ); ?></label>
			<input type="color" id="edminboost_custom_top_picker" value="<?php echo esc_attr( $edminboost_theme['custom_top'] ? $edminboost_theme['custom_top'] : $edminboost_custom_colors['topbar'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_top"
				name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_top]"
				value="<?php echo esc_attr( $edminboost_theme['custom_top'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $edminboost_custom_colors['topbar'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_sidebar"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_sidebar' ); ?><?php echo esc_html( $edminboost_color_labels['sidebar'] ); ?></label>
			<input type="color" id="edminboost_custom_sidebar_picker" value="<?php echo esc_attr( $edminboost_theme['custom_sidebar'] ? $edminboost_theme['custom_sidebar'] : $edminboost_custom_colors['sidebar'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_sidebar"
				name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_sidebar]"
				value="<?php echo esc_attr( $edminboost_theme['custom_sidebar'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $edminboost_custom_colors['sidebar'] ); ?>"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_content"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_custom_content' ); ?><?php echo esc_html( $edminboost_color_labels['content'] ); ?></label>
			<input type="color" id="edminboost_custom_content_picker" value="<?php echo esc_attr( $edminboost_theme['custom_content'] ? $edminboost_theme['custom_content'] : $edminboost_custom_colors['content'] ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_content"
				name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_content]"
				value="<?php echo esc_attr( $edminboost_theme['custom_content'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="<?php echo esc_attr( $edminboost_custom_colors['content'] ); ?>"
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
							value="<?php echo esc_attr( $edminboost_theme['font_size'] ?? 14 ); ?>"
							aria-describedby="edminboost_font_size_value"
						/>
						<input
							type="number"
							class="small-text edminboost-theme-extras-number"
							id="edminboost_font_size"
							name="<?php echo esc_attr( $edminboost_theme_key ); ?>[font_size]"
							value="<?php echo esc_attr( $edminboost_theme['font_size'] ?? 14 ); ?>"
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
						$edminboost_admin_bg_value = $edminboost_theme['admin_bg_color'] ?? '';
						$edminboost_admin_bg_picker = $edminboost_admin_bg_value ? $edminboost_admin_bg_value : $edminboost_preset_colors['content'];
						?>
						<input type="color" id="edminboost_admin_bg_color_picker" value="<?php echo esc_attr( $edminboost_admin_bg_picker ); ?>" />
						<input
							type="text"
							class="small-text"
							id="edminboost_admin_bg_color"
							name="<?php echo esc_attr( $edminboost_theme_key ); ?>[admin_bg_color]"
							value="<?php echo esc_attr( $edminboost_admin_bg_value ); ?>"
							placeholder="<?php echo esc_attr( $edminboost_preset_colors['content'] ); ?>"
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
							name="<?php echo esc_attr( $edminboost_theme_key ); ?>[admin_bg_image_id]"
							value="<?php echo esc_attr( $edminboost_theme['admin_bg_image_id'] ?? 0 ); ?>"
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
						name="<?php echo esc_attr( $edminboost_theme_key ); ?>[admin_favicon_id]"
						value="<?php echo esc_attr( $edminboost_theme['admin_favicon_id'] ?? 0 ); ?>"
						min="0"
					/>
				</div>
			</fieldset>

			<fieldset class="edminboost-fieldset edminboost-theme-extras-group edminboost-pro-section<?php echo EDMINBOOST_Pro::is_active() ? '' : ' is-pro-locked'; ?>"<?php echo EDMINBOOST_Pro::feature_attr( 'schedule_dark_mode' ); ?>>
				<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_schedule_dark_mode' ); ?><?php esc_html_e( 'Scheduled dark mode', EDMINBOOST_TEXT_DOMAIN ); ?> <?php EDMINBOOST_Pro::render_badge(); ?></legend>
				<label class="edminboost-checkbox-row" for="edminboost_schedule_dark_mode">
					<input type="checkbox" id="edminboost_schedule_dark_mode" name="<?php echo esc_attr( $edminboost_theme_key ); ?>[schedule_dark_mode]" value="1" <?php checked( ! empty( $edminboost_theme['schedule_dark_mode'] ) ); ?> />
					<?php esc_html_e( 'Enable scheduled dark mode window for Auto color mode.', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<div
					class="edminboost-dependent-section edminboost-theme-extras-schedule<?php echo empty( $edminboost_theme['schedule_dark_mode'] ) ? ' is-disabled' : ''; ?>"
					id="edminboost-theme-schedule-options"
					aria-disabled="<?php echo empty( $edminboost_theme['schedule_dark_mode'] ) ? 'true' : 'false'; ?>"
				>
					<div class="edminboost-theme-extras-schedule-grid">
						<div class="edminboost-theme-extras-row">
							<label for="edminboost_dark_mode_start"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_dark_mode_start' ); ?><?php esc_html_e( 'Dark mode start', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input
								type="time"
								class="edminboost-theme-extras-time"
								id="edminboost_dark_mode_start"
								name="<?php echo esc_attr( $edminboost_theme_key ); ?>[dark_mode_start]"
								value="<?php echo esc_attr( $edminboost_theme['dark_mode_start'] ?? '18:00' ); ?>"
							/>
						</div>
						<div class="edminboost-theme-extras-row">
							<label for="edminboost_dark_mode_end"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_dark_mode_end' ); ?><?php esc_html_e( 'Dark mode end', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input
								type="time"
								class="edminboost-theme-extras-time"
								id="edminboost_dark_mode_end"
								name="<?php echo esc_attr( $edminboost_theme_key ); ?>[dark_mode_end]"
								value="<?php echo esc_attr( $edminboost_theme['dark_mode_end'] ?? '06:00' ); ?>"
							/>
						</div>
					</div>
				</div>
				<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-pro-upgrade.php'; ?>
			</fieldset>

			<fieldset class="edminboost-fieldset edminboost-theme-extras-group">
				<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_status_colors' ); ?><?php esc_html_e( 'Post status row colors', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
				<p class="description"><?php esc_html_e( 'Optional hex colors for post list table rows by status. Leave blank to use the default table styling.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
				<div class="edminboost-theme-extras-status-grid">
					<?php
					$edminboost_status_labels = array(
						'publish' => _x( 'Published', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'pending' => _x( 'Pending', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'future'  => _x( 'Scheduled', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'private' => _x( 'Private', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'draft'   => _x( 'Draft', 'post status', EDMINBOOST_TEXT_DOMAIN ),
						'trash'   => _x( 'Trash', 'post status', EDMINBOOST_TEXT_DOMAIN ),
					);
					foreach ( ( $edminboost_theme['status_colors'] ?? array() ) as $status => $edminboost_color ) :
						$edminboost_status_label = isset( $edminboost_status_labels[ $status ] ) ? $edminboost_status_labels[ $status ] : ucfirst( $status );
						$edminboost_picker_value = $edminboost_color ? $edminboost_color : '#ffffff';
						?>
						<div class="edminboost-theme-extras-status-row" data-status="<?php echo esc_attr( $status ); ?>">
							<label for="edminboost_status_<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $edminboost_status_label ); ?></label>
							<div class="edminboost-theme-extras-color-controls">
								<input type="color" id="edminboost_status_<?php echo esc_attr( $status ); ?>_picker" value="<?php echo esc_attr( $edminboost_picker_value ); ?>" />
								<input
									type="text"
									class="small-text edminboost-theme-extras-status-input"
									id="edminboost_status_<?php echo esc_attr( $status ); ?>"
									name="<?php echo esc_attr( $edminboost_theme_key ); ?>[status_colors][<?php echo esc_attr( $status ); ?>]"
									value="<?php echo esc_attr( $edminboost_color ); ?>"
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
