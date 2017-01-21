<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package encore
 * @since 1.0.0
 */

if ( is_active_sidebar( 'sidebar-1' ) ) :
?>

	<div id="sidebar" class="offscreen-sidebar offscreen-sidebar--widgets widget-area" role="complementary" itemscope itemtype="http://schema.org/WPSideBar">

		<?php do_action( 'encore_sidebar_top' ); ?>

		<?php dynamic_sidebar( 'sidebar-1' ); ?>

		<?php do_action( 'encore_sidebar_bottom' ); ?>

	</div>

<?php
endif;
