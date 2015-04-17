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

/**
 * Retrieves available link styles.
 *
 * @since  1.0.0
 * @return array
 */
function rct_get_link_styles() {
	$styles = array(
		'button' => __( 'Button', 'review-content-type' ),
		'plain'  => __( 'Plain Text', 'review-content-type' )
	);

	return apply_filters( 'rct_link_styles', $styles );
}

/**
 * Retrieves available rating types.
 *
 * @since  1.0.0
 * @return array
 */
function rct_get_rating_types() {
	$rating_types = array(
		'star'    => __( 'Stars', 'review-content-type' ),
		'point'   => __( 'Points', 'review-content-type' ),
		'percent' => __( 'Percentage', 'review-content-type' ),
	);

	return apply_filters( 'rct_rating_types', $rating_types );
}

/**
 * Retrieves the rating scale of the given rating type.
 *
 * @since   1.0.0
 *
 * @param   string $rating_type Rating type.
 *
 * @return  array
 */
function rct_get_rating_scale( $rating_type ) {
	$scale = array(
		'min'  => 0,
		'max'  => 100,
		'step' => 1,
	);

	switch ( $rating_type ) {
		case 'star':
			$scale['max']  = 5;
			$scale['step'] = 0.5;
			break;
		case 'point':
			$scale['max'] = 10;
	}

	// Override default scale with the customized scale for the current rating type, if available.
	$_scale = review_content_type()->settings->get( 'rating_scale', 'rating' );
	if ( isset( $_scale[ $rating_type ] ) && is_array( $_scale[ $rating_type ] ) ) {
		$scale = array_merge( $scale, $_scale[ $rating_type ] );

		// Sanitize the rating scale when coming from db.
		$scale = array(
			'min'  => absint( $scale['min'] ),
			'max'  => absint( $scale['max'] ),
			'step' => floatval( $scale['step'] ),
		);
	}

	return apply_filters( 'rct_rating_scale', $scale, $rating_type );
}

/**
 * Retrieves all the available currencies.
 *
 * @since   1.0.0
 * @return  array
 */
function rct_get_currencies() {
	$currencies = array(
		'AUD' => __( 'Australian Dollars', 'review-content-type' ),
		'BDT' => __( 'Bangladeshi Taka', 'review-content-type' ),
		'BRL' => __( 'Brazilian Real', 'review-content-type' ),
		'BGN' => __( 'Bulgarian Lev', 'review-content-type' ),
		'CAD' => __( 'Canadian Dollars', 'review-content-type' ),
		'CLP' => __( 'Chilean Peso', 'review-content-type' ),
		'CNY' => __( 'Chinese Yuan', 'review-content-type' ),
		'COP' => __( 'Colombian Peso', 'review-content-type' ),
		'CZK' => __( 'Czech Koruna', 'review-content-type' ),
		'DKK' => __( 'Danish Krone', 'review-content-type' ),
		'DOP' => __( 'Dominican Peso', 'review-content-type' ),
		'EUR' => __( 'Euros', 'review-content-type' ),
		'HKD' => __( 'Hong Kong Dollar', 'review-content-type' ),
		'HRK' => __( 'Croatia kuna', 'review-content-type' ),
		'HUF' => __( 'Hungarian Forint', 'review-content-type' ),
		'ISK' => __( 'Icelandic krona', 'review-content-type' ),
		'IDR' => __( 'Indonesia Rupiah', 'review-content-type' ),
		'INR' => __( 'Indian Rupee', 'review-content-type' ),
		'NPR' => __( 'Nepali Rupee', 'review-content-type' ),
		'ILS' => __( 'Israeli Shekel', 'review-content-type' ),
		'JPY' => __( 'Japanese Yen', 'review-content-type' ),
		'KIP' => __( 'Lao Kip', 'review-content-type' ),
		'KRW' => __( 'South Korean Won', 'review-content-type' ),
		'MYR' => __( 'Malaysian Ringgits', 'review-content-type' ),
		'MXN' => __( 'Mexican Peso', 'review-content-type' ),
		'NGN' => __( 'Nigerian Naira', 'review-content-type' ),
		'NOK' => __( 'Norwegian Krone', 'review-content-type' ),
		'NZD' => __( 'New Zealand Dollar', 'review-content-type' ),
		'PYG' => __( 'Paraguayan Guaraní', 'review-content-type' ),
		'PHP' => __( 'Philippine Pesos', 'review-content-type' ),
		'PLN' => __( 'Polish Zloty', 'review-content-type' ),
		'GBP' => __( 'Pounds Sterling', 'review-content-type' ),
		'RON' => __( 'Romanian Leu', 'review-content-type' ),
		'RUB' => __( 'Russian Ruble', 'review-content-type' ),
		'SGD' => __( 'Singapore Dollar', 'review-content-type' ),
		'ZAR' => __( 'South African rand', 'review-content-type' ),
		'SEK' => __( 'Swedish Krona', 'review-content-type' ),
		'CHF' => __( 'Swiss Franc', 'review-content-type' ),
		'TWD' => __( 'Taiwan New Dollars', 'review-content-type' ),
		'THB' => __( 'Thai Baht', 'review-content-type' ),
		'TRY' => __( 'Turkish Lira', 'review-content-type' ),
		'USD' => __( 'US Dollars', 'review-content-type' ),
		'VND' => __( 'Vietnamese Dong', 'review-content-type' ),
		'EGP' => __( 'Egyptian Pound', 'review-content-type' ),
	);

	return apply_filters( 'rct_currencies', $currencies );
}

