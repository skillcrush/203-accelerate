<?php
/**
 * Accelerate Marketing functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link http://codex.wordpress.org/Theme_Development
 * @link http://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * @link http://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage Accelerate Marketing
 * @since Accelerate Marketing 1.0
 */

/**
 * Register menus.
 */
function accelerate_setup() {
  register_nav_menus( array(
    'top-nav' => __( 'Top Nav', 'accelerate' ),
    'social-media'  => __( 'Social Media Nav', 'accelerate' ),
  ) );
}
add_action( 'after_setup_theme', 'accelerate_setup' ); 

/**
 * Register widget area.
 */
function accelerate_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Widget Area', 'accelerate' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your blog sidebar.', 'accelerate' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'accelerate_widgets_init' );