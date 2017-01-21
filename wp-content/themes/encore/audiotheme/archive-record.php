<?php
/**
 * The template for displaying a record archives.
 *
 * @package Encore
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" <?php audiotheme_archive_class( array( 'content-area', 'archive-record' ) ); ?> role="main" itemprop="mainContentOfPage">

	<?php do_action( 'encore_main_top' ); ?>

	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<?php the_audiotheme_archive_title( '<h1 class="page-title screen-reader-text" itemprop="headline">', '</h1>' ); ?>
			<?php the_audiotheme_archive_description( '<div class="page-content">', '</div>' ); ?>
		</header>

		<div class="<?php encore_block_grid_classes(); ?>">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'audiotheme/record/content', 'archive' ); ?>
			<?php endwhile; ?>
		</div>

		<?php
		the_posts_navigation( array(
			'prev_text' => __( 'Next', 'encore' ),
			'next_text' => __( 'Previous', 'encore' ),
		) );
		?>

	<?php else : ?>

		<?php get_template_part( 'audiotheme/record/content', 'none' ); ?>

	<?php endif; ?>

	<?php do_action( 'encore_main_bottom' ); ?>

</main>

<?php
get_footer();
