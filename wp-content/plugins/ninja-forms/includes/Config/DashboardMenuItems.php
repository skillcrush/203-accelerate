<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_dashboard_menu_items', array(

    'widgets' => array(
        'slug' => 'widgets',
        'niceName' => __( 'Forms', 'ninja-forms' ),
    ),
    'services' => array(
        'slug' => 'services',
        'niceName' => __( 'Services', 'ninja-forms' ),
    ),
    'apps' => array(
        'slug' => 'apps',
        'niceName' => __( 'Apps & Integrations', 'ninja-forms' ),
    ),
    'memberships' => array(
        'slug' => 'memberships',
        'niceName' => __( 'Memberships', 'ninja-forms' ),
    )
));
