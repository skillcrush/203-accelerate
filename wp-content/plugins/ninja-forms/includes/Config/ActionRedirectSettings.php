<?php if ( ! defined( 'ABSPATH' ) ) exit;

return array(

    /*
    * Redirect URL
    */

    'redirect_url' => array(
        'name' => 'redirect_url',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => esc_html__( 'URL', 'ninja-forms' ),
        'placeholder' => '',
        'width' => 'full',
        'value' => '',
        'use_merge_tags' => array(
            'include' => array(
                'calcs',
            ),
        ),
    ),

);
