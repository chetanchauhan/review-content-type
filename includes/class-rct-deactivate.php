<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RCT_Deactivate
 */
class RCT_Deactivate {

	/**
	 * @since     1.0.0
	 */
	public static function deactivate() {
		// Flush rewrite rules.
		flush_rewrite_rules();

		// Remove all the custom capabilities added during plugin activation.
		self::remove_caps();
	}

	/**
	 * Remove capabilities for managing reviews from default user roles.
	 *
	 * @since     1.0.2
	 */
	public static function remove_caps() {
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
					$role->remove_cap( $cap );
				}
			}
		}

		$role = get_role( 'author' );
		if ( isset( $role ) ) {
			$role->remove_cap( 'edit_reviews' );
			$role->remove_cap( 'publish_reviews' );
			$role->remove_cap( 'delete_reviews' );
			$role->remove_cap( 'delete_published_reviews' );
			$role->remove_cap( 'edit_published_reviews' );
			$role->remove_cap( 'assign_review_terms' );
		}

		$role = get_role( 'contributor' );
		if ( isset( $role ) ) {
			$role->remove_cap( 'edit_reviews' );
			$role->remove_cap( 'delete_reviews' );
			$role->remove_cap( 'assign_review_terms' );
		}
	}
}
