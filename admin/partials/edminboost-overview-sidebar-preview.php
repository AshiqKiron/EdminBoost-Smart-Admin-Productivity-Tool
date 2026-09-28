<?php
/**
 * Compact read-only sidebar menu preview for layout preset cards.
 *
 * @package EdminBoost
 *
 * @var array  $edminboost_sidebar_items       Sidebar menu items to render.
 * @var string $edminboost_preview_id          Root element id.
 * @var string $edminboost_preview_aria_label  Accessible label for the preview region.
 * @var int    $edminboost_preview_limit       Maximum number of items to show.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_sidebar_items      = isset( $edminboost_sidebar_items ) && is_array( $edminboost_sidebar_items ) ? $edminboost_sidebar_items : array();
$edminboost_preview_id         = isset( $edminboost_preview_id ) ? $edminboost_preview_id : 'edminboost-overview-sidebar-preview';
$edminboost_preview_aria_label = isset( $edminboost_preview_aria_label ) ? $edminboost_preview_aria_label : __( 'Sidebar menu preview', 'edminboost-smart-admin-productivity-tool' );
$edminboost_preview_limit      = isset( $edminboost_preview_limit ) ? max( 1, (int) $edminboost_preview_limit ) : 8;
$edminboost_visible_items      = array();
$edminboost_overflow_count     = 0;

foreach ( $edminboost_sidebar_items as $edminboost_sidebar_item ) {
	$edminboost_item_slug = isset( $edminboost_sidebar_item['slug'] ) ? (string) $edminboost_sidebar_item['slug'] : '';
	if ( '' === $edminboost_item_slug ) {
		continue;
	}

	if ( count( $edminboost_visible_items ) >= $edminboost_preview_limit ) {
		++$edminboost_overflow_count;
		continue;
	}

	$edminboost_visible_items[] = $edminboost_sidebar_item;
}
?>
<div
	class="edminboost-overview-card__preview edminboost-overview-sidebar-preview<?php echo empty( $edminboost_visible_items ) ? ' edminboost-overview-sidebar-preview--empty' : ''; ?>"
	id="<?php echo esc_attr( $edminboost_preview_id ); ?>"
	role="img"
	aria-label="<?php echo esc_attr( $edminboost_preview_aria_label ); ?>"
>
	<?php if ( empty( $edminboost_visible_items ) ) : ?>
		<p class="edminboost-overview-sidebar-preview__empty">
			<?php esc_html_e( 'No sidebar items in this preview yet.', 'edminboost-smart-admin-productivity-tool' ); ?>
		</p>
	<?php else : ?>
		<ul class="edminboost-overview-sidebar-preview__list" aria-hidden="true">
			<?php foreach ( $edminboost_visible_items as $edminboost_sidebar_item ) : ?>
				<?php
				$edminboost_item_label = isset( $edminboost_sidebar_item['label'] ) ? $edminboost_sidebar_item['label'] : $edminboost_sidebar_item['slug'];
				$edminboost_item_icon  = isset( $edminboost_sidebar_item['icon'] ) ? $edminboost_sidebar_item['icon'] : 'dashicons-admin-generic';
				?>
				<li class="edminboost-overview-sidebar-preview__item">
					<span
						class="dashicons <?php echo esc_attr( $edminboost_item_icon ); ?>"
						aria-hidden="true"
						title="<?php echo esc_attr( $edminboost_item_label ); ?>"
					></span>
					<span class="edminboost-overview-sidebar-preview__label"><?php echo esc_html( $edminboost_item_label ); ?></span>
				</li>
			<?php endforeach; ?>
			<?php if ( $edminboost_overflow_count > 0 ) : ?>
				<li class="edminboost-overview-sidebar-preview__more">
					<?php
					printf(
						/* translators: %d: number of additional sidebar items not shown in the preview */
						esc_html__( '+%d more', 'edminboost-smart-admin-productivity-tool' ),
						(int) $edminboost_overflow_count
					);
					?>
				</li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
</div>
