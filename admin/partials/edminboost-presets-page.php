<?php
/**
 * Presets & Role Assignment Manager.
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
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap edminboost-cc-wrap--wide">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero edminboost-cc-hero--split">
		<div>
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p class="edminboost-cc-hero__lead">
				<?php esc_html_e( 'Save layout templates and control which top-bar icons each user role can see.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
		</div>
		<div class="edminboost-cc-hero__actions">
			<button type="button" class="button" id="edminboost-save-preset-btn" disabled>
				<?php esc_html_e( 'Save Current Layout as New Preset', EDMINBOOST_TEXT_DOMAIN ); ?>
			</button>
		</div>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-presets-heading">
			<h2 id="edminboost-presets-heading"><?php esc_html_e( 'Active Presets Library', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'System defaults and your custom saved templates.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>

			<div class="edminboost-preset-grid">
				<?php foreach ( $all_presets as $preset_id => $preset ) : ?>
					<?php
					$is_system   = ! empty( $preset['system'] );
					$is_default  = ( $default_preset === $preset_id );
					$preset_name = isset( $preset['name'] ) ? $preset['name'] : $preset_id;
					$preset_desc = isset( $preset['description'] ) ? $preset['description'] : '';
					?>
					<article class="edminboost-preset-card<?php echo $is_default ? ' is-default' : ''; ?>">
						<header class="edminboost-preset-card__header">
							<h3 class="edminboost-preset-card__title"><?php echo esc_html( $preset_name ); ?></h3>
							<?php if ( $is_system ) : ?>
								<span class="edminboost-preset-card__badge"><?php esc_html_e( 'System', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							<?php else : ?>
								<span class="edminboost-preset-card__badge edminboost-preset-card__badge--custom"><?php esc_html_e( 'Custom', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							<?php endif; ?>
							<?php if ( $is_default ) : ?>
								<span class="edminboost-preset-card__default"><?php esc_html_e( 'Default', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							<?php endif; ?>
						</header>
						<p class="edminboost-preset-card__desc"><?php echo esc_html( $preset_desc ); ?></p>
						<div class="edminboost-preset-card__actions">
							<label class="screen-reader-text" for="edminboost_preset_default_<?php echo esc_attr( $preset_id ); ?>">
								<?php esc_html_e( 'Set as default preset', EDMINBOOST_TEXT_DOMAIN ); ?>
							</label>
							<label class="edminboost-checkbox-row">
								<input
									type="radio"
									name="<?php echo esc_attr( $option_name ); ?>[command_center][default_preset]"
									id="edminboost_preset_default_<?php echo esc_attr( $preset_id ); ?>"
									value="<?php echo esc_attr( $preset_id ); ?>"
									<?php checked( $is_default ); ?>
								/>
								<?php esc_html_e( 'Set as default', EDMINBOOST_TEXT_DOMAIN ); ?>
							</label>
							<?php if ( ! $is_system ) : ?>
								<button type="button" class="button button-small edminboost-preset-duplicate" data-preset-id="<?php echo esc_attr( $preset_id ); ?>">
									<?php esc_html_e( 'Duplicate', EDMINBOOST_TEXT_DOMAIN ); ?>
								</button>
							<?php endif; ?>
							<button type="button" class="button button-small edminboost-preset-export" data-preset-id="<?php echo esc_attr( $preset_id ); ?>">
								<?php esc_html_e( 'Export JSON', EDMINBOOST_TEXT_DOMAIN ); ?>
							</button>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-roles-heading">
			<h2 id="edminboost-roles-heading"><?php esc_html_e( 'Role-Based Visibility Matrix', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Assign a preset per role and hide specific top-bar icons from selected roles.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>

			<?php if ( empty( $roles ) ) : ?>
				<p><?php esc_html_e( 'No roles available.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
			<?php else : ?>
				<div class="edminboost-role-matrix-wrap">
					<table class="widefat edminboost-role-matrix">
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e( 'User Role', EDMINBOOST_TEXT_DOMAIN ); ?></th>
								<th scope="col"><?php esc_html_e( 'Assigned Preset', EDMINBOOST_TEXT_DOMAIN ); ?></th>
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
											<label for="<?php echo esc_attr( $field_id ); ?>">
												<span class="screen-reader-text">
													<?php
													/* translators: 1: role name, 2: item label */
													echo esc_html( sprintf( __( 'Show %2$s for %1$s', EDMINBOOST_TEXT_DOMAIN ), $role_name, isset( $item['label'] ) ? $item['label'] : $item_slug ) );
													?>
												</span>
												<input
													type="checkbox"
													id="<?php echo esc_attr( $field_id ); ?>"
													name="<?php echo esc_attr( $option_name ); ?>[command_center][role_visibility][<?php echo esc_attr( $role_key ); ?>][]"
													value="<?php echo esc_attr( $item_slug ); ?>"
													<?php checked( ! $is_hidden ); ?>
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
					<?php esc_html_e( 'Checked icons are visible for that role. Uncheck to hide developer or sensitive tools from clients.', EDMINBOOST_TEXT_DOMAIN ); ?>
				</p>
			<?php endif; ?>
		</section>

		<p class="submit">
			<?php submit_button( __( 'Save Presets & Roles', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
		</p>
	</form>
</div>
