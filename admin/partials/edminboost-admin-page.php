<?php
/**
 * Home hub page template.
 *
 * Purpose: First-run setup, quick look changes, and status overview.
 *
 * @package EdminBoost
 *
 * @var array                     $settings Plugin settings.
 * @var array                     $cc_settings Command Center settings.
 * @var EDMINBOOST_Feature_Base[] $features Registered features.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name      = EDMINBOOST_Settings::OPTION_NAME;
$is_setup         = EDMINBOOST_Command_Center::is_setup_complete( $cc_settings );
$top_bar_items    = isset( $cc_settings['top_bar_items'] ) && is_array( $cc_settings['top_bar_items'] )
	? $cc_settings['top_bar_items']
	: array();
$item_count       = count( $top_bar_items );
$preset_label     = EDMINBOOST_Command_Center::get_active_preset_label( $cc_settings );
$look_skins       = EDMINBOOST_Command_Center::get_look_skins();
$active_skin      = isset( $cc_settings['look_skin'] ) ? $cc_settings['look_skin'] : EDMINBOOST_Command_Center::detect_look_skin( $cc_settings['behavior'] );
$behavior         = isset( $cc_settings['behavior'] ) && is_array( $cc_settings['behavior'] )
	? $cc_settings['behavior']
	: EDMINBOOST_Command_Center::get_defaults()['behavior'];
$cc_key           = $option_name . '[command_center][behavior]';
$theme            = EDMINBOOST_Theme::get_settings( $cc_settings );
$theme_key        = $option_name . '[command_center][theme]';
$presets_url      = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_PRESETS );
$mapper_url       = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER );
$settings_url     = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . '-settings' );

$active_features = 0;
foreach ( $features as $feature ) {
	if ( $feature->is_enabled() ) {
		++$active_features;
	}
}
?>
<div class="wrap edminboost-wrap edminboost-home-wrap">
	<header class="edminboost-home-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php if ( ! $is_setup ) : ?>
			<p class="edminboost-home-hero__lead">
				<?php esc_html_e( 'Set up your admin top bar in a few clicks.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
		<?php else : ?>
			<p class="edminboost-home-hero__lead">
				<?php esc_html_e( 'Manage your admin top bar layout and look.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>
		<?php endif; ?>
	</header>

	<div class="edminboost-dashboard">
		<?php if ( ! $is_setup ) : ?>
			<section class="edminboost-card edminboost-home-setup" aria-labelledby="edminboost-setup-heading">
				<h2 id="edminboost-setup-heading"><?php esc_html_e( 'Choose how to start', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
				<div class="edminboost-setup-grid">
					<article class="edminboost-setup-card">
						<span class="edminboost-setup-card__icon dashicons dashicons-layout" aria-hidden="true"></span>
						<h3><?php esc_html_e( 'Start from a preset', EDMINBOOST_TEXT_DOMAIN ); ?></h3>
						<p><?php esc_html_e( 'Pick a ready-made top bar layout for content, shops, or power users.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
						<a class="button button-primary" href="<?php echo esc_url( $presets_url ); ?>">
							<?php esc_html_e( 'Browse presets', EDMINBOOST_TEXT_DOMAIN ); ?>
						</a>
					</article>
					<article class="edminboost-setup-card">
						<span class="edminboost-setup-card__icon dashicons dashicons-art" aria-hidden="true"></span>
						<h3><?php esc_html_e( 'Quick look changes', EDMINBOOST_TEXT_DOMAIN ); ?></h3>
						<p><?php esc_html_e( 'Apply a look skin below to adjust panel style, badges, and admin bar cleanup.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
						<a class="button" href="#edminboost-look-skins">
							<?php esc_html_e( 'Pick a look', EDMINBOOST_TEXT_DOMAIN ); ?>
						</a>
					</article>
					<article class="edminboost-setup-card">
						<span class="edminboost-setup-card__icon dashicons dashicons-menu" aria-hidden="true"></span>
						<h3><?php esc_html_e( 'Build your own', EDMINBOOST_TEXT_DOMAIN ); ?></h3>
						<p><?php esc_html_e( 'Drag admin links into your top bar and configure each one manually.', EDMINBOOST_TEXT_DOMAIN ); ?></p>
						<a class="button" href="<?php echo esc_url( $mapper_url ); ?>">
							<?php esc_html_e( 'Open top bar editor', EDMINBOOST_TEXT_DOMAIN ); ?>
						</a>
					</article>
				</div>
			</section>
		<?php else : ?>
			<section class="edminboost-card edminboost-home-status" aria-labelledby="edminboost-status-heading">
				<h2 id="edminboost-status-heading"><?php esc_html_e( 'Current setup', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
				<ul class="edminboost-home-status-list">
					<li>
						<strong><?php esc_html_e( 'Plugin', EDMINBOOST_TEXT_DOMAIN ); ?></strong>
						<span class="edminboost-status <?php echo esc_attr( ! empty( $settings['enabled'] ) ? 'is-active' : 'is-inactive' ); ?>">
							<?php
							echo ! empty( $settings['enabled'] )
								? esc_html__( 'On', EDMINBOOST_TEXT_DOMAIN )
								: esc_html__( 'Off', EDMINBOOST_TEXT_DOMAIN );
							?>
						</span>
					</li>
					<li>
						<strong><?php esc_html_e( 'Top bar items', EDMINBOOST_TEXT_DOMAIN ); ?></strong>
						<?php echo esc_html( (string) $item_count ); ?>
					</li>
					<li>
						<strong><?php esc_html_e( 'Active preset', EDMINBOOST_TEXT_DOMAIN ); ?></strong>
						<?php echo esc_html( $preset_label ); ?>
					</li>
					<li>
						<strong><?php esc_html_e( 'Productivity features', EDMINBOOST_TEXT_DOMAIN ); ?></strong>
						<?php
						echo esc_html(
							sprintf(
								/* translators: 1: active feature count, 2: total feature count */
								__( '%1$d of %2$d on', EDMINBOOST_TEXT_DOMAIN ),
								$active_features,
								count( $features )
							)
						);
						?>
					</li>
				</ul>
				<p class="edminboost-home-quick-links">
					<a class="button" href="<?php echo esc_url( $mapper_url ); ?>"><?php esc_html_e( 'Edit top bar', EDMINBOOST_TEXT_DOMAIN ); ?></a>
					<a class="button" href="<?php echo esc_url( $presets_url ); ?>"><?php esc_html_e( 'Change preset', EDMINBOOST_TEXT_DOMAIN ); ?></a>
					<a class="button" href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Feature settings', EDMINBOOST_TEXT_DOMAIN ); ?></a>
				</p>
			</section>
		<?php endif; ?>

		<form action="options.php" method="post" class="edminboost-cc-form edminboost-home-form" id="edminboost-home-form">
			<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>

			<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-theme-settings.php'; ?>

			<section class="edminboost-card edminboost-home-skins" id="edminboost-look-skins" aria-labelledby="edminboost-skins-heading">
				<h2 id="edminboost-skins-heading"><?php esc_html_e( 'Quick look changes', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Skins adjust panel style, badges, and admin bar cleanup. They do not change which links appear in your top bar.', EDMINBOOST_TEXT_DOMAIN ); ?>
				</p>

				<div class="edminboost-skin-grid" role="radiogroup" aria-label="<?php esc_attr_e( 'Look skin', EDMINBOOST_TEXT_DOMAIN ); ?>">
					<?php foreach ( $look_skins as $skin_id => $skin ) : ?>
						<?php
						$is_selected = ( $active_skin === $skin_id );
						$input_id    = 'edminboost_skin_' . $skin_id;
						?>
						<label class="edminboost-skin-card<?php echo $is_selected ? ' is-selected' : ''; ?>" for="<?php echo esc_attr( $input_id ); ?>">
							<input
								type="radio"
								class="edminboost-skin-card__input screen-reader-text"
								name="<?php echo esc_attr( $option_name ); ?>[command_center][look_skin]"
								id="<?php echo esc_attr( $input_id ); ?>"
								value="<?php echo esc_attr( $skin_id ); ?>"
								<?php checked( $is_selected ); ?>
							/>
							<span class="edminboost-skin-card__icon dashicons <?php echo esc_attr( $skin['icon'] ); ?>" aria-hidden="true"></span>
							<span class="edminboost-skin-card__title"><?php echo esc_html( $skin['name'] ); ?></span>
							<span class="edminboost-skin-card__desc"><?php echo esc_html( $skin['description'] ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>

				<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[enabled]" value="1" />
				<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_mark_setup_complete]" value="1" />
				<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_apply_look_skin]" id="edminboost_apply_look_skin" value="" />
				<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][_keep_advanced_behavior]" id="edminboost_keep_advanced_behavior" value="" />

				<?php
				include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-home-advanced-look.php';
				?>

				<p class="submit">
					<?php submit_button( __( 'Save look', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
				</p>
			</section>
		</form>
	</div>
</div>
