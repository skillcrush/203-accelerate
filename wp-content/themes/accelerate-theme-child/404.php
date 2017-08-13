<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 1.0
 */

 get_header(); ?>

 	<div id="primary" class="site-content">
 		<div id="content" role="main">
 			<div class="page-content">
				<div class="error-page">
					<h2>Uh-oh! Something went wrong!</h2>
					<p>Sorry that something weird happened. Why don't you try looking at our <a href="<?php echo home_url(); ?>/blog"><span>blog</span></a> page.<p>
				</div>
 			</div>
 		</div><!-- #content -->
 	</div><!-- #primary -->

 <?php get_footer(); ?>
	
