<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_Password
 */
class NF_Fields_Password extends NF_Abstracts_Input
{
    protected $_name = 'password';

    protected $_nicename = 'Password';

    protected $_section = '';

    protected $_type = 'password';

    protected $_templates = array( 'password', 'textbox', 'input' );

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__( 'Password', 'ninja-forms' );

        add_filter( 'nf_sub_hidden_field_types', array( $this, 'hide_field_type' ) );
    }

    function hide_field_type( $field_types )
    {
        $field_types[] = $this->_name;

        return $field_types;
    }
}
