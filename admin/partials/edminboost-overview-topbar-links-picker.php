<?php
/**
 * Read-only top bar links dropdown for Dashboard overview card.
 *
 * @package EdminBoost
 *
 * @var array $top_bar_items Configured top bar items.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$top_bar_count = count( $top_bar_items );
$summary_label = 0 === $top_bar_count
	? __( 'No links configured', EDMINBOOST_TEXT_DOMAIN )
	: sprintf(
		/* translators: %d: number of top bar links */
		_n( '%d link configured', '%d links configured', $top_bar_count, EDMINBOOST_TEXT_DOMAIN ),
		(int) $top_bar_count
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
			<?php echo esc_html( $summary_label ); ?>
		</span>
		<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
	</button>

	<ul
		class="edminboost-overview-topbar-links-picker__list"
		id="edminboost-overview-topbar-links-list"
		role="listbox"
		aria-label="<?php esc_attr_e( 'Configured top bar links', EDMINBOOST_TEXT_DOMAIN ); ?>"
		hidden
	>
		<?php if ( empty( $top_bar_items ) ) : ?>
			<li class="edminboost-overview-topbar-links-picker__empty" role="presentation">
				<?php esc_html_e( 'Add links in the Top Bar editor to see them here.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</li>
		<?php else : ?>
			<?php foreach ( $top_bar_items as $top_bar_item ) : ?>
				<?php
				$item_label      = isset( $top_bar_item['label'] ) ? $top_bar_item['label'] : '';
				$item_slug       = isset( $top_bar_item['slug'] ) ? $top_bar_item['slug'] : '';
				$item_interaction = isset( $top_bar_item['interaction'] ) ? $top_bar_item['interaction'] : 'redirect';
				$display_label   = '' !== $item_label ? $item_label : $item_slug;
				$interaction_label = 'drawer' === $item_interaction
					? __( 'Opens in drawer', EDMINBOOST_TEXT_DOMAIN )
					: __( 'Opens directly', EDMINBOOST_TEXT_DOMAIN );
				?>
				<li
					class="edminboost-overview-topbar-links-picker__option"
					role="option"
					tabindex="-1"
					aria-selected="false"
				>
					<span class="edminboost-overview-topbar-links-picker__option-main">
						<span class="dashicons <?php echo esc_attr( isset( $top_bar_item['icon'] ) ? $top_bar_item['icon'] : 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
						<span class="edminboost-overview-topbar-links-picker__option-name"><?php echo esc_html( $display_label ); ?></span>
					</span>
					<span class="edminboost-overview-topbar-links-picker__option-meta"><?php echo esc_html( $interaction_label ); ?></span>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</div>
