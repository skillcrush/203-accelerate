<?php
/**
* The default template for displaying content
*
* @link http://codex.wordpress.org/Template_Hierarchy
*
* @package WordPress
* @subpackage Accelerate Marketing
* @since Accelerate Marketing 2.0
*/
?>

<article id="post-<?php the_ID(); ?>" class="post-entry">
	<div class="entry-wrap">
		<header class="entry-header">
			<div class="entry-meta">
				<time class="entry-time"><?php echo get_the_date(); ?></time>
			</div>
			<h2 class="entry-title"><?php the_title(); ?></h2>
		</header>
		<div class="entry-summary">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure>
					<?php the_post_thumbnail('full'); ?>
				</figure>
			<?php endif; ?>
			<?php the_content(); ?>
		</div>
		<footer class="entry-footer">
			<div class="entry-meta">
				<span class="entry-terms comments author">
					Written by <?php the_author_posts_link(); ?>
					/
					Posted in <?php the_category(', '); ?>
				</span>
			</div>
		</footer>
	</div>
</article>
