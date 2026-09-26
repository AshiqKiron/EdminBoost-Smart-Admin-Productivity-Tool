<?php
/**
 * Admin bar declutter toggles.
 *
 * @package EdminBoost
 *
 * @var string $edminboost_cc_key   Form field prefix for behavior.
 * @var array  $edminboost_behavior Current behavior settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-declutter-heading">
	<h2 id="edminboost-declutter-heading"><?php esc_html_e( 'Admin bar cleanup', 'edminboost' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Hide native WordPress admin bar items you do not need.', 'edminboost' ); ?></p>

	<div class="edminboost-checkbox-grid">
		<label class="edminboost-checkbox-row" for="edminboost_hide_wp_logo">
			<input type="checkbox" id="edminboost_hide_wp_logo" name="<?php echo esc_attr( $edminboost_cc_key ); ?>[hide_wp_logo]" value="1" <?php checked( ! empty( $edminboost_behavior['hide_wp_logo'] ) ); ?> />
			<?php EDMINBOOST_Setting_Help::echo_icon( 'hide_wp_logo' ); ?>
			<?php esc_html_e( 'Hide WordPress logo', 'edminboost' ); ?>
		</label>
		<label class="edminboost-checkbox-row" for="edminboost_hide_update_counters">
			<input type="checkbox" id="edminboost_hide_update_counters" name="<?php echo esc_attr( $edminboost_cc_key ); ?>[hide_update_counters]" value="1" <?php checked( ! empty( $edminboost_behavior['hide_update_counters'] ) ); ?> />
			<?php EDMINBOOST_Setting_Help::echo_icon( 'hide_update_counters' ); ?>
			<?php esc_html_e( 'Hide update counters', 'edminboost' ); ?>
		</label>
		<label class="edminboost-checkbox-row" for="edminboost_hide_howdy">
			<input type="checkbox" id="edminboost_hide_howdy" name="<?php echo esc_attr( $edminboost_cc_key ); ?>[hide_howdy]" value="1" <?php checked( ! empty( $edminboost_behavior['hide_howdy'] ) ); ?> />
			<?php EDMINBOOST_Setting_Help::echo_icon( 'hide_howdy' ); ?>
			<?php esc_html_e( 'Hide Howdy / profile text', 'edminboost' ); ?>
		</label>
		<label class="edminboost-checkbox-row" for="edminboost_hide_comments">
			<input type="checkbox" id="edminboost_hide_comments" name="<?php echo esc_attr( $edminboost_cc_key ); ?>[hide_comments]" value="1" <?php checked( ! empty( $edminboost_behavior['hide_comments'] ) ); ?> />
			<?php EDMINBOOST_Setting_Help::echo_icon( 'hide_comments' ); ?>
			<?php esc_html_e( 'Hide comments shortcut', 'edminboost' ); ?>
		</label>
		<label class="edminboost-checkbox-row" for="edminboost_hide_new_content">
			<input type="checkbox" id="edminboost_hide_new_content" name="<?php echo esc_attr( $edminboost_cc_key ); ?>[hide_new_content]" value="1" <?php checked( ! empty( $edminboost_behavior['hide_new_content'] ) ); ?> />
			<?php EDMINBOOST_Setting_Help::echo_icon( 'hide_new_content' ); ?>
			<?php esc_html_e( 'Hide New content menu', 'edminboost' ); ?>
		</label>
		<label class="edminboost-checkbox-row" for="edminboost_hide_customize">
			<input type="checkbox" id="edminboost_hide_customize" name="<?php echo esc_attr( $edminboost_cc_key ); ?>[hide_customize]" value="1" <?php checked( ! empty( $edminboost_behavior['hide_customize'] ) ); ?> />
			<?php EDMINBOOST_Setting_Help::echo_icon( 'hide_customize' ); ?>
			<?php esc_html_e( 'Hide Customize link', 'edminboost' ); ?>
		</label>
	</div>

	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-declutter-preview.php'; ?>
</section>
