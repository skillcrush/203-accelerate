<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_from_action_defaults', array(

    array(
        'id'      => 'tmp-1',
        'label'   => __( 'Success Message', 'ninja-forms' ),
        'type'    => 'successmessage',
        'message' => __( 'Your form has been successfully submitted.', 'ninja-forms' ),
        'order'   => 1,
        'active'  => TRUE,
    ),

    array(
        'id'      => 'tmp-2',
        'label'   => __( 'Admin Email', 'ninja-forms' ),
        'type'    => 'email',
        'order'   => 2,
        'active'  => TRUE,
    ),

    array(
        'id'    => 'tmp-3',
        'label' => __( 'Store Submission', 'ninja-forms' ),
        'type'  => 'save',
        'order' => 3,
        'active'=> TRUE,
    ),

));
