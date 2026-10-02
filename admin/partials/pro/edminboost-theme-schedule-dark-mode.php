<?php
/**
 * Scheduled dark mode fields (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var string $edminboost_theme_key Theme form field prefix.
 * @var array  $edminboost_theme     Theme settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<fieldset class="edminboost-fieldset edminboost-theme-extras-group">
	<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_schedule_dark_mode' ); ?><?php esc_html_e( 'Scheduled dark mode', 'edminboost-admin-customization' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_schedule_dark_mode">
		<input type="checkbox" id="edminboost_schedule_dark_mode" name="<?php echo esc_attr( $edminboost_theme_key ); ?>[schedule_dark_mode]" value="1" <?php checked( ! empty( $edminboost_theme['schedule_dark_mode'] ) ); ?> />
		<?php esc_html_e( 'Enable scheduled dark mode window for Auto color mode.', 'edminboost-admin-customization' ); ?>
	</label>
	<div
		class="edminboost-dependent-section edminboost-theme-extras-schedule<?php echo empty( $edminboost_theme['schedule_dark_mode'] ) ? ' is-disabled' : ''; ?>"
		id="edminboost-theme-schedule-options"
		aria-disabled="<?php echo empty( $edminboost_theme['schedule_dark_mode'] ) ? 'true' : 'false'; ?>"
	>
		<div class="edminboost-theme-extras-schedule-grid">
			<div class="edminboost-theme-extras-row">
				<label for="edminboost_dark_mode_start"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_dark_mode_start' ); ?><?php esc_html_e( 'Dark mode start', 'edminboost-admin-customization' ); ?></label>
				<input
					type="time"
					class="edminboost-theme-extras-time"
					id="edminboost_dark_mode_start"
					name="<?php echo esc_attr( $edminboost_theme_key ); ?>[dark_mode_start]"
					value="<?php echo esc_attr( $edminboost_theme['dark_mode_start'] ?? '18:00' ); ?>"
				/>
			</div>
			<div class="edminboost-theme-extras-row">
				<label for="edminboost_dark_mode_end"><?php EDMINBOOST_Setting_Help::echo_icon( 'theme_dark_mode_end' ); ?><?php esc_html_e( 'Dark mode end', 'edminboost-admin-customization' ); ?></label>
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
</fieldset>
