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
$edminboost_has_layout = ! empty( $edminboost_top_bar_items );
$edminboost_has_matrix = ! empty( $edminboost_matrix_items );
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap edminboost-cc-wrap--wide">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero edminboost-cc-hero--split">
		<div>
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p class="edminboost-cc-hero__lead">
				<?php esc_html_e( 'Choose a layout template—or reuse one you saved—to set up the top bar and sidebar menu, then control which admin menu items each user role can see.', 'edminboost-admin-customization' ); ?>
			</p>
		</div>
		<?php
		$edminboost_allows_custom_presets = ! EDMINBOOST_Plan::is_direct_build();
		if ( EDMINBOOST_Plan::is_direct_build() ) {
			$edminboost_allows_custom_presets = (bool) apply_filters( 'edminboost_allows_custom_preset_actions', false );
		}
		?>
		<?php if ( $edminboost_allows_custom_presets ) : ?>
			<div class="edminboost-cc-hero__actions">
				<div class="edminboost-save-preset" id="edminboost-save-preset">
					<button type="button" class="button edminboost-save-preset__trigger" id="edminboost-save-preset-btn" <?php disabled( ! $edminboost_has_layout ); ?>>
						<?php esc_html_e( 'Save current layout as preset', 'edminboost-admin-customization' ); ?>
					</button>
					<div class="edminboost-save-preset__form" id="edminboost-save-preset-form">
						<label class="edminboost-save-preset__label" for="edminboost_save_preset_name_input">
							<?php esc_html_e( 'Preset name', 'edminboost-admin-customization' ); ?>
						</label>
						<input
							type="text"
							class="regular-text edminboost-save-preset__input"
							id="edminboost_save_preset_name_input"
							placeholder="<?php echo esc_attr__( 'Enter a name for your preset', 'edminboost-admin-customization' ); ?>"
							autocomplete="off"
						/>
						<button type="button" class="button button-primary" id="edminboost-save-preset-confirm-btn">
							<?php esc_html_e( 'Save preset', 'edminboost-admin-customization' ); ?>
						</button>
						<button type="button" class="button" id="edminboost-save-preset-cancel-btn">
							<?php esc_html_e( 'Cancel', 'edminboost-admin-customization' ); ?>
						</button>
					</div>
				</div>
			</div>
		<?php endif; ?>
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
			<h2 id="edminboost-roles-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'role_visibility' ); ?><?php esc_html_e( 'Who sees what', 'edminboost-admin-customization' ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Assign a layout preset per role to customize both the top bar and admin sidebar for that role.', 'edminboost-admin-customization' ); ?>
			</p>

			<?php if ( empty( $edminboost_roles ) ) : ?>
				<p><?php esc_html_e( 'No roles available.', 'edminboost-admin-customization' ); ?></p>
			<?php elseif ( ! $edminboost_has_matrix ) : ?>
				<p><?php esc_html_e( 'No admin menu items were discovered. Try reloading this page from wp-admin.', 'edminboost-admin-customization' ); ?></p>
			<?php else : ?>
				<div class="edminboost-role-matrix-wrap">
					<table class="widefat edminboost-role-matrix">
						<thead>
							<tr>
								<th scope="col" class="edminboost-role-matrix__role-col"><?php esc_html_e( 'User role', 'edminboost-admin-customization' ); ?></th>
								<th scope="col" class="edminboost-role-matrix__preset-col">
									<?php EDMINBOOST_Setting_Help::echo_icon( 'role_assignments' ); ?><?php esc_html_e( 'Assigned preset', 'edminboost-admin-customization' ); ?>
								</th>
								<?php
								if ( EDMINBOOST_Plan::is_direct_build() ) {
									do_action( 'edminboost_admin_extension', 'presets_role_matrix_head' );
								}
								?>
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
											echo esc_html( sprintf( __( 'Preset for %s', 'edminboost-admin-customization' ), $edminboost_role_label ) );
											?>
										</label>
										<select
											class="edminboost-role-preset-select"
											name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][role_assignments][<?php echo esc_attr( $edminboost_role_key ); ?>]"
											id="edminboost_role_preset_<?php echo esc_attr( $edminboost_role_key ); ?>"
											data-edminboost-role="<?php echo esc_attr( $edminboost_role_key ); ?>"
										>
											<option value=""><?php esc_html_e( '— Use site default —', 'edminboost-admin-customization' ); ?></option>
											<?php foreach ( $edminboost_all_presets as $edminboost_preset_id => $edminboost_preset ) : ?>
												<?php
												if ( ! EDMINBOOST_Plan::include_preset_in_ui( $edminboost_preset_id, 'layout' ) ) {
													continue;
												}
												?>
												<option
													value="<?php echo esc_attr( $edminboost_preset_id ); ?>"
													<?php selected( isset( $edminboost_role_assignments[ $edminboost_role_key ] ) ? $edminboost_role_assignments[ $edminboost_role_key ] : '', $edminboost_preset_id ); ?>
												>
													<?php echo esc_html( isset( $edminboost_preset['name'] ) ? $edminboost_preset['name'] : $edminboost_preset_id ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</td>
									<?php
									if ( EDMINBOOST_Plan::is_direct_build() ) {
										do_action( 'edminboost_admin_extension', 'presets_role_matrix_cells' );
									}
									?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php
				if ( EDMINBOOST_Plan::is_direct_build() ) {
					do_action( 'edminboost_admin_extension', 'presets_role_visibility_help' );
				}
				?>
			<?php endif; ?>
		</section>

		<?php
		$edminboost_save_label = __( 'Save presets', 'edminboost-admin-customization' );
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-form-actions.php';
		?>
	</form>
</div>
