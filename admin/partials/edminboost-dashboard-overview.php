<?php
/**
 * Post-setup Dashboard overview cards.
 *
 * @package EdminBoost
 *
 * @var string $edminboost_option_name    Settings option name (prefixed include variable).
 * @var array  $cc_settings               Command Center settings.
 * @var string $edminboost_appearance_url Appearance settings URL.
 * @var string $edminboost_mapper_url     Top Bar editor URL.
 * @var string $edminboost_presets_url    Layouts URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $edminboost_option_name ) ) {
	$edminboost_option_name = EDMINBOOST_Settings::OPTION_NAME;
}
if ( ! isset( $edminboost_mapper_url ) ) {
	$edminboost_mapper_url = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER );
}
if ( ! isset( $edminboost_presets_url ) ) {
	$edminboost_presets_url = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRESETS );
}
if ( ! isset( $edminboost_appearance_url ) ) {
	$edminboost_appearance_url = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_APPEARANCE );
}

$edminboost_theme         = EDMINBOOST_Theme::get_settings( $cc_settings );
$edminboost_active_preset = isset( $edminboost_theme['preset'] ) ? $edminboost_theme['preset'] : 'default';
$edminboost_layout_preset = EDMINBOOST_Command_Center::detect_active_layout_preset( $cc_settings );
$edminboost_all_presets   = EDMINBOOST_Command_Center::get_picker_presets( true );
$edminboost_layout_name   = isset( $edminboost_all_presets[ $edminboost_layout_preset ]['name'] )
	? $edminboost_all_presets[ $edminboost_layout_preset ]['name']
	: $edminboost_layout_preset;
$edminboost_top_bar_items = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
	? $cc_settings['top_bar_items']
	: array();
$edminboost_top_bar_count = count( $edminboost_top_bar_items );
$edminboost_redirect_count = 0;
$edminboost_drawer_count   = 0;

foreach ( $edminboost_top_bar_items as $edminboost_top_bar_item ) {
	$edminboost_interaction = isset( $edminboost_top_bar_item['interaction'] ) ? $edminboost_top_bar_item['interaction'] : 'redirect';
	if ( 'drawer' === $edminboost_interaction ) {
		++$edminboost_drawer_count;
	} else {
		++$edminboost_redirect_count;
	}
}

if ( 0 === $edminboost_top_bar_count ) {
	$edminboost_top_bar_desc = __( 'Admin link shortcuts can appear in your WordPress top bar. Add links in the Top Bar editor and choose whether each opens directly or in a slide-out drawer.', 'edminboost-smart-admin-productivity-tool' );
} else {
	$edminboost_top_bar_desc  = __( 'Admin link shortcuts appear in your WordPress top bar.', 'edminboost-smart-admin-productivity-tool' );
	$edminboost_opening_parts = array();

	if ( $edminboost_redirect_count > 0 ) {
		$edminboost_opening_parts[] = sprintf(
			/* translators: %d: number of links that open directly */
			_n( '%d opens directly', '%d open directly', $edminboost_redirect_count, 'edminboost-smart-admin-productivity-tool' ),
			$edminboost_redirect_count
		);
	}

	if ( $edminboost_drawer_count > 0 ) {
		$edminboost_opening_parts[] = sprintf(
			/* translators: %d: number of links that open in a slide-out drawer */
			_n( '%d opens in a slide-out drawer', '%d open in a slide-out drawer', $edminboost_drawer_count, 'edminboost-smart-admin-productivity-tool' ),
			$edminboost_drawer_count
		);
	}

	if ( ! empty( $edminboost_opening_parts ) ) {
		$edminboost_top_bar_desc .= ' ' . implode( '; ', $edminboost_opening_parts ) . '.';
	}
}

$edminboost_layout_sidebar_items = EDMINBOOST_Command_Center::resolve_preset_sidebar_preview_items( $edminboost_layout_preset, $cc_settings );
$edminboost_theme_preview_colors = EDMINBOOST_Theme::resolve_preview_colors(
	$edminboost_active_preset,
	isset( $edminboost_theme['mode'] ) ? $edminboost_theme['mode'] : 'light',
	$edminboost_theme
);

$edminboost_theme_key = $edminboost_option_name . '[command_center][theme]';
?>
<form
	action="options.php"
	method="post"
	class="edminboost-cc-form edminboost-dashboard-overview-form"
	id="edminboost-dashboard-overview-form"
