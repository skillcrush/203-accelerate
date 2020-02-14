<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_Controllers_Fields extends NF_Abstracts_Controller
{
	private $publish_processing;

	public function __construct()
	{
		// Ajax call handed in 'maybe_delete_field' in this file
		add_action( 'wp_ajax_nf_maybe_delete_field', array( $this,
			'maybe_delete_field' ) );

	}

	/**
	 * Check if the field has data, if so let the front-end know to show
	 * delete field modal
	 */
	public function maybe_delete_field() {

		// Does the current user have admin privileges
		if (!current_user_can('manage_options')) {
			$this->_data['errors'] = esc_html__('Access denied. You must have admin privileges to perform this action.', 'ninja-forms');
			$this->_respond();
		}

		// If we don't have a nonce...
        // OR if the nonce is invalid...
        if (!isset($_REQUEST['security']) || !wp_verify_nonce($_REQUEST['security'], 'ninja_forms_builder_nonce')) {
            // Kick the request out now.
            $this->_data['errors'] = esc_html__('Request forbidden.', 'ninja-forms');
            $this->_respond();
        }

		if (!isset($_REQUEST['fieldID']) || empty($_REQUEST['fieldID'])) {
			$this->_respond();
		}
		$field_id = absint($_REQUEST[ 'fieldID' ]);
//		$field_key = $_REQUEST[ 'fieldKey' ];

		global $wpdb;
		// query for checking postmeta for submission data for field
		$sql = $wpdb->prepare( "SELECT meta_value FROM `" . $wpdb->prefix . "postmeta` 
			WHERE meta_key = '_field_%d' LIMIT 1", $field_id );
		$result = $wpdb->get_results( $sql, 'ARRAY_N' );

		$has_data = false;

		// if there are results, has_data is true
		if( 0 < count( $result ) ) {
			$has_data = true;
		}
		$this->_data[ 'field_has_data' ] = $has_data;

		$this->_respond();
	}
}
