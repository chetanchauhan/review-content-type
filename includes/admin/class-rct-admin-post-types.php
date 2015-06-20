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
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );
		add_filter( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ), 5 );

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
	 * Modifies the bulk action updated messages for reviews.
	 *
	 * @since 2.3
	 *
	 * @param array $bulk_messages Post updated messages.
	 * @param array $bulk_counts Post counts.
	 *
	 * @return array $bulk_messages Modified post updated messages
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['review'] = array(
			'updated'   => _n( '%s review updated.', '%s reviews updated.', $bulk_counts['updated'], 'review-content-type' ),
			'locked'    => _n( '%s review not updated, somebody is editing it.', '%s reviews not updated, somebody is editing them.', $bulk_counts['locked'], 'review-content-type' ),
			'deleted'   => _n( '%s review permanently deleted.', '%s reviews permanently deleted.', $bulk_counts['deleted'], 'review-content-type' ),
			'trashed'   => _n( '%s review moved to the Trash.', '%s reviews moved to the Trash.', $bulk_counts['trashed'], 'review-content-type' ),
			'untrashed' => _n( '%s review restored from the Trash.', '%s reviews restored from the Trash.', $bulk_counts['untrashed'], 'review-content-type' ),
		);

		return $bulk_messages;
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

	/**
	 * Add reviews count to 'At a Glance' dashboard widget.
	 *
	 * @since   1.0.0
	 *
	 * @param array $items Existing array of extra 'At a Glance' widget items.
	 *
	 * @return array
	 */
	public function dashboard_glance_items( $items ) {
		$num_posts = wp_count_posts( 'review' );
		if ( $num_posts && $num_posts->publish ) {
			$text = _n( '%s Review', '%s Reviews', $num_posts->publish, 'review-content-type' );
			$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
			if ( current_user_can( 'edit_reviews' ) ) {
				$text = sprintf( '<a class="review-count" href="edit.php?post_type=review">%1$s</a>', $text );
			} else {
				$text = sprintf( '<span class="review-count">%1$s</span>', $text );
			}
			$items[] = $text;
		}

		return $items;
	}

}

new RCT_Admin_Post_Types();
