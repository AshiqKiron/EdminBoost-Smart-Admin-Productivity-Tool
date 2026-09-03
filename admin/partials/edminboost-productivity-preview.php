<?php
/**
 * Live previews for Productivity tab settings.
 *
 * @package EdminBoost
 *
 * @var array  $features Feature settings.
 * @var string $preview  Preview key: notices|screen_help|dashboard_widgets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preview = isset( $preview ) ? sanitize_key( $preview ) : '';

if ( ! in_array( $preview, array( 'notices', 'screen_help', 'dashboard_widgets' ), true ) ) {
	return;
}

$theme_settings = EDMINBOOST_Theme::get_settings();
$preview_colors = EDMINBOOST_Theme::resolve_preview_colors(
	isset( $theme_settings['preset'] ) ? $theme_settings['preset'] : 'default',
	isset( $theme_settings['mode'] ) ? $theme_settings['mode'] : 'light',
	$theme_settings
);
$color_defaults = array(
	'accent'  => '#2271b1',
	'surface' => '#ffffff',
	'text'    => '#1d2327',
	'topbar'  => '#1d2327',
	'sidebar' => '#1d2327',
	'content' => '#f0f0f1',
);
$preview_colors     = wp_parse_args( $preview_colors, $color_defaults );
$preview_style_vars = sprintf(
	'--eb-op-accent:%1$s;--eb-op-surface:%2$s;--eb-op-text:%3$s;--eb-op-top:%4$s;--eb-op-sidebar:%5$s;--eb-op-content:%6$s;',
	esc_attr( $preview_colors['accent'] ),
	esc_attr( $preview_colors['surface'] ),
	esc_attr( $preview_colors['text'] ),
	esc_attr( $preview_colors['topbar'] ),
	esc_attr( $preview_colors['sidebar'] ),
	esc_attr( $preview_colors['content'] )
);

$hide_notices_enabled      = ! empty( $features['hide_admin_notices'] );
$hide_screen_enabled       = ! empty( $features['hide_screen_help'] );
$dashboard_widgets_enabled = ! empty( $features['dashboard_widgets']['enabled'] );

$preview_meta = array(
	'notices'           => array(
		'id'          => 'edminboost-productivity-notices-preview',
		'aria_label'  => __( 'Admin notices live preview', EDMINBOOST_TEXT_DOMAIN ),
		'preview_key' => 'hide_admin_notices',
	),
	'screen_help'       => array(
		'id'          => 'edminboost-productivity-screen-preview',
		'aria_label'  => __( 'Screen tabs live preview', EDMINBOOST_TEXT_DOMAIN ),
		'preview_key' => 'hide_screen_help',
	),
	'dashboard_widgets' => array(
		'id'          => 'edminboost-productivity-dashboard-preview',
		'aria_label'  => __( 'Dashboard widgets live preview', EDMINBOOST_TEXT_DOMAIN ),
		'preview_key' => '',
	),
);

$notice_items = array(
	array(
		'removable' => true,
		'class'     => 'notice-success is-dismissible',
		'message'   => __( 'Settings saved.', EDMINBOOST_TEXT_DOMAIN ),
	),
	array(
		'removable' => true,
		'class'     => 'notice-info',
		'message'   => __( 'There is a new version available.', EDMINBOOST_TEXT_DOMAIN ),
	),
	array(
		'removable' => false,
		'class'     => 'notice-warning',
		'message'   => __( 'Your site is running an outdated PHP version.', EDMINBOOST_TEXT_DOMAIN ),
	),
	array(
		'removable' => false,
		'class'     => 'notice-error',
		'message'   => __( 'Plugin could not be activated.', EDMINBOOST_TEXT_DOMAIN ),
	),
);

$screen_tabs = array(
	array(
		'label' => __( 'Screen Options', EDMINBOOST_TEXT_DOMAIN ),
	),
	array(
		'label' => __( 'Help', EDMINBOOST_TEXT_DOMAIN ),
	),
);

$dashboard_widget_labels = EDMINBOOST_Dashboard::get_widget_labels();
$dashboard_widget_layout = array(
	'welcome' => array( 'remove_welcome_panel' ),
	'main'    => array( 'remove_at_a_glance', 'remove_activity', 'remove_site_health' ),
	'side'    => array( 'remove_quick_press', 'remove_wp_news' ),
);

$current_preview = $preview_meta[ $preview ];
?>
<div
	id="<?php echo esc_attr( $current_preview['id'] ); ?>"
	class="edminboost-productivity-preview"
	style="<?php echo esc_attr( $preview_style_vars ); ?>"
	role="region"
	aria-label="<?php echo esc_attr( $current_preview['aria_label'] ); ?>"
	aria-live="polite"
	<?php if ( ! empty( $current_preview['preview_key'] ) ) : ?>
		data-preview-toggle="<?php echo esc_attr( $current_preview['preview_key'] ); ?>"
	<?php endif; ?>
>
	<p class="edminboost-productivity-preview__lead"><?php esc_html_e( 'Live preview', EDMINBOOST_TEXT_DOMAIN ); ?></p>

	<div class="edminboost-productivity-preview__canvas edminboost-productivity-preview__canvas--<?php echo esc_attr( $preview ); ?>">
		<?php if ( 'notices' === $preview ) : ?>
			<div class="edminboost-productivity-preview__viewport" aria-hidden="true">
				<div class="edminboost-productivity-preview__topbar"></div>
				<div class="edminboost-productivity-preview__main">
					<div class="edminboost-productivity-preview__notices">
				<?php foreach ( $notice_items as $notice_item ) : ?>
					<?php
					$is_removed   = ! empty( $notice_item['removable'] ) && $hide_notices_enabled;
					$notice_class = 'edminboost-productivity-preview__notice ' . $notice_item['class'];
					if ( $is_removed ) {
						$notice_class .= ' is-removed';
					}

					if ( ! empty( $notice_item['removable'] ) ) {
						$tooltip_loaded = sprintf(
							/* translators: %s: notice message */
							__( '%s — Visible', EDMINBOOST_TEXT_DOMAIN ),
							$notice_item['message']
						);
						$tooltip_removed = sprintf(
							/* translators: %s: notice message */
							__( '%s — Hidden', EDMINBOOST_TEXT_DOMAIN ),
							$notice_item['message']
						);
					} else {
						$tooltip_loaded = sprintf(
							/* translators: %s: notice message */
							__( '%s — Always visible', EDMINBOOST_TEXT_DOMAIN ),
							$notice_item['message']
						);
						$tooltip_removed = $tooltip_loaded;
					}

					$tooltip_text = $is_removed ? $tooltip_removed : $tooltip_loaded;
					?>
					<div
						class="<?php echo esc_attr( $notice_class ); ?>"
						<?php if ( ! empty( $notice_item['removable'] ) ) : ?>
							data-preview="<?php echo esc_attr( $current_preview['preview_key'] ); ?>"
						<?php endif; ?>
						data-tooltip-loaded="<?php echo esc_attr( $tooltip_loaded ); ?>"
						data-tooltip-removed="<?php echo esc_attr( $tooltip_removed ); ?>"
						tabindex="0"
						aria-label="<?php echo esc_attr( $tooltip_text ); ?>"
					>
						<p><?php echo esc_html( $notice_item['message'] ); ?></p>
						<?php if ( false !== strpos( $notice_item['class'], 'is-dismissible' ) ) : ?>
							<button type="button" class="notice-dismiss" tabindex="-1" aria-hidden="true">
								<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							</button>
						<?php endif; ?>
						<span class="edminboost-productivity-preview__tooltip" role="tooltip"><?php echo esc_html( $tooltip_text ); ?></span>
					</div>
				<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php elseif ( 'dashboard_widgets' === $preview ) : ?>
			<div class="edminboost-productivity-preview__viewport" aria-hidden="true">
				<div class="edminboost-productivity-preview__topbar"></div>
				<div class="edminboost-productivity-preview__main">
					<div class="edminboost-productivity-preview__dashboard">
						<p class="edminboost-productivity-preview__dashboard-heading"><?php esc_html_e( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ); ?></p>
						<?php foreach ( $dashboard_widget_layout as $area => $widget_keys ) : ?>
							<div class="edminboost-productivity-preview__dashboard-area edminboost-productivity-preview__dashboard-area--<?php echo esc_attr( $area ); ?>">
								<?php foreach ( $widget_keys as $widget_key ) : ?>
									<?php
									if ( ! isset( $dashboard_widget_labels[ $widget_key ] ) ) {
										continue;
									}

									$widget_label = $dashboard_widget_labels[ $widget_key ];
									$is_removed   = $dashboard_widgets_enabled && ! empty( $features['dashboard_widgets'][ $widget_key ] );
									$widget_class = 'edminboost-productivity-preview__widget';
									if ( $is_removed ) {
										$widget_class .= ' is-removed';
									}

									$tooltip_loaded = sprintf(
										/* translators: %s: dashboard widget label */
										__( '%s — Visible', EDMINBOOST_TEXT_DOMAIN ),
										$widget_label
									);
									$tooltip_removed = sprintf(
										/* translators: %s: dashboard widget label */
										__( '%s — Removed', EDMINBOOST_TEXT_DOMAIN ),
										$widget_label
									);
									$tooltip_text = $is_removed ? $tooltip_removed : $tooltip_loaded;
									?>
									<div
										class="<?php echo esc_attr( $widget_class ); ?>"
										data-widget-key="<?php echo esc_attr( $widget_key ); ?>"
										data-tooltip-loaded="<?php echo esc_attr( $tooltip_loaded ); ?>"
										data-tooltip-removed="<?php echo esc_attr( $tooltip_removed ); ?>"
										tabindex="0"
										aria-label="<?php echo esc_attr( $tooltip_text ); ?>"
									>
										<div class="edminboost-productivity-preview__widget-head">
											<span class="edminboost-productivity-preview__widget-title"><?php echo esc_html( $widget_label ); ?></span>
										</div>
										<div class="edminboost-productivity-preview__widget-body" aria-hidden="true">
											<?php
											switch ( $widget_key ) {
												case 'remove_welcome_panel':
													?>
													<div class="edminboost-productivity-preview__welcome">
														<p class="edminboost-productivity-preview__welcome-lead"><?php esc_html_e( 'Welcome to WordPress!', EDMINBOOST_TEXT_DOMAIN ); ?></p>
														<p class="edminboost-productivity-preview__welcome-links">
															<span><?php esc_html_e( 'Customize your site', EDMINBOOST_TEXT_DOMAIN ); ?></span>
															<span aria-hidden="true">&middot;</span>
															<span><?php esc_html_e( 'Write your first post', EDMINBOOST_TEXT_DOMAIN ); ?></span>
														</p>
													</div>
													<?php
													break;

												case 'remove_at_a_glance':
													?>
													<ul class="edminboost-productivity-preview__glance">
														<li><?php esc_html_e( '1 Post', EDMINBOOST_TEXT_DOMAIN ); ?></li>
														<li><?php esc_html_e( '1 Page', EDMINBOOST_TEXT_DOMAIN ); ?></li>
														<li><?php esc_html_e( '1 Comment', EDMINBOOST_TEXT_DOMAIN ); ?></li>
													</ul>
													<?php
													break;

												case 'remove_activity':
													?>
													<ul class="edminboost-productivity-preview__activity">
														<li>
															<span class="dashicons dashicons-admin-comments" aria-hidden="true"></span>
															<?php esc_html_e( 'Comment on Hello world!', EDMINBOOST_TEXT_DOMAIN ); ?>
														</li>
														<li>
															<span class="dashicons dashicons-admin-post" aria-hidden="true"></span>
															<?php esc_html_e( 'Hello world! published', EDMINBOOST_TEXT_DOMAIN ); ?>
														</li>
													</ul>
													<?php
													break;

												case 'remove_site_health':
													?>
													<div class="edminboost-productivity-preview__site-health">
														<span class="edminboost-productivity-preview__site-health-badge"><?php esc_html_e( 'Good', EDMINBOOST_TEXT_DOMAIN ); ?></span>
														<p><?php esc_html_e( 'Your site\'s health is looking good.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
													</div>
													<?php
													break;

												case 'remove_quick_press':
													?>
													<div class="edminboost-productivity-preview__quick-draft">
														<p class="edminboost-productivity-preview__quick-draft-field">
															<span class="edminboost-productivity-preview__quick-draft-label"><?php esc_html_e( 'Title', EDMINBOOST_TEXT_DOMAIN ); ?></span>
															<span class="edminboost-productivity-preview__quick-draft-input"></span>
														</p>
														<p class="edminboost-productivity-preview__quick-draft-field">
															<span class="edminboost-productivity-preview__quick-draft-label"><?php esc_html_e( 'Content', EDMINBOOST_TEXT_DOMAIN ); ?></span>
															<span class="edminboost-productivity-preview__quick-draft-textarea"></span>
														</p>
														<span class="button button-small"><?php esc_html_e( 'Save Draft', EDMINBOOST_TEXT_DOMAIN ); ?></span>
													</div>
													<?php
													break;

												case 'remove_wp_news':
													?>
													<ul class="edminboost-productivity-preview__news">
														<li><?php esc_html_e( 'WordCamp US 2026 announced', EDMINBOOST_TEXT_DOMAIN ); ?></li>
														<li><?php esc_html_e( 'WordPress 6.9 release candidate', EDMINBOOST_TEXT_DOMAIN ); ?></li>
													</ul>
													<?php
													break;
											}
											?>
										</div>
										<span class="edminboost-productivity-preview__tooltip" role="tooltip"><?php echo esc_html( $tooltip_text ); ?></span>
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
					<?php foreach ( $screen_tabs as $screen_tab ) : ?>
						<?php
						$is_removed = $hide_screen_enabled;
						$tab_class  = 'edminboost-productivity-preview__screen-tab';
						if ( $is_removed ) {
							$tab_class .= ' is-removed';
						}

						$tooltip_loaded = sprintf(
							/* translators: %s: screen tab label */
							__( '%s — Visible', EDMINBOOST_TEXT_DOMAIN ),
							$screen_tab['label']
						);
						$tooltip_removed = sprintf(
							/* translators: %s: screen tab label */
							__( '%s — Hidden', EDMINBOOST_TEXT_DOMAIN ),
							$screen_tab['label']
						);
						$tooltip_text = $is_removed ? $tooltip_removed : $tooltip_loaded;
						?>
						<div
							class="<?php echo esc_attr( $tab_class ); ?>"
							data-preview="<?php echo esc_attr( $current_preview['preview_key'] ); ?>"
							data-tooltip-loaded="<?php echo esc_attr( $tooltip_loaded ); ?>"
							data-tooltip-removed="<?php echo esc_attr( $tooltip_removed ); ?>"
							tabindex="0"
							aria-label="<?php echo esc_attr( $tooltip_text ); ?>"
						>
							<button type="button" class="button show-settings" tabindex="-1" aria-hidden="true">
								<?php echo esc_html( $screen_tab['label'] ); ?>
							</button>
							<span class="edminboost-productivity-preview__tooltip" role="tooltip"><?php echo esc_html( $tooltip_text ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
