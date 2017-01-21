<?php
/**
 * The template used for displaying content in page.php.
 *
 * @package Encore
 * @since 1.0.0
 */

if ( encore_has_content() ) :
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/CreativeWork">
		<header class="entry-header">
			<?php encore_entry_title(); ?>
		</header>

		<div class="entry-content" itemprop="text">
			<?php do_action( 'encore_entry_content_top' ); ?>
			<?php the_content(); ?>
			<?php do_action( 'encore_entry_content_bottom' ); ?>
		</div>
	</article>

<?php
endif;
