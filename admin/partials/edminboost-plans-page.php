<?php
/**
 * Static Billing (Plans) page for the WordPress.org build.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Static catalog only — no premium billing classes, license checks, or upgrade filters.
$edminboost_product_url   = 'https://asphaltthemes.com/edminboost-white-label-admin-menu-editor-top-bar-customizer-for-wordpress/';
$edminboost_active_plan   = 'free';
$edminboost_plans         = array(
	'free'   => array(
		'name'        => __( 'Free', 'edminboost-admin-customization' ),
		'price_label' => __( '$0', 'edminboost-admin-customization' ),
		'sites_label' => __( 'Unlimited sites', 'edminboost-admin-customization' ),
		'description' => __( 'Core Command Center tools on unlimited WordPress sites.', 'edminboost-admin-customization' ),
		'features'    => array(
			__( 'Dashboard setup wizard', 'edminboost-admin-customization' ),
			__( 'Top bar builder (redirect links)', 'edminboost-admin-customization' ),
			__( 'Friend\'s Website and Family Member\'s Site layout presets', 'edminboost-admin-customization' ),
			__( 'By-role layout presets', 'edminboost-admin-customization' ),
			__( 'One saved custom layout preset', 'edminboost-admin-customization' ),
			__( 'Default, Midnight, and Terminal theme presets', 'edminboost-admin-customization' ),
			__( 'Menu Studio sidebar reorder and hide', 'edminboost-admin-customization' ),
			__( 'Productivity, security, and performance tools', 'edminboost-admin-customization' ),
		),
		'featured'    => false,
	),
	'pro'    => array(
		'name'        => __( 'Pro', 'edminboost-admin-customization' ),
		'price_label' => __( '$49', 'edminboost-admin-customization' ),
		'sites_label' => __( '1 site', 'edminboost-admin-customization' ),
		'description' => __( 'Premium admin customization for one production site.', 'edminboost-admin-customization' ),
		'features'    => array(
			__( 'Everything in Free', 'edminboost-admin-customization' ),
			__( 'Additional by use case layout presets', 'edminboost-admin-customization' ),
			__( 'Unlimited saved custom layouts', 'edminboost-admin-customization' ),
			__( 'Role-based menu visibility matrix', 'edminboost-admin-customization' ),
			__( 'Additional visual theme skins', 'edminboost-admin-customization' ),
			__( 'Scheduled dark mode', 'edminboost-admin-customization' ),
			__( 'Slide-out drawer panels and live badge counters', 'edminboost-admin-customization' ),
			__( 'Full-screen and custom drawer widths, badge style, animation speed, glassmorphism', 'edminboost-admin-customization' ),
			__( 'Role-based login and logout redirects', 'edminboost-admin-customization' ),
			__( 'Custom sidebar links and icon/text display modes', 'edminboost-admin-customization' ),
			__( 'White-label branding', 'edminboost-admin-customization' ),
			__( 'Settings export and import', 'edminboost-admin-customization' ),
		),
		'featured'    => true,
	),
	'agency' => array(
		'name'        => __( 'Agency', 'edminboost-admin-customization' ),
		'price_label' => __( '$99', 'edminboost-admin-customization' ),
		'sites_label' => __( '10 sites', 'edminboost-admin-customization' ),
		'description' => __( 'Deploy EdminBoost across a client portfolio.', 'edminboost-admin-customization' ),
		'features'    => array(
			__( 'Everything in Pro', 'edminboost-admin-customization' ),
			__( '10 site license pack', 'edminboost-admin-customization' ),
		),
		'featured'    => false,
	),
);
$edminboost_comparison_rows = array(
	array(
		'type'  => 'heading',
		'label' => __( 'Plan basics', 'edminboost-admin-customization' ),
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Annual price', 'edminboost-admin-customization' ),
		'free'   => __( '$0', 'edminboost-admin-customization' ),
		'pro'    => __( '$49', 'edminboost-admin-customization' ),
		'agency' => __( '$99', 'edminboost-admin-customization' ),
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Site license', 'edminboost-admin-customization' ),
		'free'   => __( 'Unlimited', 'edminboost-admin-customization' ),
		'pro'    => __( '1 site', 'edminboost-admin-customization' ),
		'agency' => __( '10 sites', 'edminboost-admin-customization' ),
	),
	array(
		'type'  => 'heading',
		'label' => __( 'Layout presets', 'edminboost-admin-customization' ),
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Friend & Family presets', 'edminboost-admin-customization' ),
		'detail' => __( 'Friend\'s Website and Family Member\'s Site layouts', 'edminboost-admin-customization' ),
		'free'   => true,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'By-role layout presets', 'edminboost-admin-customization' ),
		'free'   => true,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Use-case layout presets', 'edminboost-admin-customization' ),
		'detail' => __( 'Additional scenario presets beyond Friend & Family', 'edminboost-admin-customization' ),
		'free'   => false,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Saved custom layouts', 'edminboost-admin-customization' ),
		'free'   => __( '1', 'edminboost-admin-customization' ),
		'pro'    => __( 'Unlimited', 'edminboost-admin-customization' ),
		'agency' => __( 'Unlimited', 'edminboost-admin-customization' ),
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Role visibility matrix', 'edminboost-admin-customization' ),
		'free'   => false,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'  => 'heading',
		'label' => __( 'Top Bar', 'edminboost-admin-customization' ),
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Top bar (redirect links)', 'edminboost-admin-customization' ),
		'free'   => true,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Slide-out drawer panels', 'edminboost-admin-customization' ),
		'free'   => false,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Live badge counters', 'edminboost-admin-customization' ),
		'detail' => __( 'Local counter sources (orders, comments, updates, forms, and more)', 'edminboost-admin-customization' ),
		'free'   => false,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'  => 'heading',
		'label' => __( 'Menu Studio', 'edminboost-admin-customization' ),
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Sidebar reorder and hide', 'edminboost-admin-customization' ),
		'free'   => true,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Custom sidebar links', 'edminboost-admin-customization' ),
		'free'   => false,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'  => 'heading',
		'label' => __( 'Theme (Appearance)', 'edminboost-admin-customization' ),
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Core theme presets', 'edminboost-admin-customization' ),
		'detail' => __( 'Default, Midnight, and Terminal', 'edminboost-admin-customization' ),
		'free'   => true,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Premium theme skins', 'edminboost-admin-customization' ),
		'free'   => false,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Scheduled dark mode', 'edminboost-admin-customization' ),
		'free'   => false,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'  => 'heading',
		'label' => __( 'Tools and branding', 'edminboost-admin-customization' ),
	),
	array(
		'type'   => 'row',
		'label'  => __( 'Utility tool modules', 'edminboost-admin-customization' ),
		'detail' => __( 'Productivity, security, and performance tools', 'edminboost-admin-customization' ),
		'free'   => true,
		'pro'    => true,
		'agency' => true,
	),
	array(
		'type'   => 'row',
		'label'  => __( 'White-label branding', 'edminboost-admin-customization' ),
		'free'   => false,
		'pro'    => true,
		'agency' => true,
	),
);
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Choose the plan that fits your workflow. Paid plans open the EdminBoost pricing page in a new tab.', 'edminboost-admin-customization' ); ?>
		</p>
	</header>

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
					<p class="edminboost-billing-plan__sites">
						<?php
						printf(
							/* translators: 1: price label, 2: site count label */
							esc_html__( '%1$s per year · %2$s', 'edminboost-admin-customization' ),
							esc_html( $edminboost_plan['price_label'] ),
							esc_html( $edminboost_plan['sites_label'] )
						);
						?>
					</p>
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
					include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-plans-cta.php';
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
											<span class="<?php echo esc_attr( 'edminboost-billing-comparison__status' . ( $edminboost_cell ? ' is-included' : ' is-excluded' ) ); ?>"></span>
											<span class="screen-reader-text">
												<?php
												if ( $edminboost_cell ) {
													esc_html_e( 'Included', 'edminboost-admin-customization' );
												} else {
													esc_html_e( 'Not included', 'edminboost-admin-customization' );
												}
												?>
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
								include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-plans-cta.php';
								?>
							</td>
						<?php endforeach; ?>
					</tr>
				</tfoot>
			</table>
		</div>
	</section>

	<p class="edminboost-billing-note description">
		<?php esc_html_e( 'Paid plans are billed annually per site license. Purchase links open the EdminBoost pricing page in a new tab.', 'edminboost-admin-customization' ); ?>
	</p>
</div>
