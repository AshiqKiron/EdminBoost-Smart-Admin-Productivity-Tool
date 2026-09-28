<?php
/**
 * Live system status footer preview for the White Label settings page.
 *
 * @package EdminBoost
 *
 * @var array $edminboost_wl White label settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_wl = isset( $edminboost_wl ) && is_array( $edminboost_wl )
	? $edminboost_wl
	: ( isset( $wl ) && is_array( $wl ) ? $wl : EDMINBOOST_White_Label::get_settings() );
$edminboost_segment_texts    = EDMINBOOST_White_Label::get_status_footer_segment_texts();
$edminboost_enabled_parts    = EDMINBOOST_White_Label::get_status_footer_parts( $edminboost_wl );
$edminboost_segment_labels   = array(
	'show_ip'               => __( 'IP address', 'edminboost-smart-admin-productivity-tool' ),
	'show_php_version'      => __( 'PHP version', 'edminboost-smart-admin-productivity-tool' ),
	'show_wp_version'       => __( 'WordPress version', 'edminboost-smart-admin-productivity-tool' ),
	'show_memory_usage'     => __( 'Memory usage', 'edminboost-smart-admin-productivity-tool' ),
	'show_memory_limit'     => __( 'Memory limit', 'edminboost-smart-admin-productivity-tool' ),
	'show_memory_available' => __( 'Memory available', 'edminboost-smart-admin-productivity-tool' ),
);
$edminboost_has_enabled_part = ! empty( $edminboost_enabled_parts );
?>
<div
	id="edminboost-wl-status-preview"
	class="edminboost-wl-status-preview<?php echo $edminboost_has_enabled_part ? '' : ' is-empty'; ?>"
	role="region"
	aria-label="<?php esc_attr_e( 'System status footer live preview', 'edminboost-smart-admin-productivity-tool' ); ?>"
	aria-live="polite"
>
	<p class="edminboost-wl-status-preview__lead"><?php esc_html_e( 'Live preview', 'edminboost-smart-admin-productivity-tool' ); ?></p>
	<p class="edminboost-wl-status-preview__desc"><?php esc_html_e( 'Shows the right-hand admin footer line affected by the toggles above.', 'edminboost-smart-admin-productivity-tool' ); ?></p>

	<div class="edminboost-wl-status-preview__footer" aria-hidden="true">
		<span class="edminboost-wl-status-preview__credit"><?php esc_html_e( 'Thank you for creating with WordPress.', 'edminboost-smart-admin-productivity-tool' ); ?></span>
		<span class="edminboost-wl-status-preview__status">
			<span
				id="edminboost-wl-status-preview-empty"
				class="edminboost-wl-status-preview__empty"
				<?php echo $edminboost_has_enabled_part ? ' hidden' : ''; ?>
			><?php esc_html_e( 'No status details selected.', 'edminboost-smart-admin-productivity-tool' ); ?></span>
			<span
				id="edminboost-wl-status-preview-line"
				class="edminboost-wl-status-preview__line"
				<?php echo $edminboost_has_enabled_part ? '' : ' hidden'; ?>
			>
				<?php foreach ( $edminboost_segment_texts as $edminboost_segment_key => $edminboost_segment_text ) : ?>
					<?php
					$edminboost_is_visible = ! empty( $edminboost_wl[ $edminboost_segment_key ] );
					$edminboost_segment_class = 'edminboost-wl-status-preview__segment';
					if ( ! $edminboost_is_visible ) {
						$edminboost_segment_class .= ' is-hidden';
					}

					$edminboost_tooltip_visible = sprintf(
						/* translators: %s: footer segment label */
						__( '%s — Visible', 'edminboost-smart-admin-productivity-tool' ),
						$edminboost_segment_labels[ $edminboost_segment_key ]
					);
					$edminboost_tooltip_hidden = sprintf(
						/* translators: %s: footer segment label */
						__( '%s — Hidden', 'edminboost-smart-admin-productivity-tool' ),
						$edminboost_segment_labels[ $edminboost_segment_key ]
					);
					$edminboost_tooltip_text   = $edminboost_is_visible ? $edminboost_tooltip_visible : $edminboost_tooltip_hidden;
					?>
					<span
						class="<?php echo esc_attr( $edminboost_segment_class ); ?>"
						data-preview="<?php echo esc_attr( $edminboost_segment_key ); ?>"
						data-tooltip-visible="<?php echo esc_attr( $edminboost_tooltip_visible ); ?>"
						data-tooltip-hidden="<?php echo esc_attr( $edminboost_tooltip_hidden ); ?>"
						tabindex="0"
						aria-label="<?php echo esc_attr( $edminboost_tooltip_text ); ?>"
					>
						<span class="edminboost-wl-status-preview__segment-text"><?php echo esc_html( $edminboost_segment_text ); ?></span>
						<span class="edminboost-wl-status-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_tooltip_text ); ?></span>
					</span>
				<?php endforeach; ?>
			</span>
		</span>
	</div>
</div>
