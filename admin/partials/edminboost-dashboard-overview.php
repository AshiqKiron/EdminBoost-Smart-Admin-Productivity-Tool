<?php
/**
 * Post-setup Dashboard overview cards.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings Command Center settings.
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
$layout_preset   = isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client';
$all_presets     = EDMINBOOST_Command_Center::get_all_presets();
$layout_name     = isset( $all_presets[ $layout_preset ]['name'] ) ? $all_presets[ $layout_preset ]['name'] : $layout_preset;
$layout_desc     = isset( $all_presets[ $layout_preset ]['description'] ) ? $all_presets[ $layout_preset ]['description'] : '';
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
?>
<div class="edminboost-dashboard-overview">
	<div class="edminboost-overview-grid">
		<article class="edminboost-card edminboost-overview-card">
			<h2><?php esc_html_e( 'Layout preset', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="edminboost-overview-card__value"><?php echo esc_html( $layout_name ); ?></p>
			<?php if ( '' !== $layout_desc ) : ?>
				<p class="edminboost-overview-card__desc"><?php echo esc_html( $layout_desc ); ?></p>
			<?php endif; ?>
			<a class="button button-secondary" href="<?php echo esc_url( $presets_url ); ?>">
				<?php esc_html_e( 'Manage layout presets', EDMINBOOST_TEXT_DOMAIN ); ?>
			</a>
		</article>
		<article class="edminboost-card edminboost-overview-card">
			<h2><?php esc_html_e( 'Color theme', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="edminboost-overview-card__value"><?php echo esc_html( $theme_name ); ?></p>
			<?php if ( '' !== $theme_desc ) : ?>
				<p class="edminboost-overview-card__desc"><?php echo esc_html( $theme_desc ); ?></p>
			<?php endif; ?>
			<a class="button button-secondary" href="<?php echo esc_url( $appearance_url ); ?>">
				<?php esc_html_e( 'Customize appearance', EDMINBOOST_TEXT_DOMAIN ); ?>
			</a>
		</article>
		<article class="edminboost-card edminboost-overview-card">
			<h2><?php esc_html_e( 'Top bar', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="edminboost-overview-card__value">
				<?php
				printf(
					/* translators: %d: number of top bar links */
					esc_html( _n( '%d link configured', '%d links configured', $top_bar_count, EDMINBOOST_TEXT_DOMAIN ) ),
					(int) $top_bar_count
				);
				?>
			</p>
			<p class="edminboost-overview-card__desc"><?php echo esc_html( $top_bar_desc ); ?></p>
			<a class="button button-secondary" href="<?php echo esc_url( $mapper_url ); ?>">
				<?php esc_html_e( 'Edit top bar', EDMINBOOST_TEXT_DOMAIN ); ?>
			</a>
		</article>
	</div>
</div>
