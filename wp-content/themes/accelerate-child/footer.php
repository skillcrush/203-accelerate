<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 1.0
 */
?>

		</div><!-- #main -->


		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="site-info">
				
				<nav class="social-media-navigation">
					<ul id="social-icon-list">
						<li> 
							<a href="http://www.twitter.com">
								<img src="wp-content/themes/accelerate-child/img/twitter_social_media.png" data-pin-nopin="true"></a>
						</li>
						<li> 
							<a href="http://www.facebook.com">
								<img src="wp-content/themes/accelerate-child/img/facebook-social-media.png" data-pin-nopin="true"></a>
						</li>
						<li> 
							<a href="http://www.linkedin.com">
								<img src="wp-content/themes/accelerate-child/img/linkedin-social-media.png" data-pin-nopin="true"></a>
						</li>
				</ul>
				</nav>	
				
			</div><!-- .site-info -->
			<div class="business-tagline">
				<p><span class="highlight">Accelerate</span> is a strategy and marketing agency aimed at making our clients visible and building their businesses.</p>
			</div>
			
			<div class="site-description">
				<p><?php bloginfo('description'); ?></p>
				<p>&copy; <?php bloginfo('title'); ?>, LLC
				</div>
		
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>