<?php
/**
 * Post-setup Dashboard overview cards.
 *
 * @package EdminBoost
 *
 * @var string $option_name    Settings option name.
 * @var array  $cc_settings    Command Center settings.
 * @var array  $theme          Current theme settings.
 * @var string $theme_key      Form field prefix for theme.
 * @var string $appearance_url Appearance settings URL.
 * @var string $mapper_url     Top Bar editor URL.
 * @var string $presets_url    Layout Presets URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme           = EDMINBOOST_Theme::get_settings( $cc_settings );
$theme_presets   = EDMINBOOST_Theme::get_presets();
$active_preset   = isset( $theme['preset'] ) ? $theme['preset'] : 'default';
$theme_name      = isset( $theme_presets[ $active_preset ]['name'] ) ? $theme_presets[ $active_preset ]['name'] : $active_preset;
$theme_desc      = isset( $theme_presets[ $active_preset ]['description'] ) ? $theme_presets[ $active_preset ]['description'] : '';
$layout_preset        = EDMINBOOST_Command_Center::detect_active_layout_preset( $cc_settings );
$all_presets          = EDMINBOOST_Command_Center::get_picker_presets( true );
$layout_name          = isset( $all_presets[ $layout_preset ]['name'] ) ? $all_presets[ $layout_preset ]['name'] : $layout_preset;
$layout_desc          = isset( $all_presets[ $layout_preset ]['description'] ) ? $all_presets[ $layout_preset ]['description'] : '';
$top_bar_items   = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
	? $cc_settings['top_bar_items']
	: array();
$top_bar_count   = count( $top_bar_items );
$redirect_count  = 0;
$drawer_count    = 0;

foreach ( $top_bar_items as $top_bar_item ) {
	$interaction = isset( $top_bar_item['interaction'] ) ? $top_bar_item['interaction'] : 'redirect';
	if ( 'drawer' === $interaction ) {
		++$drawer_count;
	} else {
		++$redirect_count;
	}
}

if ( 0 === $top_bar_count ) {
	$top_bar_desc = __( 'Admin link shortcuts can appear in your WordPress top bar. Add links in the Top Bar editor and choose whether each opens directly or in a slide-out drawer.', EDMINBOOST_TEXT_DOMAIN );
} else {
	$top_bar_desc  = __( 'Admin link shortcuts appear in your WordPress top bar.', EDMINBOOST_TEXT_DOMAIN );
	$opening_parts = array();

	if ( $redirect_count > 0 ) {
		$opening_parts[] = sprintf(
			/* translators: %d: number of links that open directly */
			_n( '%d opens directly', '%d open directly', $redirect_count, EDMINBOOST_TEXT_DOMAIN ),
			$redirect_count
		);
	}

	if ( $drawer_count > 0 ) {
		$opening_parts[] = sprintf(
			/* translators: %d: number of links that open in a slide-out drawer */
			_n( '%d opens in a slide-out drawer', '%d open in a slide-out drawer', $drawer_count, EDMINBOOST_TEXT_DOMAIN ),
			$drawer_count
		);
	}

	if ( ! empty( $opening_parts ) ) {
		$top_bar_desc .= ' ' . implode( '; ', $opening_parts ) . '.';
	}
}

$layout_preview_items = EDMINBOOST_Command_Center::resolve_preset_top_bar_items( $layout_preset, $cc_settings );
$theme_preview_colors = isset( $theme_presets[ $active_preset ]['colors'] ) ? $theme_presets[ $active_preset ]['colors'] : $theme_presets['default']['colors'];

if ( EDMINBOOST_Theme::uses_custom_colors( $theme ) ) {
	$custom_color_map = array(
		'accent'  => 'custom_accent',
		'surface' => 'custom_surface',
		'text'    => 'custom_text',
		'topbar'  => 'custom_top',
		'sidebar' => 'custom_sidebar',
		'content' => 'custom_content',
	);

	foreach ( $custom_color_map as $color_key => $theme_color_key ) {
		if ( ! empty( $theme[ $theme_color_key ] ) ) {
			$theme_preview_colors[ $color_key ] = $theme[ $theme_color_key ];
		}
	}
}

$theme_key = $option_name . '[command_center][theme]';
?>
<form
	action="options.php"
	method="post"
	class="edminboost-cc-form edminboost-dashboard-overview-form"
	id="edminboost-dashboard-overview-form"
