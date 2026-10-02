<?php
/**
 * Scheduled dark mode block in Appearance extras preview (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var bool   $edminboost_schedule_enabled Whether schedule is on.
 * @var string $edminboost_schedule_start   Start time.
 * @var string $edminboost_schedule_end     End time.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div
	class="edminboost-theme-extras-preview__schedule<?php echo $edminboost_schedule_enabled ? ' is-active' : ''; ?>"
	id="edminboost-theme-extras-preview-schedule"
	<?php echo $edminboost_schedule_enabled ? '' : 'hidden'; ?>
>
	<p class="edminboost-theme-extras-preview__schedule-label">
		<span class="dashicons dashicons-clock" aria-hidden="true"></span>
		<?php esc_html_e( 'Scheduled dark mode (Auto color mode)', 'edminboost-admin-customization' ); ?>
	</p>
	<div
		class="edminboost-theme-extras-preview__schedule-track"
		style="--eb-te-schedule-start: <?php echo esc_attr( $edminboost_schedule_start ); ?>; --eb-te-schedule-end: <?php echo esc_attr( $edminboost_schedule_end ); ?>;"
	>
		<span class="edminboost-theme-extras-preview__schedule-day" aria-hidden="true"></span>
		<span class="edminboost-theme-extras-preview__schedule-night" aria-hidden="true"></span>
	</div>
	<p class="edminboost-theme-extras-preview__schedule-times description">
		<span id="edminboost-theme-extras-preview-schedule-start"><?php echo esc_html( $edminboost_schedule_start ); ?></span>
		&ndash;
		<span id="edminboost-theme-extras-preview-schedule-end"><?php echo esc_html( $edminboost_schedule_end ); ?></span>
	</p>
</div>
