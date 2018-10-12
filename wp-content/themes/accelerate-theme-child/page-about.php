<?php
/**
 * The template for displaying the About page
 *
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 2.0
 */

get_header(); ?>
	<div id="about" class="about-top-content">
		<div class="main-content" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
						<?php the_content(); ?>
				<?php endwhile; // end of the loop. ?>
		</div><!-- .main-content -->
	</div><!-- #primary -->

	<section class="about-info">
		<div class="site-content">
				<?php query_posts('post_type=about_page'); ?>
					<?php while ( have_posts() ) : the_post();
							$our_services = get_field('our_services');
							$content_strategy = get_field('content_strategy');
							$influencer_mapping = get_field('influencer_mapping');
							$social_media_strategy = get_field('social_media_strategy');
							$design_and_development = get_field('design_and_development');
							$image_1 = get_field('image_1');
							$image_2 = get_field('image_2');
							$image_3 = get_field('image_3');
							$image_4 = get_field('image_4');
							$size = "medium";
					?>

					<div>


					<li class="individual-featured-work">
							<figure>
									<a href="<?php the_permalink(); ?>"><?php echo wp_get_attachment_image($image_1, $size); ?></a>
							</figure>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					</li>
				 <?php endwhile; ?>
				<?php wp_reset_query(); /*resets the altered query back to the original*/ ?>
		</div> <!-- End of .site-content -->
	</section>
<?php get_footer(); ?>
