<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_merge_tags_other', array(

    /*
    |--------------------------------------------------------------------------
    | Querystring
    |--------------------------------------------------------------------------
    */
    'query_string' => array(
        'tag' => '{querystring:YOUR_KEY}',
        'label' => __( 'Query String', 'ninja_forms' ),
        'callback' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | System Date
    |--------------------------------------------------------------------------
    */

    'date' => array(
        'id' => 'date',
        'tag' => '{other:date}',
        'label' => __( 'Date', 'ninja_forms' ),
        'callback' => 'system_date'
    ),

    /*
    |--------------------------------------------------------------------------
    | System Date
    |--------------------------------------------------------------------------
    */

    'time' => array(
        'id' => 'time',
        'tag' => '{other:time}',
        'label' => __( 'Time', 'ninja_forms' ),
        'callback' => 'system_time'
    ),

    /*
    |--------------------------------------------------------------------------
    | System IP Address
    |--------------------------------------------------------------------------
    */

    'ip' => array(
        'id' => 'ip',
        'tag' => '{other:user_ip}',
        'label' => __( 'User IP Address', 'ninja_forms' ),
        'callback' => 'user_ip'
    ),

));
