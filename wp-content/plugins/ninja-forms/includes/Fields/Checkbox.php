<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_Checkbox
 */
class NF_Fields_Checkbox extends NF_Abstracts_Input
{
    protected $_name = 'checkbox';

    protected $_nicename = 'Checkbox';

    protected $_section = 'common';

    protected $_icon = 'check-square-o';

    protected $_type = 'checkbox';

    protected $_templates = 'checkbox';

    protected $_test_value = 0;

    protected $_settings =  array( 'checkbox_default_value', 'checkbox_values', 'checked_calc_value', 'unchecked_calc_value' );

    protected $_settings_exclude = array( 'default', 'placeholder', 'input_limit_set' );

    /**
     * NF_Fields_Checkbox constructor.
     * @since 3.0
     */
    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Single Checkbox', 'ninja-forms' );

        $this->_settings[ 'label_pos' ][ 'value' ] = 'right';

        add_filter( 'ninja_forms_custom_columns', array( $this, 'custom_columns' ), 10, 2 );

        add_filter( 'ninja_forms_merge_tag_value_' . $this->_name, array( $this, 'filter_merge_tag_value' ), 10, 2 );
        add_filter( 'ninja_forms_merge_tag_calc_value_' . $this->_name, array( $this, 'filter_merge_tag_value_calc' ), 10, 2 );
        add_filter( 'ninja_forms_subs_export_field_value_' . $this->_type, array( $this, 'export_value' ), 10, 2 );
    }

    /**
     * Admin Form Element
     * Display the checkbox on the edit submissions area.
     * @since 3.0
     *
     * @param $id Field ID.
     * @param $value Field value.
     * @return string HTML used for display of checkbox.
     */
    public function admin_form_element( $id, $value )
    {
        // If the checkboxes value is 1 or on...
        if( 'on' == $value || 1 == $value ) {
            // ...this variable to checked.
            $checked = 'checked';
        } else {
            // ...else leave the variable empty.
            $checked = '';
        }

        // Return HTML to be output to the submission edit page.
        return "<input type='hidden' name='fields[$id]' value='0' >
                <input type='checkbox' name='fields[$id]' id='' $checked>";
    }

    /**
     * Custom Columns
     * Creates what is displayed in the columns on the submissions page.
     * @since 3.0
     *
     * @param $value checkbox value
     * @param $field field model.
     * @return $value string|void
     */
    public function custom_columns( $value, $field )
    {
        // If the field type is equal to checkbox...
        if( 'checkbox' == $field->get_setting( 'type' ) ) {
            // Backwards compatibility check for the new checked value setting.
            if( null == $field->get_setting( 'checked_value' ) && 1 == $value || 'on' == $value ) {
                return __( 'Checked', 'ninja-forms' );
            } elseif( null == $field->get_setting( 'unchecked_value' ) && 0 == $value ) {
                return __( 'Unchecked', 'ninja-forms');
            }

            // If the field value is set to 1....
            if( 1 == $value || 'on' == $value) {
                // Set the value to the checked value setting.
                $value = $field->get_setting( 'checked_value' );
            } else {
                // Else set the value to the unchecked value setting.
                $value = $field->get_setting( 'unchecked_value' );
            }
        }
        return $value;
    }

    /**
     * Filter Merge Tag Value
     * This is what provides the merge tag with the fields value.
     * @since 3.0
     *
     * @param $value Field value
     * @param $field field model
     * @return string|void
     */
    public function filter_merge_tag_value( $value, $field )
    {
        // If value is true, return checked value setting.
        if( $value ) return $field[ 'settings' ][ 'checked_value' ];
        // Else return unchecked value setting.
        return $field[ 'settings' ][ 'unchecked_value' ];;
    }

    /**
     * Filter Merge Tag Value Calc
     * Provides the calculation value when the merge tag is used.
     * @since 3.0
     *
     * @param $value checkbox value
     * @param $field field model
     * @return $field
     */
    public function filter_merge_tag_value_calc( $value, $field )
    {
        // If value is equal to 1...
        if ( 1 == $value ) {
            // ...return the checked calc value of the field model.
            return $field[ 'checked_calc_value' ];
        } else {
            // ...else return the unchecked calc value of the field model.
            return $field[ 'unchecked_calc_value' ];
        }
    }

    /**
     * Export Value
     * Determines the value to send to submission export.
     * @since 3.0
     *
     * @param $value checkbox field value
     * @param $field checkbox field model
     * @return string|void
     */
    public function export_value( $value, $field )
    {
        // If value is equal to checked or unchecked return the value
        if ( __( 'checked', 'ninja-forms' ) == $value ||
            __( 'unchecked', 'ninja-forms' ) == $value ) return $value;

        // Creating settings variables for our check.
        if( is_array( $field ) ) {
            // The email action sends teh field variable as an array
            $checked_setting    = $field[ 'setting' ][ 'checked_value' ];
            $unchecked_setting  = $field[ 'setting' ][ 'unchecked_value' ];
        } else {
            $checked_setting    = $field->get_setting( 'checked_value' );
            $unchecked_setting  = $field->get_setting( 'unchecked_value' );
        }

        // If the the value and check to see if we have checked and unchecked settings...
        if ( 1 == $value && ! empty( $checked_setting ) ) {
            // ...if we do return checked setting
            return $checked_setting;
        } elseif ( 0 == $value && ! empty( $unchecked_setting ) ) {
            // ...else return unchecked setting.
            return $unchecked_setting;
        /*
         * These checks are for checkbox fields that were created before version 3.2.7.
         */
        } elseif ( 1 == $value  ) {
            return __( 'checked', 'ninja-forms' );
        } elseif ( 0 == $value ) {
            return __( 'unchecked', 'ninja-forms' );
        }
    }
}
