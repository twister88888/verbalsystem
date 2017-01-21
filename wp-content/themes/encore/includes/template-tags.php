<?php
/**
 * Custom template tags.
 *
 * @package Encore
 * @since 1.0.0
 */

if ( ! function_exists( 'encore_site_branding' ) ) :
/**
 * Display the site logo, title, and description.
 *
 * @since 1.0.0
 */
function encore_site_branding() {
	$output = '';
// Site title.
	$output .= sprintf(
		'<h1 class="site-title"><a href="%1$s" rel="home">%2$s</a></h1>',
		esc_url( home_url( '/' ) ),
		get_bloginfo( 'name', 'display' )
	);
	// Site description.
	$output .= '<div class="site-description">' . get_bloginfo( 'description', 'display' ) . '</div>';
	$id = get_the_ID();
	if ($id == 630){

	$output .= encore_theme()->logo->html();
	}


	echo '<div class="site-branding">' . $output . '</div>'; // XSS OK
}
endif;

if ( ! function_exists( 'encore_comment_navigation' ) ) :
/**
 * Display navigation to next/previous comments when applicable.
 *
 * @since 1.0.0
 */
function encore_comment_navigation() {
	// Are there comments to navigate through?
	if ( get_comment_pages_count() < 2 || ! get_option( 'page_comments' ) ) {
		return;
	}
	?>
	<nav class="navigation comment-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'encore' ); ?></h2>
		<div class="nav-links">
			<?php
			if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'encore' ) ) ) :
				printf( '<div class="nav-previous">%s</div>', encore_allowed_tags( $prev_link ) );
			endif;

			if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'encore' ) ) ) :
				printf( '<div class="nav-next">%s</div>', encore_allowed_tags( $next_link ) );
			endif;
			?>
		</div>
	</nav>
	<?php
}
endif;

if ( ! function_exists( 'encore_content_navigation' ) ) :
/**
 * Display navigation to next/previous posts when applicable.
 *
 * @since 1.0.0
 */
function encore_content_navigation() {
	if ( is_singular() ) :
		the_post_navigation( array(
			'prev_text' => _x( 'Anterior <span class="screen-reader-text">Post: %title</span>', 'Previous post link', 'encore' ),
			'next_text' => _x( 'Siguiente <span class="screen-reader-text">Post: %title</span>', 'Next post link', 'encore' ),
		) );
	else :
		the_posts_navigation( array(
			'prev_text' => __( 'Antiguas', 'encore' ),
			'next_text' => __( 'Nuevas', 'encore' ),
		) );
	endif;
}
endif;

if ( ! function_exists( 'encore_page_links' ) ) :
/**
 * Wrapper for wp_link_pages() to maintain consistent markup.
 *
 * @since 1.0.0
 *
 * @return string
 */
function encore_page_links() {
	if ( ! is_singular() ) {
		return;
	}

	wp_link_pages( array(
		'before'      => '<nav class="page-links padded-content"><span class="page-links-title">' . __( 'Pages', 'encore' ) . '</span>',
		'after'       => '</nav>',
		'link_before' => '<span class="page-links-number">',
		'link_after'  => '</span>',
		'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'encore' ) . ' </span>%',
		'separator'   => '<span class="screen-reader-text">, </span>',
	) );
}
endif;

if ( ! function_exists( 'encore_entry_title' ) ) :
/**
 * Display an entry title.
 *
 * Includes the link on archives.
 *
 * @since 1.0.0
 */
function encore_entry_title() {
	$title  = get_the_title();

	if ( empty( $title ) ) {
		return;
	}

	if ( ! is_singular() ) {
		$title = sprintf(
			'<a class="permalink" href="%1$s" rel="bookmark" itemprop="url">%2$s</a>',
			esc_url( get_permalink() ),
			$title
		);
	}

	printf( '<h1 class="entry-title" itemprop="headline">%s</h1>', encore_allowed_tags( $title ) );
}
endif;

if ( ! function_exists( 'encore_get_entry_author' ) ) :
/**
 * Retrieve entry author.
 *
 * @since 1.0.0
 *
 * @return string
 */
function encore_get_entry_author() {
	$html  = '<span class="entry-author author vcard" itemprop="author" itemscope itemtype="http://schema.org/Person">';
	$html .= sprintf(
		'<a class="url fn n" href="%1$s" rel="author" itemprop="url"><span itemprop="name">%2$s</span></a>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_html( get_the_author() )
	);
	$html .= '</span>';

	return $html;
}
endif;

