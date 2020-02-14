<?php if ( ! defined( 'ABSPATH' ) ) exit;

return array(

    /*
    * Message
    */

    'success_msg' => array(
        'name' => 'success_msg',
        'type' => 'rte',
        'group' => 'primary',
        'label' => esc_html__( 'Message', 'ninja-forms' ),
        'placeholder' => '',
        'width' => 'full',
        'value' => esc_textarea( __( 'Your form has been successfully submitted.', 'ninja-forms' ) ),
        'use_merge_tags' => array(
            'include' => array(
                'calcs',
            ),
        ),
    ),

);
