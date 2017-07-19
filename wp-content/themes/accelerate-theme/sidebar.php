<?php
/**
 * The Sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 2.0
 */
?>

<div id="sidebar">

			<div class="widget widget_text">
					<div class="intro-text">This is a practice site for our <a href="http://skillcrush.com/">Skillcrush</a> students. <a href="https://skillcrush.com/wordpress-blueprint-package-first-to-know">We’d love to see you in class!</a></div>
				</div>

				<div class="widget widget_search">
					<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="search" />
						<input type="submit" id="searchsubmit" value="" />
					</form>
				</div>

				<div class="widget widget_categories">
					<h2 class="widget-title">Categories</h2>
					<ul>
						<li><a href="">Books </a></li>
						<li><a href="">My Notes</a></li>
						<li><a href="">Photos</a></li>
						<li><a href="">Videos</a></li>
						<li><a href="">Quotes</a></li>
					</ul>
				</div>

				<div class="widget widget_archives">
					<h2 class="widget-title">Archives</h2>
					<ul>
						<li><a href="">September 2014</a></li>
						<li><a href="">August 2014</a></li>
						<li><a href="">July 2014</a></li>
						<li><a href="">June 2014</a></li>
						<li><a href="">May 2014</a></li>
					</ul>
				</div>

				<div class="widget widget_events">
					<h2 class="widget-title">Events</h3>
					<ul>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/img/Brooklyn.png" alt=""/></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/img/Rails.png" alt=""/></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/img/Etsy.png" alt=""/></a></li>
					</ul>
				</div>

</div>
