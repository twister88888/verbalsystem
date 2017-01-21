<?php
/**
 * The template used for displaying search results.
 *
 * @package Encore
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="content-area" role="main" itemprop="mainContentOfPage" itemscope itemtype="http://schema.org/SearchResultsPage">

	<?php do_action( 'encore_main_top' ); ?>

	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'encore' ), get_search_query() ); ?></h1>
		</header>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'templates/parts/content', 'search' ); ?>

		<?php endwhile; ?>

		<?php encore_content_navigation(); ?>

	<?php else : ?>

		<?php get_template_part( 'templates/parts/content-none', 'index' ); ?>

	<?php endif; ?>

	<?php do_action( 'encore_main_bottom' ); ?>

</main>

<?php
get_footer();
