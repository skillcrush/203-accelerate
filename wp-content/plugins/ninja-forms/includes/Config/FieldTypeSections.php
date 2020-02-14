<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_field_type_sections', array(

    /*
    |--------------------------------------------------------------------------
    | Saved Fields
    |--------------------------------------------------------------------------
    */

    'saved'             => array(
        'id'            => 'saved',
        'nicename'      => esc_html__( 'Favorite Fields', 'ninja-forms' ),
        'classes'       => 'nf-saved',
        'fieldTypes'    => array(),
    ),

    /*
    |--------------------------------------------------------------------------
    | Common Fields
    |--------------------------------------------------------------------------
    */

    'common' => array(
        'id' => 'common',
        'nicename' => esc_html__( 'Common Fields', 'ninja-forms' ),
        'fieldTypes' => array(),
    ),

    /*
    |--------------------------------------------------------------------------
    | User Information Fields
    |--------------------------------------------------------------------------
    */

    'userinfo' => array(
        'id' => 'userinfo',
        'nicename' => esc_html__( 'User Information Fields', 'ninja-forms' ),
        'fieldTypes' => array(),
    ),

    /*
    |--------------------------------------------------------------------------
    | Layout Fields
    |--------------------------------------------------------------------------
    */

    'layout' => array(
        'id' => 'layout',
        'nicename' => esc_html__( 'Layout Fields', 'ninja-forms' ),
        'fieldTypes' => array(),
    ),

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous Fields
    |--------------------------------------------------------------------------
    */

    'misc' => array(
        'id' => 'misc',
        'nicename' => esc_html__( 'Miscellaneous Fields', 'ninja-forms' ),
        'fieldTypes' => array(),
    ),
));
