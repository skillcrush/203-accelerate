<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 1.0
 */

get_header(); ?>

	<!-- BLOG PAGE -->
	<section class="blog-page">
		<div class="site-content">
			<div class="main-content">
				
				<?php
				// Start the Loop.
				while ( have_posts() ) : the_post(); ?>

				<article class="post-entry individual-post">
					<div class="entry-wrap">
						
						<header class="entry-header">
							<div class="entry-meta">
								<time class="entry-time"><?php the_date();?></time>
							</div>
							<h2 class="entry-title"><?php the_title(); ?></h2>
						</header>
						
						<div class="entry-summary">
							<?php the_content(); ?>
						</div>
						
						<footer class="entry-footer">
							<div class="entry-meta">
								<span class="entry-terms author">Written by <?php the_author_posts_link(); ?></span>
								<span class="entry-terms category">Posted in <?php the_category(', '); ?></span>
								<span class="entry-terms comments"><?php comments_number( 'No comments yet!', '1 comment', '% comments' ); ?></span>
							</div>
						</footer>
						
					</div>
				</article>

					<?php 
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif; ?>
			</div>

			<?php get_sidebar(); ?>

			<footer class="navigation container">
				<div class="left">&larr;<a href="<?php echo home_url(); ?>/blog">back to posts</a></div>
			</footer>
	 
				<?php endwhile; ?>
			</div>
		</section>	
		
<?php
get_footer();