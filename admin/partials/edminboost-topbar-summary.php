<?php
/**
 * Read-only top bar layout summary.
 *
 * @package EdminBoost
 *
 * @var array  $top_bar_items Top bar items to display.
 * @var string $mapper_url    URL to the full Top Bar editor.
 * @var string $summary_id    Optional root element id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summary_id    = isset( $summary_id ) ? $summary_id : 'edminboost-topbar-summary';
$top_bar_items = isset( $top_bar_items ) && is_array( $top_bar_items ) ? $top_bar_items : array();
$item_count    = count( $top_bar_items );
?>
<div class="edminboost-topbar-summary" id="<?php echo esc_attr( $summary_id ); ?>" data-item-count="<?php echo esc_attr( (string) $item_count ); ?>">
	<p class="edminboost-topbar-summary__count">
		<?php
		printf(
			/* translators: %d: number of top bar items */
			esc_html( _n( '%d link in your top bar', '%d links in your top bar', $item_count, EDMINBOOST_TEXT_DOMAIN ) ),
			(int) $item_count
		);
		?>
	</p>

	<?php if ( empty( $top_bar_items ) ) : ?>
		<p class="edminboost-topbar-summary__empty description">
			<?php esc_html_e( 'Choose a layout preset in step 1 to populate your top bar.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	<?php else : ?>
		<ul class="edminboost-topbar-summary__list" id="<?php echo esc_attr( $summary_id ); ?>-list">
			<?php foreach ( $top_bar_items as $item ) : ?>
				<?php
				$item_slug  = isset( $item['slug'] ) ? $item['slug'] : '';
				$item_label = isset( $item['label'] ) ? $item['label'] : $item_slug;
				$item_icon  = isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic';
				if ( '' === $item_slug ) {
					continue;
				}
				?>
				<li class="edminboost-topbar-summary__item">
					<span class="dashicons <?php echo esc_attr( $item_icon ); ?>" aria-hidden="true"></span>
					<span class="edminboost-topbar-summary__label"><?php echo esc_html( $item_label ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<p class="edminboost-topbar-summary__actions">
		<a class="button" href="<?php echo esc_url( $mapper_url ); ?>">
			<?php esc_html_e( 'Open full top bar editor', EDMINBOOST_TEXT_DOMAIN ); ?>
		</a>
	</p>
</div>
