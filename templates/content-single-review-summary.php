<?php if ( $summary = rct_get_review_summary( get_the_ID() ) ) : ?>
	<div class="rct-review-summary-wrap">
		<h3 class="rct-review-summary-heading"><?php rct_review_summary_heading( get_the_ID() ); ?></h3>

		<div class="rct-review-summary">
			<?php echo $summary; ?>
		</div>
	</div>
<?php endif; ?>
