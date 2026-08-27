<?php
/**
 * Dashboard hub page template.
 *
 * Purpose: First-run setup wizard or post-setup overview.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name    = EDMINBOOST_Settings::OPTION_NAME;
$is_setup       = EDMINBOOST_Command_Center::is_setup_complete( $cc_settings );
$theme          = EDMINBOOST_Theme::get_settings( $cc_settings );
$theme_key      = $option_name . '[command_center][theme]'; // Used by setup wizard theme partial.
$mapper_url     = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER );
$presets_url    = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRESETS );
$appearance_url = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_APPEARANCE );
?>
<div class="wrap edminboost-wrap edminboost-home-wrap<?php echo $is_setup ? '' : ' edminboost-home-wrap--wizard'; ?>">
	<?php if ( $is_setup ) : ?>
		<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>
	<?php endif; ?>

	<header class="edminboost-home-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php if ( ! $is_setup ) : ?>
			<p class="edminboost-home-hero__lead">
				<?php esc_html_e( 'Set up your Command Center in four quick steps.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
		<?php else : ?>
			<p class="edminboost-home-hero__lead">
				<?php esc_html_e( 'All your major admin tools in one place. Configure layout, appearance, and your top bar to build a workflow that works for you.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
		<?php endif; ?>
	</header>

	<div class="edminboost-dashboard">
		<?php if ( ! $is_setup ) : ?>
			<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-setup-wizard.php'; ?>
		<?php else : ?>
			<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-dashboard-overview.php'; ?>
		<?php endif; ?>
	</div>
</div>
