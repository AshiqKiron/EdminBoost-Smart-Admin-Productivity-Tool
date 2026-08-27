<?php
/**
 * Top-Bar Mapper & Layout Studio.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name     = EDMINBOOST_Settings::OPTION_NAME;
$discovered      = EDMINBOOST_Command_Center::get_discovered_menu_items();
$top_bar_items   = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
	? $cc_settings['top_bar_items']
	: array();
$badge_sources   = EDMINBOOST_Command_Center::get_badge_sources();
$dashicon_options = EDMINBOOST_Command_Center::get_dashicon_options();

$active_slugs = array();
foreach ( $top_bar_items as $item ) {
	if ( ! empty( $item['slug'] ) ) {
		$item_anchor = isset( $item['anchor'] ) ? (string) $item['anchor'] : '';
		$active_slugs[] = $item['slug'] . "\0" . $item_anchor;
	}
}
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap edminboost-cc-wrap--wide">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Choose which admin links appear in your top bar and how they open.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form" id="edminboost-mapper-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>

		<div class="edminboost-mapper-layout">
			<aside class="edminboost-card edminboost-mapper-panel" aria-labelledby="edminboost-discovered-heading">
				<h2 id="edminboost-discovered-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'discovered_pages' ); ?><?php esc_html_e( 'Discovered Admin Pages', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
				<p class="description"><?php esc_html_e( 'Auto-scanned from your sidebar menus, including submenu pages.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

				<label for="edminboost-plugin-search">
					<?php EDMINBOOST_Setting_Help::echo_icon( 'mapper_search' ); ?>
					<?php esc_html_e( 'Filter plugins', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<input
					type="search"
					id="edminboost-plugin-search"
					class="edminboost-mapper-search"
					placeholder="<?php esc_attr_e( 'Search installed plugins…', EDMINBOOST_TEXT_DOMAIN ); ?>"
					autocomplete="off"
				/>

				<ul class="edminboost-discovered-list" id="edminboost-discovered-list">
					<?php if ( empty( $discovered ) ) : ?>
						<li class="edminboost-discovered-list__empty">
							<?php esc_html_e( 'No admin menus detected.', EDMINBOOST_TEXT_DOMAIN ); ?>
						</li>
					<?php else : ?>
						<?php foreach ( $discovered as $index => $menu_item ) : ?>
							<?php
							$is_active = in_array( $menu_item['slug'] . "\0", $active_slugs, true );
							$item_id   = 'edminboost_discovered_' . $index;
							$source    = isset( $menu_item['source'] ) ? $menu_item['source'] : 'top';
							?>
							<li
								class="edminboost-discovered-item edminboost-discovered-item--<?php echo esc_attr( $source ); ?><?php echo $is_active ? ' is-active' : ''; ?>"
								data-slug="<?php echo esc_attr( $menu_item['slug'] ); ?>"
								data-label="<?php echo esc_attr( $menu_item['label'] ); ?>"
								data-icon="<?php echo esc_attr( $menu_item['icon'] ); ?>"
							>
								<span class="edminboost-discovered-item__handle dashicons dashicons-move" aria-hidden="true"></span>
								<span class="edminboost-discovered-item__icon dashicons <?php echo esc_attr( $menu_item['icon'] ); ?>" aria-hidden="true"></span>
								<span class="edminboost-discovered-item__label" title="<?php echo esc_attr( $menu_item['label'] ); ?>"><?php echo esc_html( $menu_item['label'] ); ?></span>
								<label class="edminboost-discovered-item__toggle" for="<?php echo esc_attr( $item_id ); ?>">
									<span class="screen-reader-text">
										<?php
										/* translators: %s: menu item label */
										echo esc_html( sprintf( __( 'Add %s to top bar', EDMINBOOST_TEXT_DOMAIN ), $menu_item['label'] ) );
										?>
									</span>
									<input
										type="checkbox"
										id="<?php echo esc_attr( $item_id ); ?>"
										class="edminboost-discovered-item__checkbox"
										<?php checked( $is_active ); ?>
									/>
								</label>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>

				<details class="edminboost-custom-link" id="edminboost-custom-link">
					<summary class="edminboost-custom-link__heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_topbar_path' ); ?><?php esc_html_e( 'Custom admin link', EDMINBOOST_TEXT_DOMAIN ); ?></summary>
					<div class="edminboost-custom-link__body">
						<p class="description"><?php esc_html_e( 'Add any admin page path that does not appear in the list above.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

						<p>
							<label for="edminboost-custom-link-path"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_topbar_path' ); ?><?php esc_html_e( 'Admin path', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input
								type="text"
								id="edminboost-custom-link-path"
								class="regular-text code"
								placeholder="<?php echo esc_attr( 'edit-tags.php?taxonomy=product_tag&post_type=product' ); ?>"
								autocomplete="off"
							/>
						</p>

						<p>
							<label for="edminboost-custom-link-label"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_topbar_label' ); ?><?php esc_html_e( 'Label', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input
								type="text"
								id="edminboost-custom-link-label"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'Product Tags', EDMINBOOST_TEXT_DOMAIN ); ?>"
								autocomplete="off"
							/>
						</p>

						<p>
							<label for="edminboost-custom-link-anchor"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_topbar_anchor' ); ?><?php esc_html_e( 'Anchor (optional)', EDMINBOOST_TEXT_DOMAIN ); ?></label>
							<input
								type="text"
								id="edminboost-custom-link-anchor"
								class="regular-text code"
								placeholder="<?php echo esc_attr( 'woocommerce_permalink_structure' ); ?>"
								autocomplete="off"
							/>
							<span class="description"><?php esc_html_e( 'Scroll to a section on the page. You can also include #fragment in the path above.', EDMINBOOST_TEXT_DOMAIN ); ?></span>
						</p>

						<p class="edminboost-custom-link__actions">
							<button type="button" class="button button-secondary" id="edminboost-custom-link-add">
								<?php esc_html_e( 'Add to top bar', EDMINBOOST_TEXT_DOMAIN ); ?>
							</button>
						</p>

						<p class="edminboost-custom-link__error description" id="edminboost-custom-link-error" hidden role="alert"></p>
					</div>
				</details>
			</aside>

			<div class="edminboost-mapper-main">
				<section class="edminboost-card edminboost-mapper-panel" aria-labelledby="edminboost-canvas-heading">
					<h2 id="edminboost-canvas-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'topbar_canvas' ); ?><?php esc_html_e( 'Top Bar Live Canvas', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
					<p class="description"><?php esc_html_e( 'Drag items to reorder. Click an icon to configure it.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

					<div class="edminboost-topbar-canvas" id="edminboost-topbar-canvas" role="list" aria-label="<?php esc_attr_e( 'Top bar preview', EDMINBOOST_TEXT_DOMAIN ); ?>">
						<span class="edminboost-topbar-canvas__brand" aria-hidden="true">
							<span class="dashicons dashicons-wordpress"></span>
						</span>
						<ul class="edminboost-topbar-canvas__items" id="edminboost-topbar-items">
							<?php foreach ( $top_bar_items as $index => $item ) : ?>
								<?php
								$slug         = isset( $item['slug'] ) ? $item['slug'] : '';
								$anchor       = isset( $item['anchor'] ) ? $item['anchor'] : '';
								$label        = isset( $item['label'] ) ? $item['label'] : $slug;
								$icon         = isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic';
								$interaction  = isset( $item['interaction'] ) ? $item['interaction'] : 'redirect';
								$badge_source = isset( $item['badge_source'] ) ? $item['badge_source'] : '';

								if ( '' === $anchor && false !== strpos( $slug, '#' ) ) {
									$slug_parts = explode( '#', $slug, 2 );
									$slug       = $slug_parts[0];
									$anchor     = isset( $slug_parts[1] ) ? $slug_parts[1] : '';
								}
								?>
								<li
									class="edminboost-topbar-item"
									role="listitem"
									draggable="true"
									data-slug="<?php echo esc_attr( $slug ); ?>"
									data-anchor="<?php echo esc_attr( $anchor ); ?>"
									data-label="<?php echo esc_attr( $label ); ?>"
									data-icon="<?php echo esc_attr( $icon ); ?>"
									data-interaction="<?php echo esc_attr( $interaction ); ?>"
									data-badge-source="<?php echo esc_attr( $badge_source ); ?>"
								>
									<button type="button" class="edminboost-topbar-item__btn" aria-label="<?php echo esc_attr( $label ); ?>">
										<span class="edminboost-topbar-item__icon dashicons <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
										<span class="edminboost-topbar-item__text"><?php echo esc_html( $label ); ?></span>
										<?php if ( '' !== $badge_source ) : ?>
											<span class="edminboost-topbar-item__badge" aria-hidden="true">3</span>
										<?php endif; ?>
									</button>
								</li>
							<?php endforeach; ?>
						</ul>
						<span class="edminboost-topbar-canvas__profile" aria-hidden="true">
							<span class="dashicons dashicons-admin-users"></span>
						</span>
					</div>

					<p class="edminboost-topbar-canvas__hint description" id="edminboost-canvas-empty" <?php echo ! empty( $top_bar_items ) ? 'hidden' : ''; ?>>
						<?php esc_html_e( 'Toggle items from the left panel or drag them here to build your top bar.', EDMINBOOST_TEXT_DOMAIN ); ?>
					</p>

					<aside
						class="edminboost-item-drawer"
						id="edminboost-item-drawer"
						aria-labelledby="edminboost-drawer-heading"
						hidden
					>
					<h2 id="edminboost-drawer-heading"><?php esc_html_e( 'Item Configuration', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
					<p class="description" id="edminboost-drawer-subtitle"></p>

					<fieldset class="edminboost-fieldset">
						<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'item_icon' ); ?><?php esc_html_e( 'Icon', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
						<div class="edminboost-icon-picker" id="edminboost-icon-picker" role="listbox" aria-label="<?php esc_attr_e( 'Choose dashicon', EDMINBOOST_TEXT_DOMAIN ); ?>">
							<?php foreach ( $dashicon_options as $dashicon ) : ?>
								<button
									type="button"
									class="edminboost-icon-picker__btn"
									data-icon="<?php echo esc_attr( $dashicon ); ?>"
									role="option"
									aria-label="<?php echo esc_attr( $dashicon ); ?>"
								>
									<span class="dashicons <?php echo esc_attr( $dashicon ); ?>" aria-hidden="true"></span>
								</button>
							<?php endforeach; ?>
						</div>
					</fieldset>

					<p>
						<label for="edminboost-item-label"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_label' ); ?><?php esc_html_e( 'Label override', EDMINBOOST_TEXT_DOMAIN ); ?></label>
						<input type="text" id="edminboost-item-label" class="regular-text" />
					</p>

					<p>
						<label for="edminboost-item-anchor"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_anchor' ); ?><?php esc_html_e( 'Anchor (optional)', EDMINBOOST_TEXT_DOMAIN ); ?></label>
						<input type="text" id="edminboost-item-anchor" class="regular-text code" placeholder="<?php echo esc_attr( 'woocommerce_permalink_structure' ); ?>" />
						<span class="description"><?php esc_html_e( 'Scroll to a section on the page when the link is opened.', EDMINBOOST_TEXT_DOMAIN ); ?></span>
					</p>

					<fieldset class="edminboost-fieldset">
						<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'item_interaction' ); ?><?php esc_html_e( 'Interaction', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
						<label class="edminboost-checkbox-row">
							<input type="radio" name="edminboost_item_interaction" value="redirect" checked />
							<?php esc_html_e( 'Direct redirect', EDMINBOOST_TEXT_DOMAIN ); ?>
						</label>
						<label class="edminboost-checkbox-row">
							<input type="radio" name="edminboost_item_interaction" value="drawer" />
							<?php esc_html_e( 'AJAX slide-out drawer', EDMINBOOST_TEXT_DOMAIN ); ?>
						</label>
					</fieldset>

					<p class="edminboost-item-drawer__preview" id="edminboost-drawer-preview-wrap" hidden>
						<button type="button" class="button button-secondary" id="edminboost-drawer-preview">
							<?php esc_html_e( 'Preview AJAX drawer', EDMINBOOST_TEXT_DOMAIN ); ?>
						</button>
					</p>

					<p>
						<label for="edminboost-item-badge"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_badge_source' ); ?><?php esc_html_e( 'Live badge binding', EDMINBOOST_TEXT_DOMAIN ); ?></label>
						<select id="edminboost-item-badge" class="regular-text">
							<?php foreach ( $badge_sources as $source_key => $source_label ) : ?>
								<option value="<?php echo esc_attr( $source_key ); ?>">
									<?php echo esc_html( $source_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</p>

					<p>
						<button type="button" class="button" id="edminboost-drawer-close">
							<?php esc_html_e( 'Close', EDMINBOOST_TEXT_DOMAIN ); ?>
						</button>
						<button type="button" class="button button-link-delete" id="edminboost-drawer-remove">
							<?php esc_html_e( 'Remove from top bar', EDMINBOOST_TEXT_DOMAIN ); ?>
						</button>
					</p>
					</aside>
				</section>

				<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />
				<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_layout_studio_save]" value="1" />
				<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_mark_setup_complete]" value="1" />
				<div id="edminboost-topbar-hidden-inputs"></div>

				<p class="submit edminboost-mapper-submit">
					<?php submit_button( __( 'Save top bar', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
				</p>
			</div>
		</div>
	</form>
</div>
