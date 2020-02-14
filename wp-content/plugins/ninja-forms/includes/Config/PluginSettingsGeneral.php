<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_plugin_settings_general', array(

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    */

    'version' => array(
        'id'    => 'version',
        'type'  => 'desc',
        'label' => esc_html__( 'Version', 'ninja-forms' ),
        'desc'  => ''
    ),

    /*
    |--------------------------------------------------------------------------
    | Date Format
    |--------------------------------------------------------------------------
    */

    'date_format' => array(
        'id'    => 'date_format',
        'type'  => 'textbox',
        'label' => esc_html__( 'Date Format', 'ninja-forms' ),
        'desc'  => 'e.g. m/d/Y, d/m/Y - ' . sprintf( esc_html__( 'Tries to follow the %sPHP date() function%s specifications, but not every format is supported.', 'ninja-forms' ), '<a href="http://www.php.net/manual/en/function.date.php" target="_blank">', '</a>' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => array(
        'id'      => 'currency',
        'type'    => 'select',
        'options' => Ninja_Forms::config( 'Currency' ),
        'label'   => esc_html__( 'Currency', 'ninja-forms' ),
        'value'   => 'USD'
    ),

));
