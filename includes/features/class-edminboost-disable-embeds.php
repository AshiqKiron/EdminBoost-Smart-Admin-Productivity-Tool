<?php
/**
 * Disable WordPress oEmbed / embeds.
 *
 * @package EdminBoost
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disables oEmbed discovery and embed scripts.
 */
class EDMINBOOST_Disable_Embeds extends EDMINBOOST_Feature_Base {

	/**
	 * Feature ID.
	 *
	 * @var string
	 */
	protected $id = 'disable_embeds';

	/**
	 * Feature name.
	 *
	 * @var string
	 */
	protected $name = 'Disable Embeds';

	/**
	 * Feature description.
	 *
	 * @var string
	 */
	protected $description = 'Disable WordPress embeds and oEmbed discovery.';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		add_filter( 'embed_oembed_discover', '__return_false' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		add_filter( 'tiny_mce_plugins', array( $this, 'remove_embed_plugin' ) );
		add_filter( 'rewrite_rules_array', array( $this, 'remove_embed_rewrite' ) );
	}

	/**
	 * Remove wpembed from TinyMCE.
	 *
	 * @param array $plugins TinyMCE plugins.
	 * @return array
	 */
	public function remove_embed_plugin( $plugins ) {
		return array_diff( $plugins, array( 'wpembed' ) );
	}

	/**
	 * Remove embed rewrite rules.
	 *
	 * @param array $rules Rewrite rules.
	 * @return array
	 */
	public function remove_embed_rewrite( $rules ) {
		foreach ( $rules as $rule => $rewrite ) {
			if ( false !== strpos( $rewrite, 'embed=true' ) ) {
				unset( $rules[ $rule ] );
			}
		}

		return $rules;
	}
}
