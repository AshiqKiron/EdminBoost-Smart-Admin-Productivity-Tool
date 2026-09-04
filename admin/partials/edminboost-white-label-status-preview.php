<?php
/**
 * Live system status footer preview for the White Label settings page.
 *
 * @package EdminBoost
 *
 * @var array $wl White label settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wl               = isset( $wl ) && is_array( $wl ) ? $wl : EDMINBOOST_White_Label::get_settings();
$segment_texts    = EDMINBOOST_White_Label::get_status_footer_segment_texts();
$enabled_parts    = EDMINBOOST_White_Label::get_status_footer_parts( $wl );
$segment_labels   = array(
	'show_ip'               => __( 'IP address', EDMINBOOST_TEXT_DOMAIN ),
	'show_php_version'      => __( 'PHP version', EDMINBOOST_TEXT_DOMAIN ),
	'show_wp_version'       => __( 'WordPress version', EDMINBOOST_TEXT_DOMAIN ),
	'show_memory_usage'     => __( 'Memory usage', EDMINBOOST_TEXT_DOMAIN ),
	'show_memory_limit'     => __( 'Memory limit', EDMINBOOST_TEXT_DOMAIN ),
	'show_memory_available' => __( 'Memory available', EDMINBOOST_TEXT_DOMAIN ),
);
$has_enabled_part = ! empty( $enabled_parts );
?>
<div
	id="edminboost-wl-status-preview"
	class="edminboost-wl-status-preview<?php echo $has_enabled_part ? '' : ' is-empty'; ?>"
	role="region"
	aria-label="<?php esc_attr_e( 'System status footer live preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
	aria-live="polite"
>
	<p class="edminboost-wl-status-preview__lead"><?php esc_html_e( 'Live preview', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	<p class="edminboost-wl-status-preview__desc"><?php esc_html_e( 'Shows the right-hand admin footer line affected by the toggles above.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

	<div class="edminboost-wl-status-preview__footer" aria-hidden="true">
		<span class="edminboost-wl-status-preview__credit"><?php esc_html_e( 'Thank you for creating with WordPress.', EDMINBOOST_TEXT_DOMAIN ); ?></span>
		<span class="edminboost-wl-status-preview__status">
			<span
				id="edminboost-wl-status-preview-empty"
				class="edminboost-wl-status-preview__empty"
				<?php echo $has_enabled_part ? ' hidden' : ''; ?>
			><?php esc_html_e( 'No status details selected.', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			<span
				id="edminboost-wl-status-preview-line"
				class="edminboost-wl-status-preview__line"
				<?php echo $has_enabled_part ? '' : ' hidden'; ?>
			>
				<?php foreach ( $segment_texts as $segment_key => $segment_text ) : ?>
					<?php
					$is_visible = ! empty( $wl[ $segment_key ] );
					$segment_class = 'edminboost-wl-status-preview__segment';
					if ( ! $is_visible ) {
						$segment_class .= ' is-hidden';
					}

					$tooltip_visible = sprintf(
						/* translators: %s: footer segment label */
						__( '%s — Visible', EDMINBOOST_TEXT_DOMAIN ),
						$segment_labels[ $segment_key ]
					);
					$tooltip_hidden = sprintf(
						/* translators: %s: footer segment label */
						__( '%s — Hidden', EDMINBOOST_TEXT_DOMAIN ),
						$segment_labels[ $segment_key ]
					);
					$tooltip_text   = $is_visible ? $tooltip_visible : $tooltip_hidden;
					?>
					<span
						class="<?php echo esc_attr( $segment_class ); ?>"
						data-preview="<?php echo esc_attr( $segment_key ); ?>"
						data-tooltip-visible="<?php echo esc_attr( $tooltip_visible ); ?>"
						data-tooltip-hidden="<?php echo esc_attr( $tooltip_hidden ); ?>"
						tabindex="0"
						aria-label="<?php echo esc_attr( $tooltip_text ); ?>"
					>
						<span class="edminboost-wl-status-preview__segment-text"><?php echo esc_html( $segment_text ); ?></span>
						<span class="edminboost-wl-status-preview__tooltip" role="tooltip"><?php echo esc_html( $tooltip_text ); ?></span>
					</span>
				<?php endforeach; ?>
			</span>
		</span>
	</div>
</div>
