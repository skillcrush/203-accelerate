<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Updates_CacheFieldReconcilliation
 * 
 * This allows us to make sure that the data being saved to the database is in the fields table.
 */
class NF_Updates_CacheFieldReconcilliation extends NF_Abstracts_RequiredUpdate
{
    
    private $data = array();
    
    private $running = array();
    
    /**
     * Non-associatve array of field ids from the cache.
     * @var array
     */
    private $field_ids = array();

    /**
     * columns to retrieve from meta table
     */
    private $meta_keys = array(
        'label',
        'key',
        'order',
        'required',
        'default',
        'label_pos',
        'personally_identifiable'
    );

    /**
     * The denominator object for calculating our steps.
     * @var Integer
     */
    private $divisor = 200;
    
    /**
     * The table names for our database queries.
     */
    private $table;
    private $meta_table;

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
            'slug' => 'CacheFieldReconcilliation',
            'class_name' => 'NF_Updates_CacheFieldReconcilliation',
            'debug' => false,
        );
        $this->data = $data;
        $this->running = $running;

        // Call the parent constructor.
        parent::__construct( $args );
        
        // Set our table names.
        $this->table = $this->db->prefix . 'nf3_fields';
        $this->meta_table = $this->db->prefix . 'nf3_field_meta';

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
        }

        /**
         * Get the next round of fields to update
         */
        $this->get_fields_this_step();

        /**
         * Update fields
         */
        $this->update_fields();

        /**
         * Saves our current location, along with any processing data we may need for the next step.
         * If we're done with our step, runs cleanup instead.
         */
        $this->end_of_step();

        /**
         * Respond to the AJAX call.
         */
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
        
        $sql = "SELECT `id` FROM `{$this->table}`";
        $fields = $this->db->get_results( $sql, 'ARRAY_A' );
        // Record the total number of steps in this batch.
        $this->running[ 0 ][ 'steps' ] = ceil(count( $fields ) / $this->divisor);
        // Record our current step (defaulted to 0 here).
        $this->running[ 0 ][ 'current' ] = 0;
    }

    public function get_fields_this_step() {

        $offset = 0;

        if( 0 < $this->running[ 0 ][ 'current' ] ) {
            $offset = $this->running[ 0 ][ 'current' ] * $this->divisor;
        }

        // Get a list of our fields...
        $sql = "SELECT `id` FROM `{$this->table}` LIMIT {$offset}, {$this->divisor}";
        $this->field_ids = $this->db->get_results( $sql, 'ARRAY_A' );
        $this->field_ids = $this->array_squash( $this->field_ids );
        // $this->running[ 0 ][ 'fields' ] = $this->field_ids;
    }

    /**
     * Update field table records with data from field meta
     */
    public function update_fields() {
        $field_meta = $this->get_field_meta();

        if($field_meta) {
            $update_query = $this->get_update_query( $field_meta );

            if( $update_query ) {
                $this->query($update_query);
            }
        }
    }

    /**
     * Get meta data to use for updating 
     */
    public function get_field_meta() {

        if(0 === count($this->field_ids)) return false;

        $in_fields = implode( ', ', $this->field_ids );
        $meta_keys = "'" . implode( "' , '", $this->meta_keys ) . "'";

        $meta_query = $this->get_field_meta_query();

        $results = $this->db->get_results( $meta_query, 'ARRAY_A');

        $meta_data = $this->format_field_meta($results);

        return $meta_data;
    }

    /**
     * Construct the query to get meta data
     * 
     * return String $meta_query
     */
    public function get_field_meta_query() {
        $in_fields = implode( ', ', $this->field_ids );
        $meta_keys = "'" . implode( "' , '", $this->meta_keys ) . "'";

        $meta_query = "SELECT `parent_id`, `key`, `meta_key`, `meta_value`, `value` FROM `{$this->meta_table}` WHERE `parent_id` IN ({$in_fields}) AND `key` IN ({$meta_keys}) ORDER BY `parent_id` ASC";

        return $meta_query;
    }

    /**
     * Format the data into format that helps us construct the insert/update query
     * 
     * @param Array $metadata
     * 
     * @return Array $meta_data
     */
    public function format_field_meta( $metadata ) {
        $meta_data = array();

        foreach( $metadata as $meta ) {
            $parent_id = $meta['parent_id'];
            foreach( $meta as $key => $val ) {

                if( 'parent_id' !== $key ) {
                    $meta_data[ $parent_id ][ $meta['key'] ] = $meta['value'];
                    $meta_data[ $parent_id ][ 'meta_' . $meta['meta_key'] ] = $meta['meta_value'];
                }
            }
        }

        return $meta_data;
    }

    /**
     * Construct field update query
     */
    public function get_update_query( $field_data ) {
        if( 0 === count( $field_data) ) return false;

        $sql = "INSERT INTO {$this->table} 
        (`id`, `label`, `key`, `field_label`, `field_key`, `order`, `required`, `default_value`, `label_pos`, `personally_identifiable`)
        VALUES";

        foreach( $field_data as $field_id => $meta ) {
            $sql .= "({$field_id}, '{$this->db->_real_escape($meta['label'])}', '{$this->db->_real_escape($meta['key'])}', '{$this->db->_real_escape($meta['meta_label'])}', '{$this->db->_real_escape($meta['meta_key'])}', {$meta['order']},";
            
            if( isset( $meta[ 'required' ] ) && '' !== $meta[ 'required' ]) {
                $sql .= "{$meta['required']},";
             } else {
                 $sql .= "0,";
             } 
             
             if(isset( $meta[ 'meta_default' ] ) ) {
                 $sql .= "'{$this->db->_real_escape($meta['meta_default'])}',";
              } else {
                  $sql .= "'',";
              } 
              
              if( isset( $meta[ 'meta_label_pos' ] ) ) {
                  $sql .= "'{$meta['meta_label_pos']}',";
              } else {
                  $sql .= "'',";
              }
            
            if(isset($meta['personally_identifiable'])) {
                $sql .= "{$meta['personally_identifiable']}";
            } else {
                $sql .= "0";
            }

            $sql .= "),";
        }

        $sql = rtrim( $sql, ',' );

        $sql .= "ON DUPLICATE KEY
            UPDATE
            `label` = VALUES(`label`),
            `key` = VALUES(`key`),
            `field_label` = VALUES(`field_label`),
            `field_key` = VALUES(`field_key`),
            `order` = VALUES(`order`),
            `required` = VALUES(`required`),
            `required` = VALUES(`required`),
            `default_value` = VALUES(`default_value`),
            `label_pos` = VALUES(`label_pos`),
            `personally_identifiable` = VALUES(`personally_identifiable`)";

        return $sql;
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
     * After we've done our processing, but before we get to step cleanup, we need to store process information.
     *
     * This method updates our form class var so that it can be passed to the next step.
     * If we've completed this step, it calls the cleanup method.
     * 
     * @since  3.4.0
     * @return void
     */
    private function end_of_step()
    {
        $this->running[ 0 ][ 'current' ] = intval( $this->running[ 0 ][ 'current' ] ) + 1;

        // Prepare to output our number of steps and current step.
        $this->response[ 'stepsTotal' ] = $this->running[ 0 ][ 'steps' ];
        $this->response[ 'currentStep' ] = $this->running[ 0 ][ 'current' ];
        
        if ( $this->divisor > count($this->field_ids)) {
            // Run our cleanup method.
            $this->cleanup();
        }
        
        // Record our current location in the process.
        update_option( 'ninja_forms_doing_required_updates', $this->running );
        // Prepare to output the number of updates remaining.
        $this->response[ 'updatesRemaining' ] = count( $this->running );
    }

    /**
    * Function to compress our db results into a more useful format.
    * 
    * @param $data (Array) The result to be compressed.
    * 
    * @return (Array) Associative if our data was complex.
    *                 Non-associative if our data was a single item.
    * 
    * @since UPDATE_VERSION_ON_MERGE
    */
    private function array_squash( $data )
    {
        $response = array();
        // For each item in the array...
        foreach ( $data as $row ) {
            // If the item has more than 1 attribute...
            if ( 1 < count( $row ) ) {
                // Assign the data to an associated result.
                $response[] = intval($row['id']);
                // Unset the id setting, as that will be the key.
                unset( $response[ $row[ 'id' ] ][ '' ] );
            } // Otherwise... (We only have 1 attribute.)
            else {
                // Add the id to the stack in a non-associated result.
                $response[] = intval( $row[ 'id' ] );
            }
        }
        return $response;
    }
}