if ( ! function_exists( 'encore_get_entry_date' ) ) :
/**
 * Retrieve HTML with meta information for the current post-date/time.
 *
 * @since 1.0.0
 *
 * @param bool $updated Optional. Whether to print the updated time, too. Defaults to true.
 * @return string
 */
function encore_get_entry_date( $updated = true ) {
	$time_string = '<time class="entry-time published" datetime="%1$s">%2$s</time>';

	// To appease rich snippets, an updated class needs to be defined.
	// Default to the published time if the post has not been updated.
	if ( $updated ) {
		if ( get_the_time( 'U' ) === get_the_modified_time( 'U' ) ) {
			$time_string .= '<time class="entry-time updated" datetime="%1$s">%2$s</time>';
		} else {
			$time_string .= '<time class="entry-time updated" datetime="%3$s">%4$s</time>';
		}
	}

	return sprintf(
		$time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);
}
endif;

if ( ! function_exists( 'encore_posted_by' ) ) :
/**
 * Display post author byline.
 *
 * @since 1.0.0
 */
function encore_posted_by() {
	?>
	<span class="posted-by byline">
		<?php
		/* translators: %s: Author name */
		printf( __( '<span class="sep">by</span> %s', 'encore' ), encore_get_entry_author() );
		?>
	</span>
	<?php
}
endif;

if ( ! function_exists( 'encore_posted_on' ) ) :
/**
 * Display post date/time with link.
 *
 * @since 1.0.0
 */
function encore_posted_on() {
	?>
	<span class="posted-on">
		<?php
		$html = sprintf(
			'<span class="entry-date"><a href="%1$s" rel="bookmark">%2$s</a></span>',
			esc_url( get_permalink() ),
			encore_get_entry_date()
		);

		/* translators: %s: Publish date */
		printf( __( '<span class="sep">on</span> %s', 'encore' ), $html );
		?>
	</span>
	<?php
}
endif;

if ( ! function_exists( 'encore_entry_comments_link' ) ) :
/**
 * Display linked entry comment count.
 *
 * @since 1.0.0
 */
function encore_entry_comments_link() {
	if ( is_singular() || post_password_required() || ! comments_open() || ! get_comments_number() ) {
		return;
	}

	echo '<span class="entry-comments-link">';
	comments_popup_link(
		__( 'Leave a comment', 'encore' ),
		__( '1 Comment', 'encore' ),
		__( '% Comments', 'encore' )
	);
	echo '</span>';
}
endif;

if ( ! function_exists( 'encore_entry_terms' ) ) :
/**
 * Display terms for a given taxonomy.
 *
 * @since 1.0.0
 *
 * @param array $taxonomies Optional. List of taxonomy objects with labels.
 */
function encore_entry_terms( $taxonomies = array() ) {
	if ( ! is_singular() || post_password_required() ) {
		return;
	}

	echo encore_get_entry_terms( $taxonomies );
}
endif;

if ( ! function_exists( 'encore_get_entry_terms' ) ) :
/**
 * Retrieve terms for a given taxonomy.
 *
 * @since 1.0.0
 *
 * @param array $taxonomies Optional. List of taxonomy objects with labels.
 * @param int|WP_Post $post Optional. Post ID or object. Defaults to the current post.
 */
function encore_get_entry_terms( $taxonomies = array(), $post = null ) {
	$default = array(
		'category' => __( 'Posted In:', 'encore' ),
		'post_tag' => __( 'Tagged:', 'encore' ),
	);

	// Set default taxonomies if empty or not an array.
	if ( ! $taxonomies || ! is_array( $taxonomies ) ) {
		$taxonomies = $default;
	}

	// Allow plugins and themes to override taxonomies and labels.
	$taxonomies = apply_filters( 'encore_entry_terms_taxonomies', $taxonomies );

	// Return early if the taxonomies are empty or not an array.
	if ( ! $taxonomies || ! is_array( $taxonomies ) ) {
		return;
	}

	$post   = get_post( $post );
	$output = '';

	// Get object taxonomy list to validate taxonomy later on.
	$object_taxonomies = get_object_taxonomies( get_post_type() );

	// Loop through each taxonomy and set up term list html.
	foreach ( (array) $taxonomies as $taxonomy => $label ) {
		// Continue if taxonomy is not in the object taxonomy list.
		if ( ! in_array( $taxonomy, $object_taxonomies ) ) {
			continue;
		}

		// Get term list
		$term_list = get_the_term_list( $post->ID, $taxonomy, '<li>', '</li><li>', '</li>' );

		// Continue if there is not one or more terms in the taxonomy.
		if ( ! $term_list || ! encore_theme()->template->has_multiple_terms( $taxonomy ) ) {
			continue;
		}

		if ( $label ) {
			$label = sprintf( '<h3 class="term-title">%s</h3>', $label );
		}

		$term_list = sprintf( '<ul class="term-list">%s</ul>', $term_list );

		// Set term list output html.
		$output .= sprintf(
			'<div class="term-group term-group--%1$s">%2$s%3$s</div>',
			esc_attr( $taxonomy ),
			$label,
			$term_list
		);
	}

	// Return if no term lists were created.
	if ( empty( $output ) ) {
		return;
	}

	printf( '<div class="entry-terms">%s</div>', wp_kses_post( $output ) );
}
endif;

