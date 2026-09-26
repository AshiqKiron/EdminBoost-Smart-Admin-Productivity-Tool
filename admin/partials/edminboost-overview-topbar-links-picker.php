<?php
/**
 * Read-only top bar links dropdown for Dashboard overview card.
 *
 * @package EdminBoost
 *
 * @var array $edminboost_top_bar_items Configured top bar items.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_top_bar_count = count( $edminboost_top_bar_items );
$edminboost_summary_label = 0 === $edminboost_top_bar_count
	? __( 'No links configured', 'edminboost' )
	: sprintf(
		/* translators: %d: number of top bar links */
		_n( '%d link configured', '%d links configured', $edminboost_top_bar_count, 'edminboost' ),
		(int) $edminboost_top_bar_count
	);
?>
<div class="edminboost-overview-topbar-links-picker" id="edminboost-overview-topbar-links-picker">
	<button
		type="button"
		class="edminboost-overview-topbar-links-picker__toggle"
		id="edminboost_overview_topbar_links_toggle"
		aria-expanded="false"
		aria-controls="edminboost-overview-topbar-links-list"
		aria-haspopup="listbox"
	>
		<span class="edminboost-overview-topbar-links-picker__label" id="edminboost-overview-topbar-links-summary">
			<?php echo esc_html( $edminboost_summary_label ); ?>
		</span>
		<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
	</button>

	<ul
		class="edminboost-overview-topbar-links-picker__list"
		id="edminboost-overview-topbar-links-list"
		role="listbox"
		aria-label="<?php esc_attr_e( 'Configured top bar links', 'edminboost' ); ?>"
		hidden
	>
		<?php if ( empty( $edminboost_top_bar_items ) ) : ?>
			<li class="edminboost-overview-topbar-links-picker__empty" role="presentation">
				<?php esc_html_e( 'Add links in the Top Bar editor to see them here.', 'edminboost' ); ?>
			</li>
		<?php else : ?>
			<?php foreach ( $edminboost_top_bar_items as $edminboost_top_bar_item ) : ?>
				<?php
				$edminboost_item_label      = isset( $edminboost_top_bar_item['label'] ) ? $edminboost_top_bar_item['label'] : '';
				$edminboost_item_slug       = isset( $edminboost_top_bar_item['slug'] ) ? $edminboost_top_bar_item['slug'] : '';
				$edminboost_item_interaction = isset( $edminboost_top_bar_item['interaction'] ) ? $edminboost_top_bar_item['interaction'] : 'redirect';
				$edminboost_display_label   = '' !== $edminboost_item_label ? $edminboost_item_label : $edminboost_item_slug;
				$edminboost_interaction_label = 'drawer' === $edminboost_item_interaction
					? __( 'Opens in drawer', 'edminboost' )
					: __( 'Opens directly', 'edminboost' );
				?>
				<li
					class="edminboost-overview-topbar-links-picker__option"
					role="option"
					tabindex="-1"
					aria-selected="false"
				>
					<span class="edminboost-overview-topbar-links-picker__option-main">
						<span class="dashicons <?php echo esc_attr( isset( $edminboost_top_bar_item['icon'] ) ? $edminboost_top_bar_item['icon'] : 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
						<span class="edminboost-overview-topbar-links-picker__option-name"><?php echo esc_html( $edminboost_display_label ); ?></span>
					</span>
					<span class="edminboost-overview-topbar-links-picker__option-meta"><?php echo esc_html( $edminboost_interaction_label ); ?></span>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</div>
