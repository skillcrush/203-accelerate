<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_merge_tags_deprecated', array(

    /*
    |--------------------------------------------------------------------------
    | Post ID
    |--------------------------------------------------------------------------
    */

    'id' => array(
        'id' => 'id',
        'tag' => '{post:id}',
        'label' => __( 'Post ID', 'ninja_forms' ),
        'callback' => 'post_id'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Title
    |--------------------------------------------------------------------------
    */

    'title' => array(
        'id' => 'title',
        'tag' => '{post:title}',
        'label' => __( 'Post Title', 'ninja_forms' ),
        'callback' => 'post_title'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post URL
    |--------------------------------------------------------------------------
    */

    'url' => array(
        'id' => 'url',
        'tag' => '{post:url}',
        'label' => __( 'Post URL', 'ninja_forms' ),
        'callback' => 'post_url'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Author
    |--------------------------------------------------------------------------
    */

    'author' => array(
        'id' => 'author',
        'tag' => '{post:author}',
        'label' => __( 'Post Author', 'ninja_forms' ),
        'callback' => 'post_author'
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Author Email
    |--------------------------------------------------------------------------
    */

    'author_email' => array(
        'id' => 'author_email',
        'tag' => '{post:author_email}',
        'label' => __( 'Post Author Email', 'ninja_forms' ),
        'callback' => 'post_author_email'
    ),

    /*
    |--------------------------------------------------------------------------
    | User ID
    |--------------------------------------------------------------------------
    */

    'user_id' => array(
        'id' => 'user_id',
        'tag' => '{user:id}',
        'label' => __( 'User ID', 'ninja_forms' ),
        'callback' => 'user_id'
    ),

    /*
    |--------------------------------------------------------------------------
    | User First Name
    |--------------------------------------------------------------------------
    */

    'first_name' => array(
        'id' => 'first_name',
        'tag' => '{user:first_name}',
        'label' => __( 'User First Name', 'ninja_forms' ),
        'callback' => 'user_first_name'
    ),

    /*
    |--------------------------------------------------------------------------
    | User Last Name
    |--------------------------------------------------------------------------
    */

    'last_name' => array(
        'id' => 'last_name',
        'tag' => '{user:last_name}',
        'label' => __( 'User Last Name', 'ninja_forms' ),
        'callback' => 'user_last_name'
    ),

    /*
    |--------------------------------------------------------------------------
    | User Dispaly Name
    |--------------------------------------------------------------------------
    */

    'display_name' => array(
        'id' => 'display_name',
        'tag' => '{user:display_name}',
        'label' => __( 'User Display Name', 'ninja_forms' ),
        'callback' => 'user_display_name'
    ),

    /*
    |--------------------------------------------------------------------------
    | User Email Address
    |--------------------------------------------------------------------------
    */

    'user_email' => array(
        'id' => 'user_email',
        'tag' => '{user:email}',
        'label' => __( 'User Email', 'ninja_forms' ),
        'callback' => 'user_email'
    ),

    /*
    |--------------------------------------------------------------------------
    | Site Title
    |--------------------------------------------------------------------------
    */

    'site_title' => array(
        'id' => 'site_title',
        'tag' => '{site:title}',
        'label' => __( 'Site Title', 'ninja_forms' ),
        'callback' => 'site_title'
    ),

    /*
    |--------------------------------------------------------------------------
    | Site URL
    |--------------------------------------------------------------------------
    */

    'site_url' => array(
        'id' => 'site_url',
        'tag' => '{site:url}',
        'label' => __( 'Site URL', 'ninja_forms' ),
        'callback' => 'site_url'
    ),

    /*
    |--------------------------------------------------------------------------
    | Admin Email Address
    |--------------------------------------------------------------------------
    */

    'admin_email' => array(
        'id' => 'admin_email',
        'tag' => '{system:admin_email}',
        'label' => __( 'Admin Email', 'ninja_forms' ),
        'callback' => 'admin_email'
    ),

    /*
    |--------------------------------------------------------------------------
    | System Date
    |--------------------------------------------------------------------------
    */

    'date' => array(
        'id' => 'date',
        'tag' => '{system:date}',
        'label' => __( 'Date', 'ninja_forms' ),
        'callback' => 'system_date'
    ),

    /*
    |--------------------------------------------------------------------------
    | System IP Address
    |--------------------------------------------------------------------------
    */

    'ip' => array(
        'id' => 'ip',
        'tag' => '{system:ip}',
        'label' => __( 'IP Address', 'ninja_forms' ),
        'callback' => 'system_ip'
    ),

    'query_string' => array(
        'tag' => '{query_string_key}',
        'label' => __( 'Query String', 'ninja_forms' ),
        'callback' => null,
    ),


));