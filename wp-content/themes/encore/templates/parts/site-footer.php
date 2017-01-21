<?php
/**
 * The template used for displaying the site footer.
 *
 * @package Encore
 * @since 1.0.0
 */
?>

<footer id="footer" class="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">

	<?php do_action( 'encore_footer_top' ); ?>

	<div class="credits">
		<?php encore_credits(); ?>
	</div>

	<?php do_action( 'encore_footer_bottom' ); ?>

</footer>
