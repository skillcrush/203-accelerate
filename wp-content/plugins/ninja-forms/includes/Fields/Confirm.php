<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_Confirm
 */
class NF_Fields_Confirm extends NF_Abstracts_Input
{
    protected $_name = 'confirm';

    protected $_type = 'confirm';

    protected $_nicename = 'Confirm';

    protected $_section = 'misc';

    protected $_icon = 'check-circle-o';

    protected $_error_message = '';

    protected $_settings = array( 'confirm_field' );

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Confirm', 'ninja-forms' );
        $this->_settings[ 'confirm_field' ][ 'field_value_format' ] = 'key';

        add_filter( 'nf_sub_hidden_field_types', array( $this, 'hide_field_type' ) );
    }

    function hide_field_type( $field_types )
    {
        $field_types[] = $this->_name;
        return $field_types;
    }

    public function validate( $field, $data )
    {
        if( false ){
            $errors[] = __( 'Fields do not match.', 'ninja-forms' );
        }
        return $errors;
    }
}
