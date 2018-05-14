<?php
$min_price = rct_get_min_price();
$max_price = rct_get_max_price();
if ( ! rct_is_empty( $max_price ) && ! rct_is_empty( $min_price ) ) { ?>

	<div class="rct-review-price" itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
		<span><?php echo rct_format_price_amount( $min_price ); ?></span> <?php esc_html_e( 'to', 'review-content-type' ); ?>
		<span><?php echo rct_format_price_amount( $max_price ); ?></span>
		<meta itemprop="lowPrice" content="<?php echo esc_attr( $min_price ); ?>">
		<meta itemprop="highPrice" content="<?php echo esc_attr( $max_price ); ?>">
		<meta itemprop="priceCurrency"
		      content="<?php echo esc_attr( review_content_type()->settings->get( 'currency', 'currency' ) ); ?>">
	</div>

<?php } elseif ( ! rct_is_empty( $min_price ) ) { ?>

	<div class="rct-review-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<span><?php echo rct_format_price_amount( $min_price ); ?></span>

		<meta itemprop="price" content="<?php echo esc_attr( $min_price ); ?>">
		<meta itemprop="priceCurrency"
		      content="<?php echo esc_attr( review_content_type()->settings->get( 'currency', 'currency' ) ); ?>">
	</div>

<?php } ?>
