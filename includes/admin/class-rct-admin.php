<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RCT_Admin
 */
class RCT_Admin {

	/**
	 * @since   1.0.0
	 */
	public function __construct() {
		global $pagenow;

		require_once( 'class-rct-admin-post-types.php' );
		require_once( 'class-rct-admin-meta-boxes.php' );
		if ( 'options-permalink.php' === $pagenow ) {
			require_once( 'class-rct-admin-permalink-settings.php' );
		}

		add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( RCT_FILE ), array( $this, 'add_action_links' ) );
	}

	/**
	 * Register the administration menu and sub menus for this plugin
	 * into the WordPress Dashboard menu.
	 *
	 * @since     1.0.0
	 */
	public function add_admin_menus() {
		add_submenu_page(
			'edit.php?post_type=review',
			__( 'Review Content Type Settings', 'review-content-type' ),
			__( 'Settings', 'review-content-type' ),
			'manage_options',
			'rct_settings',
			array( review_content_type()->settings, 'display_settings' )
		);
	}

	/**
	 * Add action links to the plugins page.
	 *
	 * @since     1.0.0
	 *
	 * @param mixed $links
	 *
	 * @return    array
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'edit.php?post_type=review&page=rct_settings' ) . '">' . __( 'Settings', 'review-content-type' ) . '</a>'
			),
			$links
		);
	}

}

new RCT_Admin();
