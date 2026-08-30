<?php
/**
 * Live previews for Productivity tab settings.
 *
 * @package EdminBoost
 *
 * @var array  $features Feature settings.
 * @var string $preview  Preview key: admin_notices.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preview = isset( $preview ) ? sanitize_key( $preview ) : '';

if ( 'admin_notices' !== $preview ) {
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

$hide_notices_enabled = ! empty( $features['hide_admin_notices'] );
$hide_screen_enabled  = ! empty( $features['hide_screen_help'] );

$preview_items = array(
	array(
		'preview'  => 'hide_admin_notices',
		'removable' => true,
		'label'    => __( 'Success notice', EDMINBOOST_TEXT_DOMAIN ),
		'code'     => __( 'Settings saved.', EDMINBOOST_TEXT_DOMAIN ),
		'removed'  => $hide_notices_enabled,
	),
	array(
		'preview'  => 'hide_admin_notices',
		'removable' => true,
		'label'    => __( 'Info notice', EDMINBOOST_TEXT_DOMAIN ),
		'code'     => __( 'There is a new version available.', EDMINBOOST_TEXT_DOMAIN ),
		'removed'  => $hide_notices_enabled,
	),
	array(
		'preview'  => 'hide_admin_notices',
		'removable' => false,
		'label'    => __( 'Warning notice', EDMINBOOST_TEXT_DOMAIN ),
		'code'     => __( 'Your site is running an outdated PHP version.', EDMINBOOST_TEXT_DOMAIN ),
		'removed'  => false,
	),
	array(
		'preview'  => 'hide_admin_notices',
		'removable' => false,
		'label'    => __( 'Error notice', EDMINBOOST_TEXT_DOMAIN ),
		'code'     => __( 'Plugin could not be activated.', EDMINBOOST_TEXT_DOMAIN ),
		'removed'  => false,
	),
	array(
		'preview'  => 'hide_screen_help',
		'removable' => true,
		'label'    => __( 'Screen tab', EDMINBOOST_TEXT_DOMAIN ),
		'code'     => __( 'Screen Options', EDMINBOOST_TEXT_DOMAIN ),
		'removed'  => $hide_screen_enabled,
	),
	array(
		'preview'  => 'hide_screen_help',
		'removable' => true,
		'label'    => __( 'Screen tab', EDMINBOOST_TEXT_DOMAIN ),
		'code'     => __( 'Help', EDMINBOOST_TEXT_DOMAIN ),
		'removed'  => $hide_screen_enabled,
	),
);
?>
<div
	id="edminboost-productivity-admin-notices-preview"
	class="edminboost-performance-preview"
	style="<?php echo esc_attr( $preview_style_vars ); ?>"
	role="region"
	aria-label="<?php esc_attr_e( 'Admin notices and screen tabs live preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
	aria-live="polite"
>
	<p class="edminboost-performance-preview__lead"><?php esc_html_e( 'Live preview', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	<p class="edminboost-performance-preview__desc"><?php esc_html_e( 'Shows admin notices and screen tabs affected by the toggles above.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	<ul class="edminboost-performance-preview__list edminboost-performance-preview__list--stacked">
		<?php foreach ( $preview_items as $preview_item ) : ?>
			<?php
			$is_removed = ! empty( $preview_item['removed'] );
			$item_class = 'edminboost-performance-preview__item';
			if ( $is_removed ) {
				$item_class .= ' is-removed';
			}

			if ( ! empty( $preview_item['removable'] ) ) {
				$tooltip_loaded = sprintf(
					/* translators: 1: preview label, 2: preview value */
					__( '%1$s: %2$s — Loaded', EDMINBOOST_TEXT_DOMAIN ),
					$preview_item['label'],
					$preview_item['code']
				);
				$tooltip_removed = sprintf(
					/* translators: 1: preview label, 2: preview value */
					__( '%1$s: %2$s — Removed', EDMINBOOST_TEXT_DOMAIN ),
					$preview_item['label'],
					$preview_item['code']
				);
			} else {
				$tooltip_loaded = sprintf(
					/* translators: 1: preview label, 2: preview value */
					__( '%1$s: %2$s — Always visible', EDMINBOOST_TEXT_DOMAIN ),
					$preview_item['label'],
					$preview_item['code']
				);
				$tooltip_removed = $tooltip_loaded;
			}

			$tooltip_text = $is_removed ? $tooltip_removed : $tooltip_loaded;
			?>
			<li
				class="<?php echo esc_attr( $item_class ); ?>"
				<?php if ( ! empty( $preview_item['removable'] ) ) : ?>
					data-preview="<?php echo esc_attr( $preview_item['preview'] ); ?>"
				<?php endif; ?>
				data-tooltip-loaded="<?php echo esc_attr( $tooltip_loaded ); ?>"
				data-tooltip-removed="<?php echo esc_attr( $tooltip_removed ); ?>"
				tabindex="0"
				aria-label="<?php echo esc_attr( $tooltip_text ); ?>"
			>
				<span class="edminboost-performance-preview__item-label"><?php echo esc_html( $preview_item['label'] ); ?></span>
				<code class="edminboost-performance-preview__code"><?php echo esc_html( $preview_item['code'] ); ?></code>
				<span class="edminboost-performance-preview__status-dot" aria-hidden="true"></span>
				<span class="edminboost-performance-preview__tooltip" role="tooltip"><?php echo esc_html( $tooltip_text ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
