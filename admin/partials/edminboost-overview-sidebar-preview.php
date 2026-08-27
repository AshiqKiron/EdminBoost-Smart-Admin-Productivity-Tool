<?php
/**
 * Compact read-only sidebar menu preview for layout preset cards.
 *
 * @package EdminBoost
 *
 * @var array  $sidebar_items       Sidebar menu items to render.
 * @var string $preview_id          Root element id.
 * @var string $preview_aria_label  Accessible label for the preview region.
 * @var int    $preview_limit       Maximum number of items to show.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sidebar_items      = isset( $sidebar_items ) && is_array( $sidebar_items ) ? $sidebar_items : array();
$preview_id         = isset( $preview_id ) ? $preview_id : 'edminboost-overview-sidebar-preview';
$preview_aria_label = isset( $preview_aria_label ) ? $preview_aria_label : __( 'Sidebar menu preview', EDMINBOOST_TEXT_DOMAIN );
$preview_limit      = isset( $preview_limit ) ? max( 1, (int) $preview_limit ) : 8;
$visible_items      = array();
$overflow_count     = 0;

foreach ( $sidebar_items as $sidebar_item ) {
	$item_slug = isset( $sidebar_item['slug'] ) ? (string) $sidebar_item['slug'] : '';
	if ( '' === $item_slug ) {
		continue;
	}

	if ( count( $visible_items ) >= $preview_limit ) {
		++$overflow_count;
		continue;
	}

	$visible_items[] = $sidebar_item;
}
?>
<div
	class="edminboost-overview-card__preview edminboost-overview-sidebar-preview<?php echo empty( $visible_items ) ? ' edminboost-overview-sidebar-preview--empty' : ''; ?>"
	id="<?php echo esc_attr( $preview_id ); ?>"
	role="img"
	aria-label="<?php echo esc_attr( $preview_aria_label ); ?>"
>
	<?php if ( empty( $visible_items ) ) : ?>
		<p class="edminboost-overview-sidebar-preview__empty">
			<?php esc_html_e( 'No sidebar items in this preview yet.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	<?php else : ?>
		<ul class="edminboost-overview-sidebar-preview__list" aria-hidden="true">
			<?php foreach ( $visible_items as $sidebar_item ) : ?>
				<?php
				$item_label = isset( $sidebar_item['label'] ) ? $sidebar_item['label'] : $sidebar_item['slug'];
				$item_icon  = isset( $sidebar_item['icon'] ) ? $sidebar_item['icon'] : 'dashicons-admin-generic';
				?>
				<li class="edminboost-overview-sidebar-preview__item">
					<span
						class="dashicons <?php echo esc_attr( $item_icon ); ?>"
						aria-hidden="true"
						title="<?php echo esc_attr( $item_label ); ?>"
					></span>
					<span class="edminboost-overview-sidebar-preview__label"><?php echo esc_html( $item_label ); ?></span>
				</li>
			<?php endforeach; ?>
			<?php if ( $overflow_count > 0 ) : ?>
				<li class="edminboost-overview-sidebar-preview__more">
					<?php
					printf(
						/* translators: %d: number of additional sidebar items not shown in the preview */
						esc_html__( '+%d more', EDMINBOOST_TEXT_DOMAIN ),
						(int) $overflow_count
					);
					?>
				</li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
</div>
