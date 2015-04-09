<div itemscope itemtype="http://schema.org/Review">

	<?php do_action( 'rct_before_review_content' ); ?>

	<div class="rct-review-content" itemprop="reviewBody">
		<?php the_content(); ?>
	</div>

	<?php do_action( 'rct_after_review_content' ); ?>

	<meta itemprop="datePublished" content="<?php the_time( 'Y-m-d', get_the_ID() ) ?>">
	<meta itemprop="dateModified" content="<?php the_modified_time( 'Y-m-d' ) ?>">
	<span itemprop="author" itemscope itemtype="http://schema.org/Person">
		<meta itemprop="name" content="<?php the_author() ?>">
	</span>

</div>
