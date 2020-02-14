<?php

/**
 * Handles sending information to our api.ninjaforms.com endpoint.
 *
 * @since  3.2
 */
final class NF_Dispatcher
{
    private $api_url = 'http://api.ninjaforms.com/';

    /**
     * Returns bool true if we are opted-in or have a premium add-on.
     * If a premium add-on is installed, then users have opted into tracked via our terms and conditions.
     * If no premium add-ons are installed, check to see if the user has opted in or out of anonymous usage tracking.
     *
     * @since  version
     * @return bool
     */
    public function should_we_send() {

        /**
         * TODO:
         * Prevent certain URLS or IPs from submitting. i.e. staging, 127.0.0.1, localhost, etc.
         */

        if ( ! has_filter( 'ninja_forms_settings_licenses_addons' ) && ( ! Ninja_Forms()->tracking->is_opted_in() || Ninja_Forms()->tracking->is_opted_out() ) ) {
            return false;
        }
        return true;
    }

    /**
     * Package up our environment variables and send those to our API endpoint.
     * 
     * @since  3.2
     * @return void
     * 
     * @updated 3.3.17
     */
    public function update_environment_vars() {
        global $wpdb;

        // Plugins
        $active_plugins = (array) get_option( 'active_plugins', array() );

        //WP_DEBUG
        if ( defined('WP_DEBUG') && WP_DEBUG ){
            $debug = 1;
        } else {
            $debug =  0;
        }

        //WPLANG
        if ( defined( 'WPLANG' ) && WPLANG ) {
            $lang = WPLANG;
        } else {
            $lang = 'default';
        }

        $ip_address = '';
        if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) ) {
            $ip_address = $_SERVER[ 'SERVER_ADDR' ];
        } else if ( array_key_exists( 'LOCAL_ADDR', $_SERVER ) ) {
            $ip_address = $_SERVER[ 'LOCAL_ADDR' ];
        }

        // If we have a valid IP Address...
        if ( filter_var( $ip_address, FILTER_VALIDATE_IP ) ) {
            // Get the hostname.
            $host_name = gethostbyaddr( $ip_address );
        } else {
            $host_name = 'unknown';
        }

        if ( is_multisite() ) {
            $multisite_enabled = 1;
        } else {
            $multisite_enabled = 0;
        }

        $environment = array(
            'nf_version'                => Ninja_Forms::VERSION,
            'nf_db_version'             => get_option( 'ninja_forms_db_version', '1.0' ),
            'wp_version'                => get_bloginfo('version'),
            'multisite_enabled'         => $multisite_enabled,
            'server_type'               => $_SERVER['SERVER_SOFTWARE'],
            'php_version'               => phpversion(),
            'mysql_version'             => $wpdb->db_version(),
            'wp_memory_limit'           => WP_MEMORY_LIMIT,
            'wp_debug_mode'             => $debug,
            'wp_lang'                   => $lang,
            'wp_max_upload_size'        => size_format( wp_max_upload_size() ),
            'php_max_post_size'         => ini_get( 'post_max_size' ),
            'hostname'                  => $host_name,
            'smtp'                      => ini_get('SMTP'),
            'smtp_port'                 => ini_get('smtp_port'),
            'active_plugins'            => $active_plugins,
        );

        $this->send( 'update_environment_vars', $environment );
    }
    
    /**
     * Package up our form data and send it to our API endpoint.
     * 
     * @since 3.2
     * @return void
     */
    public function form_data() {
        global $wpdb;
        
        // If we have not finished the process...
        if ( ! get_option( 'nf_form_tel_sent' ) || 'false' == get_option( 'nf_form_tel_sent' ) ) {
            // Get our list of already processed forms (if it exists).
            $forms_ref = get_option( 'nf_form_tel_data' );
            // Get a list of Forms on this site.
            $sql = "SELECT id FROM `" . $wpdb->prefix . "nf3_forms`";
            $forms = $wpdb->get_results( $sql, 'ARRAY_A' );
            // If our list of processed forms already exists...
            if ( ! empty( $forms_ref ) ) {
                // Break those into an array.
                $forms_ref = explode( ',', $forms_ref );
            } // Otherwise...
            else {
                // Make sure we have an array.
                $forms_ref = array();
            }
            $match_found = false;
            // For each form...
            foreach ( $forms as $form ) {
                // If the current form is not in our list of sent values...
                if ( ! in_array( $form[ 'id' ], $forms_ref ) ) {
                    // Set our target ID.
                    $id = $form[ 'id' ];
                    // Record that we found a match.
                    $match_found = true;
                }
            }
            // If we didn't find a match.
            if ( ! $match_found ) {
                // Record that we're done.
                update_option( 'nf_form_tel_sent', 'true', false );
                // Exit.
                return false;
            }// Otherwise... (We did find a match.)
            // Get our form.
            $form_data = Ninja_Forms()->form( intval( $id ) )->get();
            // Setup our data value.
            $data = array();
            // Set the form title.
            $data[ 'title' ] = $form_data->get_setting( 'title' );
            $sql = "SELECT COUNT(meta_id) AS total FROM `" . $wpdb->prefix . "postmeta` WHERE meta_key = '_form_id' AND meta_value = '" . intval( $id ) . "'";
            $result = $wpdb->get_results( $sql, 'ARRAY_A' );
            // Set the number of submissions.
            $data[ 'subs' ] = $result[ 0 ][ 'total' ];
            // Get our fields.
            $field_data = Ninja_Forms()->form( intval( $id ) )->get_fields();
            $data[ 'fields' ] = array();
            // For each field on the form...
            foreach ( $field_data as $field ) {
                // Add that data to our array.
                $data[ 'fields' ][] = $field->get_setting( 'type' );
            }
            // Get our actions.
            $action_data = Ninja_Forms()->form( intval( $id ) )->get_actions();
            $data[ 'actions' ] = array();
            // For each action on the form...
            foreach ( $action_data as $action ) {
                // Add that data to our array.
                $data[ 'actions' ][] = $action->get_setting( 'type' );
            }
            // Add this form ID to our option.
            $forms_ref[] = $id;
            // Update our option.
            update_option( 'nf_form_tel_data', implode( ',', $forms_ref ), false );
            $this->send( 'form_data', $data );
        }
    }

    /**
     * Sends a campaign slug and data to our API endpoint.
     * Checks to ensure that the user has 1) opted into tracking or 2) they have a premium add-on installed.
     * 
     * @since  3.2
     * @param  string       $slug   Campaign slug
     * @param  array        $data   Array of data being sent. Should NOT already be a JSON string.
     * @return void
     */
    public function send( $slug, $data = array() ) {

        if ( ! $this->should_we_send() ) {
            return false;
        }

        /**
         * Gather site data before we send.
         *
         * We send the following site data with our passed data:
         * IP Address
         * Email
         * Site Url
         */

        $ip_address = '';
        if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) ) {
            $ip_address = $_SERVER[ 'SERVER_ADDR' ];
        } else if ( array_key_exists( 'LOCAL_ADDR', $_SERVER ) ) {
            $ip_address = $_SERVER[ 'LOCAL_ADDR' ];
        }

        /**
         * Email address of the current user.
         * (if one was provided)
         */
        $email = isset( $data[ 'user_email' ] ) ? $data[ 'user_email' ] : '';

        $site_data = array(
            'url'           => site_url(),
            'ip_address'    => $ip_address,
            'email'         => $email,
        );

        /*
         * Send our data using wp_remote_post.
         */
         $response = wp_remote_post(
            $this->api_url,
            array(
                'body' => array(
                    'slug'          => $slug,
                    'data'          => wp_json_encode( $data ),
                    'site_data'     => wp_json_encode( $site_data ),
                ),
            )
        );
    }
}
