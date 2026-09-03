<?php
/**
 * Live preview for Appearance extras (font size, background, favicon, status colors).
 *
 * @package EdminBoost
 *
 * @var array  $theme          Current theme settings.
 * @var array  $preview_colors Resolved theme color tokens.
 * @var string $preview_id     Root element id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preview_id     = isset( $preview_id ) ? $preview_id : 'edminboost-theme-extras-preview';
$theme          = isset( $theme ) && is_array( $theme ) ? $theme : EDMINBOOST_Theme::get_settings();
$preview_colors = isset( $preview_colors ) && is_array( $preview_colors ) ? $preview_colors : EDMINBOOST_Theme::resolve_preview_colors(
	isset( $theme['preset'] ) ? $theme['preset'] : 'default',
	isset( $theme['mode'] ) ? $theme['mode'] : 'light',
	$theme
);

$color_defaults = array(
	'accent'  => '#2271b1',
	'surface' => '#ffffff',
	'text'    => '#1d2327',
	'topbar'  => '#1d2327',
	'sidebar' => '#1d2327',
	'content' => '#f0f0f1',
);
$preview_colors = wp_parse_args( $preview_colors, $color_defaults );

$font_size        = isset( $theme['font_size'] ) ? max( 12, min( 20, absint( $theme['font_size'] ) ) ) : 14;
$admin_bg_color   = ! empty( $theme['admin_bg_color'] ) ? $theme['admin_bg_color'] : $preview_colors['content'];
$favicon_id       = isset( $theme['admin_favicon_id'] ) ? absint( $theme['admin_favicon_id'] ) : 0;
$bg_image_id      = isset( $theme['admin_bg_image_id'] ) ? absint( $theme['admin_bg_image_id'] ) : 0;
$favicon_url      = $favicon_id ? wp_get_attachment_image_url( $favicon_id, 'thumbnail' ) : '';
$bg_image_url     = $bg_image_id ? wp_get_attachment_image_url( $bg_image_id, 'medium' ) : '';
$schedule_enabled = ! empty( $theme['schedule_dark_mode'] );
$schedule_start   = isset( $theme['dark_mode_start'] ) ? $theme['dark_mode_start'] : '18:00';
$schedule_end     = isset( $theme['dark_mode_end'] ) ? $theme['dark_mode_end'] : '06:00';
$status_colors    = isset( $theme['status_colors'] ) && is_array( $theme['status_colors'] )
	? $theme['status_colors']
	: EDMINBOOST_Theme::get_defaults()['status_colors'];

$status_labels = array(
	'publish' => _x( 'Published', 'post status', EDMINBOOST_TEXT_DOMAIN ),
	'pending' => _x( 'Pending', 'post status', EDMINBOOST_TEXT_DOMAIN ),
	'future'  => _x( 'Scheduled', 'post status', EDMINBOOST_TEXT_DOMAIN ),
	'private' => _x( 'Private', 'post status', EDMINBOOST_TEXT_DOMAIN ),
	'draft'   => _x( 'Draft', 'post status', EDMINBOOST_TEXT_DOMAIN ),
	'trash'   => _x( 'Trash', 'post status', EDMINBOOST_TEXT_DOMAIN ),
);

$preview_style_vars = sprintf(
	'--eb-te-font-size:%1$spx;--eb-te-bg:%2$s;--eb-op-accent:%3$s;--eb-op-surface:%4$s;--eb-op-text:%5$s;--eb-op-top:%6$s;--eb-op-sidebar:%7$s;--eb-op-content:%8$s;',
	esc_attr( (string) $font_size ),
	esc_attr( $admin_bg_color ),
	esc_attr( $preview_colors['accent'] ),
	esc_attr( $preview_colors['surface'] ),
	esc_attr( $preview_colors['text'] ),
	esc_attr( $preview_colors['topbar'] ),
	esc_attr( $preview_colors['sidebar'] ),
	esc_attr( $preview_colors['content'] )
);
?>
<div
	class="edminboost-theme-extras-preview"
	id="<?php echo esc_attr( $preview_id ); ?>"
	style="<?php echo esc_attr( $preview_style_vars ); ?>"
	data-favicon-id="<?php echo esc_attr( (string) $favicon_id ); ?>"
	data-favicon-url="<?php echo esc_url( $favicon_url ? $favicon_url : '' ); ?>"
	data-bg-image-id="<?php echo esc_attr( (string) $bg_image_id ); ?>"
	data-bg-image-url="<?php echo esc_url( $bg_image_url ? $bg_image_url : '' ); ?>"
	aria-live="polite"
>
	<p class="edminboost-theme-extras-preview__lead description">
		<?php esc_html_e( 'Preview font size, admin background, favicon, post status colors, and scheduled dark mode.', EDMINBOOST_TEXT_DOMAIN ); ?>
	</p>

	<div class="edminboost-theme-extras-preview__browser" aria-hidden="true">
		<div class="edminboost-theme-extras-preview__tab">
			<span class="edminboost-theme-extras-preview__favicon" id="edminboost-theme-extras-preview-favicon">
				<?php if ( $favicon_url ) : ?>
					<img src="<?php echo esc_url( $favicon_url ); ?>" alt="" width="16" height="16" />
				<?php else : ?>
					<span class="dashicons dashicons-wordpress" aria-hidden="true"></span>
				<?php endif; ?>
			</span>
			<span class="edminboost-theme-extras-preview__tab-title"><?php esc_html_e( 'wp-admin', EDMINBOOST_TEXT_DOMAIN ); ?></span>
		</div>
	</div>

	<div class="edminboost-theme-extras-preview__viewport" id="edminboost-theme-extras-preview-viewport">
		<div class="edminboost-theme-extras-preview__topbar"></div>
		<div class="edminboost-theme-extras-preview__layout">
			<div class="edminboost-theme-extras-preview__sidebar"></div>
			<div class="edminboost-theme-extras-preview__main">
				<p class="edminboost-theme-extras-preview__sample" id="edminboost-theme-extras-preview-sample">
					<?php esc_html_e( 'Sample admin text at the selected font size.', EDMINBOOST_TEXT_DOMAIN ); ?>
				</p>

				<div class="edminboost-theme-extras-preview__table-wrap">
					<table class="edminboost-theme-extras-preview__table">
						<caption class="screen-reader-text"><?php esc_html_e( 'Post status row colors preview', EDMINBOOST_TEXT_DOMAIN ); ?></caption>
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e( 'Title', EDMINBOOST_TEXT_DOMAIN ); ?></th>
								<th scope="col"><?php esc_html_e( 'Status', EDMINBOOST_TEXT_DOMAIN ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $status_labels as $status_key => $status_label ) : ?>
								<?php
								$row_color = isset( $status_colors[ $status_key ] ) ? $status_colors[ $status_key ] : '';
								$row_style = $row_color ? 'background-color:' . esc_attr( $row_color ) . ';' : '';
								?>
								<tr
									class="edminboost-theme-extras-preview__status-row status-<?php echo esc_attr( $status_key ); ?>"
									data-status="<?php echo esc_attr( $status_key ); ?>"
									<?php echo $row_style ? 'style="' . esc_attr( $row_style ) . '"' : ''; ?>
								>
									<td><?php echo esc_html( sprintf( /* translators: %s: post status slug */ __( 'Sample %s post', EDMINBOOST_TEXT_DOMAIN ), $status_label ) ); ?></td>
									<td><?php echo esc_html( $status_label ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div
		class="edminboost-theme-extras-preview__schedule<?php echo $schedule_enabled ? ' is-active' : ''; ?>"
		id="edminboost-theme-extras-preview-schedule"
		<?php echo $schedule_enabled ? '' : 'hidden'; ?>
	>
		<p class="edminboost-theme-extras-preview__schedule-label">
			<span class="dashicons dashicons-clock" aria-hidden="true"></span>
			<?php esc_html_e( 'Scheduled dark mode (Auto color mode)', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
		<div
			class="edminboost-theme-extras-preview__schedule-track"
			style="--eb-te-schedule-start: <?php echo esc_attr( $schedule_start ); ?>; --eb-te-schedule-end: <?php echo esc_attr( $schedule_end ); ?>;"
		>
			<span class="edminboost-theme-extras-preview__schedule-day" aria-hidden="true"></span>
			<span class="edminboost-theme-extras-preview__schedule-night" aria-hidden="true"></span>
		</div>
		<p class="edminboost-theme-extras-preview__schedule-times description">
			<span id="edminboost-theme-extras-preview-schedule-start"><?php echo esc_html( $schedule_start ); ?></span>
			&ndash;
			<span id="edminboost-theme-extras-preview-schedule-end"><?php echo esc_html( $schedule_end ); ?></span>
		</p>
	</div>
</div>
