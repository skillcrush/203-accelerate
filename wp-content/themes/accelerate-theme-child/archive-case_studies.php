<?php
/**
 * The template for displaying the case studies archive page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 2.0
 */

get_header(); ?>

    	<div id="primary" class="site-content sidebar">
		<div class="main-content" role="main">
			
<?php while ( have_posts() ) : the_post();
	$size = "full";
	$services = get_field('services');
	$link = get_field('site_link');
	$image_1 = get_field('image_1'); ?>
				
	<article class="case-study">
    	<aside class="case-study-sidebar">
        	<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
         	<h4><?php echo $services; ?></h4>

			<?php the_excerpt(); ?>

          	<p class="read-more-link"><a href="<?php echo $link; ?>">View Project</a></p>
     	</aside>

		<div class="case-study-images">
            <a href="<?php echo $link; ?>">
                <?php if ($image_1) { 
				    echo wp_get_attachment_image ( $image_1, $size);
                } ?>
            </a>
		</div>
				
			<?php endwhile; // end of the loop. ?>
		</div><!-- .main-content -->

	</div><!-- #primary -->

<?php get_footer(); ?>