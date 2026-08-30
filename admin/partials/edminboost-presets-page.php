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
$matrix_items      = EDMINBOOST_Command_Center::get_role_matrix_menu_items();
$top_bar_items     = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
	? $cc_settings['top_bar_items']
	: array();
$has_layout        = ! empty( $top_bar_items );
$has_matrix        = ! empty( $matrix_items );
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap edminboost-cc-wrap--wide">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero edminboost-cc-hero--split">
		<div>
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p class="edminboost-cc-hero__lead">
				<?php esc_html_e( 'Choose a layout template—or reuse one you saved—to set up the top bar and sidebar menu, then control which admin menu items each user role can see.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
		</div>
		<div class="edminboost-cc-hero__actions">
			<div class="edminboost-save-preset" id="edminboost-save-preset">
				<button type="button" class="button edminboost-save-preset__trigger" id="edminboost-save-preset-btn" <?php echo $has_layout ? '' : 'disabled'; ?>>
					<?php esc_html_e( 'Save current layout as preset', EDMINBOOST_TEXT_DOMAIN ); ?>
				</button>
				<div class="edminboost-save-preset__form" id="edminboost-save-preset-form">
					<label class="edminboost-save-preset__label" for="edminboost_save_preset_name_input">
						<?php esc_html_e( 'Preset name', EDMINBOOST_TEXT_DOMAIN ); ?>
					</label>
					<input
						type="text"
						class="regular-text edminboost-save-preset__input"
						id="edminboost_save_preset_name_input"
						placeholder="<?php echo esc_attr__( 'Enter a name for your preset', EDMINBOOST_TEXT_DOMAIN ); ?>"
						autocomplete="off"
					/>
					<button type="button" class="button button-primary" id="edminboost-save-preset-confirm-btn">
						<?php esc_html_e( 'Save preset', EDMINBOOST_TEXT_DOMAIN ); ?>
					</button>
					<button type="button" class="button" id="edminboost-save-preset-cancel-btn">
						<?php esc_html_e( 'Cancel', EDMINBOOST_TEXT_DOMAIN ); ?>
					</button>
				</div>
			</div>
		</div>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form" id="edminboost-presets-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_apply_preset]" id="edminboost_apply_preset" value="" />
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_save_custom_preset][name]" id="edminboost_save_custom_preset_name" value="" />

		<section class="edminboost-card edminboost-cc-section">
			<?php
			$preset_picker_mode = 'full';
			include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-preset-picker.php';
			?>
		</section>

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-roles-heading">
			<h2 id="edminboost-roles-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'role_visibility' ); ?><?php esc_html_e( 'Who sees what', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Assign a layout preset per role to customize both the top bar and admin sidebar for that role. Changing a preset updates the menu checkboxes for that role; you can still fine-tune visibility before saving.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>

			<?php if ( empty( $roles ) ) : ?>
				<p><?php esc_html_e( 'No roles available.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
			<?php elseif ( ! $has_matrix ) : ?>
				<p><?php esc_html_e( 'No admin menu items were discovered. Try reloading this page from wp-admin.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
			<?php else : ?>
				<div class="edminboost-role-matrix-wrap">
					<table class="widefat edminboost-role-matrix">
						<thead>
							<tr>
								<th scope="col" class="edminboost-role-matrix__role-col"><?php esc_html_e( 'User role', EDMINBOOST_TEXT_DOMAIN ); ?></th>
								<th scope="col" class="edminboost-role-matrix__preset-col">
									<?php EDMINBOOST_Setting_Help::echo_icon( 'role_assignments' ); ?><?php esc_html_e( 'Assigned preset', EDMINBOOST_TEXT_DOMAIN ); ?>
								</th>
								<?php foreach ( $matrix_items as $item ) : ?>
									<?php
									$item_slug  = isset( $item['slug'] ) ? $item['slug'] : '';
									$item_label = isset( $item['label'] ) ? $item['label'] : $item_slug;
									if ( '' === $item_slug ) {
										continue;
									}
									?>
									<th scope="col" class="edminboost-role-matrix__menu-col">
										<span class="edminboost-role-matrix__menu-heading">
											<span class="dashicons <?php echo esc_attr( isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
											<span class="edminboost-role-matrix__menu-label"><?php echo esc_html( $item_label ); ?></span>
										</span>
									</th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $roles as $role_key => $role_name ) : ?>
								<?php $role_label = translate_user_role( $role_name ); ?>
								<tr class="edminboost-role-matrix__row" data-edminboost-role="<?php echo esc_attr( $role_key ); ?>">
									<th scope="row" class="edminboost-role-matrix__role-col"><?php echo esc_html( $role_label ); ?></th>
									<td class="edminboost-role-matrix__preset-col">
										<label class="screen-reader-text" for="edminboost_role_preset_<?php echo esc_attr( $role_key ); ?>">
											<?php
											/* translators: %s: role name */
											echo esc_html( sprintf( __( 'Preset for %s', EDMINBOOST_TEXT_DOMAIN ), $role_label ) );
											?>
										</label>
										<select
											class="edminboost-role-preset-select"
											name="<?php echo esc_attr( $option_name ); ?>[command_center][role_assignments][<?php echo esc_attr( $role_key ); ?>]"
											id="edminboost_role_preset_<?php echo esc_attr( $role_key ); ?>"
											data-edminboost-role="<?php echo esc_attr( $role_key ); ?>"
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
									<?php foreach ( $matrix_items as $item ) : ?>
										<?php
										$item_slug       = isset( $item['slug'] ) ? $item['slug'] : '';
										if ( '' === $item_slug ) {
											continue;
										}
										$item_label      = isset( $item['label'] ) ? $item['label'] : $item_slug;
										$can_access      = EDMINBOOST_Command_Center::role_can_access_menu_slug( $role_key, $item_slug );
										$hidden_for_role = isset( $role_visibility[ $role_key ] ) && is_array( $role_visibility[ $role_key ] )
											? $role_visibility[ $role_key ]
											: array();
										$has_saved_visibility = array_key_exists( $role_key, $role_visibility );
										$is_hidden            = in_array( $item_slug, $hidden_for_role, true );
										$protected_slugs      = EDMINBOOST_Command_Center::get_protected_slugs_for_role( $role_key );
										$is_protected         = in_array( $item_slug, $protected_slugs, true );
										$field_id             = 'edminboost_vis_' . sanitize_html_class( $role_key . '_' . $item_slug );

										if ( $is_protected ) {
											$is_checked = true;
										} elseif ( ! $can_access && ! $has_saved_visibility ) {
											$is_checked = false;
										} else {
											$is_checked = ! $is_hidden;
										}

										$cell_classes = 'edminboost-role-matrix__check';
										if ( $is_protected ) {
											$cell_classes .= ' is-protected';
										} elseif ( ! $can_access ) {
											$cell_classes .= ' is-capability-restricted';
										}
										?>
										<td
											class="<?php echo esc_attr( $cell_classes ); ?>"
											data-item-slug="<?php echo esc_attr( $item_slug ); ?>"
										>
											<label class="edminboost-role-matrix__check-label" for="<?php echo esc_attr( $field_id ); ?>">
												<span class="screen-reader-text">
													<?php
													echo esc_html(
														sprintf(
															/* translators: 1: role name, 2: item label */
															__( 'Show %2$s for %1$s', EDMINBOOST_TEXT_DOMAIN ),
															$role_label,
															$item_label
														)
													);
													?>
												</span>
												<input
													type="checkbox"
													class="edminboost-role-visibility-checkbox"
													id="<?php echo esc_attr( $field_id ); ?>"
													name="<?php echo esc_attr( $option_name ); ?>[command_center][role_visibility][<?php echo esc_attr( $role_key ); ?>][]"
													value="<?php echo esc_attr( $item_slug ); ?>"
													data-item-slug="<?php echo esc_attr( $item_slug ); ?>"
													<?php checked( $is_checked ); ?>
													<?php disabled( $is_protected ); ?>
												/>
											</label>
										</td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<p class="description">
					<?php esc_html_e( 'Checked menu items stay visible in the top bar and sidebar for that role. Uncheck to hide tools from clients or editors. Items not included in the assigned preset start unchecked; you can still enable them manually. Items this role cannot access by default appear unchecked—you may enable them if needed.', EDMINBOOST_TEXT_DOMAIN ); ?>
				</p>
			<?php endif; ?>
		</section>

		<?php
		$save_label = __( 'Save presets', EDMINBOOST_TEXT_DOMAIN );
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-form-actions.php';
		?>
	</form>
</div>
