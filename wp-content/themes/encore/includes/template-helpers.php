<?php
/**
 * Helper methods for loading or displaying template partials.
 *
 * These are typically miscellaneous template parts used outside the loop.
 * Although if the partial requires any sort of set up or tearddown, moving that
 * logic into a helper keeps the parent template a little more lean, clean,
 * reusable and easier to override in child themes.
 *
 * Loading these partials within an action hook will allow them to be easily
 * added, removed, or reordered without changing the parent template file.
 *
 * Take a look at encore_register_template_parts() to see where most of these
 * are inserted.
 *
 * This approach tries to blend the two common approaches to theme development
 * (hooks or partials).
 *
 * @package Encore
 * @since 1.0.0
 */

/**
 * Display the featured image on the homepage.
 *
 * @since 1.0.0
 */
function encore_front_page_featured_image() {
	if ( ! ( is_front_page() && is_page() && has_post_thumbnail() ) ) {
		return;
	}

	the_post_thumbnail( 'encore-site-logo' );
}

/**
 * Load the site wide player template part.
 *
 * @since 1.0.0
 */
function encore_site_wide_player() {
	$settings = encore_get_player_settings();

	if ( empty( $settings['tracks'] ) ) {
		return;
	}

	wp_enqueue_script( 'encore-cue' );

	include( locate_template( 'templates/parts/player.php' ) );
}
