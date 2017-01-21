<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package Encore
 * @since 1.0.0
 */

// If the current post is protected by a password and the visitor has not yet
// entered the password, return early without loading the comments.
if ( post_password_required() ) {
	return;
}

// Don't show comments if commenting is closed and the current post doesn't
// have any comments associated with it.
if ( ! comments_open() && '0' === get_comments_number() ) {
	return;
}

$comments_by_type = $wp_query->comments_by_type;
$comments_count   = count( $comments_by_type['comment'] );

do_action( 'encore_comments_before' );
?>

<section id="comments" class="comments-area">

	<?php do_action( 'encore_comments_top' ); ?>

	<div class="content-box comments-wrapper">
		<?php if ( have_comments() ) : ?>

			<?php if ( '0' != count( $comments_by_type['comment'] ) ) : ?>
				<header class="comments-header">
					<h2 class="comments-title">
						<?php
						echo esc_html( sprintf(
							_n( '%1$s Comment', '%1$s Comments', $comments_count, 'encore' ),
							esc_html( number_format_i18n( $comments_count ) )
						) );
						?>
					</h2>
				</header>
			<?php endif; ?>

			<ol class="comment-list<?php if ( '1' === get_option( 'show_avatars' ) ) { echo ' show-avatars'; } ?>">
				<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 60,
					'max_depth'   => 4,
				) );
				?>
			</ol>

		<?php endif; ?>

		<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>

			<footer class="comments-footer">
				<p><?php esc_html_e( 'Comments are closed.', 'encore' ); ?></p>
			</difooterv>

		<?php endif; ?>
	</div>

	<div class="content-box comment-form-wrapper">
		<?php
		comment_form( array(
			'comment_notes_after' => false,
		) );
		?>
	</div>

	<?php encore_comment_navigation(); ?>

	<?php do_action( 'encore_comments_bottom' ); ?>

</section>

<?php
do_action( 'encore_comments_after' );
