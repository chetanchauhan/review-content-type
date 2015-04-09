<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Retrieve URL for the placeholder image.
 *
 * @since   1.0.0
 * @return string
 */
function rct_get_placeholder_image_url() {

	/**
	 * Filter the placeholder image URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL for the placeholder image.
	 */
	return apply_filters( 'rct_placeholder_image_url', RCT_URL . 'assets/images/placeholder.png' );
}
