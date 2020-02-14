<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_from_action_defaults', array(

    array(
        'id'      => 'tmp-1',
        'label'   => esc_html__( 'Success Message', 'ninja-forms' ),
        'type'    => 'successmessage',
        'message' => esc_textarea( __( 'Your form has been successfully submitted.', 'ninja-forms' ) ),
        'order'   => 1,
        'active'  => TRUE,
    ),

    array(
        'id'      => 'tmp-2',
        'label'   => esc_html__( 'Admin Email', 'ninja-forms' ),
        'type'    => 'email',
        'order'   => 2,
        'active'  => TRUE,
    ),

    array(
        'id'    => 'tmp-3',
        'label' => esc_html__( 'Store Submission', 'ninja-forms' ),
        'type'  => 'save',
        'order' => 3,
        'active'=> TRUE,
    ),

));
