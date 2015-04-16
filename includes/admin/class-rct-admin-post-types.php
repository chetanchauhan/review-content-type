<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RCT_Admin_Post_Types
 */
class RCT_Admin_Post_Types {

	/**
	 * @since   1.0.0
	 */
	public function __construct() {
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		// Allow filtering of reviews by taxonomy on the Reviews list table.
		add_action( 'restrict_manage_posts', array( $this, 'add_taxonomy_filters' ) );
	}

	/**
	 * Modify the default "Enter title here" text.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $title
	 * @param  object $post
	 *
	 * @return string
	 */
	public function enter_title_here( $title, $post ) {
		if ( 'review' === $post->post_type ) {
			$title = __( 'Enter review title here', 'review-content-type' );
		}

		return $title;
	}

	/**
	 * Modifies the post updated messages for reviews.
	 *
	 * @since  1.0.0
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array $messages Modified post update messages.
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$permalink         = get_permalink( $post_ID );
		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );

		$view_link    = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Review', 'review-content-type' ) );
		$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview Review', 'review-content-type' ) );

		$messages['review'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Review updated. %s', 'review-content-type' ), $view_link ),
			2  => __( 'Custom field updated.', 'review-content-type' ),
			3  => __( 'Custom field deleted.', 'review-content-type' ),
			4  => __( 'Review updated.', 'review-content-type' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Review restored to revision from %s', 'review-content-type' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Review published. %s', 'review-content-type' ), $view_link ),
			7  => __( 'Review saved.', 'review-content-type' ),
			8  => sprintf( __( 'Review submitted. %s', 'review-content-type' ), $preview_link ),
			9  => sprintf( __( 'Review scheduled for: <strong>%1$s</strong>. %2$s', 'review-content-type' ), date_i18n( __( 'M j, Y @ G:i', 'review-content-type' ), strtotime( $post->post_date ) ), $view_link ),
			10 => sprintf( __( 'Review draft updated. %s', 'review-content-type' ), $preview_link ),
		);

		return $messages;
	}

	/**
	 * Add taxonomy filters to the Reviews list page.
	 *
	 * @since   1.0.0
	 */
	public function add_taxonomy_filters() {
		global $typenow;

		if ( 'review' === $typenow ) {
			$taxonomies = apply_filters( 'rct_filter_reviews_by_taxonomies', array( 'review_category', 'review_tag' ) );

			foreach ( $taxonomies as $taxonomy ) {
				// Retrieve the taxonomy object.
				if ( ! $taxonomy = get_taxonomy( $taxonomy ) ) {
					// Taxonomy doesn't exist.
					continue;
				}

				// Retrieve all the term objects for the current taxonomy.
				$terms = get_terms( $taxonomy->name );
				// Get the current taxonomy term selected from the filter.
				$selected = isset( $_GET[ $taxonomy->name ] ) ? $_GET[ $taxonomy->name ] : false;

				// Output the taxonomy dropdown filter.
				if ( ! empty( $terms ) ) {
					echo '<select name="' . esc_attr( $taxonomy->name ) . '" id="' . esc_attr( $taxonomy->name ) . '" class="postform">';
					echo '<option value="0">View All ' . esc_html( $taxonomy->labels->menu_name ) . '</option>';
					foreach ( $terms as $term ) {
						printf( '<option value="%s"%s>%s (%s)</option>', esc_attr( $term->slug ), selected( $term->slug, $selected, false ), esc_html( $term->name ), esc_html( $term->count ) );
					}
					echo '</select>';
				}
			}
		}
	}
}

new RCT_Admin_Post_Types();
