<?php
/**
 * Featured content feature.
 *
 * @since 3.1.0
 *
 * @package Cedaro\Theme
 * @copyright Copyright (c) 2014, Cedaro
 * @license GPL-2.0+
 */

/**
 * Class for the featured content feature.
 *
 * @package Cedaro\Theme
 * @since 3.1.0
 */
class Cedaro_Theme_FeaturedContent {
	/**
	 * Maximum number of posts to feature.
	 *
	 * @since 3.1.0
	 * @type int
	 */
	protected $max_posts = 15;

	/**
	 * Post types that can be featured.
	 *
	 * @since 3.1.0
	 * @type array
	 */
	protected $post_types = array( 'post', 'page' );

	/**
	 * The theme object.
	 *
	 * @since 3.1.0
	 * @type Cedaro_Theme
	 */
	protected $theme;

	/**
	 * Constructor method.
	 *
	 * @since 3.1.0
	 *
	 * @param Cedoro_Theme $theme Cedaro theme instance.
	 */
	public function __construct( Cedaro_Theme $theme ) {
		$this->theme = $theme;
	}

	/*
	 * Public API methods.
	 */

	/**
	 * Wire up theme hooks for supporting featured content.
	 *
	 * @since 3.1.0
	 */
	public function add_support() {
		add_action( 'wp_ajax_ctfc_find_posts', array( $this, 'ajax_find_posts' ) );
		add_action( 'pre_get_posts', array( $this, 'exclude_featured_posts' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_controls_assets' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_templates' ) );
		return $this;
	}

	/**
	 * Register post types that can be featured.
	 *
	 * @since 3.1.0
	 *
	 * @param array|string $post_types Post types.
	 */
	public function add_post_types( $post_types ) {
		$this->post_types = array_merge( $this->post_types, (array) $post_types );
		return $this;
	}

	/**
	 * Maxmium number of posts to feature.
	 *
	 * @since 3.1.0
	 *
	 * @param int $count Number of posts to feature.
	 */
	public function set_max_posts( $count ) {
		$this->max_posts = absint( $count );
		return $this;
	}

	/**
	 * Retrieve featured posts.
	 *
	 * @since 3.1.0
	 *
	 * @todo Cache this.
	 *
	 * @return array An array of WP_Post objects.
	 */
	public function get_posts() {
		$featured_posts = array();
		$post_ids       = get_theme_mod( 'featured_content_ids', array() );

		$args = apply_filters( $this->theme->prefix . '_featured_content_args', array(
			'posts_per_page' => $this->max_posts,
			'post_type'      => $this->post_types,
			'orderby'        => 'post__in',
		) );

		if ( $post_ids ) {
			$args['post__in'] = array_map( 'absint', explode( ',', $post_ids ) );
			$featured_posts   = get_posts( $args );
		}

		return apply_filters( $this->theme->prefix . '_featured_posts', $featured_posts, $args );
	}

	/*
	 * Hook callbacks.
	 */

	/**
	 * Exclude featured posts from the blog query when the blog is the front-page.
	 *
	 * Filter the home page posts, and remove any featured post ID's from it.
	 * Hooked onto the 'pre_get_posts' action, this changes the parameters of the
	 * query before it retrieves any posts.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_Query $query
	 * @return WP_Query Possibly modified WP_Query
	 */
	public function exclude_featured_posts( $wp_query ) {
		// Bail if not home or not main query.
		if ( ! $wp_query->is_home() || ! $wp_query->is_main_query() ) {
			return;
		}

		$page_on_front = get_option( 'page_on_front' );

		// Bail if the blog page is not the front page.
		if ( ! empty( $page_on_front ) ) {
			return;
		}

		$featured = $this->get_posts();

		// Bail if no featured posts.
		if ( empty( $featured ) ) {
			return;
		}

		$featured = wp_list_pluck( (array) $featured, 'ID' );
		$featured = array_map( 'absint', $featured );

		// We need to respect post ids already in the blacklist.
		$post__not_in = $wp_query->get( 'post__not_in' );

		if ( ! empty( $post__not_in ) ) {
			$featured = array_merge( (array) $post__not_in, $featured );
			$featured = array_unique( $featured );
		}

		$wp_query->set( 'post__not_in', $featured );
	}

