<?php
/**
 * Menu Studio display mode control (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var string $edminboost_ms_key Menu Studio form field prefix.
 * @var array  $edminboost_menu_studio Menu Studio settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="edminboost-menu-layout-row">
	<label for="edminboost_menu_display_mode"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_display_mode' ); ?><?php esc_html_e( 'Menu item display', 'edminboost-admin-customization' ); ?></label>
	<select id="edminboost_menu_display_mode" class="edminboost-menu-layout-select" name="<?php echo esc_attr( $edminboost_ms_key ); ?>[display_mode]">
		<option value="both" <?php selected( $edminboost_menu_studio['display_mode'] ?? 'both', 'both' ); ?>><?php esc_html_e( 'Icon and text', 'edminboost-admin-customization' ); ?></option>
		<option value="icon" <?php selected( $edminboost_menu_studio['display_mode'] ?? 'both', 'icon' ); ?>><?php esc_html_e( 'Icon only', 'edminboost-admin-customization' ); ?></option>
		<option value="text" <?php selected( $edminboost_menu_studio['display_mode'] ?? 'both', 'text' ); ?>><?php esc_html_e( 'Text only', 'edminboost-admin-customization' ); ?></option>
	</select>
</div>
