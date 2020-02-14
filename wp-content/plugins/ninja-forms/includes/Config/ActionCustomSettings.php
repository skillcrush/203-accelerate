<?php if ( ! defined( 'ABSPATH' ) ) exit;

return array(

    /*
     * TAG
     */

    'tag' => array(
        'name' => 'tag',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => esc_html__( 'Hook Tag', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '',
        'width' => 'full',
        'use_merge_tags' => array(
            'include' => array(
                'calcs',
            ),
        ),
    ),
    
);