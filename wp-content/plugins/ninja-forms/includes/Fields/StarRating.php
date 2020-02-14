<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_StarRating
 */
class NF_Fields_StarRating extends NF_Abstracts_Input
{
    protected $_name = 'starrating';

    protected $_section = 'misc';

    protected $_icon = 'star-half-o';

    protected $_aliases = array( 'rating' );

    protected $_type = 'starrating';

    protected $_templates = 'starrating';

    protected $_settings_only = array( 'label', 'label_pos', 'default',
	    'number_of_stars', 'required', 'classes', 'key', 'admin_label' );

    public function __construct()
    {
        parent::__construct();

        // Put this in the primary settings group
        $this->_settings['number_of_stars']['group'] = 'primary';

        $this->_nicename = esc_html__( 'Star Rating', 'ninja-forms' );
    }

}
