<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
    'slug_name' => array(
        //Same as slug name/
        'id'            => 'slug_name',
        'title'         => 'nicename',
        'template-desc' => '',
    ),
 */

return apply_filters( 'ninja_forms_new_form_templates', array(
    'formtemplate-contactform' => array(
       'id'             => 'formtemplate-contactform',
        'title'         => __( 'Contact Us', 'ninja-forms' ),
        'template-desc' => __( 'Allow your users to contact you with this simple contact form. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-quoterequest' => array(
        'id' => 'formtemplate-quoterequest',
        'title' => __( 'Quote Request', 'ninja-forms' ),
        'template-desc' => __( 'Manage quote requests from your website easily with this template. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-eventregistration' => array(
        'id' => 'formtemplate-eventregistration',
        'title' => __( 'Event Registration', 'ninja-forms' ),
        'template-desc' => __( 'Allow user to register for your next event this easy to complete form. You can add and remove fields as needed.', 'ninja-forms' ),
    ),
));