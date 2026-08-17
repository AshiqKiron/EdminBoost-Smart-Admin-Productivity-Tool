<?php
/**
 * Behavior, Badges & Visual Styling.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name = EDMINBOOST_Settings::OPTION_NAME;
$behavior    = isset( $cc_settings['behavior'] ) && is_array( $cc_settings['behavior'] )
	? $cc_settings['behavior']
	: EDMINBOOST_Command_Center::get_defaults()['behavior'];
$cc_key      = $option_name . '[command_center][behavior]';
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Fine-tune micro-interactions, badge polling, and visual styling without dashboard bloat.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-drawer-settings-heading">
			<h2 id="edminboost-drawer-settings-heading"><?php esc_html_e( 'AJAX Slide-Out Drawer Settings', EDMINBOOST_TEXT_DOMAIN ); ?></h2>

			<fieldset class="edminboost-fieldset">
				<legend><?php esc_html_e( 'Drawer width', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
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
					<?php esc_html_e( 'Full screen modal', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
			</fieldset>

			<fieldset class="edminboost-fieldset">
				<legend for="edminboost_animation_speed"><?php esc_html_e( 'Animation speed', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
				<select name="<?php echo esc_attr( $cc_key ); ?>[animation_speed]" id="edminboost_animation_speed">
					<option value="fast" <?php selected( $behavior['animation_speed'], 'fast' ); ?>><?php esc_html_e( 'Fast (150ms)', EDMINBOOST_TEXT_DOMAIN ); ?></option>
					<option value="normal" <?php selected( $behavior['animation_speed'], 'normal' ); ?>><?php esc_html_e( 'Normal (300ms)', EDMINBOOST_TEXT_DOMAIN ); ?></option>
					<option value="slow" <?php selected( $behavior['animation_speed'], 'slow' ); ?>><?php esc_html_e( 'Slow (500ms)', EDMINBOOST_TEXT_DOMAIN ); ?></option>
				</select>
			</fieldset>

			<label class="edminboost-checkbox-row" for="edminboost_glassmorphism">
				<input
					type="checkbox"
					id="edminboost_glassmorphism"
					name="<?php echo esc_attr( $cc_key ); ?>[glassmorphism]"
					value="1"
					<?php checked( ! empty( $behavior['glassmorphism'] ) ); ?>
				/>
				<?php esc_html_e( 'Enable backdrop blur (glassmorphism effect)', EDMINBOOST_TEXT_DOMAIN ); ?>
			</label>

			<p>
				<label for="edminboost_autosave_interval"><?php esc_html_e( 'Auto-save interval for drawer forms (seconds)', EDMINBOOST_TEXT_DOMAIN ); ?></label>
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
			<h2 id="edminboost-badge-settings-heading"><?php esc_html_e( 'Live Status Badge Configurations', EDMINBOOST_TEXT_DOMAIN ); ?></h2>

			<p>
				<label for="edminboost_badge_refresh"><?php esc_html_e( 'Refresh rate / polling interval (seconds)', EDMINBOOST_TEXT_DOMAIN ); ?></label>
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
				<span class="description"><?php esc_html_e( 'e.g. check every 60 seconds for unread counts.', EDMINBOOST_TEXT_DOMAIN ); ?></span>
			</p>

			<fieldset class="edminboost-fieldset">
				<legend><?php esc_html_e( 'Notification badge styling', EDMINBOOST_TEXT_DOMAIN ); ?></legend>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[badge_style]" value="dot" <?php checked( $behavior['badge_style'], 'dot' ); ?> />
					<?php esc_html_e( 'Dot pulse animation', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[badge_style]" value="pill" <?php checked( $behavior['badge_style'], 'pill' ); ?> />
					<?php esc_html_e( 'Counter pill style', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row">
					<input type="radio" name="<?php echo esc_attr( $cc_key ); ?>[badge_style]" value="accent" <?php checked( $behavior['badge_style'], 'accent' ); ?> />
					<?php esc_html_e( 'Accent color matcher', EDMINBOOST_TEXT_DOMAIN ); ?>
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

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-declutter-heading">
			<h2 id="edminboost-declutter-heading"><?php esc_html_e( 'Core De-clutter Toggles', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description"><?php esc_html_e( 'Quick switches to clean up native WordPress admin bar clutter.', EDMINBOOST_TEXT_DOMAIN ); ?></p>

			<div class="edminboost-checkbox-grid">
				<label class="edminboost-checkbox-row" for="edminboost_hide_wp_logo">
					<input type="checkbox" id="edminboost_hide_wp_logo" name="<?php echo esc_attr( $cc_key ); ?>[hide_wp_logo]" value="1" <?php checked( ! empty( $behavior['hide_wp_logo'] ) ); ?> />
					<?php esc_html_e( 'Hide WordPress logo', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row" for="edminboost_hide_update_counters">
					<input type="checkbox" id="edminboost_hide_update_counters" name="<?php echo esc_attr( $cc_key ); ?>[hide_update_counters]" value="1" <?php checked( ! empty( $behavior['hide_update_counters'] ) ); ?> />
					<?php esc_html_e( 'Hide update counters', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row" for="edminboost_hide_howdy">
					<input type="checkbox" id="edminboost_hide_howdy" name="<?php echo esc_attr( $cc_key ); ?>[hide_howdy]" value="1" <?php checked( ! empty( $behavior['hide_howdy'] ) ); ?> />
					<?php esc_html_e( 'Hide Howdy / profile text', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
				<label class="edminboost-checkbox-row" for="edminboost_hide_comments">
					<input type="checkbox" id="edminboost_hide_comments" name="<?php echo esc_attr( $cc_key ); ?>[hide_comments]" value="1" <?php checked( ! empty( $behavior['hide_comments'] ) ); ?> />
					<?php esc_html_e( 'Hide comments shortcut', EDMINBOOST_TEXT_DOMAIN ); ?>
				</label>
			</div>
		</section>

		<p class="submit">
			<?php submit_button( __( 'Save Behavior & Style', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
		</p>
	</form>
</div>
