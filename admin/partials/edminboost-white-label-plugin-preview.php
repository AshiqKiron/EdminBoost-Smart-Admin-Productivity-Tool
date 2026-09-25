<?php
/**
 * Live plugin rebranding preview for the White Label settings page.
 *
 * @package EdminBoost
 *
 * @var array $edminboost_wl       White label settings.
 * @var array $edminboost_defaults Plugin header defaults for empty fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_wl = isset( $edminboost_wl ) && is_array( $edminboost_wl )
	? $edminboost_wl
	: array();
$edminboost_defaults = isset( $edminboost_defaults ) && is_array( $edminboost_defaults )
	? $edminboost_defaults
	: EDMINBOOST_White_Label::get_plugin_header_defaults();

$edminboost_preview_name        = '' !== ( $edminboost_wl['plugin_name'] ?? '' ) ? $edminboost_wl['plugin_name'] : $edminboost_defaults['plugin_name'];
$edminboost_preview_description = '' !== ( $edminboost_wl['plugin_description'] ?? '' ) ? $edminboost_wl['plugin_description'] : $edminboost_defaults['plugin_description'];
$edminboost_preview_author      = '' !== ( $edminboost_wl['plugin_author'] ?? '' ) ? $edminboost_wl['plugin_author'] : $edminboost_defaults['plugin_author'];
$edminboost_preview_uri         = '' !== ( $edminboost_wl['plugin_uri'] ?? '' ) ? $edminboost_wl['plugin_uri'] : $edminboost_defaults['plugin_uri'];
$edminboost_preview_menu_label  = '' !== ( $edminboost_wl['menu_label'] ?? '' ) ? $edminboost_wl['menu_label'] : $edminboost_defaults['menu_label'];
?>
<div
	id="edminboost-wl-rebrand-preview"
	class="edminboost-wl-rebrand-preview"
	data-default-name="<?php echo esc_attr( $edminboost_defaults['plugin_name'] ); ?>"
	data-default-description="<?php echo esc_attr( $edminboost_defaults['plugin_description'] ); ?>"
	data-default-author="<?php echo esc_attr( $edminboost_defaults['plugin_author'] ); ?>"
	data-default-uri="<?php echo esc_attr( $edminboost_defaults['plugin_uri'] ); ?>"
	data-default-menu-label="<?php echo esc_attr( $edminboost_defaults['menu_label'] ); ?>"
	role="region"
	aria-label="<?php esc_attr_e( 'Plugin rebranding live preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
	aria-live="polite"
>
	<p class="edminboost-wl-rebrand-preview__lead"><?php esc_html_e( 'Live preview', EDMINBOOST_TEXT_DOMAIN ); ?></p>

	<div class="edminboost-wl-rebrand-preview__panel">
		<p class="edminboost-wl-rebrand-preview__heading"><?php esc_html_e( 'Plugins screen', EDMINBOOST_TEXT_DOMAIN ); ?></p>
		<div class="edminboost-wl-rebrand-preview__plugin-row" aria-hidden="true">
			<div class="edminboost-wl-rebrand-preview__plugin-title">
				<strong id="edminboost-wl-preview-name"><?php echo esc_html( $edminboost_preview_name ); ?></strong>
				<span class="edminboost-wl-rebrand-preview__plugin-version">
					<?php
					printf(
						/* translators: %s: plugin version number */
						esc_html__( 'Version %s', EDMINBOOST_TEXT_DOMAIN ),
						esc_html( EDMINBOOST_VERSION )
					);
					?>
				</span>
			</div>
			<div class="edminboost-wl-rebrand-preview__plugin-meta">
				<p id="edminboost-wl-preview-description"><?php echo esc_html( $edminboost_preview_description ); ?></p>
				<p class="edminboost-wl-rebrand-preview__plugin-author">
					<?php esc_html_e( 'By', EDMINBOOST_TEXT_DOMAIN ); ?>
					<a
						id="edminboost-wl-preview-author-link"
						href="<?php echo esc_url( $edminboost_preview_uri ? $edminboost_preview_uri : '#' ); ?>"
						<?php echo $edminboost_preview_uri ? '' : ' tabindex="-1"'; ?>
					><?php echo esc_html( $edminboost_preview_author ); ?></a>
				</p>
			</div>
		</div>
	</div>

	<div class="edminboost-wl-rebrand-preview__panel">
		<p class="edminboost-wl-rebrand-preview__heading"><?php esc_html_e( 'Admin menu', EDMINBOOST_TEXT_DOMAIN ); ?></p>
		<ul class="edminboost-wl-rebrand-preview__menu" aria-hidden="true">
			<li class="edminboost-wl-rebrand-preview__menu-item"><?php esc_html_e( 'Dashboard', EDMINBOOST_TEXT_DOMAIN ); ?></li>
			<li class="edminboost-wl-rebrand-preview__menu-item is-target" id="edminboost-wl-preview-menu-label"><?php echo esc_html( $edminboost_preview_menu_label ); ?></li>
			<li class="edminboost-wl-rebrand-preview__menu-item"><?php esc_html_e( 'Posts', EDMINBOOST_TEXT_DOMAIN ); ?></li>
		</ul>
	</div>
</div>
