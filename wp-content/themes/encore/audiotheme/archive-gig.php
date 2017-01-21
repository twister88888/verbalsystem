<?php
/**
 * The template for displaying a list of gigs.
 *
 * @package Encore
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" <?php audiotheme_archive_class( array( 'content-area', 'archive-gig' ) ); ?> role="main" itemprop="mainContentOfPage">

	<?php do_action( 'encore_main_top' ); ?>

	<?php if ( have_posts() ) : ?>

		<?php get_template_part( 'audiotheme/gig/content', 'archive' ); ?>

	<?php else : ?>

		<?php get_template_part( 'audiotheme/gig/content', 'none' ); ?>

	<?php endif; ?>

	<?php do_action( 'encore_main_bottom' ); ?>

</main>

<?php
get_footer();
