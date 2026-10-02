<?php
/**
 * Billing plan call-to-action button (current plan or upgrade link).
 *
 * @package EdminBoost
 *
 * @var bool   $edminboost_is_active   Whether this plan is the active plan.
 * @var array  $edminboost_plan        Plan definition (requires `name`).
 * @var string $edminboost_upgrade_url Upgrade URL for non-active plans.
 * @var string $edminboost_class       Optional extra class(es) on the CTA element.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_cta_class = 'button button-large';
$edminboost_cta_class .= $edminboost_is_active ? ' button-secondary' : ' button-primary';
if ( ! empty( $edminboost_class ) ) {
	$edminboost_cta_class .= ' ' . $edminboost_class;
}
?>
<?php if ( $edminboost_is_active ) : ?>
	<button type="button" class="<?php echo esc_attr( $edminboost_cta_class ); ?>" disabled aria-disabled="true">
		<?php esc_html_e( 'Current plan', 'edminboost-admin-customization' ); ?>
	</button>
<?php else : ?>
	<a
		class="<?php echo esc_attr( $edminboost_cta_class ); ?>"
		href="<?php echo esc_url( $edminboost_upgrade_url ); ?>"
		target="_blank"
		rel="noopener noreferrer"
	>
		<?php
		printf(
			/* translators: %s: plan name */
			esc_html__( 'Upgrade to %s', 'edminboost-admin-customization' ),
			esc_html( $edminboost_plan['name'] )
		);
		?>
	</a>
<?php endif; ?>
