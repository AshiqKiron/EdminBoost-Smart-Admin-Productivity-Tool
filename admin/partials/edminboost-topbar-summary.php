<?php
/**
 * Read-only top bar layout summary.
 *
 * @package EdminBoost
 *
 * @var array  $edminboost_top_bar_items Top bar items to display.
 * @var string $edminboost_mapper_url    URL to the full Top Bar editor.
 * @var string $edminboost_summary_id    Optional root element id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_top_bar_items = isset( $edminboost_top_bar_items ) && is_array( $edminboost_top_bar_items )
	? $edminboost_top_bar_items
	: array();
$edminboost_summary_id = isset( $edminboost_summary_id )
	? $edminboost_summary_id
	: 'edminboost-topbar-summary';
$edminboost_mapper_url = isset( $edminboost_mapper_url )
	? $edminboost_mapper_url
	: '';
$edminboost_item_count = count( $edminboost_top_bar_items );
?>
<div class="edminboost-topbar-summary" id="<?php echo esc_attr( $edminboost_summary_id ); ?>" data-item-count="<?php echo esc_attr( (string) $edminboost_item_count ); ?>">
	<p class="edminboost-topbar-summary__count">
		<?php
		printf(
			/* translators: %d: number of top bar items */
			esc_html( _n( '%d link in your top bar', '%d links in your top bar', $edminboost_item_count, EDMINBOOST_TEXT_DOMAIN ) ),
			(int) $edminboost_item_count
		);
		?>
	</p>

	<?php if ( empty( $edminboost_top_bar_items ) ) : ?>
		<p class="edminboost-topbar-summary__empty description">
			<?php esc_html_e( 'Choose a layout preset in step 1 to populate your top bar.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	<?php else : ?>
		<ul class="edminboost-topbar-summary__list" id="<?php echo esc_attr( $edminboost_summary_id ); ?>-list">
			<?php foreach ( $edminboost_top_bar_items as $edminboost_item ) : ?>
				<?php
				$edminboost_item_slug  = isset( $edminboost_item['slug'] ) ? $edminboost_item['slug'] : '';
				$edminboost_item_label = isset( $edminboost_item['label'] ) ? $edminboost_item['label'] : $edminboost_item_slug;
				$edminboost_item_icon  = isset( $edminboost_item['icon'] ) ? $edminboost_item['icon'] : 'dashicons-admin-generic';
				if ( '' === $edminboost_item_slug ) {
					continue;
				}
				?>
				<li class="edminboost-topbar-summary__item">
					<span class="dashicons <?php echo esc_attr( $edminboost_item_icon ); ?>" aria-hidden="true"></span>
					<span class="edminboost-topbar-summary__label"><?php echo esc_html( $edminboost_item_label ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<p class="edminboost-topbar-summary__actions">
		<a class="button" href="<?php echo esc_url( $edminboost_mapper_url ); ?>">
			<?php esc_html_e( 'Open full top bar editor', EDMINBOOST_TEXT_DOMAIN ); ?>
		</a>
	</p>
</div>
