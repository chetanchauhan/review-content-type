<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Return the path to the templates directory in this plugin.
 *
 * @since   1.0.0
 * @return string
 */
function rct_get_templates_dir() {
	return RCT_DIR . 'templates';
}

/**
 * Return the directory name where plugin templates are located in theme.
 *
 * Themes can filter this by using the rct_theme_templates_dir filter.
 *
 * @since   1.0.0
 * @return string
 */
function rct_get_theme_templates_dir() {
	return trailingslashit( apply_filters( 'rct_theme_templates_dir', 'review-content-type' ) );
}

/**
 * Retrieves a template part.
 *
 * @since   1.0.0
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 *
 * @return string
 */
function rct_get_template_part( $slug, $name = null ) {
	// Fires before the specified template part file is loaded.
	do_action( 'get_template_part_' . $slug, $slug, $name );

	$templates = array();
	if ( isset( $name ) ) {
		$templates[] = $slug . '-' . $name . '.php';
	}
	$templates[] = $slug . '.php';

	// Allow template choices to be filtered.
	$templates = apply_filters( 'rct_get_template_part', $templates, $slug, $name );

	return rct_locate_template( $templates, true, false );
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * @since   1.0.0
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool         $load           If true the template file will be loaded if it is found.
 * @param bool         $require_once   Whether to require_once or require. Default true. Has no effect if `$load` is false.
 *
 * @return string The template filename if one is located.
 */
function rct_locate_template( $template_names, $load = false, $require_once = true ) {
	// No file found yet.
	$located = false;

	// Remove empty entries.
	$template_names = array_filter( (array) $template_names );
	$template_paths = rct_get_template_paths();

	// Try to find a template file.
	foreach ( $template_names as $template_name ) {
		// Trim off any slashes from the template name.
		$template_name = ltrim( $template_name, '/' );

		// Try locating this template file by looping through the template paths.
		foreach ( $template_paths as $template_path ) {
			if ( file_exists( $template_path . $template_name ) ) {
				$located = $template_path . $template_name;
				break 2;
			}
		}
	}

	if ( $load && $located ) {
		load_template( $located, $require_once );
	}

	return $located;
}

/**
 * Return a list of paths to check for template locations.
 *
 * @since 1.0.0
 * @return array
 */
function rct_get_template_paths() {
	$theme_directory = rct_get_theme_templates_dir();
	$file_paths      = array(
		10  => trailingslashit( get_template_directory() ) . $theme_directory,
		100 => rct_get_templates_dir(),
	);

	// Only add this conditionally, so non-child themes don't redundantly check active theme twice.
	if ( is_child_theme() ) {
		$file_paths[1] = trailingslashit( get_stylesheet_directory() ) . $theme_directory;
	}

	// Allow ordered list of template paths to be amended.
	$file_paths = apply_filters( 'rct_template_paths', $file_paths );

	// Sort the file paths based on priority.
	ksort( $file_paths, SORT_NUMERIC );

	return array_map( 'trailingslashit', $file_paths );
}

/**
 * Append extra content when displaying single review.
 *
 * @since 1.0.8
 * @global       $post
 *
 * @param string $content Content of the current review.
 *
 * @return string
 */
function rct_review_content( $content ) {
	global $post;

	if ( ! is_singular( 'review' ) || ! is_main_query() ) {
		return $content;
	}

	remove_filter( 'the_content', 'rct_review_content' );

	if ( 'review' === $post->post_type ) {
		ob_start();
		rct_get_template_part( 'content-single', 'review' );
		$content = ob_get_clean();
	}

	add_filter( 'the_content', 'rct_review_content' );

	return $content;
}

add_filter( 'the_content', 'rct_review_content' );

/**
 * Retrieves the reviewed item name.
 *
 * @since   1.0.0
 *
 * @param int $review_id Review ID
 *
 * @return string
 */
function rct_get_reviewed_item_name( $review_id = 0 ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$name = get_post_meta( $review_id, '_rct_name', true );

	return apply_filters( 'rct_get_reviewed_item_name', $name, $review_id );
}

/**
 * Retrieves the review pros.
 *
 * @since   1.0.0
 *
 * @param int $review_id Review ID
 *
 * @return array
 */
function rct_get_review_pros( $review_id = 0 ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$pros = get_post_meta( $review_id, '_rct_pros', true );

	return (array) apply_filters( 'rct_get_review_pros', $pros, $review_id );
}

/**
 * Retrieves the review cons.
 *
 * @since   1.0.0
 *
 * @param int $review_id Review ID
 *
 * @return array
 */
function rct_get_review_cons( $review_id = 0 ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$cons = get_post_meta( $review_id, '_rct_cons', true );

	return (array) apply_filters( 'rct_get_review_cons', $cons, $review_id );
}

/**
 * Retrieves the review summary.
 *
 * @since   1.0.0
 *
 * @param int $review_id Review ID
 *
 * @return string
 */
function rct_get_review_summary( $review_id = 0 ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$summary = get_post_meta( $review_id, '_rct_summary', true );

	return apply_filters( 'rct_get_review_summary', $summary, $review_id );
}

/**
 * Retrieves the minimum price.
 *
 * @since 1.0.0
 *
 * @param int  $review_id Review ID
 * @param bool $format    Whether to format the price or not.
 *
 * @return string Minimum price. Optionally formatted.
 */
function rct_get_min_price( $review_id = 0, $format = false ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$price = get_post_meta( $review_id, '_rct_min_price', true );
	$price = rct_sanitize_price_amount( $price );
	$price = $format ? rct_format_price_amount( $price ) : $price;

	return apply_filters( 'rct_get_min_price', $price, $review_id, $format );
}

/**
 * Retrieves the maximum price.
 *
 * @since 1.0.0
 *
 * @param int  $review_id Review ID
 * @param bool $format    Whether to format the price or not.
 *
 * @return string Maximum price. Optionally formatted.
 */
function rct_get_max_price( $review_id = 0, $format = false ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$price = get_post_meta( $review_id, '_rct_max_price', true );
	$price = rct_sanitize_price_amount( $price );
	$price = $format ? rct_format_price_amount( $price ) : $price;

	return apply_filters( 'rct_get_max_price', $price, $review_id, $format );
}

/**
 * Return or display the review pros heading.
 *
 * @since   1.0.0
 *
 * @param   int  $review_id Review ID
 * @param   bool $echo      Whether to display or return.
 *
 * @return string
 */
function rct_review_pros_heading( $review_id = 0, $echo = true ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$heading = get_post_meta( $review_id, '_rct_pros_heading', true );
	$heading = empty( $heading ) ? review_content_type()->settings->get( 'pros_heading', 'display' ) : $heading;

	if ( $echo ) {
		echo $heading;
	} else {
		return $heading;
	}
}

/**
 * Return or display the review cons heading.
 *
 * @since   1.0.0
 *
 * @param   int  $review_id Review ID
 * @param   bool $echo      Whether to display or return.
 *
 * @return string
 */
function rct_review_cons_heading( $review_id = 0, $echo = true ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$heading = get_post_meta( $review_id, '_rct_cons_heading', true );
	$heading = empty( $heading ) ? review_content_type()->settings->get( 'cons_heading', 'display' ) : $heading;

	if ( $echo ) {
		echo $heading;
	} else {
		return $heading;
	}
}

/**
 * Return or display the review summary heading.
 *
 * @since   1.0.0
 *
 * @param   int  $review_id Review ID
 * @param   bool $echo      Whether to display or return.
 *
 * @return string
 */
function rct_review_summary_heading( $review_id = 0, $echo = true ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$heading = get_post_meta( $review_id, '_rct_summary_heading', true );
	$heading = empty( $heading ) ? review_content_type()->settings->get( 'summary_heading', 'display' ) : $heading;

	if ( $echo ) {
		echo $heading;
	} else {
		return $heading;
	}
}

/**
 * Retrieve the reviews featured image url.
 *
 * @since 1.0.0
 *
 * @param int $review_id Review ID
 *
 * @return string $url Review featured image url (if available). Otherwise empty string.
 */
function rct_get_featured_image_url( $review_id = 0 ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}

	// Bail early if post type is not review.
	if ( 'review' !== get_post_type( $review_id ) ) {
		return '';
	}

	$link      = get_post_meta( $review_id, '_rct_featured_image_link', true );
	$link_type = isset( $link['type'] ) ? $link['type'] : '';
	switch ( $link_type ) {
		case 'file':
			$url = wp_get_attachment_url( get_post_thumbnail_id() );
			if ( false === $url ) {
				$url = rct_get_placeholder_image_url();
			}
			break;
		case 'review':
			$url = get_the_permalink( $review_id );
			break;
		case 'custom':
			$url = isset( $link['url'] ) ? $link['url'] : '';
			break;
		default:
			$url = '';
	}

	return apply_filters( 'rct_get_featured_image_url', $url, $link_type, $review_id );
}

