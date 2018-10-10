<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Notices page to house all of the admin notices for Core
 *
 * Can be simply used be adding another line into the nf_admin_notices() function
 *
 * The class NF_Notices in notices-class.php can be extended to create more advanced notices to include triggered events
 *
 * @since 2.9
 */

function nf_admin_notices( $notices ) {

    if( ! ninja_forms_three_addons_check() || ( ninja_forms_three_addons_version_check() && ninja_forms_three_addons_check() ) ){


        /*
         * Upgrade Now
         */
        $upgrade_link = admin_url( 'admin.php?page=ninja-forms-three' );
        $notice = array(
            'title' => __( 'Upgrade to Ninja Forms THREE', 'ninja-forms' ),
            'link' => '',
            'int' => 0,
            'blacklist' => array( 'ninja-forms', 'ninja-forms-three' ),
        );
        if ( ! get_option( 'ninja_forms_has_invalid_addons' ) ) {
            $notice[ 'msg' ] = sprintf( __( 'You are eligible to upgrade to Ninja Forms THREE! %sUpgrade Now%s', 'ninja-forms' ), '<a target="_blank" href="' . $upgrade_link . '">', '</a>' );
        } else {
            if ( ! function_exists( 'get_plugins' ) ) {
                // Require the core file.
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            // Get a list of plugins.
            $plugins = get_plugins();
            $not_ours = array();
            // For each plugin...
            foreach( $plugins as $plugin => $data ){
                // If this isn't Ninja Forms Core...
                // AND If it includes a ninja-forms prefix...
                if ( 'ninja-forms/ninja-forms.php' != $plugin && 0 === strncmp( $plugin, 'ninja-forms-', 12 ) ){
                    // Get the plugin slug.
                    $slug = explode( '/', $plugin );
                    // If the plugin is not active...
                    // Exit early.
                    if ( ! is_plugin_active( $plugin ) ) continue;
                    // If the plugin is not in our list...
                    if ( ! ninja_forms_valid_slug( $slug[ 0 ] ) ) {
                        // Add it onto our array of data
                        $not_ours[ $plugin ] = $data;
                    }
                }
            }
            $display = sprintf( __( 'You are eligible to upgrade to Ninja Forms THREE! However, the following plugins are not compatible with Ninja Forms THREE and could lead to issues with the upgrade process.%sPlease deactivate and remove the following before attempting to upgrade:%s', 'ninja-forms' ), '<br />', '<br />' );
            foreach( $not_ours as $plugin ) {
                $display .= __( $plugin[ 'Name' ], $plugin[ 'TextDomain' ] ) . ', ';
            }
            $display = substr( $display, 0, strlen( $display ) - 2 );
            $notice[ 'msg' ] = $display;
            $notice[ 'blacklist' ] = array( 'ninja-forms-three' );
        }
        $notices['three_upgrade'] = $notice;
    } else {

        /*
         * THREE is Coming!
         */
        $three_info = add_query_arg( array( 'nf_admin_notice_ignore' => 'three_info' ) );
        $three_link = nf_aff_link( 'https://ninjaforms.com/three/?utm_medium=plugin&utm_source=admin-notice&utm_campaign=Ninja+Forms+THREE&utm_content=Learn+More' );
        $notices['three_info'] = array(
            'title' => __( 'THREE is coming!', 'ninja-forms' ),
            'msg' => sprintf( __( 'A major update is coming to Ninja Forms. %sLearn more about new features, backwards compatibility, and more Frequently Asked Questions.%s', 'ninja-forms' ), '<a target="_blank" href="' . $three_link . '">', '</a>' ),
            'link' => '',
            'int' => 0,
            'blacklist' => array( 'ninja-forms', 'ninja-forms-three' ),
        );

    }



    $one_week_support = add_query_arg( array( 'nf_admin_notice_ignore' => 'one_week_support' ) );
    $support_link = nf_aff_link( 'https://ninjaforms.com/contact/?utm_medium=plugin&utm_source=admin-notice&utm_campaign=Ninja+Forms+Upsell&utm_content=Ninja+Forms+Support' );
    $support_docs_link = nf_aff_link( 'http://docs.ninjaforms.com/?utm_medium=plugin&utm_source=admin-notice&utm_campaign=Ninja+Forms+Upsell&utm_content=Ninja+Forms+Docs' );
    $notices['one_week_support'] = array(
        'title' => __( 'How\'s It Going?', 'ninja-forms' ),
        'msg' => __( 'Thank you for using Ninja Forms! We hope that you\'ve found everything you need, but if you have any questions:', 'ninja-forms' ),
        'link' => '<li><span class="dashicons dashicons-media-text"></span><a target="_blank" href="' . $support_docs_link . '">' . __( 'Check out our documentation', 'ninja-forms' ) . '</a></li>
                    <li><span class="dashicons dashicons-sos"></span><a target="_blank" href="' . $support_link . '">' . __( 'Get Some Help' ,'ninja-forms' ) . '</a></li>
                    <li><span class="dashicons dashicons-dismiss"></span><a href="' . $one_week_support . '">' . __( 'Dismiss' ,'ninja-forms' ) . '</a></li>',
        'int' => 7,
        'blacklist' => array( 'ninja-forms-three' ),
    );

//    $two_week_review_ignore = add_query_arg( array( 'nf_admin_notice_ignore' => 'two_week_review' ) );
//    $two_week_review_temp = add_query_arg( array( 'nf_admin_notice_temp_ignore' => 'two_week_review', 'int' => 14 ) );
//    $notices['two_week_review'] = array(
//        'title' => __( 'Leave A Review?', 'ninja-forms' ),
//        'msg' => __( 'We hope you\'ve enjoyed using Ninja Forms! Would you consider leaving us a review on WordPress.org?', 'ninja-forms' ),
//        'link' => '<li> <span class="dashicons dashicons-smiley"></span><a href="' . $two_week_review_ignore . '"> ' . __( 'I\'ve already left a review', 'ninja-forms' ) . '</a></li>
//                    <li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $two_week_review_temp . '">' . __( 'Maybe Later' ,'ninja-forms' ) . '</a></li>
//                    <li><span class="dashicons dashicons-external"></span><a href="http://wordpress.org/support/view/plugin-reviews/ninja-forms?filter=5" target="_blank">' . __( 'Sure! I\'d love to!', 'ninja-forms' ) . '</a></li>',
//        'int' => 14
//    );


    return $notices;
}
// This function is used to hold all of the basic notices
// Date format accepts most formats but can get confused so preferred methods are m/d/Y or d-m-Y

add_filter( 'nf_admin_notices', 'nf_admin_notices' );

// Require any files that contain class extensions for NF_Notices
require_once( NF_PLUGIN_DIR . 'classes/notices-multipart.php' );

// Require any files that contain class extensions for NF_Notices
require_once( NF_PLUGIN_DIR . 'classes/notices-save-progress.php' );
