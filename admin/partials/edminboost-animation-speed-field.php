<?php
/**
 * Animation speed field with live drawer preview per option.
 *
 * @package EdminBoost
 *
 * @var string $edminboost_cc_key   Form field prefix for behavior.
 * @var array  $edminboost_behavior Current behavior settings.
 * @var string $cc_key              Legacy include variable.
 * @var array  $behavior            Legacy include variable.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_cc_key = isset( $edminboost_cc_key )
	? $edminboost_cc_key
	: ( isset( $cc_key ) ? $cc_key : '' );
$edminboost_behavior = isset( $edminboost_behavior ) && is_array( $edminboost_behavior )
	? $edminboost_behavior
	: ( isset( $behavior ) && is_array( $behavior ) ? $behavior : array() );

$edminboost_animation_speeds = EDMINBOOST_Command_Center::get_animation_speed_options();
$edminboost_active_speed     = isset( $edminboost_behavior['animation_speed'] ) ? $edminboost_behavior['animation_speed'] : 'normal';

if ( ! isset( $edminboost_animation_speeds[ $edminboost_active_speed ] ) ) {
	$edminboost_active_speed = 'normal';
}

$edminboost_active_label = $edminboost_animation_speeds[ $edminboost_active_speed ]['label'];
$edminboost_active_ms    = $edminboost_animation_speeds[ $edminboost_active_speed ]['ms'];
?>
<fieldset class="edminboost-fieldset">
	<legend for="edminboost_animation_speed"><?php EDMINBOOST_Setting_Help::echo_icon( 'animation_speed' ); ?><?php esc_html_e( 'Animation speed', 'edminboost-admin-customization' ); ?></legend>
	<select
		name="<?php echo esc_attr( $edminboost_cc_key ); ?>[animation_speed]"
		id="edminboost_animation_speed"
		class="screen-reader-text"
		tabindex="-1"
		aria-hidden="true"
	>
		<?php foreach ( $edminboost_animation_speeds as $edminboost_speed_id => $edminboost_speed ) : ?>
			<option value="<?php echo esc_attr( $edminboost_speed_id ); ?>" <?php selected( $edminboost_active_speed, $edminboost_speed_id ); ?>>
				<?php echo esc_html( $edminboost_speed['label'] ); ?>
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
					<?php echo esc_html( $edminboost_active_label ); ?>
				</span>
				<span
					class="edminboost-animation-speed-picker__preview"
					aria-hidden="true"
					style="--edminboost-animation-preview-ms: <?php echo esc_attr( (string) $edminboost_active_ms ); ?>ms;"
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
			aria-label="<?php esc_attr_e( 'Animation speed', 'edminboost-admin-customization' ); ?>"
			hidden
		>
			<?php foreach ( $edminboost_animation_speeds as $edminboost_speed_id => $edminboost_speed ) : ?>
				<?php $edminboost_is_selected = ( $edminboost_active_speed === $edminboost_speed_id ); ?>
				<li
					class="edminboost-animation-speed-picker__option<?php echo $edminboost_is_selected ? ' is-selected' : ''; ?>"
					role="option"
					tabindex="-1"
					data-value="<?php echo esc_attr( $edminboost_speed_id ); ?>"
					data-ms="<?php echo esc_attr( (string) $edminboost_speed['ms'] ); ?>"
					aria-selected="<?php echo $edminboost_is_selected ? 'true' : 'false'; ?>"
				>
					<span class="edminboost-animation-speed-picker__option-main">
						<span class="edminboost-animation-speed-picker__option-name"><?php echo esc_html( $edminboost_speed['label'] ); ?></span>
						<span
							class="edminboost-animation-speed-picker__preview"
							aria-hidden="true"
							style="--edminboost-animation-preview-ms: <?php echo esc_attr( (string) $edminboost_speed['ms'] ); ?>ms;"
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
