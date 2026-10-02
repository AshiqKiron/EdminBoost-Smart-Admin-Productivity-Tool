<?php
/**
 * Top Bar item configuration panel (free tier: icon, label, anchor).
 *
 * @package EdminBoost
 *
 * @var array $edminboost_dashicon_options Dashicon picker options.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<aside
	class="edminboost-item-panel"
	id="edminboost-item-panel"
	aria-labelledby="edminboost-item-panel-heading"
	hidden
>
	<h2 id="edminboost-item-panel-heading"><?php esc_html_e( 'Item Configuration', 'edminboost-admin-customization' ); ?></h2>
	<p class="description" id="edminboost-item-config-subtitle"></p>

	<fieldset class="edminboost-fieldset">
		<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'item_icon' ); ?><?php esc_html_e( 'Icon', 'edminboost-admin-customization' ); ?></legend>
		<div class="edminboost-icon-picker" id="edminboost-icon-picker" role="listbox" aria-label="<?php esc_attr_e( 'Choose dashicon', 'edminboost-admin-customization' ); ?>">
			<?php foreach ( $edminboost_dashicon_options as $edminboost_dashicon ) : ?>
				<button
					type="button"
					class="edminboost-icon-picker__btn"
					data-icon="<?php echo esc_attr( $edminboost_dashicon ); ?>"
					role="option"
					aria-label="<?php echo esc_attr( $edminboost_dashicon ); ?>"
				>
					<span class="dashicons <?php echo esc_attr( $edminboost_dashicon ); ?>" aria-hidden="true"></span>
				</button>
			<?php endforeach; ?>
		</div>
	</fieldset>

	<p>
		<label for="edminboost-item-label"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_label' ); ?><?php esc_html_e( 'Label override', 'edminboost-admin-customization' ); ?></label>
		<input type="text" id="edminboost-item-label" class="regular-text" />
	</p>

	<p>
		<label for="edminboost-item-anchor"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_anchor' ); ?><?php esc_html_e( 'Anchor (optional)', 'edminboost-admin-customization' ); ?></label>
		<input type="text" id="edminboost-item-anchor" class="regular-text code" placeholder="<?php echo esc_attr( 'woocommerce_permalink_structure' ); ?>" />
		<span class="description"><?php esc_html_e( 'Scroll to a section on the page when the link is opened.', 'edminboost-admin-customization' ); ?></span>
	</p>

	<p>
		<button type="button" class="button" id="edminboost-item-panel-close">
			<?php esc_html_e( 'Close', 'edminboost-admin-customization' ); ?>
		</button>
		<button type="button" class="button button-link-delete" id="edminboost-item-panel-remove">
			<?php esc_html_e( 'Remove from top bar', 'edminboost-admin-customization' ); ?>
		</button>
	</p>
</aside>
