<?php
/**
 * 
 * Template Name: About Us Page
 * 
 * 
 * The template for displaying the About page
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

	<div id=about-primary" class="about-us about-site-content">
		<div class="about-main-content" role="main">
            <?php while ( have_posts() ) : the_post();
                $short_description = get_field('short_description');
                $overview_statement = get_field('overview_statement');
                $service_1 = get_field('service_1');
                $service_2 = get_field('service_2');
                $service_3 = get_field('service_3');
                $service_4 = get_field('service_4');
                $service_1_description = get_field('service_1_description');
                $service_2_description = get_field('service_2_description');
                $service_3_description = get_field('service_3_description');
                $service_4_description = get_field('service_4_description');
                $service_1_image = get_field('service_1_image');
                $service_2_image = get_field('service_2_image');
                $service_3_image = get_field('service_3_image');
                $service_4_image = get_field('service_4_image');
                ?>  

            <div class="about-hero">
                <h2><?php echo $short_description; ?></h2>
            </div>       

            <article class="about-services">

                <div class="overview">
                    <h3>OUR SERVICES</h3>
                    <p><?php echo $overview_statement ?></p>
                </div>
                
                <div class="services-wrapper about-left-image">
                    <?php if($service_1_image) { 
                        echo wp_get_attachment_image($service_1_image, $size); 
                    } ?>
                    <div>
                        <h4><?php echo $service_1 ?></h4>
                        <p><?php echo $service_1_description ?></p>
                    </div>
                </div>

                <div class="services-wrapper about-right-image">
                    <div>
                        <h4><?php echo $service_2 ?></h4>
                        <p><?php echo $service_2_description ?></p>
                    </div>
                    <?php if($service_2_image) {
                        echo wp_get_attachment_image($service_2_image, $size); 
                    } ?>
                </div>

                <div class="services-wrapper about-left-image">
                    <?php if($service_3_image) {
                        echo wp_get_attachment_image($service_3_image, $size); 
                    } ?>
                    <div>
                        <h4><?php echo $service_3 ?></h4>
                        <p><?php echo $service_3_description ?></p>
                    </div>
                </div>

                <div class="services-wrapper about-right-image">
                    <div>
                        <h4><?php echo $service_4 ?></h4>
                        <p><?php echo $service_4_description ?></p>
                    </div>
                    <?php if($service_4_image) {
                        echo wp_get_attachment_image($service_4_image, $size); 
                    } ?>
                <div>
            </article>

			<?php endwhile ?>
		</div><!-- .main-content -->
	</div><!-- #primary -->		

	<nav id="navigation" class="container">
        <div class="about-bottom-nav-wrapper">
            <div class="about-contact-section">
                <p><?php echo "Interested in working with us? "?></p>
            <a class="button" href="<?php echo site_url('/contact-us/') ?>">Contact Us</a>
            </div>
        </div>
		
	</nav>
<?php get_footer(); ?>