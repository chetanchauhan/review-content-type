<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Register custom post types and taxonomies.
 */
class RCT_Post_Types {

	/**
	 * @since   1.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 1 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 1 );
	}

	/**
	 * Register custom taxonomies for the plugin.
	 *
	 * @since   1.0.0
	 */
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'review_category' ) ) {
			return;
		}

		$settings      = get_option( 'rct_permalink_settings' );
		$category_base = empty( $settings['category_base'] ) ? _x( 'reviews/category', 'slug', 'review-content-type' ) : $settings['category_base'];
		$tag_base      = empty( $settings['tag_base'] ) ? _x( 'reviews/tag', 'slug', 'review-content-type' ) : $settings['tag_base'];

		// Categories
		$category_args = array(
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'query_var'         => 'review_category',
			'rewrite'           => array(
				'slug'         => $category_base,
				'with_front'   => false,
				'hierarchical' => true,
			),
			'capabilities'      => array(
				'manage_terms' => 'manage_review_terms',
				'edit_terms'   => 'edit_review_terms',
				'assign_terms' => 'assign_review_terms',
				'delete_terms' => 'delete_review_terms',
			),
			'labels'            => array(
				'name'                       => __( 'Review Categories', 'review-content-type' ),
				'singular_name'              => __( 'Review Category', 'review-content-type' ),
				'menu_name'                  => __( 'Categories', 'review-content-type' ),
				'all_items'                  => __( 'All Review Categories', 'review-content-type' ),
				'edit_item'                  => __( 'Edit Review Category', 'review-content-type' ),
				'view_item'                  => __( 'View Review Category', 'review-content-type' ),
				'update_item'                => __( 'Update Review Category', 'review-content-type' ),
				'add_new_item'               => __( 'Add New Review Category', 'review-content-type' ),
				'new_item_name'              => __( 'New Review Category Name', 'review-content-type' ),
				'parent_item'                => __( 'Parent Review Category', 'review-content-type' ),
				'parent_item_colon'          => __( 'Parent Review Category:', 'review-content-type' ),
				'search_items'               => __( 'Search Review Categories', 'review-content-type' ),
				'popular_items'              => __( 'Popular Review Categories', 'review-content-type' ),
				'separate_items_with_commas' => __( 'Separate review categories with commas', 'review-content-type' ),
				'add_or_remove_items'        => __( 'Add or remove review categories', 'review-content-type' ),
				'choose_from_most_used'      => __( 'Choose from the most used review categories', 'review-content-type' ),
				'not_found'                  => __( 'No review categories found.', 'review-content-type' ),
			)
		);

		register_taxonomy( 'review_category', array( 'review' ), $category_args );

		// Tags
		$tag_args = array(
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'query_var'         => 'review_tag',
			'rewrite'           => array(
				'slug'         => $tag_base,
				'with_front'   => false,
				'hierarchical' => true,
			),
			'capabilities'      => array(
				'manage_terms' => 'manage_review_terms',
				'edit_terms'   => 'edit_review_terms',
				'assign_terms' => 'assign_review_terms',
				'delete_terms' => 'delete_review_terms',
			),
			'labels'            => array(
				'name'                       => __( 'Review Tags', 'review-content-type' ),
				'singular_name'              => __( 'Review Tag', 'review-content-type' ),
				'menu_name'                  => __( 'Tags', 'review-content-type' ),
				'all_items'                  => __( 'All Review Tags', 'review-content-type' ),
				'edit_item'                  => __( 'Edit Review Tag', 'review-content-type' ),
				'view_item'                  => __( 'View Review Tag', 'review-content-type' ),
				'update_item'                => __( 'Update Review Tag', 'review-content-type' ),
				'add_new_item'               => __( 'Add New Review Tag', 'review-content-type' ),
				'new_item_name'              => __( 'New Review Tag Name', 'review-content-type' ),
				'parent_item'                => __( 'Parent Review Tag', 'review-content-type' ),
				'parent_item_colon'          => __( 'Parent Review Tag:', 'review-content-type' ),
				'search_items'               => __( 'Search Review Tags', 'review-content-type' ),
				'popular_items'              => __( 'Popular Review Tags', 'review-content-type' ),
				'separate_items_with_commas' => __( 'Separate review tags with commas', 'review-content-type' ),
				'add_or_remove_items'        => __( 'Add or remove review tags', 'review-content-type' ),
				'choose_from_most_used'      => __( 'Choose from the most used review tags', 'review-content-type' ),
				'not_found'                  => __( 'No review tags found.', 'review-content-type' ),
			)
		);

		register_taxonomy( 'review_tag', array( 'review' ), $tag_args );
	}

	/**
	 * Register the required post types for the plugin.
	 *
	 * @since  1.0.0
	 */
	public static function register_post_types() {
		if ( post_type_exists( 'review' ) ) {
			return;
		}

		$settings            = get_option( 'rct_permalink_settings' );
		$default_review_base = _x( 'reviews', 'slug', 'review-content-type' );
		$review_base         = empty( $settings['review_base'] ) ? $default_review_base : $settings['review_base'];

		$args = array(
			'label'               => 'Reviews',
			'labels'              => array(
				'name'               => _x( 'Reviews', 'Reviews Post Type General Name', 'review-content-type' ),
				'singular_name'      => _x( 'Review', 'Reviews Post Type Singular Name', 'review-content-type' ),
				'menu_name'          => __( 'Reviews', 'review-content-type' ),
				'name_admin_bar'     => __( 'Review', 'review-content-type' ),
				'all_items'          => __( 'Reviews', 'review-content-type' ),
				'add_new'            => __( 'Add New', 'review-content-type' ),
				'add_new_item'       => __( 'Add New Review', 'review-content-type' ),
				'edit_item'          => __( 'Edit Review', 'review-content-type' ),
				'new_item'           => __( 'New Review', 'review-content-type' ),
				'view_item'          => __( 'View Review', 'review-content-type' ),
				'search_items'       => __( 'Search Reviews', 'review-content-type' ),
				'not_found'          => __( 'No reviews found', 'review-content-type' ),
				'not_found_in_trash' => __( 'No reviews found in trash', 'review-content-type' ),
				'parent_item_colon'  => __( 'Parent Review', 'review-content-type' ),
			),
			'description'         => __( 'Review content type.', 'review-content-type' ),
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-star-filled',
			'capability_type'     => 'review',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'supports'            => array(
				'title',
				'editor',
				'excerpt',
				'author',
				'thumbnail',
				'comments',
				'revisions',
			),
			'has_archive'         => $default_review_base,
			'rewrite'             => array(
				'slug'       => $review_base,
				'with_front' => false,
				'feeds'      => true,
				'pages'      => true,
				'ep_mask'    => EP_PERMALINK,
			),
			'query_var'           => true,
			'can_export'          => true,

		);

		register_post_type( 'review', $args );
	}

}

RCT_Post_Types::init();
