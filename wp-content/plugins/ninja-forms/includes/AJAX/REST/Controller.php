<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * A controller extensions for mapping REST requests to an admin-ajax action.
 */
abstract class NF_AJAX_REST_Controller extends NF_Abstracts_Controller
{
    /**
     * The name of the admin-ajax action.
     * @var string
     */
    protected $action;

    /**
     * Setup admin-ajax to access the endpoint router.
     */
    public function __construct()
    {
        if( $this->action ) {
            add_action('wp_ajax_' . $this->action, array($this, 'route'));
        }
    }

    /**
     * Map admin-ajax requests to the corresponding method callback.
     */
    public function route()
    {
        register_shutdown_function( array( $this, 'shutdown' ) );

        $method = strtolower( $_SERVER['REQUEST_METHOD'] );

        /*
         * Request Method Override
         * Allows for a POST request to function as another Request Method
         *   by passing a `method_override` value as a request parameter.
         * For example, some servers do not support the DELETE request method.
         */
        if( 'post' == $method and isset( $_REQUEST[ 'method_override' ] ) ){
            $method = sanitize_text_field( $_REQUEST[ 'method_override' ] );
        }

        if( ! method_exists( $this, $method ) ){
            $this->_errors[] = __( 'Endpoint does not exist.', 'ninja-forms' );
            $this->_respond();
        }

        $request_data = $this->get_request_data();

        try {
            $data = $this->$method($request_data);
            $this->_respond( $data );
        } catch( Exception $e ) {
            $this->_errors[] = $e->getMessage();
        }
        $this->_respond();
    }

    /**
     * [OVERRIDE THIS] Get sanitized request data for use in method callbacks.
     * @return array
     */
    protected function get_request_data()
    {
        // This section intentionally left blank.

        /*
         * [Example] FORM ID
         */
//        if( isset( $_REQUEST[ 'form_id' ] ) && $_REQUEST[ 'form_id' ] ){
//            $request_data[ 'form_id' ] = absint( $_REQUEST[ 'form_id' ] );
//        }

        return array();
    }

    /**
     * Returns debugging data when a fatal error is triggered.
     */
    public function shutdown()
    {
        $error = error_get_last();
        if( $error !== NULL && in_array( $error[ 'type' ], array( E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR ) ) ) {

            $this->_errors[ 'form' ][ 'last' ] = __( 'The server encountered an error during processing.', 'ninja-forms' );

            if( current_user_can( 'manage_options' ) && isset( $error[ 'message' ] ) ){
                $this->_errors[ 'form' ][ 'last_admin' ] = '<pre>' . $error[ 'message' ] . '</pre>';
            }

            $this->_errors[ 'last' ] = $error;
            $this->_respond();
        }
    }
}
