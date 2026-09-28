<?php
/**
 * Live previews for Productivity tab settings.
 *
 * @package EdminBoost
 *
 * @var array  $edminboost_features Feature settings.
 * @var string $edminboost_preview  Preview key: notices|screen_help|dashboard_widgets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_preview = isset( $edminboost_preview ) ? sanitize_key( $edminboost_preview ) : '';

if ( ! in_array( $edminboost_preview, array( 'notices', 'screen_help', 'dashboard_widgets' ), true ) ) {
	return;
}

$edminboost_theme_settings = EDMINBOOST_Theme::get_settings();
$edminboost_preview_colors = EDMINBOOST_Theme::resolve_preview_colors(
	isset( $edminboost_theme_settings['preset'] ) ? $edminboost_theme_settings['preset'] : 'default',
	isset( $edminboost_theme_settings['mode'] ) ? $edminboost_theme_settings['mode'] : 'light',
	$edminboost_theme_settings
);
$edminboost_color_defaults = array(
	'accent'  => '#2271b1',
	'surface' => '#ffffff',
	'text'    => '#1d2327',
	'topbar'  => '#1d2327',
	'sidebar' => '#1d2327',
	'content' => '#f0f0f1',
);
$edminboost_preview_colors = wp_parse_args( $edminboost_preview_colors, $edminboost_color_defaults );
$edminboost_preview_style_vars        = sprintf(
	'--eb-op-accent:%1$s;--eb-op-surface:%2$s;--eb-op-text:%3$s;--eb-op-top:%4$s;--eb-op-sidebar:%5$s;--eb-op-content:%6$s;',
	esc_attr( $edminboost_preview_colors['accent'] ),
	esc_attr( $edminboost_preview_colors['surface'] ),
	esc_attr( $edminboost_preview_colors['text'] ),
	esc_attr( $edminboost_preview_colors['topbar'] ),
	esc_attr( $edminboost_preview_colors['sidebar'] ),
	esc_attr( $edminboost_preview_colors['content'] )
);

$edminboost_hide_notices_enabled      = ! empty( $edminboost_features['hide_admin_notices'] );
$edminboost_hide_screen_enabled       = ! empty( $edminboost_features['hide_screen_help'] );
$edminboost_dashboard_widgets_enabled = ! empty( $edminboost_features['dashboard_widgets']['enabled'] );

$edminboost_preview_meta = array(
	'notices'           => array(
		'id'          => 'edminboost-productivity-notices-preview',
		'aria_label'  => __( 'Admin notices live preview', 'edminboost-smart-admin-productivity-tool' ),
		'preview_key' => 'hide_admin_notices',
	),
	'screen_help'       => array(
		'id'          => 'edminboost-productivity-screen-preview',
		'aria_label'  => __( 'Screen tabs live preview', 'edminboost-smart-admin-productivity-tool' ),
		'preview_key' => 'hide_screen_help',
	),
	'dashboard_widgets' => array(
		'id'          => 'edminboost-productivity-dashboard-preview',
		'aria_label'  => __( 'Dashboard widgets live preview', 'edminboost-smart-admin-productivity-tool' ),
		'preview_key' => '',
	),
);

$edminboost_notice_items = array(
	array(
		'removable' => true,
		'class'     => 'notice-success is-dismissible',
		'message'   => __( 'Settings saved.', 'edminboost-smart-admin-productivity-tool' ),
	),
	array(
		'removable' => true,
		'class'     => 'notice-info',
		'message'   => __( 'There is a new version available.', 'edminboost-smart-admin-productivity-tool' ),
	),
	array(
		'removable' => false,
		'class'     => 'notice-warning',
		'message'   => __( 'Your site is running an outdated PHP version.', 'edminboost-smart-admin-productivity-tool' ),
	),
	array(
		'removable' => false,
		'class'     => 'notice-error',
		'message'   => __( 'Plugin could not be activated.', 'edminboost-smart-admin-productivity-tool' ),
	),
);

$edminboost_screen_tabs = array(
	array(
		'label' => __( 'Screen Options', 'edminboost-smart-admin-productivity-tool' ),
	),
	array(
		'label' => __( 'Help', 'edminboost-smart-admin-productivity-tool' ),
	),
);

$edminboost_dashboard_widget_labels = EDMINBOOST_Dashboard::get_widget_labels();
$edminboost_dashboard_widget_layout = array(
	'welcome' => array( 'remove_welcome_panel' ),
	'main'    => array( 'remove_at_a_glance', 'remove_activity', 'remove_site_health' ),
	'side'    => array( 'remove_quick_press', 'remove_wp_news' ),
);

$edminboost_current_preview = $edminboost_preview_meta[ $edminboost_preview ];
?>
<div
	id="<?php echo esc_attr( $edminboost_current_preview['id'] ); ?>"
	class="edminboost-productivity-preview"
	style="<?php echo esc_attr( $edminboost_preview_style_vars ); ?>"
	role="region"
	aria-label="<?php echo esc_attr( $edminboost_current_preview['aria_label'] ); ?>"
	aria-live="polite"
	<?php if ( ! empty( $edminboost_current_preview['preview_key'] ) ) : ?>
		data-preview-toggle="<?php echo esc_attr( $edminboost_current_preview['preview_key'] ); ?>"
	<?php endif; ?>
>
	<p class="edminboost-productivity-preview__lead"><?php esc_html_e( 'Live preview', 'edminboost-smart-admin-productivity-tool' ); ?></p>

	<div class="edminboost-productivity-preview__canvas edminboost-productivity-preview__canvas--<?php echo esc_attr( $edminboost_preview ); ?>">
		<?php if ( 'notices' === $edminboost_preview ) : ?>
			<div class="edminboost-productivity-preview__viewport" aria-hidden="true">
				<div class="edminboost-productivity-preview__topbar"></div>
				<div class="edminboost-productivity-preview__main">
					<div class="edminboost-productivity-preview__notices">
				<?php foreach ( $edminboost_notice_items as $edminboost_notice_item ) : ?>
					<?php
					$edminboost_is_removed   = ! empty( $edminboost_notice_item['removable'] ) && $edminboost_hide_notices_enabled;
					$edminboost_notice_class = 'edminboost-productivity-preview__notice ' . $edminboost_notice_item['class'];
					if ( $edminboost_is_removed ) {
						$edminboost_notice_class .= ' is-removed';
					}

					if ( ! empty( $edminboost_notice_item['removable'] ) ) {
						$edminboost_tooltip_loaded = sprintf(
							/* translators: %s: notice message */
							__( '%s — Visible', 'edminboost-smart-admin-productivity-tool' ),
							$edminboost_notice_item['message']
						);
						$edminboost_tooltip_removed = sprintf(
							/* translators: %s: notice message */
							__( '%s — Hidden', 'edminboost-smart-admin-productivity-tool' ),
							$edminboost_notice_item['message']
						);
					} else {
						$edminboost_tooltip_loaded = sprintf(
							/* translators: %s: notice message */
							__( '%s — Always visible', 'edminboost-smart-admin-productivity-tool' ),
							$edminboost_notice_item['message']
						);
						$edminboost_tooltip_removed = $edminboost_tooltip_loaded;
					}

					$edminboost_tooltip_text = $edminboost_is_removed ? $edminboost_tooltip_removed : $edminboost_tooltip_loaded;
					?>
					<div
						class="<?php echo esc_attr( $edminboost_notice_class ); ?>"
						<?php if ( ! empty( $edminboost_notice_item['removable'] ) ) : ?>
							data-preview="<?php echo esc_attr( $edminboost_current_preview['preview_key'] ); ?>"
						<?php endif; ?>
						data-tooltip-loaded="<?php echo esc_attr( $edminboost_tooltip_loaded ); ?>"
						data-tooltip-removed="<?php echo esc_attr( $edminboost_tooltip_removed ); ?>"
						tabindex="0"
						aria-label="<?php echo esc_attr( $edminboost_tooltip_text ); ?>"
					>
						<p><?php echo esc_html( $edminboost_notice_item['message'] ); ?></p>
						<?php if ( false !== strpos( $edminboost_notice_item['class'], 'is-dismissible' ) ) : ?>
							<button type="button" class="notice-dismiss" tabindex="-1" aria-hidden="true">
								<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'edminboost-smart-admin-productivity-tool' ); ?></span>
							</button>
						<?php endif; ?>
						<span class="edminboost-productivity-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_tooltip_text ); ?></span>
					</div>
				<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php elseif ( 'dashboard_widgets' === $edminboost_preview ) : ?>
			<div class="edminboost-productivity-preview__viewport" aria-hidden="true">
				<div class="edminboost-productivity-preview__topbar"></div>
				<div class="edminboost-productivity-preview__main">
					<div class="edminboost-productivity-preview__dashboard">
						<p class="edminboost-productivity-preview__dashboard-heading"><?php esc_html_e( 'Dashboard', 'edminboost-smart-admin-productivity-tool' ); ?></p>
						<?php foreach ( $edminboost_dashboard_widget_layout as $edminboost_area => $edminboost_widget_keys ) : ?>
							<div class="edminboost-productivity-preview__dashboard-area edminboost-productivity-preview__dashboard-area--<?php echo esc_attr( $edminboost_area ); ?>">
								<?php foreach ( $edminboost_widget_keys as $edminboost_widget_key ) : ?>
									<?php
									if ( ! isset( $edminboost_dashboard_widget_labels[ $edminboost_widget_key ] ) ) {
										continue;
									}

									$edminboost_widget_label = $edminboost_dashboard_widget_labels[ $edminboost_widget_key ];
									$edminboost_is_removed   = $edminboost_dashboard_widgets_enabled && ! empty( $edminboost_features['dashboard_widgets'][ $edminboost_widget_key ] );
									$edminboost_widget_class = 'edminboost-productivity-preview__widget';
									if ( $edminboost_is_removed ) {
										$edminboost_widget_class .= ' is-removed';
									}

									$edminboost_tooltip_loaded = sprintf(
										/* translators: %s: dashboard widget label */
										__( '%s — Visible', 'edminboost-smart-admin-productivity-tool' ),
										$edminboost_widget_label
									);
									$edminboost_tooltip_removed = sprintf(
										/* translators: %s: dashboard widget label */
										__( '%s — Removed', 'edminboost-smart-admin-productivity-tool' ),
										$edminboost_widget_label
									);
									$edminboost_tooltip_text = $edminboost_is_removed ? $edminboost_tooltip_removed : $edminboost_tooltip_loaded;
									?>
									<div
										class="<?php echo esc_attr( $edminboost_widget_class ); ?>"
										data-widget-key="<?php echo esc_attr( $edminboost_widget_key ); ?>"
										data-tooltip-loaded="<?php echo esc_attr( $edminboost_tooltip_loaded ); ?>"
										data-tooltip-removed="<?php echo esc_attr( $edminboost_tooltip_removed ); ?>"
										tabindex="0"
										aria-label="<?php echo esc_attr( $edminboost_tooltip_text ); ?>"
									>
										<div class="edminboost-productivity-preview__widget-head">
											<span class="edminboost-productivity-preview__widget-title"><?php echo esc_html( $edminboost_widget_label ); ?></span>
										</div>
										<div class="edminboost-productivity-preview__widget-body" aria-hidden="true">
											<?php
											switch ( $edminboost_widget_key ) {
												case 'remove_welcome_panel':
													?>
													<div class="edminboost-productivity-preview__welcome">
														<p class="edminboost-productivity-preview__welcome-lead"><?php esc_html_e( 'Welcome to WordPress!', 'edminboost-smart-admin-productivity-tool' ); ?></p>
														<p class="edminboost-productivity-preview__welcome-links">
															<span><?php esc_html_e( 'Customize your site', 'edminboost-smart-admin-productivity-tool' ); ?></span>
															<span aria-hidden="true">&middot;</span>
															<span><?php esc_html_e( 'Write your first post', 'edminboost-smart-admin-productivity-tool' ); ?></span>
														</p>
													</div>
													<?php
													break;

												case 'remove_at_a_glance':
													?>
													<ul class="edminboost-productivity-preview__glance">
														<li><?php esc_html_e( '1 Post', 'edminboost-smart-admin-productivity-tool' ); ?></li>
														<li><?php esc_html_e( '1 Page', 'edminboost-smart-admin-productivity-tool' ); ?></li>
														<li><?php esc_html_e( '1 Comment', 'edminboost-smart-admin-productivity-tool' ); ?></li>
													</ul>
													<?php
													break;

												case 'remove_activity':
													?>
													<ul class="edminboost-productivity-preview__activity">
														<li>
															<span class="dashicons dashicons-admin-comments" aria-hidden="true"></span>
															<?php esc_html_e( 'Comment on Hello world!', 'edminboost-smart-admin-productivity-tool' ); ?>
														</li>
														<li>
															<span class="dashicons dashicons-admin-post" aria-hidden="true"></span>
															<?php esc_html_e( 'Hello world! published', 'edminboost-smart-admin-productivity-tool' ); ?>
														</li>
													</ul>
													<?php
													break;

												case 'remove_site_health':
													?>
													<div class="edminboost-productivity-preview__site-health">
														<span class="edminboost-productivity-preview__site-health-badge"><?php esc_html_e( 'Good', 'edminboost-smart-admin-productivity-tool' ); ?></span>
														<p><?php esc_html_e( 'Your site\'s health is looking good.', 'edminboost-smart-admin-productivity-tool' ); ?></p>
													</div>
													<?php
													break;

												case 'remove_quick_press':
													?>
													<div class="edminboost-productivity-preview__quick-draft">
														<p class="edminboost-productivity-preview__quick-draft-field">
															<span class="edminboost-productivity-preview__quick-draft-label"><?php esc_html_e( 'Title', 'edminboost-smart-admin-productivity-tool' ); ?></span>
															<span class="edminboost-productivity-preview__quick-draft-input"></span>
														</p>
														<p class="edminboost-productivity-preview__quick-draft-field">
															<span class="edminboost-productivity-preview__quick-draft-label"><?php esc_html_e( 'Content', 'edminboost-smart-admin-productivity-tool' ); ?></span>
															<span class="edminboost-productivity-preview__quick-draft-textarea"></span>
														</p>
														<span class="button button-small"><?php esc_html_e( 'Save Draft', 'edminboost-smart-admin-productivity-tool' ); ?></span>
													</div>
													<?php
													break;

												case 'remove_wp_news':
													?>
													<ul class="edminboost-productivity-preview__news">
														<li><?php esc_html_e( 'WordCamp US 2026 announced', 'edminboost-smart-admin-productivity-tool' ); ?></li>
														<li><?php esc_html_e( 'WordPress 6.9 release candidate', 'edminboost-smart-admin-productivity-tool' ); ?></li>
													</ul>
													<?php
													break;
											}
											?>
										</div>
										<span class="edminboost-productivity-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_tooltip_text ); ?></span>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="edminboost-productivity-preview__screen-meta">
				<div class="edminboost-productivity-preview__screen-meta-links">
					<?php foreach ( $edminboost_screen_tabs as $edminboost_screen_tab ) : ?>
						<?php
						$edminboost_is_removed = $edminboost_hide_screen_enabled;
						$edminboost_tab_class  = 'edminboost-productivity-preview__screen-tab';
						if ( $edminboost_is_removed ) {
							$edminboost_tab_class .= ' is-removed';
						}

						$edminboost_tooltip_loaded = sprintf(
							/* translators: %s: screen tab label */
							__( '%s — Visible', 'edminboost-smart-admin-productivity-tool' ),
							$edminboost_screen_tab['label']
						);
						$edminboost_tooltip_removed = sprintf(
							/* translators: %s: screen tab label */
							__( '%s — Hidden', 'edminboost-smart-admin-productivity-tool' ),
							$edminboost_screen_tab['label']
						);
						$edminboost_tooltip_text = $edminboost_is_removed ? $edminboost_tooltip_removed : $edminboost_tooltip_loaded;
						?>
						<div
							class="<?php echo esc_attr( $edminboost_tab_class ); ?>"
							data-preview="<?php echo esc_attr( $edminboost_current_preview['preview_key'] ); ?>"
							data-tooltip-loaded="<?php echo esc_attr( $edminboost_tooltip_loaded ); ?>"
							data-tooltip-removed="<?php echo esc_attr( $edminboost_tooltip_removed ); ?>"
							tabindex="0"
							aria-label="<?php echo esc_attr( $edminboost_tooltip_text ); ?>"
						>
							<button type="button" class="button show-settings" tabindex="-1" aria-hidden="true">
								<?php echo esc_html( $edminboost_screen_tab['label'] ); ?>
							</button>
							<span class="edminboost-productivity-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_tooltip_text ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
