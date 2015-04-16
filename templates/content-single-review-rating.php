<div class="rct-review-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
	<span class="rct-review-rating-heading"><?php _e( 'Editor\'s Rating:', 'review-content-type' ); ?></span>
	<?php $rating = rct_get_review_rating(); ?>
	<?php rct_rating_html( $rating, rct_get_rating_type() ); ?>
	<meta itemprop="worstRating" content="0">
	<meta itemprop="ratingValue" content="<?php echo esc_attr( $rating ); ?>">
	<meta itemprop="bestRating" content="100">
</div>
