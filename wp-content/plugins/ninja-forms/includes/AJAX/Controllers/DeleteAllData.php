<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_Controllers_DeleteAllData extends NF_Abstracts_Controller
{
	public function __construct()
	{
		// Ajax call handled in 'delete_all_data' in this file.
		add_action( 'wp_ajax_nf_delete_all_data', array( $this, 'delete_all_data' ) );
	}

	public function delete_all_data()
	{
		// Does the current user have admin privileges
		if (!current_user_can('manage_options')) {
			$this->_data['errors'] = esc_html__('Access denied. You must have admin privileges to perform this action.', 'ninja-forms');
			$this->_respond();
		}

		// If we don't have a nonce...
        // OR if the nonce is invalid...
        if (!isset($_REQUEST['security']) || !wp_verify_nonce($_REQUEST['security'], 'ninja_forms_settings_nonce')) {
            // Kick the request out now.
            $this->_data['errors'] = esc_html__('Request forbidden.', 'ninja-forms');
            $this->_respond();
        }

		check_ajax_referer( 'ninja_forms_settings_nonce', 'security' );

		global $wpdb;
		$total_subs_deleted = 0;
		$post_result = 0;
		$max_cnt = 500;

		if (!isset($_POST['form']) || empty($_POST['form'])) {
			$this->_respond();
		}

		$form_id = absint($_POST[ 'form' ]);
		// SQL for getting 250 subs at a time
		$sub_sql = "SELECT id FROM `" . $wpdb->prefix . "posts` AS p
			LEFT JOIN `" . $wpdb->prefix . "postmeta` AS m ON p.id = m.post_id
			WHERE p.post_type = 'nf_sub' AND m.meta_key = '_form_id'
			AND m.meta_value = %s LIMIT " . $max_cnt;

		while ($post_result <= $max_cnt ) {
			$subs = $wpdb->get_col( $wpdb->prepare( $sub_sql, $form_id ),0 );
			// if we are out of subs, then stop
			if( 0 === count( $subs ) ) break;
			// otherwise, let's delete the postmeta as well
			$delete_meta_query = "DELETE FROM `" . $wpdb->prefix . "postmeta` WHERE post_id IN ( [IN] )";
			$delete_meta_query = $this->prepare_in( $delete_meta_query, $subs );
			$meta_result       = $wpdb->query( $delete_meta_query );
			if ( $meta_result > 0 ) {
				// now we actually delete the posts(nf_sub)
				$delete_post_query = "DELETE FROM `" . $wpdb->prefix . "posts` WHERE id IN ( [IN] )";
				$delete_post_query = $this->prepare_in( $delete_post_query, $subs );
				$post_result       = $wpdb->query( $delete_post_query );
				$total_subs_deleted = $total_subs_deleted + $post_result;

			}
		}

		$this->_data[ 'form_id' ] = $form_id;
		$this->_data[ 'delete_count' ] = $total_subs_deleted;
		$this->_data[ 'success' ] = true;

		if ( 1 == $_POST[ 'last_form' ] ) {
			//if we are on the last form, then deactivate and nuke db tables
			$migrations = new NF_Database_Migrations();
			$migrations->nuke(TRUE, TRUE);
			$migrations->nuke_settings(TRUE, TRUE);
			$migrations->nuke_deprecated(TRUE, TRUE);
			deactivate_plugins( 'ninja-forms/ninja-forms.php' );
			$this->_data[ 'plugin_url' ] = admin_url( 'plugins.php' );
		}

		$this->_respond();
	}

	 private function prepare_in( $sql, $vals ) {
		global $wpdb;
		$not_in_count = substr_count( $sql, '[IN]' );
		if ( $not_in_count > 0 ) {
			$args = array( str_replace( '[IN]', implode( ', ', array_fill( 0, count( $vals ), '%d' ) ), str_replace( '%', '%%', $sql ) ) );
			// This will populate ALL the [IN]'s with the $vals, assuming you have more than one [IN] in the sql
			for ( $i=0; $i < substr_count( $sql, '[IN]' ); $i++ ) {
				$args = array_merge( $args, $vals );
			}
			$sql = call_user_func_array( array( $wpdb, 'prepare' ), array_merge( $args ) );
		}
		return $sql;
	}


}