	/**
	 * Register Customizer settings and controls.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 */
	public function customize_register( $wp_customize ) {
		$wp_customize->add_section( 'featured_content', array(
			'title'       => __( 'Featured Content', 'encore' ),
			'description' => sprintf( __( 'Your theme supports up to %d posts in its featured content area.', 'encore' ), $this->max_posts ),
			'priority'    => 130,
		) );

		$wp_customize->add_setting( 'featured_content_ids', array(
			'sanitize_callback' => array( $this, 'sanitize_id_list' ),
		) );

		$wp_customize->add_control( new Cedaro_Theme_Customize_Control_FeaturedContent( $wp_customize, 'cedaro_featured_content', array(
			'label'       => '', //__( 'Featured Content', 'cedaro-theme' ),
			'section'     => 'featured_content',
			'settings'    => 'featured_content_ids',
			'post_types'  => $this->post_types,
		) ) );
	}

	/**
	 * Enqueue scripts to display in the Customizer preview.
	 *
	 * @since 3.1.0
	 */
	public function enqueue_customizer_controls_assets() {
		wp_enqueue_script(
			'cedaro-theme-customize-controls-featured-content',
			$this->theme->get_library_uri( 'assets/js/customize-controls-featured-content.js' ),
			array( 'customize-controls', 'wp-backbone' ),
			'1.0.0',
			true
		);

		wp_enqueue_style(
			'cedaro-theme-customize-controls-featured-content',
			$this->theme->get_library_uri( 'assets/css/customize-controls-featured-content.css' ),
			array( 'dashicons' )
		);
	}

	/**
	 * Print JavaScript templates in the Customizer footer.
	 *
	 * @since 3.1.0
	 */
	public function print_templates() {
		?>
		<script type="text/html" id="tmpl-cedaro-theme-featured-content-button-add">
			<button class="button button-secondary alignright"><?php esc_html_e( 'Add Posts', 'encore' ); ?></button>
		</script>

		<script type="text/html" id="tmpl-cedaro-theme-featured-content-post">
			<h4 class="ctfc-post-title"><span class="text">{{{ data.title }}}</span> <i class="ctfc-post-toggle js-toggle"></i></h4>

			<div class="ctfc-post-inside">
				<div class="ctfc-post-actions">
					<a class="ctfc-post-remove js-remove"><?php esc_html_e( 'Delete', 'encore' ); ?></a> |
					<a class="js-close"><?php esc_html_e( 'Close', 'encore' ); ?></a>
				</div>
			</div>
		</script>

		<script type="text/html" id="tmpl-cedaro-theme-featured-content-modal">
			<div class="ctfc-modal-head find-box-head">
				<?php esc_html_e( 'Find Posts', 'encore' ); ?>
				<div class="ctfc-modal-close js-close"></div>
			</div>
			<div class="ctfc-modal-inside find-box-inside">
				<div class="ctfc-modal-search find-box-search">
					<?php wp_nonce_field( 'find-posts', '_ajax_nonce', false ); ?>
					<input type="text" name="s" value="" class="ctfc-modal-search-field">
					<span class="spinner"></span>
					<input type="button" value="<?php esc_attr_e( 'Search', 'encore' ); ?>" class="button ctfc-modal-search-button" />
					<div class="clear"></div>
				</div>
				<div class="ctfc-modal-response"></div>
			</div>
			<div class="ctfc-modal-buttons find-box-buttons">
				<button class="button button-primary alignright js-select">Select</button>
			</div>
		</script>
		<?php
	}

