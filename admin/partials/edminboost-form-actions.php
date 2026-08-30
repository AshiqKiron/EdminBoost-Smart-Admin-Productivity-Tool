<?php
/**
 * Save and Reset form actions.
 *
 * @package EdminBoost
 *
 * @var string $save_label     Primary submit button label.
 * @var string $wrapper_tag    Wrapper element tag (`p`, `footer`, or `div`).
 * @var string $wrapper_class  Wrapper CSS classes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$save_label    = isset( $save_label ) ? $save_label : __( 'Save', EDMINBOOST_TEXT_DOMAIN );
$wrapper_tag   = isset( $wrapper_tag ) ? sanitize_key( $wrapper_tag ) : 'p';
$wrapper_class = isset( $wrapper_class ) ? $wrapper_class : 'submit edminboost-form-actions';

if ( ! in_array( $wrapper_tag, array( 'p', 'footer', 'div' ), true ) ) {
	$wrapper_tag = 'p';
}
?>
<<?php echo esc_html( $wrapper_tag ); ?> class="<?php echo esc_attr( $wrapper_class ); ?>">
	<?php submit_button( $save_label, 'primary', 'submit', false ); ?>
	<button type="button" class="button edminboost-form-reset">
		<?php esc_html_e( 'Reset to defaults', EDMINBOOST_TEXT_DOMAIN ); ?>
	</button>
</<?php echo esc_html( $wrapper_tag ); ?>>
