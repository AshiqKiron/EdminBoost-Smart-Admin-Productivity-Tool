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

$edminboost_option_name       = EDMINBOOST_Settings::OPTION_NAME;
$edminboost_all_presets       = EDMINBOOST_Command_Center::get_all_presets();
$edminboost_default_preset    = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';
$edminboost_role_assignments  = isset( $cc_settings['role_assignments'] ) && is_array( $cc_settings['role_assignments'] )
	? $cc_settings['role_assignments']
	: array();
$edminboost_role_visibility   = isset( $cc_settings['role_visibility'] ) && is_array( $cc_settings['role_visibility'] )
	? $cc_settings['role_visibility']
	: array();
$edminboost_roles             = EDMINBOOST_Command_Center::get_assignable_roles();
$edminboost_matrix_items      = EDMINBOOST_Command_Center::get_role_matrix_menu_items();
$edminboost_top_bar_items     = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
	? $cc_settings['top_bar_items']
	: array();
$edminboost_has_layout        = ! empty( $edminboost_top_bar_items );
$edminboost_has_matrix        = ! empty( $edminboost_matrix_items );
$edminboost_is_pro            = EDMINBOOST_Pro::is_active();
$edminboost_can_save_preset   = EDMINBOOST_Pro::can_save_custom_preset( $cc_settings );
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
				<button type="button" class="button edminboost-save-preset__trigger" id="edminboost-save-preset-btn" <?php disabled( ! $edminboost_has_layout || ! $edminboost_can_save_preset ); ?>>
					<?php esc_html_e( 'Save current layout as preset', EDMINBOOST_TEXT_DOMAIN ); ?>
					<?php if ( ! $edminboost_is_pro ) : ?>
						<?php EDMINBOOST_Pro::render_badge(); ?>
					<?php endif; ?>
				</button>
				<?php if ( ! $edminboost_can_save_preset && $edminboost_has_layout ) : ?>
					<p class="description edminboost-pro-upgrade"><?php esc_html_e( 'Free includes one saved custom layout. Upgrade for unlimited saves.', EDMINBOOST_TEXT_DOMAIN ); ?> <a href="<?php echo esc_url( EDMINBOOST_Pro::get_billing_url() ); ?>"><?php esc_html_e( 'View plans', EDMINBOOST_TEXT_DOMAIN ); ?></a></p>
				<?php endif; ?>
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
		<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[enabled]" value="1" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_apply_preset]" id="edminboost_apply_preset" value="" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_save_custom_preset][name]" id="edminboost_save_custom_preset_name" value="" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_rename_custom_preset][id]" id="edminboost_rename_custom_preset_id" value="" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_rename_custom_preset][name]" id="edminboost_rename_custom_preset_name" value="" />

		<section class="edminboost-card edminboost-cc-section">
			<?php
			$edminboost_preset_picker_mode = 'full';
			include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-preset-picker.php';
			?>
		</section>

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-roles-heading">
			<h2 id="edminboost-roles-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'role_visibility' ); ?><?php esc_html_e( 'Who sees what', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Assign a layout preset per role to customize both the top bar and admin sidebar for that role. Changing a preset updates the menu checkboxes for that role; you can still fine-tune visibility before saving.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>

			<?php if ( empty( $edminboost_roles ) ) : ?>
				<p><?php esc_html_e( 'No roles available.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
			<?php elseif ( ! $edminboost_has_matrix ) : ?>
				<p><?php esc_html_e( 'No admin menu items were discovered. Try reloading this page from wp-admin.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
			<?php else : ?>
				<?php if ( ! $edminboost_is_pro ) : ?>
					<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-pro-upgrade.php'; ?>
				<?php endif; ?>
				<div class="edminboost-role-matrix-wrap">
					<table class="widefat edminboost-role-matrix">
						<thead>
							<tr>
								<th scope="col" class="edminboost-role-matrix__role-col"><?php esc_html_e( 'User role', EDMINBOOST_TEXT_DOMAIN ); ?></th>
								<th scope="col" class="edminboost-role-matrix__preset-col">
									<?php EDMINBOOST_Setting_Help::echo_icon( 'role_assignments' ); ?><?php esc_html_e( 'Assigned preset', EDMINBOOST_TEXT_DOMAIN ); ?>
								</th>
								<?php foreach ( $edminboost_matrix_items as $edminboost_item ) : ?>
									<?php
									$edminboost_item_slug       = isset( $edminboost_item['slug'] ) ? $edminboost_item['slug'] : '';
									if ( '' === $edminboost_item_slug ) {
										continue;
									}
									$edminboost_item_label      = isset( $edminboost_item['label'] ) ? $edminboost_item['label'] : $edminboost_item_slug;
									$edminboost_item_source     = isset( $edminboost_item['source'] ) ? $edminboost_item['source'] : 'top';
									$edminboost_is_submenu      = 'submenu' === $edminboost_item_source;
									$edminboost_parent_label    = isset( $edminboost_item['parent_label'] ) ? $edminboost_item['parent_label'] : '';
									$edminboost_column_classes  = 'edminboost-role-matrix__menu-col';
									if ( $edminboost_is_submenu ) {
										$edminboost_column_classes .= ' is-submenu';
									}
									?>
									<th scope="col" class="<?php echo esc_attr( $edminboost_column_classes ); ?>">
										<span class="edminboost-role-matrix__menu-heading">
											<?php if ( ! $edminboost_is_submenu ) : ?>
												<span class="dashicons <?php echo esc_attr( isset( $edminboost_item['icon'] ) ? $edminboost_item['icon'] : 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
											<?php endif; ?>
											<span class="edminboost-role-matrix__menu-label">
												<?php if ( $edminboost_is_submenu && '' !== $edminboost_parent_label ) : ?>
													<span class="edminboost-role-matrix__menu-parent"><?php echo esc_html( $edminboost_parent_label ); ?></span>
												<?php endif; ?>
												<span class="edminboost-role-matrix__menu-name"><?php echo esc_html( $edminboost_item_label ); ?></span>
											</span>
										</span>
									</th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $edminboost_roles as $edminboost_role_key => $edminboost_role_name ) : ?>
								<?php $edminboost_role_label = translate_user_role( $edminboost_role_name ); ?>
								<tr class="edminboost-role-matrix__row" data-edminboost-role="<?php echo esc_attr( $edminboost_role_key ); ?>">
									<th scope="row" class="edminboost-role-matrix__role-col"><?php echo esc_html( $edminboost_role_label ); ?></th>
									<td class="edminboost-role-matrix__preset-col">
										<label class="screen-reader-text" for="edminboost_role_preset_<?php echo esc_attr( $edminboost_role_key ); ?>">
											<?php
											/* translators: %s: role name */
											echo esc_html( sprintf( __( 'Preset for %s', EDMINBOOST_TEXT_DOMAIN ), $edminboost_role_label ) );
											?>
										</label>
										<select
											class="edminboost-role-preset-select"
											name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][role_assignments][<?php echo esc_attr( $edminboost_role_key ); ?>]"
											id="edminboost_role_preset_<?php echo esc_attr( $edminboost_role_key ); ?>"
											data-edminboost-role="<?php echo esc_attr( $edminboost_role_key ); ?>"
										>
											<option value=""><?php esc_html_e( '— Use site default —', EDMINBOOST_TEXT_DOMAIN ); ?></option>
											<?php foreach ( $edminboost_all_presets as $edminboost_preset_id => $edminboost_preset ) : ?>
												<option
													value="<?php echo esc_attr( $edminboost_preset_id ); ?>"
													<?php selected( isset( $edminboost_role_assignments[ $edminboost_role_key ] ) ? $edminboost_role_assignments[ $edminboost_role_key ] : '', $edminboost_preset_id ); ?>
												>
													<?php echo esc_html( isset( $edminboost_preset['name'] ) ? $edminboost_preset['name'] : $edminboost_preset_id ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</td>
									<?php foreach ( $edminboost_matrix_items as $edminboost_item ) : ?>
										<?php
										$edminboost_item_slug       = isset( $edminboost_item['slug'] ) ? $edminboost_item['slug'] : '';
										if ( '' === $edminboost_item_slug ) {
											continue;
										}
										$edminboost_item_label      = isset( $edminboost_item['label'] ) ? $edminboost_item['label'] : $edminboost_item_slug;
										$edminboost_item_source     = isset( $edminboost_item['source'] ) ? $edminboost_item['source'] : 'top';
										$edminboost_is_submenu      = 'submenu' === $edminboost_item_source;
										$edminboost_parent_label    = isset( $edminboost_item['parent_label'] ) ? $edminboost_item['parent_label'] : '';
										$edminboost_can_access      = EDMINBOOST_Command_Center::role_can_access_menu_slug( $edminboost_role_key, $edminboost_item_slug );
										$edminboost_hidden_for_role = isset( $edminboost_role_visibility[ $edminboost_role_key ] ) && is_array( $edminboost_role_visibility[ $edminboost_role_key ] )
											? $edminboost_role_visibility[ $edminboost_role_key ]
											: array();
										$edminboost_has_saved_visibility = array_key_exists( $edminboost_role_key, $edminboost_role_visibility );
										$edminboost_is_hidden            = in_array( $edminboost_item_slug, $edminboost_hidden_for_role, true );
										$edminboost_protected_slugs      = EDMINBOOST_Command_Center::get_protected_slugs_for_role( $edminboost_role_key );
										$edminboost_is_protected         = ! $edminboost_is_submenu && in_array( $edminboost_item_slug, $edminboost_protected_slugs, true );
										$edminboost_field_id             = 'edminboost_vis_' . sanitize_html_class( $edminboost_role_key . '_' . $edminboost_item_slug );

										if ( $edminboost_is_protected ) {
											$edminboost_is_checked = true;
										} elseif ( ! $edminboost_can_access && ! $edminboost_has_saved_visibility ) {
											$edminboost_is_checked = false;
										} else {
											$edminboost_is_checked = ! $edminboost_is_hidden;
										}

										$edminboost_cell_classes = 'edminboost-role-matrix__check';
										if ( $edminboost_is_submenu ) {
											$edminboost_cell_classes .= ' is-submenu';
										}
										if ( $edminboost_is_protected ) {
											$edminboost_cell_classes .= ' is-protected';
										} elseif ( ! $edminboost_can_access ) {
											$edminboost_cell_classes .= ' is-capability-restricted';
										}
										if ( ! $edminboost_is_pro ) {
											$edminboost_cell_classes .= ' is-pro-locked';
										}
										?>
										<td
											class="<?php echo esc_attr( $edminboost_cell_classes ); ?>"
											data-item-slug="<?php echo esc_attr( $edminboost_item_slug ); ?>"
										>
											<label class="edminboost-role-matrix__check-label" for="<?php echo esc_attr( $edminboost_field_id ); ?>">
												<span class="screen-reader-text">
													<?php
													if ( $edminboost_is_submenu && '' !== $edminboost_parent_label ) {
														echo esc_html(
															sprintf(
																/* translators: 1: role name, 2: parent menu label, 3: submenu label */
																__( 'Show %2$s › %3$s for %1$s', EDMINBOOST_TEXT_DOMAIN ),
																$edminboost_role_label,
																$edminboost_parent_label,
																$edminboost_item_label
															)
														);
													} else {
														echo esc_html(
															sprintf(
																/* translators: 1: role name, 2: item label */
																__( 'Show %2$s for %1$s', EDMINBOOST_TEXT_DOMAIN ),
																$edminboost_role_label,
																$edminboost_item_label
															)
														);
													}
													?>
												</span>
												<input
													type="checkbox"
													class="edminboost-role-visibility-checkbox"
													id="<?php echo esc_attr( $edminboost_field_id ); ?>"
													name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][role_visibility][<?php echo esc_attr( $edminboost_role_key ); ?>][]"
													value="<?php echo esc_attr( $edminboost_item_slug ); ?>"
													data-item-slug="<?php echo esc_attr( $edminboost_item_slug ); ?>"
													<?php checked( $edminboost_is_checked ); ?>
													<?php disabled( $edminboost_is_protected ); ?>
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
					<?php esc_html_e( 'Checked menu items stay visible in the top bar and sidebar for that role. Uncheck to hide tools from clients or editors. Submenu columns control individual pages under a parent menu. Items not included in the assigned preset start unchecked; you can still enable them manually. Items this role cannot access by default appear unchecked—you may enable them if needed.', EDMINBOOST_TEXT_DOMAIN ); ?>
				</p>
			<?php endif; ?>
		</section>

		<?php
		$edminboost_save_label = __( 'Save presets', EDMINBOOST_TEXT_DOMAIN );
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-form-actions.php';
		?>
	</form>
</div>
