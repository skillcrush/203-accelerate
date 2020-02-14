<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_plugin_settings_groups', array(

    'general' => array(
        'id' => 'general',
        'label' => esc_html__( 'General Settings', 'ninja-forms' ),
    ),

    'recaptcha' => array(
        'id' => 'recaptcha',
        'label' => esc_html__( 'reCaptcha Settings', 'ninja-forms' ),
    ),

    'advanced' => array(
        'id' => 'advanced',
        'label' => esc_html__( 'Advanced Settings', 'ninja-forms' ),
    ),

    'saved_fields' => array(
        'id' => 'saved_fields',
        'label' => esc_html__( 'Favorite Fields', 'ninja-forms' ),
    ),

));
