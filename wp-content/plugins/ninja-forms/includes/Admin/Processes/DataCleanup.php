<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Abstracts_Batch_Process
 */
class NF_Admin_Processes_DataCleanup extends NF_Abstracts_BatchProcess
{
    private $response = array(
        'batch_complete' => false
    );
    protected $delete = array();
    
    /**
     * Constructor
     */
    public function __construct( $data = array() )
    {
        //Bail if we aren't in the admin.
        if ( ! is_admin() )
            return false;
        // Run process.
        $this->process();
    }


    /**
     * Function to loop over the batch.
     */
    public function process()
    {
        global $wpdb;
        // If we've not already started the cleanup process...
        if ( ! get_option( 'nf_doing_data_cleanup' ) ) {
            // Run the startup process.
            $this->startup();
        } // Otherwise... (We've already run startup.)
        else {
            // Get our data.
            $data = get_option( 'nf_data_cleanup_ids' );
            $this->delete = explode( ',', $data );
        }
        // If our array isn't emtpy...
        if ( ! empty( $this->delete ) ) {
            // Fetch the last item on it.
            $id = array_pop( $this->delete );
            // Get a list of post IDs to delete.
            $sql = "SELECT DISTINCT(`id`) FROM `" . $wpdb->prefix . "posts` WHERE `id` IN( SELECT DISTINCT(`post_id`) FROM `" . $wpdb->prefix . "postmeta` WHERE `meta_key` = '_form_id' AND `meta_value` = '" . $id . "' ) AND `post_type` = 'nf_sub' LIMIT 500";
            $result = $wpdb->get_results( $sql, 'ARRAY_A' );
            // If we got 500 or more results...
            if ( 500 == count( $result ) ) {
                // Put this id back in our array to run again.
                array_push( $this->delete, $id );
            }
            // Convert our results to something we can use in a query.
            array_walk( $result, array( $this, 'smush_results' ) );
            $sub_sql = implode( ', ', $result );
            // If we have something to query...
            if ( '' != $sub_sql ) {
                // Delete postmeta data.
                $sql = "DELETE FROM `" . $wpdb->prefix . "postmeta` WHERE `post_id` IN(" . $sub_sql . ")";
                $wpdb->query( $sql );
                // Delete post data.
                $sql = "DELETE FROM `" . $wpdb->prefix . "posts` WHERE `id` IN(" . $sub_sql . ")";
                $wpdb->query( $sql );
            }
        }
        // If our array isn't empty...
        if ( ! empty( $this->delete ) ) {
            // Determine how many steps we have left.
            $this->response[ 'step_remaining' ] = count( $this->delete );
            update_option( 'nf_data_cleanup_ids', implode( ',', $this->delete ) );
            echo wp_json_encode( $this->response );
            wp_die();
        }
        // Run our cleanup process.
        $this->cleanup();
        echo wp_json_encode( $this->response );
        wp_die();
    }


    /**
     * Function to run any setup steps necessary to begin processing.
     */
    public function startup()
    {
        global $wpdb;
        // Get a list of IDs from the forms table.
        $sql = "SELECT DISTINCT(`id`) FROM `" . $wpdb->prefix . "nf3_forms`";
        $forms = $wpdb->get_results( $sql, 'ARRAY_A' );
        // Get a list of IDs from the Submissions data.
        $sql = "SELECT DISTINCT(m.meta_value) AS id FROM `" . $wpdb->prefix . "postmeta` AS m LEFT JOIN `" . $wpdb->prefix . "posts` AS p on p.id = m.post_id WHERE m.meta_key = '_form_id' AND p.post_type = 'nf_sub'";
        $sub_forms = $wpdb->get_results( $sql, 'ARRAY_A' );
        // For each form ID in the submission records...
        foreach( $sub_forms AS $form ) {
            // If the form is not currently defined in our forms table...
            if ( ! in_array( $form, $forms ) ) {
                // Add it to our list of things to delete.
                $this->delete[] = $form[ 'id' ];
            }
        }
        // Get our number of steps for the progress bar.
        $this->response[ 'step_total' ] = count( $this->delete );
        // Flag startup done.
        add_option( 'nf_doing_data_cleanup', 'true' );
    }


    /**
     * Function to cleanup any lingering temporary elements of a batch process after completion.
     */
    public function cleanup()
    {
        global $wpdb;
        // Delete our options.
        delete_option( 'nf_data_cleanup_ids' );
        delete_option( 'nf_doing_data_cleanup' );
        // Add our "finished" option.
        add_option( 'ninja_forms_data_is_clean', 'true' );
        // Tell our JS that we're done.
        $this->response[ 'step_remaining' ] = 0;
        $this->response[ 'batch_complete' ] = true;
    }
    
    /**
     * Function to compress data array and eliminate unnecessary keys.
     */
    public function smush_results( &$value, $key ) {
        $value = $value[ 'id' ];
    }
}