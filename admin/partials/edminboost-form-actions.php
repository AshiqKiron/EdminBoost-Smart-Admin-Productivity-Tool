<?php
/**
 * Save and Reset form actions.
 *
 * @package EdminBoost
 *
 * @var string $edminboost_save_label    Primary submit button label.
 * @var string $edminboost_wrapper_tag   Wrapper element tag (`p`, `footer`, or `div`).
 * @var string $edminboost_wrapper_class Wrapper CSS classes.
 * @var string $save_label               Legacy include variable for primary submit button label.
 * @var string $wrapper_tag              Legacy include variable for wrapper element tag.
 * @var string $wrapper_class            Legacy include variable for wrapper CSS classes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_save_label = isset( $edminboost_save_label )
	? $edminboost_save_label
	: ( isset( $save_label ) ? $save_label : __( 'Save', 'edminboost-admin-customization' ) );
$edminboost_wrapper_tag = isset( $edminboost_wrapper_tag )
	? sanitize_key( $edminboost_wrapper_tag )
	: ( isset( $wrapper_tag ) ? sanitize_key( $wrapper_tag ) : 'p' );
$edminboost_wrapper_class = isset( $edminboost_wrapper_class )
	? $edminboost_wrapper_class
	: ( isset( $wrapper_class ) ? $wrapper_class : 'submit edminboost-form-actions' );

if ( ! in_array( $edminboost_wrapper_tag, array( 'p', 'footer', 'div' ), true ) ) {
	$edminboost_wrapper_tag = 'p';
}
?>
<<?php echo esc_html( $edminboost_wrapper_tag ); ?> class="<?php echo esc_attr( $edminboost_wrapper_class ); ?>">
	<?php submit_button( $edminboost_save_label, 'primary', 'submit', false ); ?>
	<button type="button" class="button edminboost-form-reset">
		<?php esc_html_e( 'Reset to defaults', 'edminboost-admin-customization' ); ?>
	</button>
</<?php echo esc_html( $edminboost_wrapper_tag ); ?>>
