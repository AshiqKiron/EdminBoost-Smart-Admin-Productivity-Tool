<?php
/**
 * Compact theme preset picker for Dashboard overview cards.
 *
 * @package EdminBoost
 *
 * @var string $option_name Settings option name.
 * @var string $theme_key   Form field prefix for theme.
 * @var array  $theme       Current theme settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme_presets  = EDMINBOOST_Theme::get_presets();
$color_labels   = EDMINBOOST_Theme::get_color_labels();
$active_preset  = isset( $theme['preset'] ) ? $theme['preset'] : 'default';
$preset_colors  = isset( $theme_presets[ $active_preset ]['colors'] ) ? $theme_presets[ $active_preset ]['colors'] : $theme_presets['default']['colors'];

if ( EDMINBOOST_Theme::uses_custom_colors( $theme ) ) {
	$custom_color_map = array(
		'accent'  => 'custom_accent',
		'surface' => 'custom_surface',
		'text'    => 'custom_text',
		'topbar'  => 'custom_top',
		'sidebar' => 'custom_sidebar',
		'content' => 'custom_content',
	);

	foreach ( $custom_color_map as $color_key => $theme_color_key ) {
		if ( ! empty( $theme[ $theme_color_key ] ) ) {
			$preset_colors[ $color_key ] = $theme[ $theme_color_key ];
		}
	}
}
?>
<div class="edminboost-overview-theme-picker">
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
			aria-label="<?php esc_attr_e( 'Color theme', EDMINBOOST_TEXT_DOMAIN ); ?>"
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

	<p class="description edminboost-overview-card__desc" id="edminboost-theme-preset-desc">
		<?php
		echo esc_html(
			isset( $theme_presets[ $active_preset ]['description'] )
				? $theme_presets[ $active_preset ]['description']
				: ''
		);
		?>
	</p>
</div>
