<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_merge_tags_wp', array(

    /*
    |--------------------------------------------------------------------------
    | Post ID
    |--------------------------------------------------------------------------
    */

    'id' => array(
        'id' => 'id',
        'tag' => '{wp:post_id}',
        'label' => esc_html__( 'Post ID', 'ninja_forms' ),
        'callback' => 'post_id'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Title
    |--------------------------------------------------------------------------
    */

    'title' => array(
        'id' => 'title',
        'tag' => '{wp:post_title}',
        'label' => esc_html__( 'Post Title', 'ninja_forms' ),
        'callback' => 'post_title'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post URL
    |--------------------------------------------------------------------------
    */

    'url' => array(
        'id' => 'url',
        'tag' => '{wp:post_url}',
        'label' => esc_html__( 'Post URL', 'ninja_forms' ),
        'callback' => 'post_url'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Author
    |--------------------------------------------------------------------------
    */

    'author' => array(
        'id' => 'author',
        'tag' => '{wp:post_author}',
        'label' => esc_html__( 'Post Author', 'ninja_forms' ),
        'callback' => 'post_author'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Author Email
    |--------------------------------------------------------------------------
    */

    'author_email' => array(
        'id' => 'author_email',
        'tag' => '{wp:post_author_email}',
        'label' => esc_html__( 'Post Author Email', 'ninja_forms' ),
        'callback' => 'post_author_email'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Meta
    |--------------------------------------------------------------------------
    */

    'post_meta' => array(
        'id' => 'post_meta',
        'tag' => '{post_meta:YOUR_META_KEY}',
        'label' => esc_html__( 'Post Meta', 'ninja_forms' ),
        'callback' => null
    ),

    /*
    |--------------------------------------------------------------------------
    | User ID
    |--------------------------------------------------------------------------
    */

    'user_id' => array(
        'id' => 'user_id',
        'tag' => '{wp:user_id}',
        'label' => esc_html__( 'User ID', 'ninja_forms' ),
        'callback' => 'user_id'
    ),

    /*
    |--------------------------------------------------------------------------
    | User First Name
    |--------------------------------------------------------------------------
    */

    'first_name' => array(
        'id' => 'first_name',
        'tag' => '{wp:user_first_name}',
        'label' => esc_html__( 'User First Name', 'ninja_forms' ),
        'callback' => 'user_first_name'
    ),

    /*
    |--------------------------------------------------------------------------
    | User Last Name
    |--------------------------------------------------------------------------
    */

    'last_name' => array(
        'id' => 'last_name',
        'tag' => '{wp:user_last_name}',
        'label' => esc_html__( 'User Last Name', 'ninja_forms' ),
        'callback' => 'user_last_name'
    ),

    /*
    |--------------------------------------------------------------------------
    | User Disply Name
    |--------------------------------------------------------------------------
    */

    'display_name' => array(
        'id' => 'display_name',
        'tag' => '{wp:user_display_name}',
        'label' => esc_html__( 'User Display Name', 'ninja_forms' ),
        'callback' => 'user_display_name'
    ),

    /*
    |--------------------------------------------------------------------------
    | User Username
    |--------------------------------------------------------------------------
    */

    'username' => array(
        'id' => 'username',
        'tag' => '{wp:user_username}',
        'label' => esc_html__( 'User Username', 'ninja_forms' ),
        'callback' => 'user_username'
    ),

    /*
    |--------------------------------------------------------------------------
    | User Email Address
    |--------------------------------------------------------------------------
    */

    'user_email' => array(
        'id' => 'user_email',
        'tag' => '{wp:user_email}',
        'label' => esc_html__( 'User Email', 'ninja_forms' ),
        'callback' => 'user_email'
    ),

    /*
    |--------------------------------------------------------------------------
    | User Website Address
    |--------------------------------------------------------------------------
    */

    'user_url' => array(
        'id' => 'user_url',
        'tag' => '{wp:user_url}',
        'label' => esc_html__( 'User URL', 'ninja_forms' ),
        'callback' => 'user_url'
    ),

    /*
     |--------------------------------------------------------------------------
     | Post Meta
     |--------------------------------------------------------------------------
     */

    'user_meta' => array(
        'id' => 'user_meta',
        'tag' => '{user_meta:YOUR_META_KEY}',
        'label' => esc_html__( 'User Meta', 'ninja_forms' ),
        'callback' => null
    ),

    /*
    |--------------------------------------------------------------------------
    | Site Title
    |--------------------------------------------------------------------------
    */

    'site_title' => array(
        'id' => 'site_title',
        'tag' => '{wp:site_title}',
        'label' => esc_html__( 'Site Title', 'ninja_forms' ),
        'callback' => 'site_title'
    ),

    /*
    |--------------------------------------------------------------------------
    | Site URL
    |--------------------------------------------------------------------------
    */

    'site_url' => array(
        'id' => 'site_url',
        'tag' => '{wp:site_url}',
        'label' => esc_html__( 'Site URL', 'ninja_forms' ),
        'callback' => 'site_url'
    ),

    /*
    |--------------------------------------------------------------------------
    | Admin Email Address
    |--------------------------------------------------------------------------
    */

    'admin_email' => array(
        'id' => 'admin_email',
        'tag' => '{wp:admin_email}',
        'label' => esc_html__( 'Admin Email', 'ninja_forms' ),
        'callback' => 'admin_email'
    ),

));
