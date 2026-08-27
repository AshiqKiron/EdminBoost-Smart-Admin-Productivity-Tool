<?php
/**
 * Setup wizard review step (populated by JS).
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="edminboost-setup-review" id="edminboost-setup-review" aria-live="polite">
	<dl class="edminboost-setup-review__list">
		<div class="edminboost-setup-review__row">
			<dt><?php esc_html_e( 'Layout preset', EDMINBOOST_TEXT_DOMAIN ); ?></dt>
			<dd id="edminboost-review-layout">—</dd>
		</div>
		<div class="edminboost-setup-review__row">
			<dt><?php esc_html_e( 'Color theme', EDMINBOOST_TEXT_DOMAIN ); ?></dt>
			<dd id="edminboost-review-theme">—</dd>
		</div>
		<div class="edminboost-setup-review__row">
			<dt><?php esc_html_e( 'Sidebar menu', EDMINBOOST_TEXT_DOMAIN ); ?></dt>
			<dd id="edminboost-review-sidebar">—</dd>
		</div>
		<div class="edminboost-setup-review__row">
			<dt><?php esc_html_e( 'Top bar links', EDMINBOOST_TEXT_DOMAIN ); ?></dt>
			<dd id="edminboost-review-topbar">—</dd>
		</div>
	</dl>
</div>
