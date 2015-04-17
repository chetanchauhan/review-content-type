<div class="rct-review-pros-cons-wrap">
	<?php do_action( 'rct_before_pros_cons' ); ?>

	<?php if ( $pros = rct_get_review_pros( get_the_ID() ) ) : ?>
		<div class="rct-review-pros-wrap">
			<h3 class="rct-review-pros-heading"><?php rct_review_pros_heading( get_the_ID() ); ?></h3>
			<ul class="rct-review-pros">
				<?php foreach ( $pros as $pro ) : ?>
					<li><?php echo $pro; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( $cons = rct_get_review_cons( get_the_ID() ) ) : ?>
		<div class="rct-review-cons-wrap">
			<h3 class="rct-review-cons-heading"><?php rct_review_cons_heading( get_the_ID() ); ?></h3>
			<ul class="rct-review-cons">
				<?php foreach ( $cons as $con ) : ?>
					<li><?php echo $con; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php do_action( 'rct_after_pros_cons' ); ?>
</div>
