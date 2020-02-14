<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_Controllers_DispatchPoints
{
    /*
     * Constructor method
     */
    public function __construct()
    {
        // Add our ajax end points. Calls are handled in this file.
        add_action( 'wp_ajax_nf_undo_click',   array( $this, 'undo_click' ) );
        add_action( 'wp_ajax_nf_form_telemetry', array( $this, 'form_telemetry' ) );
    }
    
    /*
     * Function called when the undo manager is used in the builder.
     * 
     * @since 3.2
     */
    public function undo_click() {
        // Make sure we have a valid nonce.
        check_ajax_referer( 'ninja_forms_builder_nonce', 'security' );
        // Send the action to our dispatcher.
        Ninja_Forms()->dispatcher()->send( 'undo_click' );
        // Exit.
        die( 1 );
    }
    
    /*
     * Function to startup our form data telemtry.
     * 
     * @since 3.2
     */
    public function form_telemetry() {
        // Make sure we have a valid nonce.
        check_ajax_referer( 'ninja_forms_dashboard_nonce', 'security' );
        // Send the action to our dispatcher.
        Ninja_Forms()->dispatcher()->form_data();
        // Exit.
        die( 1 );
    }
}