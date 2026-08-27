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

$option_name   = EDMINBOOST_Settings::OPTION_NAME;
$menu_studio   = isset( $cc_settings['menu_studio'] ) && is_array( $cc_settings['menu_studio'] )
	? wp_parse_args( $cc_settings['menu_studio'], EDMINBOOST_Command_Center::get_menu_studio_defaults() )
	: EDMINBOOST_Command_Center::get_menu_studio_defaults();
$ms_key        = $option_name . '[command_center][menu_studio]';
$menu_tree     = EDMINBOOST_Command_Center::get_discovered_menu_tree();
$canvas_items  = EDMINBOOST_Command_Center::resolve_menu_studio_order( $menu_studio );
$hidden_items  = isset( $menu_studio['hidden_items'] ) && is_array( $menu_studio['hidden_items'] )
	? $menu_studio['hidden_items']
	: array();
$colors        = isset( $menu_studio['colors'] ) && is_array( $menu_studio['colors'] )
	? wp_parse_args( $menu_studio['colors'], EDMINBOOST_Command_Center::get_menu_studio_defaults()['colors'] )
	: EDMINBOOST_Command_Center::get_menu_studio_defaults()['colors'];
$custom_items  = isset( $menu_studio['custom_items'] ) && is_array( $menu_studio['custom_items'] )
	? $menu_studio['custom_items']
	: array();
$protected     = EDMINBOOST_Menu_Studio::get_protected_slugs();

