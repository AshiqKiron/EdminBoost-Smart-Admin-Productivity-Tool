<?php
/**
 * Login redirects fieldset (Pro/Agency).
 *
 * @package EdminBoost
 *
 * @var string $edminboost_features_key Features form prefix.
 * @var array  $edminboost_features     Feature settings.
 * @var array  $edminboost_roles        Assignable roles.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_login_redirects_enabled       = ! empty( $edminboost_features['login_redirects']['enabled'] );
$edminboost_login_redirects_options_class = 'edminboost-dependent-section' . ( $edminboost_login_redirects_enabled ? '' : ' is-disabled' );
$edminboost_login_redirects_options_aria  = $edminboost_login_redirects_enabled ? 'false' : 'true';
?>
<fieldset class="edminboost-fieldset">
	<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'login_redirects_enabled' ); ?><?php esc_html_e( 'Login redirects', 'edminboost-admin-customization' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_login_redirects_enabled">
		<input type="checkbox" id="edminboost_login_redirects_enabled" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][enabled]" value="1" <?php checked( $edminboost_login_redirects_enabled ); ?> />
		<?php esc_html_e( 'Enable role-based login and logout redirects.', 'edminboost-admin-customization' ); ?>
	</label>
	<div id="edminboost-login-redirects-options" class="<?php echo esc_attr( $edminboost_login_redirects_options_class ); ?>" aria-disabled="<?php echo esc_attr( $edminboost_login_redirects_options_aria ); ?>">
		<p>
			<label for="edminboost_default_login"><?php EDMINBOOST_Setting_Help::echo_icon( 'default_login_redirect' ); ?><?php esc_html_e( 'Default login redirect URL', 'edminboost-admin-customization' ); ?>
				<input type="url" class="regular-text" id="edminboost_default_login" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][default_login]" value="<?php echo esc_attr( $edminboost_features['login_redirects']['default_login'] ?? '' ); ?>" />
			</label>
		</p>
		<p>
			<label for="edminboost_default_logout"><?php EDMINBOOST_Setting_Help::echo_icon( 'default_logout_redirect' ); ?><?php esc_html_e( 'Default logout redirect URL', 'edminboost-admin-customization' ); ?>
				<input type="url" class="regular-text" id="edminboost_default_logout" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][default_logout]" value="<?php echo esc_attr( $edminboost_features['login_redirects']['default_logout'] ?? '' ); ?>" />
			</label>
		</p>
		<?php foreach ( $edminboost_roles as $edminboost_role_key => $edminboost_role_label ) : ?>
			<p>
				<strong><?php echo esc_html( $edminboost_role_label ); ?></strong><br />
				<label><?php EDMINBOOST_Setting_Help::echo_icon( 'role_login_redirect' ); ?><?php esc_html_e( 'Login URL', 'edminboost-admin-customization' ); ?>
					<input type="url" class="regular-text" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][login_roles][<?php echo esc_attr( $edminboost_role_key ); ?>]" value="<?php echo esc_attr( $edminboost_features['login_redirects']['login_roles'][ $edminboost_role_key ] ?? '' ); ?>" />
				</label>
				<label><?php EDMINBOOST_Setting_Help::echo_icon( 'role_logout_redirect' ); ?><?php esc_html_e( 'Logout URL', 'edminboost-admin-customization' ); ?>
					<input type="url" class="regular-text" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][logout_roles][<?php echo esc_attr( $edminboost_role_key ); ?>]" value="<?php echo esc_attr( $edminboost_features['login_redirects']['logout_roles'][ $edminboost_role_key ] ?? '' ); ?>" />
				</label>
			</p>
		<?php endforeach; ?>
	</div>
</fieldset>
