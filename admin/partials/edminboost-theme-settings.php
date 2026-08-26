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

$theme_presets = EDMINBOOST_Theme::get_presets();
$theme_modes   = EDMINBOOST_Theme::get_modes();
$theme_fonts   = EDMINBOOST_Theme::get_fonts();
$active_preset = isset( $theme['preset'] ) ? $theme['preset'] : 'default';
?>
<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-theme-heading">
	<h2 id="edminboost-theme-heading"><?php esc_html_e( 'Visual theme', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Colors and fonts for the Command Center bar, slide-out drawer, and EdminBoost admin screens. Does not change behavior or top bar layout.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</p>

	<div class="edminboost-theme-grid" role="radiogroup" aria-label="<?php esc_attr_e( 'Theme preset', EDMINBOOST_TEXT_DOMAIN ); ?>">
		<?php foreach ( $theme_presets as $preset_id => $preset ) : ?>
			<?php
			$is_selected = ( $active_preset === $preset_id );
			$input_id    = 'edminboost_theme_' . $preset_id;
			?>
			<label class="edminboost-theme-card<?php echo $is_selected ? ' is-selected' : ''; ?>" for="<?php echo esc_attr( $input_id ); ?>">
				<input
					type="radio"
					class="screen-reader-text edminboost-theme-card__input"
					name="<?php echo esc_attr( $theme_key ); ?>[preset]"
					id="<?php echo esc_attr( $input_id ); ?>"
					value="<?php echo esc_attr( $preset_id ); ?>"
					<?php checked( $is_selected ); ?>
				/>
				<span class="edminboost-theme-card__swatches" aria-hidden="true">
					<?php foreach ( $preset['swatch'] as $color ) : ?>
						<span class="edminboost-theme-card__swatch" style="background-color: <?php echo esc_attr( $color ); ?>;"></span>
					<?php endforeach; ?>
				</span>
				<span class="edminboost-theme-card__title"><?php echo esc_html( $preset['name'] ); ?></span>
				<span class="edminboost-theme-card__desc"><?php echo esc_html( $preset['description'] ); ?></span>
			</label>
		<?php endforeach; ?>
	</div>

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

	<label class="edminboost-checkbox-row" for="edminboost_use_custom_colors">
		<input
			type="checkbox"
			id="edminboost_use_custom_colors"
			name="<?php echo esc_attr( $theme_key ); ?>[use_custom_colors]"
			value="1"
			<?php checked( ! empty( $theme['use_custom_colors'] ) ); ?>
		/>
		<?php esc_html_e( 'Override preset with custom colors', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>

	<div
		class="edminboost-theme-custom-colors"
		id="edminboost-theme-custom-colors"
		<?php echo empty( $theme['use_custom_colors'] ) ? 'hidden' : ''; ?>
	>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_accent"><?php esc_html_e( 'Accent', EDMINBOOST_TEXT_DOMAIN ); ?></label>
			<input type="color" id="edminboost_custom_accent_picker" value="<?php echo esc_attr( $theme['custom_accent'] ? $theme['custom_accent'] : '#2271b1' ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_accent"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_accent]"
				value="<?php echo esc_attr( $theme['custom_accent'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="#2271b1"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_surface"><?php esc_html_e( 'Surface', EDMINBOOST_TEXT_DOMAIN ); ?></label>
			<input type="color" id="edminboost_custom_surface_picker" value="<?php echo esc_attr( $theme['custom_surface'] ? $theme['custom_surface'] : '#ffffff' ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_surface"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_surface]"
				value="<?php echo esc_attr( $theme['custom_surface'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="#ffffff"
			/>
		</div>
		<div class="edminboost-theme-color-row">
			<label for="edminboost_custom_text"><?php esc_html_e( 'Text', EDMINBOOST_TEXT_DOMAIN ); ?></label>
			<input type="color" id="edminboost_custom_text_picker" value="<?php echo esc_attr( $theme['custom_text'] ? $theme['custom_text'] : '#1d2327' ); ?>" />
			<input
				type="text"
				class="small-text"
				id="edminboost_custom_text"
				name="<?php echo esc_attr( $theme_key ); ?>[custom_text]"
				value="<?php echo esc_attr( $theme['custom_text'] ); ?>"
				pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
				placeholder="#1d2327"
			/>
		</div>
	</div>

	<div class="edminboost-theme-preview" id="edminboost-theme-preview" aria-live="polite">
		<div class="edminboost-theme-preview__bar">
			<span class="dashicons dashicons-admin-generic" aria-hidden="true"></span>
			<span><?php esc_html_e( 'Command Center', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			<span class="edminboost-theme-preview__badge">3</span>
		</div>
		<p class="edminboost-theme-preview__accent"><?php esc_html_e( 'Accent link example', EDMINBOOST_TEXT_DOMAIN ); ?></p>
		<p class="edminboost-theme-preview__muted"><?php esc_html_e( 'Muted description text on a themed card surface.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	</div>
</section>
