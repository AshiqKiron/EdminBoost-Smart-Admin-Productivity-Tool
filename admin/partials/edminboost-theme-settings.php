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
$preset_colors  = isset( $theme_presets[ $active_preset ]['colors'] ) ? $theme_presets[ $active_preset ]['colors'] : $theme_presets['default']['colors'];
$custom_colors  = isset( $theme_presets['custom']['colors'] ) ? $theme_presets['custom']['colors'] : array();
?>
<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-theme-heading">
	<h2 id="edminboost-theme-heading"><?php esc_html_e( 'Visual theme', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Colors and fonts for the admin top bar, sidebar menu, Command Center bar, slide-out drawer, and EdminBoost admin screens. Does not change behavior or top bar layout.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</p>

	<fieldset class="edminboost-fieldset">
		<legend><?php esc_html_e( 'Theme preset', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
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
					$option_colors = isset( $preset['colors'] ) ? $preset['colors'] : array();
					$is_selected   = ( $active_preset === $preset_id );
					?>
					<li
						class="edminboost-theme-preset-picker__option<?php echo $is_selected ? ' is-selected' : ''; ?>"
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
		<legend for="edminboost_theme_mode"><?php esc_html_e( 'Color mode', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
		<select name="<?php echo esc_attr( $theme_key ); ?>[mode]" id="edminboost_theme_mode">
			<?php foreach ( $theme_modes as $mode_id => $mode_label ) : ?>
				<option value="<?php echo esc_attr( $mode_id ); ?>" <?php selected( $theme['mode'], $mode_id ); ?>>
					<?php echo esc_html( $mode_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</fieldset>

	<fieldset class="edminboost-fieldset">
		<legend for="edminboost_theme_font"><?php esc_html_e( 'Font', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
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
		<p class="description"><?php esc_html_e( 'Custom colors', EDMINBOOST_TEXT_DOMAIN ); ?></p>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_accent"><?php echo esc_html( $color_labels['accent'] ); ?></label>
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
			<label for="edminboost_custom_surface"><?php echo esc_html( $color_labels['surface'] ); ?></label>
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
			<label for="edminboost_custom_text"><?php echo esc_html( $color_labels['text'] ); ?></label>
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
			<label for="edminboost_custom_top"><?php echo esc_html( $color_labels['topbar'] ); ?></label>
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
			<label for="edminboost_custom_sidebar"><?php echo esc_html( $color_labels['sidebar'] ); ?></label>
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
			<label for="edminboost_custom_content"><?php echo esc_html( $color_labels['content'] ); ?></label>
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
