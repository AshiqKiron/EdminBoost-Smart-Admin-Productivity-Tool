<?php
/**
 * Compact admin chrome preview for Dashboard color theme card.
 *
 * @package EdminBoost
 *
 * @var array  $edminboost_preview_colors Resolved theme color tokens (legacy include variable).
 * @var string $edminboost_preview_id     Root element id (legacy include variable).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_preview_id     = isset( $edminboost_preview_id ) ? $edminboost_preview_id : 'edminboost-overview-theme-preview';
$edminboost_preview_colors = isset( $edminboost_preview_colors ) && is_array( $edminboost_preview_colors ) ? $edminboost_preview_colors : array();
$edminboost_color_defaults = array(
	'accent'  => '#2271b1',
	'surface' => '#ffffff',
	'text'    => '#1d2327',
	'topbar'  => '#1d2327',
	'sidebar' => '#1d2327',
	'content' => '#f0f0f1',
);
$edminboost_preview_colors = wp_parse_args( $edminboost_preview_colors, $edminboost_color_defaults );

$edminboost_style_vars = sprintf(
	'--eb-op-accent:%1$s;--eb-op-surface:%2$s;--eb-op-text:%3$s;--eb-op-top:%4$s;--eb-op-sidebar:%5$s;--eb-op-content:%6$s;',
	esc_attr( $edminboost_preview_colors['accent'] ),
	esc_attr( $edminboost_preview_colors['surface'] ),
	esc_attr( $edminboost_preview_colors['text'] ),
	esc_attr( $edminboost_preview_colors['topbar'] ),
	esc_attr( $edminboost_preview_colors['sidebar'] ),
	esc_attr( $edminboost_preview_colors['content'] )
);
?>
<div
	class="edminboost-overview-card__preview edminboost-overview-theme-preview"
	id="<?php echo esc_attr( $edminboost_preview_id ); ?>"
	style="<?php echo esc_attr( $edminboost_style_vars ); ?>"
	role="img"
	aria-label="<?php esc_attr_e( 'Admin color theme preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
>
	<div class="edminboost-overview-theme-preview__bar" aria-hidden="true"></div>
	<div class="edminboost-overview-theme-preview__layout" aria-hidden="true">
		<div class="edminboost-overview-theme-preview__sidebar"></div>
		<div class="edminboost-overview-theme-preview__main">
			<span class="edminboost-overview-theme-preview__accent"></span>
			<span class="edminboost-overview-theme-preview__line"></span>
			<span class="edminboost-overview-theme-preview__line edminboost-overview-theme-preview__line--short"></span>
		</div>
	</div>
	<ul class="edminboost-overview-theme-preview__swatches" aria-hidden="true">
		<?php foreach ( EDMINBOOST_Theme::get_color_labels() as $edminboost_color_key => $edminboost_color_label ) : ?>
			<?php
			$edminboost_chip_color = isset( $edminboost_preview_colors[ $edminboost_color_key ] ) ? $edminboost_preview_colors[ $edminboost_color_key ] : $edminboost_color_defaults['accent'];
			?>
			<li
				class="edminboost-overview-theme-preview__swatch"
				style="background-color: <?php echo esc_attr( $edminboost_chip_color ); ?>;"
				title="<?php echo esc_attr( $edminboost_color_label ); ?>"
			></li>
		<?php endforeach; ?>
	</ul>
</div>
