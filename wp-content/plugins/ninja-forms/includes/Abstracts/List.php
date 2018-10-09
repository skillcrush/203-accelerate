<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Abstracts_List
 */
abstract class NF_Abstracts_List extends NF_Abstracts_Field
{
    protected $_name = '';

    protected $_section = 'common';

    protected $_type = 'list';

    protected $_test_value = FALSE;

    protected $_settings_all_fields = array(
        'key', 'label', 'label_pos', 'required', 'options', 'classes', 'admin_label', 'help', 'description'
    );

    public static $_base_template = 'list';

    public function __construct()
    {
        parent::__construct();

        add_filter( 'ninja_forms_custom_columns', array( $this, 'custom_columns' ), 10, 2 );

        add_filter( 'ninja_forms_render_options', array( $this, 'query_string_default' ), 10, 2 );
    }

    public function get_parent_type()
    {
        return 'list';
    }

    public function admin_form_element( $id, $value )
    {
        $form_id = get_post_meta( absint( $_GET[ 'post' ] ), '_form_id', true );

        $field = Ninja_Forms()->form( $form_id )->get_field( $id );

        $settings = $field->get_settings();
        $settings[ 'options' ] = apply_filters( 'ninja_forms_render_options', $settings[ 'options' ], $settings );
        $settings[ 'options' ] = apply_filters( 'ninja_forms_render_options_' . $field->get_type(), $settings[ 'options' ], $settings );

        $options = '<option>--</option>';
        if ( is_array( $settings[ 'options' ] ) ) {
            foreach( $settings[ 'options' ] as $option ){
                $selected = ( $value == $option[ 'value' ] ) ? "selected" : '';
                $options .= "<option value='" . esc_attr( $option[ 'value' ] ) . "' $selected>" . esc_html( $option[ 'label' ] ) . "</option>";
            }            
        }

        return "<select class='widefat' name='fields[" . esc_attr( $id ) . "]' id=''>$options</select>";
    }

    /*
     * Appropriate output for a column cell in submissions list.
     */
    public function custom_columns( $value, $field )
    {
        if( $this->_name != $field->get_setting( 'type' ) ) return $value;
        
        //Consider &amp; to be the same as the & values in database in a selectbox saved value:
        if( ! is_array( $value ) ) $value = array( htmlspecialchars_decode($value) );

        $output = '';
        $options = $field->get_setting( 'options' );
        if( ! empty( $options ) ) {
            foreach ($options as $option) {

                if ( ! in_array( $option[ 'value' ], $value ) ) continue;

                $output .= esc_html( $option[ 'label' ] ) . "<br />";
            }
        }

        return $output;
    }

    public function query_string_default( $options, $settings )
    {
        if( ! isset( $settings[ 'key' ] ) ) return $options;

        $field_key = $settings[ 'key' ];

        if( ! isset( $_GET[ $field_key ] ) ) return $options;

        foreach( $options as $key => $option ){

            if( ! isset( $option[ 'value' ] ) ) continue;

            if( $option[ 'value' ] != $_GET[ $field_key ] ) continue;

            $options[ $key ][ 'selected' ] = 1;
        }

        return $options;
    }
}
