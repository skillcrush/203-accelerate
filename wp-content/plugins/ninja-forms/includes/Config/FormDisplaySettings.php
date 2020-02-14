<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_form_display_settings', array(

    /*
    * FORM TITLE
    */

    'title' => array(
        'name' => 'title',
        'type' => 'textbox',
        'label' => esc_html__( 'Form Title', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'value' => '',

    ),

    /*
    * SHOW FORM TITLE
    */

    'show_title' => array(
        'name' => 'show_title',
        'type' => 'toggle',
        'label' => esc_html__( 'Display Form Title', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'value' => 1,

    ),

    /*
    * ALLOW PUBLIC LINK
    */

    'allow_public_link' => array(
        'name' => 'allow_public_link',
        'type' => 'toggle',
        'label' => esc_html__( 'Allow a public link?', 'ninja-forms' ),
        'width' => 'full',
        'group' => '',
        'value' => 0,
        'help'  => esc_html__( 'If this box is checked, Ninja Forms will create a public link to access the form.', 'ninja-forms' ),
    ),

    'public_link' => array(
        'name' => 'public_link',
        'type' => 'copyresettext',
        'label' => esc_html__( 'Link To Your Form', 'ninja-forms' ),
        'width' => 'full',
        'group' => '',
        'help'  => esc_html__( 'A public link to access the form.', 'ninja-forms' ),
        'deps' => array(
            'allow_public_link' => 1
        ),
    ),

    'embed_form' => array(
        'name' => 'embed_form',
        'type' => 'copytext',
        'label' => esc_html__( 'Embed Your Form', 'ninja-forms' ),
        'width' => 'full',
        'group' => '',
        'value' => '',
        'help'  => esc_html__( 'The shortcode you can use to embed this form on a page or post.', 'ninja-forms' ),
    ),

    /*
    * CLEAR SUCCESSFULLY COMPLETED FORM
    */

    'clear_complete' => array(
        'name' => 'clear_complete',
        'type' => 'toggle',
        'label' => esc_html__( 'Clear successfully completed form?', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'value' => 1,
        'help'  => esc_html__( 'If this box is checked, Ninja Forms will clear the form values after it has been successfully submitted.', 'ninja-forms' ),
    ),

    /*
    * HIDE SUCCESSFULLY COMPLETED FORMS
    */

    'hide_complete' => array(
        'name' => 'hide_complete',
        'type' => 'toggle',
        'label' => esc_html__( 'Hide successfully completed form?', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'value' => 1,
        'help'  => esc_html__( 'If this box is checked, Ninja Forms will hide the form after it has been successfully submitted.', 'ninja-forms' ),
    ),

    /*
    * Default Label Position
    */

    'default_label_pos' => array(
        'name' => 'default_label_pos',
        'type' => 'select',
        'label' => esc_html__( 'Default Label Position', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'advanced',
        'options' => array(
            array(
                'label' => esc_html__( 'Above Element', 'ninja-forms' ),
                'value' => 'above'
            ),
            array(
                'label' => esc_html__( 'Below Element', 'ninja-forms' ),
                'value' => 'below'
            ),
            array(
                'label' => esc_html__( 'Left of Element', 'ninja-forms' ),
                'value' => 'left'
            ),
            array(
                'label' => esc_html__( 'Right of Element', 'ninja-forms' ),
                'value' => 'right'
            ),
            array(
                'label' => esc_html__( 'Hidden', 'ninja-forms' ),
                'value' => 'hidden'
            ),
        ),
        'value' => 'above',
    ),

    /*
     * Classes
     */

    'classes' => array(
        'name' => 'classes',
        'type' => 'fieldset',
        'label' => esc_html__( 'Custom Class Names', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'advanced',
        'settings' => array(
            array(
                'name' => 'wrapper_class',
                'type' => 'textbox',
                'placeholder' => '',
                'label' => esc_html__( 'Wrapper', 'ninja-forms' ),
                'width' => 'one-half',
                'value' => '',
                'use_merge_tags' => FALSE,
            ),
            array(
                'name' => 'element_class',
                'type' => 'textbox',
                'label' => esc_html__( 'Element', 'ninja-forms' ),
                'placeholder' => '',
                'width' => 'one-half',
                'value' => '',
                'use_merge_tags' => FALSE,
            ),
        ),
    ),

    /*
     * KEY
     */

    'key' => array(
        'name' => 'key',
        'type' => 'textbox',
        'label' => esc_html__( 'Form Key', 'ninja-forms'),
        'width' => 'full',
        'group' => 'administration',
        'value' => '',
        'help' => esc_html__( 'Programmatic name that can be used to reference this form.', 'ninja-forms' ),
    ),

    /*
     * ADD SUBMIT CHECKBOX
     */

    'add_submit' => array(
        'name' => 'add_submit',
        'type' => 'toggle',
        'label' => esc_html__( 'Add Submit Button', 'ninja-forms'),
        'width' => 'full',
        'group' => '',
        'value' => 1,
        'help' => esc_html__( 'We have noticed that you do not have a submit button on your form. We can add one for you automatically.', 'ninja-forms' ),
    ),

    /*
     * Form Labels
     */

    'custom_messages' => array(
        'name' => 'custom_messages',
        'type' => 'fieldset',
        'label' => esc_html__( 'Custom Labels', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'advanced',
        'settings' => array(
            array(
                'name' => 'changeEmailErrorMsg',
                'type' => 'textbox',
                'label' => esc_html__( 'Please enter a valid email address!', 'ninja-forms' ),
                'width' => 'full'
            ),
	        array(
		        'name' => 'changeDateErrorMsg',
		        'type' => 'textbox',
		        'label' => esc_html__( 'Please enter a valid date!', 'ninja-forms' ),
		        'width' => 'full'
	        ),
            array(
                'name' => 'confirmFieldErrorMsg',
                'type' => 'textbox',
                'label' => esc_html__( 'These fields must match!', 'ninja-forms' ),
                'width' => 'full'
            ),
            array(
                'name' => 'fieldNumberNumMinError',
                'type' => 'textbox',
                'label' => esc_html__( 'Number Min Error', 'ninja-forms' ),
                'width' => 'full'
            ),
            array(
                'name' => 'fieldNumberNumMaxError',
                'type' => 'textbox',
                'label' => esc_html__( 'Number Max Error', 'ninja-forms' ),
                'width' => 'full'
            ),
            array(
                'name' => 'fieldNumberIncrementBy',
                'type' => 'textbox',
                'label' => esc_html__( 'Please increment by ', 'ninja-forms' ),
                'width' => 'full'
            ),
            array(
                'name' => 'formErrorsCorrectErrors',
                'type' => 'textbox',
                'label' => esc_html__( 'Please correct errors before submitting this form.', 'ninja-forms' ),
                'width' => 'full'
            ),
            array(
                'name' => 'validateRequiredField',
                'type' => 'textbox',
                'label' => esc_html__( 'This is a required field.', 'ninja-forms' ),
                'width' => 'full'
            ),
            array(
                'name' => 'honeypotHoneypotError',
                'type' => 'textbox',
                'label' => esc_html__( 'Honeypot Error', 'ninja-forms' ),
                'width' => 'full'
            ),
            array(
                'name' => 'fieldsMarkedRequired',
                'type' => 'textbox',
                'label' => sprintf( esc_html__( 'Fields marked with an %s*%s are required', 'ninja-forms' ), '<span class="ninja-forms-req-symbol">', '</span>' ),
                'width' => 'full'
            ),
        )
    ),

    /*
     * CURRENCY
     */

    'currency' => array(
        'name'      => 'currency',
        'type'    => 'select',
        'options' => array_merge( array( array( 'label' => esc_html__( 'Plugin Default', 'ninja-forms' ), 'value' => '' ) ), Ninja_Forms::config( 'Currency' ) ),
        'label'   => esc_html__( 'Currency', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'advanced',
        'value'   => ''
    ),

));