$color_fields = array(
	'parent_bg'         => array(
		'label'       => __( 'Parent background', EDMINBOOST_TEXT_DOMAIN ),
		'placeholder' => '#1d2327',
		'default'     => '#1d2327',
	),
	'parent_text'       => array(
		'label'       => __( 'Parent text', EDMINBOOST_TEXT_DOMAIN ),
		'placeholder' => '#f0f0f1',
		'default'     => '#f0f0f1',
	),
	'parent_active'     => array(
		'label'       => __( 'Parent active / hover', EDMINBOOST_TEXT_DOMAIN ),
		'placeholder' => '#2271b1',
		'default'     => '#2271b1',
	),
	'submenu_bg'        => array(
		'label'       => __( 'Submenu background', EDMINBOOST_TEXT_DOMAIN ),
		'placeholder' => '#2c3338',
		'default'     => '#2c3338',
	),
	'submenu_text'      => array(
		'label'       => __( 'Submenu text', EDMINBOOST_TEXT_DOMAIN ),
		'placeholder' => '#c3c4c7',
		'default'     => '#c3c4c7',
	),
	'notification_bg'   => array(
		'label'       => __( 'Notification background', EDMINBOOST_TEXT_DOMAIN ),
		'placeholder' => '#d63638',
		'default'     => '#d63638',
	),
	'notification_text' => array(
		'label'       => __( 'Notification text', EDMINBOOST_TEXT_DOMAIN ),
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
			<?php esc_html_e( 'Reorder the WordPress admin sidebar, add custom links, and style parent menus, submenus, and update badges.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form" id="edminboost-menu-studio-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_menu_studio_save]" value="1" />

		<p class="edminboost-menu-studio-enable">
			<label for="edminboost_menu_studio_enabled">
				<input
					type="checkbox"
					id="edminboost_menu_studio_enabled"
					name="<?php echo esc_attr( $ms_key ); ?>[enabled]"
					value="1"
					<?php checked( ! empty( $menu_studio['enabled'] ) ); ?>
				/>
				<?php EDMINBOOST_Setting_Help::echo_icon( 'menu_studio_enabled' ); ?>
				<?php esc_html_e( 'Enable Menu Studio on all admin screens', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>
		</p>

		<div class="edminboost-menu-studio-layout">
			<aside class="edminboost-card edminboost-menu-studio-panel" aria-labelledby="edminboost-menu-discovered-heading">
				<h2 id="edminboost-menu-discovered-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_discovered' ); ?><?php esc_html_e( 'Admin Menu Items', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
				<p class="description"><?php esc_html_e( 'Toggle visibility or drag items into the sidebar preview.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

				<label for="edminboost-menu-search">
					<?php EDMINBOOST_Setting_Help::echo_icon( 'menu_search' ); ?>
					<?php esc_html_e( 'Filter menu items', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<input
					type="search"
					id="edminboost-menu-search"
					class="edminboost-mapper-search"
					placeholder="<?php esc_attr_e( 'Search menu items…', EDMINBOOST_TEXT_DOMAIN ); ?>"
					autocomplete="off"
				/>

				<ul class="edminboost-discovered-list edminboost-menu-discovered-list" id="edminboost-menu-discovered-list">
					<?php if ( empty( $menu_tree ) ) : ?>
						<li class="edminboost-discovered-list__empty">
							<?php esc_html_e( 'No admin menus detected.', EDMINBOOST_TEXT_DOMAIN ); ?>
						</li>
					<?php else : ?>
						<?php foreach ( $menu_tree as $index => $menu_item ) : ?>
							<?php
							$slug         = $menu_item['slug'];
							$is_hidden    = in_array( $slug, $hidden_items, true );
							$is_protected = in_array( $slug, $protected, true );
							$item_id      = 'edminboost_menu_discovered_' . $index;
							$child_count  = ! empty( $menu_item['children'] ) ? count( $menu_item['children'] ) : 0;
							?>
							<li
								class="edminboost-discovered-item edminboost-discovered-item--top<?php echo $is_hidden ? ' is-hidden-item' : ''; ?>"
								data-slug="<?php echo esc_attr( $slug ); ?>"
								data-label="<?php echo esc_attr( $menu_item['label'] ); ?>"
								data-icon="<?php echo esc_attr( $menu_item['icon'] ); ?>"
								data-child-count="<?php echo esc_attr( (string) $child_count ); ?>"
								<?php if ( ! empty( $menu_item['children'] ) ) : ?>
									data-children="<?php echo esc_attr( wp_json_encode( $menu_item['children'] ) ); ?>"
								<?php endif; ?>
							>
								<span class="edminboost-discovered-item__handle dashicons dashicons-move" aria-hidden="true"></span>
								<span class="edminboost-discovered-item__icon dashicons <?php echo esc_attr( $menu_item['icon'] ); ?>" aria-hidden="true"></span>
								<span class="edminboost-discovered-item__label" title="<?php echo esc_attr( $menu_item['label'] ); ?>">
									<?php echo esc_html( $menu_item['label'] ); ?>
									<?php if ( $child_count > 0 ) : ?>
										<span class="edminboost-menu-discovered-item__count">(<?php echo esc_html( (string) $child_count ); ?>)</span>
									<?php endif; ?>
								</span>
								<label class="edminboost-discovered-item__toggle" for="<?php echo esc_attr( $item_id ); ?>">
									<span class="screen-reader-text">
										<?php
										/* translators: %s: menu item label */
										echo esc_html( sprintf( __( 'Show %s on sidebar', EDMINBOOST_TEXT_DOMAIN ), $menu_item['label'] ) );
										?>
									</span>
									<input
										type="checkbox"
										id="<?php echo esc_attr( $item_id ); ?>"
										class="edminboost-discovered-item__checkbox"
										<?php checked( ! $is_hidden ); ?>
										<?php disabled( $is_protected ); ?>
									/>
								</label>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>

				<details class="edminboost-custom-link" id="edminboost-menu-custom-link">
					<summary class="edminboost-custom-link__heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_path' ); ?><?php esc_html_e( 'Custom sidebar link', EDMINBOOST_TEXT_DOMAIN ); ?></summary>
					<div class="edminboost-custom-link__body">
						<p class="description"><?php esc_html_e( 'Add a top-level or submenu link to the admin sidebar.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

						<p>
							<label for="edminboost-menu-custom-path"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_path' ); ?><?php esc_html_e( 'Admin path', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input type="text" id="edminboost-menu-custom-path" class="regular-text code" placeholder="<?php echo esc_attr( 'edit.php?post_type=page' ); ?>" autocomplete="off" />
						</p>

						<p>
							<label for="edminboost-menu-custom-label"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_label' ); ?><?php esc_html_e( 'Label', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input type="text" id="edminboost-menu-custom-label" class="regular-text" placeholder="<?php esc_attr_e( 'All Pages', EDMINBOOST_TEXT_DOMAIN ); ?>" autocomplete="off" />
						</p>

						<p>
							<label for="edminboost-menu-custom-parent"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_menu_parent' ); ?><?php esc_html_e( 'Parent menu (optional)', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<select id="edminboost-menu-custom-parent">
								<option value=""><?php esc_html_e( 'Top level', EDMINBOOST_TEXT_DOMAIN ); ?></option>
								<?php foreach ( $menu_tree as $menu_item ) : ?>
									<option value="<?php echo esc_attr( $menu_item['slug'] ); ?>"><?php echo esc_html( $menu_item['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</p>

						<p class="edminboost-custom-link__actions">
							<button type="button" class="button button-secondary" id="edminboost-menu-custom-add">
								<?php esc_html_e( 'Add to sidebar', EDMINBOOST_TEXT_DOMAIN ); ?>
							</button>
						</p>

						<p class="edminboost-custom-link__error description" id="edminboost-menu-custom-error" hidden role="alert"></p>
					</div>
				</details>
			</aside>

			<div class="edminboost-menu-studio-main">
				<section class="edminboost-card edminboost-menu-studio-panel" aria-labelledby="edminboost-menu-canvas-heading">
					<h2 id="edminboost-menu-canvas-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_canvas' ); ?><?php esc_html_e( 'Sidebar Preview', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
					<p class="description"><?php esc_html_e( 'Drag to reorder top-level items. Expand a parent to reorder its submenus.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

					<div class="edminboost-sidebar-canvas" id="edminboost-sidebar-canvas" role="list" aria-label="<?php esc_attr_e( 'Sidebar preview', EDMINBOOST_TEXT_DOMAIN ); ?>">
						<ul class="edminboost-sidebar-canvas__items" id="edminboost-sidebar-items">
							<?php foreach ( $canvas_items as $item ) : ?>
								<?php
								$slug      = isset( $item['slug'] ) ? $item['slug'] : '';
								$label     = isset( $item['label'] ) ? $item['label'] : $slug;
								$icon      = isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic';
								$children  = isset( $item['children'] ) && is_array( $item['children'] ) ? $item['children'] : array();
								$is_custom = ! empty( $item['custom'] );
								$custom_path = '';
								if ( $is_custom ) {
									$custom_id = str_replace( 'edminboost_ms_', '', $slug );
									foreach ( $custom_items as $custom_item ) {
										if ( isset( $custom_item['id'] ) && $custom_item['id'] === $custom_id ) {
											$custom_path = isset( $custom_item['path'] ) ? $custom_item['path'] : '';
											break;
										}
									}
								}
								?>
								<li
									class="edminboost-sidebar-item<?php echo $is_custom ? ' is-custom' : ''; ?>"
									role="listitem"
									draggable="true"
									data-slug="<?php echo esc_attr( $slug ); ?>"
									data-label="<?php echo esc_attr( $label ); ?>"
									data-icon="<?php echo esc_attr( $icon ); ?>"
									<?php if ( $is_custom ) : ?>
										data-custom="1"
										data-path="<?php echo esc_attr( $custom_path ); ?>"
									<?php endif; ?>
									<?php if ( ! empty( $children ) ) : ?>
										data-children="<?php echo esc_attr( wp_json_encode( $children ) ); ?>"
									<?php endif; ?>
								>
									<div class="edminboost-sidebar-item__row">
										<span class="edminboost-sidebar-item__handle dashicons dashicons-move" aria-hidden="true"></span>
										<span class="edminboost-sidebar-item__icon dashicons <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
										<span class="edminboost-sidebar-item__label"><?php echo esc_html( $label ); ?></span>
										<?php if ( ! empty( $children ) ) : ?>
											<button type="button" class="edminboost-sidebar-item__expand" aria-expanded="false" aria-label="<?php esc_attr_e( 'Expand submenu', EDMINBOOST_TEXT_DOMAIN ); ?>">
												<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
											</button>
										<?php endif; ?>
										<span class="edminboost-sidebar-item__badge" aria-hidden="true">2</span>
									</div>
									<?php if ( ! empty( $children ) ) : ?>
										<ul class="edminboost-sidebar-subitems" hidden>
											<?php foreach ( $children as $child ) : ?>
												<li
													class="edminboost-sidebar-subitem"
													draggable="true"
													data-slug="<?php echo esc_attr( $child['slug'] ); ?>"
													data-label="<?php echo esc_attr( $child['label'] ); ?>"
													data-parent="<?php echo esc_attr( $slug ); ?>"
												>
													<span class="edminboost-sidebar-subitem__handle dashicons dashicons-move" aria-hidden="true"></span>
													<span class="edminboost-sidebar-subitem__label"><?php echo esc_html( $child['label'] ); ?></span>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<p class="edminboost-sidebar-canvas__hint description" id="edminboost-menu-canvas-empty" <?php echo ! empty( $canvas_items ) ? 'hidden' : ''; ?>>
						<?php esc_html_e( 'Drag menu items here to reorder your admin sidebar.', EDMINBOOST_TEXT_DOMAIN ); ?>
					</p>
				</section>

				<section class="edminboost-card edminboost-menu-studio-panel" aria-labelledby="edminboost-menu-styles-heading">
					<h2 id="edminboost-menu-styles-heading"><?php esc_html_e( 'Sidebar layout & typography', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
					<p>
						<label for="edminboost_menu_width"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_width' ); ?><?php esc_html_e( 'Menu width (px)', EDMINBOOST_TEXT_DOMAIN ); ?>
							<input type="number" class="small-text" id="edminboost_menu_width" name="<?php echo esc_attr( $ms_key ); ?>[menu_width]" value="<?php echo esc_attr( $menu_studio['menu_width'] ?? 160 ); ?>" min="120" max="300" />
						</label>
					</p>
					<p>
						<label for="edminboost_menu_font_size"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_font_size' ); ?><?php esc_html_e( 'Font size (px)', EDMINBOOST_TEXT_DOMAIN ); ?>
							<input type="number" class="small-text" id="edminboost_menu_font_size" name="<?php echo esc_attr( $ms_key ); ?>[font_size]" value="<?php echo esc_attr( $menu_studio['font_size'] ?? 14 ); ?>" min="10" max="24" />
						</label>
						<label for="edminboost_menu_line_height"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_line_height' ); ?><?php esc_html_e( 'Line height (px)', EDMINBOOST_TEXT_DOMAIN ); ?>
							<input type="number" class="small-text" id="edminboost_menu_line_height" name="<?php echo esc_attr( $ms_key ); ?>[line_height]" value="<?php echo esc_attr( $menu_studio['line_height'] ?? 20 ); ?>" min="12" max="36" />
						</label>
						<label for="edminboost_menu_letter_spacing"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_letter_spacing' ); ?><?php esc_html_e( 'Letter spacing (px)', EDMINBOOST_TEXT_DOMAIN ); ?>
							<input type="number" class="small-text" id="edminboost_menu_letter_spacing" name="<?php echo esc_attr( $ms_key ); ?>[letter_spacing]" value="<?php echo esc_attr( $menu_studio['letter_spacing'] ?? 0 ); ?>" min="-2" max="6" />
						</label>
					</p>
					<p>
						<label for="edminboost_menu_display_mode"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_display_mode' ); ?><?php esc_html_e( 'Menu item display', EDMINBOOST_TEXT_DOMAIN ); ?></label>
						<select id="edminboost_menu_display_mode" name="<?php echo esc_attr( $ms_key ); ?>[display_mode]">
							<option value="both" <?php selected( $menu_studio['display_mode'] ?? 'both', 'both' ); ?>><?php esc_html_e( 'Icon and text', EDMINBOOST_TEXT_DOMAIN ); ?></option>
							<option value="icon" <?php selected( $menu_studio['display_mode'] ?? 'both', 'icon' ); ?>><?php esc_html_e( 'Icon only', EDMINBOOST_TEXT_DOMAIN ); ?></option>
							<option value="text" <?php selected( $menu_studio['display_mode'] ?? 'both', 'text' ); ?>><?php esc_html_e( 'Text only', EDMINBOOST_TEXT_DOMAIN ); ?></option>
						</select>
					</p>
				</section>

				<section class="edminboost-card edminboost-menu-studio-panel edminboost-menu-studio-colors" aria-labelledby="edminboost-menu-colors-heading">
					<h2 id="edminboost-menu-colors-heading"><?php esc_html_e( 'Sidebar Colors', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
					<p class="description"><?php esc_html_e( 'Customize parent menu, submenu, and notification badge colors across wp-admin.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

					<label class="edminboost-checkbox-row" for="edminboost_menu_studio_use_colors">
						<input
							type="checkbox"
							id="edminboost_menu_studio_use_colors"
							name="<?php echo esc_attr( $ms_key ); ?>[use_colors]"
							value="1"
							<?php checked( ! empty( $menu_studio['use_colors'] ) ); ?>
						/>
						<?php EDMINBOOST_Setting_Help::echo_icon( 'menu_use_colors' ); ?>
						<?php esc_html_e( 'Apply custom sidebar colors', EDMINBOOST_TEXT_DOMAIN ); ?>
					</label>

					<div class="edminboost-menu-color-grid" id="edminboost-menu-color-grid" <?php echo empty( $menu_studio['use_colors'] ) ? 'hidden' : ''; ?>>
						<?php foreach ( $color_fields as $color_key => $color_meta ) : ?>
							<?php
							$field_id    = 'edminboost_menu_color_' . $color_key;
							$picker_id   = $field_id . '_picker';
							$value       = isset( $colors[ $color_key ] ) ? $colors[ $color_key ] : '';
							$picker_val  = $value ? $value : $color_meta['default'];
							?>
							<div class="edminboost-menu-color-row" data-color-key="<?php echo esc_attr( $color_key ); ?>">
								<label for="<?php echo esc_attr( $field_id ); ?>"><?php EDMINBOOST_Setting_Help::echo_icon( 'menu_color_' . $color_key ); ?><?php echo esc_html( $color_meta['label'] ); ?></label>
								<input type="color" id="<?php echo esc_attr( $picker_id ); ?>" value="<?php echo esc_attr( $picker_val ); ?>" data-target="<?php echo esc_attr( $field_id ); ?>" />
								<input
									type="text"
									class="small-text edminboost-menu-color-input"
									id="<?php echo esc_attr( $field_id ); ?>"
									name="<?php echo esc_attr( $ms_key ); ?>[colors][<?php echo esc_attr( $color_key ); ?>]"
									value="<?php echo esc_attr( $value ); ?>"
									placeholder="<?php echo esc_attr( $color_meta['placeholder'] ); ?>"
									pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
								/>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="edminboost-menu-color-preview" id="edminboost-menu-color-preview" aria-live="polite">
						<div class="edminboost-menu-color-preview__parent">
							<span class="dashicons dashicons-admin-post" aria-hidden="true"></span>
							<span><?php esc_html_e( 'Posts', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							<span class="edminboost-menu-color-preview__badge">3</span>
						</div>
						<ul class="edminboost-menu-color-preview__submenu">
							<li><?php esc_html_e( 'All Posts', EDMINBOOST_TEXT_DOMAIN ); ?></li>
							<li><?php esc_html_e( 'Add New', EDMINBOOST_TEXT_DOMAIN ); ?></li>
						</ul>
					</div>
				</section>
			</div>
		</div>

		<div id="edminboost-menu-hidden-inputs" hidden aria-hidden="true"></div>

		<footer class="edminboost-cc-footer">
			<?php submit_button( __( 'Save Menu Studio', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
		</footer>
	</form>
</div>
