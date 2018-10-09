<?php
/**
 * Tracking functions for reporting plugin usage to the Ninja Forms site for users that have opted in
 *
 * @package     Ninja Forms
 * @subpackage  Admin
 * @copyright   Copyright (c) 2016, The WP Ninjas
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.9.52
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Tracking
 */
final class NF_Tracking
{
    const OPT_IN = 1;
    const OPT_OUT = 0;
    const FLAG = 'ninja_forms_opt_in';

    /**
     * NF_Tracking constructor.
     */
    public function __construct()
    {

        // Temporary: Report previously opted-in users that were not already reported. @todo Remove after a couple of versions.
//        add_action( 'admin_init', array( $this, 'report_optin' ) );

        add_action( 'wp_ajax_nf_optin', array( $this, 'maybe_opt_in' ) );
        add_filter( 'ninja_forms_check_setting_allow_tracking',  array( $this, 'check_setting' ) );
        add_filter( 'ninja_forms_update_setting_allow_tracking', array( $this, 'update_setting' ) );
    }

    /**
     * Check if an opt in/out action should be performed.
     *
     * @access public
     * @hook admin_init
     */
    public function maybe_opt_in()
    {
        if( $this->can_opt_in() ) {

            $opt_in_action = htmlspecialchars( $_POST[ self::FLAG ] );

            if( self::OPT_IN == $opt_in_action ){
                $this->opt_in();
            }

            if( self::OPT_OUT == $opt_in_action ){
                $this->opt_out();
            }
        }
        die( 1 );
    }

    /**
     * Report that a user has opted-in.
     *
     * @param array $data Dispatch event data.
     */
    function report_optin($data = array() )
    {
        // Only send initial opt-in.
        if( get_option( 'ninja_forms_optin_reported', 0 ) ) return;

        $data = wp_parse_args( $data, array(
            'send_email' => 0, // Do not send email by default.
            'user_email' => ''
        ) );

        Ninja_Forms()->dispatcher()->send( 'optin', $data );
        Ninja_Forms()->dispatcher()->update_environment_vars();

        // Debounce opt-in dispatch.
        update_option( 'ninja_forms_optin_reported', 1 );
    }

    /**
     * Check if the current user is allowed to opt in on behalf of a site
     *
     * @return bool
     */
    private function can_opt_in()
    {
        return current_user_can( apply_filters( 'ninja_forms_admin_opt_in_capabilities', 'manage_options' ) );
    }

    /**
     * Check if a site is opted in
     *
     * @access public
     * @return bool
     */
    public function is_opted_in()
    {
        return (bool) get_option( 'ninja_forms_allow_tracking' );
    }

    /**
     * Opt In a site for tracking
     *
     * @access private
     * @return null
     */
    public function opt_in()
    {
        if( $this->is_opted_in() ) return;

        /**
         * Update our tracking options.
         */
        update_option( 'ninja_forms_allow_tracking', true );
        update_option( 'ninja_forms_do_not_allow_tracking', false );

        /**
         * Send updated environment variables.
         */
        Ninja_Forms()->dispatcher()->update_environment_vars();

        /**
         * Send our optin event
         */
        if ( isset ( $_REQUEST[ 'send_email' ] ) ) {
            $send_email = absint( $_REQUEST[ 'send_email' ] );
            $user_email = $_REQUEST[ 'user_email' ];
            add_option( 'ninja_forms_optin_email', $user_email, '', 'no' );
        } else {
            $send_email = 0;
            $user_email = '';
        }

        $this->report_optin( array( 'send_email' => $send_email, 'user_email' => $user_email ) );
    }

    /**
     * Check if a site is opted out
     *
     * @access public
     * @return bool
     */
    public function is_opted_out()
    {
        return (bool) get_option( 'ninja_forms_do_not_allow_tracking' );
    }

    /**
     * Opt Out a site from tracking
     *
     * @access private
     * @return null
     */
    private function opt_out()
    {
        if( $this->is_opted_out() ) return;
        
        $data = array();
        $user_email = get_option( 'ninja_forms_optin_email' );
        if ( $user_email ) {
            $data[ 'user_email' ] = $user_email;
        }
        Ninja_Forms()->dispatcher()->send( 'optout', $data );
        delete_option( 'ninja_forms_optin_email' );

        // Disable tracking.
        update_option( 'ninja_forms_allow_tracking', false );
        update_option( 'ninja_forms_do_not_allow_tracking', true );

        // Clear dispatch debounce flag.
        update_option( 'ninja_forms_optin_reported', 0 );
    }

    public function check_setting( $setting )
    {
        if( $this->is_opted_in() && ! $this->is_opted_out() ) {
            $setting[ 'value' ] = "1";
        } else {
            $setting[ 'value' ] = "0";
        }
        return $setting;
    }

    public function update_setting( $value )
    {
        if( "1" == $value ){ // Allow Tracking
            $this->opt_in();
        } else {
            $this->opt_out();
        }
        return $value;
    }

} // END CLASS NF_Tracking
