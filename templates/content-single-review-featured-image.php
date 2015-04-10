<div class="rct-featured-image">
	<?php
	do_action( 'rct_before_featured_image' );

	if ( $url = rct_get_featured_image_url() ) {
		printf( '<a href="%s" title="%s" rel="nofollow">%s</a>', esc_url( $url ), esc_attr( rct_get_reviewed_item_name() ), rct_get_featured_image() );
	} else {
		echo rct_get_featured_image();
	}

	do_action( 'rct_after_featured_image' );
	?>
</div>
