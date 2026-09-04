<?php
/**
 * Billing page — subscription plans and feature comparison.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plans           = EDMINBOOST_Command_Center::get_billing_plans();
$comparison_rows = EDMINBOOST_Command_Center::get_billing_comparison_rows();
$active_plan     = EDMINBOOST_Command_Center::get_active_billing_plan();
$active_label    = isset( $plans[ $active_plan ] ) ? $plans[ $active_plan ]['name'] : __( 'Free', EDMINBOOST_TEXT_DOMAIN );
$upgrade_url     = EDMINBOOST_Command_Center::get_upgrade_url();
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
			$is_active  = $plan_id === $active_plan;
			$card_class = 'edminboost-billing-plan';
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
					<?php
					$class = 'edminboost-billing-plan__upgrade';
					include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-billing-plan-cta.php';
					?>
				</footer>
			</article>
		<?php endforeach; ?>
	</section>

	<section class="edminboost-card edminboost-cc-section edminboost-billing-comparison">
		<h2><?php esc_html_e( 'Compare plans', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
		<p class="description edminboost-billing-comparison__lead">
			<?php esc_html_e( 'Scan down a column to see what each plan includes.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>

		<ul class="edminboost-billing-comparison__legend" aria-hidden="true">
			<li>
				<span class="edminboost-billing-comparison__status is-included"></span>
				<?php esc_html_e( 'Included', EDMINBOOST_TEXT_DOMAIN ); ?>
			</li>
			<li>
				<span class="edminboost-billing-comparison__status is-excluded"></span>
				<?php esc_html_e( 'Not included', EDMINBOOST_TEXT_DOMAIN ); ?>
			</li>
		</ul>

		<div class="edminboost-billing-comparison__wrap">
			<table class="edminboost-billing-comparison__table">
				<thead>
					<tr>
						<th scope="col" class="edminboost-billing-comparison__feature-col">
							<?php esc_html_e( 'Feature', EDMINBOOST_TEXT_DOMAIN ); ?>
						</th>
						<?php foreach ( array( 'free', 'pro', 'agency' ) as $plan_key ) : ?>
							<?php
							$plan       = $plans[ $plan_key ];
							$col_class  = 'edminboost-billing-comparison__plan-col is-plan-' . $plan_key;
							$col_class .= ! empty( $plan['featured'] ) ? ' is-featured' : '';
							$col_class .= $plan_key === $active_plan ? ' is-current' : '';
							?>
							<th scope="col" class="<?php echo esc_attr( $col_class ); ?>">
								<span class="edminboost-billing-comparison__plan-name"><?php echo esc_html( $plan['name'] ); ?></span>
								<span class="edminboost-billing-comparison__plan-price">
									<?php
									printf(
										/* translators: 1: price label, 2: site count label */
										esc_html__( '%1$s/yr · %2$s', EDMINBOOST_TEXT_DOMAIN ),
										esc_html( $plan['price_label'] ),
										esc_html( $plan['sites_label'] )
									);
									?>
								</span>
								<?php if ( $plan_key === $active_plan ) : ?>
									<span class="edminboost-billing-comparison__plan-current"><?php esc_html_e( 'Current', EDMINBOOST_TEXT_DOMAIN ); ?></span>
								<?php endif; ?>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $comparison_rows as $row ) : ?>
						<?php if ( 'heading' === $row['type'] ) : ?>
							<tr class="edminboost-billing-comparison__heading-row">
								<th scope="rowgroup" colspan="4"><?php echo esc_html( $row['label'] ); ?></th>
							</tr>
						<?php else : ?>
							<tr class="edminboost-billing-comparison__feature-row">
								<th scope="row" class="edminboost-billing-comparison__feature-col">
									<span class="edminboost-billing-comparison__feature-label"><?php echo esc_html( $row['label'] ); ?></span>
									<?php if ( ! empty( $row['detail'] ) ) : ?>
										<span class="edminboost-billing-comparison__feature-detail"><?php echo esc_html( $row['detail'] ); ?></span>
									<?php endif; ?>
								</th>
								<?php foreach ( array( 'free', 'pro', 'agency' ) as $plan_key ) : ?>
									<?php
									$cell       = $row[ $plan_key ];
									$cell_class = 'edminboost-billing-comparison__plan-col is-plan-' . $plan_key;
									$cell_class .= ! empty( $plans[ $plan_key ]['featured'] ) ? ' is-featured' : '';
									$cell_class .= $plan_key === $active_plan ? ' is-current' : '';
									?>
									<td class="<?php echo esc_attr( $cell_class ); ?>">
										<?php if ( is_bool( $cell ) ) : ?>
											<span class="edminboost-billing-comparison__status<?php echo $cell ? ' is-included' : ' is-excluded'; ?>"></span>
											<span class="screen-reader-text">
												<?php echo $cell ? esc_html__( 'Included', EDMINBOOST_TEXT_DOMAIN ) : esc_html__( 'Not included', EDMINBOOST_TEXT_DOMAIN ); ?>
											</span>
										<?php else : ?>
											<span class="edminboost-billing-comparison__value"><?php echo esc_html( (string) $cell ); ?></span>
										<?php endif; ?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr class="edminboost-billing-comparison__cta-row">
						<th scope="row" class="edminboost-billing-comparison__feature-col">
							<span class="screen-reader-text"><?php esc_html_e( 'Plan actions', EDMINBOOST_TEXT_DOMAIN ); ?></span>
						</th>
						<?php foreach ( array( 'free', 'pro', 'agency' ) as $plan_key ) : ?>
							<?php
							$plan       = $plans[ $plan_key ];
							$is_active  = $plan_key === $active_plan;
							$col_class  = 'edminboost-billing-comparison__plan-col is-plan-' . $plan_key;
							$col_class .= ! empty( $plan['featured'] ) ? ' is-featured' : '';
							$col_class .= $is_active ? ' is-current' : '';
							?>
							<td class="<?php echo esc_attr( $col_class ); ?>">
								<?php
								$class = 'edminboost-billing-comparison__cta';
								include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-billing-plan-cta.php';
								?>
							</td>
						<?php endforeach; ?>
					</tr>
				</tfoot>
			</table>
		</div>
	</section>

	<p class="edminboost-billing-note description">
		<?php esc_html_e( 'Paid plans are billed annually per site license. Upgrade opens the EdminBoost pricing page in a new tab.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</p>
</div>
