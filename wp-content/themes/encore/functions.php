<?php
/**
 * Encore functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * see http://codex.wordpress.org/Plugin_API
 *
 * @package Encore
 * @since 1.0.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 800;
}

/**
 * Adjust the content width for full width pages.
 *
 * @since 1.0.0
 */
function encore_content_width() {
	global $content_width;

	if ( is_front_page() && is_page() ) {
		$content_width = 880;
	}
}
add_action( 'template_redirect', 'encore_content_width' );

/**
 * Load helper functions and libraries.
 */
require( get_template_directory() . '/includes/customizer.php' );
require( get_template_directory() . '/includes/hooks.php' );
require( get_template_directory() . '/includes/template-helpers.php' );
require( get_template_directory() . '/includes/template-tags.php' );
require( get_template_directory() . '/includes/vendor/cedaro-theme/autoload.php' );
encore_theme()->load();

/**
 * Set up theme defaults and register support for various WordPress features.
 *
 * @since 1.0.0
 */
function encore_setup() {
	// Add support for translating strings in this theme.
	// @link http://codex.wordpress.org/Function_Reference/load_theme_textdomain
	load_theme_textdomain( 'encore', get_template_directory() . '/languages' );

	// This theme styles the visual editor to resemble the theme style.
	add_editor_style( array(
		is_rtl() ? 'assets/css/editor-style-rtl.css' : 'assets/css/editor-style.css',
		encore_fonts_url(),
		encore_fonts_icon_url(),
	) );

	// Add support for default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Add support for the title tag.
	// @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	add_theme_support( 'title-tag' );

	// Add support for post thumbnails.
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'encore-block-grid-16x9', 425, 241, array( 'center', 'top' ) );
	add_image_size( 'encore-featured-image',  880, 500, true );
	add_image_size( 'encore-site-logo',       880, 640 ); // 2x max-height
	set_post_thumbnail_size(                  425, 425, array( 'center', 'top' ) );

	// Add support for a logo.
	add_theme_support( 'site-logo', array(
		'size' => 'encore-site-logo',
	) );

	// Add support for Custom Background functionality.
	add_theme_support( 'custom-background', array(
		'default-color' => 'f2efea',
	) );

	// Add HTML5 markup for the comment forms, search forms and comment lists.
	add_theme_support( 'html5', array(
		'caption', 'comment-form', 'comment-list', 'gallery', 'search-form',
	) );

	// Register default nav menus.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'encore' ),
		'social'  => __( 'Social Links Menu', 'encore' ),
	) );

	// Register support for archive content settings.
	encore_theme()->archive_content->add_support();
}
add_action( 'after_setup_theme', 'encore_setup' );

/**
 * Register widget area.
 *
 * @since 1.0.0
 */
