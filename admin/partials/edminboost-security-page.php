<?php
/**
 * Security settings page.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_option_name = EDMINBOOST_Settings::OPTION_NAME;
$edminboost_features                 = EDMINBOOST_Settings::get()['features'];
$edminboost_section                  = 'security';
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead"><?php esc_html_e( 'Harden public endpoints, comments, and login redirects.', 'edminboost-smart-admin-productivity-tool' ); ?></p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form edminboost-settings-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[enabled]" value="1" />
		<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-feature-fields.php'; ?>
		<?php
		$edminboost_save_label = __( 'Save security settings', 'edminboost-smart-admin-productivity-tool' );
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-form-actions.php';
		?>
	</form>
</div>
