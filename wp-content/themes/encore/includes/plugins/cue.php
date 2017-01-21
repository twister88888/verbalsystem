<?php
/**
 * Cue integration.
 *
 * @package Encore
 * @since 1.0.0
 * @link https://audiotheme.com/view/cue/
 */

/**
 * Register a Cue player.
 *
 * @since 1.0.0
 *
 * @param array $players List of players.
 */
function encore_register_cue_players( $players ) {
	$players['encore_player'] = __( 'Site Wide Player', 'encore' );
	return $players;
}
add_filter( 'cue_players', 'encore_register_cue_players' );

/**
 * Filter attachment IDs for the site-wide player.
 *
 * Returns tracks from the Cue playlist associated with the player location.
 *
 * @since 1.0.0
 *
 * @param array $track List of tracks.
 */
function encore_cue_player_tracks( $tracks ) {
	if ( function_exists( 'get_cue_player_tracks' ) ) {
		$tracks = get_cue_player_tracks( 'encore_player', array( 'context' => 'wp-playlist' ) );
	}

	return $tracks;
}
add_filter( 'pre_player_attachment_ids_tracks', 'encore_cue_player_tracks' );
