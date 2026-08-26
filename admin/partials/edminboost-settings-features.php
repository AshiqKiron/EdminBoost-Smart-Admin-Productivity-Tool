<?php
/**
 * Settings page feature fields partial.
 *
 * Purpose: Render checkbox/text fields for all productivity feature toggles.
 *
 * @package EdminBoost
 *
 * @var array $settings Plugin settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name    = EDMINBOOST_Settings::OPTION_NAME;
$features       = isset( $settings['features'] ) ? $settings['features'] : array();
$widget_labels  = EDMINBOOST_Dashboard::get_widget_labels();
$bar_labels     = EDMINBOOST_Admin_Bar::get_option_labels();
$footer_enabled = ! empty( $features['admin_footer']['enabled'] );
$footer_text    = isset( $features['admin_footer']['text'] ) ? $features['admin_footer']['text'] : '';
?>
<fieldset class="edminboost-fieldset">
	<legend class="screen-reader-text"><?php esc_html_e( 'Admin notices', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label for="edminboost_hide_admin_notices">
		<input
			type="checkbox"
			id="edminboost_hide_admin_notices"
			name="<?php echo esc_attr( $option_name ); ?>[features][hide_admin_notices]"
			value="1"
			<?php checked( ! empty( $features['hide_admin_notices'] ) ); ?>
		/>
		<?php esc_html_e( 'Hide routine admin notices on non-EdminBoost screens. Errors and warnings remain visible.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Dashboard widgets', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<p class="description"><?php esc_html_e( 'Remove selected default dashboard widgets.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	<?php foreach ( $widget_labels as $widget_key => $widget_label ) : ?>
		<label class="edminboost-checkbox-row" for="edminboost_widget_<?php echo esc_attr( $widget_key ); ?>">
			<input
				type="checkbox"
				id="edminboost_widget_<?php echo esc_attr( $widget_key ); ?>"
				name="<?php echo esc_attr( $option_name ); ?>[features][dashboard_widgets][<?php echo esc_attr( $widget_key ); ?>]"
				value="1"
				<?php checked( ! empty( $features['dashboard_widgets'][ $widget_key ] ) ); ?>
			/>
			<?php echo esc_html( $widget_label ); ?>
		</label>
	<?php endforeach; ?>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Admin footer', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label for="edminboost_admin_footer_enabled" class="edminboost-checkbox-row">
		<input
			type="checkbox"
			id="edminboost_admin_footer_enabled"
			name="<?php echo esc_attr( $option_name ); ?>[features][admin_footer][enabled]"
			value="1"
			<?php checked( $footer_enabled ); ?>
		/>
		<?php esc_html_e( 'Replace the default admin footer text.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<p>
		<label for="edminboost_admin_footer_text">
			<span class="screen-reader-text"><?php esc_html_e( 'Custom footer text', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			<input
				type="text"
				class="regular-text"
				id="edminboost_admin_footer_text"
				name="<?php echo esc_attr( $option_name ); ?>[features][admin_footer][text]"
				value="<?php echo esc_attr( $footer_text ); ?>"
				placeholder="<?php esc_attr_e( 'Custom footer text', EDMINBOOST_TEXT_DOMAIN ); ?>"
			/>
		</label>
	</p>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Performance', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label for="edminboost_disable_emojis" class="edminboost-checkbox-row">
		<input
			type="checkbox"
			id="edminboost_disable_emojis"
			name="<?php echo esc_attr( $option_name ); ?>[features][disable_emojis]"
			value="1"
			<?php checked( ! empty( $features['disable_emojis'] ) ); ?>
		/>
		<?php esc_html_e( 'Disable emoji scripts in the admin area.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Admin bar', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<p class="description"><?php esc_html_e( 'Hide selected items from the admin bar.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	<?php foreach ( $bar_labels as $bar_key => $bar_label ) : ?>
		<label class="edminboost-checkbox-row" for="edminboost_bar_<?php echo esc_attr( $bar_key ); ?>">
			<input
				type="checkbox"
				id="edminboost_bar_<?php echo esc_attr( $bar_key ); ?>"
				name="<?php echo esc_attr( $option_name ); ?>[features][admin_bar][<?php echo esc_attr( $bar_key ); ?>]"
				value="1"
				<?php checked( ! empty( $features['admin_bar'][ $bar_key ] ) ); ?>
			/>
			<?php echo esc_html( $bar_label ); ?>
		</label>
	<?php endforeach; ?>
</fieldset>
