<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_REST_BatchProcess extends NF_AJAX_REST_Controller
{
    protected $action = 'nf_batch_process';
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * POST /forms/<id>/
     * @param array $request_data [ int $clone_id ]
     * @return array $data [ int $new_form_id ]
     */
    public function post( $request_data )
    {
        $data = array();

        // If we don't have a nonce...
        // OR if the nonce is invalid...
        if ( ! isset( $request_data[ 'security' ] ) || ! wp_verify_nonce( $request_data[ 'security' ], 'ninja_forms_batch_nonce' ) ) {
            // Kick the request out now.
            $data[ 'error' ] = __( 'Request forbidden.', 'ninja-forms' );
        }
        // If we have a batch type...
        if ( isset( $request_data[ 'batch_type' ]) ){
            $batch_type = $request_data[ 'batch_type' ];
            // Route the request to the proper controller.
            switch ( $batch_type ) {
                case 'chunked_publish':
	                $batch = new NF_Admin_Processes_ChunkPublish(
	                	$request_data );
                    break;
                case 'data_cleanup':
                    $batch = new NF_Admin_Processes_DataCleanup(
                        $request_data );
                    break;
                case 'expired_submission_cleanup':
                    $batch = new NF_Admin_Processes_ExpiredSubmissionCleanup(
                        $request_data );
                    break;
                default:
                    $data[ 'error' ] = __( 'Invalid request.', 'ninja-forms' );
                    break;
            }
        } // Otherwise... (We don't have a batch type.)
        else {
            // Kick the request out.
            $data[ 'error' ] = __( 'Invalid request.', 'ninja-forms' );
        }
        return $data;
    }

	protected function get_request_data()
	{
		$request_data = array();

		if( isset( $_REQUEST[ 'batch_type' ] ) && $_REQUEST[ 'batch_type' ] ){
			$request_data[ 'batch_type' ] = $_REQUEST[ 'batch_type' ];
		}

		if( isset( $_REQUEST[ 'data' ] ) && $_REQUEST[ 'data' ] ){
			$request_data[ 'data' ] = $_REQUEST[ 'data' ];
		}

		if( isset( $_REQUEST[ 'security' ] ) && $_REQUEST[ 'security' ] ){
			$request_data[ 'security' ] = $_REQUEST[ 'security' ];
		}

		if( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] ){
			$request_data[ 'action' ] = $_REQUEST[ 'action' ];
		}

		return $request_data;
	}
}
