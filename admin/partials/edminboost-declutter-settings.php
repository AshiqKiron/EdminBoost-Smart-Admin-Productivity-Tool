<?php
/**
 * Admin bar declutter toggles.
 *
 * @package EdminBoost
 *
 * @var string $cc_key   Form field prefix for behavior.
 * @var array  $behavior Current behavior settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-declutter-heading">
	<h2 id="edminboost-declutter-heading"><?php esc_html_e( 'Admin bar cleanup', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
	<p class="description"><?php esc_html_e( 'Hide native WordPress admin bar items you do not need.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

	<div class="edminboost-checkbox-grid">
		<label class="edminboost-checkbox-row" for="edminboost_hide_wp_logo">
			<input type="checkbox" id="edminboost_hide_wp_logo" name="<?php echo esc_attr( $cc_key ); ?>[hide_wp_logo]" value="1" <?php checked( ! empty( $behavior['hide_wp_logo'] ) ); ?> />
			<?php esc_html_e( 'Hide WordPress logo', EDMINBOOST_TEXT_DOMAIN ); ?>
		</label>
		<label class="edminboost-checkbox-row" for="edminboost_hide_update_counters">
			<input type="checkbox" id="edminboost_hide_update_counters" name="<?php echo esc_attr( $cc_key ); ?>[hide_update_counters]" value="1" <?php checked( ! empty( $behavior['hide_update_counters'] ) ); ?> />
			<?php esc_html_e( 'Hide update counters', EDMINBOOST_TEXT_DOMAIN ); ?>
		</label>
		<label class="edminboost-checkbox-row" for="edminboost_hide_howdy">
			<input type="checkbox" id="edminboost_hide_howdy" name="<?php echo esc_attr( $cc_key ); ?>[hide_howdy]" value="1" <?php checked( ! empty( $behavior['hide_howdy'] ) ); ?> />
			<?php esc_html_e( 'Hide Howdy / profile text', EDMINBOOST_TEXT_DOMAIN ); ?>
		</label>
		<label class="edminboost-checkbox-row" for="edminboost_hide_comments">
			<input type="checkbox" id="edminboost_hide_comments" name="<?php echo esc_attr( $cc_key ); ?>[hide_comments]" value="1" <?php checked( ! empty( $behavior['hide_comments'] ) ); ?> />
			<?php esc_html_e( 'Hide comments shortcut', EDMINBOOST_TEXT_DOMAIN ); ?>
		</label>
	</div>
</section>
