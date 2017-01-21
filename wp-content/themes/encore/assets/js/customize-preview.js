/*global wp:false */

/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make the Customizer preview load changes asynchronously.
 */

(function( $, wp ) {
	'use strict';

	var api = wp.customize,
		$siteDescription = $( '.site-description' ),
		$siteTitle = $( '.site-title a' ),
		stylesTemplate = wp.template( 'encore-customizer-styles' ),
		$styles = $( '#encore-custom-css' );

	if ( ! $styles.length ) {
		$styles = $( 'head' ).append( '<style type="text/css" id="encore-custom-css"></style>' )
		                     .find( '#encore-custom-css' );
	}

	// Site title.
	api( 'blogname', function( value ) {
		value.bind(function( to ) {
			$siteTitle.text( to );
		});
	});

	// Site description.
	api( 'blogdescription', function( value ) {
		value.bind(function( to ) {
			$siteDescription.text( to );
		});
	});

	// Site navigation background color.
	api( 'background_color', function( value ) {
		value.bind(function( to ) {
			updateCSS();
		});
	});

	function updateCSS() {
		var css = stylesTemplate({
			backgroundColor: api( 'background_color' )()
		});

		$styles.html( css );
	}

})( jQuery, wp );
