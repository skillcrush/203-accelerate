<?php
/**
* Accelerate Marketing Child functions and definitions
*
* @link http://codex.wordpress.org/Theme_Development
* @link http://codex.wordpress.org/Child_Themes
*
* @package WordPress
* @subpackage Accelerate Marketing
* @since Accelerate Marketing 2.0
*/

// Enqueue scripts and styles
function accelerate_child_scripts(){
	wp_enqueue_style( 'accelerate-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'accelerate-style' ));
}
add_action( 'wp_enqueue_scripts', 'accelerate_child_scripts' );


function create_custom_post_types() {

    register_post_type( 'case_studies', //new custom post type with a unique name
        array(
            'labels' => array( 					//defines settings for your new post type.
                'name' => __( 'Case Studies' ), //name for your collection of case studies posts. See in the left nav in admin.
                'singular_name' => __( 'Case Study' ) //name for a single case study post.
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array( 'slug' => 'case-studies' ), //name used in the URLs for your case study posts.
        )
    );
}
add_action( 'init', 'create_custom_post_types' );
