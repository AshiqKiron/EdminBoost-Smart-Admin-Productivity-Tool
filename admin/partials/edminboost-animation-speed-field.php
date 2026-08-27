<?php
/**
 * Animation speed field with live drawer preview per option.
 *
 * @package EdminBoost
 *
 * @var string $cc_key   Form field prefix for behavior.
 * @var array  $behavior Current behavior settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$animation_speeds = EDMINBOOST_Command_Center::get_animation_speed_options();
$active_speed     = isset( $behavior['animation_speed'] ) ? $behavior['animation_speed'] : 'normal';

if ( ! isset( $animation_speeds[ $active_speed ] ) ) {
	$active_speed = 'normal';
}

$active_label = $animation_speeds[ $active_speed ]['label'];
$active_ms    = $animation_speeds[ $active_speed ]['ms'];
?>
<fieldset class="edminboost-fieldset">
	<legend for="edminboost_animation_speed"><?php esc_html_e( 'Animation speed', EDMINBOOST_TEXT_DOMAIN ); ?><?php EDMINBOOST_Setting_Help::echo_icon( 'animation_speed' ); ?></legend>
	<select
		name="<?php echo esc_attr( $cc_key ); ?>[animation_speed]"
		id="edminboost_animation_speed"
		class="screen-reader-text"
		tabindex="-1"
		aria-hidden="true"
	>
		<?php foreach ( $animation_speeds as $speed_id => $speed ) : ?>
			<option value="<?php echo esc_attr( $speed_id ); ?>" <?php selected( $active_speed, $speed_id ); ?>>
				<?php echo esc_html( $speed['label'] ); ?>
			</option>
		<?php endforeach; ?>
	</select>

	<div class="edminboost-animation-speed-picker" id="edminboost-animation-speed-picker">
		<button
			type="button"
			class="edminboost-animation-speed-picker__toggle"
			id="edminboost_animation_speed_toggle"
			aria-expanded="false"
			aria-controls="edminboost-animation-speed-list"
			aria-haspopup="listbox"
		>
			<span class="edminboost-animation-speed-picker__label">
				<span class="edminboost-animation-speed-picker__name" id="edminboost-animation-speed-name">
					<?php echo esc_html( $active_label ); ?>
				</span>
				<span
					class="edminboost-animation-speed-picker__preview"
					aria-hidden="true"
					style="--edminboost-animation-preview-ms: <?php echo esc_attr( (string) $active_ms ); ?>ms;"
				>
					<span class="edminboost-animation-speed-picker__preview-content"></span>
					<span class="edminboost-animation-speed-picker__preview-drawer" id="edminboost_animation_speed_toggle_drawer"></span>
				</span>
			</span>
			<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
		</button>

		<ul
			class="edminboost-animation-speed-picker__list"
			id="edminboost-animation-speed-list"
			role="listbox"
			aria-label="<?php esc_attr_e( 'Animation speed', EDMINBOOST_TEXT_DOMAIN ); ?>"
			hidden
		>
			<?php foreach ( $animation_speeds as $speed_id => $speed ) : ?>
				<?php $is_selected = ( $active_speed === $speed_id ); ?>
				<li
					class="edminboost-animation-speed-picker__option<?php echo $is_selected ? ' is-selected' : ''; ?>"
					role="option"
					tabindex="-1"
					data-value="<?php echo esc_attr( $speed_id ); ?>"
					data-ms="<?php echo esc_attr( (string) $speed['ms'] ); ?>"
					aria-selected="<?php echo $is_selected ? 'true' : 'false'; ?>"
				>
					<span class="edminboost-animation-speed-picker__option-main">
						<span class="edminboost-animation-speed-picker__option-name"><?php echo esc_html( $speed['label'] ); ?></span>
						<span
							class="edminboost-animation-speed-picker__preview"
							aria-hidden="true"
							style="--edminboost-animation-preview-ms: <?php echo esc_attr( (string) $speed['ms'] ); ?>ms;"
						>
							<span class="edminboost-animation-speed-picker__preview-content"></span>
							<span class="edminboost-animation-speed-picker__preview-drawer"></span>
						</span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</fieldset>
