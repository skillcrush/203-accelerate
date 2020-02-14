<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_from_restriction_settings', array(

    /*
     * UNIQUE FIELD SET
     */
    
    'unique-field-set' => array(
        'name'    => 'unique-field-set',
        'type'    => 'fieldset',
        'label'   => esc_html__( 'Unique Field', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'settings' => array(
            /*
             * SET A UNIQUE FIELD
             */
            'unique_field' => array(
                'name' => 'unique_field',
                'type' => 'field-select',
                'width' => 'full',
                'group' => 'primary',
                'field_value_format' => 'key',
                /* Optional */
                'field_types' => array(
                    'firstname',
                    'lastname',
                    'email',
                    'textbox',
                    'listselect',
                    'listradio',
                    'listmultiselect',
                    'date'
                ),
            ),
            
            /*
             * UNIQUE FIELD ERROR
             */
            'unique_field_error'    => array(
                'name'              => 'unique_field_error',
                'type'              => 'textbox',
                'label'             => esc_html__( 'Unique Field Error Message', 'ninja-forms' ),
                'width'             => 'full',
                'group'             => 'primary',
                'value'             => esc_textarea( __( 'A form with this value has already been submitted.', 'ninja-forms' ) ),
            ),
        )
    ),

    'logged-in-set' => array(
        'name'    => 'logged-in-set',
        'type'    => 'fieldset',
        'label'   => esc_html__( 'Logged In', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'settings' => array(

            /*
             * REQUIRE USER TO BE LOGGED IN TO VIEW FORM?
             */

            'logged_in' => array(
                'name' => 'logged_in',
                'type' => 'toggle',
                'label' => esc_html__( 'Require user to be logged in to view form?', 'ninja-forms' ),
                'width' => 'one-half',
                'group' => 'primary',
                'value' => FALSE,
                'help' => esc_html__( 'Does apply to form preview.', 'ninja-forms' )
            ),

            /*
             * NOT LOGGED-IN MESSAGE
             */

            'not_logged_in_msg' => array(
                'name' => 'not_logged_in_msg',
                'type' => 'rte', //TODO: Add WYSIWYG
                'label' => esc_html__( 'Not Logged-In Message', 'ninja-forms' ),
                'width' => 'full',
                'group' => 'primary',
                'value' => '',
            ),
        )
    ),

    'limit-submissions-set' => array(
        'name'    => 'limit-submissions-set',
        'type'    => 'fieldset',
        'label'   => esc_html__( 'Limit Submissions', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'settings' => array(

            /*
             * LIMIT SUBMISSIONS
             */

            'sub_limit_number' => array(
                'name' => 'sub_limit_number',
                'type' => 'number',
                'label' => esc_html__( 'Submission Limit', 'ninja-forms' ),
                'width' => 'one-third',
                'group' => 'primary',
                'value' => NULL,
                'help' => esc_html__( 'Does NOT apply to form preview.', 'ninja-forms' )

                //TODO: Add following text below the element.
                //Select the number of submissions that this form will accept. Leave empty for no limit.
            ),

            /*
             * LIMIT REACHED MESSAGE
             */

            'sub_limit_msg' => array(
                'name' => 'sub_limit_msg',
                'type' => 'rte',//TODO: Add WYSIWYG
                'label' => esc_html__( 'Limit Reached Message', 'ninja-forms' ),
                'width' => 'full',
                'group' => 'primary',
                'value' => esc_textarea( __( 'The form has reached its submission limit.', 'ninja-forms' ) )

                //TODO: Add following text below the WYSIWYG.
                //Please enter a message that you want displayed when this form has reached its submission limit and will not
                //accept new submissions.
            ),
        )
    ),
));
