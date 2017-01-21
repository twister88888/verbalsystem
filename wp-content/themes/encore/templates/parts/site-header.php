<?php
/**
 * The template used for displaying the site header.
 *
 * @package Encore
 * @since 1.0.0
 */
?>

<header id="masthead" class="site-header" role="banner" itemscope itemtype="http://schema.org/WPHeader">

	<?php do_action( 'encore_header_top' ); ?>

	<?php encore_site_branding(); ?>

	<?php do_action( 'encore_branding_after' ); ?>

	<button class="offscreen-sidebar-toggle toggle-button">
		<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'encore' ); ?></span>
	</button>

	<div class="offscreen-sidebar offscreen-sidebar--header">
		<nav id="site-navigation" class="site-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'menu',
				'fallback_cb'    => false,
				'depth'          => 3,
			) );
			?>
		</nav>

		<nav class="social-navigation"></nav>
		<div class="widget-area"></div>
	</div>

	<?php if ( has_nav_menu( 'social' ) ) : ?>
		<button class="social-navigation-toggle toggle-button">
			<span class="screen-reader-text"><?php esc_html_e( 'Social Links', 'encore' ); ?></span>
		</button>

		<nav class="social-navigation" role="navigation">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'social',
				'container'      => false,
				'depth'          => 1,
				'link_before'    => '<span class="screen-reader-text">',
				'link_after'     => '</span>',
			) );
			?>
		</nav>
	<?php endif; ?>

	<?php do_action( 'encore_header_bottom' ); ?>

</header>
