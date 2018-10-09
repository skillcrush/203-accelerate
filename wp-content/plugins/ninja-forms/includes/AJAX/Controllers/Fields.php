<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_Controllers_Fields extends NF_Abstracts_Controller
{
	private $publish_processing;

	public function __construct()
	{
		add_action( 'wp_ajax_nf_maybe_delete_field', array( $this,
			'maybe_delete_field' ) );

	}

	/**
	 * Check if the field has data, if so let the front-end know to show
	 * delete field modal
	 */
	public function maybe_delete_field() {
		$field_id = $_REQUEST[ 'fieldID' ];
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
