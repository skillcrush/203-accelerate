<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_field_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Primary Settings
    |--------------------------------------------------------------------------
    |
    | The most commonly used settings for a field.
    |
    */

    /*
     * LABEL
     */

    'label' => array(
        'name' => 'label',
        'type' => 'textbox',
        'label' => esc_html__( 'Label', 'ninja-forms'),
        'width' => 'one-half',
        'group' => 'primary',
        'value' => '',
        'help' => esc_html__( 'Enter the label of the form field. This is how users will identify individual fields.', 'ninja-forms' ),
    ),

    /*
     * LABEL POSITION
     */

    'label_pos' => array(
        'name' => 'label_pos',
        'type' => 'select',
        'label' => esc_html__( 'Label Position', 'ninja-forms' ),
        'options' => array(
            array(
                'label' => esc_html__( 'Form Default', 'ninja-forms' ),
                'value' => 'default'
            ),
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
        'width' => 'one-half',
        'group' => 'advanced',
        'value' => 'default',
        'help' => esc_html__( 'Select the position of your label relative to the field element itself.', 'ninja-forms' ),

    ),

    /*
     * REQUIRED
     */

    'required' => array(
        'name' => 'required',
        'type' => 'toggle',
        'label' => esc_html__( 'Required Field', 'ninja-forms' ),
        'width' => 'one-half',
        'group' => 'primary',
        'value' => FALSE,
        'help' => esc_html__( 'Ensure that this field is completed before allowing the form to be submitted.', 'ninja-forms' ),
    ),

    /*
     * NUMBER
     */

    'number' => array(
        'name' => 'number',
        'type' => 'fieldset',
        'label' => esc_html__( 'Number Options', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'settings' => array(
            array(
                'name' => 'num_min',
                'type' => 'number',
                'placeholder' => '',
                'label' => esc_html__( 'Min', 'ninja-forms' ),
                'width' => 'one-third',
                'value' => ''
            ),
            array(
                'name' => 'num_max',
                'type' => 'number',
                'label' => esc_html__( 'Max', 'ninja-forms' ),
                'placeholder' => '',
                'width' => 'one-third',
                'value' => ''
            ),
            array(
                'name' => 'num_step',
                'type' => 'textbox',
                'label' => esc_html__( 'Step', 'ninja-forms' ),
                'placeholder' => '',
                'width' => 'one-third',
                'value' => 1
            ),
        ),

    ),

    /*
     * Checkbox Default Value
     */

    'checkbox_default_value' => array(
        'name' => 'default_value',
        'type' => 'select',
        'label' => esc_html__( 'Default Value', 'ninja-forms' ),
        'options' => array(
            array(
                'label' => esc_html__( 'Unchecked', 'ninja-forms' ),
                'value' => 'unchecked'
            ),
            array(
                'label' => esc_html__( 'Checked', 'ninja-forms'),
                'value' => 'checked',
            ),
        ),
        'width' => 'one-half',
        'group' => 'primary',
        'value' => 'unchecked',

    ),

    /*
    * Checkbox Values
    */
    'checkbox_values' => array(
        'name' => 'checkbox_values',
        'type' => 'fieldset',
        'label' => esc_html__( 'Checkbox Values', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'settings' => array(
            array(
                'name'  => 'checked_value',
                'type'  => 'textbox',
                'label' => esc_html__( 'Checked Value', 'ninja-forms' ),
                'value' => esc_textarea( __( 'Checked', 'ninja-forms' ) ),
                'width' => 'one-half',
            ),
            array(
                'name'  => 'unchecked_value',
                'type'  => 'textbox',
                'label' => esc_html__( 'Unchecked Value', 'ninja-forms' ),
                'value' => esc_textarea( __( 'Unchecked', 'ninja-forms' ) ),
                'width' => 'one-half',
            ),
        ),
    ),

    /*
     * List Display Style
     */
    'list_orientation' => array(
        'name' => 'list_orientation',
        'type' => 'button-toggle',
        'width' => 'full',
        'group' => 'primary',
        'options' => array(
            array( 'label' => esc_html__( 'Horizontal', 'ninja-forms' ), 'value' => 'horizontal' ),
            array( 'label' => esc_html__( 'Vertical', 'ninja-forms' ), 'value' => 'vertical' )
        ),
        'label' => esc_html__( 'List Orientation', 'ninja-forms' ),
        'value' => 'horizontal',
    ),

    /*
     * Max Columns
     */
    'num_columns'       => array(
        'name'          => 'num_columns',
        'type'          => 'number',
        'label'         => esc_html__( 'Number of Columns', 'ninja-forms'),
        'width'         => 'one-half',
        'group'         => 'primary',
        'value'         => 3,
        'deps'          => array(
            'list_orientation' => 'horizontal'
        ),
    ),

    /*
     * Allow multi-select
     */
    'allow_multi_select' => array(
        'name' => 'allow_multi_select',
        'type' => 'toggle',
        'label' => esc_html__( 'Allow Multiple Selections', 'ninja-forms' ),
        'width' => 'one-half',
        'group' => 'primary',
        'value' => FALSE,
    ),

    /*
     * Show option labels
     */
    'show_option_labels' => array(
        'name' => 'show_option_labels',
        'type' => 'toggle',
        'label' => esc_html__( 'Show Labels', 'ninja-forms' ),
        'width' => 'one-half',
        'group' => 'primary',
        'value' => TRUE,
    ),

    /*
     * OPTIONS
     */

    'options' => array(
        'name' => 'options',
        'type' => 'option-repeater',
        'label' => esc_html__( 'Options', 'ninja-forms' ) . ' <a href="#" class="nf-add-new">' . esc_html__( 'Add New', 'ninja-forms' ) . '</a> <a href="#" class="extra nf-open-import-tooltip"><i class="fa fa-sign-in" aria-hidden="true"></i> ' . esc_html__( 'Import', 'ninja-forms' ) . '</a>',
        'width' => 'full',
        'group' => 'primary',
        // 'value' => 'option-repeater',
        'value' => array(
            array( 'label'  => esc_html__( 'One', 'ninja-forms' ), 'value' => esc_textarea( __( 'one', 'ninja-forms' ) ), 'calc' => '', 'selected' => 0, 'order' => 0 ),
            array( 'label'  => esc_html__( 'Two', 'ninja-forms' ), 'value' => esc_textarea( __( 'two', 'ninja-forms' ) ), 'calc' => '', 'selected' => 0, 'order' => 1 ),
            array( 'label'  => esc_html__( 'Three', 'ninja-forms' ), 'value' => esc_textarea( __( 'three', 'ninja-forms' ) ), 'calc' => '', 'selected' => 0, 'order' => 2 ),
        ),
        'columns'           => array(
            'label'          => array(
                'header'    => esc_html__( 'Label', 'ninja-forms' ),
                'default'   => '',
            ),

            'value'         => array(
                'header'    => esc_html__( 'Value', 'ninja-forms' ),
                'default'   => '',
            ),
            'calc'          => array(
                'header'    => esc_html__( 'Calc Value', 'ninja-forms' ),
                'default'   => '',
            ),
            'selected'      => array(
                'header'    => '<span class="dashicons dashicons-yes"></span>',
                'default'   => 0,
            ),
        ),

    ),

    /*
     * IMAGE OPTIONS
     */

    'image_options' => array(
        'name' => 'image_options',
        'type' => 'image-option-repeater',
        'label' => esc_html__( 'Image Options', 'ninja-forms' ) . ' <a href="#" class="nf-add-new">' . esc_html__( 'Add New', 'ninja-forms' ) . '</a>',
        'width' => 'full',
        'group' => 'primary',
        // 'value' => 'option-repeater',
        'value' => array(
            array( 'label' => '', 'image' => '', 'value' => '', 'image_id' => '', 'calc' => '', 'selected' => 0, 'order' => 0 ),
            array( 'label' => '', 'image' => '', 'value' => '', 'image_id' => '', 'calc' => '', 'selected' => 0, 'order' => 1 ),
            array( 'label' => '', 'image' => '', 'value' => '', 'image_id' => '', 'calc' => '', 'selected' => 0, 'order' => 2 ),
        ),
        'columns'           => array(
            'label'          => array(
                'header'    => esc_html__( 'Label', 'ninja-forms' ),
                'default'   => '',
            ),
            'value'         => array(
                'header'    => esc_html__( 'Value', 'ninja-forms' ),
                'default'   => '',
            ),
            'calc'          => array(
                'header'    => esc_html__( 'Calc Value', 'ninja-forms' ),
                'default'   => '',
            ),
            'selected'      => array(
                'header'    => '<span class="dashicons dashicons-yes"></span>',
                'default'   => 0,
            ),
        ),

    ),

    /*
    |--------------------------------------------------------------------------
    | Restriction Settings
    |--------------------------------------------------------------------------
    |
    | Limit the behavior or validation of an input.
    |
    */

    /*
     * MASK
     */

    'mask' => array(
        'name' => 'mask',
        'type' => 'select',
        'label' => esc_html__( 'Input Mask', 'ninja-forms'),
        'width' => 'one-half',
        'group' => 'restrictions',
        'help'  => esc_html__( 'Restricts the kind of input your users can put into this field.', 'ninja-forms' ),
        'options' => array(
            array(
                'label' => esc_html__( 'none', 'ninja-forms' ),
                'value' => ''
            ),
            array(
                'label' => esc_html__( 'US Phone', 'ninja-forms' ),
                'value' => '(999) 999-9999',
            ),
            array(
                'label' => esc_html__( 'Date', 'ninja-forms' ),
                'value' => '99/99/9999',
            ),
            array(
                'label' => esc_html__( 'Currency', 'ninja-forms' ),
                'value' => 'currency',
            ),
            array(
                'label' => esc_html__( 'Custom', 'ninja-forms' ),
                'value' => 'custom',
            ),
        ),
        'value' => '',
    ),
    /*
     * CUSTOM MASK
     */

    'custom_mask'       => array(
        'name'          => 'custom_mask',
        'type'          => 'textbox',
        'label'         => esc_html__( 'Custom Mask', 'ninja-forms'),
        'width'         => 'one-half',
        'group'         => 'restrictions',
        'value'         => '',

        'deps'          => array(
            'mask'      => 'custom'
        ),
        'placeholder'   => '',
        'help'  => sprintf( '%s' . esc_html__( 'a - Represents an alpha character (A-Z,a-z) - Only allows letters to be entered.', 'ninja-forms' ) .
                            '%s' . esc_html__( '9 - Represents a numeric character (0-9) - Only allows numbers to be entered.', 'ninja-forms' ) .
                            '%s' . esc_html__( '* - Represents an alphanumeric character (A-Z,a-z,0-9) - This allows both numbers and letters to be entered.', 'ninja-forms' ) .
                            '%s', '<ul><li>', '</li><li>', '</li><li>', '</li></ul>' ),
    ),

    /*
     * INPUT LIMIT SET
     */

    'input_limit_set' => array(
        'name' => 'input_limit_set',
        'type' => 'fieldset',
        'label' => esc_html__( 'Limit Input to this Number', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'restrictions',
        'settings' => array(
            array(
                'name' => 'input_limit',
                'type' => 'textbox',
                'width' => 'one-half',
                'value' => '',
                'label' => '',
            ),
            array(
                'name' => 'input_limit_type',
                'type' => 'select',
                'options' => array(
                    array(
                        'label' => esc_html__( 'Character(s)', 'ninja-forms' ),
                        'value' => 'characters'
                    ),
                    array(
                        'label' => esc_html__( 'Word(s)', 'ninja-forms' ),
                        'value' => 'word'
                    ),
                ),
                'value' => 'characters',
                'label' => '',
            ),
            array(
                'name' => 'input_limit_msg',
                'type' => 'textbox',
                'label' => esc_html__( 'Text to Appear After Counter', 'ninja-forms' ),
                'placeholder' => esc_attr__( 'Character(s) left', 'ninja-forms' ),
                'width' => 'full',
                'value' => esc_textarea( __( 'Character(s) left', 'ninja-forms' ) )
            )
        ),

    ),

    /*
    |--------------------------------------------------------------------------
    | Advanced Settings
    |--------------------------------------------------------------------------
    |
    | The least commonly used settings for a field.
    | These settings should only be used for specific reasons.
    |
    */

    /*
     * Custom Name Attribute
     */

    'custom_name_attribute' => array(
        'name' => 'custom_name_attribute',
        'type' => 'textbox',
        'label' => esc_html__( 'Custom Name Attribute', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'advanced',
        'value' => '',
        'help' => esc_html__( 'This value will be used as the HTML input "name" attribute.', 'ninja-forms' ),
        'use_merge_tags' => FALSE,
    ),

    /*
     * INPUT PLACEHOLDER
     */

    'placeholder' => array(
        'name' => 'placeholder',
        'type' => 'textbox',
        'label' => esc_html__( 'Placeholder', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'display',
        'value' => '',
        'help' => esc_html__( 'Enter text you would like displayed in the field before a user enters any data.', 'ninja-forms' ),
        'use_merge_tags' => FALSE,
    ),


    /*
     * DEFAULT VALUE
     */

    'default' => array(
        'name' => 'default',
        'label' => esc_html__( 'Default Value', 'ninja-forms' ),
        'type' => 'textbox',
        'width' => 'full',
        'value' => '',
        'group' => 'display',
        'use_merge_tags' => array(
            'exclude' => array(
                'fields'
            )
        ),
    ),

    /*
    * CLASSES
    */
    'classes' => array(
        'name' => 'classes',
        'type' => 'fieldset',
        'label' => esc_html__( 'Custom Class Names', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'display',
        'settings' => array(
            array(
                'name' => 'container_class',
                'type' => 'textbox',
                'placeholder' => '',
                'label' => esc_html__( 'Container', 'ninja-forms' ),
                'width' => 'one-half',
                'value' => '',
                'use_merge_tags' => FALSE,
                'help' => esc_html__( 'Adds an extra class to your field wrapper.', 'ninja-forms' ),
            ),
            array(
                'name' => 'element_class',
                'type' => 'textbox',
                'label' => esc_html__( 'Element', 'ninja-forms' ),
                'placeholder' => '',
                'width' => 'one-half',
                'value' => '',
                'use_merge_tags' => FALSE,
                'help' => esc_html__( 'Adds an extra class to your field element.', 'ninja-forms' ),
            ),
        ),
    ),

    /*
     * DATE FORMAT
     */

    'date_format'        => array(
        'name'          => 'date_format',
        'type'          => 'select',
        'label'         => esc_html__( 'Format', 'ninja-forms' ),
        'width'         => 'full',
        'group'         => 'primary',
        'options'       => array(
            array(
                'label' => esc_html__( 'Default', 'ninja-forms' ),
                'value' => 'default',
            ),
            array(
                'label' => esc_html__( 'DD/MM/YYYY', 'ninja-forms' ),
                'value' => 'DD/MM/YYYY',
            ),
            array(
                'label' => esc_html__( 'DD-MM-YYYY', 'ninja-forms' ),
                'value' => 'DD-MM-YYYY',
            ),
            array(
                'label' => esc_html__( 'DD.MM.YYYY', 'ninja-forms' ),
                'value' => 'DD.MM.YYYY',
            ),
            array(
                'label' => esc_html__( 'MM/DD/YYYY', 'ninja-forms' ),
                'value' => 'MM/DD/YYYY',
            ),
            array(
                'label' => esc_html__( 'MM-DD-YYYY', 'ninja-forms' ),
                'value' => 'MM-DD-YYYY',
            ),
            array(
                'label' => esc_html__( 'MM.DD.YYYY', 'ninja-forms' ),
                'value' => 'MM.DD.YYYY',
            ),
            array(
                'label' => esc_html__( 'YYYY-MM-DD', 'ninja-forms' ),
                'value' => 'YYYY-MM-DD',
            ),
            array(
                'label' => esc_html__( 'YYYY/MM/DD', 'ninja-forms' ),
                'value' => 'YYYY/MM/DD',
            ),
            array(
                'label' => esc_html__( 'YYYY.MM.DD', 'ninja-forms' ),
                'value' => 'YYYY.MM.DD',
            ),
            array(
                'label' => esc_html__( 'Friday, November 18, 2019', 'ninja-forms' ),
                'value' => 'dddd, MMMM D YYYY',
            ),
        ),
        'value'         => 'default',
    ),

    /*
     * DATE DEFAULT
     */

    'date_default'       => array(
        'name'          => 'date_default',
        'type'          => 'toggle',
        'label'         => esc_html__( 'Default To Current Date', 'ninja-forms' ),
        'width'         => 'one-half',
        'group'         => 'primary'
    ),

    /*
     * Year Range
     */

    'year_range' => array(
        'name' => 'year_range',
        'type' => 'fieldset',
        'label' => esc_html__( 'Year Range', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'advanced',
        'settings' => array(
            array(
                'name' => 'year_range_start',
                'type' => 'number',
                'label' => esc_html__( 'Start Year', 'ninja_forms' ),
                'value' => ''
            ),
            array(
                'name' => 'year_range_end',
                'type' => 'number',
                'label' => esc_html__( 'End Year', 'ninja_forms' ),
                'value' => ''
            ),
        )
    ),

    /*
     * TIME SETTING
     */

    'time_submit' => array(
        'name' => 'time_submit',
        'type' => 'textbox',
        'label' => esc_html__( 'Number of seconds for timed submit.', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'advanced',
        'value' => FALSE,

    ),

    /*
     * KEY
     */

    'key' => array(
        'name' => 'key',
        'type' => 'textbox',
        'label' => esc_html__( 'Field Key', 'ninja-forms'),
        'width' => 'full',
        'group' => 'administration',
        'value' => '',
        'help' => esc_html__( 'Creates a unique key to identify and target your field for custom development.', 'ninja-forms' ),
    ),

    /*
     * ADMIN LABEL
     */

    'admin_label'           => array(
        'name'              => 'admin_label',
        'type'              => 'textbox',
        'label'             => esc_html__( 'Admin Label', 'ninja-forms' ),
        'width'             => 'full',
        'group'             => 'administration',
        'value'             => '',
        'help'              => esc_html__( 'Label used when viewing and exporting submissions.', 'ninja-forms' ),
    ),

    /*
     * HELP
     */

    'help'           => array(
        'name'              => 'help',
        'type'              => 'fieldset',
        'label'             => esc_html__( 'Help Text', 'ninja-forms' ),
        'group'             => 'display',
        'help'              => esc_html__( 'Shown to users as a hover.', 'ninja-forms' ),
        'settings'          => array(
            /*
             * HELP TEXT
             */

            'help_text'             => array(
                'name'              => 'help_text',
                'type'              => 'rte',
                'label'             => '',
                'width'             => 'full',
                'group'             => 'advanced',
                'value'             => '',
                'use_merge_tags'    => true,
            ),
        ),
    ),


    /*
     * DESCRIPTION
     */
    'description'           => array(
        'name'              => 'description',
        'type'              => 'fieldset',
        'label'             => esc_html__( 'Description', 'ninja-forms' ),
        'group'             => 'display',
        'settings'          => array(
            /*
             * DESCRIPTION TEXT
             */

            'desc_text'           => array(
                'name'              => 'desc_text',
                'type'              => 'rte',
                'label'             => '',
                'width'             => 'full',
                'use_merge_tags'    => true,
            ),
        ),
    ),

    /*
     * NUMERIC SORT
     */

    'num_sort' => array(
        'name' => 'num_sort',
        'type' => 'toggle',
        'label' => esc_html__( 'Sort as Numeric', 'ninja-forms'),
        'width' => 'full',
        'group' => 'administration',
        'value' => '',
        'help' => esc_html__( 'This column in the submissions table will sort by number.', 'ninja-forms' ),
    ),

    'personally_identifiable'   => array(
	    'name'           => 'personally_identifiable',
	    'type'           => 'toggle',
	    'group'          => 'advanced',
	    'label'          => esc_html__( 'This Field Is Personally Identifiable Data', 'ninja-forms' ),
	    'width'          => 'full',
	    'value'          => '',
	    'help'           => esc_html__( 'This option helps with privacy regulation compliance', 'ninja-forms' ),
    ),

    /*
     |--------------------------------------------------------------------------
     | Display Settings
     |--------------------------------------------------------------------------
     */

    // Multi-Select List Only
    'multi_size' => array(
        'name' => 'multi_size',
        'type' => 'number',
        'label' => esc_html__( 'Multi-Select Box Size', 'ninja-forms'),
        'width' => 'one-half',
        'group' => 'advanced',
        'value' => 5,
    ),

    /*
    |--------------------------------------------------------------------------
    | Un-Grouped Settings
    |--------------------------------------------------------------------------
    |
    | Hidden from grouped listings, but still searchable.
    |
    */

    'manual_key' => array(
        'name' => 'manual_key',
        'type' => 'bool',
        'value' => FALSE,
    ),

    'timed_submit_label' => array(
        'name' => 'timed_submit_label',
        'type' => 'textbox',
        'label' => esc_html__( 'Label', 'ninja-forms' ),
        //The following text appears below the element
        //'Submit button text after timer expires'
        'width' => '',
        'group' => '',
        'value' => '',
        'use_merge_tags' => TRUE,
    ),

    /*
     * Timed Submit Timer
     */

    'timed_submit_timer' => array(
        'name' => 'timed_submit_timer',
        'type' => 'textbox',
        'label' => esc_html__( 'Label' , 'ninja-forms' ),
        // This text was located below the element '%n will be used to signfify the number of seconds'
        'value' => sprintf( esc_textarea( __( 'Please wait %s seconds', 'ninja-forms' ) ), '%n'),
        'width' => '',
        'group' => '',

    ),

    /*
     * Timed Submit Countdown
     */

    'timed_submit_countdown' => array (
        'name' => 'timed_submit_countdown',
        'type' => 'number',
        'label' => esc_html__( 'Number of seconds for the countdown', 'ninja-forms' ),
        //The following text appears to the right of the element
        //"This is how long the user must waitin to submit the form"
        'value' => 10,
        'width' => '',
        'group' => '',

    ),

    /*
     * Password Registration checkbox
     */

    'password_registration_checkbox' => array(
        'name' => 'password_registration_checkbox',
        'type' => 'checkbox',
        'value' => 'unchecked',
        'label' => esc_html__( 'Use this as a registration password field. If this box is check, both
                        password and re-password textboxes will be output', 'ninja-forms' ),
        'width' => '',
        'group' => '',

    ),


    /*
     * Number of Stars Textbox
     */

    'number_of_stars' => array(
        'name' => 'number_of_stars',
        'type' => 'textbox',
        'value' => 5,
        'label' => esc_html__( 'Number of stars', 'ninja-forms' ),
        'width' => 'full',
        'group' => '',

    ),

    /*
     * Disable Browser Autocomplete
     */

    'disable_browser_autocomplete' => array(
        'name' => 'disable_browser_autocomplete',
        'type' => 'toggle',
        'label' => esc_html__( 'Disable Browser Autocomplete', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'restrictions',
    ),

    /*
     * Disable input
     */

    'disable_input' => array(
        'name'      => 'disable_input',
        'type'      => 'toggle',
        'label'     => esc_html__( 'Disable Input', 'ninja-forms' ),
        'width'     => 'full',
        'group'     => 'restrictions',
    ),

    //TODO: Ask about the list of states and countries.
    /*
     *  Country - Use Custom First Option
     */

    'use_custom_first_option' => array(
        'name' => 'use_custom_first_option',
        'type' => 'checkbox',
        'value' => 'unchecked',
        'label' => esc_html__( 'Use a custom first option', 'ninja-forms' ),
        'width' => '',
        'group' => '',

    ),

    /*
     * Country - Custom first option
     */

    'custom_first_option' => array(
        'name' => 'custom_first_option',
        'type' => 'textbox',
        'label' => esc_html__( 'Custom first option', 'ninja-forms' ),
        'width' => '',
        'group' => '',
        'value' => FALSE,

    ),

    'type'         => array(
        'name'              => 'type',
        'type'              => 'select',
        'options'           => array(),
        'label'             => esc_html__( 'Type', 'ninja-forms' ),
        'width'             => 'full',
        'group'             => 'primary',
        'value'             => 'single',
    ),

    'fieldset' => array(
        'name' => 'fieldset',
        'type' => 'fieldset',
        'label' => esc_html__( 'Settings', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'settings' => array(),
    ),

    'confirm_field' => array(
        'name' => 'confirm_field',
        'type' => 'field-select',
        'label' => esc_html__( 'Confirm', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary'
    ),

    /*
    |--------------------------------------------------------------------------
    | Textarea Settings
    |--------------------------------------------------------------------------
    */

    'textarea_rte'          => array(
        'name'              => 'textarea_rte',
        'type'              => 'toggle',
        'label'             => esc_html__( 'Show Rich Text Editor', 'ninja-forms' ),
        'width'             => 'one-third',
        'group'             => 'display',
        'value'             => '',
        'help'              => esc_html__( 'Allows rich text input.', 'ninja-forms' ),
    ),

    'textarea_media'          => array(
        'name'              => 'textarea_media',
        'type'              => 'toggle',
        'label'             => esc_html__( 'Show Media Upload Button', 'ninja-forms' ),
        'width'             => 'one-third',
        'group'             => 'display',
        'value'             => '',
        'deps'              => array(
            'textarea_rte'  => 1
        )
    ),

    'disable_rte_mobile'    => array(
        'name'              => 'disable_rte_mobile',
        'type'              => 'toggle',
        'label'             => esc_html__( 'Disable Rich Text Editor on Mobile', 'ninja-forms' ),
        'width'             => 'one-third',
        'group'             => 'display',
        'value'             => '',
        'deps'              => array(
            'textarea_rte'  => 1
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Submit Button Settings
    |--------------------------------------------------------------------------
    */

    'processing_label' => array(
        'name' => 'processing_label',
        'type' => 'textbox',
        'label' => esc_html__( 'Processing Label', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'primary',
        'value' => esc_textarea( __( 'Processing', 'ninja-forms' ) )
    ),

    /*
    |--------------------------------------------------------------------------
    | Calc Value that is used for checkbox fields
    |--------------------------------------------------------------------------
    */

    'checked_calc_value'    => array(
        'name'      => 'checked_calc_value',
        'type'      => 'textbox',
        'label'     => esc_html__( 'Checked Calculation Value', 'ninja-forms' ),
        'width'     => 'one-half',
        'group'     => 'advanced',
        'help'      => esc_html__( 'This number will be used in calculations if the box is checked.', 'ninja-forms' ),
    ),

    'unchecked_calc_value'    => array(
        'name'      => 'unchecked_calc_value',
        'type'      => 'textbox',
        'label'     => esc_html__( 'Unchecked Calculation Value', 'ninja-forms' ),
        'width'     => 'one-half',
        'group'     => 'advanced',
        'help'      => esc_html__( 'This number will be used in calculations if the box is unchecked.', 'ninja-forms' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | DISPLAY CALCULATION SETTINGS
    |--------------------------------------------------------------------------
    */
    'calc_var'              => array(
        'name'              => 'calc_var',
        'type'              => 'select',
        'label'             => esc_html__( 'Display This Calculation Variable', 'ninja-forms' ),
        'width'             => 'full',
        'group'             => 'primary',
        'options'           => array(),
        'select_product'    => array(
            'value'         => '',
            'label'         => esc_html__( '- Select a Variable', 'ninja-forms' ),
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Pricing Fields Settings
    |--------------------------------------------------------------------------
    */

    'product_price' => array(
        'name' => 'product_price',
        'type' => 'textbox',
        'label' => esc_html__( 'Price', 'ninja-forms' ),
        'width' => 'one-half',
        'group' => 'primary',
        'value' => '1.00',
        'mask' => array(
            'type' => 'currency', // 'numeric', 'currency', 'custom'
            'options' => array()
        )
    ),

    'product_use_quantity' => array(
        'name' => 'product_use_quantity',
        'type' => 'toggle',
        'label' => esc_html__( 'Use Inline Quantity', 'ninja-forms' ),
        'width' => 'one-half',
        'group' => 'primary',
        'value' => TRUE,
        'help'  => esc_html__( 'Allows users to choose more than one of this product.', 'ninja-forms' ),

    ),

    'product_type' => array(
        'name' => 'product_type',
        'type' => 'select',
        'label' => esc_html__( 'Product Type', 'ninja-forms' ),
        'width' => 'full',
        'group' => '',
        'options' => array(
            array(
                'label' => esc_html__( 'Single Product (default)', 'ninja-forms' ),
                'value' => 'single'
            ),
            array(
                'label' => esc_html__( 'Multi Product - Dropdown', 'ninja-forms' ),
                'value' => 'dropdown'
            ),
            array(
                'label' => esc_html__( 'Multi Product - Choose Many', 'ninja-forms' ),
                'value' => 'checkboxes'
            ),
            array(
                'label' => esc_html__( 'Multi Product - Choose One', 'ninja-forms' ),
                'value' => 'radiolist'
            ),
            array(
                'label' => esc_html__( 'User Entry', 'ninja-forms' ),
                'value' => 'user'
            ),
            array(
                'label' => esc_html__( 'Hidden', 'ninja-forms' ),
                'value' => 'hidden'
            ),
        ),
        'value' => 'single',
        'use_merge_tags' => FALSE
    ),

    'shipping_cost'         => array(
        'name'              => 'shipping_cost',
        'type'              => 'textbox',
        'label'             => esc_html__( 'Cost', 'ninja-forms' ),
        'width'             => 'full',
        'group'             => 'primary',
        'value'             => '0.00',
        'mask' => array(
            'type' => 'currency', // 'numeric', 'currency', 'custom'
            'options' => array()
        ),
        'deps'              => array(
            'shipping_type' => 'single',
        ),
    ),

    'shipping_options'      => array(
        'name'              => 'shipping_options',
        'type'              => 'option-repeater',
        'label'             => esc_html__( 'Cost Options', 'ninja-forms' ) . ' <a href="#" class="nf-add-new">' . esc_html__( 'Add New', 'ninja-forms' ) . '</a>',
        'width'             => 'full',
        'group'             => 'primary',
        'value'             => array(
            array( 'label'  => esc_textarea( __( 'One', 'ninja-forms' ) ), 'value' => '1.00', 'order' => 0 ),
            array( 'label'  => esc_textarea( __( 'Two', 'ninja-forms' ) ), 'value' => '2.00', 'order' => 1 ),
            array( 'label'  => esc_textarea( __( 'Three', 'ninja-forms' ) ), 'value' => '3.00', 'order' => 2 ),
        ),
        'columns'          => array(
            'label'         => array(
                'header'    => esc_html__( 'Label', 'ninja-forms' ),
                'default'   => '',
            ),

            'value'         => array(
                'header'    => esc_html__( 'Value', 'ninja-forms' ),
                'default'   => '',
            ),
        ),
        'deps'              => array(
            'shipping_type' => 'select'
        ),
    ),

    'shipping_type'         => array(
        'name'              => 'shipping_type',
        'type'              => 'select',
        'options'           => array(
            array(
                'label'     => esc_html__( 'Single Cost', 'ninja-forms' ),
                'value'     => 'single',
            ),
            array(
                'label'     => esc_html__( 'Cost Dropdown', 'ninja-forms' ),
                'value'     => 'select',
            ),
        ),
        'label'             => esc_html__( 'Cost Type', 'ninja-forms' ),
        'width'             => 'full',
        'group'             => '', //'primary',
        'value'             => 'single',
    ),

    'product_assignment'      => array(
        'name'              => 'product_assignment',
        'type'              => 'select',
        'label'             => esc_html__( 'Product', 'ninja-forms' ),
        'width'             => 'full',
        'group'             => 'primary',
        'options'           => array(),
        'select_product'    => array(
            'value'         => '',
            'label'         => esc_html__( '- Select a Product', 'ninja-forms' ),
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Anti-Spam Field Settings
    |--------------------------------------------------------------------------
    */

    /*
     * Spam Answer
     */

    'spam_answer' => array(
        'name' => 'spam_answer',
        'type' => 'textbox',
        'label' => esc_html__( 'Answer', 'ninja-forms'),
        'width' => 'full',
        'group' => 'primary',
        'value' => '',
        'help'  => esc_html__( 'A case sensitive answer to help prevent spam submissions of your form.', 'ninja-forms' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Term Field Settings
    |--------------------------------------------------------------------------
    */

    /*
     * Taxonomy
     */

    'taxonomy' => array(
        'name' => 'taxonomy',
        'type' => 'select',
        'label' => esc_html__( 'Taxonomy', 'ninja-forms'),
        'width' => 'full',
        'group' => 'primary',
        'options' => array(
            array(
                'label' => "-",
                'value' => ''
            )
        )
    ),

    /*
     * Add New Terms
     */

    'add_new_terms' => array(
        'name' => 'add_new_terms',
        'type' => 'toggle',
        'label' => esc_html__( 'Add New Terms', 'ninja-forms'),
        'width' => 'full',
        'group' => 'advanced',
    ),

    /*
    |--------------------------------------------------------------------------
    | Backwards Compatibility Field Settings
    |--------------------------------------------------------------------------
    */

    'user_state' => array(
        'name' => 'user_state',
        'type' => 'toggle',
        'label' => esc_html__( 'This is a user\'s state.', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'administration',
        'value' => FALSE,
        'help' => esc_html__( 'Used for marking a field for processing.', 'ninja-forms' ),
    ),

));


// Example of settings

// Add all core settings. Fields can unset if unneeded.
// $this->_settings = $this->load_settings(
//     array( 'label', 'label_pos', 'required', 'number', 'spam_question', 'mask', 'input_limit_set','rich_text_editor', 'placeholder', 'textare_placeholder', 'default', 'checkbox_default_value', 'classes', 'timed_submit' )
// );
