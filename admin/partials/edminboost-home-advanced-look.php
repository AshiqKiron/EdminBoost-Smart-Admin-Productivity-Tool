<?php
/**
 * Advanced look settings (slide-out panel, badges).
 *
 * @package EdminBoost
 *
 * @var string $option_name Settings option name.
 * @var array  $behavior    Current behavior settings.
 * @var string $cc_key      Form field prefix for behavior.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$drawer_width_custom = isset( $behavior['drawer_width_custom'] )
	? (int) $behavior['drawer_width_custom']
	: EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_DEFAULT;
$drawer_width_custom = max(
	EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MIN,
	min( EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MAX, $drawer_width_custom )
);
?>
<div class="edminboost-advanced-look">
		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-drawer-settings-heading">
			<h2 id="edminboost-drawer-settings-heading"><?php esc_html_e( 'Slide-out panel', EDMINBOOST_TEXT_DOMAIN ); ?></h2>

			<fieldset class="edminboost-fieldset">
				<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'drawer_width' ); ?><?php esc_html_e( 'Panel width', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[drawer_width]" value="compact" <?php checked( $behavior['drawer_width'], 'compact' ); ?> />
					<?php esc_html_e( 'Compact (400px)', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[drawer_width]" value="standard" <?php checked( $behavior['drawer_width'], 'standard' ); ?> />
					<?php esc_html_e( 'Standard (600px)', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[drawer_width]" value="fullscreen" <?php checked( $behavior['drawer_width'], 'fullscreen' ); ?> />
					<?php esc_html_e( 'Full screen', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[drawer_width]" value="custom" <?php checked( $behavior['drawer_width'], 'custom' ); ?> />
					<?php esc_html_e( 'Custom', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<div
					class="edminboost-drawer-width-custom"
					id="edminboost-drawer-width-custom"
					<?php echo 'custom' === $behavior['drawer_width'] ? '' : 'hidden'; ?>
				>
					<label for="edminboost_drawer_width_custom">
						<?php EDMINBOOST_Setting_Help::echo_icon( 'drawer_width_custom' ); ?>
						<?php esc_html_e( 'Custom width', EDMINBOOST_TEXT_DOMAIN ); ?>
						<span class="edminboost-drawer-width-custom__value" id="edminboost_drawer_width_custom_value">
							<?php echo esc_html( (string) $drawer_width_custom ); ?>px
						</span>
					</label>
					<input
						type="range"
						id="edminboost_drawer_width_custom"
						name="<?php echo esc_attr( $cc_key ); ?>[drawer_width_custom]"
						min="<?php echo esc_attr( (string) EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MIN ); ?>"
						max="<?php echo esc_attr( (string) EDMINBOOST_Command_Center::DRAWER_CUSTOM_WIDTH_MAX ); ?>"
						step="10"
						value="<?php echo esc_attr( (string) $drawer_width_custom ); ?>"
						aria-describedby="edminboost-drawer-width-preview-caption"
					/>
				</div>
				<div class="edminboost-drawer-width-preview" id="edminboost-drawer-width-preview">
					<p class="edminboost-drawer-width-preview__heading">
						<?php esc_html_e( 'Preview', EDMINBOOST_TEXT_DOMAIN ); ?>
						<span class="screen-reader-text"><?php esc_html_e( 'Drawer width preview on a typical desktop screen.', EDMINBOOST_TEXT_DOMAIN ); ?></span>
					</p>
					<div
						class="edminboost-drawer-width-preview__viewport"
						role="img"
						aria-labelledby="edminboost-drawer-width-preview-caption"
					>
						<div class="edminboost-drawer-width-preview__adminbar" aria-hidden="true"></div>
						<div class="edminboost-drawer-width-preview__stage">
							<div class="edminboost-drawer-width-preview__content" aria-hidden="true">
								<span class="edminboost-drawer-width-preview__content-label"><?php esc_html_e( 'Page', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							</div>
							<div
								class="edminboost-drawer-width-preview__drawer"
								id="edminboost_drawer_width_preview_drawer"
								aria-hidden="true"
							>
								<span class="edminboost-drawer-width-preview__drawer-label"><?php esc_html_e( 'Panel', EDMINBOOST_TEXT_DOMAIN ); ?></span>
							</div>
						</div>
					</div>
					<p class="description edminboost-drawer-width-preview__caption" id="edminboost-drawer-width-preview-caption"></p>
				</div>
			</fieldset>

			<?php require EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-animation-speed-field.php'; ?>

			<label class="edminboost-checkbox-row" for="edminboost_glassmorphism">
				<input
					type="checkbox"
					id="edminboost_glassmorphism"
					name="<?php echo esc_attr( $cc_key ); ?>[glassmorphism]"
					value="1"
					<?php checked( ! empty( $behavior['glassmorphism'] ) ); ?>
				/>
				<?php EDMINBOOST_Setting_Help::echo_icon( 'glassmorphism' ); ?>
				<?php esc_html_e( 'Enable backdrop blur', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>

			<p>
				<label for="edminboost_autosave_interval"><?php EDMINBOOST_Setting_Help::echo_icon( 'autosave_interval' ); ?><?php esc_html_e( 'Auto-save interval for panel forms (seconds)', EDMINBOOST_TEXT_DOMAIN ); ?></label>
				<input
					type="number"
					class="small-text"
					id="edminboost_autosave_interval"
					name="<?php echo esc_attr( $cc_key ); ?>[autosave_interval]"
					value="<?php echo esc_attr( (string) (int) $behavior['autosave_interval'] ); ?>"
					min="10"
					max="600"
					step="10"
				/>
			</p>
		</section>

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-badge-settings-heading">
			<h2 id="edminboost-badge-settings-heading"><?php esc_html_e( 'Notification badges', EDMINBOOST_TEXT_DOMAIN ); ?></h2>

			<p>
				<label for="edminboost_badge_refresh"><?php EDMINBOOST_Setting_Help::echo_icon( 'badge_refresh_rate' ); ?><?php esc_html_e( 'Refresh rate (seconds)', EDMINBOOST_TEXT_DOMAIN ); ?></label>
				<input
					type="number"
					class="small-text"
					id="edminboost_badge_refresh"
					name="<?php echo esc_attr( $cc_key ); ?>[badge_refresh_rate]"
					value="<?php echo esc_attr( (string) (int) $behavior['badge_refresh_rate'] ); ?>"
					min="15"
					max="600"
					step="15"
				/>
			</p>

			<fieldset class="edminboost-fieldset">
				<legend><?php EDMINBOOST_Setting_Help::echo_icon( 'badge_style' ); ?><?php esc_html_e( 'Badge style', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[badge_style]" value="dot" <?php checked( $behavior['badge_style'], 'dot' ); ?> />
					<?php esc_html_e( 'Dot', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[badge_style]" value="pill" <?php checked( $behavior['badge_style'], 'pill' ); ?> />
					<?php esc_html_e( 'Counter pill', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[badge_style]" value="accent" <?php checked( $behavior['badge_style'], 'accent' ); ?> />
					<?php esc_html_e( 'Accent color', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
			</fieldset>

			<div class="edminboost-badge-preview" aria-hidden="true">
				<span class="edminboost-badge-preview__label"><?php esc_html_e( 'Preview', EDMINBOOST_TEXT_DOMAIN ); ?></span>
				<span class="edminboost-badge-preview__item edminboost-badge-preview__item--dot" data-style="dot">
					<span class="dashicons dashicons-cart"></span>
					<span class="edminboost-topbar-item__badge edminboost-topbar-item__badge--dot"></span>
				</span>
				<span class="edminboost-badge-preview__item edminboost-badge-preview__item--pill" data-style="pill">
					<span class="dashicons dashicons-email"></span>
					<span class="edminboost-topbar-item__badge">12</span>
				</span>
				<span class="edminboost-badge-preview__item edminboost-badge-preview__item--accent" data-style="accent">
					<span class="dashicons dashicons-admin-comments"></span>
					<span class="edminboost-topbar-item__badge edminboost-topbar-item__badge--accent">5</span>
				</span>
			</div>
		</section>
</div>