/**
 * Determine if a post has content.
 *
 * Mimics the_content() to closely approximate the output.
 *
 * If a post ID is passed, the post data will be reset afterward.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Defaults to the current global post.
 * @return bool
 */
function encore_has_content( $post_id = null ) {
	global $post;

	if ( ! empty( $post_id ) ) {
		$post = get_post( $post_id );
		setup_postdata( $post );
	}

	$content = apply_filters( 'the_content', get_the_content() );
	$content = str_replace( ']]>', ']]&gt;', $content );

	$has_content = apply_filters( 'encore_has_content', ! empty( $content ), $post );

	if ( ! empty( $post_id ) ) {
		wp_reset_postdata();
	}

	return (bool) $has_content;
}

/**
 * Print classes needed to render a block grid.
 *
 * @since 1.0.0
 *
 * @param array $classes List of HTML classes.
 */
function encore_block_grid_classes( $classes = array() ) {
	// Split a string.
	if ( ! empty( $classes ) && ! is_array( $classes ) ) {
		$classes = preg_split( '#\s+#', $classes );
	}

	array_unshift( $classes, 'block-grid', 'block-grid--gutters' );
	$classes = apply_filters( 'encore_block_grid_classes', $classes );

	echo esc_attr( implode( ' ', $classes ) );
}

/**
 * Retrieve settings for the homepage player.
 *
 * @since 1.0.0
 *
 * @return array Array of settings.
 */
function encore_get_player_settings() {
	$tracks = encore_get_player_tracks();

	if ( empty( $tracks ) ) {
		return array();
	}

	$settings = array(
		'signature' => md5( implode( ',', wp_list_pluck( $tracks, 'src' ) ) ),
		'tracks'    => $tracks,
	);

	return $settings;
}

/**
 * Retrieve the tracks for the homepage player.
 *
 * Uses values set by a filter, otherwise uses an option from the Customizer.
 *
 * @since 1.0.0
 * @see wp_get_playlist()
 *
 * @return array Array of tracks.
 */
function encore_get_player_tracks() {
	return encore_theme()->template->get_tracks( 'player_attachment_ids' );
}

if ( ! function_exists( 'encore_allowed_tags' ) ) :
/**
 * Allow only the allowedtags array in a string.
 *
 * @since 1.0.1
 *
 * @link https://www.tollmanz.com/wp-kses-performance/
 *
 * @param  string $string The unsanitized string.
 * @return string         The sanitized string.
 */
function encore_allowed_tags( $string ) {
	global $allowedtags;

	$theme_tags = array(
		'a'    => array(
			'href'     => true,
			'itemprop' => true,
			'rel'      => true,
			'title'    => true,
		),
		'span' => array(
			'class' => true,
		),
	);

	return wp_kses( $string, array_merge( $allowedtags, $theme_tags ) );
}
endif;

if ( ! function_exists( 'encore_credits' ) ) :
/**
 * Theme credits text.
 *
 * @since 1.0.0
 */
function encore_credits() {
	echo encore_get_credits();
}
endif;

if ( ! function_exists( 'encore_get_credits' ) ) :
/**
 * Retrieve theme credits text.
 *
 * @since 1.0.0
 *
 * @param string $text Text to display.
 * @return string
 */
function encore_get_credits() {
	$text = sprintf(
		'Verbal System, '.date("Y")
	);

	return apply_filters( 'encore_credits', $text );
}
endif;
