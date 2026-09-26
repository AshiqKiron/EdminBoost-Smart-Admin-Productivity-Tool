<?php
/**
 * Live preview for Appearance extras (font size, background, favicon, status colors).
 *
 * @package EdminBoost
 *
 * @var array  $edminboost_theme          Current theme settings.
 * @var array  $edminboost_preview_colors Resolved theme color tokens.
 * @var string $edminboost_preview_id     Root element id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_preview_id = isset( $edminboost_preview_id ) ? $edminboost_preview_id : 'edminboost-theme-extras-preview';
$edminboost_theme      = isset( $edminboost_theme ) && is_array( $edminboost_theme ) ? $edminboost_theme : EDMINBOOST_Theme::get_settings();
$edminboost_preview_colors = isset( $edminboost_preview_colors ) && is_array( $edminboost_preview_colors ) ? $edminboost_preview_colors : EDMINBOOST_Theme::resolve_preview_colors(
	isset( $edminboost_theme['preset'] ) ? $edminboost_theme['preset'] : 'default',
	isset( $edminboost_theme['mode'] ) ? $edminboost_theme['mode'] : 'light',
	$edminboost_theme
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

$edminboost_font_size        = isset( $edminboost_theme['font_size'] ) ? max( 12, min( 20, absint( $edminboost_theme['font_size'] ) ) ) : 14;
$edminboost_admin_bg_color   = ! empty( $edminboost_theme['admin_bg_color'] ) ? $edminboost_theme['admin_bg_color'] : $edminboost_preview_colors['content'];
$edminboost_favicon_id       = isset( $edminboost_theme['admin_favicon_id'] ) ? absint( $edminboost_theme['admin_favicon_id'] ) : 0;
$edminboost_bg_image_id      = isset( $edminboost_theme['admin_bg_image_id'] ) ? absint( $edminboost_theme['admin_bg_image_id'] ) : 0;
$edminboost_favicon_url      = $edminboost_favicon_id ? wp_get_attachment_image_url( $edminboost_favicon_id, 'thumbnail' ) : '';
$edminboost_bg_image_url     = $edminboost_bg_image_id ? wp_get_attachment_image_url( $edminboost_bg_image_id, 'medium' ) : '';
$edminboost_schedule_enabled = ! empty( $edminboost_theme['schedule_dark_mode'] );
$edminboost_schedule_start   = isset( $edminboost_theme['dark_mode_start'] ) ? $edminboost_theme['dark_mode_start'] : '18:00';
$edminboost_schedule_end     = isset( $edminboost_theme['dark_mode_end'] ) ? $edminboost_theme['dark_mode_end'] : '06:00';
$edminboost_status_colors    = isset( $edminboost_theme['status_colors'] ) && is_array( $edminboost_theme['status_colors'] )
	? $edminboost_theme['status_colors']
	: EDMINBOOST_Theme::get_defaults()['status_colors'];

$edminboost_status_labels = array(
	'publish' => _x( 'Published', 'post status', 'edminboost' ),
	'pending' => _x( 'Pending', 'post status', 'edminboost' ),
	'future'  => _x( 'Scheduled', 'post status', 'edminboost' ),
	'private' => _x( 'Private', 'post status', 'edminboost' ),
	'draft'   => _x( 'Draft', 'post status', 'edminboost' ),
	'trash'   => _x( 'Trash', 'post status', 'edminboost' ),
);

$edminboost_preview_style_vars = sprintf(
	'--eb-te-font-size:%1$spx;--eb-te-bg:%2$s;--eb-op-accent:%3$s;--eb-op-surface:%4$s;--eb-op-text:%5$s;--eb-op-top:%6$s;--eb-op-sidebar:%7$s;--eb-op-content:%8$s;',
	esc_attr( (string) $edminboost_font_size ),
	esc_attr( $edminboost_admin_bg_color ),
	esc_attr( $edminboost_preview_colors['accent'] ),
	esc_attr( $edminboost_preview_colors['surface'] ),
	esc_attr( $edminboost_preview_colors['text'] ),
	esc_attr( $edminboost_preview_colors['topbar'] ),
	esc_attr( $edminboost_preview_colors['sidebar'] ),
	esc_attr( $edminboost_preview_colors['content'] )
);
?>
<div
	class="edminboost-theme-extras-preview"
	id="<?php echo esc_attr( $edminboost_preview_id ); ?>"
	style="<?php echo esc_attr( $edminboost_preview_style_vars ); ?>"
	data-favicon-id="<?php echo esc_attr( (string) $edminboost_favicon_id ); ?>"
	data-favicon-url="<?php echo esc_url( $edminboost_favicon_url ? $edminboost_favicon_url : '' ); ?>"
	data-bg-image-id="<?php echo esc_attr( (string) $edminboost_bg_image_id ); ?>"
	data-bg-image-url="<?php echo esc_url( $edminboost_bg_image_url ? $edminboost_bg_image_url : '' ); ?>"
	aria-live="polite"
>
	<p class="edminboost-theme-extras-preview__lead description">
		<?php esc_html_e( 'Preview font size, admin background, favicon, post status colors, and scheduled dark mode.', 'edminboost' ); ?>
	</p>

	<div class="edminboost-theme-extras-preview__browser" aria-hidden="true">
		<div class="edminboost-theme-extras-preview__tab">
			<span class="edminboost-theme-extras-preview__favicon" id="edminboost-theme-extras-preview-favicon">
				<?php if ( $edminboost_favicon_url ) : ?>
					<img src="<?php echo esc_url( $edminboost_favicon_url ); ?>" alt="" width="16" height="16" />
				<?php else : ?>
					<span class="dashicons dashicons-wordpress" aria-hidden="true"></span>
				<?php endif; ?>
			</span>
			<span class="edminboost-theme-extras-preview__tab-title"><?php esc_html_e( 'wp-admin', 'edminboost' ); ?></span>
		</div>
	</div>

	<div class="edminboost-theme-extras-preview__viewport" id="edminboost-theme-extras-preview-viewport">
		<div class="edminboost-theme-extras-preview__topbar"></div>
		<div class="edminboost-theme-extras-preview__layout">
			<div class="edminboost-theme-extras-preview__sidebar"></div>
			<div class="edminboost-theme-extras-preview__main">
				<p class="edminboost-theme-extras-preview__sample" id="edminboost-theme-extras-preview-sample">
					<?php esc_html_e( 'Sample admin text at the selected font size.', 'edminboost' ); ?>
				</p>

				<div class="edminboost-theme-extras-preview__table-wrap">
					<table class="edminboost-theme-extras-preview__table">
						<caption class="screen-reader-text"><?php esc_html_e( 'Post status row colors preview', 'edminboost' ); ?></caption>
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e( 'Title', 'edminboost' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Status', 'edminboost' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $edminboost_status_labels as $edminboost_status_key => $edminboost_status_label ) : ?>
								<?php
								$edminboost_row_color = isset( $edminboost_status_colors[ $edminboost_status_key ] ) ? $edminboost_status_colors[ $edminboost_status_key ] : '';
								$edminboost_row_style = $edminboost_row_color ? 'background-color:' . esc_attr( $edminboost_row_color ) . ';' : '';
								?>
								<tr
									class="edminboost-theme-extras-preview__status-row status-<?php echo esc_attr( $edminboost_status_key ); ?>"
									data-status="<?php echo esc_attr( $edminboost_status_key ); ?>"
									<?php echo $edminboost_row_style ? 'style="' . esc_attr( $edminboost_row_style ) . '"' : ''; ?>
								>
									<td><?php echo esc_html( sprintf( /* translators: %s: post status slug */ __( 'Sample %s post', 'edminboost' ), $edminboost_status_label ) ); ?></td>
									<td><?php echo esc_html( $edminboost_status_label ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div
		class="edminboost-theme-extras-preview__schedule<?php echo $edminboost_schedule_enabled ? ' is-active' : ''; ?>"
		id="edminboost-theme-extras-preview-schedule"
		<?php echo $edminboost_schedule_enabled ? '' : 'hidden'; ?>
	>
		<p class="edminboost-theme-extras-preview__schedule-label">
			<span class="dashicons dashicons-clock" aria-hidden="true"></span>
			<?php esc_html_e( 'Scheduled dark mode (Auto color mode)', 'edminboost' ); ?>
		</p>
		<div
			class="edminboost-theme-extras-preview__schedule-track"
			style="--eb-te-schedule-start: <?php echo esc_attr( $edminboost_schedule_start ); ?>; --eb-te-schedule-end: <?php echo esc_attr( $edminboost_schedule_end ); ?>;"
		>
			<span class="edminboost-theme-extras-preview__schedule-day" aria-hidden="true"></span>
			<span class="edminboost-theme-extras-preview__schedule-night" aria-hidden="true"></span>
		</div>
		<p class="edminboost-theme-extras-preview__schedule-times description">
			<span id="edminboost-theme-extras-preview-schedule-start"><?php echo esc_html( $edminboost_schedule_start ); ?></span>
			&ndash;
			<span id="edminboost-theme-extras-preview-schedule-end"><?php echo esc_html( $edminboost_schedule_end ); ?></span>
		</p>
	</div>
</div>
