<?php
/**
 * Billing plan call-to-action button (current plan or upgrade link).
 *
 * @package EdminBoost
 *
 * @var bool   $is_active   Whether this plan is the active plan.
 * @var array  $plan        Plan definition (requires `name`).
 * @var string $upgrade_url Upgrade URL for non-active plans.
 * @var string $class       Optional extra class(es) on the CTA element.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta_class = 'button button-large';
$cta_class .= $is_active ? ' button-secondary' : ' button-primary';
if ( ! empty( $class ) ) {
	$cta_class .= ' ' . $class;
}
?>
<?php if ( $is_active ) : ?>
	<button type="button" class="<?php echo esc_attr( $cta_class ); ?>" disabled aria-disabled="true">
		<?php esc_html_e( 'Current plan', EDMINBOOST_TEXT_DOMAIN ); ?>
	</button>
<?php else : ?>
	<a
		class="<?php echo esc_attr( $cta_class ); ?>"
		href="<?php echo esc_url( $upgrade_url ); ?>"
		target="_blank"
		rel="noopener noreferrer"
	>
		<?php
		printf(
			/* translators: %s: plan name */
			esc_html__( 'Upgrade to %s', EDMINBOOST_TEXT_DOMAIN ),
			esc_html( $plan['name'] )
		);
		?>
	</a>
<?php endif; ?>
