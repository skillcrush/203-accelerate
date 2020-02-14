<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_REST_RequiredUpdate extends NF_AJAX_REST_Controller
{
	private $updates = array();
	private $running = array();
	
    protected $action = 'nf_required_update';
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * POST
     * @param array $request_data [ int $clone_id ]
     * @return array $data [ int $new_form_id ]
     */
    public function post( $request_data )
    {
		$data = array();
		
		// Does the current user have admin privileges
		if (!current_user_can('manage_options')) {
			$data['error'] = esc_html__('Access denied. You must have admin privileges to perform this action.', 'ninja-forms');
			return $data;
		}

        // If we don't have a nonce...
        // OR if the nonce is invalid...
        if ( ! isset( $request_data[ 'security' ] ) || ! wp_verify_nonce( $request_data[ 'security' ], 'ninja_forms_required_update_nonce' ) ) {
            // Kick the request out now.
            $data[ 'error' ] = esc_html__( 'Request forbidden.', 'ninja-forms' );
			return $data;
        }
		$doing_updates = get_option( 'ninja_forms_doing_required_updates' );
		// If we're not already doing updates...
		if ( ! $doing_updates ) {
			// Get our list of already run updates.
			$processed = get_option( 'ninja_forms_required_updates', array() );
			// Get our list of updates to run.
			$this->updates = Ninja_Forms()->config( 'RequiredUpdates' );
			// Sort our updates.
			$this->running = $this->sort_updates( $this->updates, $processed );
		} // Otherwise... (We are already processing updates.)
		else {
			$this->running = $doing_updates;
		}
		// Call the class of our current update.
		$class = $this->running[ 0 ][ 'class_name' ];
		$update_class = new $class( $request_data, $this->running );
    }

	/**
	 * GET
	 * @param $request_data (Array)
	 * @return $data (Array)
	 * 
	 * @since 3.4.0
	 */
	public function get( $request_data ) {
        
        $data = array();
		$data[ 'updates' ] = array();

        // Get our list of already run updates.
        $processed = get_option( 'ninja_forms_required_updates', array() );
        // Get our list of updates yet to be run.
        $this->updates = Ninja_Forms()->config( 'RequiredUpdates' );
        // Sort our updates.
        $sorted = $this->sort_updates( $this->updates, $processed );
        $data[ 'updates' ] = $sorted;
        return $data;
	}

	protected function get_request_data()
	{
		$request_data = array();

		if (isset($_REQUEST['data']) && $_REQUEST['data']) {
			$request_data['data'] = WPN_Helper::sanitize_text_field($_REQUEST['data']);
		}

		if( isset( $_REQUEST[ 'security' ] ) && $_REQUEST[ 'security' ] ){
			$request_data[ 'security' ] = WPN_Helper::sanitize_text_field($_REQUEST[ 'security' ]);
		}

		if( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] ){
			$request_data[ 'action' ] = WPN_Helper::sanitize_text_field($_REQUEST[ 'action' ]);
		}

		return $request_data;
	}

	/**
	 * Function to get the list of updates that need to run.
	 * 
	 * @param $processed (Array) The list of updates that have already run on this install.
	 * @return Array
	 */
	private function get_current_updates( $processed ) {
		$updates = array();
		// For each update in the list...
		foreach ( $this->updates as $slug => $update ) {
			// If we've not already processed it...
			if ( ! isset( $processed[ $slug ] ) ) {
				// Add it to our list.
				$updates[ $slug ] = $update;
			}
		}
		return $updates;
	}

	/**
	 * Function to sort the updates to be run.
	 * 
	 * @param $current (Array) The list of updates to be run.
	 * @param $previous (Array) The list of updates that have already been run.
	 * @return Array
	 */
	private function sort_updates( $current, $previous ) {
		$sorted = array();
		$queue = array();
		// While we have not finished sorting updates...
		while ( count( $sorted ) < count( $current ) ) {
			// For each update we wish to run...
			foreach ( $current as $slug => $update ) {
				// Migrate the slug to a property.
				$update[ 'slug' ] = $slug;
				// If we've not already added this to the sorted list...
				if ( ! in_array( $update, $sorted ) ) {
					// If it has requirements...
					if ( ! empty( $update[ 'requires' ] ) ) {
						$enqueued = 0;
						// For each requirement...
						foreach ( $update[ 'requires' ] as $requirement ) {
							// If the requirement doesn't exist...
							if ( ! isset( $this->updates[ $requirement ] ) ) {
								// unset the update b/c we are missing requirements
								unset( $current[ $slug ] );
								unset( $this->updates[ $slug ] );
							}
							// If the requirement has already been added to the stack...
							if ( in_array( $requirement, $queue ) ) {
								$enqueued++;
							} // OR If the requirement has already been processed...
							elseif ( isset( $previous[ $requirement ] ) ) {
								$enqueued++;
							}
						}
						// If all requirement are met...
						if ( $enqueued == count( $update[ 'requires' ] ) ) {
							// Add it to the list.
							array_push( $sorted, $update );
							// Record that we enqueued it.
							array_push( $queue, $slug );
						}
					} // Otherwise... (It has no requirements.)
					else {
						// Add it to the list.
						array_push( $sorted, $update );
						// Record that we enqueued it.
						array_push( $queue, $slug );
					}
				}
			}
		}
		return $sorted;
	}

}
