<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_Date
 */
class NF_Fields_Date extends NF_Fields_Textbox
{
    protected $_name = 'date';

    protected $_nicename = 'Date';

    protected $_section = 'common';

    protected $_icon = 'calendar';

    protected $_type = 'date';

    protected $_templates = 'date';

    protected $_test_value = '12/12/2022';

    protected $_settings = array( 'date_default', 'date_format', 'year_range' );

    protected $_settings_exclude = array( 'default', 'input_limit_set', 'disable_input' );

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__( 'Date', 'ninja-forms' );
    }

    public function process( $field, $data )
    {
        return $data;
    }

    private function get_format( $format )
    {
        $lookup = array(
            'MM/DD/YYYY' => esc_html__( 'm/d/Y', 'ninja-forms' ),
            'MM-DD-YYYY' => esc_html__( 'm-d-Y', 'ninja-forms' ),
            'MM.DD.YYYY' => esc_html__( 'm.d.Y', 'ninja-forms' ),
            'DD/MM/YYYY' => esc_html__( 'm/d/Y', 'ninja-forms' ),
            'DD-MM-YYYY' => esc_html__( 'd-m-Y', 'ninja-forms' ),
            'DD.MM.YYYY' => esc_html__( 'd.m.Y', 'ninja-forms' ),
            'YYYY-MM-DD' => esc_html__( 'Y-m-d', 'ninja-forms' ),
            'YYYY/MM/DD' => esc_html__( 'Y/m/d', 'ninja-forms' ),
            'YYYY.MM.DD' => esc_html__( 'Y.m.d', 'ninja-forms' ),
            'dddd, MMMM D YYYY' => esc_html__( 'l, F d Y', 'ninja-forms' ),
        );

        return ( isset( $lookup[ $format ] ) ) ? $lookup[ $format ] : $format;
    }

}
