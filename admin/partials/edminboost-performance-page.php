<?php
/**
 * Performance settings page.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name = EDMINBOOST_Settings::OPTION_NAME;
$settings    = EDMINBOOST_Settings::get();
$features    = $settings['features'];
$section     = 'performance';
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead"><?php esc_html_e( 'Reduce script overhead, control Heartbeat, and trim front-end assets.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form edminboost-settings-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />
		<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-feature-fields.php'; ?>
		<p class="submit"><?php submit_button( __( 'Save performance settings', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?></p>
	</form>
</div>
