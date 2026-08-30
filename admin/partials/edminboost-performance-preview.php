<?php
/**
 * Live previews for Performance tab settings.
 *
 * @package EdminBoost
 *
 * @var array  $features Feature settings.
 * @var string $preview  Preview key: emoji|assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preview = isset( $preview ) ? sanitize_key( $preview ) : '';

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
$preview_colors   = wp_parse_args( $preview_colors, $color_defaults );
$preview_style_vars = sprintf(
	'--eb-op-accent:%1$s;--eb-op-surface:%2$s;--eb-op-text:%3$s;--eb-op-top:%4$s;--eb-op-sidebar:%5$s;--eb-op-content:%6$s;',
	esc_attr( $preview_colors['accent'] ),
	esc_attr( $preview_colors['surface'] ),
	esc_attr( $preview_colors['text'] ),
	esc_attr( $preview_colors['topbar'] ),
	esc_attr( $preview_colors['sidebar'] ),
	esc_attr( $preview_colors['content'] )
);

$preview_copy = array(
	'emoji'  => array(
		'lead' => __( 'Live preview', EDMINBOOST_TEXT_DOMAIN ),
		'desc' => __( 'Shows emoji scripts and styles that load or are removed in each scope.', EDMINBOOST_TEXT_DOMAIN ),
	),
	'assets' => array(
		'lead' => __( 'Live preview', EDMINBOOST_TEXT_DOMAIN ),
		'desc' => __( 'Shows front-end and script assets affected by the toggles above.', EDMINBOOST_TEXT_DOMAIN ),
	),
);

if ( 'emoji' === $preview ) :
	$emoji_enabled = ! empty( $features['disable_emojis']['enabled'] );
	$emoji_scope   = isset( $features['disable_emojis']['scope'] ) ? sanitize_key( $features['disable_emojis']['scope'] ) : 'admin';
	if ( ! in_array( $emoji_scope, array( 'admin', 'frontend', 'both' ), true ) ) {
		$emoji_scope = 'admin';
	}

	$emoji_areas = array(
		'admin'    => __( 'Admin', EDMINBOOST_TEXT_DOMAIN ),
		'frontend' => __( 'Front end', EDMINBOOST_TEXT_DOMAIN ),
	);

	$emoji_assets = array(
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
		style="<?php echo esc_attr( $preview_style_vars ); ?>"
		role="region"
		aria-label="<?php esc_attr_e( 'Emoji scripts live preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
		aria-live="polite"
	>
		<p class="edminboost-performance-preview__lead"><?php echo esc_html( $preview_copy['emoji']['lead'] ); ?></p>
		<p class="edminboost-performance-preview__desc"><?php echo esc_html( $preview_copy['emoji']['desc'] ); ?></p>
		<div class="edminboost-performance-preview__panels">
			<?php foreach ( $emoji_areas as $area_key => $area_label ) : ?>
				<?php
				$area_removed = $emoji_enabled && ( 'both' === $emoji_scope || $emoji_scope === $area_key );
				$panel_class  = 'edminboost-performance-preview__panel';
				if ( $area_removed ) {
					$panel_class .= ' is-active';
				}
				$panel_tooltip = sprintf(
					/* translators: %s: admin area label such as Admin or Front end */
					__( '%s emoji assets preview', EDMINBOOST_TEXT_DOMAIN ),
					$area_label
				);
				?>
				<div
					class="<?php echo esc_attr( $panel_class ); ?>"
					data-scope="<?php echo esc_attr( $area_key ); ?>"
					tabindex="0"
					aria-label="<?php echo esc_attr( $panel_tooltip ); ?>"
				>
					<p class="edminboost-performance-preview__heading"><?php echo esc_html( $area_label ); ?></p>
					<span class="edminboost-performance-preview__tooltip" role="tooltip"><?php echo esc_html( $panel_tooltip ); ?></span>
					<ul class="edminboost-performance-preview__list">
						<?php foreach ( $emoji_assets as $asset ) : ?>
							<?php
							$item_class = 'edminboost-performance-preview__item';
							if ( $area_removed ) {
								$item_class .= ' is-removed';
							}

							$tooltip_loaded = sprintf(
								/* translators: 1: asset type label, 2: asset file name */
								__( '%1$s: %2$s — Loaded', EDMINBOOST_TEXT_DOMAIN ),
								$asset['type'],
								$asset['code']
							);
							$tooltip_removed = sprintf(
								/* translators: 1: asset type label, 2: asset file name */
								__( '%1$s: %2$s — Removed', EDMINBOOST_TEXT_DOMAIN ),
								$asset['type'],
								$asset['code']
							);
							$tooltip_text = $area_removed ? $tooltip_removed : $tooltip_loaded;
							?>
							<li
								class="<?php echo esc_attr( $item_class ); ?>"
								data-asset="<?php echo esc_attr( $asset['key'] ); ?>"
								data-tooltip-loaded="<?php echo esc_attr( $tooltip_loaded ); ?>"
								data-tooltip-removed="<?php echo esc_attr( $tooltip_removed ); ?>"
								tabindex="0"
								aria-label="<?php echo esc_attr( $tooltip_text ); ?>"
							>
								<span class="edminboost-performance-preview__item-label"><?php echo esc_html( $asset['type'] ); ?></span>
								<code class="edminboost-performance-preview__code"><?php echo esc_html( $asset['code'] ); ?></code>
								<span class="edminboost-performance-preview__status-dot" aria-hidden="true"></span>
								<span class="edminboost-performance-preview__tooltip" role="tooltip"><?php echo esc_html( $tooltip_text ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
elseif ( 'assets' === $preview ) :
	$asset_previews = array(
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
		style="<?php echo esc_attr( $preview_style_vars ); ?>"
		role="region"
		aria-label="<?php esc_attr_e( 'Assets live preview', EDMINBOOST_TEXT_DOMAIN ); ?>"
		aria-live="polite"
	>
		<p class="edminboost-performance-preview__lead"><?php echo esc_html( $preview_copy['assets']['lead'] ); ?></p>
		<p class="edminboost-performance-preview__desc"><?php echo esc_html( $preview_copy['assets']['desc'] ); ?></p>
		<ul class="edminboost-performance-preview__list edminboost-performance-preview__list--stacked">
			<?php foreach ( $asset_previews as $feature_key => $asset_preview ) : ?>
				<?php
				$is_removed = ! empty( $features[ $feature_key ] );
				$item_class = 'edminboost-performance-preview__item';
				if ( $is_removed ) {
					$item_class .= ' is-removed';
				}

				$asset_code = $asset_preview['code'];
				if ( ! empty( $asset_preview['suffix'] ) ) {
					$asset_code .= $asset_preview['suffix'];
				}

				$tooltip_loaded = sprintf(
					/* translators: 1: asset label, 2: asset identifier */
					__( '%1$s: %2$s — Loaded', EDMINBOOST_TEXT_DOMAIN ),
					$asset_preview['label'],
					$asset_code
				);
				$tooltip_removed = sprintf(
					/* translators: 1: asset label, 2: asset identifier */
					__( '%1$s: %2$s — Removed', EDMINBOOST_TEXT_DOMAIN ),
					$asset_preview['label'],
					$asset_code
				);
				$tooltip_text = $is_removed ? $tooltip_removed : $tooltip_loaded;
				?>
				<li
					class="<?php echo esc_attr( $item_class ); ?>"
					data-preview="<?php echo esc_attr( $feature_key ); ?>"
					data-tooltip-loaded="<?php echo esc_attr( $tooltip_loaded ); ?>"
					data-tooltip-removed="<?php echo esc_attr( $tooltip_removed ); ?>"
					tabindex="0"
					aria-label="<?php echo esc_attr( $tooltip_text ); ?>"
				>
					<span class="edminboost-performance-preview__item-label"><?php echo esc_html( $asset_preview['label'] ); ?></span>
					<span class="edminboost-performance-preview__code-wrap">
						<code class="edminboost-performance-preview__code"><?php echo esc_html( $asset_preview['code'] ); ?></code>
						<?php if ( ! empty( $asset_preview['suffix'] ) ) : ?>
							<code class="edminboost-performance-preview__code edminboost-performance-preview__code--suffix"><?php echo esc_html( $asset_preview['suffix'] ); ?></code>
						<?php endif; ?>
					</span>
					<span class="edminboost-performance-preview__status-dot" aria-hidden="true"></span>
					<span class="edminboost-performance-preview__tooltip" role="tooltip"><?php echo esc_html( $tooltip_text ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
endif;
