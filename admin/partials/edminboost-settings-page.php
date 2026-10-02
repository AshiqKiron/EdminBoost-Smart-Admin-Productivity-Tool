<?php
/**
 * Settings page — backup and import/export (Pro/Agency when pro admin package is active).
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
	</header>

	<?php
	if ( EDMINBOOST_Plan::is_direct_build() ) {
		do_action( 'edminboost_admin_extension', 'settings_backup' );
	}
	?>
</div>
