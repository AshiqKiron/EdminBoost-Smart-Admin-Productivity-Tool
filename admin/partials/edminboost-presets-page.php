<?php
/**
 * Presets and role visibility manager.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name       = EDMINBOOST_Settings::OPTION_NAME;
$all_presets       = EDMINBOOST_Command_Center::get_all_presets();
$default_preset    = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';
$role_assignments  = isset( $cc_settings['role_assignments'] ) && is_array( $cc_settings['role_assignments'] )
	? $cc_settings['role_assignments']
	: array();
$role_visibility   = isset( $cc_settings['role_visibility'] ) && is_array( $cc_settings['role_visibility'] )
	? $cc_settings['role_visibility']
	: array();
$roles             = EDMINBOOST_Command_Center::get_assignable_roles();
$top_bar_items     = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
	? $cc_settings['top_bar_items']
	: array();
$has_layout        = ! empty( $top_bar_items );
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap edminboost-cc-wrap--wide">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero edminboost-cc-hero--split">
		<div>
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p class="edminboost-cc-hero__lead">
				<?php esc_html_e( 'Apply a layout template or control which top-bar icons each user role can see.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
		</div>
		<div class="edminboost-cc-hero__actions">
			<button type="button" class="button" id="edminboost-save-preset-btn" <?php echo $has_layout ? '' : 'disabled'; ?>>
				<?php esc_html_e( 'Save current layout as preset', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
		</div>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form" id="edminboost-presets-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_apply_preset]" id="edminboost_apply_preset" value="" />
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_duplicate_preset]" id="edminboost_duplicate_preset" value="" />
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_save_custom_preset][name]" id="edminboost_save_custom_preset_name" value="" />

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-presets-heading">
			<h2 id="edminboost-presets-heading"><?php esc_html_e( 'Layout preset library', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Pick a use-case or role-based template, or use layouts you have saved.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>

			<?php
			$preset_picker_mode = 'full';
			include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-preset-picker.php';
			?>
		</section>

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-roles-heading">
			<h2 id="edminboost-roles-heading"><?php esc_html_e( 'Who sees what', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Hide specific top-bar icons from selected roles. Per-role preset assignment is saved for a future release.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>

			<?php if ( empty( $roles ) ) : ?>
				<p><?php esc_html_e( 'No roles available.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
			<?php elseif ( empty( $top_bar_items ) ) : ?>
				<p><?php esc_html_e( 'Apply a preset or build a top bar layout before configuring role visibility.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
			<?php else : ?>
				<div class="edminboost-role-matrix-wrap">
					<table class="widefat edminboost-role-matrix">
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e( 'User role', EDMINBOOST_TEXT_DOMAIN ); ?></th>
								<th scope="col"><?php esc_html_e( 'Assigned preset', EDMINBOOST_TEXT_DOMAIN ); ?></th>
								<?php foreach ( $top_bar_items as $item ) : ?>
									<?php
									$item_slug  = isset( $item['slug'] ) ? $item['slug'] : '';
									$item_label = isset( $item['label'] ) ? $item['label'] : $item_slug;
									if ( '' === $item_slug ) {
										continue;
									}
									?>
									<th scope="col" class="edminboost-role-matrix__icon-col">
										<span class="dashicons <?php echo esc_attr( isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
										<span class="screen-reader-text"><?php echo esc_html( $item_label ); ?></span>
									</th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $roles as $role_key => $role_name ) : ?>
								<tr>
									<th scope="row"><?php echo esc_html( translate_user_role( $role_name ) ); ?></th>
									<td>
										<label class="screen-reader-text" for="edminboost_role_preset_<?php echo esc_attr( $role_key ); ?>">
											<?php
											/* translators: %s: role name */
											echo esc_html( sprintf( __( 'Preset for %s', EDMINBOOST_TEXT_DOMAIN ), $role_name ) );
											?>
										</label>
										<select
											name="<?php echo esc_attr( $option_name ); ?>[command_center][role_assignments][<?php echo esc_attr( $role_key ); ?>]"
											id="edminboost_role_preset_<?php echo esc_attr( $role_key ); ?>"
										>
											<option value=""><?php esc_html_e( '— Use site default —', EDMINBOOST_TEXT_DOMAIN ); ?></option>
											<?php foreach ( $all_presets as $preset_id => $preset ) : ?>
												<option
													value="<?php echo esc_attr( $preset_id ); ?>"
													<?php selected( isset( $role_assignments[ $role_key ] ) ? $role_assignments[ $role_key ] : '', $preset_id ); ?>
												>
													<?php echo esc_html( isset( $preset['name'] ) ? $preset['name'] : $preset_id ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</td>
									<?php foreach ( $top_bar_items as $item ) : ?>
										<?php
										$item_slug = isset( $item['slug'] ) ? $item['slug'] : '';
										if ( '' === $item_slug ) {
											continue;
										}
										$hidden_for_role = isset( $role_visibility[ $role_key ] ) && is_array( $role_visibility[ $role_key ] )
											? $role_visibility[ $role_key ]
											: array();
										$is_hidden       = in_array( $item_slug, $hidden_for_role, true );
										$field_id        = 'edminboost_vis_' . sanitize_html_class( $role_key . '_' . $item_slug );
										?>
										<td class="edminboost-role-matrix__check">
											<input
												type="checkbox"
												id="<?php echo esc_attr( $field_id ); ?>"
												name="<?php echo esc_attr( $option_name ); ?>[command_center][role_visibility][<?php echo esc_attr( $role_key ); ?>][]"
												value="<?php echo esc_attr( $item_slug ); ?>"
												aria-label="<?php echo esc_attr( sprintf(
													/* translators: 1: role name, 2: item label */
													__( 'Show %2$s for %1$s', EDMINBOOST_TEXT_DOMAIN ),
													translate_user_role( $role_name ),
													isset( $item['label'] ) ? $item['label'] : $item_slug
												) ); ?>"
												<?php checked( ! $is_hidden ); ?>
											/>
										</td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<p class="description">
					<?php esc_html_e( 'Checked icons are visible for that role. Uncheck to hide tools from clients or editors.', EDMINBOOST_TEXT_DOMAIN ); ?>
				</p>
			<?php endif; ?>
		</section>

		<p class="submit">
			<?php submit_button( __( 'Save presets', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
		</p>
	</form>
</div>
