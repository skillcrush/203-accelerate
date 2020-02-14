<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_action_email_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Primary Settings
    |--------------------------------------------------------------------------
    */

    /*
     * To
     */

    'to' => array(
        'name' => 'to',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => esc_html__( 'To', 'ninja-forms' ),
        'placeholder' => esc_attr__( 'Email address or search for a field', 'ninja-forms' ),
        'value' => '{wp:admin_email}',
        'width' => 'one-half',
        'use_merge_tags' => TRUE,
    ),

    /*
     * Reply To
     */

    'reply_to' => array(
        'name' => 'reply_to',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => esc_html__( 'Reply To', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '',
        'width' => 'one-half',
        'use_merge_tags' => TRUE,
    ),

    /*
     * Subject
     */

    'email_subject' => array(
        'name' => 'email_subject',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => esc_html__( 'Subject', 'ninja-forms' ),
        'placeholder' => esc_attr__( 'Subject Text or seach for a field', 'ninja-forms' ),
        'value' => esc_textarea( __( 'Ninja Forms Submission', 'ninja-forms' ) ),
        'width' => 'full',
        'use_merge_tags' => TRUE,
    ),

    /*
     * Email Message
     */

    'email_message' => array(
        'name' => 'email_message',
        'type' => 'rte',
        'group' => 'primary',
        'label' => esc_html__( 'Email Message', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '{fields_table}',
        'width' => 'full',
        'use_merge_tags' => array(
            'include' => array(
                'calcs',
            ),
        ),
        'deps' => array(
            'email_format' => 'html'
        )
    ),

    'email_message_plain' => array(
        'name' => 'email_message_plain',
        'type' => 'textarea',
        'group' => 'primary',
        'label' => esc_html__( 'Email Message', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '',
        'width' => 'full',
        'use_merge_tags' => TRUE,
        'deps' => array(
            'email_format' => 'plain'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Advanced Settings
    |--------------------------------------------------------------------------
    */

    /*
     * From Name
     */

    'from_name' => array(
        'name' => 'from_name',
        'type' => 'textbox',
        'group' => 'advanced',
        'label' => esc_html__( 'From Name', 'ninja-forms' ),
        'placeholder' => esc_attr__( 'Name or fields', 'ninja-forms' ),
        'value' => '',
        'width' => 'one-half',
        'use_merge_tags' => TRUE,
    ),

    /*
     * From Address
     */

    'from_address' => array(
        'name' => 'from_address',
        'type' => 'textbox',
        'group' => 'advanced',
        'label' => esc_html__( 'From Address', 'ninja-forms' ),
        'placeholder' => esc_attr__( 'One email address or field', 'ninja-forms' ),
        'value' => '',
        'use_merge_tags' => TRUE,
    ),

    /*
     * Format
     */

    'email_format' => array(
        'name' => 'email_format',
        'type' => 'select',
            'options' => array(
                array( 'label' => esc_html__( 'HTML', 'ninja-forms' ), 'value' => 'html' ),
                array( 'label' => esc_html__( 'Plain Text', 'ninja-forms' ), 'value' => 'plain' )
            ),
        'group' => 'advanced',
        'label' => esc_html__( 'Format', 'ninja-forms' ),
        'value' => 'html',
        
    ),

    /*
     * Cc
     */

    'cc' => array(
        'name' => 'cc',
        'type' => 'textbox',
        'group' => 'advanced',
        'label' => esc_html__( 'Cc', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '',
        'use_merge_tags' => TRUE,
    ),

    /*
     * Bcc
     */

    'bcc' => array(
        'name' => 'bcc',
        'type' => 'textbox',
        'group' => 'advanced',
        'label' => esc_html__( 'Bcc', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '',
        'use_merge_tags' => TRUE,
    ),

    /*
     * Attach CSV
     */

    'attach_csv' => array(
        'name' => 'attach_csv',
        'type' => 'toggle',
        'group' => 'primary',
        'label' => esc_html__( 'Attach CSV', 'ninja-forms' ),
    ),

    /**
     * File Attachments
     */

     'file_attachment' => array(
         'name' => 'file_attachment',
         'type' => 'media',
         'group' => 'advanced',
         'label' => esc_html__('Add Attachment', 'ninja-forms'),
         'width' => 'full',
         'use_merge_tags' => false,
     )

));
