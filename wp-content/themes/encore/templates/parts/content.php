<?php
/**
 * The template used for displaying content.
 *
 * @package Encore
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/BlogPosting" itemprop="blogPost">
	<header class="entry-header">
		<?php encore_entry_title(); ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-media">
			<?php the_post_thumbnail( 'encore-featured-image' ); ?>
		</figure>
	<?php endif; ?>

	<div class="entry-content" itemprop="text">
		<?php do_action( 'encore_entry_content_top' ); ?>
		<?php the_content(); ?>
		<?php encore_entry_terms(); ?>
		<?php encore_page_links(); ?>
		<?php do_action( 'encore_entry_content_bottom' ); ?>
	</div>

	<footer class="entry-footer">
		<div class="entry-meta">
			<?php encore_posted_on(); ?>
			<?php encore_posted_by(); ?>
			<?php encore_entry_comments_link(); ?>
		</div>
	</footer>
</article>
