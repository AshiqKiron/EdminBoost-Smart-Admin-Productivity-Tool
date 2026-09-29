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

$edminboost_plans           = EDMINBOOST_Command_Center::get_billing_plans();
$edminboost_comparison_rows = EDMINBOOST_Command_Center::get_billing_comparison_rows();
$edminboost_active_plan     = EDMINBOOST_Command_Center::get_active_billing_plan();
$edminboost_active_label    = isset( $edminboost_plans[ $edminboost_active_plan ] ) ? $edminboost_plans[ $edminboost_active_plan ]['name'] : __( 'Free', 'edminboost-admin-customization' );
$edminboost_upgrade_url     = EDMINBOOST_Command_Center::get_upgrade_url();
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Choose the plan that fits your workflow. Upgrade anytime as your sites grow.', 'edminboost-admin-customization' ); ?>
		</p>
	</header>

	<section class="edminboost-card edminboost-cc-section edminboost-billing-current">
		<h2><?php esc_html_e( 'Current plan', 'edminboost-admin-customization' ); ?></h2>
		<p class="edminboost-billing-current__plan">
			<span class="edminboost-billing-current__label"><?php echo esc_html( $edminboost_active_label ); ?></span>
			<?php if ( isset( $edminboost_plans[ $edminboost_active_plan ] ) ) : ?>
				<span class="edminboost-billing-current__meta">
					<?php
					printf(
						/* translators: 1: price label, 2: site count label */
						esc_html__( '%1$s per year · %2$s', 'edminboost-admin-customization' ),
						esc_html( $edminboost_plans[ $edminboost_active_plan ]['price_label'] ),
						esc_html( $edminboost_plans[ $edminboost_active_plan ]['sites_label'] )
					);
					?>
				</span>
			<?php endif; ?>
		</p>
	</section>

	<section class="edminboost-billing-plans" aria-label="<?php esc_attr_e( 'Available plans', 'edminboost-admin-customization' ); ?>">
		<?php foreach ( $edminboost_plans as $edminboost_plan_id => $edminboost_plan ) : ?>
			<?php
			$edminboost_is_active  = $edminboost_plan_id === $edminboost_active_plan;
			$edminboost_card_class = 'edminboost-billing-plan';
			$edminboost_card_class .= ! empty( $edminboost_plan['featured'] ) ? ' is-featured' : '';
			$edminboost_card_class .= $edminboost_is_active ? ' is-current' : '';
			?>
			<article class="<?php echo esc_attr( $edminboost_card_class ); ?>">
				<?php if ( ! empty( $edminboost_plan['featured'] ) ) : ?>
					<p class="edminboost-billing-plan__badge"><?php esc_html_e( 'Most popular', 'edminboost-admin-customization' ); ?></p>
				<?php endif; ?>

				<header class="edminboost-billing-plan__header">
					<h2 class="edminboost-billing-plan__name"><?php echo esc_html( $edminboost_plan['name'] ); ?></h2>
					<p class="edminboost-billing-plan__price">
						<span class="edminboost-billing-plan__amount"><?php echo esc_html( $edminboost_plan['price_label'] ); ?></span>
						<span class="edminboost-billing-plan__period"><?php esc_html_e( '/ year', 'edminboost-admin-customization' ); ?></span>
					</p>
					<p class="edminboost-billing-plan__sites"><?php echo esc_html( $edminboost_plan['sites_label'] ); ?></p>
					<p class="edminboost-billing-plan__desc"><?php echo esc_html( $edminboost_plan['description'] ); ?></p>
				</header>

				<ul class="edminboost-billing-plan__features">
					<?php foreach ( $edminboost_plan['features'] as $edminboost_feature ) : ?>
						<li><?php echo esc_html( $edminboost_feature ); ?></li>
					<?php endforeach; ?>
				</ul>

				<footer class="edminboost-billing-plan__footer">
					<?php
					$edminboost_class = 'edminboost-billing-plan__upgrade';
					include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-billing-plan-cta.php';
					?>
				</footer>
			</article>
		<?php endforeach; ?>
	</section>

	<section class="edminboost-card edminboost-cc-section edminboost-billing-comparison">
		<h2><?php esc_html_e( 'Compare plans', 'edminboost-admin-customization' ); ?></h2>
		<p class="description edminboost-billing-comparison__lead">
			<?php esc_html_e( 'Scan down a column to see what each plan includes.', 'edminboost-admin-customization' ); ?>
		</p>

		<ul class="edminboost-billing-comparison__legend" aria-hidden="true">
			<li>
				<span class="edminboost-billing-comparison__status is-included"></span>
				<?php esc_html_e( 'Included', 'edminboost-admin-customization' ); ?>
			</li>
			<li>
				<span class="edminboost-billing-comparison__status is-excluded"></span>
				<?php esc_html_e( 'Not included', 'edminboost-admin-customization' ); ?>
			</li>
		</ul>

		<div class="edminboost-billing-comparison__wrap">
			<table class="edminboost-billing-comparison__table">
				<thead>
					<tr>
						<th scope="col" class="edminboost-billing-comparison__feature-col">
							<?php esc_html_e( 'Feature', 'edminboost-admin-customization' ); ?>
						</th>
						<?php foreach ( array( 'free', 'pro', 'agency' ) as $edminboost_plan_key ) : ?>
							<?php
							$edminboost_plan       = $edminboost_plans[ $edminboost_plan_key ];
							$edminboost_col_class  = 'edminboost-billing-comparison__plan-col is-plan-' . $edminboost_plan_key;
							$edminboost_col_class .= ! empty( $edminboost_plan['featured'] ) ? ' is-featured' : '';
							$edminboost_col_class .= $edminboost_plan_key === $edminboost_active_plan ? ' is-current' : '';
							?>
							<th scope="col" class="<?php echo esc_attr( $edminboost_col_class ); ?>">
								<span class="edminboost-billing-comparison__plan-name"><?php echo esc_html( $edminboost_plan['name'] ); ?></span>
								<span class="edminboost-billing-comparison__plan-price">
									<?php
									printf(
										/* translators: 1: price label, 2: site count label */
										esc_html__( '%1$s/yr · %2$s', 'edminboost-admin-customization' ),
										esc_html( $edminboost_plan['price_label'] ),
										esc_html( $edminboost_plan['sites_label'] )
									);
									?>
								</span>
								<?php if ( $edminboost_plan_key === $edminboost_active_plan ) : ?>
									<span class="edminboost-billing-comparison__plan-current"><?php esc_html_e( 'Current', 'edminboost-admin-customization' ); ?></span>
								<?php endif; ?>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $edminboost_comparison_rows as $edminboost_row ) : ?>
						<?php if ( 'heading' === $edminboost_row['type'] ) : ?>
							<tr class="edminboost-billing-comparison__heading-row">
								<th scope="rowgroup" colspan="4"><?php echo esc_html( $edminboost_row['label'] ); ?></th>
							</tr>
						<?php else : ?>
							<tr class="edminboost-billing-comparison__feature-row">
								<th scope="row" class="edminboost-billing-comparison__feature-col">
									<span class="edminboost-billing-comparison__feature-label"><?php echo esc_html( $edminboost_row['label'] ); ?></span>
									<?php if ( ! empty( $edminboost_row['detail'] ) ) : ?>
										<span class="edminboost-billing-comparison__feature-detail"><?php echo esc_html( $edminboost_row['detail'] ); ?></span>
									<?php endif; ?>
								</th>
								<?php foreach ( array( 'free', 'pro', 'agency' ) as $edminboost_plan_key ) : ?>
									<?php
									$edminboost_cell       = $edminboost_row[ $edminboost_plan_key ];
									$edminboost_cell_class = 'edminboost-billing-comparison__plan-col is-plan-' . $edminboost_plan_key;
									$edminboost_cell_class .= ! empty( $edminboost_plans[ $edminboost_plan_key ]['featured'] ) ? ' is-featured' : '';
									$edminboost_cell_class .= $edminboost_plan_key === $edminboost_active_plan ? ' is-current' : '';
									?>
									<td class="<?php echo esc_attr( $edminboost_cell_class ); ?>">
										<?php if ( is_bool( $edminboost_cell ) ) : ?>
											<span class="edminboost-billing-comparison__status<?php echo $edminboost_cell ? ' is-included' : ' is-excluded'; ?>"></span>
											<span class="screen-reader-text">
												<?php echo $edminboost_cell ? esc_html__( 'Included', 'edminboost-admin-customization' ) : esc_html__( 'Not included', 'edminboost-admin-customization' ); ?>
											</span>
										<?php else : ?>
											<span class="edminboost-billing-comparison__value"><?php echo esc_html( (string) $edminboost_cell ); ?></span>
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
							<span class="screen-reader-text"><?php esc_html_e( 'Plan actions', 'edminboost-admin-customization' ); ?></span>
						</th>
						<?php foreach ( array( 'free', 'pro', 'agency' ) as $edminboost_plan_key ) : ?>
							<?php
							$edminboost_plan       = $edminboost_plans[ $edminboost_plan_key ];
							$edminboost_is_active  = $edminboost_plan_key === $edminboost_active_plan;
							$edminboost_col_class  = 'edminboost-billing-comparison__plan-col is-plan-' . $edminboost_plan_key;
							$edminboost_col_class .= ! empty( $edminboost_plan['featured'] ) ? ' is-featured' : '';
							$edminboost_col_class .= $edminboost_is_active ? ' is-current' : '';
							?>
							<td class="<?php echo esc_attr( $edminboost_col_class ); ?>">
								<?php
								$edminboost_class = 'edminboost-billing-comparison__cta';
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
		<?php esc_html_e( 'Paid plans are billed annually per site license. Upgrade opens the EdminBoost pricing page in a new tab.', 'edminboost-admin-customization' ); ?>
	</p>
</div>
