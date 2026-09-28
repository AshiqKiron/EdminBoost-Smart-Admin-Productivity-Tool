<?php
/**
 * Compact theme preset picker for Dashboard overview cards.
 *
 * @package EdminBoost
 *
 * @var string $edminboost_option_name Settings option name.
 * @var string $edminboost_theme_key   Form field prefix for theme.
 * @var array  $edminboost_theme       Current theme settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_theme_presets  = EDMINBOOST_Theme::get_presets();
$edminboost_color_labels   = EDMINBOOST_Theme::get_color_labels();
$edminboost_active_preset  = isset( $edminboost_theme['preset'] ) ? $edminboost_theme['preset'] : 'default';
$edminboost_theme_mode     = isset( $edminboost_theme['mode'] ) ? $edminboost_theme['mode'] : 'light';
$edminboost_preset_colors  = EDMINBOOST_Theme::resolve_preview_colors(
	$edminboost_active_preset,
	$edminboost_theme_mode,
	$edminboost_theme
);
?>
<div class="edminboost-overview-theme-picker">
	<select
		name="<?php echo esc_attr( $edminboost_theme_key ); ?>[preset]"
		id="edminboost_theme_preset"
		class="screen-reader-text"
		tabindex="-1"
		aria-hidden="true"
	>
		<?php foreach ( $edminboost_theme_presets as $edminboost_preset_id => $edminboost_preset ) : ?>
			<?php if ( ! EDMINBOOST_Pro::include_preset_in_ui( $edminboost_preset_id, 'theme' ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>
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
			aria-label="<?php esc_attr_e( 'Color theme', 'edminboost-smart-admin-productivity-tool' ); ?>"
			hidden
		>
			<?php foreach ( $edminboost_theme_presets as $edminboost_preset_id => $edminboost_preset ) : ?>
				<?php
				if ( ! EDMINBOOST_Pro::include_preset_in_ui( $edminboost_preset_id, 'theme' ) ) {
					continue;
				}
				$edminboost_option_colors = EDMINBOOST_Theme::resolve_preview_colors(
					$edminboost_preset_id,
					$edminboost_theme_mode,
					'custom' === $edminboost_preset_id ? $edminboost_theme : null
				);
				$edminboost_is_selected   = ( $edminboost_active_preset === $edminboost_preset_id );
				?>
				<li
					class="edminboost-theme-preset-picker__option<?php echo $edminboost_is_selected ? ' is-selected' : ''; ?>"
					role="option"
					tabindex="-1"
					data-value="<?php echo esc_attr( $edminboost_preset_id ); ?>"
					aria-selected="<?php echo $edminboost_is_selected ? 'true' : 'false'; ?>"
				>
					<span class="edminboost-theme-preset-picker__option-main">
						<span class="edminboost-theme-preset-picker__option-name"><?php echo esc_html( $edminboost_preset['name'] ); ?></span>
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

	<p class="description edminboost-overview-card__desc" id="edminboost-theme-preset-desc">
		<?php
		echo esc_html(
			isset( $edminboost_theme_presets[ $edminboost_active_preset ]['description'] )
				? $edminboost_theme_presets[ $edminboost_active_preset ]['description']
				: ''
		);
		?>
	</p>
</div>
