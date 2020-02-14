<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_from_settings_types', array(

    'display' 			=> array(
        'id' 			=> 'display',
        'nicename' 		=> esc_html__( 'Display Settings', 'ninja-forms' ),
    ),

    'restrictions' 		=> array(
        'id' 			=> 'restrictions',
        'nicename' 		=> esc_html__( 'Restrictions', 'ninja-forms' )
    ),

    'calculations' 		=> array(
    	'id' 			=> 'calculations',
    	'nicename' 		=> esc_html__( 'Calculations', 'ninja-forms')
    ),

));
