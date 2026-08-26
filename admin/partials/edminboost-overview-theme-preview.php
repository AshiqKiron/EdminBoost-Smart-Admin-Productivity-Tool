<?php
/**
 * Compact admin chrome preview for Dashboard color theme card.
 *
 * @package EdminBoost
 *
 * @var array  $preview_colors Resolved theme color tokens (accent, surface, text, topbar, sidebar, content).
 * @var string $preview_id     Root element id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preview_id     = isset( $preview_id ) ? $preview_id : 'edminboost-overview-theme-preview';
$preview_colors = isset( $preview_colors ) && is_array( $preview_colors ) ? $preview_colors : array();
$defaults       = array(
	'accent'  => '#2271b1',
	'surface' => '#ffffff',
	'text'    => '#1d2327',
	'topbar'  => '#1d2327',
	'sidebar' => '#1d2327',
	'content' => '#f0f0f1',
);
$preview_colors = wp_parse_args( $preview_colors, $defaults );

$style_vars = sprintf(
	'--eb-op-accent:%1$s;--eb-op-surface:%2$s;--eb-op-text:%3$s;--eb-op-top:%4$s;--eb-op-sidebar:%5$s;--eb-op-content:%6$s;',
	esc_attr( $preview_colors['accent'] ),
	esc_attr( $preview_colors['surface'] ),
	esc_attr( $preview_colors['text'] ),
	esc_attr( $preview_colors['topbar'] ),
	esc_attr( $preview_colors['sidebar'] ),
	esc_attr( $preview_colors['content'] )
);
?>
<div
	class="edminboost-overview-card__preview edminboost-overview-theme-preview"
	id="<?php echo esc_attr( $preview_id ); ?>"
	style="<?php echo esc_attr( $style_vars ); ?>"
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
		<?php foreach ( EDMINBOOST_Theme::get_color_labels() as $color_key => $color_label ) : ?>
			<?php
			$chip_color = isset( $preview_colors[ $color_key ] ) ? $preview_colors[ $color_key ] : $defaults['accent'];
			?>
			<li
				class="edminboost-overview-theme-preview__swatch"
				style="background-color: <?php echo esc_attr( $chip_color ); ?>;"
				title="<?php echo esc_attr( $color_label ); ?>"
			></li>
		<?php endforeach; ?>
	</ul>
</div>
