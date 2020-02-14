<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_Email
 */
class NF_Fields_Email extends NF_Abstracts_UserInfo
{
    protected $_name = 'email';

    protected $_nicename = 'Email';

    protected $_type = 'email';

    protected $_section = 'userinfo';

    protected $_icon = 'envelope-o';

    protected $_templates = 'email';

    protected  $_test_value = 'foo@bar.dev';

    protected $_settings_all_fields = array( 'custom_name_attribute', 'personally_identifiable' );

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__( 'Email', 'ninja-forms' );

        $this->_settings[ 'custom_name_attribute' ][ 'value' ] = 'email';
        $this->_settings[ 'personally_identifiable' ][ 'value' ] = '1';

    }

    public function filter_default_value( $default_value, $field_class, $settings )
    {
        if( ! isset( $settings[ 'default_type' ] ) ||
            'user-meta' != $settings[ 'default_type' ] ||
            $this->_name != $field_class->get_name()) return $default_value;

        $current_user = wp_get_current_user();

        if( $current_user ){
            $default_value = $current_user->user_email;
        }

        return $default_value;
    }
}
