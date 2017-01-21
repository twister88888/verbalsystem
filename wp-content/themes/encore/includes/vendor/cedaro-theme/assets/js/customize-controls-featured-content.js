/*global _:false, Backbone:false, wp:false */

(function( window, $, _, Backbone, wp, undefined ) {
	'use strict';

	var api = wp.customize,
		app = {};

	_.extend( app, { model: {}, view: {} } );

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */

	app.model.Post = Backbone.Model.extend({
		defaults: {
			title: '',
			order: 0
		}
	});

	app.model.Posts = Backbone.Collection.extend({
		model: app.model.Post,

		comparator: function( post ) {
			return parseInt( post.get( 'order' ), 10 );
		}
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	app.view.CustomizerControl = wp.Backbone.View.extend({
		initialize: function( options ) {
			this.l10n = options.data.l10n;
			this.modal = options.modal;
			this.setting = options.setting;

			this.listenTo( this.collection, 'add remove reset sort', this.updateSetting );
			this.render();
		},

		render: function() {
			var selector = '#' + this.$el.attr( 'id' );

			this.views.add([
				new app.view.PostList({
					collection: this.collection,
					parent: this
				}),

				new app.view.ControlActions({
					collection: this.collection,
					parent: this
				})
			]);

			return this;
		},

		updateSetting: function() {
			var postIds = this.collection.sort({ silent: true }).pluck( 'id' ).join( ',' );
			this.setting.set( postIds );
		}
	});

	app.view.ControlActions = wp.Backbone.View.extend({
		className: 'actions',
		tagName: 'div',

		events: {
			'click .button': 'openModal'
		},

		initialize: function( options ) {
			this.parent = options.parent;
		},

		render: function() {
			this.$el.html( wp.template( 'cedaro-theme-featured-content-button-add' )() );
			return this;
		},

		openModal: function( e ) {
			e.preventDefault();
			this.parent.modal.open();
		}
	});

	app.view.Modal = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'find-box',
		template: wp.template( 'cedaro-theme-featured-content-modal' ),

		events : {
			'click .ctfc-modal-search-button': 'send',
			'keypress .ctfc-modal-search :input': 'maybeSend',
			'click .js-select': 'select',
			'click .js-close': 'close',
			'keyup .ctfc-modal-search-field': 'escClose'
		},

		initialize: function( options ) {
			this.l10n = options.data.l10n;
			this.postTypes = options.data.postTypes;
			this.$overlay = false;

			this.render();
		},

		render: function() {
			this.$el.hide().html( this.template() );
			this.$field = this.$el.find( '.ctfc-modal-search-field' );
			this.$overlay = $( '.ui-find-overlay' );
			this.$response = this.$el.find( '.ctfc-modal-response' );
			this.$spinner = this.$el.find( '.ctfc-modal-search .spinner' );
			return this;
		},

		close: function() {
			this.$overlay.hide();
			this.$el.hide();
		},

		escClose: function( e ) {
			if ( e.which && 27 === e.which ) {
				this.close();
			}
		},

		open: function() {
			this.$response.html( '' );

			this.$el.show();

			this.$field.focus();

			if ( ! this.$overlay.length ) {
				$( '.wp-full-overlay' ).append( '<div class="ui-find-overlay"></div>' );
				this.$overlay  = $( '.ui-find-overlay' );
			}

			this.$overlay.show();

			// Pull some results up by default
			this.send();

			return false;
		},

		send: function() {
			var self = this;
			self.$spinner.show();

			wp.ajax.post( 'ctfc_find_posts', {
				s: self.$field.val(),
				post_types: this.postTypes,
				_ajax_nonce: $('#_ajax_nonce').val()
			}).always(function() {
				self.$spinner.hide();
			}).done(function( response ) {
				self.$response.html( response );
			}).fail(function() {
				self.$response.text( self.l10n.responseError );
			});
		},

		maybeSend: function( e ) {
			if ( 13 == e.which ) {
				this.send();
				return false;
			}
		},

		select: function( e ) {
			var posts;

			e.preventDefault();

			posts = this.$response.find( 'input[type="checkbox"]:checked' ).map(function() {
				var $this = $( this ),
					$row = $this.closest( 'tr' );

				return {
					id: this.value,
					title: $row.find( 'td:eq(1) label' ).text()
				};
			}).get();

			if ( posts.length ) {
				_.each( posts, function( post ) {
					this.collection.push( post );
				}, this );
			}

			this.close();
		}
	});

	app.view.PostList = wp.Backbone.View.extend({
		className: 'ctfc-posts-list',
		tagName: 'ol',

		initialize: function() {
			this.listenTo( this.collection, 'add', this.addPost );
			this.listenTo( this.collection, 'add remove', this.updateOrder );
			this.listenTo( this.collection, 'reset', this.render );

			this.render().$el.sortable({
				axis: 'y',
				delay: 150,
				forceHelperSize: true,
				forcePlaceholderSize: true,
				opacity: 0.6,
				start: function( e, ui ) {
					ui.placeholder.css( 'visibility', 'visible' );
				},
				update: _.bind(function( e, ui ) {
					this.updateOrder();
				}, this )
			});
		},

		render: function() {
			this.$el.empty();
			this.collection.each( this.addPost, this );
			return this;
		},

		addPost: function( post ) {
			var postView = new app.view.Post({ model: post });
			this.$el.append( postView.render().el );
		},

		updateOrder: function() {
			_.each( this.$el.find( 'li' ), function( item, i ) {
				var id = $( item ).data( 'post-id' );
				this.collection.get( id ).set( 'order', i );
			}, this );

			this.collection.sort();
		}
	});

	app.view.Post = wp.Backbone.View.extend({
		tagName: 'li',
		className: 'ctfc-post',
		template: wp.template( 'cedaro-theme-featured-content-post' ),

		events: {
			'click .js-toggle': 'toggleOpenStatus',
			'dblclick .ctfc-post-title': 'toggleOpenStatus',
			'click .js-close': 'minimize',
			'click .js-remove': 'destroy'
		},

		initialize: function() {
			this.listenTo( this.model, 'destroy', this.remove );
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) ).data( 'post-id', this.model.get( 'id' ) );
			return this;
		},

		minimize: function( e ) {
			e.preventDefault();
			this.$el.removeClass( 'is-open' );
		},

		toggleOpenStatus: function( e ) {
			e.preventDefault();
			this.$el.toggleClass( 'is-open' );
		},

		/**
		 * Destroy the view's model.
		 *
		 * Avoid syncing to the server by triggering an event instead of
		 * calling destroy() directly on the model.
		 */
		destroy: function() {
			this.model.trigger( 'destroy', this.model );
		},

		remove: function() {
			this.$el.remove();
		}
	});

	/**
	 * ========================================================================
	 * SETUP
	 * ========================================================================
	 */

	api.controlConstructor['cedaro-theme-featured-content'] = api.Control.extend({
		ready: function() {
			this.posts = new app.model.Posts( this.params.posts );
			delete this.params.posts;

			this.modal = new app.view.Modal({
				collection: this.posts,
				data: this.params
			});

			$( '.wp-full-overlay' ).append( this.modal.$el );

			this.view = new app.view.CustomizerControl({
				el: this.container,
				collection: this.posts,
				data: this.params,
				modal: this.modal,
				setting: this.setting
			});
		}
	});

})( window, jQuery, _, Backbone, wp );
