<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_dashboard_menu_items', array(

    'widgets' => array(
        'slug' => 'widgets',
        'niceName' => esc_html__( 'Forms', 'ninja-forms' ),
    ),
    'services' => array(
        'slug' => 'services',
        'niceName' => esc_html__( 'Services', 'ninja-forms' ),
    ),
    'apps' => array(
        'slug' => 'apps',
        'niceName' => esc_html__( 'Apps & Integrations', 'ninja-forms' ),
    ),
));