>
	<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>
	<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />
	<input
		type="hidden"
		name="<?php echo esc_attr( $option_name ); ?>[command_center][_apply_preset]"
		id="edminboost_dashboard_apply_preset"
		value=""
	/>
	<input
		type="hidden"
		name="<?php echo esc_attr( $theme_key ); ?>[mode]"
		value="<?php echo esc_attr( isset( $theme['mode'] ) ? $theme['mode'] : 'light' ); ?>"
	/>
	<input
		type="hidden"
		name="<?php echo esc_attr( $theme_key ); ?>[font]"
		value="<?php echo esc_attr( isset( $theme['font'] ) ? $theme['font'] : 'inherit' ); ?>"
	/>
	<?php if ( EDMINBOOST_Theme::uses_custom_colors( $theme ) ) : ?>
		<input type="hidden" name="<?php echo esc_attr( $theme_key ); ?>[custom_accent]" value="<?php echo esc_attr( isset( $theme['custom_accent'] ) ? $theme['custom_accent'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $theme_key ); ?>[custom_surface]" value="<?php echo esc_attr( isset( $theme['custom_surface'] ) ? $theme['custom_surface'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $theme_key ); ?>[custom_text]" value="<?php echo esc_attr( isset( $theme['custom_text'] ) ? $theme['custom_text'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $theme_key ); ?>[custom_top]" value="<?php echo esc_attr( isset( $theme['custom_top'] ) ? $theme['custom_top'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $theme_key ); ?>[custom_sidebar]" value="<?php echo esc_attr( isset( $theme['custom_sidebar'] ) ? $theme['custom_sidebar'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $theme_key ); ?>[custom_content]" value="<?php echo esc_attr( isset( $theme['custom_content'] ) ? $theme['custom_content'] : '' ); ?>" />
	<?php endif; ?>

	<div class="edminboost-dashboard-overview">
		<div class="edminboost-overview-grid">
			<article class="edminboost-card edminboost-overview-card edminboost-overview-card--layout">
				<h2><?php esc_html_e( 'Layout preset', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
				<?php
				$preset_picker_mode = 'overview';
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-preset-picker.php';
				?>
				<?php
				$preview_items      = $layout_preview_items;
				$preview_id         = 'edminboost-overview-layout-preview';
				$preview_aria_label = sprintf(
					/* translators: %s: layout preset name */
					__( 'Top bar preview for the %s layout preset', EDMINBOOST_TEXT_DOMAIN ),
					$layout_name
				);
				$show_interaction   = false;
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-topbar-preview.php';
				?>
				<a class="button button-secondary" href="<?php echo esc_url( $presets_url ); ?>">
					<?php esc_html_e( 'Manage layout presets', EDMINBOOST_TEXT_DOMAIN ); ?>
				</a>
			</article>
			<article class="edminboost-card edminboost-overview-card edminboost-overview-card--theme">
				<h2><?php esc_html_e( 'Color theme', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
				<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-theme-picker.php'; ?>
				<?php
				$preview_colors = $theme_preview_colors;
				$preview_id     = 'edminboost-overview-theme-preview';
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-theme-preview.php';
				?>
				<a class="button button-secondary" href="<?php echo esc_url( $appearance_url ); ?>">
					<?php esc_html_e( 'Customize appearance', EDMINBOOST_TEXT_DOMAIN ); ?>
				</a>
			</article>
			<article class="edminboost-card edminboost-overview-card edminboost-overview-card--topbar">
				<h2><?php esc_html_e( 'Top bar', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
				<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-topbar-links-picker.php'; ?>
				<p class="edminboost-overview-card__desc" id="edminboost-overview-topbar-desc"><?php echo esc_html( $top_bar_desc ); ?></p>
				<?php
				$preview_items      = $top_bar_items;
				$preview_id         = 'edminboost-overview-topbar-preview';
				$preview_aria_label = __( 'Preview of your configured top bar links', EDMINBOOST_TEXT_DOMAIN );
				$show_interaction   = true;
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-overview-topbar-preview.php';
				?>
				<a class="button button-secondary" href="<?php echo esc_url( $mapper_url ); ?>">
					<?php esc_html_e( 'Edit top bar', EDMINBOOST_TEXT_DOMAIN ); ?>
				</a>
			</article>
		</div>
	</div>
</form>
