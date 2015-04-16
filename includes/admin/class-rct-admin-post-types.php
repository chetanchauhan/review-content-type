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

}

new RCT_Admin_Post_Types();