/**
 * Retrieves call to action link of a review.
 *
 * @since   1.0.0
 *
 * @param int $review_id Review ID
 *
 * @return string
 */
function rct_get_review_link( $review_id = 0 ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}
	$link_url = esc_url( get_post_meta( $review_id, '_rct_link_url', true ) );
	if ( empty( $link_url ) ) {
		return;
	}
	$link_text = get_post_meta( $review_id, '_rct_link_text', true );
	if ( empty( $link_text ) ) {
		$link_text = review_content_type()->settings->get( 'link_text', 'display' );
	}

	$link_style = get_post_meta( $review_id, '_rct_link_style', true );

	$css_classes = 'rct-review-link rct-' . $link_style . '-link-style';

	$html = '<a class="' . esc_attr( $css_classes ) . '" href="' . $link_url . '" itemprop="url" target="_blank" rel="nofollow">' . $link_text . '</a>';

	return $html;
}

/**
 * Display the call to action link of a review.
 *
 * @since 1.0.0
 *
 * @param int $review_id Review ID
 */
function rct_review_link( $review_id = 0 ) {
	echo rct_get_review_link( $review_id );
}

/**
 * Retrieves rating type that should be used for displaying
 * ratings of a review.
 *
 * @since   1.0.0
 *
 * @param int $review_id Review ID
 *
 * @return string
 */
