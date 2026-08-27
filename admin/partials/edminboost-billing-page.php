<?php
/**
 * Billing page — subscription plans.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plans        = EDMINBOOST_Command_Center::get_billing_plans();
$active_plan  = EDMINBOOST_Command_Center::get_active_billing_plan();
$active_label = isset( $plans[ $active_plan ] ) ? $plans[ $active_plan ]['name'] : __( 'Free', EDMINBOOST_TEXT_DOMAIN );
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Choose the plan that fits your workflow. Upgrade anytime as your sites grow.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	</header>

	<section class="edminboost-card edminboost-cc-section edminboost-billing-current">
		<h2><?php esc_html_e( 'Current plan', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
		<p class="edminboost-billing-current__plan">
			<span class="edminboost-billing-current__label"><?php echo esc_html( $active_label ); ?></span>
			<?php if ( isset( $plans[ $active_plan ] ) ) : ?>
				<span class="edminboost-billing-current__meta">
					<?php
					printf(
						/* translators: 1: price label, 2: site count label */
						esc_html__( '%1$s per year · %2$s', EDMINBOOST_TEXT_DOMAIN ),
						esc_html( $plans[ $active_plan ]['price_label'] ),
						esc_html( $plans[ $active_plan ]['sites_label'] )
					);
					?>
				</span>
			<?php endif; ?>
		</p>
	</section>

	<section class="edminboost-billing-plans" aria-label="<?php esc_attr_e( 'Available plans', EDMINBOOST_TEXT_DOMAIN ); ?>">
		<?php foreach ( $plans as $plan_id => $plan ) : ?>
			<?php
			$is_active   = $plan_id === $active_plan;
			$card_class  = 'edminboost-billing-plan';
			$card_class .= ! empty( $plan['featured'] ) ? ' is-featured' : '';
			$card_class .= $is_active ? ' is-current' : '';
			?>
			<article class="<?php echo esc_attr( $card_class ); ?>">
				<?php if ( ! empty( $plan['featured'] ) ) : ?>
					<p class="edminboost-billing-plan__badge"><?php esc_html_e( 'Most popular', EDMINBOOST_TEXT_DOMAIN ); ?></p>
				<?php endif; ?>

				<header class="edminboost-billing-plan__header">
					<h2 class="edminboost-billing-plan__name"><?php echo esc_html( $plan['name'] ); ?></h2>
					<p class="edminboost-billing-plan__price">
						<span class="edminboost-billing-plan__amount"><?php echo esc_html( $plan['price_label'] ); ?></span>
						<span class="edminboost-billing-plan__period"><?php esc_html_e( '/ year', EDMINBOOST_TEXT_DOMAIN ); ?></span>
					</p>
					<p class="edminboost-billing-plan__sites"><?php echo esc_html( $plan['sites_label'] ); ?></p>
					<p class="edminboost-billing-plan__desc"><?php echo esc_html( $plan['description'] ); ?></p>
				</header>

				<ul class="edminboost-billing-plan__features">
					<?php foreach ( $plan['features'] as $feature ) : ?>
						<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>

				<footer class="edminboost-billing-plan__footer">
					<?php if ( $is_active ) : ?>
						<button type="button" class="button button-secondary button-large" disabled aria-disabled="true">
							<?php esc_html_e( 'Current plan', EDMINBOOST_TEXT_DOMAIN ); ?>
						</button>
					<?php else : ?>
						<button type="button" class="button button-primary button-large edminboost-billing-plan__upgrade" data-edminboost-plan="<?php echo esc_attr( $plan_id ); ?>">
							<?php
							printf(
								/* translators: %s: plan name */
								esc_html__( 'Upgrade to %s', EDMINBOOST_TEXT_DOMAIN ),
								esc_html( $plan['name'] )
							);
							?>
						</button>
					<?php endif; ?>
				</footer>
			</article>
		<?php endforeach; ?>
	</section>

	<p class="edminboost-billing-note description">
		<?php esc_html_e( 'Paid plans are billed annually per site license. Checkout will be available in a future update.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</p>
</div>
