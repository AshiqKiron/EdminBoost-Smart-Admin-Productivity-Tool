<?php
/**
 * Live preview for Appearance admin bar cleanup toggles.
 *
 * @package EdminBoost
 *
 * @var array $behavior Current behavior settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$behavior = isset( $behavior ) && is_array( $behavior )
	? $behavior
	: EDMINBOOST_Command_Center::get_defaults()['behavior'];

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

$preview_items = array(
	array(
		'key'   => 'hide_wp_logo',
		'label' => __( 'WordPress logo', EDMINBOOST_TEXT_DOMAIN ),
		'icon'  => 'dashicons-wordpress',
		'class' => 'edminboost-declutter-preview__item--brand',
	),
	array(
		'key'   => 'hide_update_counters',
		'label' => __( 'Updates', EDMINBOOST_TEXT_DOMAIN ),
		'icon'  => 'dashicons-update',
		'badge' => '3',
	),
	array(
		'key'   => 'hide_comments',
		'label' => __( 'Comments', EDMINBOOST_TEXT_DOMAIN ),
		'icon'  => 'dashicons-admin-comments',
	),
	array(
		'key'   => 'hide_new_content',
		'label' => _x( 'New', 'admin bar new content menu', EDMINBOOST_TEXT_DOMAIN ),
		'icon'  => 'dashicons-plus',
	),
	array(
		'key'   => 'hide_customize',
		'label' => __( 'Customize', EDMINBOOST_TEXT_DOMAIN ),
		'icon'  => 'dashicons-admin-appearance',
	),
	array(
		'key'   => 'hide_howdy',
		'label' => __( 'Howdy, Admin', EDMINBOOST_TEXT_DOMAIN ),
		'icon'  => 'dashicons-admin-users',
		'class' => 'edminboost-declutter-preview__item--profile',
	),
);
?>
<div
	id="edminboost-declutter-preview"
	class="edminboost-declutter-preview"
	style="<?php echo esc_attr( $preview_style_vars ); ?>"
	role="region"
	aria-label="<?php esc_attr_e( 'Admin bar cleanup live preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
	aria-live="polite"
>
	<p class="edminboost-declutter-preview__lead"><?php esc_html_e( 'Live preview', EDMINBOOST_TEXT_DOMAIN ); ?></p>
	<p class="edminboost-declutter-preview__desc"><?php esc_html_e( 'Shows native WordPress admin bar items affected by the toggles above.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

	<div class="edminboost-declutter-preview__canvas" aria-hidden="true">
		<?php foreach ( $preview_items as $preview_item ) : ?>
			<?php
			$behavior_key = $preview_item['key'];
			$is_hidden    = ! empty( $behavior[ $behavior_key ] );
			$item_class   = 'edminboost-declutter-preview__item';
			if ( ! empty( $preview_item['class'] ) ) {
				$item_class .= ' ' . $preview_item['class'];
			}
			if ( $is_hidden ) {
				$item_class .= ' is-hidden';
			}

			$tooltip_visible = sprintf(
				/* translators: %s: admin bar item label */
				__( '%s — Visible', EDMINBOOST_TEXT_DOMAIN ),
				$preview_item['label']
			);
			$tooltip_hidden = sprintf(
				/* translators: %s: admin bar item label */
				__( '%s — Hidden', EDMINBOOST_TEXT_DOMAIN ),
				$preview_item['label']
			);
			$tooltip_text   = $is_hidden ? $tooltip_hidden : $tooltip_visible;
			?>
			<span
				class="<?php echo esc_attr( $item_class ); ?>"
				data-preview="<?php echo esc_attr( $behavior_key ); ?>"
				data-tooltip-visible="<?php echo esc_attr( $tooltip_visible ); ?>"
				data-tooltip-hidden="<?php echo esc_attr( $tooltip_hidden ); ?>"
				tabindex="0"
				aria-label="<?php echo esc_attr( $tooltip_text ); ?>"
			>
				<span class="dashicons <?php echo esc_attr( $preview_item['icon'] ); ?>" aria-hidden="true"></span>
				<span class="edminboost-declutter-preview__label"><?php echo esc_html( $preview_item['label'] ); ?></span>
				<?php if ( ! empty( $preview_item['badge'] ) ) : ?>
					<span class="edminboost-declutter-preview__badge" aria-hidden="true"><?php echo esc_html( $preview_item['badge'] ); ?></span>
				<?php endif; ?>
				<span class="edminboost-declutter-preview__tooltip" role="tooltip"><?php echo esc_html( $tooltip_text ); ?></span>
			</span>
		<?php endforeach; ?>
	</div>
</div>
