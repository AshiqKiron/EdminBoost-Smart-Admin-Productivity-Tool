<?php
/**
 * Role visibility matrix body cells for one role row (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var string $edminboost_option_name     Settings option name.
 * @var string $edminboost_role_key        Role key.
 * @var string $edminboost_role_label      Role label.
 * @var array  $edminboost_matrix_items    Matrix items.
 * @var array  $edminboost_role_visibility Role visibility map.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( $edminboost_matrix_items as $edminboost_item ) :
	$edminboost_item_slug       = isset( $edminboost_item['slug'] ) ? $edminboost_item['slug'] : '';
	if ( '' === $edminboost_item_slug ) {
		continue;
	}
	$edminboost_item_label      = isset( $edminboost_item['label'] ) ? $edminboost_item['label'] : $edminboost_item_slug;
	$edminboost_item_source     = isset( $edminboost_item['source'] ) ? $edminboost_item['source'] : 'top';
	$edminboost_is_submenu      = 'submenu' === $edminboost_item_source;
	$edminboost_parent_label    = isset( $edminboost_item['parent_label'] ) ? $edminboost_item['parent_label'] : '';
	$edminboost_can_access      = EDMINBOOST_Command_Center::role_can_access_menu_slug( $edminboost_role_key, $edminboost_item_slug );
	$edminboost_hidden_for_role = isset( $edminboost_role_visibility[ $edminboost_role_key ] ) && is_array( $edminboost_role_visibility[ $edminboost_role_key ] )
		? $edminboost_role_visibility[ $edminboost_role_key ]
		: array();
	$edminboost_has_saved_visibility = array_key_exists( $edminboost_role_key, $edminboost_role_visibility );
	$edminboost_is_hidden            = in_array( $edminboost_item_slug, $edminboost_hidden_for_role, true );
	$edminboost_protected_slugs      = EDMINBOOST_Command_Center::get_protected_slugs_for_role( $edminboost_role_key );
	$edminboost_is_protected         = ! $edminboost_is_submenu && in_array( $edminboost_item_slug, $edminboost_protected_slugs, true );
	$edminboost_field_id             = 'edminboost_vis_' . sanitize_html_class( $edminboost_role_key . '_' . $edminboost_item_slug );

	if ( $edminboost_is_protected ) {
		$edminboost_is_checked = true;
	} elseif ( ! $edminboost_can_access && ! $edminboost_has_saved_visibility ) {
		$edminboost_is_checked = false;
	} else {
		$edminboost_is_checked = ! $edminboost_is_hidden;
	}

	$edminboost_cell_classes = 'edminboost-role-matrix__check';
	if ( $edminboost_is_submenu ) {
		$edminboost_cell_classes .= ' is-submenu';
	}
	if ( $edminboost_is_protected ) {
		$edminboost_cell_classes .= ' is-protected';
	} elseif ( ! $edminboost_can_access ) {
		$edminboost_cell_classes .= ' is-capability-restricted';
	}
	?>
	<td
		class="<?php echo esc_attr( $edminboost_cell_classes ); ?>"
		data-item-slug="<?php echo esc_attr( $edminboost_item_slug ); ?>"
	>
		<label class="edminboost-role-matrix__check-label" for="<?php echo esc_attr( $edminboost_field_id ); ?>">
			<span class="screen-reader-text">
				<?php
				if ( $edminboost_is_submenu && '' !== $edminboost_parent_label ) {
					echo esc_html(
						sprintf(
							/* translators: 1: role name, 2: parent menu label, 3: submenu label */
							__( 'Show %2$s › %3$s for %1$s', 'edminboost-admin-customization' ),
							$edminboost_role_label,
							$edminboost_parent_label,
							$edminboost_item_label
						)
					);
				} else {
					echo esc_html(
						sprintf(
							/* translators: 1: role name, 2: item label */
							__( 'Show %2$s for %1$s', 'edminboost-admin-customization' ),
							$edminboost_role_label,
							$edminboost_item_label
						)
					);
				}
				?>
			</span>
			<input
				type="checkbox"
				class="edminboost-role-visibility-checkbox"
				id="<?php echo esc_attr( $edminboost_field_id ); ?>"
				name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][role_visibility][<?php echo esc_attr( $edminboost_role_key ); ?>][]"
				value="<?php echo esc_attr( $edminboost_item_slug ); ?>"
				data-item-slug="<?php echo esc_attr( $edminboost_item_slug ); ?>"
				<?php checked( $edminboost_is_checked ); ?>
				<?php disabled( $edminboost_is_protected ); ?>
			/>
		</label>
	</td>
	<?php
endforeach;
