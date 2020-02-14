<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Updates_CacheCollateForms
 * 
 * This class manages the step process of running through the CacheCollateObjects required update.
 * It will define an object to pull data from (if necessary) to pick back up if exited early.
 * It will run an upgrade function to alter the nf3_objects and nf3_object_meta tables.
 * Then, it will step over each object in the db, following this process:
 * - Append the object_title
 * Then, it will step over each object_meta in the db, following this process:
 * - Copy over the meta_key
 * - Append the meta_value
 * After completing the above for every form on the site, it will remove the data object that manages its location.
 */
class NF_Updates_CacheCollateObjects extends NF_Abstracts_RequiredUpdate
{
    
    private $data = array();
    
    private $running = array();
    
    /**
     * The table names for our database queries.
     */
    private $table;
    private $meta_table;
    
    /**
     * The row counts of our database tables.
     */
    private $table_rows = 0;
    private $meta_rows = 0;
    
    /**
     * The denominator object for calculating our steps.
     * @var Integer
     */
    private $divisor = 500;

    /**
     * Constructor
     * 
     * @param $data (Array) The data object passed in by the AJAX call.
     * @param $running (Array) The array of required updates being run.
     * 
     * @since 3.4.0
     */
    public function __construct( $data = array(), $running )
    {
        // Build our arguments array.
        $args = array(
            'slug' => 'CacheCollateObjects',
            'class_name' => 'NF_Updates_CacheCollateObjects',
            'debug' => false,
        );
        $this->data = $data;
        $this->running = $running;

        // Call the parent constructor.
        parent::__construct( $args );
        
        // Set our table names.
        $this->table = $this->db->prefix . 'nf3_objects';
        $this->meta_table = $this->db->prefix . 'nf3_object_meta';

        // Begin processing.
        $this->process();
    }

    /**
     * Function to loop over the batch.
     * 
     * @since 3.4.0
     */
    public function process()
    {
        // If we've not already started...
        if ( ! isset( $this->running[ 0 ][ 'running' ] ) ) {
            // Run our startup method.
            $this->startup();
        } // Otherwise... (We're picking up an old process.)
        else {
            // Get our number of remaining table rows.
            if ( isset( $this->running[ 0 ][ 'table_rows' ] ) ) {
                $this->table_rows = intval( $this->running[ 0 ][ 'table_rows' ] );
            }
            // Get our number of remaining meta rows.
            if ( isset( $this->running[ 0 ][ 'meta_rows' ] ) ) {
                $this->meta_rows = intval( $this->running[ 0 ][ 'meta_rows' ] );
            }
        }

        // Update values in the objects table.
        $this->maybe_update_objects();
        
        // Update values in the object_meta table.
        $this->maybe_update_object_meta();

        // Increment our step count.
        $this->running[ 0 ][ 'current' ] += 1;
        // Prepare to output our number of steps and current step.
        $this->response[ 'stepsTotal' ] = $this->running[ 0 ][ 'steps' ];
        $this->response[ 'currentStep' ] = $this->running[ 0 ][ 'current' ];
        $this->running[ 0 ][ 'table_rows' ] = $this->table_rows;
        $this->running[ 0 ][ 'meta_rows' ] = $this->meta_rows;
        // If we have no meta left to update at this point...
        if ( 0 >= $this->table_rows && 0 >= $this->meta_rows ) {
            // Run our cleanup process.
            $this->cleanup();
        }
        // Prepare to output the number of updates remaining.
        $this->response[ 'updatesRemaining' ] = count( $this->running );

        // Record our current location in the process.
        update_option( 'ninja_forms_doing_required_updates', $this->running );

        // Respond to the AJAX call.
        $this->respond();
    }

    /**
     * Function to run any setup steps necessary to begin processing.
     * 
     * @since 3.4.0
     */
    public function startup()
    {
        // Record that we're processing the update.
        $this->running[ 0 ][ 'running' ] = true;
        // If we're not debugging...
        if ( ! $this->debug ) {
            // Ensure that our data tables are updated.
            $this->migrate( 'cache_collate_objects' );
            // Set out new db version.
            update_option( 'ninja_forms_db_version', '1.4' );
        }
        // Get the number of rows in the objects table.
        $sql = "SELECT COUNT( `id` ) as Total FROM `{$this->table}`";
        $result = $this->db->get_results( $sql, 'ARRAY_A' );
        // If we got something back...
        if ( ! empty( $result ) ) {
            // Record the total.
            $this->table_rows = intval( $result[ 0 ][ 'Total' ] );
        }
        // If the table was empty...
        if ( 0 == $this->table_rows ) {
            /**
             * Clean out the object_meta table.
             * It should contain nothing if there is nothing in the objects table.
             */
            $sql = "DELETE FROM `{$this->meta_table}`";
            $this->query( $sql );
            // Lock processing.
            $this->lock_process = true;
        }
        // If processing is locked...
        if ( $this->lock_process ) {
            // Prepare to output our number of steps and current step.
            $this->response[ 'stepsTotal' ] = 1;
            $this->response[ 'currentStep' ] = 1;
            // Skip straight to our cleanup method.
            $this->cleanup();
            // Prepare to output the number of updates remaining.
            $this->response[ 'updatesRemaining' ] = count( $this->running );
            // Record our current location in the process.
            update_option( 'ninja_forms_doing_required_updates', $this->running );
            $this->respond();
        }
        // Get the number of rows in the object_meta table.
        $sql = "SELECT COUNT( `id` ) as Total FROM `{$this->meta_table}`";
        $result = $this->db->get_results( $sql, 'ARRAY_A' );
        // If we got something back...
        if ( ! empty( $result ) ) {
            // Record the total.
            $this->meta_rows = intval( $result[ 0 ][ 'Total' ] );
        }
        $steps = ceil( $this->table_rows / $this->divisor );
        $steps += ceil( $this->meta_rows / $this->divisor );
        
        // Record the total number of steps in this batch.
        $this->running[ 0 ][ 'steps' ] = $steps;
        // Record our current step (defaulted to 0 here).
        $this->running[ 0 ][ 'current' ] = 0;
    }

