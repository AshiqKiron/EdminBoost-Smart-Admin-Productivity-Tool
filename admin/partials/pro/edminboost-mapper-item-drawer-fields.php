<?php
/**
 * Top Bar item drawer interaction and badge fields (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var array $edminboost_badge_sources Badge source options.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div>
	<fieldset class="edminboost-fieldset">
		<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'item_interaction' ); ?><?php esc_html_e( 'Interaction', 'edminboost-admin-customization' ); ?></legend>
		<label class="edminboost-checkbox-row">
			<input type="radio" name="edminboost_item_interaction" value="redirect" checked />
			<?php esc_html_e( 'Direct redirect', 'edminboost-admin-customization' ); ?>
		</label>
		<label class="edminboost-checkbox-row">
			<input type="radio" name="edminboost_item_interaction" value="drawer" />
			<?php esc_html_e( 'AJAX slide-out drawer', 'edminboost-admin-customization' ); ?>
		</label>
	</fieldset>

	<p class="edminboost-item-drawer__preview" id="edminboost-drawer-preview-wrap" hidden>
		<button type="button" class="button button-secondary" id="edminboost-drawer-preview">
			<?php esc_html_e( 'Preview AJAX drawer', 'edminboost-admin-customization' ); ?>
		</button>
	</p>

	<p>
		<label for="edminboost-item-badge"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_badge_source' ); ?><?php esc_html_e( 'Live badge binding', 'edminboost-admin-customization' ); ?></label>
		<select id="edminboost-item-badge" class="regular-text">
			<?php foreach ( $edminboost_badge_sources as $edminboost_source_key => $edminboost_source_label ) : ?>
				<option value="<?php echo esc_attr( $edminboost_source_key ); ?>">
					<?php echo esc_html( $edminboost_source_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
</div>
