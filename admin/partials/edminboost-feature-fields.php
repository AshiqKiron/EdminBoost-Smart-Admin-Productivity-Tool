<?php
/**
 * Shared feature page fields.
 *
 * @package EdminBoost
 *
 * @var string $option_name              Settings option name (legacy include variable).
 * @var string $edminboost_option_name    Settings option name (prefixed include variable).
 * @var array  $edminboost_features    Feature settings.
 * @var string $edminboost_section     Section key: productivity|security|performance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $edminboost_option_name ) ) {
	$edminboost_option_name = isset( $option_name ) ? $option_name : EDMINBOOST_Settings::OPTION_NAME;
}
$edminboost_features_key = $edminboost_option_name . '[features]';
$edminboost_widget_labels = EDMINBOOST_Dashboard::get_widget_labels();
$edminboost_roles = EDMINBOOST_Command_Center::get_assignable_roles();
$edminboost_post_types = get_post_types( array( 'public' => true ), 'objects' );
?>

<?php if ( 'productivity' === $edminboost_section ) : ?>
<fieldset class="edminboost-fieldset edminboost-productivity-fieldset">
	<legend><?php esc_html_e( 'Admin notices', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<div class="edminboost-productivity-layout edminboost-productivity-layout--stacked" id="edminboost-productivity-hide-notices-layout">
		<div class="edminboost-productivity-fields">
			<label class="edminboost-checkbox-row" for="edminboost_hide_admin_notices">
				<?php EDMINBOOST_Setting_Help::echo_icon( 'hide_admin_notices' ); ?>
				<input type="checkbox" id="edminboost_hide_admin_notices" name="<?php echo esc_attr( $edminboost_features_key ); ?>[hide_admin_notices]" value="1" <?php checked( ! empty( $edminboost_features['hide_admin_notices'] ) ); ?> />
				<?php esc_html_e( 'Hide routine admin notices. Errors and warnings remain visible.', 'edminboost-smart-admin-productivity-tool' ); ?>
			</label>
		</div>
		<?php
		$edminboost_preview = 'notices';
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-productivity-preview.php';
		?>
	</div>
</fieldset>

<fieldset class="edminboost-fieldset edminboost-productivity-fieldset">
	<legend><?php esc_html_e( 'Screen tabs', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<div class="edminboost-productivity-layout edminboost-productivity-layout--stacked" id="edminboost-productivity-hide-screen-layout">
		<div class="edminboost-productivity-fields">
			<label class="edminboost-checkbox-row" for="edminboost_hide_screen_help">
				<?php EDMINBOOST_Setting_Help::echo_icon( 'hide_screen_help' ); ?>
				<input type="checkbox" id="edminboost_hide_screen_help" name="<?php echo esc_attr( $edminboost_features_key ); ?>[hide_screen_help]" value="1" <?php checked( ! empty( $edminboost_features['hide_screen_help'] ) ); ?> />
				<?php esc_html_e( 'Hide Screen Options and Help tabs.', 'edminboost-smart-admin-productivity-tool' ); ?>
			</label>
		</div>
		<?php
		$edminboost_preview = 'screen_help';
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-productivity-preview.php';
		?>
	</div>
</fieldset>

<?php
$edminboost_dashboard_widgets_enabled = ! empty( $edminboost_features['dashboard_widgets']['enabled'] );
$edminboost_dashboard_widgets_options_class = 'edminboost-dependent-section' . ( $edminboost_dashboard_widgets_enabled ? '' : ' is-disabled' );
$edminboost_dashboard_widgets_options_aria  = $edminboost_dashboard_widgets_enabled ? 'false' : 'true';
?>
<fieldset class="edminboost-fieldset edminboost-productivity-fieldset">
	<legend><?php esc_html_e( 'Dashboard widgets', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<div class="edminboost-productivity-layout" id="edminboost-productivity-dashboard-widgets-layout">
		<div class="edminboost-productivity-fields">
			<label class="edminboost-checkbox-row" for="edminboost_dashboard_widgets_enabled">
				<?php EDMINBOOST_Setting_Help::echo_icon( 'dashboard_widgets_enabled' ); ?>
				<span class="edminboost-checkbox-row__main">
					<input type="checkbox" id="edminboost_dashboard_widgets_enabled" name="<?php echo esc_attr( $edminboost_features_key ); ?>[dashboard_widgets][enabled]" value="1" <?php checked( $edminboost_dashboard_widgets_enabled ); ?> />
					<span class="edminboost-checkbox-row__text"><?php esc_html_e( 'Remove selected default dashboard widgets.', 'edminboost-smart-admin-productivity-tool' ); ?></span>
				</span>
			</label>
			<div id="edminboost-dashboard-widgets-options" class="<?php echo esc_attr( $edminboost_dashboard_widgets_options_class ); ?>" aria-disabled="<?php echo esc_attr( $edminboost_dashboard_widgets_options_aria ); ?>">
				<?php foreach ( $edminboost_widget_labels as $edminboost_widget_key => $edminboost_widget_label ) : ?>
					<label class="edminboost-checkbox-row" for="edminboost_widget_<?php echo esc_attr( $edminboost_widget_key ); ?>">
						<input type="checkbox" id="edminboost_widget_<?php echo esc_attr( $edminboost_widget_key ); ?>" name="<?php echo esc_attr( $edminboost_features_key ); ?>[dashboard_widgets][<?php echo esc_attr( $edminboost_widget_key ); ?>]" value="1" <?php checked( ! empty( $edminboost_features['dashboard_widgets'][ $edminboost_widget_key ] ) ); ?> />
						<?php echo esc_html( $edminboost_widget_label ); ?>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		$edminboost_preview = 'dashboard_widgets';
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-productivity-preview.php';
		?>
	</div>
</fieldset>

<?php
$edminboost_admin_footer_enabled = ! empty( $edminboost_features['admin_footer']['enabled'] );
$edminboost_admin_footer_options_class = 'edminboost-dependent-section' . ( $edminboost_admin_footer_enabled ? '' : ' is-disabled' );
$edminboost_admin_footer_options_aria  = $edminboost_admin_footer_enabled ? 'false' : 'true';
?>
<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Admin footer', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_admin_footer_enabled">
		<?php EDMINBOOST_Setting_Help::echo_icon( 'admin_footer_enabled' ); ?>
		<input type="checkbox" id="edminboost_admin_footer_enabled" name="<?php echo esc_attr( $edminboost_features_key ); ?>[admin_footer][enabled]" value="1" <?php checked( $edminboost_admin_footer_enabled ); ?> />
		<?php esc_html_e( 'Replace the default admin footer text.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<div id="edminboost-admin-footer-options" class="<?php echo esc_attr( $edminboost_admin_footer_options_class ); ?>" aria-disabled="<?php echo esc_attr( $edminboost_admin_footer_options_aria ); ?>">
		<p>
			<label for="edminboost_admin_footer_text">
				<?php EDMINBOOST_Setting_Help::echo_icon( 'admin_footer_text' ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Custom footer text', 'edminboost-smart-admin-productivity-tool' ); ?></span>
				<input type="text" class="regular-text" id="edminboost_admin_footer_text" name="<?php echo esc_attr( $edminboost_features_key ); ?>[admin_footer][text]" value="<?php echo esc_attr( $edminboost_features['admin_footer']['text'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Custom footer text', 'edminboost-smart-admin-productivity-tool' ); ?>" />
			</label>
		</p>
	</div>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Workflow tools', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_post_duplicator">
		<?php EDMINBOOST_Setting_Help::echo_icon( 'post_duplicator' ); ?>
		<input type="checkbox" id="edminboost_post_duplicator" name="<?php echo esc_attr( $edminboost_features_key ); ?>[post_duplicator][enabled]" value="1" <?php checked( ! empty( $edminboost_features['post_duplicator']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Enable post and page duplicator row action.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_classic_widgets">
		<?php EDMINBOOST_Setting_Help::echo_icon( 'classic_widgets' ); ?>
		<input type="checkbox" id="edminboost_classic_widgets" name="<?php echo esc_attr( $edminboost_features_key ); ?>[classic_widgets]" value="1" <?php checked( ! empty( $edminboost_features['classic_widgets'] ) ); ?> />
		<?php esc_html_e( 'Use the classic widgets screen.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_menu_duplicator">
		<?php EDMINBOOST_Setting_Help::echo_icon( 'menu_duplicator' ); ?>
		<input type="checkbox" id="edminboost_menu_duplicator" name="<?php echo esc_attr( $edminboost_features_key ); ?>[menu_duplicator]" value="1" <?php checked( ! empty( $edminboost_features['menu_duplicator'] ) ); ?> />
		<?php esc_html_e( 'Enable navigation menu duplication.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Custom list columns', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_custom_columns">
		<?php EDMINBOOST_Setting_Help::echo_icon( 'custom_admin_columns' ); ?>
		<input type="checkbox" id="edminboost_custom_columns" name="<?php echo esc_attr( $edminboost_features_key ); ?>[custom_admin_columns][enabled]" value="1" <?php checked( ! empty( $edminboost_features['custom_admin_columns']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Add optional columns to post and page list tables.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<?php foreach ( array( 'post', 'page' ) as $edminboost_pt ) : ?>
		<p><strong><?php echo esc_html( $edminboost_pt ); ?></strong></p>
		<label class="edminboost-checkbox-row">
			<?php EDMINBOOST_Setting_Help::echo_icon( 'column_thumbnail' ); ?>
			<input type="checkbox" name="<?php echo esc_attr( $edminboost_features_key ); ?>[custom_admin_columns][<?php echo esc_attr( $edminboost_pt ); ?>][thumbnail]" value="1" <?php checked( ! empty( $edminboost_features['custom_admin_columns'][ $edminboost_pt ]['thumbnail'] ) ); ?> />
			<?php esc_html_e( 'Featured image', 'edminboost-smart-admin-productivity-tool' ); ?>
		</label>
		<label class="edminboost-checkbox-row">
			<?php EDMINBOOST_Setting_Help::echo_icon( 'column_id' ); ?>
			<input type="checkbox" name="<?php echo esc_attr( $edminboost_features_key ); ?>[custom_admin_columns][<?php echo esc_attr( $edminboost_pt ); ?>][id]" value="1" <?php checked( ! empty( $edminboost_features['custom_admin_columns'][ $edminboost_pt ]['id'] ) ); ?> />
			<?php esc_html_e( 'Post ID', 'edminboost-smart-admin-productivity-tool' ); ?>
		</label>
		<p>
			<label>
				<?php EDMINBOOST_Setting_Help::echo_icon( 'column_meta_key' ); ?>
				<?php esc_html_e( 'Meta key column', 'edminboost-smart-admin-productivity-tool' ); ?>
				<input type="text" class="regular-text" name="<?php echo esc_attr( $edminboost_features_key ); ?>[custom_admin_columns][<?php echo esc_attr( $edminboost_pt ); ?>][post_meta_key]" value="<?php echo esc_attr( $edminboost_features['custom_admin_columns'][ $edminboost_pt ]['post_meta_key'] ?? '' ); ?>" />
			</label>
		</p>
	<?php endforeach; ?>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Post ordering', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_post_order">
		<?php EDMINBOOST_Setting_Help::echo_icon( 'post_order' ); ?>
		<input type="checkbox" id="edminboost_post_order" name="<?php echo esc_attr( $edminboost_features_key ); ?>[post_order][enabled]" value="1" <?php checked( ! empty( $edminboost_features['post_order']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Enable manual ordering via the Order column.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
</fieldset>
<?php endif; ?>

<?php if ( 'security' === $edminboost_section ) : ?>
<fieldset class="edminboost-fieldset">
	<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'security_hardening_note' ); ?><?php esc_html_e( 'Hardening', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_disable_xmlrpc">
		<input type="checkbox" id="edminboost_disable_xmlrpc" name="<?php echo esc_attr( $edminboost_features_key ); ?>[disable_xmlrpc]" value="1" <?php checked( ! empty( $edminboost_features['disable_xmlrpc'] ) ); ?> />
		<?php EDMINBOOST_Setting_Help::echo_icon( 'disable_xmlrpc' ); ?>
		<?php esc_html_e( 'Disable XML-RPC.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_disable_feeds">
		<input type="checkbox" id="edminboost_disable_feeds" name="<?php echo esc_attr( $edminboost_features_key ); ?>[disable_feeds]" value="1" <?php checked( ! empty( $edminboost_features['disable_feeds'] ) ); ?> />
		<?php EDMINBOOST_Setting_Help::echo_icon( 'disable_feeds' ); ?>
		<?php esc_html_e( 'Disable RSS/Atom feeds and redirect feed URLs.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_rest_hide_head">
		<input type="checkbox" id="edminboost_rest_hide_head" name="<?php echo esc_attr( $edminboost_features_key ); ?>[rest_api_hardening][hide_head]" value="1" <?php checked( ! empty( $edminboost_features['rest_api_hardening']['hide_head'] ) ); ?> />
		<?php EDMINBOOST_Setting_Help::echo_icon( 'rest_hide_head' ); ?>
		<?php esc_html_e( 'Remove REST API link from HTML head.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_rest_disable_guests">
		<input type="checkbox" id="edminboost_rest_disable_guests" name="<?php echo esc_attr( $edminboost_features_key ); ?>[rest_api_hardening][disable_guests]" value="1" <?php checked( ! empty( $edminboost_features['rest_api_hardening']['disable_guests'] ) ); ?> />
		<?php EDMINBOOST_Setting_Help::echo_icon( 'rest_disable_guests' ); ?>
		<?php esc_html_e( 'Disable REST API for guests.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'disable_comments' ); ?><?php esc_html_e( 'Comments', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_disable_comments">
		<input type="checkbox" id="edminboost_disable_comments" name="<?php echo esc_attr( $edminboost_features_key ); ?>[disable_comments][enabled]" value="1" <?php checked( ! empty( $edminboost_features['disable_comments']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Disable comments for selected post types.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<?php foreach ( $edminboost_post_types as $edminboost_post_type ) : ?>
		<label class="edminboost-checkbox-row">
			<input type="checkbox" name="<?php echo esc_attr( $edminboost_features_key ); ?>[disable_comments][post_types][]" value="<?php echo esc_attr( $edminboost_post_type->name ); ?>" <?php checked( in_array( $edminboost_post_type->name, $edminboost_features['disable_comments']['post_types'] ?? array(), true ) ); ?> />
			<?php echo esc_html( $edminboost_post_type->labels->name ); ?>
		</label>
	<?php endforeach; ?>
</fieldset>

<?php if ( EDMINBOOST_Pro::shows_pro_settings_ui() ) : ?>
<?php
$edminboost_login_redirects_enabled = ! empty( $edminboost_features['login_redirects']['enabled'] );
$edminboost_login_redirects_options_class = 'edminboost-dependent-section' . ( $edminboost_login_redirects_enabled ? '' : ' is-disabled' );
$edminboost_login_redirects_options_aria  = $edminboost_login_redirects_enabled ? 'false' : 'true';
?>
<fieldset class="edminboost-fieldset <?php echo esc_attr( EDMINBOOST_Pro::section_class() ); ?>"<?php EDMINBOOST_Pro::echo_feature_attr( 'login_redirects' ); ?>>
	<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'login_redirects_enabled' ); ?><?php esc_html_e( 'Login redirects', 'edminboost-smart-admin-productivity-tool' ); ?> <?php EDMINBOOST_Pro::render_badge(); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_login_redirects_enabled">
		<input type="checkbox" id="edminboost_login_redirects_enabled" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][enabled]" value="1" <?php checked( $edminboost_login_redirects_enabled ); ?> />
		<?php esc_html_e( 'Enable role-based login and logout redirects.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<div id="edminboost-login-redirects-options" class="<?php echo esc_attr( $edminboost_login_redirects_options_class ); ?>" aria-disabled="<?php echo esc_attr( $edminboost_login_redirects_options_aria ); ?>">
		<p>
			<label for="edminboost_default_login"><?php EDMINBOOST_Setting_Help::echo_icon( 'default_login_redirect' ); ?><?php esc_html_e( 'Default login redirect URL', 'edminboost-smart-admin-productivity-tool' ); ?>
				<input type="url" class="regular-text" id="edminboost_default_login" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][default_login]" value="<?php echo esc_attr( $edminboost_features['login_redirects']['default_login'] ?? '' ); ?>" />
			</label>
		</p>
		<p>
			<label for="edminboost_default_logout"><?php EDMINBOOST_Setting_Help::echo_icon( 'default_logout_redirect' ); ?><?php esc_html_e( 'Default logout redirect URL', 'edminboost-smart-admin-productivity-tool' ); ?>
				<input type="url" class="regular-text" id="edminboost_default_logout" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][default_logout]" value="<?php echo esc_attr( $edminboost_features['login_redirects']['default_logout'] ?? '' ); ?>" />
			</label>
		</p>
		<?php foreach ( $edminboost_roles as $edminboost_role_key => $edminboost_role_label ) : ?>
			<p>
				<strong><?php echo esc_html( $edminboost_role_label ); ?></strong><br />
				<label><?php EDMINBOOST_Setting_Help::echo_icon( 'role_login_redirect' ); ?><?php esc_html_e( 'Login URL', 'edminboost-smart-admin-productivity-tool' ); ?>
					<input type="url" class="regular-text" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][login_roles][<?php echo esc_attr( $edminboost_role_key ); ?>]" value="<?php echo esc_attr( $edminboost_features['login_redirects']['login_roles'][ $edminboost_role_key ] ?? '' ); ?>" />
				</label>
				<label><?php EDMINBOOST_Setting_Help::echo_icon( 'role_logout_redirect' ); ?><?php esc_html_e( 'Logout URL', 'edminboost-smart-admin-productivity-tool' ); ?>
					<input type="url" class="regular-text" name="<?php echo esc_attr( $edminboost_features_key ); ?>[login_redirects][logout_roles][<?php echo esc_attr( $edminboost_role_key ); ?>]" value="<?php echo esc_attr( $edminboost_features['login_redirects']['logout_roles'][ $edminboost_role_key ] ?? '' ); ?>" />
				</label>
			</p>
		<?php endforeach; ?>
	</div>
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-pro-upgrade.php'; ?>
</fieldset>
<?php endif; ?>
<?php endif; ?>

<?php if ( 'performance' === $edminboost_section ) : ?>
<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Emoji scripts', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_disable_emojis_enabled">
		<?php EDMINBOOST_Setting_Help::echo_icon( 'disable_emojis' ); ?>
		<input type="checkbox" id="edminboost_disable_emojis_enabled" name="<?php echo esc_attr( $edminboost_features_key ); ?>[disable_emojis][enabled]" value="1" <?php checked( ! empty( $edminboost_features['disable_emojis']['enabled'] ) ); ?> />
		<?php esc_html_e( 'Disable emoji detection scripts.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<p>
		<label for="edminboost_disable_emojis_scope"><?php EDMINBOOST_Setting_Help::echo_icon( 'disable_emojis_scope' ); ?><?php esc_html_e( 'Scope', 'edminboost-smart-admin-productivity-tool' ); ?></label>
		<select id="edminboost_disable_emojis_scope" name="<?php echo esc_attr( $edminboost_features_key ); ?>[disable_emojis][scope]">
			<option value="admin" <?php selected( $edminboost_features['disable_emojis']['scope'] ?? 'admin', 'admin' ); ?>><?php esc_html_e( 'Admin only', 'edminboost-smart-admin-productivity-tool' ); ?></option>
			<option value="frontend" <?php selected( $edminboost_features['disable_emojis']['scope'] ?? 'admin', 'frontend' ); ?>><?php esc_html_e( 'Front end only', 'edminboost-smart-admin-productivity-tool' ); ?></option>
			<option value="both" <?php selected( $edminboost_features['disable_emojis']['scope'] ?? 'admin', 'both' ); ?>><?php esc_html_e( 'Admin and front end', 'edminboost-smart-admin-productivity-tool' ); ?></option>
		</select>
	</p>
	<?php
	$edminboost_preview = 'emoji';
	include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-performance-preview.php';
	?>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php esc_html_e( 'Assets', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<label class="edminboost-checkbox-row" for="edminboost_remove_asset_versions">
		<input type="checkbox" id="edminboost_remove_asset_versions" name="<?php echo esc_attr( $edminboost_features_key ); ?>[remove_asset_versions]" value="1" <?php checked( ! empty( $edminboost_features['remove_asset_versions'] ) ); ?> />
		<?php EDMINBOOST_Setting_Help::echo_icon( 'remove_asset_versions' ); ?>
		<?php esc_html_e( 'Remove version query strings from scripts and styles.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_remove_dashicons_frontend">
		<input type="checkbox" id="edminboost_remove_dashicons_frontend" name="<?php echo esc_attr( $edminboost_features_key ); ?>[remove_dashicons_frontend]" value="1" <?php checked( ! empty( $edminboost_features['remove_dashicons_frontend'] ) ); ?> />
		<?php EDMINBOOST_Setting_Help::echo_icon( 'remove_dashicons_frontend' ); ?>
		<?php esc_html_e( 'Remove Dashicons on the front end for visitors.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<label class="edminboost-checkbox-row" for="edminboost_disable_embeds">
		<input type="checkbox" id="edminboost_disable_embeds" name="<?php echo esc_attr( $edminboost_features_key ); ?>[disable_embeds]" value="1" <?php checked( ! empty( $edminboost_features['disable_embeds'] ) ); ?> />
		<?php EDMINBOOST_Setting_Help::echo_icon( 'disable_embeds' ); ?>
		<?php esc_html_e( 'Disable WordPress embeds and oEmbed discovery.', 'edminboost-smart-admin-productivity-tool' ); ?>
	</label>
	<?php
	$edminboost_preview = 'assets';
	include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-performance-preview.php';
	?>
</fieldset>

<fieldset class="edminboost-fieldset">
	<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'heartbeat_control' ); ?><?php esc_html_e( 'Heartbeat API', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
	<?php
	$edminboost_hb_labels = array(
		'admin'    => __( 'Admin screens', 'edminboost-smart-admin-productivity-tool' ),
		'editor'   => __( 'Post editor', 'edminboost-smart-admin-productivity-tool' ),
		'frontend' => __( 'Front end', 'edminboost-smart-admin-productivity-tool' ),
	);
	$edminboost_hb_options = array(
		'default' => __( 'Default', 'edminboost-smart-admin-productivity-tool' ),
		'slow'    => __( 'Slow (60s)', 'edminboost-smart-admin-productivity-tool' ),
		'disable' => __( 'Disable', 'edminboost-smart-admin-productivity-tool' ),
	);
	?>
	<div class="edminboost-select-rows">
		<?php foreach ( $edminboost_hb_labels as $edminboost_ctx => $edminboost_label ) : ?>
			<div class="edminboost-select-row">
				<label for="edminboost_heartbeat_<?php echo esc_attr( $edminboost_ctx ); ?>"><?php EDMINBOOST_Setting_Help::echo_icon( 'heartbeat_' . $edminboost_ctx ); ?><?php echo esc_html( $edminboost_label ); ?></label>
				<select id="edminboost_heartbeat_<?php echo esc_attr( $edminboost_ctx ); ?>" name="<?php echo esc_attr( $edminboost_features_key ); ?>[heartbeat_control][<?php echo esc_attr( $edminboost_ctx ); ?>]">
					<?php foreach ( $edminboost_hb_options as $edminboost_val => $edminboost_opt_label ) : ?>
						<option value="<?php echo esc_attr( $edminboost_val ); ?>" <?php selected( $edminboost_features['heartbeat_control'][ $edminboost_ctx ] ?? 'default', $edminboost_val ); ?>><?php echo esc_html( $edminboost_opt_label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endforeach; ?>
	</div>
</fieldset>
<?php endif; ?>
