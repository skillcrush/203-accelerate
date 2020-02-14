<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_admin_notices', array(

    /*
    |--------------------------------------------------------------------------
    | One Week Support
    |--------------------------------------------------------------------------
    */

    'one_week_support' => array(
        'title' => esc_html__( 'How\'s It Going?', 'ninja-forms' ),
        'msg' => esc_html__( 'Thank you for using Ninja Forms! We hope that you\'ve found everything you need, but if you have any questions:', 'ninja-forms' ),
        'link' => '<li><span class="dashicons dashicons-media-text"></span><a target="_blank" href="https://ninjaforms.com/documentation/?utm_medium=plugin&utm_source=admin-notice&utm_campaign=Ninja+Forms+Upsell&utm_content=Ninja+Forms+Docs">' . esc_html__( 'Check out our documentation', 'ninja-forms' ) . '</a></li>
                    <li><span class="dashicons dashicons-sos"></span><a target="_blank" href="https://ninjaforms.com/contact/?utm_medium=plugin&utm_source=admin-notice&utm_campaign=Ninja+Forms+Upsell&utm_content=Ninja+Forms+Support">' . esc_html__( 'Get Some Help' ,'ninja-forms' ) . '</a></li>
                    <li><span class="dashicons dashicons-dismiss"></span><a href="' . wp_nonce_url( add_query_arg( array( 'nf_admin_notice_ignore' => 'one_week_support' ) ) ) . '">' . esc_html__( 'Dismiss' ,'ninja-forms' ) . '</a></li>',
        'int' => 7,
        'blacklist' => array( 'ninja-forms-three' ),
    ),

));
