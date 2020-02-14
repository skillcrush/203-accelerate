<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_Recaptcha
 */
class NF_Fields_Recaptcha extends NF_Abstracts_Field
{
    protected $_name = 'recaptcha';

    protected $_type = 'recaptcha';

    protected $_section = 'misc';

    protected $_icon = 'filter';

    protected $_templates = 'recaptcha';

    protected $_test_value = '';

    protected $_settings = array( 'label', 'classes' );

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__( 'Recaptcha', 'ninja-forms' );

        $this->_settings[ 'size '] = array(
            'name' => 'size',
            'type' => 'select',
            'label' => esc_html__( 'Visibility', 'ninja-forms' ),
            'options' => array(
                array(
                    'label' => esc_html__( 'Visible', 'ninja-forms' ),
                    'value' => 'visible'
                ),
                array(
                    'label' => esc_html__( 'Invisible', 'ninja-forms' ),
                    'value' => 'invisible'
                ),
            ),
            'width' => 'one-half',
            'group' => 'primary',
            'value' => 'visible',
            'help' => esc_html__( 'Select whether to display a "I\'m not a robot" field or to detect if the user is a robot in the background.', 'ninja-forms' ),
        );

        add_filter( 'nf_sub_hidden_field_types', array( $this, 'hide_field_type' ) );
    }

    public function localize_settings( $settings, $form ) {
        $settings['site_key'] = Ninja_Forms()->get_setting( 'recaptcha_site_key' );
        $settings['theme'] = Ninja_Forms()->get_setting( 'recaptcha_theme' );
        $settings['theme'] = ( $settings['theme'] ) ? $settings['theme'] : 'light';
    	$settings['lang'] = Ninja_Forms()->get_setting( 'recaptcha_lang' );
    	return $settings;
    }

    public function validate( $field, $data ) {
        if ( empty( $field['value'] ) ) {
            return esc_html__( 'Please complete the recaptcha', 'ninja-forms' );
        }

        $secret_key = Ninja_Forms()->get_setting( 'recaptcha_secret_key' );
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response='.sanitize_text_field( $field['value'] );
        $resp = wp_remote_get( esc_url_raw( $url ) );

        if ( !is_wp_error( $resp ) ) {
            $body = wp_remote_retrieve_body( $resp );
            $response = json_decode( $body );
            if ( $response->success === false ) {
                if ( !empty( $response->{'error-codes'} ) && $response->{'error-codes'} != 'missing-input-response' ) {
                    return array( esc_html__( 'Please make sure you have entered your Site & Secret keys correctly', 'ninja-forms' ) );
                }else {
                    return array( esc_html__( 'Captcha mismatch. Please enter the correct value in captcha field', 'ninja-forms' ) );
                }
            }
        }
    }

    function hide_field_type( $field_types )
    {
        $field_types[] = $this->_name;
        return $field_types;
    }
}
