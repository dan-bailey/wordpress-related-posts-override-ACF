<?php

get_header();

// summary shortener
	function ShortenSummary ($words, $excerptLength, $variance) {
		// make the maximum length a random number for more organic block lengths
		$maxLength = rand($excerptLength - $variance, $excerptLength + $variance);

		if (strlen($words) < $excerptLength) {
			$p = $words;
		} else {
			$sourceString = $words;
			$sourceString = substr($sourceString, 0, $maxLength);
			$cutpoint = strrpos($sourceString, " ");
			$sourceString = substr($sourceString, 0, $cutpoint);
			$sourceString = rtrim($sourceString, ".,?!;:");
			$sourceString = $sourceString . "...";
			$sourceString = str_replace('"', "'", $sourceString);

			$p = $sourceString;
		}
		return $p;
	 }

$all_tags = get_the_tags(get_the_ID());

$relatedArticles = get_posts([
	'category__in' => wp_get_post_categories(get_the_ID()),
	'post_type' => 'post',
	'numberposts' => 4,
	'suppress_filters' => 0,
	'post__not_in' => [get_the_ID()]
]);

$relatedArticles = array_map(function ($p) {
	$p->link = get_the_permalink($p->ID);
	$p->thumb = empty(get_the_post_thumbnail_url($p->ID)) ? false : get_the_post_thumbnail_url($p->ID);
	return $p;
}, $relatedArticles);
?>

<?php
// get the ACF fields, if any, and overwrite the $relatedArticles array fields, as necessary

if (get_field('related_articles_mode') == 'manual') {

	$related_posts = get_field('select_related_articles');
	$count_to = (count($related_posts));

	for ($x = 0; $x <= ($count_to - 1); $x++) {
		
		$nid = $related_posts[$x]['post_identity']->ID;

		// build WP Post Object
		$postx = new stdClass();
		$postx->ID = $nid;
		$postx->post_title = get_the_title($nid);
		$postx->thumb = get_the_post_thumbnail_url($nid);
		$postx->link = get_the_permalink($nid);
		$postx->filter = 'raw';

		$newEntry = new WP_Post ($postx);

		// inject Post Object to front of array
		array_unshift($relatedArticles, $newEntry);
	}

	// now reduce the complete array back to the first four items
	$aCount = count($relatedArticles);
	$relatedArticles = array_slice ($relatedArticles, 0, 4, true );
}
?>


<article class="single-post__article" role="article" id="<?= 'article-' . get_the_ID() ?>">
	<div class="container">
		<header>
			<h1><?php the_title() ?></h1>
			<p class="summary"><?= get_field('summary') ?></p>
		</header>
		<main>
			<aside>
				<h4><?php _e('Article Information', 'example-translate-name') ?></h4>
				<?php if ($all_tags) : ?>
					<p class="tags-list"><?= __('Tags:', 'example-translate-name') . ' ' . implode(', ', array_map(function ($t) {
							return "<a href='" . get_term_link($t->term_id) . "'>" . $t->name . "</a>";
						}, $all_tags)) ?></p>

				<?php endif; ?>

				<hr>
                <div>
                    <?php _e(get_field('khub_first_cta', 'option'), 'example-translate-name'); ?>
                </div>
                <div>
                    <?php _e(get_field('khub_second_cta', 'option'), 'example-translate-name'); ?>
                </div>
			</aside>
			<div class="content">
				<?php the_content(); ?>
			</div>
		</main>

		<hr>

		<div class="related-articles">
			<h2><?php _e('Related Articles', 'example-translate-name') ?></h2>
			<div class="flexgrid">
				<?php foreach ($relatedArticles as $p) : ?>
					<div class="card flexgrid__item">
							<div class="card__inner">
								<a href="<?php echo $p->link ?>" class="card__link">
										<?php if ($p->thumb) : ?>
											<div class="image" style="background-image:url(<?= $p->thumb ?>);"></div>
										<?php endif; ?>
										<div class="content">
											<h3><?= $p->post_title ?></h3>
											
											<?php // excerpt
												$the_excerpt = get_field( 'summary', $p->ID );
												if( $the_excerpt ) :
											?>

											<!-- excerpt -->
											<div class="content__excerpt"><?php 
												$temp = sanitize_text_field( $the_excerpt );
												echo ShortenSummary ($temp, 125, 5); 
											?></div>

											<?php endif; ?>
											<span class="button button--primary"><?php _e('Learn more', 'example-translate-name') ?></span>
										</div>
								</a>
							</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</article>

<?php

get_footer();

?>