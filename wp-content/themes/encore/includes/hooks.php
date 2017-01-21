<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * @package Encore
 * @since 1.0.0
 */

/**
 * Register template parts to load throughout the theme.
 *
 * Take note of the priorities. Changing them will allow template parts to be
 * loaded in a different order.
 *
 * To remove any of these parts, use remove_action() in the
 * "encore_register_template_parts" hook or later.
 *
 * @since 1.0.0
 */
function encore_register_template_parts() {
	add_action( 'encore_branding_after', 'encore_front_page_featured_image' );
	add_action( 'encore_header_after', 'encore_site_wide_player' );

	do_action( 'encore_register_template_parts' );
}
add_action( 'template_redirect', 'encore_register_template_parts', 9 );

/**
 * Add classes to the 'body' element.
 *
 * @since 1.0.0
 *
 * @param array $classes Default classes.
 * @return array
 */
function encore_body_class( $classes ) {
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'has-widget-area';
	}

	return array_unique( $classes );
}
add_filter( 'body_class', 'encore_body_class' );

/**
 * Add classes to the post element.
 *
 * @since 1.0.0
 *
 * @param array $classes Default classes.
 * @return array
 */
function encore_post_class( $classes ) {
	if ( apply_filters( 'encore_content_box_post_class', true ) ) {
		$classes[] = 'content-box';
	}

	return array_unique( $classes );
}
add_filter( 'post_class', 'encore_post_class' );

/**
 * Disable the content-box class on the front page when set to show a page.
 *
 * @since 1.0.0
 *
 * @param array $bool Add class.
 * @return array
 */
function encore_front_page_content_box_post_class( $bool ) {
	if ( is_front_page() && is_page() ) {
		$bool = false;
	}

	return $bool;
}
add_filter( 'encore_content_box_post_class', 'encore_front_page_content_box_post_class' );
