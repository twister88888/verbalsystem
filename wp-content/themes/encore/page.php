<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default. Please note that
 * this is the WordPress construct of pages and that other 'pages' on your
 * WordPress site will use a different template.
 *
 * @package Encore
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="content-area" role="main" itemprop="mainContentOfPage">

	<?php do_action( 'encore_main_top' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'templates/parts/content', is_front_page() ? 'front-page' : 'page' ); ?>

		<?php comments_template( '', true ); ?>

	<?php endwhile; ?>

	<?php do_action( 'encore_main_bottom' ); ?>

</main>

<?php
get_footer();