    /**
     * Function to cleanup any lingering temporary elements of a required update after completion.
     * 
     * @since 3.4.0
     */
    public function cleanup()
    {
        // Remove the current process from the array.
        array_shift( $this->running );
        // Record to our updates setting that this update is complete.
        $this->confirm_complete();
        // If we have no updates left to process...
        if ( empty( $this->running ) ) {
            // Call the parent cleanup method.
            parent::cleanup();
        }
    }

    /**
     * Function to manage the updating of our objects table.
     * 
     * @return Void
     * 
     * @since 3.4.0
     */
    private function maybe_update_objects()
    {
        // If we have no table rows left to process, exit early.
        if ( 0 >= $this->table_rows ) return false;
        // Compile our query.
        $sql = "SELECT `id`, `title` FROM `{$this->table}` ";
        // If we are picking up an old process...
        if ( isset( $this->running[ 0 ][ 'last_updated' ] ) ) {
            // Make sure we don't fetch old values.
            $sql .= "WHERE `id` > " . intval( $this->running[ 0 ][ 'last_updated' ] ) . " ";
        }
        // Ensure that we gate the number of records that we fetch.
        $sql .= "ORDER BY `id` ASC LIMIT {$this->divisor}";
        $result = $this->db->get_results( $sql, 'ARRAY_A' );
        // Build our Update query.
        $sub_sql = array();
        foreach ( $result as $object ) {
            array_push( $sub_sql, "WHEN `id` = " . intval( $object[ 'id' ] ) . " THEN '" . $this->prepare( $object[ 'title' ] ) . "'" );
        }
        // If we have values to update...
        if ( ! empty( $sub_sql ) ) {
            // Run the update.
            $sql = "UPDATE `{$this->table}` SET `object_title` = CASE " . implode( ' ', $sub_sql ) . " ELSE `object_title` END;";
            $this->query( $sql );
            // Get the last item updated.
            $last = array_pop( $result );
            // Record it to our process object.
            $this->running[ 0 ][ 'last_updated' ] = $last[ 'id' ];
            // Also record that we ran the update on this table.
            $this->running[ 0 ][ 'updating_table' ] = $this->table;
            // Reduce our table rows by the divisor.
            $this->table_rows = $this->table_rows - $this->divisor;
            // Lock processing.
            $this->lock_process = true;
        }
    }

    /**
     * Function to manage the updating of our object_meta table.
     * 
     * @return Void
     * 
     * @since 3.4.0
     */
    private function maybe_update_object_meta()
    {
        // If we've locked processing, exit early.
        if ( $this->lock_process ) return false;
        // If we have no meta rows left to process, exit early.
        if ( 0 >= $this->meta_rows ) return false;
        // Compile our query.
        $sql = "SELECT `id`, `key`, `value` FROM `{$this->meta_table}` ";
        // If we are picking up an old process...
        if ( $this->meta_table == $this->running[ 0 ][ 'updating_table' ] ) {
            // Make sure we don't fetch old values.
            $sql .= "WHERE `id` > " . intval( $this->running[ 0 ][ 'last_updated' ] ) . " ";
        }
        // Ensure that we gate the number of records that we fetch.
        $sql .= "ORDER BY `id` ASC LIMIT {$this->divisor}";
        $result = $this->db->get_results( $sql, 'ARRAY_A' );
        // Build our Update query.
        $sub_sql = array();
        $meta_ids = array();
        foreach ( $result as $object ) {
            array_push( $sub_sql, "WHEN `id` = " . intval( $object[ 'id' ] ) . " THEN '" . $this->prepare( $object[ 'value' ] ) . "'" );
            array_push( $meta_ids, $object[ 'id' ] );
        }
        // If we have values to update...
        if ( ! empty( $sub_sql ) ) {
            // Run the update on the meta_key column.
            $sql = "UPDATE `{$this->meta_table}` SET `meta_key` = `key` WHERE `id` IN(" . implode( ', ', $meta_ids ) . ");";
            $this->query( $sql );
            // Run the update on the meta_value column.
            $sql = "UPDATE `{$this->meta_table}` SET `meta_value` = CASE " . implode( ' ', $sub_sql ) . " ELSE `meta_value` END;";
            $this->query( $sql );
            // Get the last item updated.
            $last = array_pop( $result );
            // Record it to our process object.
            $this->running[ 0 ][ 'last_updated' ] = $last[ 'id' ];
            // Also record that we ran the update on this table.
            $this->running[ 0 ][ 'updating_table' ] = $this->meta_table;
            // Reduce our meta rows by the divisor.
            $this->meta_rows -= $this->divisor;
            // Lock processing.
            $this->lock_process = true;
        }
    }

}