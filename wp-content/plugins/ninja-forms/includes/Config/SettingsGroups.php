<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_field_settings_groups', array(

    'primary' => array(
        'id' => 'primary',
        'label' => '',
        'display' => TRUE,
        'priority' => 100
    ),

    'rte' => array(
        'id' => 'rte',
        'label' => esc_html__( 'Rich Text Editor (RTE)', 'ninja-forms' )
    ),

    'restrictions' => array(
        'id' => 'restrictions',
        'label' => esc_html__( 'Restrictions', 'ninja-forms' )
    ),

    'display' => array(
        'id' => 'display',
        'label' => esc_html__( 'Display', 'ninja-forms' ),
        'priority' => 700
    ),

    'advanced' => array(
        'id' => 'advanced',
        'label' => esc_html__( 'Advanced', 'ninja-forms' ),
        'priority' => 800
    ),

    'administration' => array(
        'id' => 'administration',
        'label' => esc_html__( 'Administration', 'ninja-forms' ),
        'priority' => 900
    )

));