/**
 * Retrieves symbol of the given currency.
 *
 * @since 1.0.0
 *
 * @param string $currency Currency code.
 *
 * @return string $currency_symbol Currency symbol.
 */
function rct_get_currency_symbol( $currency ) {
	switch ( $currency ) {
		case 'BDT':
			$currency_symbol = '&#2547;&nbsp;';
			break;
		case 'BRL' :
			$currency_symbol = '&#82;&#36;';
			break;
		case 'BGN' :
			$currency_symbol = '&#1083;&#1074;.';
			break;
		case 'AUD' :
		case 'CAD' :
		case 'CLP' :
		case 'COP' :
		case 'MXN' :
		case 'NZD' :
		case 'HKD' :
		case 'SGD' :
		case 'USD' :
			$currency_symbol = '&#36;';
			break;
		case 'EUR' :
			$currency_symbol = '&euro;';
			break;
		case 'CNY' :
		case 'RMB' :
		case 'JPY' :
			$currency_symbol = '&yen;';
			break;
		case 'RUB' :
			$currency_symbol = '&#1088;&#1091;&#1073;.';
			break;
		case 'KRW' :
			$currency_symbol = '&#8361;';
			break;
		case 'PYG' :
			$currency_symbol = '&#8370;';
			break;
		case 'TRY' :
			$currency_symbol = '&#8378;';
			break;
		case 'NOK' :
			$currency_symbol = '&#107;&#114;';
			break;
		case 'ZAR' :
			$currency_symbol = '&#82;';
			break;
		case 'CZK' :
			$currency_symbol = '&#75;&#269;';
			break;
		case 'MYR' :
			$currency_symbol = '&#82;&#77;';
			break;
		case 'DKK' :
			$currency_symbol = 'kr.';
			break;
		case 'HUF' :
			$currency_symbol = '&#70;&#116;';
			break;
		case 'IDR' :
			$currency_symbol = 'Rp';
			break;
		case 'INR' :
			$currency_symbol = '&#8377;';
			break;
		case 'NPR' :
			$currency_symbol = 'Rs.';
			break;
		case 'ISK' :
			$currency_symbol = 'Kr.';
			break;
		case 'ILS' :
			$currency_symbol = '&#8362;';
			break;
		case 'PHP' :
			$currency_symbol = '&#8369;';
			break;
		case 'PLN' :
			$currency_symbol = '&#122;&#322;';
			break;
		case 'SEK' :
			$currency_symbol = '&#107;&#114;';
			break;
		case 'CHF' :
			$currency_symbol = '&#67;&#72;&#70;';
			break;
		case 'TWD' :
			$currency_symbol = '&#78;&#84;&#36;';
			break;
		case 'THB' :
			$currency_symbol = '&#3647;';
			break;
		case 'GBP' :
			$currency_symbol = '&pound;';
			break;
		case 'RON' :
			$currency_symbol = 'lei';
			break;
		case 'VND' :
			$currency_symbol = '&#8363;';
			break;
		case 'NGN' :
			$currency_symbol = '&#8358;';
			break;
		case 'HRK' :
			$currency_symbol = 'Kn';
			break;
		case 'EGP' :
			$currency_symbol = 'EGP';
			break;
		case 'DOP' :
			$currency_symbol = 'RD&#36;';
			break;
		case 'KIP' :
			$currency_symbol = '&#8365;';
			break;
		default    :
			$currency_symbol = $currency;
			break;
	}

	return apply_filters( 'rct_currency_symbol', $currency_symbol, $currency );
}

