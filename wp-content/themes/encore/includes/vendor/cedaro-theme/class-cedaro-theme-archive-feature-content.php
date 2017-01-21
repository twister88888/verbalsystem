<?php
/**
 * Archive content settings.
 *
 * @since 3.2.0
 *
 * @package Cedaro\Theme
 * @copyright Copyright (c) 2015, Cedaro
 * @license GPL-2.0+
 */

/**
 * Class for managing content display on archives.
 *
 * @package Cedaro\Theme
 * @since 3.2.0
 */
class Cedaro_Theme_Archive_Feature_Content extends Cedaro_Theme_Archive_Feature {
	/**
	 * Post formats that should show full-text.
	 *
	 * @since 3.2.0
	 * @type array
	 */
	protected $full_text_formats = array( 'aside', 'audio', 'quote', 'status' );

	/*
	 * Public API methods.
	 */

	/**
	 * Wire up the theme hooks.
	 *
	 * @since 3.2.0
	 */
	public function add_support( $options = array() ) {
		if ( empty( $options ) ) {
			$options = array(
				'full-text'       => __( 'Full Text', 'encore' ),
				'summary'         => __( 'Summary', 'encore' ),
				'custom-excerpts' => __( 'Custom Excerpts', 'encore' ),
				'none'            => __( 'No Content', 'encore' ),
			);
		}

		$this->set_options( $options );

		add_filter( 'the_content',        array( $this, 'filter_the_content' ), 1000 );
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		return $this;
	}

	/**
	 * Display the current post's content.
	 *
	 * @since 3.2.0
	 *
	 * @param string $more_link_text Optional. Content for when there is more text.
	 * @param bool $strip_teaser Optional. Strip teaser content before the more text. Default is false.
	 */
	public function the_content( $more_link_text = null, $strip_teaser = false ) {
		$mode = $this->get_mode();

		$this->in_the_content = true;
		if ( 'full-text' == $mode ) {
			the_content( $more_link_text, $strip_teaser );
		} elseif ( 'summary' == $mode ) {
			the_excerpt();
		} elseif ( 'custom-excerpts' == $mode && has_excerpt() ) {
			the_excerpt();
		}
		$this->in_the_content = false;
	}

	/**
	 * Retrieve the content mode for the current post.
	 *
	 * @since 3.2.0
	 *
	 * @param int|WP_Post $post Post ID or object.
	 * @retrun string
	 */
	public function get_mode( $post = null ) {
		$post = get_post( $post );
		$mode = get_theme_mod( 'archive_content_mode', '' );

		// Some post formats should always display full-text.
		if ( in_array( get_post_format( $post->ID ), $this->full_text_formats ) ) {
			$mode = 'full-text';
		}

		return apply_filters( $this->theme->prefix . '_archive_content_mode', $mode, $post );
	}

	/**
	 * Set post formats that should always show full-text.
	 *
	 * @since 3.2.0
	 *
	 * @param array $formats Post formats.
	 * @return $this
	 */
	public function set_full_text_formats( $formats ) {
		$this->full_text_formats = $formats;
		return $this;
	}

	/*
	 * Hook callbacks.
	 */

	/**
	 * Filter the content to show an excerpt on archive pages if in summary mode.
	 *
	 * Note: Any filters set to run after the_content:1000 will still append
	 * their content when in summary mode.
	 *
	 * @since 3.2.0
	 *
	 * @param string $content Default post content.
	 * @return string
	 */
	public function filter_the_content( $content ) {
		if  ( ! $this->in_supported_loop() ) {
			return $content;
		}

		$mode = $this->get_mode();

		if ( 'none' == $mode ) {
			$content = '';
		} elseif ( 'summary' == $mode || 'custom-excerpts' == $mode ) {
			$content = '';

			// Prevent recursive loops when wp_trim_excerpt() applies
			// `the_content` filter again.
			$this->in_the_content = true;
			if ( 'summary' == $mode || has_excerpt() ) {
				the_excerpt();
			}
			$this->in_the_content = false;
		}

		return $content;
	}

	/**
	 * Register Customizer settings and controls.
	 *
	 * @since 3.2.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 */
	public function customize_register( $wp_customize ) {
		// Set up archive content mode setting.
		$wp_customize->add_setting( 'archive_content_mode', array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => array( $this, 'sanitize_choices' ),
		) );

		// Set up archive content mode setting control.
		$wp_customize->add_control( 'cedaro_archive_content_mode', array(
			'label'    => __( 'Archive Content', 'encore' ),
			'section'  => 'theme_options',
			'settings' => 'archive_content_mode',
			'type'     => 'select',
			'choices'  => $this->get_choices(),
		) );
	}
}
