<?php
/**
 * Customizer integration.
 *
 * @package Encore
 * @since 1.0.0
 */

/**
 * Register and update Customizer settings.
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function encore_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->add_section( 'theme_options', array(
		'title'    => __( 'Theme Options', 'encore' ),
		'priority' => 120,
	) );
}
add_action( 'customize_register', 'encore_customize_register' );

/**
 * Bind JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since 1.0.0
 */
function encore_customize_preview_assets() {
	wp_enqueue_script(
		'encore-customize-preview',
		get_template_directory_uri() . '/assets/js/customize-preview.js',
		array( 'customize-preview', 'underscore', 'wp-util' ),
		'20150213',
		true
	);
}
add_action( 'customize_preview_init', 'encore_customize_preview_assets' );

/**
 * Print an Underscores template with CSS to generate based on options
 * selected in the Customizer.
 *
 * @since 1.0.0
 */
function encore_customize_styles_template() {
	if ( ! is_customize_preview() ) {
		return;
	}

	$css = <<<CSS
	.site-header .toggle-button,
	.site-header .toggle-button:focus,
	.site-header .toggle-button:hover {
		background-color: {{ data.backgroundColor }};
	}

	@media only screen and ( min-width: 960px ) {
		.offscreen-sidebar--header {
			background-color: {{ data.backgroundColor }};
		}
	}
CSS;

	printf( '<script type="text/html" id="tmpl-encore-customizer-styles">%s</script>', $css ); // XSS OK
}
add_action( 'wp_footer', 'encore_customize_styles_template' );