/**
 * Convert rating value on 0-100 scale to the given rating type
 * scale, and vice versa.
 *
 * @since 1.0.0
 *
 * @param  mixed  $rating      Rating value on 0-100 scale.
 * @param  string $rating_type The rating type to which rating value needs to be converted.
 * @param  bool   $reverse     If true, perform the reverse i.e. retrieve rating value on 0-100 scale.
 *
 * @return float|int
 */
function rct_adjust_rating( $rating, $rating_type, $reverse = false ) {
	$scale = rct_get_rating_scale( $rating_type );

	if ( $reverse ) {
		return $rating * 100 / $scale['max'];
	}

	$rating = $rating * $scale['max'] / 100;
	if ( $rating >= $scale['min'] ) {
		return $rating;
	}

	return $scale['min'];
}

/**
 * Retrieves the nicely formatted price amount.
 *
 * @since   1.0.0
 *
 * @param   string $price Sanitized price amount to be formatted.
 *
 * @return  string Formatted price amount with currency symbol and thousands separator.
 */
function rct_format_price_amount( $price ) {
	if ( rct_is_empty( $price ) ) {
		return '';
	}

	$currency      = review_content_type()->settings->get( 'currency', 'currency' );
	$thousands_sep = apply_filters( 'rct_price_thousands_separator', ',' );
	$decimal_sep   = apply_filters( 'rct_price_decimal_separator', '.' );
	$decimals      = absint( apply_filters( 'rct_price_decimals_count', 2 ) );

	$price = number_format( $price, $decimals, $decimal_sep, $thousands_sep );

	switch ( review_content_type()->settings->get( 'currency_position', 'currency' ) ) {
		case 'before':
			$formatted = rct_get_currency_symbol( $currency ) . $price;
			break;
		case 'after':
		default:
			$formatted = $price . rct_get_currency_symbol( $currency );
	}

	return apply_filters( 'rct_formatted_price_amount', $formatted, $price, $decimals, $decimal_sep, $thousands_sep );
}

/**
 * Sanitize price amount by stripping out any invalid characters.
 *
 * @since   1.0.0
 *
 * @param   string $price Price amount to be sanitized.
 *
 * @return  string $price Sanitized price amount.
 */
function rct_sanitize_price_amount( $price ) {
	$price = preg_replace( '/[^0-9\.]/', '', $price );

	// Remove trailing zeros and everything after second decimal point.
	if ( '' !== $price ) {
		$price = (string) floatval( $price );
	}

	return apply_filters( 'rct_sanitize_price_amount', $price );
}

add_filter( 'rct_sanitize_review_data_min_price_field', 'rct_sanitize_price_amount', 10 );
add_filter( 'rct_sanitize_review_data_max_price_field', 'rct_sanitize_price_amount', 10 );


/**
 * Determine whether a variable is empty.
 *
 * The main difference between this and empty() is that 0 as a string
 * is not considered to be empty.
 *
 * @since   1.0.0
 *
 * @param   mixed $var
 *
 * @return  bool
 */
function rct_is_empty( $var ) {
	return ! ( isset( $var ) && ( '0' === $var || $var ) );
}

