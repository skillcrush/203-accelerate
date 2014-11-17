<?php
/**
 * The template for displaying case studies
 *
 *
 * @package WordPress
 * @subpackage Accelerate_Theme
 * @since Accelerate Theme 1.1
 */

get_header(); ?>

<div id="primary" class="site-content">
	<div id="content" role="main">

	<?php while ( have_posts() ) : the_post(); 
		$services = get_field('services'); 
		$client = get_field('client');
		$link = get_field('site_link');
		$image_1 = get_field('image_1');
		$image_2 = get_field('image_2');
		$image_3 = get_field('image_3'); ?>

	<article class="case-study">
		<aside class="case-study-sidebar">
			<h2><?php the_title(); ?></h2>
			<h5><?php echo $services; ?></h5>
			<h6>Client: <?php echo $client; ?></h6>

			<?php the_content(); ?>

			<p><strong><a href="<?php echo $link; ?>">Site Link</a></strong></p>
		</aside>

		<div class="case-study-images">
			<?php if($image_1) { ?>
				<img src="<?php echo $image_1; ?>" />
			<?php } ?>
			
			<?php if($image_2) { ?>
				<img src="<?php echo $image_2; ?>" />
			<?php } ?>

			<?php if($image_3) { ?>
			<img src="<?php echo $image_3; ?>" />
			<?php } ?>
		</div>


			</article>
			<?php endwhile; // end of the loop. ?>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>