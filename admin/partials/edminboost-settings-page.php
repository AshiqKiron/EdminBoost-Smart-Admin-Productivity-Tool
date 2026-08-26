<?php
/**
 * Settings page — productivity feature toggles.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Enable individual productivity tools. All features apply only in the WordPress admin area.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-settings-form">
		<?php
		settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP );
		do_settings_sections( EDMINBOOST_Admin::PAGE_SLUG . '-settings' );
		submit_button( __( 'Save settings', EDMINBOOST_TEXT_DOMAIN ) );
		?>
	</form>
</div>
