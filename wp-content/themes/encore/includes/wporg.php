<?php
/**
 * Functionality specific to self-hosted installations of WordPress, including
 * support for plugins.
 *
 * @package Encore
 * @since 1.0.0
 */

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 1.0.0
 */
function encore_wporg_enqueue_assets() {
	wp_enqueue_script( 'jquery-fitvids', get_template_directory_uri() . '/assets/js/vendor/jquery.fitvids.js', array( 'jquery' ), '1.1', true );
}
add_action( 'wp_enqueue_scripts', 'encore_wporg_enqueue_assets' );


/*
 * Plugin support.
 * -----------------------------------------------------------------------------
 */

/**
 * Load AudioTheme support or display a notice that it's needed.
 */
if ( function_exists( 'audiotheme_load' ) ) {
	include( get_template_directory() . '/includes/plugins/audiotheme.php' );
} else {
	include( get_template_directory() . '/includes/vendor/class-audiotheme-themenotice.php' );
	new Audiotheme_ThemeNotice();
}

/**
 * Load Cue support.
 */
if ( class_exists( 'Cue' ) ) {
	include( get_template_directory() . '/includes/plugins/cue.php' );
}

/**
 * Load Jetpack support.
 */
if ( class_exists( 'Jetpack' ) ) {
	include( get_template_directory() . '/includes/plugins/jetpack.php' );
}

/**
 * Load WooCommerce support.
 */
if ( class_exists( 'WooCommerce' ) ) {
	include( get_template_directory() . '/includes/plugins/woocommerce.php' );
}


/*
 * Theme upgrade methods.
 * -----------------------------------------------------------------------------
 */

/**
 * Upgrade theme data after an update.
 *
 * @since 1.0.0
 */
function encore_wporg_upgrade() {
	$previous_version = get_theme_mod( 'theme_version', '0' );
	$current_version  = wp_get_theme()->get( 'Version' );

	if ( version_compare( $previous_version, '1.0.0', '<' ) ) {
		encore_wporg_upgrade_100();
	}

	// Update the theme mod if the version is outdated.
	if ( '0' === $previous_version || version_compare( $previous_version, $current_version, '<' ) ) {
		set_theme_mod( 'theme_version', $current_version );
	}
}
add_action( 'admin_init', 'encore_wporg_upgrade' );

/**
 * Migrate data from Shaken Encore.
 *
 * @since 1.0.0
 */
function encore_wporg_upgrade_100() {
	$shaken_options     = get_option( 'shaken_options', array() );
	$shaken_theme_mods  = get_option( 'theme_mods_shaken-encore', array() );

	// Copy background theme mods from Shaken Encore.
	foreach ( array( 'color', 'image', 'repeat', 'position_x', 'attachment' ) as $key ) {
		$name = 'background_' . $key;

		if ( ! isset( $shaken_theme_mods[ $name ] ) ) {
			continue;
		}

		set_theme_mod( $name, sanitize_text_field( $shaken_theme_mods[ $name ] ) );
	}

	// Copy the primary menu from Shaken Encore.
	if ( isset( $shaken_theme_mods['nav_menu_locations']['main_menu'] ) ) {
		$menus = get_theme_mod( 'nav_menu_locations', array() );
		$menus['primary'] = absint( $shaken_theme_mods['nav_menu_locations']['main_menu'] );
		set_theme_mod( 'nav_menu_locations', $menus );
	}

	// Copy the logo from Shaken Encore.
	if ( ! empty( $shaken_options['logo'] ) ) {
		$logo_url      = $shaken_options['logo'];
		$attachment_id = encore_get_attachment_id_by_url( $logo_url );
		$site_logo     = get_option( 'site_logo', array() );

		// Only update data if the old logo attachment can be found and
		// a site logo option hasn't been set.
		if ( $attachment_id && ( empty( $site_logo ) || empty( $site_logo['url'] ) ) ) {
			$attachment_data = wp_prepare_attachment_for_js( $attachment_id );

			set_theme_mod( 'cedaro_site_logo_url', esc_url_raw( $logo_url ) );

			// Update the site logo option.
			$site_logo = array_intersect_key( $attachment_data, array_flip( array( 'id', 'sizes', 'url' ) ) );
			update_option( 'site_logo', $site_logo );
		}
	}
}

/**
 * Return an ID of an attachment by searching the database with the file URL.
 *
 * First checks to see if the $url is pointing to a file that exists in the
 * wp-content directory. If so, then we search the database for a partial match
 * consisting of the remaining path AFTER the wp-content directory. Finally, if
 * a match is found the attachment ID will be returned.
 *
 * @link http://frankiejarrett.com/get-an-attachment-id-by-url-in-wordpress/
 * @link https://core.trac.wordpress.org/ticket/16830
 * @todo https://core.trac.wordpress.org/changeset/24240
 *
 * @param string $url URL.
 * @return int Attachment ID.
 */
function encore_get_attachment_id_by_url( $url ) {
	global $wpdb;

	// Split the $url into two parts with the wp-content directory as the separator.
	$parse_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

	// Get the host of the current site and the host of the $url, ignoring www.
	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

	// Return nothing if there aren't any $url parts or if the current host and $url host do not match.
	if ( ! isset( $parse_url[1] ) || empty( $parse_url[1] ) || ( $this_host !== $file_host ) ) {
		return null;
	}

	// Search the DB for an attachment GUID with a partial path match.
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='attachment' AND guid RLIKE %s", $parse_url[1] ) );

	// Returns null if no attachment is found.
	return $attachment[0];
}
