<?php
/**
 * Compact read-only top bar preview for Dashboard overview cards.
 *
 * @package EdminBoost
 *
 * @var array  $preview_items       Top bar items to render.
 * @var string $preview_id          Root element id.
 * @var string $preview_aria_label  Accessible label for the preview region.
 * @var bool   $show_interaction    Whether to mark drawer vs direct links.
 * @var bool   $compact_preview     Optional icon-only compact strip (default: labels shown).
 * @var int    $preview_limit       Maximum number of items to show.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preview_items      = isset( $preview_items ) && is_array( $preview_items ) ? $preview_items : array();
$preview_id         = isset( $preview_id ) ? $preview_id : 'edminboost-overview-topbar-preview';
$preview_aria_label = isset( $preview_aria_label ) ? $preview_aria_label : __( 'Top bar preview', EDMINBOOST_TEXT_DOMAIN );
$show_interaction   = ! empty( $show_interaction );
$compact_preview    = ! empty( $compact_preview );
$preview_limit      = isset( $preview_limit ) ? max( 1, (int) $preview_limit ) : ( $compact_preview ? 10 : 6 );
$visible_items      = array();
$overflow_count     = 0;

foreach ( $preview_items as $preview_item ) {
	$item_slug = isset( $preview_item['slug'] ) ? (string) $preview_item['slug'] : '';
	if ( '' === $item_slug ) {
		continue;
	}

	if ( count( $visible_items ) >= $preview_limit ) {
		++$overflow_count;
		continue;
	}

	$visible_items[] = $preview_item;
}
?>
<div
	class="edminboost-overview-card__preview edminboost-overview-topbar-preview<?php echo $compact_preview ? ' edminboost-overview-topbar-preview--compact' : ''; ?><?php echo empty( $visible_items ) ? ' edminboost-overview-topbar-preview--empty' : ''; ?>"
	id="<?php echo esc_attr( $preview_id ); ?>"
	role="group"
	aria-label="<?php echo esc_attr( $preview_aria_label ); ?>"
>
	<?php if ( empty( $visible_items ) ) : ?>
		<p class="edminboost-overview-topbar-preview__empty">
			<?php esc_html_e( 'No links in this preview yet.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	<?php else : ?>
		<div class="edminboost-overview-topbar-preview__canvas">
			<span class="edminboost-overview-topbar-preview__tip edminboost-overview-topbar-preview__brand">
				<span class="dashicons dashicons-wordpress" aria-hidden="true"></span>
				<span class="edminboost-overview-topbar-preview__tooltip" role="tooltip"><?php esc_html_e( 'WordPress', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			</span>
			<ul class="edminboost-overview-topbar-preview__items">
				<?php foreach ( $visible_items as $preview_item ) : ?>
					<?php
					$item_label       = isset( $preview_item['label'] ) ? $preview_item['label'] : $preview_item['slug'];
					$item_icon        = isset( $preview_item['icon'] ) ? $preview_item['icon'] : 'dashicons-admin-generic';
					$item_interaction = isset( $preview_item['interaction'] ) ? $preview_item['interaction'] : 'redirect';
					$is_drawer        = ( 'drawer' === $item_interaction );
					?>
					<li class="edminboost-overview-topbar-preview__item<?php echo $is_drawer ? ' is-drawer' : ' is-direct'; ?>">
						<span class="edminboost-overview-topbar-preview__tip">
							<span class="dashicons <?php echo esc_attr( $item_icon ); ?>" aria-hidden="true"></span>
							<span class="edminboost-overview-topbar-preview__tooltip" role="tooltip"><?php echo esc_html( $item_label ); ?></span>
						</span>
						<span class="edminboost-overview-topbar-preview__label"><?php echo esc_html( $item_label ); ?></span>
						<?php if ( $show_interaction && $is_drawer ) : ?>
							<span class="edminboost-overview-topbar-preview__badge" aria-hidden="true">
								<span class="dashicons dashicons-leftright"></span>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $overflow_count > 0 ) : ?>
				<?php
				$overflow_label = sprintf(
					/* translators: %d: number of additional top bar links not shown in the preview */
					_n( '%d more link', '%d more links', $overflow_count, EDMINBOOST_TEXT_DOMAIN ),
					(int) $overflow_count
				);
				?>
				<span class="edminboost-overview-topbar-preview__more">
					<?php
					printf(
						/* translators: %d: number of additional top bar links not shown in the preview */
						esc_html__( '+%d', EDMINBOOST_TEXT_DOMAIN ),
						(int) $overflow_count
					);
					?>
					<span class="edminboost-overview-topbar-preview__tooltip" role="tooltip"><?php echo esc_html( $overflow_label ); ?></span>
				</span>
			<?php endif; ?>
			<span class="edminboost-overview-topbar-preview__tip edminboost-overview-topbar-preview__profile">
				<span class="dashicons dashicons-admin-users" aria-hidden="true"></span>
				<span class="edminboost-overview-topbar-preview__tooltip" role="tooltip"><?php esc_html_e( 'My account', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			</span>
		</div>
	<?php endif; ?>
</div>