function encore_register_sidebars() {
	register_sidebar( array(
		'id'            => 'sidebar-1',
		'name'          => __( 'Widget Area', 'encore' ),
		'description'   => __( 'Widgets appear in an offscreen container on the left side of every page.', 'encore' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'encore_register_sidebars' );

/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 */
function encore_enqueue_assets() {
	// Add Themicons font, used in the main stylesheet.
	wp_enqueue_style( 'themicons', encore_fonts_icon_url(), array(), '2.1.0' );

	// Load webfonts.
	wp_enqueue_style( 'encore-fonts', encore_fonts_url(), array(), null );

	// Load main style sheet.
	wp_enqueue_style( 'encore-style', get_stylesheet_uri() );

	// Load RTL style sheet.
	wp_style_add_data( 'encore-style', 'rtl', 'replace' );

	// Load theme scripts.
	wp_enqueue_script( 'encore-plugins', get_template_directory_uri() . '/assets/js/plugins.js', array( 'jquery' ), '20150410', true );
	wp_enqueue_script( 'encore', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery', 'encore-plugins', 'underscore' ), '20150401', true );

	// Localize the main theme script.
	wp_localize_script( 'encore', '_encoreSettings', array(
		'l10n' => array(
			'expand'         => '<span class="screen-reader-text">' . __( 'Expand', 'encore' ) . '</span>',
			'collapse'       => '<span class="screen-reader-text">' . __( 'Collapse', 'encore' ) . '</span>',
			'nextTrack'      => __( 'Next Track', 'encore' ),
			'previousTrack'  => __( 'Previous Track', 'encore' ),
			'togglePlaylist' => __( 'Toggle Playlist', 'encore' ),
		),
		'mejs' => array(
			'pluginPath' => includes_url( 'js/mediaelement/', 'relative' ),
		),
	) );

	// Load script to support comment threading when it's enabled.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Register scripts for enqueueing on demand.
	wp_register_script( 'encore-cue', get_template_directory_uri() . '/assets/js/vendor/jquery.cue.js', array( 'jquery', 'mediaelement' ), '1.1.3', true );
}
add_action( 'wp_enqueue_scripts', 'encore_enqueue_assets' );

/**
 * Print offscreen background color styles.
 *
 * @since 1.0.0
 */
function encore_offscreen_navigation_style() {
	$color = get_background_color();

	if ( empty( $color ) ) {
		return;
	}

	$css = <<<CSS
	.site-header .toggle-button,
	.site-header .toggle-button:focus,
	.site-header .toggle-button:hover {
		background-color: #{$color};
	}

	@media only screen and ( min-width: 960px ) {
		.offscreen-sidebar--header {
			background-color: #{$color};
		}
	}
CSS;

	wp_add_inline_style( 'encore-style', $css );
}
add_action( 'wp_enqueue_scripts', 'encore_offscreen_navigation_style' );

/**
 * JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since 1.0.0
 */
function encore_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'encore_javascript_detection', 0 );

/**
 * Add an HTML class to MediaElement.js container elements to aid styling.
 *
 * Extends the core _wpmejsSettings object to add a new feature via the
 * MediaElement.js plugin API.
 *
 * @since 1.0.0
 */
function encore_mejs_add_container_class() {
	if ( ! wp_script_is( 'mediaelement', 'done' ) ) {
		return;
	}
	?>
	<script>
	(function() {
		var settings = window._wpmejsSettings || {};
		settings.features = settings.features || mejs.MepDefaults.features;
		settings.features.push( 'encoreclass' );

		MediaElementPlayer.prototype.buildencoreclass = function( player ) {
			player.container.addClass( 'encore-mejs-container' );
		};
	})();
	</script>
	<?php
}
add_action( 'wp_print_footer_scripts', 'encore_mejs_add_container_class' );

/**
 * Return the Google font stylesheet URL, if available.
 *
 * The default Google font usage is localized. For languages that use characters
 * not supported by the font, the font can be disabled.
 *
 * @since 1.0.0
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function encore_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin';

	/*
	 * translators: If there are characters in your language that are not
	 * supported by these fonts, translate this to 'off'.
	 * Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Cousine: on or off', 'encore' ) ) {
		$fonts[] = 'Cousine:400,700,400italic,700italic';
	}

	if ( 'off' !== _x( 'on', 'Rajdhani: on or off', 'encore' ) ) {
		$fonts[] = 'Rajdhani:500,600';
	}

	/*
	 * translators: To add a character subset specific to your language,
	 * translate this to 'latin-ext', 'cyrillic', 'greek', or 'vietnamese'.
	 * Do not translate into your own language.
	 */
	$subset = _x( 'no-subset', 'Add new subset (latin-ext)', 'encore' );

	if ( 'latin-ext' === $subset ) {
		$subsets .= ',latin-ext';
	} elseif ( 'cyrillic' === $subset ) {
		$subsets .= ',cyrillic,cyrillic-ext';
	} elseif ( 'greek' === $subset ) {
		$subsets .= ',greek,greek-ext';
	} elseif ( 'vietnamese' === $subset ) {
		$subsets .= ',vietnamese';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => rawurlencode( implode( '|', $fonts ) ),
			'subset' => rawurlencode( $subsets ),
		), '//fonts.googleapis.com/css' );
	}

	return $fonts_url;
}

/**
 * Retrieve the icon font style sheet URL.
 *
 * @since 1.0.0
 *
 * @return string Font stylesheet.
 */
function encore_fonts_icon_url() {
	return get_template_directory_uri() . '/assets/css/themicons.css';
}

/**
 * Wrapper for accessing the Cedaro_Theme instance.
 *
 * @since 1.0.0
 *
 * @return Cedaro_Theme
 */
function encore_theme() {
	static $instance;

	if ( null === $instance ) {
		Cedaro_Theme_Autoloader::register();
		$instance = new Cedaro_Theme( array( 'prefix' => 'encore' ) );
	}

	return $instance;
}
function tweakjp_rm_comments_att( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
}
add_filter( 'comments_open', 'tweakjp_rm_comments_att', 10 , 2 );
//Remover el campo sitio web de los comentarios
function remover_campo_sitio_web_comentarios( $campo ){
	return false;
}
add_filter( 'comment_form_field_url', 'remover_campo_sitio_web_comentarios' );