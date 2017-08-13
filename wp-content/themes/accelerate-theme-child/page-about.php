<?php
/**
 * The custom template for displaying the about page
 *
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 1.0
 */

get_header(); ?>

<div class="about-page-top">
	<div class="about-hero">
	<p>Accelerate is a strategy and marketing agency located in the heart of NYC. Our goal is to build businesses
		by making our clients visible and making their customers smile.</p>
	</div>
</div>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<?php while ( have_posts() ) : the_post();
				$size = "thumb";
				$mission_statement = get_field('mission_statement');
				$content_strategy = get_field('content_strategy');
				$influencer_mapping = get_field('influencer_mapping');
				$social_media = get_field('social_media');
				$design_development = get_field('design_development');
				$image_1 = get_field('image_1');
				$image_2 = get_field('image_2');
				$image_3 = get_field('image_3');
				$image_4 = get_field('image_4');
			?>

			<div class="about-page-content">

				<div class="about-intro">
				<h4>Our Services</h4>
				<p><?php echo $mission_statement; ?></p>
				</div>

				<div class="about-info" class="about-info-right">
					<figure>
						<?php if($image_1) { ?>
							<?php echo wp_get_attachment_image( $image_1, $size ); ?>
						<?php } ?>
					</figure>
					<div>
						<h3>Content Strategy</h3>
						<p><?php echo $content_strategy; ?></p>
					</div>
				</div>

				<div class="about-info" class="about-info-left">
					<div>
						<h3>Influencer Mapping</h3>
						<p><?php echo $influencer_mapping; ?></p>
					</div>
					<figure>
						<?php if($image_2) { ?>
							<?php echo wp_get_attachment_image( $image_2, $size ); ?>
						<?php } ?>
					</figure>
				</div>

				<div class="about-info" class="about-info-right">
					<figure>
						<?php if($image_3) { ?>
							<?php echo wp_get_attachment_image( $image_3, $size ); ?>
						<?php } ?>
					</figure>
					<div>
						<h3>Content Strategy</h3>
						<p><?php echo $content_strategy; ?></p>
					</div>
				</div>

				<div class="about-info" class="about-info-left">
					<div>
						<h3>Design and Development</h3>
						<p><?php echo $design_development; ?></p>
					</div>
					<figure>
						<?php if($image_4) { ?>
							<?php echo wp_get_attachment_image( $image_4, $size ); ?>
						<?php } ?>
					</figure>
				</div>
			<?php endwhile; // end of the loop. ?>

			<div class="about-foot">
				<h2>Interested in working with us?</h2>
				<a class="contact-button" href="<?php echo home_url(); ?>/contact-us">Contact Us</a>
			</div>

			</div>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>
