<?php
/**
 * Admin dashboard page template.
 *
 * @package EdminBoost
 *
 * @var array $settings Plugin settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap edminboost-wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div class="edminboost-dashboard">
		<div class="edminboost-card">
			<h2><?php esc_html_e( 'Welcome to Edmin Boost', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p>
				<?php esc_html_e( 'Your smart admin productivity toolkit for WordPress. Customize the dashboard, streamline workflows, and get more done in less time.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Status:', EDMINBOOST_TEXT_DOMAIN ); ?></strong>
				<span class="edminboost-status <?php echo ! empty( $settings['enabled'] ) ? 'is-active' : 'is-inactive'; ?>">
					<?php
					echo ! empty( $settings['enabled'] )
						? esc_html__( 'Active', EDMINBOOST_TEXT_DOMAIN )
						: esc_html__( 'Inactive', EDMINBOOST_TEXT_DOMAIN );
					?>
				</span>
			</p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . '-settings' ) ); ?>">
					<?php esc_html_e( 'Open Settings', EDMINBOOST_TEXT_DOMAIN ); ?>
				</a>
			</p>
		</div>

		<div class="edminboost-card">
			<h2><?php esc_html_e( 'Getting Started', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<ol class="edminboost-steps">
				<li><?php esc_html_e( 'Enable Edmin Boost from the Settings page.', EDMINBOOST_TEXT_DOMAIN ); ?></li>
				<li><?php esc_html_e( 'Configure admin productivity features as they are added.', EDMINBOOST_TEXT_DOMAIN ); ?></li>
				<li><?php esc_html_e( 'Enjoy a faster, cleaner WordPress admin experience.', EDMINBOOST_TEXT_DOMAIN ); ?></li>
			</ol>
		</div>
	</div>
</div>
