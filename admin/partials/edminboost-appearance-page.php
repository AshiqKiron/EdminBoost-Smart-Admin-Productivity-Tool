<?php
/**
 * Appearance settings — visual theme, panel behavior, declutter.
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
$behavior    = isset( $cc_settings['behavior'] ) && is_array( $cc_settings['behavior'] )
	? $cc_settings['behavior']
	: EDMINBOOST_Command_Center::get_defaults()['behavior'];
$cc_key      = $option_name . '[command_center][behavior]';
$theme       = EDMINBOOST_Theme::get_settings( $cc_settings );
$theme_key   = $option_name . '[command_center][theme]';
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Customize colors, fonts, slide-out panels, badges, and admin bar cleanup.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form" id="edminboost-appearance-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />

		<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-theme-settings.php'; ?>

		<section class="edminboost-card edminboost-cc-section edminboost-home-look" id="edminboost-appearance-look" aria-labelledby="edminboost-appearance-look-heading">
			<h2 id="edminboost-appearance-look-heading"><?php esc_html_e( 'Panel & badges', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Adjust slide-out panel style and notification badges. These settings do not change which links appear in your top bar.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
			<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-home-advanced-look.php'; ?>
		</section>

		<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-declutter-settings.php'; ?>

		<p class="submit">
			<?php submit_button( __( 'Save appearance', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
		</p>
	</form>
</div>
