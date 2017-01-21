<?php
/**
 * Featured Content control for the Customizer.
 *
 * @package Cedaro\Theme
 * @license GPL-2.0+
 * @since 3.1.0
 */

/**
 * Featured Content Customizer control.
 *
 * @package Cedaro\Theme
 * @since 3.1.0
 */
class Cedaro_Theme_Customize_Control_FeaturedContent extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @since 3.1.0
	 * @type string
	 */
	public $type = 'cedaro-theme-featured-content';

	/**
	 * Post types that can be featured.
	 *
	 * @since 3.1.0
	 * @type array
	 */
	public $post_types = array( 'post' );

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.1.0
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$this->json['posts']     = $this->get_posts();
		$this->json['postTypes'] = $this->post_types;

		$this->json['l10n'] = array(
			'responseError' => __( 'An error has occurred. Please reload the page and try again.', 'encore' ),
		);
	}

	/**
	 * Render the control's content.
	 *
	 * @since 3.1.0
	 */
	public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<input type="hidden" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
		</label>
		<?php
	}

	/**
	 * Retrieve featured posts.
	 *
	 * @since 3.1.0
	 *
	 * @return array
	 */
	protected function get_posts() {
		$data  = array();
		$value = $this->value();

		if ( ! empty( $value ) ) {
			$posts = get_posts( array(
				'post_type'      => $this->post_types,
				'post_status'    => 'any',
				'post__in'       => array_map( 'absint', explode( ',', $value ) ),
				'orderby'        => 'post__in',
				'posts_per_page' => 20,
			) );
		}

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$data[] = array(
					'id'    => $post->ID,
					'title' => $post->post_title,
				);
			}
		}

		return $data;
	}
}
