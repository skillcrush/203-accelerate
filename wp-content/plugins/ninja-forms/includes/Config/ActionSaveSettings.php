<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_action_email_settings', array(

    /*
     * To
     */
	'submitter_email' => array(
		'name' => 'submitter_email',
		'type' => 'email-select',
		'options' => array(),
		'group' => 'primary',
		'label' => __( 'Designated Submitter\'s Email Address', 'ninja-forms' ),
		'value' => '',
		'help' => __( 'The email address used in this field will be allowed to '
			. 'make data export and delete requests on behalf of their form submission.', 'ninja-forms' ),
	),

    'fields_save_toggle' => array(
        'name' => 'fields-save-toggle',
        'type' => 'button-toggle',
        'width' => 'full',
        'options' => array(
            array( 'label' => __( 'Save All', 'ninja-forms' ), 'value' => 'save_all' ),
            array( 'label' => __( 'Save None', 'ninja-forms' ), 'value' => 'save_none' )
        ),
        'group' => 'primary',
        'label' => __( 'Fields', 'ninja-forms' ),
        'value' => 'save_all',
    ),
	/*
    |--------------------------------------------------------------------------
    | Exception Field
    |--------------------------------------------------------------------------
    */

    'exception_fields' => array(
	    'name'      => 'exception_fields',
	    'type'      => 'option-repeater',
	    'label'     => __( 'Except', 'ninja-forms' ) . ' <a href="#" class="nf-add-new">' .
	                   __( 'Add New', 'ninja-forms' ) . '</a>',
	    'width'     => 'full',
	    'group'     => 'primary',
	    'tmpl_row'  => 'nf-tmpl-save-field-repeater-row',
	    'value'     => array(),
	    'columns'   => array(
		    'form_field' => array(
			    'header' => __( 'Form Field', 'ninja-forms' ),
			    'default' => '',
			    'options' => array(),
		    ),
	    ),
    ),

    /*
    * Set subs to expire.
    */
    'set_subs_to_expire' => array(
        'name' => 'set_subs_to_expire',
        'type' => 'toggle',
        'group' => 'advanced',
        'label' => __( 'Set Submissions to expire?', 'ninja-forms' ),
        'value' => 0,
        'help'  => __( 'Sets submissions to be trashes after a certain number of days, it affects all existing and new submissions', 'ninja-forms' ),
        'width' => 'one-half',
    ),

    /*
    * Subs expire in?
    */
    'subs_expire_time' => array(
        'name' => 'subs_expire_time',
        'type' => 'number',
        'group' => 'advanced',
        'label' => __( 'How long in days until subs expire?', 'ninja-forms' ),
        'value' => '90',
        'min_val' => 1, // new minimum value setting
        'max_val' => null, // new maximum value setting
        'width' => 'one-half',
        'deps'  => array(
            'set_subs_to_expire' => 1
        )
    ),
));

