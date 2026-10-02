<?php
/**
 * Top Bar panel and badge settings section (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var array  $edminboost_behavior Behavior settings.
 * @var string $edminboost_cc_key   Form field prefix for behavior.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Included partial expects `$behavior` and `$cc_key` in parent scope.
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$behavior = $edminboost_behavior;
$cc_key   = $edminboost_cc_key;
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
?>
<section class="edminboost-card edminboost-cc-section edminboost-home-look is-disabled" id="edminboost-mapper-look" aria-labelledby="edminboost-mapper-look-heading" aria-disabled="true">
	<h2 id="edminboost-mapper-look-heading"><?php esc_html_e( 'Panel & badges', 'edminboost-admin-customization' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Adjust slide-out panel style and notification badges. Set a top bar link to AJAX slide-out drawer to configure these settings.', 'edminboost-admin-customization' ); ?>
	</p>
	<?php include __DIR__ . '/edminboost-home-advanced-look.php'; ?>
</section>
