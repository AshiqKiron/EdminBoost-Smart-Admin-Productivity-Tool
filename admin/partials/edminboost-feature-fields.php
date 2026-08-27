<?php
/**
 * Shared feature page fields.
 *
 * @package EdminBoost
 *
 * @var string $option_name Settings option name.
 * @var array  $features    Feature settings.
 * @var string $section     Section key: productivity|security|performance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$features_key = $option_name . '[features]';
$widget_labels = EDMINBOOST_Dashboard::get_widget_labels();
$roles = EDMINBOOST_Command_Center::get_assignable_roles();
$post_types = get_post_types( array( 'public' => true ), 'objects' );
?>

<?php if ( 'productivity' === $section ) : ?>
<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Admin notices', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_hide_admin_notices">
		<input type="checkbox" id="edminboost_hide_admin_notices" name="<?php echo esc_attr( $features_key ); ?>[hide_admin_notices]" value="1" <?php checked( ! empty( $features['hide_admin_notices'] ) ); ?> />
		<?php esc_html_e( 'Hide routine admin notices. Errors and warnings remain visible.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_hide_screen_help">
		<input type="checkbox" id="edminboost_hide_screen_help" name="<?php echo esc_attr( $features_key ); ?>[hide_screen_help]" value="1" <?php checked( ! empty( $features['hide_screen_help'] ) ); ?> />
		<?php esc_html_e( 'Hide Screen Options and Help tabs.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Dashboard widgets', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<?php foreach ( $widget_labels as $widget_key => $widget_label ) : ?>
		<label class="edminboost-checkbox-row" for="edminboost_widget_<?php echo esc_attr( $widget_key ); ?>">
			<input type="checkbox" id="edminboost_widget_<?php echo esc_attr( $widget_key ); ?>" name="<?php echo esc_attr( $features_key ); ?>[dashboard_widgets][<?php echo esc_attr( $widget_key ); ?>]" value="1" <?php checked( ! empty( $features['dashboard_widgets'][ $widget_key ] ) ); ?> />
			<?php echo esc_html( $widget_label ); ?>
		</label>
	<?php endforeach; ?>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Admin footer', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_admin_footer_enabled">
		<input type="checkbox" id="edminboost_admin_footer_enabled" name="<?php echo esc_attr( $features_key ); ?>[admin_footer][enabled]" value="1" <?php checked( ! empty( $features['admin_footer']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Replace the default admin footer text.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<p>
		<label for="edminboost_admin_footer_text">
			<span class="screen-reader-text"><?php esc_html_e( 'Custom footer text', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			<input type="text" class="regular-text" id="edminboost_admin_footer_text" name="<?php echo esc_attr( $features_key ); ?>[admin_footer][text]" value="<?php echo esc_attr( $features['admin_footer']['text'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Custom footer text', EDMINBOOST_TEXT_DOMAIN ); ?>" />
		</label>
	</p>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Workflow tools', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_post_duplicator">
		<input type="checkbox" id="edminboost_post_duplicator" name="<?php echo esc_attr( $features_key ); ?>[post_duplicator][enabled]" value="1" <?php checked( ! empty( $features['post_duplicator']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Enable post and page duplicator row action.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_classic_widgets">
		<input type="checkbox" id="edminboost_classic_widgets" name="<?php echo esc_attr( $features_key ); ?>[classic_widgets]" value="1" <?php checked( ! empty( $features['classic_widgets'] ) ); ?> />
		<?php esc_html_e( 'Use the classic widgets screen.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_menu_duplicator">
		<input type="checkbox" id="edminboost_menu_duplicator" name="<?php echo esc_attr( $features_key ); ?>[menu_duplicator]" value="1" <?php checked( ! empty( $features['menu_duplicator'] ) ); ?> />
		<?php esc_html_e( 'Enable navigation menu duplication.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Custom list columns', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_custom_columns">
		<input type="checkbox" id="edminboost_custom_columns" name="<?php echo esc_attr( $features_key ); ?>[custom_admin_columns][enabled]" value="1" <?php checked( ! empty( $features['custom_admin_columns']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Add optional columns to post and page list tables.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<?php foreach ( array( 'post', 'page' ) as $pt ) : ?>
		<p><strong><?php echo esc_html( $pt ); ?></strong></p>
		<label class="edminboost-checkbox-row">
			<input type="checkbox" name="<?php echo esc_attr( $features_key ); ?>[custom_admin_columns][<?php echo esc_attr( $pt ); ?>][thumbnail]" value="1" <?php checked( ! empty( $features['custom_admin_columns'][ $pt ]['thumbnail'] ) ); ?> />
			<?php esc_html_e( 'Featured image', EDMINBOOST_TEXT_DOMAIN ); ?>
		</label>
		<label class="edminboost-checkbox-row">
			<input type="checkbox" name="<?php echo esc_attr( $features_key ); ?>[custom_admin_columns][<?php echo esc_attr( $pt ); ?>][id]" value="1" <?php checked( ! empty( $features['custom_admin_columns'][ $pt ]['id'] ) ); ?> />
			<?php esc_html_e( 'Post ID', EDMINBOOST_TEXT_DOMAIN ); ?>
		</label>
		<p>
			<label>
				<?php esc_html_e( 'Meta key column', EDMINBOOST_TEXT_DOMAIN ); ?>
				<input type="text" class="regular-text" name="<?php echo esc_attr( $features_key ); ?>[custom_admin_columns][<?php echo esc_attr( $pt ); ?>][meta_key]" value="<?php echo esc_attr( $features['custom_admin_columns'][ $pt ]['meta_key'] ?? '' ); ?>" />
			</label>
		</p>
	<?php endforeach; ?>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Post ordering', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_post_order">
		<input type="checkbox" id="edminboost_post_order" name="<?php echo esc_attr( $features_key ); ?>[post_order][enabled]" value="1" <?php checked( ! empty( $features['post_order']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Enable manual ordering via the Order column.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
</fieldset>
<?php endif; ?>

<?php if ( 'security' === $section ) : ?>
<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Hardening', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<p class="description"><?php esc_html_e( 'These options may affect plugins or themes that rely on XML-RPC, feeds, or public REST access.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	<label class="edminboost-checkbox-row" for="edminboost_disable_xmlrpc">
		<input type="checkbox" id="edminboost_disable_xmlrpc" name="<?php echo esc_attr( $features_key ); ?>[disable_xmlrpc]" value="1" <?php checked( ! empty( $features['disable_xmlrpc'] ) ); ?> />
		<?php esc_html_e( 'Disable XML-RPC.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_disable_feeds">
		<input type="checkbox" id="edminboost_disable_feeds" name="<?php echo esc_attr( $features_key ); ?>[disable_feeds]" value="1" <?php checked( ! empty( $features['disable_feeds'] ) ); ?> />
		<?php esc_html_e( 'Disable RSS/Atom feeds and redirect feed URLs.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_rest_hide_head">
		<input type="checkbox" id="edminboost_rest_hide_head" name="<?php echo esc_attr( $features_key ); ?>[rest_api_hardening][hide_head]" value="1" <?php checked( ! empty( $features['rest_api_hardening']['hide_head'] ) ); ?> />
		<?php esc_html_e( 'Remove REST API link from HTML head.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_rest_disable_guests">
		<input type="checkbox" id="edminboost_rest_disable_guests" name="<?php echo esc_attr( $features_key ); ?>[rest_api_hardening][disable_guests]" value="1" <?php checked( ! empty( $features['rest_api_hardening']['disable_guests'] ) ); ?> />
		<?php esc_html_e( 'Disable REST API for guests.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Comments', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_disable_comments">
		<input type="checkbox" id="edminboost_disable_comments" name="<?php echo esc_attr( $features_key ); ?>[disable_comments][enabled]" value="1" <?php checked( ! empty( $features['disable_comments']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Disable comments for selected post types.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<?php foreach ( $post_types as $post_type ) : ?>
		<label class="edminboost-checkbox-row">
			<input type="checkbox" name="<?php echo esc_attr( $features_key ); ?>[disable_comments][post_types][]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $features['disable_comments']['post_types'] ?? array(), true ) ); ?> />
			<?php echo esc_html( $post_type->labels->name ); ?>
		</label>
	<?php endforeach; ?>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Login redirects', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_login_redirects_enabled">
		<input type="checkbox" id="edminboost_login_redirects_enabled" name="<?php echo esc_attr( $features_key ); ?>[login_redirects][enabled]" value="1" <?php checked( ! empty( $features['login_redirects']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Enable role-based login and logout redirects.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<p>
		<label for="edminboost_default_login"><?php esc_html_e( 'Default login redirect URL', EDMINBOOST_TEXT_DOMAIN ); ?>
			<input type="url" class="regular-text" id="edminboost_default_login" name="<?php echo esc_attr( $features_key ); ?>[login_redirects][default_login]" value="<?php echo esc_attr( $features['login_redirects']['default_login'] ?? '' ); ?>" />
		</label>
	</p>
	<p>
		<label for="edminboost_default_logout"><?php esc_html_e( 'Default logout redirect URL', EDMINBOOST_TEXT_DOMAIN ); ?>
			<input type="url" class="regular-text" id="edminboost_default_logout" name="<?php echo esc_attr( $features_key ); ?>[login_redirects][default_logout]" value="<?php echo esc_attr( $features['login_redirects']['default_logout'] ?? '' ); ?>" />
		</label>
	</p>
	<?php foreach ( $roles as $role_key => $role_label ) : ?>
		<p>
			<strong><?php echo esc_html( $role_label ); ?></strong><br />
			<label><?php esc_html_e( 'Login URL', EDMINBOOST_TEXT_DOMAIN ); ?>
				<input type="url" class="regular-text" name="<?php echo esc_attr( $features_key ); ?>[login_redirects][login_roles][<?php echo esc_attr( $role_key ); ?>]" value="<?php echo esc_attr( $features['login_redirects']['login_roles'][ $role_key ] ?? '' ); ?>" />
			</label>
			<label><?php esc_html_e( 'Logout URL', EDMINBOOST_TEXT_DOMAIN ); ?>
				<input type="url" class="regular-text" name="<?php echo esc_attr( $features_key ); ?>[login_redirects][logout_roles][<?php echo esc_attr( $role_key ); ?>]" value="<?php echo esc_attr( $features['login_redirects']['logout_roles'][ $role_key ] ?? '' ); ?>" />
			</label>
		</p>
	<?php endforeach; ?>
</fieldset>
<?php endif; ?>

<?php if ( 'performance' === $section ) : ?>
<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Emoji scripts', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_disable_emojis_enabled">
		<input type="checkbox" id="edminboost_disable_emojis_enabled" name="<?php echo esc_attr( $features_key ); ?>[disable_emojis][enabled]" value="1" <?php checked( ! empty( $features['disable_emojis']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Disable emoji detection scripts.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<p>
		<label for="edminboost_disable_emojis_scope"><?php esc_html_e( 'Scope', EDMINBOOST_TEXT_DOMAIN ); ?></label>
		<select id="edminboost_disable_emojis_scope" name="<?php echo esc_attr( $features_key ); ?>[disable_emojis][scope]">
			<option value="admin" <?php selected( $features['disable_emojis']['scope'] ?? 'admin', 'admin' ); ?>><?php esc_html_e( 'Admin only', EDMINBOOST_TEXT_DOMAIN ); ?></option>
			<option value="frontend" <?php selected( $features['disable_emojis']['scope'] ?? 'admin', 'frontend' ); ?>><?php esc_html_e( 'Front end only', EDMINBOOST_TEXT_DOMAIN ); ?></option>
			<option value="both" <?php selected( $features['disable_emojis']['scope'] ?? 'admin', 'both' ); ?>><?php esc_html_e( 'Admin and front end', EDMINBOOST_TEXT_DOMAIN ); ?></option>
		</select>
	</p>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Assets', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_remove_asset_versions">
		<input type="checkbox" id="edminboost_remove_asset_versions" name="<?php echo esc_attr( $features_key ); ?>[remove_asset_versions]" value="1" <?php checked( ! empty( $features['remove_asset_versions'] ) ); ?> />
		<?php esc_html_e( 'Remove version query strings from scripts and styles.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_remove_dashicons_frontend">
		<input type="checkbox" id="edminboost_remove_dashicons_frontend" name="<?php echo esc_attr( $features_key ); ?>[remove_dashicons_frontend]" value="1" <?php checked( ! empty( $features['remove_dashicons_frontend'] ) ); ?> />
		<?php esc_html_e( 'Remove Dashicons on the front end for visitors.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_disable_embeds">
		<input type="checkbox" id="edminboost_disable_embeds" name="<?php echo esc_attr( $features_key ); ?>[disable_embeds]" value="1" <?php checked( ! empty( $features['disable_embeds'] ) ); ?> />
		<?php esc_html_e( 'Disable WordPress embeds and oEmbed discovery.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</label>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Heartbeat API', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
	<?php
	$hb_labels = array(
		'admin'    => __( 'Admin screens', EDMINBOOST_TEXT_DOMAIN ),
		'editor'   => __( 'Post editor', EDMINBOOST_TEXT_DOMAIN ),
		'frontend' => __( 'Front end', EDMINBOOST_TEXT_DOMAIN ),
	);
	$hb_options = array(
		'default' => __( 'Default', EDMINBOOST_TEXT_DOMAIN ),
		'slow'    => __( 'Slow (60s)', EDMINBOOST_TEXT_DOMAIN ),
		'disable' => __( 'Disable', EDMINBOOST_TEXT_DOMAIN ),
	);
	foreach ( $hb_labels as $ctx => $label ) :
		?>
		<p>
			<label for="edminboost_heartbeat_<?php echo esc_attr( $ctx ); ?>"><?php echo esc_html( $label ); ?></label>
			<select id="edminboost_heartbeat_<?php echo esc_attr( $ctx ); ?>" name="<?php echo esc_attr( $features_key ); ?>[heartbeat_control][<?php echo esc_attr( $ctx ); ?>]">
				<?php foreach ( $hb_options as $val => $opt_label ) : ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $features['heartbeat_control'][ $ctx ] ?? 'default', $val ); ?>><?php echo esc_html( $opt_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php endforeach; ?>
</fieldset>
<?php endif; ?>
