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

    protected $_settings =  array( 'checkbox_default_value', 'checked_calc_value', 'unchecked_calc_value' );

    protected $_settings_exclude = array( 'default', 'placeholder', 'input_limit_set' );

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Single Checkbox', 'ninja-forms' );

        $this->_settings[ 'label_pos' ][ 'value' ] = 'right';

        add_filter( 'ninja_forms_custom_columns', array( $this, 'custom_columns' ), 10, 2 );

        add_filter( 'ninja_forms_merge_tag_value_' . $this->_name, array( $this, 'filter_merge_tag_value' ), 10, 2 );
        add_filter( 'ninja_forms_merge_tag_calc_value_' . $this->_name, array( $this, 'filter_merge_tag_value_calc' ), 10, 2 );
        add_filter( 'ninja_forms_subs_export_field_value_' . $this->_type, array( $this, 'export_value' ), 10 );
    }

    public function admin_form_element( $id, $value )
    {
        $checked = ( $value ) ? "checked" : "";

        return "<input type='hidden' name='fields[$id]' value='0' >
                <input type='checkbox' name='fields[$id]' id='' $checked>";
    }

    public function custom_columns( $value, $field )
    {
        if( 'checkbox' == $field->get_setting( 'type' ) ) {
            if ( __( 'checked', 'ninja-forms' ) == $value ||
                 __( 'unchecked', 'ninja-forms' ) == $value ) return $value;
            $value = ( $value ) ? __( 'checked', 'ninja-forms' ) : __( 'unchecked', 'ninja-forms' );
        }
        return $value;
    }

    public function filter_merge_tag_value( $value, $field )
    {
        if( $value ){
            if( isset( $field[ 'checked_calc_value' ] ) && '' != $field[ 'checked_calc_value' ] ) {
                return $field['checked_calc_value'];
            } else {
                return __( 'checked', 'ninja-forms' );
            }
        }

        if( ! $value ){
            if( isset( $field[ 'unchecked_calc_value' ] ) && '' != $field[ 'unchecked_calc_value' ] ) {
                return $field['unchecked_calc_value'];
            } else {
                return __( 'unchecked', 'ninja-forms' );
            }
        }

        return $value;
    }

    public function filter_merge_tag_value_calc( $value, $field )
    {
        return ( 1 == $value ) ? $field[ 'checked_calc_value' ] : $field[ 'unchecked_calc_value' ];
    }

    public function export_value( $value ) {
        if ( __( 'checked', 'ninja-forms' ) == $value ||
             __( 'unchecked', 'ninja-forms' ) == $value ) return $value;
        if ( $value ) {
            return __( 'checked', 'ninja-forms' );
        } else {
            return __( 'unchecked', 'ninja-forms' );
        }
    }
}