>
	<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
	<input type="hidden" name="<?php echo esc_attr( $edminboost_option_name ); ?>[enabled]" value="1" />
	<input
		type="hidden"
		name="<?php echo esc_attr( $edminboost_option_name ); ?>[command_center][_apply_preset]"
		id="edminboost_dashboard_apply_preset"
		value=""
	/>
	<input
		type="hidden"
		name="<?php echo esc_attr( $edminboost_theme_key ); ?>[mode]"
		id="edminboost_theme_mode"
		value="<?php echo esc_attr( isset( $edminboost_theme['mode'] ) ? $edminboost_theme['mode'] : 'light' ); ?>"
	/>
	<input
		type="hidden"
		name="<?php echo esc_attr( $edminboost_theme_key ); ?>[font]"
		id="edminboost_theme_font"
		value="<?php echo esc_attr( isset( $edminboost_theme['font'] ) ? $edminboost_theme['font'] : 'inherit' ); ?>"
	/>
	<?php if ( EDMINBOOST_Theme::uses_custom_colors( $edminboost_theme ) ) : ?>
		<input type="hidden" name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_accent]" value="<?php echo esc_attr( isset( $edminboost_theme['custom_accent'] ) ? $edminboost_theme['custom_accent'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_surface]" value="<?php echo esc_attr( isset( $edminboost_theme['custom_surface'] ) ? $edminboost_theme['custom_surface'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_text]" value="<?php echo esc_attr( isset( $edminboost_theme['custom_text'] ) ? $edminboost_theme['custom_text'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_top]" value="<?php echo esc_attr( isset( $edminboost_theme['custom_top'] ) ? $edminboost_theme['custom_top'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_sidebar]" value="<?php echo esc_attr( isset( $edminboost_theme['custom_sidebar'] ) ? $edminboost_theme['custom_sidebar'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $edminboost_theme_key ); ?>[custom_content]" value="<?php echo esc_attr( isset( $edminboost_theme['custom_content'] ) ? $edminboost_theme['custom_content'] : '' ); ?>" />
	<?php endif; ?>

	<div class="edminboost-dashboard-overview">
		<div class="edminboost-overview-grid">
			<article class="edminboost-card edminboost-overview-card edminboost-overview-card--layout">
				<h2><?php esc_html_e( 'Layout preset', 'edminboost-smart-admin-productivity-tool' ); ?></h2>
				<?php
				$edminboost_preset_picker_mode = 'overview';
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-preset-picker.php';
				?>
				<div class="edminboost-layout-preset-previews">
					<?php
					$edminboost_sidebar_items      = $edminboost_layout_sidebar_items;
					$edminboost_preview_limit      = 5;
					$edminboost_preview_id         = 'edminboost-overview-layout-sidebar-preview';
					$edminboost_preview_aria_label = sprintf(
						/* translators: %s: layout preset name */
						__( 'Sidebar menu preview for the %s layout preset', 'edminboost-smart-admin-productivity-tool' ),
						$edminboost_layout_name
					);
					include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-sidebar-preview.php';
					?>
				</div>
				<a class="button button-secondary" href="<?php echo esc_url( $edminboost_presets_url ); ?>">
					<?php esc_html_e( 'Manage layout presets', 'edminboost-smart-admin-productivity-tool' ); ?>
				</a>
			</article>
			<article class="edminboost-card edminboost-overview-card edminboost-overview-card--theme">
				<h2><?php esc_html_e( 'Color theme', 'edminboost-smart-admin-productivity-tool' ); ?></h2>
				<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-theme-picker.php'; ?>
				<?php
				$edminboost_preview_colors = $edminboost_theme_preview_colors;
				$edminboost_preview_id     = 'edminboost-overview-theme-preview';
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-theme-preview.php';
				?>
				<a class="button button-secondary" href="<?php echo esc_url( $edminboost_appearance_url ); ?>">
					<?php esc_html_e( 'Customize appearance', 'edminboost-smart-admin-productivity-tool' ); ?>
				</a>
			</article>
			<article class="edminboost-card edminboost-overview-card edminboost-overview-card--topbar">
				<h2><?php esc_html_e( 'Top bar', 'edminboost-smart-admin-productivity-tool' ); ?></h2>
				<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-topbar-links-picker.php'; ?>
				<p class="edminboost-overview-card__desc" id="edminboost-overview-topbar-desc"><?php echo esc_html( $edminboost_top_bar_desc ); ?></p>
				<?php
				$edminboost_preview_items      = $edminboost_top_bar_items;
				$edminboost_preview_id         = 'edminboost-overview-topbar-preview';
				$edminboost_preview_aria_label = __( 'Preview of your configured top bar links', 'edminboost-smart-admin-productivity-tool' );
				$edminboost_show_interaction   = true;
				$edminboost_compact_preview    = true;
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-topbar-preview.php';
				?>
				<a class="button button-secondary" href="<?php echo esc_url( $edminboost_mapper_url ); ?>">
					<?php esc_html_e( 'Edit top bar', 'edminboost-smart-admin-productivity-tool' ); ?>
				</a>
			</article>
		</div>
	</div>
</form>
