<?php
/**
 * Menu Studio custom sidebar link panel (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var array $edminboost_menu_tree Discovered menu tree for parent select.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<details class="edminboost-custom-link" id="edminboost-menu-custom-link">
	<summary class="edminboost-custom-link__heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_path' ); ?><?php esc_html_e( 'Custom sidebar link', 'edminboost-admin-customization' ); ?></summary>
	<div class="edminboost-custom-link__body">
		<p class="description"><?php esc_html_e( 'Add a top-level or submenu link to the admin sidebar.', 'edminboost-admin-customization' ); ?></p>

		<p>
			<label for="edminboost-menu-custom-path"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_path' ); ?><?php esc_html_e( 'Admin path', 'edminboost-admin-customization' ); ?></label>
			<input type="text" id="edminboost-menu-custom-path" class="regular-text code" placeholder="<?php echo esc_attr( 'edit.php?post_type=page' ); ?>" autocomplete="off" />
		</p>

		<p>
			<label for="edminboost-menu-custom-label"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_label' ); ?><?php esc_html_e( 'Label', 'edminboost-admin-customization' ); ?></label>
			<input type="text" id="edminboost-menu-custom-label" class="regular-text" placeholder="<?php esc_attr_e( 'All Pages', 'edminboost-admin-customization' ); ?>" autocomplete="off" />
		</p>

		<p>
			<label for="edminboost-menu-custom-parent"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_parent' ); ?><?php esc_html_e( 'Parent menu (optional)', 'edminboost-admin-customization' ); ?></label>
			<select id="edminboost-menu-custom-parent">
				<option value=""><?php esc_html_e( 'Top level', 'edminboost-admin-customization' ); ?></option>
				<?php foreach ( $edminboost_menu_tree as $edminboost_menu_item ) : ?>
					<option value="<?php echo esc_attr( $edminboost_menu_item['slug'] ); ?>"><?php echo esc_html( $edminboost_menu_item['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="edminboost-custom-link__actions">
			<button type="button" class="button button-secondary" id="edminboost-menu-custom-add">
				<?php esc_html_e( 'Add to sidebar', 'edminboost-admin-customization' ); ?>
			</button>
		</p>

		<p class="edminboost-custom-link__error description" id="edminboost-menu-custom-error" hidden role="alert"></p>
	</div>
</details>
