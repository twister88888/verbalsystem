/*global _:false, _encoreSettings:false, AudiothemeTracks:false */

window.cue = window.cue || {};
window.encore = window.encore || {};

(function( window, $, undefined ) {
	'use strict';

	var $window        = $( window ),
		$html          = $( 'html' ),
		$body          = $( 'body' ),
		$sidebarHeader = $( '.offscreen-sidebar--header' ),
		$socialMenu    = $( '.social-navigation .menu' ),
		$socialItems   = $socialMenu.find( 'li' ),
		$socialToggle  = $( '.social-navigation-toggle' ),
		l10n           = _encoreSettings.l10n,
		cue            = window.cue,
		encore         = window.encore;

	// Localize jquery.cue.js.
	cue.l10n = $.extend( cue.l10n, l10n );

	$.extend( encore, {
		config: {
			player: {
				cuePlaylistToggle: function( $tracklist ) {
					$tracklist.slideToggle( 200 );
				},
				cueSkin: 'encore-playbar',
				features: [
					'cuehistory',
					'cueartwork',
					'playpause',
					'cuecurrentdetails',
					'cueplaylist',
					'cueplaylisttoggle'
				],
				pluginPath: _encoreSettings.mejs.pluginPath
			},
			tracklist: {
				cueSkin: 'encore-tracklist',
				cueSelectors: {
					playlist: '.tracklist-area',
					track: '.track',
					trackCurrentTime: '.track-current-time',
					trackDuration: '.track-duration'
				},
				features: ['cueplaylist'],
				pluginPath: _encoreSettings.mejs.pluginPath
			}
		},

		init: function() {
			$body.addClass( 'ontouchstart' in window || 'onmsgesturechange' in window ? 'touch' : 'no-touch' );

			// Open external links in a new window.
			$( '.js-maybe-external' ).each(function() {
				if ( this.hostname && this.hostname !== window.location.hostname ) {
					$( this ).attr( 'target', '_blank' );
				}
			});

			// Open new windows for links with a class of '.js-popup'.
			$( '.js-popup' ).on( 'click', function( e ) {
				var $this       = $( this ),
					popupId     = $this.data( 'popup-id' ) || 'popup',
					popupUrl    = $this.data( 'popup-url' ) || $this.attr( 'href' ),
					popupWidth  = $this.data( 'popup-width' ) || 550,
					popupHeight = $this.data( 'popup-height' ) || 260;

				e.preventDefault();

				window.open( popupUrl, popupId, [
					'width=' + popupWidth,
					'height=' + popupHeight,
					'directories=no',
					'location=no',
					'menubar=no',
					'scrollbars=no',
					'status=no',
					'toolbar=no'
				].join( ',' ) );
			});

			// Move the footer widgets depending on the screen width.
			$( '.widget-area .widget' ).appendAround({
				set: $( '.widget-area' )
			});

			// Move the social navigation menu depending on the screen width.
			$( '.social-navigation .menu' ).appendAround({
				set: $( '.social-navigation' )
			});

			_.bindAll( this, 'onResize' );
			$window.on( 'load orientationchange resize', _.throttle( this.onResize, 100 ) );
		},

		/**
		 * Set up the main navigation.
		 */
		setupNavigation: function() {
			var theme = this,
				blurTimeout,
				$navigation = $( '.site-navigation' ),
				$menu = $navigation.find( '.menu' );

			// Append sub-menu toggle elements.
			$menu.find( 'ul' ).parent().children( 'a' ).append( '<button class="sub-menu-toggle"></button>' );

			// Toggle sub-menus.
			$menu.on( 'mouseenter', 'li', function() {
				$( this ).addClass( 'is-active' ).addClass(function() {
					return theme.isNavOffscreen() ? '' : 'is-sub-menu-open';
				});
			}).on( 'mouseleave', 'li', function() {
				$( this ).removeClass( 'is-active' ).removeClass(function() {
					return theme.isNavOffscreen() ? '' : 'is-sub-menu-open';
				});
			}).on( 'focus', 'a', function() {
				var $this = $( this ),
					$parents = $( this ).parents( 'li' );

				$parents.addClass( 'is-active' );

				// Open the top-level menu item when focused if the toggle button isn't visible.
				if ( $this.parent().hasClass( 'menu-item-has-children' ) && ! $this.children( '.sub-menu-toggle' ).is( ':visible' ) ) {
					$parents.last().addClass( 'is-sub-menu-open' );
				}
			}).on( 'blur', 'a', function() {
				clearTimeout( blurTimeout );

				// Hack to grab the activeElement after the blur event has been processed.
				blurTimeout = setTimeout(function() {
					var $parents = $( document.activeElement ).parents( 'li' );
					$menu.find( 'li' ).not( $parents ).removeClass(function() {
						return theme.isNavOffscreen() ? '' : 'is-sub-menu-open';
					});
				}, 1 );
			}).on( 'click', '.sub-menu-toggle', function( e ) {
				e.preventDefault();
				$( this ).closest( 'li' ).toggleClass( 'is-sub-menu-open' );
			});
		},

		/**
		 * Initialize the theme player and playlist.
		 */
		setupPlayer: function() {
			if ( $.isFunction( $.fn.cuePlaylist ) ) {
				$( '.encore-player' ).cuePlaylist( this.config.player );
			}
		},

		/**
		 * Set up the offscreen sidebar.
		 */
		setupSidebar: function() {
			var $toggle = $( '.offscreen-sidebar-toggle' ),
				$siteHeader = $( '#masthead' ),
				$siteOverlay = $siteHeader.append( '<div class="site-overlay" />' ).find( '.site-overlay' );

			function toggleSidebar( e ) {
				e.preventDefault();

				$body.toggleClass( 'offscreen-sidebar-is-open' );

				if ( $body.hasClass( 'offscreen-sidebar-is-open' ) ) {
					$html.css( 'overflow', 'hidden' );
				} else {
					$html.css( 'overflow', 'auto' );
				}
			}

			$toggle.on( 'click', toggleSidebar );
			$siteOverlay.on( 'click', toggleSidebar );
		},

		/**
		 * Set up the social navigation sidebar.
		 */
		setupSocialNavigation: function() {
			$socialToggle.on( 'click', function() {
				$body.toggleClass( 'social-navigation-is-open' );
			});
		},

		setupTracklist: function() {
			var $tracklist = $( '.tracklist-area' );

			if ( $tracklist.length && $.isFunction( $.fn.cuePlaylist ) ) {
				$tracklist.cuePlaylist( $.extend( this.config.tracklist, {
					cuePlaylistTracks: AudiothemeTracks.record
				}));
			}
		},

		/**
		 * Set up videos.
		 *
		 * - Makes videos responsive.
		 * - Moves videos embedded in page content to an '.entry-video'
		 *   container. Used primarily with the WPCOM single video templates.
		 */
		setupVideos: function() {
			if ( $.isFunction( $.fn.fitVids ) ) {
				$( '.hentry' ).fitVids();
			}

			$( 'body.page' ).find( '.single-video' ).find( '.jetpack-video-wrapper' ).first().appendTo( '.entry-video' );
		},

		onResize: function() {
			var itemCount, itemWidth,
				vw = this.viewportWidth();

			$body.css( 'padding-top', function() {
				return ( 960 <= vw ) ? $sidebarHeader.height() : '';
			});

			if ( 960 <= vw && ! $body.hasClass( '.has-widget-area' ) ) {
				$body.removeClass( 'offscreen-sidebar-is-open' );
			}

			if ( 960 <= vw ) {
				// Toggle the social toggle button.
				itemCount = $socialItems.length;
				itemWidth = $socialItems.slice( 0, 1 ).width();
				$socialToggle.toggle( $socialMenu.width() < itemWidth * itemCount );
			}
		},

		isNavOffscreen: function() {
			return 960 > this.viewportWidth();
		},

		viewportWidth: function() {
			return window.innerWidth || $window.width();
		}
	});

	// Document ready.
	jQuery(function() {
		encore.init();
		encore.setupNavigation();
		encore.setupPlayer();
		encore.setupSidebar();
		encore.setupSocialNavigation();
		encore.setupTracklist();
		encore.setupVideos();
	});

})( this, jQuery );
