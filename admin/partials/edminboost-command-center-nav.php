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

$edminboost_nav_items    = EDMINBOOST_Command_Center::get_nav_items();
$edminboost_current_page = isset( $current_page ) ? $current_page : '';
?>
<nav class="edminboost-cc-nav" aria-label="<?php esc_attr_e( 'EdminBoost Command Center', 'edminboost' ); ?>">
	<ul class="edminboost-cc-nav__list">
		<?php foreach ( $edminboost_nav_items as $edminboost_item ) : ?>
			<?php
			$edminboost_is_active   = ( $edminboost_current_page === $edminboost_item['slug'] );
			$edminboost_is_external = ! empty( $edminboost_item['external'] ) && ! empty( $edminboost_item['url'] );
			$edminboost_url         = $edminboost_is_external ? $edminboost_item['url'] : admin_url( 'admin.php?page=' . $edminboost_item['slug'] );
			?>
			<li class="edminboost-cc-nav__item">
				<a
					class="edminboost-cc-nav__link<?php echo $edminboost_is_active ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $edminboost_url ); ?>"
					data-edminboost-page="<?php echo esc_attr( $edminboost_item['slug'] ); ?>"
					<?php if ( $edminboost_is_external ) : ?>
						target="_blank"
						rel="noopener noreferrer"
						data-edminboost-external="1"
					<?php endif; ?>
					<?php echo $edminboost_is_active ? ' aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $edminboost_item['label'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
