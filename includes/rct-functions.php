<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Retrieve post featured image whenever available or placeholder image.
 *
 * @since 1.0.0
 *
 * @param int          $post_id    Optional. Post ID.
 * @param string       $image_size Optional. Image size. Defaults to 'rct_featured_image'.
 * @param string|array $attr       Optional. Query string or array of attributes.
 *
 * @return  string
 */
function rct_get_featured_image( $post_id = null, $image_size = 'rct_featured_image', $attr = '' ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $image_size, $attr );
	}

	return rct_get_placeholder_image( $image_size, $attr );
}

/**
 * Retrieve placeholder image.
 *
 * @since   1.0.0
 *
 * @param string       $image_size Image size. Defaults to 'rct_featured_image'.
 * @param string|array $attr       Attributes for the image markup. Default empty string.
 *
 * @return string
 */
function rct_get_placeholder_image( $image_size = 'rct_featured_image', $attr = '' ) {
	$size         = rct_get_image_size( $image_size );
	$default_attr = array(
		'src'    => rct_get_placeholder_image_url(),
		'class'  => 'rct-placeholder-image attachment-' . $image_size . ' wp-post-image',
		'width'  => $size['width'],
		'height' => $size['height'],
	);
	$attr         = wp_parse_args( $attr, $default_attr );
	$attr         = apply_filters( 'rct_placeholder_image_html_attributes', $attr, $image_size );
	$html         = '<img';
	foreach ( $attr as $name => $value ) {
		$html .= " $name=" . '"' . esc_attr( $value ) . '"';
	}
	$html .= ' />';

	return $html;
}

/**
 * Retrieve the dimensions of a registered image size.
 *
 * @since   1.0.0
 *
 * @param string $image_size
 *
 * @return array
 */
function rct_get_image_size( $image_size ) {
	global $_wp_additional_image_sizes;
	$size = array(
		'width'  => '300',
		'height' => '300',
		'crop'   => 1,
	);
	if ( in_array( $image_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
		$size['width']  = get_option( $image_size . '_size_w' );
		$size['height'] = get_option( $image_size . '_size_h' );
		$size['crop']   = (bool) get_option( $image_size . '_crop' );
	} elseif ( isset( $_wp_additional_image_sizes[ $image_size ] ) && is_array( $_wp_additional_image_sizes[ $image_size ] ) ) {
		$size = array_merge( $size, $_wp_additional_image_sizes[ $image_size ] );
	}

	return apply_filters( 'rct_get_image_size_' . $image_size, $size );
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
