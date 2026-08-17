<?php
/**
 * Onboarding & Persona Setup Wizard.
 *
 * @package EdminBoost
 *
 * @var array  $cc_settings Command Center settings.
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option_name   = EDMINBOOST_Settings::OPTION_NAME;
$personas      = EDMINBOOST_Command_Center::get_personas();
$active_persona = isset( $cc_settings['persona'] ) ? $cc_settings['persona'] : '';
$mapper_url    = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . EDMINBOOST_Command_Center::PAGE_MAPPER );
$settings_url  = admin_url( 'admin.php?page=' . EDMINBOOST_Admin::PAGE_SLUG . '-settings' );
?>
<div class="wrap edminboost-wrap edminboost-cc-wrap">
	<?php include EDMINBOOST_PLUGIN_DIR . 'admin/partials/edminboost-command-center-nav.php'; ?>

	<header class="edminboost-cc-hero">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p class="edminboost-cc-hero__lead">
			<?php esc_html_e( 'Welcome to EdminBoost. Let\'s transform your WordPress admin experience in seconds.', EDMINBOOST_TEXT_DOMAIN ); ?>
		</p>
	</header>

	<form action="options.php" method="post" class="edminboost-cc-form">
		<?php settings_fields( EDMINBOOST_Settings::SETTINGS_GROUP ); ?>

		<section class="edminboost-card edminboost-cc-section" aria-labelledby="edminboost-persona-heading">
			<h2 id="edminboost-persona-heading"><?php esc_html_e( 'Step 1: Choose Your Persona', EDMINBOOST_TEXT_DOMAIN ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Select a preset tailored to how you work. You can fine-tune everything later in the Layout Studio.', EDMINBOOST_TEXT_DOMAIN ); ?>
			</p>

			<div class="edminboost-persona-grid" role="radiogroup" aria-label="<?php esc_attr_e( 'Persona preset', EDMINBOOST_TEXT_DOMAIN ); ?>">
				<?php foreach ( $personas as $persona_id => $persona ) : ?>
					<?php
					$is_selected = ( $active_persona === $persona_id );
					$input_id    = 'edminboost_persona_' . $persona_id;
					?>
					<label class="edminboost-persona-card<?php echo $is_selected ? ' is-selected' : ''; ?>" for="<?php echo esc_attr( $input_id ); ?>">
						<input
							type="radio"
							class="edminboost-persona-card__input screen-reader-text"
							name="<?php echo esc_attr( $option_name ); ?>[command_center][persona]"
							id="<?php echo esc_attr( $input_id ); ?>"
							value="<?php echo esc_attr( $persona_id ); ?>"
							data-preset="<?php echo esc_attr( $persona['preset'] ); ?>"
							<?php checked( $is_selected ); ?>
						/>
						<span class="edminboost-persona-card__icon dashicons <?php echo esc_attr( $persona['icon'] ); ?>" aria-hidden="true"></span>
						<span class="edminboost-persona-card__title"><?php echo esc_html( $persona['title'] ); ?></span>
						<span class="edminboost-persona-card__desc"><?php echo esc_html( $persona['description'] ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</section>

		<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>[command_center][onboarding_completed]" value="1" />
		<input
			type="hidden"
			name="<?php echo esc_attr( $option_name ); ?>[command_center][default_preset]"
			id="edminboost_default_preset"
			value="<?php echo esc_attr( isset( $cc_settings['default_preset'] ) ? $cc_settings['default_preset'] : 'system_client' ); ?>"
		/>

		<footer class="edminboost-cc-footer">
			<?php submit_button( __( 'Apply Preset & Launch Command Center', EDMINBOOST_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
			<a class="edminboost-cc-footer__secondary" href="<?php echo esc_url( $mapper_url ); ?>">
				<?php esc_html_e( 'Skip to Advanced Manual Setup', EDMINBOOST_TEXT_DOMAIN ); ?>
			</a>
			<span class="edminboost-cc-footer__divider" aria-hidden="true">|</span>
			<a class="edminboost-cc-footer__secondary" href="<?php echo esc_url( $settings_url ); ?>">
				<?php esc_html_e( 'Classic Settings', EDMINBOOST_TEXT_DOMAIN ); ?>
			</a>
		</footer>
	</form>
</div>
