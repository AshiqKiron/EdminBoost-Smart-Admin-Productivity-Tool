<?php
/**
 * Live previews for Performance tab settings.
 *
 * @package EdminBoost
 *
 * @var array  $edminboost_features Feature settings.
 * @var string $edminboost_preview  Preview key: emoji|assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edminboost_preview = isset( $edminboost_preview ) ? sanitize_key( $edminboost_preview ) : '';

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

$edminboost_preview_copy = array(
	'emoji'  => array(
		'lead' => __( 'Live preview', EDMINBOOST_TEXT_DOMAIN ),
		'desc' => __( 'Shows emoji scripts and styles that load or are removed in each scope.', EDMINBOOST_TEXT_DOMAIN ),
	),
	'assets' => array(
		'lead' => __( 'Live preview', EDMINBOOST_TEXT_DOMAIN ),
		'desc' => __( 'Shows front-end and script assets affected by the toggles above.', EDMINBOOST_TEXT_DOMAIN ),
	),
);

if ( 'emoji' === $edminboost_preview ) :
	$edminboost_emoji_enabled = ! empty( $edminboost_features['disable_emojis']['enabled'] );
	$edminboost_emoji_scope   = isset( $edminboost_features['disable_emojis']['scope'] ) ? sanitize_key( $edminboost_features['disable_emojis']['scope'] ) : 'admin';
	if ( ! in_array( $edminboost_emoji_scope, array( 'admin', 'frontend', 'both' ), true ) ) {
		$edminboost_emoji_scope = 'admin';
	}

	$edminboost_emoji_areas = array(
		'admin'    => __( 'Admin', EDMINBOOST_TEXT_DOMAIN ),
		'frontend' => __( 'Front end', EDMINBOOST_TEXT_DOMAIN ),
	);

	$edminboost_emoji_assets = array(
		array(
			'key'  => 'emoji-script',
			'type' => __( 'Script', EDMINBOOST_TEXT_DOMAIN ),
			'code' => 'wp-emoji-release.min.js',
		),
		array(
			'key'  => 'emoji-style',
			'type' => __( 'Style', EDMINBOOST_TEXT_DOMAIN ),
			'code' => 'wp-emoji-styles-inline-css',
		),
	);
	?>
	<div
		id="edminboost-performance-emoji-preview"
		class="edminboost-performance-preview"
		style="<?php echo esc_attr( $edminboost_preview_style_vars ); ?>"
		role="region"
		aria-label="<?php esc_attr_e( 'Emoji scripts live preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
		aria-live="polite"
	>
		<p class="edminboost-performance-preview__lead"><?php echo esc_html( $edminboost_preview_copy['emoji']['lead'] ); ?></p>
		<p class="edminboost-performance-preview__desc"><?php echo esc_html( $edminboost_preview_copy['emoji']['desc'] ); ?></p>
		<div class="edminboost-performance-preview__panels">
			<?php foreach ( $edminboost_emoji_areas as $edminboost_area_key => $edminboost_area_label ) : ?>
				<?php
				$edminboost_area_removed = $edminboost_emoji_enabled && ( 'both' === $edminboost_emoji_scope || $edminboost_emoji_scope === $edminboost_area_key );
				$edminboost_panel_class  = 'edminboost-performance-preview__panel';
				if ( $edminboost_area_removed ) {
					$edminboost_panel_class .= ' is-active';
				}
				$edminboost_panel_tooltip = sprintf(
					/* translators: %s: admin area label such as Admin or Front end */
					__( '%s emoji assets preview', EDMINBOOST_TEXT_DOMAIN ),
					$edminboost_area_label
				);
				?>
				<div
					class="<?php echo esc_attr( $edminboost_panel_class ); ?>"
					data-scope="<?php echo esc_attr( $edminboost_area_key ); ?>"
					tabindex="0"
					aria-label="<?php echo esc_attr( $edminboost_panel_tooltip ); ?>"
				>
					<p class="edminboost-performance-preview__heading"><?php echo esc_html( $edminboost_area_label ); ?></p>
					<span class="edminboost-performance-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_panel_tooltip ); ?></span>
					<ul class="edminboost-performance-preview__list">
						<?php foreach ( $edminboost_emoji_assets as $edminboost_asset ) : ?>
							<?php
							$edminboost_item_class = 'edminboost-performance-preview__item';
							if ( $edminboost_area_removed ) {
								$edminboost_item_class .= ' is-removed';
							}

							$edminboost_tooltip_loaded = sprintf(
								/* translators: 1: asset type label, 2: asset file name */
								__( '%1$s: %2$s — Loaded', EDMINBOOST_TEXT_DOMAIN ),
								$edminboost_asset['type'],
								$edminboost_asset['code']
							);
							$edminboost_tooltip_removed = sprintf(
								/* translators: 1: asset type label, 2: asset file name */
								__( '%1$s: %2$s — Removed', EDMINBOOST_TEXT_DOMAIN ),
								$edminboost_asset['type'],
								$edminboost_asset['code']
							);
							$edminboost_tooltip_text = $edminboost_area_removed ? $edminboost_tooltip_removed : $edminboost_tooltip_loaded;
							?>
							<li
								class="<?php echo esc_attr( $edminboost_item_class ); ?>"
								data-asset="<?php echo esc_attr( $edminboost_asset['key'] ); ?>"
								data-tooltip-loaded="<?php echo esc_attr( $edminboost_tooltip_loaded ); ?>"
								data-tooltip-removed="<?php echo esc_attr( $edminboost_tooltip_removed ); ?>"
								tabindex="0"
								aria-label="<?php echo esc_attr( $edminboost_tooltip_text ); ?>"
							>
								<span class="edminboost-performance-preview__item-label"><?php echo esc_html( $edminboost_asset['type'] ); ?></span>
								<code class="edminboost-performance-preview__code"><?php echo esc_html( $edminboost_asset['code'] ); ?></code>
								<span class="edminboost-performance-preview__status-dot" aria-hidden="true"></span>
								<span class="edminboost-performance-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_tooltip_text ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
