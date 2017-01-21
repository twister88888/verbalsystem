<?php
/**
 * AudioTheme integration.
 *
 * @package Encore
 * @since 1.0.0
 * @link https://audiotheme.com/
 */

/**
 * Set up theme defaults and register support for AudioTheme features.
 *
 * @since 1.0.0
 */
function encore_audiotheme_setup() {
	// Add AudioTheme automatic updates support
	add_theme_support( 'audiotheme-automatic-updates' );

	// Add support for AudioTheme widgets.
	add_theme_support( 'audiotheme-widgets', array(
		'record', 'track', 'upcoming-gigs', 'video',
	) );

	// Remove AudiotTheme video wrappers
	remove_filter( 'embed_oembed_html', 'audiotheme_oembed_html', 10 );
	remove_filter( 'embed_handler_html', 'audiotheme_oembed_html', 10 );
}
add_action( 'after_setup_theme', 'encore_audiotheme_setup', 11 );

/**
 * Load required scripts for AudioTheme support.
 *
 * @since 1.0.0
 */
function encore_audiotheme_enqueue_assets() {
	if ( in_array( get_post_type(), array( 'audiotheme_record', 'audiotheme_track' ) ) ) {
		wp_enqueue_script( 'encore-cue' );
	}
}
add_action( 'wp_enqueue_scripts', 'encore_audiotheme_enqueue_assets', 20 );


/*
 * Plugin hooks.
 * -----------------------------------------------------------------------------
 */

/**
 * Activate default archive setting fields.
 *
 * @since 1.0.0
 *
 * @param array $fields List of default fields to activate.
 * @param string $post_type Post type archive.
 * @return array
 */
function encore_audiotheme_archive_settings_fields( $fields, $post_type ) {
	if ( ! in_array( $post_type, array( 'audiotheme_record', 'audiotheme_video' ) ) ) {
		return $fields;
	}

	$fields['columns'] = array(
		'choices' => range( 2, 3 ),
		'default' => 2,
	);

	$fields['posts_per_archive_page'] = true;

	return $fields;
}
add_filter( 'audiotheme_archive_settings_fields', 'encore_audiotheme_archive_settings_fields', 10, 2 );

/**
 * Disable Jetpack Infinite Scroll on AudioTheme post types.
 *
 * @since 1.0.0
 *
 * @param bool $supported Whether Infinite Scroll is supported for the current request.
 * @return bool
 */
function encore_audiotheme_infinite_scroll_archive_supported( $supported ) {
	$post_type = get_post_type() ? get_post_type() : get_query_var( 'post_type' );

	if ( $post_type && false !== strpos( $post_type, 'audiotheme_' ) ) {
		$supported = false;
	}

	return $supported;
}
add_filter( 'infinite_scroll_archive_supported', 'encore_audiotheme_infinite_scroll_archive_supported' );


/*
 * Theme hooks.
 * -----------------------------------------------------------------------------
 */

/**
 * Add classes to archive block grids.
 *
 * @since 1.0.0
 *
 * @param array $classes List of HTML classes.
 * @return array
 */
function encore_audiotheme_archive_block_grid_classes( $classes ) {
	if (
		is_post_type_archive( 'audiotheme_record' ) ||
		is_tax( 'audiotheme_record_type' )
	) {
		$classes[] = 'block-grid-' . get_audiotheme_archive_meta( 'columns', true, 2 );
	}

	if (
		is_post_type_archive( 'audiotheme_video' ) ||
		is_tax( 'audiotheme_video_category' )
	) {
		$classes[] = 'block-grid-' . get_audiotheme_archive_meta( 'columns', true, 2 );
		$classes[] = 'block-grid--16x9';
	}

	return $classes;
}
add_filter( 'encore_block_grid_classes', 'encore_audiotheme_archive_block_grid_classes' );

/**
 * Disable the 'content-box' post class on AudioTheme archives.
 *
 * @since 1.0.0
 *
 * @param array $bool Add class.
 * @return array
 */
function encore_audiotheme_content_box_post_class( $bool ) {
	if (
		is_audiotheme_post_type_archive() ||
		is_tax( array( 'audiotheme_record_type', 'audiotheme_video_category' ) )
	) {
		$bool = false;
	}

	return $bool;
}
add_filter( 'encore_content_box_post_class', 'encore_audiotheme_content_box_post_class' );

/**
 * Return a set of recent gigs.
 *
 * @since  1.0.0
 */
function encore_audiotheme_recent_gigs_query() {
	$args = array(
		'order'          => 'desc',
		'posts_per_page' => 5,
		'meta_query'     => array(
			array(
				'key'     => '_audiotheme_gig_datetime',
				'value'   => current_time( 'mysql' ),
				'compare' => '<=',
				'type'    => 'DATETIME',
			),
		),
	);

	return new Audiotheme_Gig_Query( apply_filters( 'encore_recent_gigs_query_args', $args ) );
}

/**
 * Display a track's duration.
 *
 * @since 1.0.0
 *
 * @param int $track_id Track ID.
 */
function encore_audiotheme_track_length( $track_id = 0 ) {
	$track_id = empty( $track_id ) ? get_the_ID() : $track_id;
	$length   = get_audiotheme_track_length( $track_id );

	if ( empty( $length ) ) {
		$length = _x( '-:--', 'default track length', 'encore' );
	}

	echo esc_html( $length );
}