function rct_get_rating_type( $review_id = 0 ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}

	$rating_type = get_post_meta( $review_id, '_rct_rating_type', true );
	if ( empty( $rating_type ) ) {
		$rating_type = review_content_type()->settings->get( 'rating_type', 'rating' );
	}

	return apply_filters( 'rct_get_rating_type', $rating_type, $review_id );
}

/**
 * Retrieves rating value of a review on the scale of 0-100.
 *
 * @since   1.0.0
 *
 * @param int $review_id Review ID
 *
 * @return string
 */
function rct_get_review_rating( $review_id = 0 ) {
	if ( ! $review_id ) {
		$review_id = get_the_ID();
	}

	return min( abs( get_post_meta( $review_id, '_rct_rating', true ) ), 100 );
}

/**
 * Outputs the rating.
 *
 * @since 1.0.0
 *
 * @param int|float $rating      Rating to display, expressed in 0-100 scale.
 * @param string    $rating_type Rating type that should be used for displaying the rating.
 */
function rct_rating_html( $rating, $rating_type ) {
	$rating = rct_adjust_rating( $rating, $rating_type );
	$scale  = rct_get_rating_scale( $rating_type );
	$title  = sprintf( __( 'Rated %s out of %s', 'review-content-type' ), $rating, $scale['max'] );

	$html = '<span class="rct-' . sanitize_html_class( $rating_type ) . '-rating" title="' . esc_attr( $title ) . '">';

	$html .= '<span class="screen-reader-text">' . $title . '</span>';

	switch ( $rating_type ) {
		case 'star':
			// Calculate the number of each type of star needed
			$full_stars  = floor( $rating );
			$half_stars  = ceil( $rating - $full_stars );
			$empty_stars = $scale['max'] - $full_stars - $half_stars;
			$html .= str_repeat( '<span class="dashicons dashicons-star-filled"></span>', $full_stars );
			$html .= str_repeat( '<span class="dashicons dashicons-star-half"></span>', $half_stars );
			$html .= str_repeat( '<span class="dashicons dashicons-star-empty"></span>', $empty_stars );
			break;
		case 'percent':
			$html .= "{$rating}%";
			break;
		default:
			$html .= $rating . '/' . $scale['max'];
	}

	echo apply_filters( 'rct_rating_html', $html, $rating, $rating_type );
}

/**
 * Display the reviewed item name on single review page.
 */
function rct_display_review_name() {
	rct_get_template_part( 'content-single-review-name' );
}

add_action( 'rct_before_review_content', 'rct_display_review_name', 10 );

/**
 * Display the review pros & cons on single review page.
 */
function rct_display_review_pros_cons() {
	rct_get_template_part( 'content-single-review-pros-cons' );
}

add_action( 'rct_before_review_content', 'rct_display_review_pros_cons', 15 );

/**
 * Display the featured image on single review page.
 */
function rct_display_review_featured_image() {
	rct_get_template_part( 'content-single-review-featured-image' );
}

add_action( 'rct_before_review_content', 'rct_display_review_featured_image', 20 );

/**
 * Display the review summary on single review page.
 */
function rct_display_review_summary() {
	rct_get_template_part( 'content-single-review-summary' );
}

add_action( 'rct_before_review_content', 'rct_display_review_summary', 30 );

/**
 * Display review rating on single review page.
 */
function rct_display_review_rating() {
	rct_get_template_part( 'content-single-review-rating' );
}

add_action( 'rct_after_featured_image', 'rct_display_review_rating', 10 );

/**
 * Display review price on single review page.
 */
function rct_display_review_price() {
	rct_get_template_part( 'content-single-review-price' );
}

add_action( 'rct_after_featured_image', 'rct_display_review_price', 20 );

/**
 * Display the review call to action link on single review page.
 */
add_action( 'rct_after_featured_image', 'rct_review_link', 30 );


