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
	 * Assign default user roles the capabilities for managing reviews.
	 *
	 * @since     1.0.0
	 */
	public static function add_caps() {
		$capabilities = array(
			'edit_reviews',
			'edit_others_reviews',
			'publish_reviews',
			'read_private_reviews',
			'delete_reviews',
			'delete_private_reviews',
			'delete_published_reviews',
			'delete_others_reviews',
			'edit_private_reviews',
			'edit_published_reviews',
			'manage_review_terms',
			'edit_review_terms',
			'delete_review_terms',
			'assign_review_terms',
		);

		foreach ( array( 'administrator', 'editor' ) as $role ) {
			$role = get_role( $role );
			if ( isset( $role ) ) {
				foreach ( $capabilities as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}

		$role = get_role( 'author' );
		if ( isset( $role ) ) {
			$role->add_cap( 'edit_reviews' );
			$role->add_cap( 'publish_reviews' );
			$role->add_cap( 'delete_reviews' );
			$role->add_cap( 'delete_published_reviews' );
			$role->add_cap( 'edit_published_reviews' );
			$role->add_cap( 'assign_review_terms' );
		}

		$role = get_role( 'contributor' );
		if ( isset( $role ) ) {
			$role->add_cap( 'edit_reviews' );
			$role->add_cap( 'delete_reviews' );
			$role->add_cap( 'assign_review_terms' );
		}
	}
}
