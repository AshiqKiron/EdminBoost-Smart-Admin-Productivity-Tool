<?php
/**
 * Admin dashboard page template.
 *
 * Purpose: Render feature status overview and getting-started guidance.
 *
 * @package EdminBoost
 *
 * @var array                    $settings Plugin settings.
 * @var EDMINBOOST_Feature_Base[] $features Registered features.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_features = 0;
foreach ( $features as $feature ) {
	if ( $feature->is_enabled() ) {
		++$active_features;
	}
}
?>
<div class="wrap edminboost-wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div class="edminboost-dashboard">
		<div class="edminboost-card">
			<h2><?php esc_html_e( 'Welcome to EdminBoost', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p>
				<?php esc_html_e( 'Your smart admin productivity toolkit for WordPress. Customize the dashboard, streamline workflows, and get more done in less time.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Status:', EDMINBOOST_TEXT_DOMAIN ); ?></strong>
				<span class="edminboost-status <?php echo esc_attr( ! empty( $settings['enabled'] ) ? 'is-active' : 'is-inactive' ); ?>">
					<?php
					echo ! empty( $settings['enabled'] )
						? esc_html__( 'Active', EDMINBOOST_TEXT_DOMAIN )
						: esc_html__( 'Inactive', EDMINBOOST_TEXT_DOMAIN );
					?>
				</span>
			</p>
			<p>
				<strong><?php esc_html_e( 'Active features:', EDMINBOOST_TEXT_DOMAIN ); ?></strong>
				<?php echo esc_html( (string) $active_features ); ?>
				/
				<?php echo esc_html( (string) count( $features ) ); ?>
			</p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_ONBOARDING ) ); ?>">
					<?php esc_html_e( 'Launch Command Center', EDMINBOOST_TEXT_DOMAIN ); ?>
				</a>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . '-settings' ) ); ?>">
					<?php esc_html_e( 'Open Settings', EDMINBOOST_TEXT_DOMAIN ); ?>
				</a>
			</p>
		</div>

		<div class="edminboost-card">
			<h2><?php esc_html_e( 'Features', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<ul class="edminboost-feature-list">
				<?php foreach ( $features as $feature ) : ?>
					<li>
						<span class="edminboost-status <?php echo esc_attr( $feature->is_enabled() ? 'is-active' : 'is-inactive' ); ?>">
							<?php echo $feature->is_enabled() ? esc_html__( 'On', EDMINBOOST_TEXT_DOMAIN ) : esc_html__( 'Off', EDMINBOOST_TEXT_DOMAIN ); ?>
						</span>
						<strong><?php echo esc_html( $feature->get_name() ); ?></strong>
						<span class="description"><?php echo esc_html( $feature->get_description() ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="edminboost-card">
			<h2><?php esc_html_e( 'Command Center', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p><?php esc_html_e( 'Configure your admin top bar with persona presets, drag-and-drop layout mapping, role assignments, and visual styling.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
			<ul class="edminboost-feature-list">
				<?php
				$cc_links = EDMINBOOST_Command_Center::get_nav_items();
				foreach ( $cc_links as $link ) :
					?>
					<li>
						<span class="edminboost-status is-active"><?php esc_html_e( 'Go', EDMINBOOST_TEXT_DOMAIN ); ?></span>
						<strong>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $link['slug'] ) ); ?>">
								<?php echo esc_html( $link['label'] ); ?>
							</a>
						</strong>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="edminboost-card">
			<h2><?php esc_html_e( 'Getting Started', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<ol class="edminboost-steps">
				<li><?php esc_html_e( 'Start with the Onboarding wizard to pick a persona preset.', EDMINBOOST_TEXT_DOMAIN ); ?></li>
				<li><?php esc_html_e( 'Map sidebar plugins to your top bar in the Layout Studio.', EDMINBOOST_TEXT_DOMAIN ); ?></li>
				<li><?php esc_html_e( 'Assign presets to roles and fine-tune behavior in the Command Center.', EDMINBOOST_TEXT_DOMAIN ); ?></li>
			</ol>
		</div>
	</div>
</div>
