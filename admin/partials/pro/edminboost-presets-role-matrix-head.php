<?php
/**
 * Role visibility matrix header columns (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var array $edminboost_matrix_items Matrix menu items.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( $edminboost_matrix_items as $edminboost_item ) :
	$edminboost_item_slug      = isset( $edminboost_item['slug'] ) ? $edminboost_item['slug'] : '';
	if ( '' === $edminboost_item_slug ) {
		continue;
	}
	$edminboost_item_label     = isset( $edminboost_item['label'] ) ? $edminboost_item['label'] : $edminboost_item_slug;
	$edminboost_item_source    = isset( $edminboost_item['source'] ) ? $edminboost_item['source'] : 'top';
	$edminboost_is_submenu     = 'submenu' === $edminboost_item_source;
	$edminboost_parent_label   = isset( $edminboost_item['parent_label'] ) ? $edminboost_item['parent_label'] : '';
	$edminboost_column_classes = 'edminboost-role-matrix__menu-col';
	if ( $edminboost_is_submenu ) {
		$edminboost_column_classes .= ' is-submenu';
	}
	?>
	<th scope="col" class="<?php echo esc_attr( $edminboost_column_classes ); ?>">
		<span class="edminboost-role-matrix__menu-heading">
			<?php if ( ! $edminboost_is_submenu ) : ?>
				<span class="dashicons <?php echo esc_attr( isset( $edminboost_item['icon'] ) ? $edminboost_item['icon'] : 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
			<?php endif; ?>
			<span class="edminboost-role-matrix__menu-label">
				<?php if ( $edminboost_is_submenu && '' !== $edminboost_parent_label ) : ?>
					<span class="edminboost-role-matrix__menu-parent"><?php echo esc_html( $edminboost_parent_label ); ?></span>
				<?php endif; ?>
				<span class="edminboost-role-matrix__menu-name"><?php echo esc_html( $edminboost_item_label ); ?></span>
			</span>
		</span>
	</th>
	<?php
endforeach;