elseif ( 'assets' === $edminboost_preview ) :
	$edminboost_asset_previews = array(
		'remove_asset_versions'     => array(
			'label'  => __( 'Script URL', EDMINBOOST_TEXT_DOMAIN ),
			'code'   => '/wp-includes/js/jquery/jquery.min.js',
			'suffix' => '?ver=3.7.1',
		),
		'remove_dashicons_frontend' => array(
			'label' => __( 'Stylesheet (visitors)', EDMINBOOST_TEXT_DOMAIN ),
			'code'  => 'dashicons.min.css',
		),
		'disable_embeds'            => array(
			'label' => __( 'Embed assets', EDMINBOOST_TEXT_DOMAIN ),
			'code'  => 'wp-embed.min.js + oEmbed discovery',
		),
	);
	?>
	<div
		id="edminboost-performance-assets-preview"
		class="edminboost-performance-preview"
		style="<?php echo esc_attr( $edminboost_preview_style_vars ); ?>"
		role="region"
		aria-label="<?php esc_attr_e( 'Assets live preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
		aria-live="polite"
	>
		<p class="edminboost-performance-preview__lead"><?php echo esc_html( $edminboost_preview_copy['assets']['lead'] ); ?></p>
		<p class="edminboost-performance-preview__desc"><?php echo esc_html( $edminboost_preview_copy['assets']['desc'] ); ?></p>
		<ul class="edminboost-performance-preview__list edminboost-performance-preview__list--stacked">
			<?php foreach ( $edminboost_asset_previews as $edminboost_feature_key => $edminboost_asset_preview ) : ?>
				<?php
				$edminboost_is_removed = ! empty( $edminboost_features[ $edminboost_feature_key ] );
				$edminboost_item_class = 'edminboost-performance-preview__item';
				if ( $edminboost_is_removed ) {
					$edminboost_item_class .= ' is-removed';
				}

				$edminboost_asset_code = $edminboost_asset_preview['code'];
				if ( ! empty( $edminboost_asset_preview['suffix'] ) ) {
					$edminboost_asset_code .= $edminboost_asset_preview['suffix'];
				}

				$edminboost_tooltip_loaded = sprintf(
					/* translators: 1: asset label, 2: asset identifier */
					__( '%1$s: %2$s — Loaded', EDMINBOOST_TEXT_DOMAIN ),
					$edminboost_asset_preview['label'],
					$edminboost_asset_code
				);
				$edminboost_tooltip_removed = sprintf(
					/* translators: 1: asset label, 2: asset identifier */
					__( '%1$s: %2$s — Removed', EDMINBOOST_TEXT_DOMAIN ),
					$edminboost_asset_preview['label'],
					$edminboost_asset_code
				);
				$edminboost_tooltip_text = $edminboost_is_removed ? $edminboost_tooltip_removed : $edminboost_tooltip_loaded;
				?>
				<li
					class="<?php echo esc_attr( $edminboost_item_class ); ?>"
					data-preview="<?php echo esc_attr( $edminboost_feature_key ); ?>"
					data-tooltip-loaded="<?php echo esc_attr( $edminboost_tooltip_loaded ); ?>"
					data-tooltip-removed="<?php echo esc_attr( $edminboost_tooltip_removed ); ?>"
					tabindex="0"
					aria-label="<?php echo esc_attr( $edminboost_tooltip_text ); ?>"
				>
					<span class="edminboost-performance-preview__item-label"><?php echo esc_html( $edminboost_asset_preview['label'] ); ?></span>
					<span class="edminboost-performance-preview__code-wrap">
						<code class="edminboost-performance-preview__code"><?php echo esc_html( $edminboost_asset_preview['code'] ); ?></code>
						<?php if ( ! empty( $edminboost_asset_preview['suffix'] ) ) : ?>
							<code class="edminboost-performance-preview__code edminboost-performance-preview__code--suffix"><?php echo esc_html( $edminboost_asset_preview['suffix'] ); ?></code>
						<?php endif; ?>
					</span>
					<span class="edminboost-performance-preview__status-dot" aria-hidden="true"></span>
					<span class="edminboost-performance-preview__tooltip" role="tooltip"><?php echo esc_html( $edminboost_tooltip_text ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
endif;
