<?php
/**
 * Command Center sub-navigation tabs.
 *
 * @package EdminBoost
 *
 * @var string $current_page Current page slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nav_items = EDMINBOOST_Command_Center::get_nav_items();
?>
<nav class="edminboost-cc-nav" aria-label="<?php esc_attr_e( 'EdminBoost Command Center', EDMINBOOST_TEXT_DOMAIN ); ?>">
	<ul class="edminboost-cc-nav__list">
		<?php foreach ( $nav_items as $item ) : ?>
			<?php
			$is_active = ( $current_page === $item['slug'] );
			$url       = admin_url( 'admin.php?page=' . $item['slug'] );
			?>
			<li class="edminboost-cc-nav__item">
				<a
					class="edminboost-cc-nav__link<?php echo $is_active ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $url ); ?>"
					data-edminboost-page="<?php echo esc_attr( $item['slug'] ); ?>"
					<?php echo $is_active ? ' aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
