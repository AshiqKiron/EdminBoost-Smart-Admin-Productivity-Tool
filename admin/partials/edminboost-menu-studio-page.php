<?php
/**
 * Menu Studio — reorder wp-admin sidebar and customize menu colors.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings  Command Center settings.
 * @var string $current_page   Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_option_name   = EDMINBOOST_Settings::OPTION_NAME;
$edminboost_menu_studio   = isset( $cc_settings['menu_studio'] ) && is_array( $cc_settings['menu_studio'] )
	? wp_parse_args( $cc_settings['menu_studio'], EDMINBOOST_Command_Center::get_menu_studio_defaults() )
	: EDMINBOOST_Command_Center::get_menu_studio_defaults();
$edminboost_ms_key        = $edminboost_option_name . '[command_center][menu_studio]';
$edminboost_menu_tree     = EDMINBOOST_Command_Center::get_discovered_menu_tree();
$edminboost_canvas_items  = EDMINBOOST_Command_Center::resolve_menu_studio_order( $edminboost_menu_studio );
$edminboost_hidden_items  = isset( $edminboost_menu_studio['hidden_items'] ) && is_array( $edminboost_menu_studio['hidden_items'] )
	? $edminboost_menu_studio['hidden_items']
	: array();
$edminboost_menu_studio_colors        = isset( $edminboost_menu_studio['colors'] ) && is_array( $edminboost_menu_studio['colors'] )
	? wp_parse_args( $edminboost_menu_studio['colors'], EDMINBOOST_Command_Center::get_menu_studio_defaults()['colors'] )
	: EDMINBOOST_Command_Center::get_menu_studio_defaults()['colors'];
$edminboost_custom_items  = isset( $edminboost_menu_studio['custom_items'] ) && is_array( $edminboost_menu_studio['custom_items'] )
	? $edminboost_menu_studio['custom_items']
	: array();
$edminboost_protected     = EDMINBOOST_Menu_Studio::get_protected_slugs();

$edminboost_color_fields = array(
	'parent_bg'         => array(
		'label'       => __( 'Parent background', 'edminboost-smart-admin-productivity-tool' ),
		'placeholder' => '#1d2327',
		'default'     => '#1d2327',
	),
	'parent_text'       => array(
		'label'       => __( 'Parent text', 'edminboost-smart-admin-productivity-tool' ),
		'placeholder' => '#f0f0f1',
		'default'     => '#f0f0f1',
	),
	'parent_active'     => array(
		'label'       => __( 'Parent hover / active', 'edminboost-smart-admin-productivity-tool' ),
		'placeholder' => '#2271b1',
		'default'     => '#2271b1',
	),
	'submenu_bg'        => array(
		'label'       => __( 'Submenu background', 'edminboost-smart-admin-productivity-tool' ),
		'placeholder' => '#2c3338',
		'default'     => '#2c3338',
	),
	'submenu_text'      => array(
		'label'       => __( 'Submenu text', 'edminboost-smart-admin-productivity-tool' ),
		'placeholder' => '#c3c4c7',
		'default'     => '#c3c4c7',
	),
	'submenu_hover_text' => array(
		'label'       => __( 'Submenu hover text', 'edminboost-smart-admin-productivity-tool' ),
		'placeholder' => '#ffffff',
		'default'     => '#ffffff',
	),
	'notification_bg'   => array(
		'label'       => __( 'Notification background', 'edminboost-smart-admin-productivity-tool' ),
		'placeholder' => '#d63638',
		'default'     => '#d63638',
	),
	'notification_text' => array(
		'label'       => __( 'Notification text', 'edminboost-smart-admin-productivity-tool' ),
		'placeholder' => '#ffffff',
		'default'     => '#ffffff',
	),
);
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap edminboost-cc-wrap--wide">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Reorder the WordPress admin sidebar, add custom links, and style parent menus, submenus, and update badges.', 'edminboost-smart-admin-productivity-tool' ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form" id="edminboost-menu-studio-form" data-edminboost-custom-items="<?php echo esc_attr( wp_json_encode( array_values( $edminboost_custom_items ) ) ); ?>">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_menu_studio_save]" value="1" />

		<p class="edminboost-menu-studio-enable">
			<label for="edminboost_menu_studio_enabled">
				<input type="hidden" name="<?php echo esc_attr( $edminboost_ms_key ); ?>[enabled]" value="0" />
				<input
					type="checkbox"
					id="edminboost_menu_studio_enabled"
					name="<?php echo esc_attr( $edminboost_ms_key ); ?>[enabled]"
					value="1"
					<?php checked( ! empty( $edminboost_menu_studio['enabled'] ) ); ?>
				/>
				<?php EDMINBOOST_Setting_Help::echo_icon( 'menu_studio_enabled' ); ?>
				<?php esc_html_e( 'Enable Menu Studio on all admin screens', 'edminboost-smart-admin-productivity-tool' ); ?>
			</label>
		</p>

		<div class="edminboost-menu-studio-layout">
			<aside class="edminboost-card edminboost-menu-studio-panel" aria-labelledby="edminboost-menu-discovered-heading">
				<h2 id="edminboost-menu-discovered-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_discovered' ); ?><?php esc_html_e( 'Admin Menu Items', 'edminboost-smart-admin-productivity-tool' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Toggle visibility or drag items into the sidebar preview.', 'edminboost-smart-admin-productivity-tool' ); ?></p>

				<label for="edminboost-menu-search">
					<?php EDMINBOOST_Setting_Help::echo_icon( 'menu_search' ); ?>
					<?php esc_html_e( 'Filter menu items', 'edminboost-smart-admin-productivity-tool' ); ?>
				</label>
				<input
					type="search"
					id="edminboost-menu-search"
					class="edminboost-mapper-search"
					placeholder="<?php esc_attr_e( 'Search menu items…', 'edminboost-smart-admin-productivity-tool' ); ?>"
					autocomplete="off"
				/>

				<ul class="edminboost-discovered-list edminboost-menu-discovered-list" id="edminboost-menu-discovered-list">
					<?php if ( empty( $edminboost_menu_tree ) ) : ?>
						<li class="edminboost-discovered-list__empty">
							<?php esc_html_e( 'No admin menus detected.', 'edminboost-smart-admin-productivity-tool' ); ?>
						</li>
					<?php else : ?>
						<?php foreach ( $edminboost_menu_tree as $edminboost_index => $edminboost_menu_item ) : ?>
							<?php
							$edminboost_slug         = $edminboost_menu_item['slug'];
							$edminboost_is_hidden    = in_array( $edminboost_slug, $edminboost_hidden_items, true );
							$edminboost_is_protected = in_array( $edminboost_slug, $edminboost_protected, true );
							$edminboost_item_id = 'edminboost_menu_discovered_' . $edminboost_index;
							$edminboost_child_count  = ! empty( $edminboost_menu_item['children'] ) ? count( $edminboost_menu_item['children'] ) : 0;
							?>
							<li
								class="edminboost-discovered-item edminboost-discovered-item--top<?php echo $edminboost_is_hidden ? ' is-hidden-item' : ''; ?>"
								data-slug="<?php echo esc_attr( $edminboost_slug ); ?>"
								data-label="<?php echo esc_attr( $edminboost_menu_item['label'] ); ?>"
								data-icon="<?php echo esc_attr( $edminboost_menu_item['icon'] ); ?>"
								data-child-count="<?php echo esc_attr( (string) $edminboost_child_count ); ?>"
								<?php if ( ! empty( $edminboost_menu_item['children'] ) ) : ?>
									data-children="<?php echo esc_attr( wp_json_encode( $edminboost_menu_item['children'] ) ); ?>"
								<?php endif; ?>
							>
								<span class="edminboost-discovered-item__handle dashicons dashicons-move" aria-hidden="true"></span>
								<span class="edminboost-discovered-item__icon dashicons <?php echo esc_attr( $edminboost_menu_item['icon'] ); ?>" aria-hidden="true"></span>
								<span class="edminboost-discovered-item__label" title="<?php echo esc_attr( $edminboost_menu_item['label'] ); ?>">
									<?php echo esc_html( $edminboost_menu_item['label'] ); ?>
									<?php if ( $edminboost_child_count > 0 ) : ?>
										<span class="edminboost-menu-discovered-item__count">(<?php echo esc_html( (string) $edminboost_child_count ); ?>)</span>
									<?php endif; ?>
								</span>
								<label class="edminboost-discovered-item__toggle" for="<?php echo esc_attr( $edminboost_item_id ); ?>">
									<span class="screen-reader-text">
										<?php
										/* translators: %s: menu item label */
										echo esc_html( sprintf( __( 'Show %s on sidebar', 'edminboost-smart-admin-productivity-tool' ), $edminboost_menu_item['label'] ) );
										?>
									</span>
									<input
										type="checkbox"
										id="<?php echo esc_attr( $edminboost_item_id ); ?>"
										class="edminboost-discovered-item__checkbox"
										<?php checked( ! $edminboost_is_hidden ); ?>
										<?php disabled( $edminboost_is_protected ); ?>
									/>
								</label>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>

				<?php if ( EDMINBOOST_Pro::shows_pro_settings_ui() ) : ?>
				<details class="edminboost-custom-link <?php echo esc_attr( EDMINBOOST_Pro::section_class() ); ?>" id="edminboost-menu-custom-link"<?php EDMINBOOST_Pro::echo_feature_attr( 'menu_custom_links' ); ?>>
					<summary class="edminboost-custom-link__heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_path' ); ?><?php esc_html_e( 'Custom sidebar link', 'edminboost-smart-admin-productivity-tool' ); ?> <?php EDMINBOOST_Pro::render_badge(); ?></summary>
					<div class="edminboost-custom-link__body">
						<p class="description"><?php esc_html_e( 'Add a top-level or submenu link to the admin sidebar.', 'edminboost-smart-admin-productivity-tool' ); ?></p>

						<p>
							<label for="edminboost-menu-custom-path"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_path' ); ?><?php esc_html_e( 'Admin path', 'edminboost-smart-admin-productivity-tool' ); ?></label>
							<input type="text" id="edminboost-menu-custom-path" class="regular-text code" placeholder="<?php echo esc_attr( 'edit.php?post_type=page' ); ?>" autocomplete="off" />
						</p>

						<p>
							<label for="edminboost-menu-custom-label"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_label' ); ?><?php esc_html_e( 'Label', 'edminboost-smart-admin-productivity-tool' ); ?></label>
							<input type="text" id="edminboost-menu-custom-label" class="regular-text" placeholder="<?php esc_attr_e( 'All Pages', 'edminboost-smart-admin-productivity-tool' ); ?>" autocomplete="off" />
						</p>

						<p>
							<label for="edminboost-menu-custom-parent"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_parent' ); ?><?php esc_html_e( 'Parent menu (optional)', 'edminboost-smart-admin-productivity-tool' ); ?></label>
							<select id="edminboost-menu-custom-parent">
								<option value=""><?php esc_html_e( 'Top level', 'edminboost-smart-admin-productivity-tool' ); ?></option>
								<?php foreach ( $edminboost_menu_tree as $edminboost_menu_item ) : ?>
									<option value="<?php echo esc_attr( $edminboost_menu_item['slug'] ); ?>"><?php echo esc_html( $edminboost_menu_item['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</p>

						<p class="edminboost-custom-link__actions">
							<button type="button" class="button button-secondary" id="edminboost-menu-custom-add">
								<?php esc_html_e( 'Add to sidebar', 'edminboost-smart-admin-productivity-tool' ); ?>
							</button>
						</p>

						<p class="edminboost-custom-link__error description" id="edminboost-menu-custom-error" hidden role="alert"></p>
						<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-pro-upgrade.php'; ?>
					</div>
				</details>
				<?php endif; ?>
			</aside>

			<div class="edminboost-menu-studio-main">
				<section class="edminboost-card edminboost-menu-studio-panel" aria-labelledby="edminboost-menu-canvas-heading">
					<h2 id="edminboost-menu-canvas-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_canvas' ); ?><?php esc_html_e( 'Sidebar Preview', 'edminboost-smart-admin-productivity-tool' ); ?></h2>
					<p class="description"><?php esc_html_e( 'Drag to reorder top-level items. Expand a parent to reorder its submenus.', 'edminboost-smart-admin-productivity-tool' ); ?></p>

					<div class="edminboost-sidebar-canvas" id="edminboost-sidebar-canvas" role="list" aria-label="<?php esc_attr_e( 'Sidebar preview', 'edminboost-smart-admin-productivity-tool' ); ?>">
						<ul class="edminboost-sidebar-canvas__items" id="edminboost-sidebar-items">
							<?php foreach ( $edminboost_canvas_items as $edminboost_item ) : ?>
								<?php
								$edminboost_slug      = isset( $edminboost_item['slug'] ) ? $edminboost_item['slug'] : '';
								$edminboost_label     = isset( $edminboost_item['label'] ) ? $edminboost_item['label'] : $edminboost_slug;
								$edminboost_icon      = isset( $edminboost_item['icon'] ) ? $edminboost_item['icon'] : 'dashicons-admin-generic';
								$edminboost_children  = isset( $edminboost_item['children'] ) && is_array( $edminboost_item['children'] ) ? $edminboost_item['children'] : array();
								$edminboost_is_custom = ! empty( $edminboost_item['custom'] );
								$edminboost_custom_path = '';
								if ( $edminboost_is_custom ) {
									$edminboost_custom_id = str_replace( 'edminboost_ms_', '', $edminboost_slug );
									foreach ( $edminboost_custom_items as $edminboost_custom_item ) {
										if ( isset( $edminboost_custom_item['id'] ) && $edminboost_custom_item['id'] === $edminboost_custom_id ) {
											$edminboost_custom_path = isset( $edminboost_custom_item['path'] ) ? $edminboost_custom_item['path'] : '';
											break;
										}
									}
								}
								?>
								<li
									class="edminboost-sidebar-item<?php echo $edminboost_is_custom ? ' is-custom' : ''; ?>"
									role="listitem"
									draggable="true"
									data-slug="<?php echo esc_attr( $edminboost_slug ); ?>"
									data-label="<?php echo esc_attr( $edminboost_label ); ?>"
									data-icon="<?php echo esc_attr( $edminboost_icon ); ?>"
									<?php if ( $edminboost_is_custom ) : ?>
										data-custom="1"
										data-path="<?php echo esc_attr( $edminboost_custom_path ); ?>"
									<?php endif; ?>
									<?php if ( ! empty( $edminboost_children ) ) : ?>
										data-children="<?php echo esc_attr( wp_json_encode( $edminboost_children ) ); ?>"
									<?php endif; ?>
								>
									<div class="edminboost-sidebar-item__row">
										<span class="edminboost-sidebar-item__handle dashicons dashicons-move" aria-hidden="true"></span>
										<span class="edminboost-sidebar-item__icon dashicons <?php echo esc_attr( $edminboost_icon ); ?>" aria-hidden="true"></span>
										<span class="edminboost-sidebar-item__label"><?php echo esc_html( $edminboost_label ); ?></span>
										<?php if ( ! empty( $edminboost_children ) ) : ?>
											<button type="button" class="edminboost-sidebar-item__expand" aria-expanded="false" aria-label="<?php esc_attr_e( 'Expand submenu', 'edminboost-smart-admin-productivity-tool' ); ?>">
												<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
											</button>
										<?php endif; ?>
										<span class="edminboost-sidebar-item__badge" aria-hidden="true">2</span>
									</div>
									<?php if ( ! empty( $edminboost_children ) ) : ?>
										<ul class="edminboost-sidebar-subitems" hidden>
											<?php foreach ( $edminboost_children as $edminboost_child ) : ?>
												<li
													class="edminboost-sidebar-subitem"
													draggable="true"
													data-slug="<?php echo esc_attr( $edminboost_child['slug'] ); ?>"
													data-label="<?php echo esc_attr( $edminboost_child['label'] ); ?>"
													data-parent="<?php echo esc_attr( $edminboost_slug ); ?>"
												>
													<span class="edminboost-sidebar-subitem__handle dashicons dashicons-move" aria-hidden="true"></span>
													<span class="edminboost-sidebar-subitem__label"><?php echo esc_html( $edminboost_child['label'] ); ?></span>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<p class="edminboost-sidebar-canvas__hint description" id="edminboost-menu-canvas-empty" <?php echo ! empty( $edminboost_canvas_items ) ? 'hidden' : ''; ?>>
						<?php esc_html_e( 'Drag menu items here to reorder your admin sidebar.', 'edminboost-smart-admin-productivity-tool' ); ?>
					</p>
				</section>

				<section class="edminboost-card edminboost-menu-studio-panel edminboost-menu-layout-panel" aria-labelledby="edminboost-menu-styles-heading">
					<h2 id="edminboost-menu-styles-heading"><?php esc_html_e( 'Sidebar layout & typography', 'edminboost-smart-admin-productivity-tool' ); ?></h2>
					<p class="description"><?php esc_html_e( 'Adjust sidebar width, typography, and how menu icons and labels appear in wp-admin.', 'edminboost-smart-admin-productivity-tool' ); ?></p>
					<div class="edminboost-menu-layout-panel__grid">
						<div class="edminboost-menu-layout-panel__fields">
							<div class="edminboost-menu-layout-fields">
								<div class="edminboost-menu-layout-row">
									<label for="edminboost_menu_width"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_width' ); ?><?php esc_html_e( 'Menu width (px)', 'edminboost-smart-admin-productivity-tool' ); ?></label>
									<div class="edminboost-menu-layout-control">
										<input
											type="range"
											class="edminboost-menu-layout-range"
											id="edminboost_menu_width_range"
											min="120"
											max="300"
											step="1"
											value="<?php echo esc_attr( $edminboost_menu_studio['menu_width'] ?? 160 ); ?>"
											aria-describedby="edminboost_menu_width"
										/>
										<input type="number" class="small-text edminboost-menu-layout-input" id="edminboost_menu_width" name="<?php echo esc_attr( $edminboost_ms_key ); ?>[menu_width]" value="<?php echo esc_attr( $edminboost_menu_studio['menu_width'] ?? 160 ); ?>" min="120" max="300" />
										<span class="edminboost-menu-layout-unit">px</span>
									</div>
								</div>

								<fieldset class="edminboost-menu-layout-typography">
									<legend><?php esc_html_e( 'Typography', 'edminboost-smart-admin-productivity-tool' ); ?></legend>
									<div class="edminboost-menu-layout-typography__grid">
										<div class="edminboost-menu-layout-field">
											<label for="edminboost_menu_font_size"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_font_size' ); ?><?php esc_html_e( 'Font size (px)', 'edminboost-smart-admin-productivity-tool' ); ?></label>
											<div class="edminboost-menu-layout-control">
												<input
													type="range"
													class="edminboost-menu-layout-range"
													id="edminboost_menu_font_size_range"
													min="10"
													max="24"
													step="1"
													value="<?php echo esc_attr( $edminboost_menu_studio['font_size'] ?? 14 ); ?>"
													aria-describedby="edminboost_menu_font_size"
												/>
												<input type="number" class="small-text edminboost-menu-layout-input" id="edminboost_menu_font_size" name="<?php echo esc_attr( $edminboost_ms_key ); ?>[font_size]" value="<?php echo esc_attr( $edminboost_menu_studio['font_size'] ?? 14 ); ?>" min="10" max="24" />
												<span class="edminboost-menu-layout-unit">px</span>
											</div>
										</div>
										<div class="edminboost-menu-layout-field">
											<label for="edminboost_menu_line_height"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_line_height' ); ?><?php esc_html_e( 'Line height (px)', 'edminboost-smart-admin-productivity-tool' ); ?></label>
											<div class="edminboost-menu-layout-control">
												<input
													type="range"
													class="edminboost-menu-layout-range"
													id="edminboost_menu_line_height_range"
													min="12"
													max="36"
													step="1"
													value="<?php echo esc_attr( $edminboost_menu_studio['line_height'] ?? 20 ); ?>"
													aria-describedby="edminboost_menu_line_height"
												/>
												<input type="number" class="small-text edminboost-menu-layout-input" id="edminboost_menu_line_height" name="<?php echo esc_attr( $edminboost_ms_key ); ?>[line_height]" value="<?php echo esc_attr( $edminboost_menu_studio['line_height'] ?? 20 ); ?>" min="12" max="36" />
												<span class="edminboost-menu-layout-unit">px</span>
											</div>
										</div>
										<div class="edminboost-menu-layout-field">
											<label for="edminboost_menu_letter_spacing"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_letter_spacing' ); ?><?php esc_html_e( 'Letter spacing (px)', 'edminboost-smart-admin-productivity-tool' ); ?></label>
											<div class="edminboost-menu-layout-control">
												<input
													type="range"
													class="edminboost-menu-layout-range"
													id="edminboost_menu_letter_spacing_range"
													min="-2"
													max="6"
													step="1"
													value="<?php echo esc_attr( $edminboost_menu_studio['letter_spacing'] ?? 0 ); ?>"
													aria-describedby="edminboost_menu_letter_spacing"
												/>
												<input type="number" class="small-text edminboost-menu-layout-input" id="edminboost_menu_letter_spacing" name="<?php echo esc_attr( $edminboost_ms_key ); ?>[letter_spacing]" value="<?php echo esc_attr( $edminboost_menu_studio['letter_spacing'] ?? 0 ); ?>" min="-2" max="6" />
												<span class="edminboost-menu-layout-unit">px</span>
											</div>
										</div>
									</div>
								</fieldset>

								<?php if ( EDMINBOOST_Pro::shows_pro_settings_ui() ) : ?>
								<div class="edminboost-menu-layout-row <?php echo esc_attr( EDMINBOOST_Pro::section_class() ); ?>"<?php EDMINBOOST_Pro::echo_feature_attr( 'menu_display_mode' ); ?>>
									<label for="edminboost_menu_display_mode"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_display_mode' ); ?><?php esc_html_e( 'Menu item display', 'edminboost-smart-admin-productivity-tool' ); ?> <?php EDMINBOOST_Pro::render_badge(); ?></label>
									<select id="edminboost_menu_display_mode" class="edminboost-menu-layout-select" name="<?php echo esc_attr( $edminboost_ms_key ); ?>[display_mode]">
										<option value="both" <?php selected( $edminboost_menu_studio['display_mode'] ?? 'both', 'both' ); ?>><?php esc_html_e( 'Icon and text', 'edminboost-smart-admin-productivity-tool' ); ?></option>
										<option value="icon" <?php selected( $edminboost_menu_studio['display_mode'] ?? 'both', 'icon' ); ?>><?php esc_html_e( 'Icon only', 'edminboost-smart-admin-productivity-tool' ); ?></option>
										<option value="text" <?php selected( $edminboost_menu_studio['display_mode'] ?? 'both', 'text' ); ?>><?php esc_html_e( 'Text only', 'edminboost-smart-admin-productivity-tool' ); ?></option>
									</select>
									<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-pro-upgrade.php'; ?>
								</div>
								<?php endif; ?>
							</div>
						</div>

						<div class="edminboost-menu-layout-preview" id="edminboost-menu-layout-preview" aria-live="polite">
							<p class="edminboost-menu-layout-preview__lead description"><?php esc_html_e( 'Preview how sidebar width, typography, and icon display will look in wp-admin.', 'edminboost-smart-admin-productivity-tool' ); ?></p>
							<div class="edminboost-menu-layout-preview__sidebar">
								<div class="edminboost-menu-layout-preview__item is-active">
									<span class="edminboost-menu-layout-preview__icon dashicons dashicons-admin-post" aria-hidden="true"></span>
									<span class="edminboost-menu-layout-preview__label"><?php esc_html_e( 'Posts', 'edminboost-smart-admin-productivity-tool' ); ?></span>
									<span class="edminboost-menu-layout-preview__badge" aria-hidden="true">3</span>
								</div>
								<div class="edminboost-menu-layout-preview__item">
									<span class="edminboost-menu-layout-preview__icon dashicons dashicons-admin-media" aria-hidden="true"></span>
									<span class="edminboost-menu-layout-preview__label"><?php esc_html_e( 'Media', 'edminboost-smart-admin-productivity-tool' ); ?></span>
								</div>
								<div class="edminboost-menu-layout-preview__item">
									<span class="edminboost-menu-layout-preview__icon dashicons dashicons-admin-page" aria-hidden="true"></span>
									<span class="edminboost-menu-layout-preview__label"><?php esc_html_e( 'Pages', 'edminboost-smart-admin-productivity-tool' ); ?></span>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section class="edminboost-card edminboost-menu-studio-panel edminboost-menu-studio-colors" aria-labelledby="edminboost-menu-colors-heading">
					<h2 id="edminboost-menu-colors-heading"><?php esc_html_e( 'Sidebar Colors', 'edminboost-smart-admin-productivity-tool' ); ?></h2>
					<p class="description"><?php esc_html_e( 'Customize parent menu, submenu, and notification badge colors across wp-admin.', 'edminboost-smart-admin-productivity-tool' ); ?></p>
					<input type="hidden" name="<?php echo esc_attr( $edminboost_ms_key ); ?>[use_colors]" value="1" />

					<div class="edminboost-menu-colors-panel__grid" id="edminboost-menu-colors-panel">
						<div class="edminboost-menu-colors-panel__fields">
							<div class="edminboost-menu-color-grid" id="edminboost-menu-color-grid">
								<?php foreach ( $edminboost_color_fields as $edminboost_color_key => $edminboost_color_meta ) : ?>
									<?php
									$edminboost_field_id   = 'edminboost_menu_color_' . $edminboost_color_key;
									$edminboost_picker_id  = $edminboost_field_id . '_picker';
									$edminboost_value      = isset( $edminboost_menu_studio_colors[ $edminboost_color_key ] ) ? $edminboost_menu_studio_colors[ $edminboost_color_key ] : '';
									$edminboost_picker_val = $edminboost_value ? $edminboost_value : $edminboost_color_meta['default'];
									?>
									<div class="edminboost-menu-color-row" data-color-key="<?php echo esc_attr( $edminboost_color_key ); ?>">
										<label for="<?php echo esc_attr( $edminboost_field_id ); ?>"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_color_' . $edminboost_color_key ); ?><?php echo esc_html( $edminboost_color_meta['label'] ); ?></label>
										<span class="edminboost-menu-color-controls">
											<input type="color" id="<?php echo esc_attr( $edminboost_picker_id ); ?>" value="<?php echo esc_attr( $edminboost_picker_val ); ?>" data-target="<?php echo esc_attr( $edminboost_field_id ); ?>" aria-label="<?php echo esc_attr( $edminboost_color_meta['label'] ); ?>" />
											<input
												type="text"
												class="small-text edminboost-menu-color-input"
												id="<?php echo esc_attr( $edminboost_field_id ); ?>"
												name="<?php echo esc_attr( $edminboost_ms_key ); ?>[colors][<?php echo esc_attr( $edminboost_color_key ); ?>]"
												value="<?php echo esc_attr( $edminboost_value ); ?>"
												placeholder="<?php echo esc_attr( $edminboost_color_meta['placeholder'] ); ?>"
												pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
											/>
										</span>
									</div>
								<?php endforeach; ?>
							</div>
						</div>

						<div class="edminboost-menu-color-preview-panel">
							<p class="edminboost-menu-color-preview-panel__lead description"><?php esc_html_e( 'Preview how parent menu, submenu, and badge colors will look in wp-admin.', 'edminboost-smart-admin-productivity-tool' ); ?></p>
							<div class="edminboost-menu-color-preview" id="edminboost-menu-color-preview" aria-live="polite">
								<div class="edminboost-menu-color-preview__parent is-active">
									<span class="dashicons dashicons-admin-post" aria-hidden="true"></span>
									<span><?php esc_html_e( 'Posts', 'edminboost-smart-admin-productivity-tool' ); ?></span>
									<span class="edminboost-menu-color-preview__badge">3</span>
								</div>
								<ul class="edminboost-menu-color-preview__submenu">
									<li class="is-active"><?php esc_html_e( 'All Posts', 'edminboost-smart-admin-productivity-tool' ); ?></li>
									<li><?php esc_html_e( 'Add New', 'edminboost-smart-admin-productivity-tool' ); ?></li>
								</ul>
								<div class="edminboost-menu-color-preview__parent">
									<span class="dashicons dashicons-admin-media" aria-hidden="true"></span>
									<span><?php esc_html_e( 'Media', 'edminboost-smart-admin-productivity-tool' ); ?></span>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>

		<div id="edminboost-menu-hidden-inputs" hidden aria-hidden="true"></div>

		<?php
		$edminboost_save_label    = __( 'Save Menu Studio', 'edminboost-smart-admin-productivity-tool' );
		$edminboost_wrapper_tag   = 'footer';
		$edminboost_wrapper_class = 'edminboost-cc-footer edminboost-form-actions';
		include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-form-actions.php';
		?>
	</form>
</div>
