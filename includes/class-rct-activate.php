<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RCT_Activate
 */
class RCT_Activate {

	/**
	 * @since     1.0.0
	 */
	public static function activate() {
		// Setup the plugin custom post types and taxonomies.
		RCT_Post_Types::register_taxonomies();
		RCT_Post_Types::register_post_types();

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Add all the required capabilities.
		self::add_caps();

		update_option( 'rct_version', RCT_VERSION );
	}

	/**
	 * Adds required capabilities for managing reviews to administrator role.
	 *
	 * @since     1.0.0
	 */
	public static function add_caps() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		if ( is_object( $wp_roles ) ) {
			// Add the main post type capabilities
			$capability_type = 'review';
			$capabilities    = array(
				// Post type
				"edit_{$capability_type}",
				"delete_{$capability_type}",
				"read_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",
				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"assign_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
			);

			foreach ( $capabilities as $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

}
