<?php
/**
 * Upgrade prompt for pro-gated sections.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! EDMINBOOST_Pro::shows_pro_settings_ui() || EDMINBOOST_Pro::is_active() ) {
	return;
}
?>
<p class="edminboost-pro-upgrade">
	<?php EDMINBOOST_Pro::render_badge(); ?>
	<a href="<?php echo esc_url( EDMINBOOST_Pro::get_billing_url() ); ?>">
		<?php esc_html_e( 'Upgrade to Pro to unlock this feature.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</a>
</p>
