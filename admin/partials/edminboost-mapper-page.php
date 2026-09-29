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

$edminboost_option_name      = EDMINBOOST_Settings::OPTION_NAME;
$edminboost_behavior         = isset( $cc_settings['behavior'] ) && is_array( $cc_settings['behavior'] )
	? $cc_settings['behavior']
	: EDMINBOOST_Command_Center::get_defaults()['behavior'];
$edminboost_cc_key            = $edminboost_option_name . '[command_center][behavior]';
$edminboost_discovered        = EDMINBOOST_Command_Center::get_discovered_menu_items();
$edminboost_top_bar_items     = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
	? $cc_settings['top_bar_items']
	: array();
$edminboost_badge_sources     = EDMINBOOST_Command_Center::get_badge_sources();
$edminboost_dashicon_options  = EDMINBOOST_Command_Center::get_dashicon_options();

$edminboost_active_slugs = array();
foreach ( $edminboost_top_bar_items as $edminboost_item ) {
	if ( ! empty( $edminboost_item['slug'] ) ) {
		$edminboost_item_anchor   = isset( $edminboost_item['anchor'] ) ? (string) $edminboost_item['anchor'] : '';
		$edminboost_active_slugs[] = $edminboost_item['slug'] . "\0" . $edminboost_item_anchor;
	}
}
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap edminboost-cc-wrap--wide">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Choose which admin links appear in your top bar and how they open.', 'edminboost-admin-customization' ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form" id="edminboost-mapper-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>

		<div class="edminboost-mapper-layout">
			<aside class="edminboost-card edminboost-mapper-panel" aria-labelledby="edminboost-discovered-heading">
				<h2 id="edminboost-discovered-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'discovered_pages' ); ?><?php esc_html_e( 'Discovered Admin Pages', 'edminboost-admin-customization' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Auto-scanned from your sidebar menus, including submenu pages.', 'edminboost-admin-customization' ); ?></p>

				<label for="edminboost-plugin-search">
					<?php EDMINBOOST_Setting_Help::echo_icon( 'mapper_search' ); ?>
					<?php esc_html_e( 'Filter plugins', 'edminboost-admin-customization' ); ?>
				</label>
				<input
					type="search"
					id="edminboost-plugin-search"
					class="edminboost-mapper-search"
					placeholder="<?php esc_attr_e( 'Search installed plugins…', 'edminboost-admin-customization' ); ?>"
					autocomplete="off"
				/>

				<ul class="edminboost-discovered-list" id="edminboost-discovered-list">
					<?php if ( empty( $edminboost_discovered ) ) : ?>
						<li class="edminboost-discovered-list__empty">
							<?php esc_html_e( 'No admin menus detected.', 'edminboost-admin-customization' ); ?>
						</li>
					<?php else : ?>
						<?php foreach ( $edminboost_discovered as $edminboost_index => $edminboost_menu_item ) : ?>
							<?php
							$edminboost_is_active = in_array( $edminboost_menu_item['slug'] . "\0", $edminboost_active_slugs, true );
							$edminboost_item_id   = 'edminboost_discovered_' . $edminboost_index;
							$edminboost_source    = isset( $edminboost_menu_item['source'] ) ? $edminboost_menu_item['source'] : 'top';
							?>
							<li
								class="edminboost-discovered-item edminboost-discovered-item--<?php echo esc_attr( $edminboost_source ); ?><?php echo $edminboost_is_active ? ' is-active' : ''; ?>"
								data-slug="<?php echo esc_attr( $edminboost_menu_item['slug'] ); ?>"
								data-label="<?php echo esc_attr( $edminboost_menu_item['label'] ); ?>"
								data-icon="<?php echo esc_attr( $edminboost_menu_item['icon'] ); ?>"
							>
								<span class="edminboost-discovered-item__handle dashicons dashicons-move" aria-hidden="true"></span>
								<span class="edminboost-discovered-item__icon dashicons <?php echo esc_attr( $edminboost_menu_item['icon'] ); ?>" aria-hidden="true"></span>
								<span class="edminboost-discovered-item__label" title="<?php echo esc_attr( $edminboost_menu_item['label'] ); ?>"><?php echo esc_html( $edminboost_menu_item['label'] ); ?></span>
								<label class="edminboost-discovered-item__toggle" for="<?php echo esc_attr( $edminboost_item_id ); ?>">
									<span class="screen-reader-text">
										<?php
										/* translators: %s: menu item label */
										echo esc_html( sprintf( __( 'Add %s to top bar', 'edminboost-admin-customization' ), $edminboost_menu_item['label'] ) );
										?>
									</span>
									<input
										type="checkbox"
										id="<?php echo esc_attr( $edminboost_item_id ); ?>"
										class="edminboost-discovered-item__checkbox"
										<?php checked( $edminboost_is_active ); ?>
									/>
								</label>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>

				<details class="edminboost-custom-link" id="edminboost-custom-link">
					<summary class="edminboost-custom-link__heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_topbar_path' ); ?><?php esc_html_e( 'Custom admin link', 'edminboost-admin-customization' ); ?></summary>
					<div class="edminboost-custom-link__body">
						<p class="description"><?php esc_html_e( 'Add any admin page path that does not appear in the list above.', 'edminboost-admin-customization' ); ?></p>

						<p>
							<label for="edminboost-custom-link-path"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_topbar_path' ); ?><?php esc_html_e( 'Admin path', 'edminboost-admin-customization' ); ?></label>
							<input
								type="text"
								id="edminboost-custom-link-path"
								class="regular-text code"
								placeholder="<?php echo esc_attr( 'edit-tags.php?taxonomy=product_tag&post_type=product' ); ?>"
								autocomplete="off"
							/>
						</p>

						<p>
							<label for="edminboost-custom-link-label"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_topbar_label' ); ?><?php esc_html_e( 'Label', 'edminboost-admin-customization' ); ?></label>
							<input
								type="text"
								id="edminboost-custom-link-label"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'Product Tags', 'edminboost-admin-customization' ); ?>"
								autocomplete="off"
							/>
						</p>

						<p>
							<label for="edminboost-custom-link-anchor"><?php EDMINBOOST_Setting_Help::echo_icon( 'custom_topbar_anchor' ); ?><?php esc_html_e( 'Anchor (optional)', 'edminboost-admin-customization' ); ?></label>
							<input
								type="text"
								id="edminboost-custom-link-anchor"
								class="regular-text code"
								placeholder="<?php echo esc_attr( 'woocommerce_permalink_structure' ); ?>"
								autocomplete="off"
							/>
							<span class="description"><?php esc_html_e( 'Scroll to a section on the page. You can also include #fragment in the path above.', 'edminboost-admin-customization' ); ?></span>
						</p>

						<p class="edminboost-custom-link__actions">
							<button type="button" class="button button-secondary" id="edminboost-custom-link-add">
								<?php esc_html_e( 'Add to top bar', 'edminboost-admin-customization' ); ?>
							</button>
						</p>

						<p class="edminboost-custom-link__error description" id="edminboost-custom-link-error" hidden role="alert"></p>
					</div>
				</details>
			</aside>

			<div class="edminboost-mapper-main">
				<section class="edminboost-card edminboost-mapper-panel" aria-labelledby="edminboost-canvas-heading">
					<h2 id="edminboost-canvas-heading"><?php EDMINBOOST_Setting_Help::echo_icon( 'topbar_canvas' ); ?><?php esc_html_e( 'Top Bar Live Canvas', 'edminboost-admin-customization' ); ?></h2>
					<p class="description"><?php esc_html_e( 'Drag items to reorder. Click an icon to configure it.', 'edminboost-admin-customization' ); ?></p>

					<div class="edminboost-topbar-canvas" id="edminboost-topbar-canvas" role="list" aria-label="<?php esc_attr_e( 'Top bar preview', 'edminboost-admin-customization' ); ?>">
						<span class="edminboost-topbar-canvas__brand" aria-hidden="true">
							<span class="dashicons dashicons-wordpress"></span>
						</span>
						<ul class="edminboost-topbar-canvas__items" id="edminboost-topbar-items">
							<?php foreach ( $edminboost_top_bar_items as $edminboost_index => $edminboost_item ) : ?>
								<?php
								$edminboost_slug         = isset( $edminboost_item['slug'] ) ? $edminboost_item['slug'] : '';
								$edminboost_anchor       = isset( $edminboost_item['anchor'] ) ? $edminboost_item['anchor'] : '';
								$edminboost_label        = isset( $edminboost_item['label'] ) ? $edminboost_item['label'] : $edminboost_slug;
								$edminboost_icon         = isset( $edminboost_item['icon'] ) ? $edminboost_item['icon'] : 'dashicons-admin-generic';
								$edminboost_interaction  = isset( $edminboost_item['interaction'] ) ? $edminboost_item['interaction'] : 'redirect';
								$edminboost_badge_source = isset( $edminboost_item['badge_source'] ) ? $edminboost_item['badge_source'] : '';

								if ( '' === $edminboost_anchor && false !== strpos( $edminboost_slug, '#' ) ) {
									$edminboost_slug_parts = explode( '#', $edminboost_slug, 2 );
									$edminboost_slug       = $edminboost_slug_parts[0];
									$edminboost_anchor     = isset( $edminboost_slug_parts[1] ) ? $edminboost_slug_parts[1] : '';
								}
								?>
								<li
									class="edminboost-topbar-item"
									role="listitem"
									draggable="true"
									data-slug="<?php echo esc_attr( $edminboost_slug ); ?>"
									data-anchor="<?php echo esc_attr( $edminboost_anchor ); ?>"
									data-label="<?php echo esc_attr( $edminboost_label ); ?>"
									data-icon="<?php echo esc_attr( $edminboost_icon ); ?>"
									data-interaction="<?php echo esc_attr( $edminboost_interaction ); ?>"
									data-badge-source="<?php echo esc_attr( $edminboost_badge_source ); ?>"
								>
									<button type="button" class="edminboost-topbar-item__btn" aria-label="<?php echo esc_attr( $edminboost_label ); ?>">
										<span class="edminboost-topbar-item__icon dashicons <?php echo esc_attr( $edminboost_icon ); ?>" aria-hidden="true"></span>
										<span class="edminboost-topbar-item__text"><?php echo esc_html( $edminboost_label ); ?></span>
										<?php if ( '' !== $edminboost_badge_source ) : ?>
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

					<p class="edminboost-topbar-canvas__hint description" id="edminboost-canvas-empty" <?php echo ! empty( $edminboost_top_bar_items ) ? 'hidden' : ''; ?>>
						<?php esc_html_e( 'Toggle items from the left panel or drag them here to build your top bar.', 'edminboost-admin-customization' ); ?>
					</p>

					<aside
						class="edminboost-item-drawer"
						id="edminboost-item-drawer"
						aria-labelledby="edminboost-drawer-heading"
						hidden
					>
					<h2 id="edminboost-drawer-heading"><?php esc_html_e( 'Item Configuration', 'edminboost-admin-customization' ); ?></h2>
					<p class="description" id="edminboost-drawer-subtitle"></p>

					<fieldset class="edminboost-fieldset">
						<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'item_icon' ); ?><?php esc_html_e( 'Icon', 'edminboost-admin-customization' ); ?></legend>
						<div class="edminboost-icon-picker" id="edminboost-icon-picker" role="listbox" aria-label="<?php esc_attr_e( 'Choose dashicon', 'edminboost-admin-customization' ); ?>">
							<?php foreach ( $edminboost_dashicon_options as $edminboost_dashicon ) : ?>
								<button
									type="button"
									class="edminboost-icon-picker__btn"
									data-icon="<?php echo esc_attr( $edminboost_dashicon ); ?>"
									role="option"
									aria-label="<?php echo esc_attr( $edminboost_dashicon ); ?>"
								>
									<span class="dashicons <?php echo esc_attr( $edminboost_dashicon ); ?>" aria-hidden="true"></span>
								</button>
							<?php endforeach; ?>
						</div>
					</fieldset>

					<p>
						<label for="edminboost-item-label"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_label' ); ?><?php esc_html_e( 'Label override', 'edminboost-admin-customization' ); ?></label>
						<input type="text" id="edminboost-item-label" class="regular-text" />
					</p>

					<p>
						<label for="edminboost-item-anchor"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_anchor' ); ?><?php esc_html_e( 'Anchor (optional)', 'edminboost-admin-customization' ); ?></label>
						<input type="text" id="edminboost-item-anchor" class="regular-text code" placeholder="<?php echo esc_attr( 'woocommerce_permalink_structure' ); ?>" />
						<span class="description"><?php esc_html_e( 'Scroll to a section on the page when the link is opened.', 'edminboost-admin-customization' ); ?></span>
					</p>

					<?php if ( EDMINBOOST_Pro::shows_pro_settings_ui() ) : ?>
					<div class="<?php echo esc_attr( EDMINBOOST_Pro::section_class() ); ?>"<?php EDMINBOOST_Pro::echo_feature_attr( 'top_bar_drawer_items' ); ?>>
					<fieldset class="edminboost-fieldset">
						<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'item_interaction' ); ?><?php esc_html_e( 'Interaction', 'edminboost-admin-customization' ); ?></legend>
						<label class="edminboost-checkbox-row">
							<input type="radio" name="edminboost_item_interaction" value="redirect" checked />
							<?php esc_html_e( 'Direct redirect', 'edminboost-admin-customization' ); ?>
						</label>
						<label class="edminboost-checkbox-row">
							<input type="radio" name="edminboost_item_interaction" value="drawer" />
							<?php esc_html_e( 'AJAX slide-out drawer', 'edminboost-admin-customization' ); ?>
						</label>
					</fieldset>

					<p class="edminboost-item-drawer__preview" id="edminboost-drawer-preview-wrap" hidden>
						<button type="button" class="button button-secondary" id="edminboost-drawer-preview">
							<?php esc_html_e( 'Preview AJAX drawer', 'edminboost-admin-customization' ); ?>
						</button>
					</p>

					<p>
						<label for="edminboost-item-badge"><?php EDMINBOOST_Setting_Help::echo_icon( 'item_badge_source' ); ?><?php esc_html_e( 'Live badge binding', 'edminboost-admin-customization' ); ?></label>
						<select id="edminboost-item-badge" class="regular-text">
							<?php foreach ( $edminboost_badge_sources as $edminboost_source_key => $edminboost_source_label ) : ?>
								<option value="<?php echo esc_attr( $edminboost_source_key ); ?>">
									<?php echo esc_html( $edminboost_source_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</p>
					</div>
					<?php endif; ?>

					<p>
						<button type="button" class="button" id="edminboost-drawer-close">
							<?php esc_html_e( 'Close', 'edminboost-admin-customization' ); ?>
						</button>
						<button type="button" class="button button-link-delete" id="edminboost-drawer-remove">
							<?php esc_html_e( 'Remove from top bar', 'edminboost-admin-customization' ); ?>
						</button>
					</p>
					</aside>
				</section>

				<?php if ( EDMINBOOST_Pro::shows_pro_settings_ui() ) : ?>
				<section class="edminboost-card edminboost-cc-section edminboost-home-look is-disabled" id="edminboost-mapper-look" aria-labelledby="edminboost-mapper-look-heading" aria-disabled="true">
					<h2 id="edminboost-mapper-look-heading"><?php esc_html_e( 'Panel & badges', 'edminboost-admin-customization' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Adjust slide-out panel style and notification badges. Set a top bar link to AJAX slide-out drawer to configure these settings.', 'edminboost-admin-customization' ); ?>
					</p>
					<?php
					// Included partial expects `$behavior` and `$cc_key` in parent scope.
					// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
					$behavior = $edminboost_behavior;
					$cc_key   = $edminboost_cc_key;
					// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
					include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-home-advanced-look.php';
					?>
				</section>
				<?php endif; ?>

				<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[enabled]" value="1" />
				<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_layout_studio_save]" value="1" />
				<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_mark_setup_complete]" value="1" />
				<div id="edminboost-topbar-hidden-inputs"></div>

				<?php
				$edminboost_save_label    = __( 'Save top bar', 'edminboost-admin-customization' );
				$edminboost_wrapper_class = 'submit edminboost-mapper-submit edminboost-form-actions';
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-form-actions.php';
				?>
			</div>
		</div>
	</form>
</div>
