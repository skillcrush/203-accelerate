<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package WordPress
 * @subpackage Accelerate
 * @since Accelerate 2.0
 */
  ?>

 <div id="comments" class="comments-area">
 	<?php if ( have_comments() ) : ?>
 		<h2 class="comments-title">
 			<?php
 				printf( _n( '1 comment', '%1$s comments', get_comments_number(), 'accelerate' ),
 					number_format_i18n( get_comments_number() ), get_the_title() );
 			?>
 		</h2>


         <ol class="comment-list">
         	<?php
         		wp_list_comments( array(
         			'style'      => 'ul',
         			'short_ping' => true,
         			'avatar_size'=> 0,
         			'callback' => 'accelerate_comments'
         		) );
         	?>
         </ol><!-- .comment-list -->

         <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
             <nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
             	<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'accelerate' ); ?></h1>
             	<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'accelerate' ) ); ?></div>
             	<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'accelerate' ) ); ?></div>
             </nav><!-- #comment-nav-below -->
         <?php endif; // Check for comment navigation. ?>

         <?php if ( ! comments_open() ) : ?>
         	<p class="no-comments"><?php _e( 'Comments are closed.', 'accelerate' ); ?></p>
         <?php endif; ?>

 	<?php else: ?>
 		<h2 class="comments-title">No comments</h2>
 	<?php endif; // have_comments() ?>

 	<?php comment_form(array('title_reply' => 'Leave a Comment')); ?>

 </div><!-- #comments -->
