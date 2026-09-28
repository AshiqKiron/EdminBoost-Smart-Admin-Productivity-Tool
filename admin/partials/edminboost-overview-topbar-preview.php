<?php
/**
 * Compact read-only top bar preview for Dashboard overview cards.
 *
 * @package EdminBoost
 *
 * @var array  $edminboost_preview_items       Top bar items to render.
 * @var string $edminboost_preview_id          Root element id.
 * @var string $edminboost_preview_aria_label  Accessible label for the preview region.
 * @var bool   $edminboost_show_interaction    Whether to mark drawer vs direct links.
 * @var bool   $edminboost_compact_preview     Optional icon-only compact strip (default: labels shown).
 * @var int    $edminboost_preview_limit       Maximum number of items to show.
 * @var array  $preview_items                  Legacy include variable for top bar items.
 * @var string $preview_id                     Legacy include variable for root element id.
 * @var string $preview_aria_label             Legacy include variable for accessible label.
 * @var bool   $show_interaction               Legacy include variable for drawer vs direct links.
 * @var bool   $compact_preview                Legacy include variable for icon-only compact strip.
 * @var int    $preview_limit                  Legacy include variable for maximum items to show.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_preview_items = isset( $edminboost_preview_items ) && is_array( $edminboost_preview_items )
	? $edminboost_preview_items
	: ( isset( $preview_items ) && is_array( $preview_items ) ? $preview_items : array() );
$edminboost_preview_id = isset( $edminboost_preview_id )
	? $edminboost_preview_id
	: ( isset( $preview_id ) ? $preview_id : 'edminboost-overview-topbar-preview' );
$edminboost_preview_aria_label = isset( $edminboost_preview_aria_label )
	? $edminboost_preview_aria_label
	: ( isset( $preview_aria_label ) ? $preview_aria_label : __( 'Top bar preview', 'edminboost-smart-admin-productivity-tool' ) );
$edminboost_show_interaction = isset( $edminboost_show_interaction )
	? ! empty( $edminboost_show_interaction )
	: ! empty( $show_interaction );
$edminboost_compact_preview = isset( $edminboost_compact_preview )
	? ! empty( $edminboost_compact_preview )
	: ! empty( $compact_preview );
$edminboost_preview_limit = isset( $edminboost_preview_limit )
	? max( 1, (int) $edminboost_preview_limit )
	: ( isset( $preview_limit ) ? max( 1, (int) $preview_limit ) : ( $edminboost_compact_preview ? 10 : 6 ) );
$edminboost_visible_items  = array();
$edminboost_overflow_count = 0;

foreach ( $edminboost_preview_items as $edminboost_preview_item ) {
	$edminboost_item_slug = isset( $edminboost_preview_item['slug'] ) ? (string) $edminboost_preview_item['slug'] : '';
	if ( '' === $edminboost_item_slug ) {
		continue;
	}

	if ( count( $edminboost_visible_items ) >= $edminboost_preview_limit ) {
		++$edminboost_overflow_count;
		continue;
	}

	$edminboost_visible_items[] = $edminboost_preview_item;
}
?>
<div
	class="edminboost-overview-card__preview edminboost-overview-topbar-preview<?php echo $edminboost_compact_preview ? ' edminboost-overview-topbar-preview--compact' : ''; ?><?php echo empty( $edminboost_visible_items ) ? ' edminboost-overview-topbar-preview--empty' : ''; ?>"
	id="<?php echo esc_attr( $edminboost_preview_id ); ?>"
	role="group"
	aria-label="<?php echo esc_attr( $edminboost_preview_aria_label ); ?>"
>
	<?php if ( empty( $edminboost_visible_items ) ) : ?>
		<p class="edminboost-overview-topbar-preview__empty">
			<?php esc_html_e( 'No links in this preview yet.', 'edminboost-smart-admin-productivity-tool' ); ?>
		</p>
	<?php else : ?>
		<div class="edminboost-overview-topbar-preview__canvas">
			<span class="edminboost-overview-topbar-preview__tip edminboost-overview-topbar-preview__brand">
				<span class="dashicons dashicons-wordpress" aria-hidden="true"></span>
				<span class="edminboost-overview-topbar-preview__tooltip" role="tooltip"><?php esc_html_e( 'WordPress', 'edminboost-smart-admin-productivity-tool' ); ?></span>
			</span>
			<ul class="edminboost-overview-topbar-preview__items">
				<?php foreach ( $edminboost_visible_items as $edminboost_preview_item ) : ?>
					<?php
					$edminboost_item_label       = isset( $edminboost_preview_item['label'] ) ? $edminboost_preview_item['label'] : $edminboost_preview_item['slug'];
					$edminboost_item_icon        = isset( $edminboost_preview_item['icon'] ) ? $edminboost_preview_item['icon'] : 'dashicons-admin-generic';
					$edminboost_item_interaction = isset( $edminboost_preview_item['interaction'] ) ? $edminboost_preview_item['interaction'] : 'redirect';
					$edminboost_is_drawer        = ( 'drawer' === $edminboost_item_interaction );
					?>
					<li class="edminboost-overview-topbar-preview__item<?php echo $edminboost_is_drawer ? ' is-drawer' : ' is-direct'; ?>">
						<span class="edminboost-overview-topbar-preview__tip">
							<span class="dashicons <?php echo esc_attr( $edminboost_item_icon ); ?>" aria-hidden="true"></span>
							<span class="edminboost-overview-topbar-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_item_label ); ?></span>
						</span>
						<span class="edminboost-overview-topbar-preview__label"><?php echo esc_html( $edminboost_item_label ); ?></span>
						<?php if ( $edminboost_show_interaction && $edminboost_is_drawer ) : ?>
							<span class="edminboost-overview-topbar-preview__badge" aria-hidden="true">
								<span class="dashicons dashicons-leftright"></span>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $edminboost_overflow_count > 0 ) : ?>
				<?php
				$edminboost_overflow_label = sprintf(
					/* translators: %d: number of additional top bar links not shown in the preview */
					_n( '%d more link', '%d more links', $edminboost_overflow_count, 'edminboost-smart-admin-productivity-tool' ),
					(int) $edminboost_overflow_count
				);
				?>
				<span class="edminboost-overview-topbar-preview__more">
					<?php
					printf(
						/* translators: %d: number of additional top bar links not shown in the preview */
						esc_html__( '+%d', 'edminboost-smart-admin-productivity-tool' ),
						(int) $edminboost_overflow_count
					);
					?>
					<span class="edminboost-overview-topbar-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_overflow_label ); ?></span>
				</span>
			<?php endif; ?>
			<span class="edminboost-overview-topbar-preview__tip edminboost-overview-topbar-preview__profile">
				<span class="dashicons dashicons-admin-users" aria-hidden="true"></span>
				<span class="edminboost-overview-topbar-preview__tooltip" role="tooltip"><?php esc_html_e( 'My account', 'edminboost-smart-admin-productivity-tool' ); ?></span>
			</span>
		</div>
	<?php endif; ?>
</div>