	/**
	 * Ajax handler for finding posts.
	 *
	 * @since 3.1.0
	 *
	 * @see wp_ajax_find_posts()
	 */
	public function ajax_find_posts() {
		check_ajax_referer( 'find-posts' );

		$post_types = array();

		if ( ! empty( $_POST['post_types'] ) ) {
			foreach ( $_POST['post_types'] as $post_type ) {
				$post_types[ $post_type ] = get_post_type_object( $post_type );
			}
		}

		if ( empty( $post_types ) ) {
			$post_types['post'] = get_post_type_object( 'post' );
		}

		$args = array(
			'post_type'      => array_keys( $post_types ),
			'post_status'    => 'any',
			'posts_per_page' => 50,
		);

		if ( ! empty( $_POST['s'] ) ) {
			$args['s'] = wp_unslash( $_POST['s'] );
		}

		$posts = get_posts( $args );

		if ( ! $posts ) {
			wp_send_json_error( __( 'No items found.', 'encore' ) );
		}

		$html = $this->get_found_posts_html( $posts );

		wp_send_json_success( $html );
	}

	/**
	 * Sanitization callback for lists of IDs in the Customizer.
	 *
	 * @since 3.1.0
	 *
	 * @param string $value Setting value.
	 * @return string Comma-separated list of IDs.
	 */
	public function sanitize_id_list( $value ) {
		$value = implode( ',', wp_parse_id_list( $value ) );
		return ( '0' == $value ) ? '' : $value;
	}

	/**
	 * Retrieve HTML for displaying a list of posts.
	 *
	 * @since 3.1.0
	 *
	 * @param array $posts Array of post objects.
	 * @return string
	 */
	protected function get_found_posts_html( $posts ) {
		$alt  = '';
		$html = sprintf(
			'<table class="widefat"><thead><tr><th class="found-radio"><br /></th><th>%1$s</th><th class="no-break">%2$s</th><th class="no-break">%3$s</th><th class="no-break">%4$s</th></tr></thead><tbody>',
			__( 'Title', 'encore' ),
			__( 'Type', 'encore' ),
			__( 'Date', 'encore' ),
			__( 'Status', 'encore' )
		);

		foreach ( $posts as $post ) {
			$title = trim( $post->post_title ) ? $post->post_title : __( '(no title)', 'encore' );
			$alt   = ( 'alternate' == $alt ) ? '' : 'alternate';

			switch ( $post->post_status ) {
				case 'publish' :
				case 'private' :
					$stat = __( 'Published', 'encore' );
					break;
				case 'future' :
					$stat = __( 'Scheduled', 'encore' );
					break;
				case 'pending' :
					$stat = __( 'Pending Review', 'encore' );
					break;
				case 'draft' :
					$stat = __( 'Draft', 'encore' );
					break;
			}

			if ( '0000-00-00 00:00:00' == $post->post_date ) {
				$time = '';
			} else {
				/* translators: date format in table columns, see http://php.net/date */
				$time = mysql2date( __( 'Y/m/d', 'encore' ), $post->post_date );
			}

			$html .= sprintf(
				'<tr class="%1$s"><td class="found-radio"><input type="checkbox" id="found-%2$d" name="found_post_id[]" value="%2$d"></td>',
				esc_attr( trim( 'found-posts ' . $alt ) ),
				esc_attr( $post->ID )
			);

			$html .= sprintf(
				'<td><label for="found-%1$d">%2$s</label></td><td class="no-break">%3$s</td><td class="no-break">%4$s</td><td class="no-break">%5$s </td></tr>' . "\n\n",
				esc_attr( $post->ID ),
				esc_html( $title ),
				esc_html( get_post_type_object( $post->post_type )->labels->singular_name ),
				esc_html( $time ),
				esc_html( $stat )
			);
		}

		$html .= '</tbody></table>';

		return $html;
	}
}
