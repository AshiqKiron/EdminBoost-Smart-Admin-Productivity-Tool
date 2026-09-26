<?php
/**
 * Live preview for Appearance admin bar cleanup toggles.
 *
 * @package EdminBoost
 *
 * @var array $edminboost_behavior Current behavior settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_behavior = isset( $edminboost_behavior ) && is_array( $edminboost_behavior )
	? $edminboost_behavior
	: EDMINBOOST_Command_Center::get_defaults()['behavior'];

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
$edminboost_preview_colors     = wp_parse_args( $edminboost_preview_colors, $edminboost_color_defaults );
$edminboost_preview_style_vars = sprintf(
	'--eb-op-accent:%1$s;--eb-op-surface:%2$s;--eb-op-text:%3$s;--eb-op-top:%4$s;--eb-op-sidebar:%5$s;--eb-op-content:%6$s;',
	esc_attr( $edminboost_preview_colors['accent'] ),
	esc_attr( $edminboost_preview_colors['surface'] ),
	esc_attr( $edminboost_preview_colors['text'] ),
	esc_attr( $edminboost_preview_colors['topbar'] ),
	esc_attr( $edminboost_preview_colors['sidebar'] ),
	esc_attr( $edminboost_preview_colors['content'] )
);

$edminboost_preview_items = array(
	array(
		'key'   => 'hide_wp_logo',
		'label' => __( 'WordPress logo', 'edminboost' ),
		'icon'  => 'dashicons-wordpress',
		'class' => 'edminboost-declutter-preview__item--brand',
	),
	array(
		'key'   => 'hide_update_counters',
		'label' => __( 'Updates', 'edminboost' ),
		'icon'  => 'dashicons-update',
		'badge' => '3',
	),
	array(
		'key'   => 'hide_comments',
		'label' => __( 'Comments', 'edminboost' ),
		'icon'  => 'dashicons-admin-comments',
	),
	array(
		'key'   => 'hide_new_content',
		'label' => _x( 'New', 'admin bar new content menu', 'edminboost' ),
		'icon'  => 'dashicons-plus',
	),
	array(
		'key'   => 'hide_customize',
		'label' => __( 'Customize', 'edminboost' ),
		'icon'  => 'dashicons-admin-appearance',
	),
	array(
		'key'   => 'hide_howdy',
		'label' => __( 'Howdy, Admin', 'edminboost' ),
		'icon'  => 'dashicons-admin-users',
		'class' => 'edminboost-declutter-preview__item--profile',
	),
);
?>
<div
	id="edminboost-declutter-preview"
	class="edminboost-declutter-preview"
	style="<?php echo esc_attr( $edminboost_preview_style_vars ); ?>"
	role="region"
	aria-label="<?php esc_attr_e( 'Admin bar cleanup live preview', 'edminboost' ); ?>"
	aria-live="polite"
>
	<p class="edminboost-declutter-preview__lead"><?php esc_html_e( 'Live preview', 'edminboost' ); ?></p>
	<p class="edminboost-declutter-preview__desc"><?php esc_html_e( 'Shows native WordPress admin bar items affected by the toggles above.', 'edminboost' ); ?></p>

	<div class="edminboost-declutter-preview__canvas" aria-hidden="true">
		<?php foreach ( $edminboost_preview_items as $edminboost_preview_item ) : ?>
			<?php
			$edminboost_behavior_key = $edminboost_preview_item['key'];
			$edminboost_is_hidden    = ! empty( $edminboost_behavior[ $edminboost_behavior_key ] );
			$edminboost_item_class   = 'edminboost-declutter-preview__item';
			if ( ! empty( $edminboost_preview_item['class'] ) ) {
				$edminboost_item_class .= ' ' . $edminboost_preview_item['class'];
			}
			if ( $edminboost_is_hidden ) {
				$edminboost_item_class .= ' is-hidden';
			}

			$edminboost_tooltip_visible = sprintf(
				/* translators: %s: admin bar item label */
				__( '%s — Visible', 'edminboost' ),
				$edminboost_preview_item['label']
			);
			$edminboost_tooltip_hidden = sprintf(
				/* translators: %s: admin bar item label */
				__( '%s — Hidden', 'edminboost' ),
				$edminboost_preview_item['label']
			);
			$edminboost_tooltip_text = $edminboost_is_hidden ? $edminboost_tooltip_hidden : $edminboost_tooltip_visible;
			?>
			<span
				class="<?php echo esc_attr( $edminboost_item_class ); ?>"
				data-preview="<?php echo esc_attr( $edminboost_behavior_key ); ?>"
				data-tooltip-visible="<?php echo esc_attr( $edminboost_tooltip_visible ); ?>"
				data-tooltip-hidden="<?php echo esc_attr( $edminboost_tooltip_hidden ); ?>"
				tabindex="0"
				aria-label="<?php echo esc_attr( $edminboost_tooltip_text ); ?>"
			>
				<span class="dashicons <?php echo esc_attr( $edminboost_preview_item['icon'] ); ?>" aria-hidden="true"></span>
				<span class="edminboost-declutter-preview__label"><?php echo esc_html( $edminboost_preview_item['label'] ); ?></span>
				<?php if ( ! empty( $edminboost_preview_item['badge'] ) ) : ?>
					<span class="edminboost-declutter-preview__badge" aria-hidden="true"><?php echo esc_html( $edminboost_preview_item['badge'] ); ?></span>
				<?php endif; ?>
				<span class="edminboost-declutter-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_tooltip_text ); ?></span>
			</span>
		<?php endforeach; ?>
	</div>
</div>
