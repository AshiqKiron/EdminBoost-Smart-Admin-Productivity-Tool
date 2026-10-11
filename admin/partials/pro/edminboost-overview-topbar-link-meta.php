<?php
/**
 * Top bar link interaction label for Dashboard overview picker (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var string $edminboost_item_interaction Item interaction mode (redirect|drawer).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_item_interaction = isset( $edminboost_item_interaction ) ? $edminboost_item_interaction : 'redirect';
$edminboost_interaction_label = 'drawer' === $edminboost_item_interaction
	? __( 'Opens in drawer', 'edminboost-admin-customization' )
	: __( 'Opens directly', 'edminboost-admin-customization' );
?>
<span class="edminboost-overview-topbar-links-picker__option-meta"><?php echo esc_html( $edminboost_interaction_label ); ?></span>
