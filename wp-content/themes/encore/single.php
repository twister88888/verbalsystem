<?php
/**
 * The template for displaying individual posts.
 *
 * @package Encore
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="content-area" role="main" itemprop="mainContentOfPage" itemscope itemtype="http://schema.org/Blog">

	<?php do_action( 'encore_main_top' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'templates/parts/content', get_post_format() ); ?>

		<?php encore_content_navigation(); ?>

		<?php comments_template( '', true ); ?>

	<?php endwhile; ?>

	<?php do_action( 'encore_main_bottom' ); ?>

</main>

<?php
get_footer();
