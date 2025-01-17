<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to sweetdate_comment() which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments">
	<?php if ( have_comments() ) : ?>

                <h4>
                <?php
                printf( _n( 'One comment', '%1$s comments', get_comments_number(), 'kleo_framework' ).": ",number_format_i18n( get_comments_number() ) );
                ?>  
                </h4>
    
		<ol class="comments-list clearfix">
                    <?php wp_list_comments( array( 'callback' => 'sweetdate_comment', 'style' => 'ul' ) ); ?>
		</ol><!-- .commentlist -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation" role="navigation">
                    <h1 class="assistive-text section-heading"><?php _e( 'Comment navigation', 'kleo_framework' ); ?></h1>
                    <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'kleo_framework' ) ); ?></div>
                    <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'kleo_framework' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<?php
		/* If there are no comments and comments are closed, let's leave a note.
		 * But we only want the note on posts and pages that had comments in the first place.
		 */
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="nocomments"><?php _e( 'Comments are closed.' , 'kleo_framework' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php kleo_comment_form(); ?>

</div><!-- #comments .comments-area -->