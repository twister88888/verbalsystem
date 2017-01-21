<?php
/**
 * The template used for displaying content in page.php.
 *
 * @package Encore
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/CreativeWork">
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
		<?php encore_page_links(); ?>
		<?php do_action( 'encore_entry_content_bottom' ); ?>
	</div>
</article>
