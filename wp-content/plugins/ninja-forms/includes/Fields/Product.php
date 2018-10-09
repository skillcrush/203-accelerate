<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_Product
 */
class NF_Fields_Product extends NF_Abstracts_Input
{
    protected $_name = 'product';

    protected $_section = 'pricing';

    protected $_icon = 'tag';

    protected $_aliases = array();

    protected $_type = 'product';

    protected $_templates = array( 'product', 'textbox', 'hidden', 'listselect' );

    protected $_test_value = '0';

    protected $processing_fields = array( 'quantity', 'modifier', 'shipping', 'tax', 'total' );

    protected $_settings = array( 'product_use_quantity', 'product_price', 'product_type', 'product_type' );

    protected $_settings_exclude = array( 'input_limit_set', 'disable_input', 'required' );

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Product', 'ninja-forms' );

        $this->_settings[ 'product_price' ][ 'width' ] = 'full';

        add_filter( 'ninja_forms_merge_tag_value_product', array( $this, 'merge_tag_value' ), 10, 2 );

        add_filter( 'ninja_forms_custom_columns', array( $this, 'custom_columns' ), 10, 3 );
        add_filter( 'ninja_forms_merge_tag_calc_value_product', array( $this, 'merge_tag_value' ), 10, 2 );

        add_filter( 'ninja_forms_localize_field_' . $this->_name, array( $this, 'filter_required_setting' ) );
        add_filter( 'ninja_forms_localize_field_' . $this->_name . '_preview', array( $this, 'filter_required_setting' ) );
    }

    public function process( $product, $data )
    {
        $related = array();

        foreach( $data[ 'fields' ] as $key => $field ){


            if( ! isset ( $field[ 'type' ] ) || ! in_array( $field[ 'type' ], $this->processing_fields ) ) continue;

            $type = $field[ 'type' ];

            if( ! isset( $field[ 'product_assignment' ] ) ) continue;

            if( $product[ 'id' ] != $field[ 'product_assignment' ] ) continue;

            $related[ $type ] = &$data[ 'fields' ][ $key ]; // Assign by reference
        }

        //TODO: Does not work in non-English locales
        $total = str_replace( array( ',', '$' ), '', $product[ 'product_price' ] );
        $total = floatval( $total );

        if( isset( $related[ 'quantity' ][ 'value' ] ) && $related[ 'quantity' ][ 'value' ] ){
            $total = $total * $related[ 'quantity' ][ 'value' ];
        } elseif( $product[ 'product_use_quantity'] && $product[ 'value' ] ){
            $total = $total * $product[ 'value' ];
        }

        if( isset( $related[ 'modifier' ] ) ){
            //TODO: Handle multiple modifiers.
        }

        $data[ 'product_totals' ][] = number_format( $total, 2 );
        $data[ 'extra' ][ 'product_fields' ][ $product[ 'id' ] ][ 'product_price' ] = $product[ 'settings' ][ 'product_price' ];

        return $data;
    }

    /**
     * Validate
     *
     * @param $field
     * @param $data
     * @return array $errors
     */
    public function validate( $field, $data )
    {
        $errors = array();

        if( isset( $field[ 'product_use_quantity' ] ) && 1 == $field[ 'product_use_quantity' ] ){

            // Required check.
            if( isset( $field['required'] ) && 1 == $field['required'] && ! trim( $field['value'] ) ){
                $errors[] = 'Field is required.';
            }
        }

        return $errors;
    }

    public function filter_required_setting( $field )
    {
        if( ! isset( $field[ 'settings' ][ 'product_use_quantity' ] ) || 1 != $field[ 'settings' ][ 'product_use_quantity' ] ) {
            $field[ 'settings' ][ 'required' ] = 0;
        }
        return $field;
    }

    public function merge_tag_value( $value, $field )
    {
        // TODO: Replaced this to fix English locales.
        // Other locales are still broken and will need to be addressed in refactor.
//        $product_price = preg_replace ('/[^\d,\.]/', '', $field[ 'product_price' ] );
        $product_price =  str_replace( array( ',', '$' ), '', $field[ 'product_price' ] );

        $product_quantity = ( isset( $field[ 'product_use_quantity' ] ) && 1 == $field[ 'product_use_quantity' ] ) ? $value : 1;

        return number_format( $product_price * $product_quantity, 2 );
    }

    public function custom_columns( $value, $field, $sub_id )
    {
        if ( ! $field->get_setting( 'product_use_quantity' ) ) return $value;
        if ( 0 == absint( $_REQUEST[ 'form_id' ] ) ) return $value;

        $form_id = absint( $_REQUEST[ 'form_id' ] );

        /*
         * Check to see if we have a stored "price" setting for this field.
         * This lets us track what the value was when the user submitted so that total isn't incorrect if the user changes the price after a submission.
         */
        $sub = Ninja_Forms()->form()->get_sub( $sub_id );
        $product_fields = $sub->get_extra_value( 'product_fields' );

        if( is_array( $product_fields ) && isset ( $product_fields[ $field->get_id() ] ) ) {
            $price = $product_fields[ $field->get_id() ][ 'product_price' ];
        } else {
            $price = $field->get_setting( 'product_price' ); 
        }

        /*
         * Get our currency marker setting. First, we check the form, then plugin settings.
         */
        $currency = Ninja_Forms()->form( $form_id )->get()->get_setting( 'currency' );
        
        if ( empty( $currency ) ) {
            /*
             * Check our plugin currency.
             */
            $currency = Ninja_Forms()->get_setting( 'currency' );
        }

        $currency_symbols = Ninja_Forms::config( 'CurrencySymbol' );
        $currency_symbol = html_entity_decode( $currency_symbols[ $currency ] );

        // @todo Update to use the locale of the form.
        global $wp_locale;
        $price = str_replace( array( $wp_locale->number_format[ 'thousands_sep' ], $currency_symbol ), '', $price );
        $price = floatval( $price );
        $value = intval( $value );

        $total = number_format_i18n( $price * $value, 2 );

        $output = $currency_symbol . $total . ' ( ' . $value . ' ) ';
        return $output;
    }

    public function admin_form_element( $id, $value )
    {
        $form_id = get_post_meta( absint( $_GET[ 'post' ] ), '_form_id', true );

        $field = Ninja_Forms()->form( $form_id )->get_field( $id );

        /*
         * Check to see if we have a stored "price" setting for this field.
         * This lets us track what the value was when the user submitted so that total isn't incorrect if the user changes the price after a submission.
         */
        $sub = Ninja_Forms()->form()->get_sub( absint( $_REQUEST[ 'post' ] ) );
        $product_fields = $sub->get_extra_value( 'product_fields' );

        if( is_array( $product_fields ) && isset ( $product_fields[ $id ] ) ) {
            $price = $product_fields[ $id ][ 'product_price' ];
        } else {
            $price = $field->get_setting( 'product_price' ); 
        }

        /*
         * Get our currency marker setting. First, we check the form, then plugin settings.
         */
        $currency = Ninja_Forms()->form( $form_id )->get()->get_setting( 'currency' );
        
        if ( empty( $currency ) ) {
            /*
             * Check our plugin currency.
             */
            $currency = Ninja_Forms()->get_setting( 'currency' );
        }

        $currency_symbols = Ninja_Forms::config( 'CurrencySymbol' );
        $currency_symbol = html_entity_decode( $currency_symbols[ $currency ] );

        // @todo Update to use the locale of the form.
        global $wp_locale;
        $price = str_replace( array( $wp_locale->number_format[ 'thousands_sep' ], $currency_symbol ), '', $price );
        $price = floatval( $price );
        $value = intval( $value );

        $total = number_format_i18n( $price * $value, 2 );
        $price = number_format_i18n( $price, 2 );

        return "Price: <strong>" . $currency_symbol . $price . "</strong> X Quantity: <input name='fields[$id]' type='number' value='" . $value . "'> = " . $currency_symbol . $total;
    }
}
