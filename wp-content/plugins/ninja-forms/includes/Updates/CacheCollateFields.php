<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Updates_CacheCollateFields
 * 
 * This class manages the step process of running through the CacheCollateFields required update.
 * It will define an object to pull data from (if necessary) to pick back up if exited early.
 * It will run an upgrade function to alter the nf3_fields and nf3_field_meta tables.
 * Then, it will step over each form on the site, following this process:
 * - Fields that exist in the data tables but not in the cache will be deleted.
 * - Fields that exist in the cache but not in the data tables will be inserted.
 * - Fields that exist in the data tables but have an incorrect form ID will be inserted as a new ID and referenced from the cache.
 * - Fields that exist in both will be updated from the cache to ensure the data is correct.
 * After completing the above for every form on the site, it will remove the data object that manages its location.
 */
class NF_Updates_CacheCollateFields extends NF_Abstracts_RequiredUpdate
{
    
    private $data = array();
    
    private $running = array();
    
    /**
     * Non-associatve array of field ids from the cache.
     * @var array
     */
    private $field_ids = array();
    
    /**
     * Associative array of field ids from the cache, using the field id as the key.
     * $fields_by_id[ field_id ] = $settings;
     * @var array
     */
    private $fields_by_id = array();
    
    /**
     * Non-associative array that tracks what we should we insert because it exists in our cache but not in the Fields table.
     * @var array
     */
    private $insert = array();
    
    /**
     * Non-associatve array that tracks field ids that should be deleted from fields DB table.
     * @var array
     */
    private $delete = array();
    
    /**
     * Associative array that tracks field ids that have changed.
     * $submission_updates[ old_field_id ] = new_field_id;
     * @var array
     */
    private $submission_updates = array();
    
    /**
     * Associatve array that tracks newly inserted fields.
     * $insert_ids[ field_id ] = field_id;
     * @var array
     */
    private $insert_ids = array();

    /**
     * Hard limit for the number of querys we run during a single step.
     * @var integer
     */
    private $limit = 10;

    /**
     * Stores information about the current form being processed.
     * @var array
     */
    private $form;
    
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
            'slug' => 'CacheCollateFields',
            'class_name' => 'NF_Updates_CacheCollateFields',
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
         * Get all of our database variables up and running.
         * Sets up class vars that are used in subsequent methods.
         */
        $this->setup_vars();


        /**
         * Run SQL queries to delete fields if necessary.
         */
        $this->maybe_delete_fields();
        /**
         * Insert fields if necessary.
         * Also sets up the class var $submission_updates with duplicate ids that need replaced.
         */
        $this->maybe_insert_fields();
        /**
         * Update submission post meta if necessary.
         * Uses the class var $submission_updates setup in the method above.
         */
        $this->maybe_update_submissions();
        /**
         * If we have fields that exist in the DB for a form, update those with cache settings.
         */
        $this->maybe_update_fields();
        /**
         * Update our form cache with any field id changes.
         */
        $this->update_form_cache();
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
        // If we're not debugging...
        if ( ! $this->debug ) {
            // Ensure that our data tables are updated.
            $this->migrate( 'cache_collate_fields' );
            // Set out new db version.
            update_option( 'ninja_forms_db_version', '1.3' );
        }
        // Get a list of our forms...
        $sql = "SELECT ID FROM `{$this->db->prefix}nf3_forms`";
        $forms = $this->db->get_results( $sql, 'ARRAY_A' );
        $this->running[ 0 ][ 'forms' ] = $forms;
        // Record the total number of steps in this batch.
        $this->running[ 0 ][ 'steps' ] = count( $forms );
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
     * Function to delete unncessary items from our existing tables.
     * 
     * @param $items (Array) The list of ids to be deleted.
     * 
     * @since 3.4.0
     */
    public function maybe_delete_fields()
    {
        if ( empty( $this->delete ) ) {
            return false;
        }

        // Delete all meta for those fields.
        $sql = "DELETE FROM `{$this->meta_table}` WHERE parent_id IN(" . implode( ', ', $this->delete ) . ")";
        $this->query( $sql );
        // Delete the fields.
        $sql = "DELETE FROM `{$this->table}` WHERE id IN(" . implode( ', ', $this->delete ) . ")";
        $this->query( $sql );

        $this->delete = array();
    }

    /**
     * Most of the methods in this class use class vars to access and store data.
     *
     * This method sets the initial state of these class vars.
     * Class vars include:
     *    $form <- reference to the form currently being processed.
     *    $field_ids <- non-associatve array of field ids from the cache.
     *    $insert <- array that tracks what we should we insert because it exists in our cache but not in the Fields table.
     *    $submission_updates <- array that tracks fields that have had their id changed.
     *    $fields_by_id <- associative array of field ids from the cache, using the field id as the key.
     *
     * If we are not running a form for the first time, 
     * we set class vars based on what we have been passed. 
     * After setting those class vars, we bail early.
     * 
     * If we are running for the first time, set have to hit the database to
     * get the information for class vars.
     *
     * We need to compare the fields in our form cache to those in the fields table.
     * Ultimately, we're trying to make sure that our fields table matches our form cache.
     * 
     * Since we're treating the cache as the truth, we want to remove fields that don't exist in the cache.
     * We also want to insert any fields that exist in the cache, but not in the fields table.
     *
     * This method doesn't perform those operations, but it sets the class vars that the appropriate
     * methods use to figure out what to add and remove.
     *
     * @since  3.4.0
     * @return void
     */
    private function setup_vars()
    {
        // Set the form we're currently working with.
        $this->form = array_pop( $this->running[ 0 ][ 'forms' ] );
        // Enable maintenance mode on the front end when the fields start processing.
        $this->enable_maintenance_mode( $this->db->prefix, $this->form[ 'ID' ] );

        // Get the fields for our form from the cache.
        $form_cache = WPN_Helper::get_nf_cache( $this->form[ 'ID' ] );
        // Create an empty $fields array.
        $fields = array();
        /**
         * Loop over our cached form fields and instantiate a model for each.
         *     Update its settings to match those in the cache.
         *     Add it to our $fields array.
         */
        foreach( $form_cache[ 'fields' ] as $cached_field ){
            // Create a new model for this field.
            $field = new NF_Database_Models_Field( $this->db, $cached_field[ 'id' ], $this->form[ 'ID' ] );
            // Update settings to match cache.
            $field->update_settings( $cached_field[ 'settings' ] );
            // Add this to our $fields array, using the field id as the key.
            $fields[ $field->get_id() ] = $field;
        }

        /**
         * For each field in our cache, add it to our class vars:
         * field_ids <- non-associatve array of field ids from the cache.
         * fields_by_id <- associative array of field ids from the cache, using the field id as the key.
         */
        foreach ( $fields as $field ) {
            array_push( $this->field_ids, $field->get_id() );
            $this->fields_by_id[ $field->get_id() ] = $field->get_settings();
        }
        
        /**
         * If we're continuing a process, set our class vars appropriately.
         * Bail early so that nothing else fires.
         */
        if ( isset( $this->form[ 'field_ids' ] ) ) {
            $this->field_ids = $this->form[ 'field_ids' ];
            $this->insert = $this->form[ 'insert' ];
            $this->submission_updates = $this->form[ 'submission_updates' ];
            return false;
        }
        /**
         * We need to cross reference the Fields table to see if these ids exist for this form.
         * If they exist in the table, we don't need to insert them.
         */
        $sql = "SELECT id FROM `{$this->table}` WHERE parent_id = {$this->form[ 'ID' ]}";
        $db_fields = $this->db->get_results( $sql, 'ARRAY_A' );
        $db_field_ids = array();
        /**
         * Loop over every field that exists in the table:
         *     If it doesn't exist in the cache, add it to our delete class var so that it is deleted later.
         *     If it does exist in both, add it to our $db_field_ids array for later comparison.
         */
        foreach ( $db_fields as $field ) {
            // If we have no reference to it in the cache...
            if ( ! in_array( $field[ 'id' ], $this->field_ids ) ) {
                // Schedule it for deletion.
                array_push( $this->delete, $field[ 'id' ] );
            } else { // Push the id onto our comparison array.
                array_push( $db_field_ids, $field[ 'id' ] );
            }
        }

        /**
         * Loop over every field that exists in our form cache to see if we need to insert it.
         */
        foreach ( $this->field_ids as $field ) {
            // If we have no reference to it in the fields table...
            if ( ! in_array( $field, $db_field_ids ) ) {
                // Schedule it for insertion.
                array_push( $this->insert, $field );
            }
        }
        /**
         * Cross reference the Fields table to see if these ids exist on other Forms.
         * If an id exists on another form, then we need to change the current field's id and add that field to our submission_updates class var.
         */
        if ( ! empty( $this->field_ids ) ) {
            $sql = "SELECT id FROM `{$this->table}` WHERE id IN(" . implode( ', ', $this->field_ids ) . ") AND parent_id <> {$this->form[ 'ID' ]}";
            $duplicates = $this->db->get_results( $sql, 'ARRAY_A' );
        } else {
            $duplicates = array();
        }
        /**
         * If we got something back, there were duplicates.
         */
        if ( ! empty( $duplicates ) ) {
            /**
             * Loop over our duplicates and add it to our insert class var if it isn't already there.
             * Also, add this field to our submission_updates class var so that we can handle the id change later.
             */
            foreach ( $duplicates as $duplicate ) {
                if ( ! in_array( $duplicate[ 'id' ], $this->insert ) ) {
                   array_push( $this->insert, $duplicate[ 'id' ] ); 
                }
                
                $this->submission_updates[ $duplicate[ 'id' ] ] = true;
            }
        }
    }

    /**
     * Our setup_vars method adds fields to the insert class var.
     * Any fields that are in this array need to be inserted into our database.
     *
     * This is the first insert/update method to run, so it doesn't check lock_process.
     * If the insert class var is empty, then we bail early.
     * 
     * @since  3.4.0
     * @return void
     */
    private function maybe_insert_fields()
    {
        // If we don't have any items to insert, bail early.
        if ( empty( $this->insert ) ) {
            return false;
        }

        // Store the meta items outside the loop for faster insertion.
        $meta_items = array();
        $flush_ids = array();
        // While we still have items to insert...
        while ( 0 < count( $this->insert ) ) {
            // If we have hit our limit...
            if ( 1 > $this->limit ) {
                // Lock processing.
                $this->lock_process = true;
                // Exit the loop.
                break;
            }
            // Get our item to be inserted.
            $inserting = array_pop( $this->insert );
            $settings = $this->fields_by_id[ $inserting ];
            
            /*
             * We want to preserve the field ids from the cache if we can.
             * To do this, we check our $this->submission_updates array for this current field.
             * If it doesn't exist in the array, we can trust the cached field id.
             * If it exists in that array, then this is a duplicate.
             */
            if ( ! isset( $this->submission_updates[ $inserting ] ) ) {
                $maybe_field_id = intval( $inserting ); // Use the cached field id.
            } else {
                $maybe_field_id = 'NULL'; // Setting 'NULL' uses SQL auto-increment.
            }
            
            // Insert into the fields table.
            $sql = "INSERT INTO `{$this->table}` ( `id`, label, `key`, `type`, parent_id, field_label, field_key, `order`, required, default_value, label_pos, personally_identifiable ) VALUES ( " .
                $maybe_field_id . ", '" .
                $this->prepare( $settings[ 'label' ] ) . "', '".
                $this->prepare( $settings[ 'key' ] ) . "', '" .
                $this->prepare( $settings[ 'type' ] ) . "', " .
                intval( $this->form[ 'ID' ] ) . ", '" .
                $this->prepare( $settings[ 'label' ] ) . "', '" .
                $this->prepare( $settings[ 'key' ] ) . "', " .
                intval( $settings[ 'order' ] ) . ", " .
                intval( $settings[ 'required' ] ) . ", '" .
                $this->prepare( $settings[ 'default_value' ] ) . "', '" .
                $this->prepare( $settings[ 'label_pos' ] ) . "', " .
                intval( $settings[ 'personally_identifiable' ] ) . " )";

            $this->query( $sql );

            // Set a default new_id for debugging.
            $new_id = 0;
            // If we're not in debug mode...
            if ( ! $this->debug ) {
                // Get the ID of the new field.
                $new_id = $this->db->insert_id;
                $settings[ 'old_field_id' ] = $inserting;
            }
            // Save a reference to this insertion.
            $this->insert_ids[ $inserting ] = $new_id;

            // Update our submission_updates array with the new ID of this field so that we can use it later.
            if ( isset ( $this->submission_updates[ $inserting ] ) ) {
                $this->submission_updates[ $inserting ] = $new_id; 
            }

            // Push the new ID onto our list of IDs to flush.
            array_push( $flush_ids, $new_id );
            
            // For each meta of the field...
            foreach ( $settings as $meta => $value ) {
                // If it's not empty...
                if ( ( ! empty( $value ) || '0' == $value ) ) {
                    // Add the data to the list.
                    array_push( $meta_items, "( " . intval( $new_id ) . ", '" . $meta . "', '" . $this->prepare( $value ) . "', '" . $meta . "', '" . $this->prepare( $value ) . "' )" );
                }
            }
            // Remove the item from the list of fields.
            unset( $this->fields_by_id[ $inserting ] );
            $field_index = array_search( $inserting, $this->field_ids );
            unset( $this->field_ids[ $field_index ] );
            // Reduce the limit.
            $this->limit--;
        }

        if ( ! empty ( $flush_ids ) ) {
            // Flush our existing meta.
            $sql = "DELETE FROM `{$this->meta_table}` WHERE parent_id IN(" . implode( ', ', $flush_ids ) . ")";
            $this->query( $sql );            
        }

        // Insert our meta.
        $sql = "INSERT INTO `{$this->meta_table}` ( parent_id, `key`, value, meta_key, meta_value ) VALUES " . implode( ', ', $meta_items );
        $this->query( $sql );
    }

    /**
     * If we have any duplicate field ids, we need to update any existing submissions with the new field ID.
     *
     * The $this->submission_updates array will look like:
     *
     * $this->submission_updates[ original_id ] = new_id;
     *
     * This method:
     *     Checks to see if we have any fields in our $this->submission_updates array (have a changed ID)
     *     Makes sure that processing isn't locked
     *     Loops over fields in our $this->submission_updates array
     *     Fetches submission post meta for the specific form ID and _field_OLDID
     *     Uses a SQL UPDATE statement to replace _field_OLDID with _field_NEWID
     * 
     * @since  3.4.0
     * @return void
     */
    private function maybe_update_submissions()
    {
        // If we don't have any submissions to update OR the lock_process is true, bail early.
        if ( empty ( $this->submission_updates ) || $this->lock_process ) {
            return false;
        }
            
        /*
         * Keep track of old field ids we've used.
         *     Initially, we set our record array to our current submission updates array.
         *     When we finish updating an old field, we remove it from the record array.
         *     When we're done with all fields, we set the submission updates array to the record array.
         */
        $submission_updates_record = $this->submission_updates;
        // Meta key update limit; How many meta keys do we want to update at a time?
        $meta_key_limit = 200;
        // Loop through submission updates and query the postmeta table for any meta_key values of _field_{old_id}.
        foreach ( $this->submission_updates as $old_id => $new_id ) {
            // Make sure that we haven't reached our query limit.
            if ( 1 > $this->limit ) {
                // Lock processing.
                $this->lock_process = true;
                // Exit the loop.
                break;
            }

            // This sql is designed to grab our old _field_X post meta keys so that we can replace them with new _field_X meta keys.
            $sql = "SELECT
                old_field_id.meta_id
                FROM
                `{$this->db->prefix}posts` p
                INNER JOIN `{$this->db->prefix}postmeta` old_field_id ON old_field_id.post_id = p.ID
                AND old_field_id.meta_key = '_field_{$old_id}'
                INNER JOIN `{$this->db->prefix}postmeta` form_id ON form_id.post_id = p.ID
                AND form_id.meta_key = '_form_id'

                WHERE old_field_id.meta_key = '_field_{$old_id}'
                 AND form_id.meta_value = {$this->form[ 'ID' ]}
                 AND p.post_type = 'nf_sub'
                 LIMIT {$meta_key_limit}";
            // Fetch our sql results.
            $meta_ids = $this->db->get_results( $sql, 'ARRAY_N' );
            if ( ! empty( $meta_ids ) ) {
                // Implode our meta ids so that we can use the result in our update sql.
                $imploded_ids = implode( ',', call_user_func_array( 'array_merge', $meta_ids ) );
                // Update all our fetched meta ids with the new _field_ meta key.
                $sql = "UPDATE `{$this->db->prefix}postmeta`
                    SET    meta_key = '_field_{$new_id}'
                    WHERE  meta_id IN ( {$imploded_ids} )";

                $this->query( $sql );
            }

            /*
             * Let's make sure that we're done processing all post meta for this old field ID.
             * 
             * If the number of meta rows retrieved equals our limit:
             *     lock processing
             *     break out of this loop
             * Else
             *     we're done with this old field, remove it from our list
             *     subtract from our $this->limit var
             */
            if ( $meta_key_limit === count( $meta_ids ) ) {
                // Keep anything else from processing.
                $this->lock_process = true;
                // Exit this foreach loop.
                break;
            } else { // We're done with this old field.
                // Remove the field ID from our submission array.
                unset( $submission_updates_record[ $old_id ] );
                // Decrement our query limit.
                $this->limit--;
            }

        } // End foreach
        // Set our submission updates array to our record array so that we remove any completed old ids.
        $this->submission_updates = $submission_updates_record;
    }

    /**
     * If we still have field_ids in our class var, then we need to update the field table.
     *
     * If lock_process is true or we have no field_ids, we bail early.
     * 
     * @since  3.4.0
     * @return void
     */
    private function maybe_update_fields()
    {
        // If we have no fields to insert OR lock_process is true, bail early.
        if ( empty ( $this->field_ids ) || $this->lock_process ) {
            return false;
        }
            
        // Store the meta items outside the loop for faster insertion.
        $meta_items = array();
        $flush_ids = array();
        // While we still have items to update...
        while ( 0 < count( $this->field_ids ) ) {
            // If we have hit our limit...
            if ( 1 > $this->limit ) {
                // Lock processing.
                $this->lock_process = true;
                // Exit the loop.
                break;
            }
            // Get our item to be updated.
            $updating = array_pop( $this->field_ids );
            array_push( $flush_ids, $updating );
            $settings = $this->fields_by_id[ $updating ];
            // Update the fields table.
            $sql = "UPDATE `{$this->table}` SET label = '" 
                . $this->prepare( $settings[ 'label' ] ) 
                . "', `key` = '" . $this->prepare( $settings[ 'key' ] ) 
                . "', `type` = '" . $this->prepare( $settings[ 'type' ] ) 
                . "', field_label = '" . $this->prepare( $settings[ 'label' ] ) 
                . "', field_key = '" . $this->prepare( $settings[ 'key' ] ) 
                . "', `order` = " . intval( $settings[ 'order' ] );

                if( isset( $settings[ 'required' ] ) ) {
                    $sql .= ", required = " . intval( $settings[ 'required' ] );
                } else {
                    $sql .= ", required = 0";
                }

                if ( isset( $settings[ 'default_value' ] ) ) {
                    $sql .= ", default_value = '" . $this->prepare( $settings[ 'default_value' ] ) . "'";
                } else {
                    $sql .= ", default_value = ''";
                }

                if ( isset( $settings[ 'label_pos' ] ) ) {
                    $sql .= ", label_pos = '" . $this->prepare( $settings[ 'label_pos' ] ) . "'";
                } else {
                    $sql .= ", label_pos = ''";
                }

                if ( isset( $settings[ 'personally_identifiable' ] ) ) {
                    $sql .= ", personally_identifiable = " . intval( $settings[ 'personally_identifiable' ] );
                } else {
                    $sql .= ", personally_identifiable = 0";;
                }
                $sql .= " WHERE id = " . intval( $updating );
            $this->query( $sql );
            // For each meta of the field...
            foreach ( $settings as $meta => $value ) {
                // If it's not empty...
                if ( ( ! empty( $value ) || '0' == $value ) ) {
                    // Add the data to the list.
                    array_push( $meta_items, "( " . intval( $updating ) . ", '" . $meta . "', '" . $this->prepare( $value ) . "', '" . $meta . "', '" . $this->prepare( $value ) . "' )" );
                }
            }
            // Remove the item from the list of fields.
            unset( $this->fields_by_id[ $updating ] );
            // Reduce the limit.
            $this->limit--;
        }
        if ( ! empty ( $flush_ids ) ) {
            // Flush our existing meta.
            $sql = "DELETE FROM `{$this->meta_table}` WHERE parent_id IN(" . implode( ', ', $flush_ids ) . ")";
            $this->query( $sql );            
        }

        // Insert our updated meta.
        $sql = "INSERT INTO `{$this->meta_table}` ( parent_id, `key`, value, meta_key, meta_value ) VALUES " . implode( ', ', $meta_items );
        $this->query( $sql );
    }

    /**
     * If we've inserted any fields that have changed ids, we want to update those ids in our cache.
     * This method grabs the cache, updates any field ids, then updates the cache.
     * 
     * @since  3.4.0
     * @return void
     */
    private function update_form_cache()
    {
        // Get a copy of the cache.
        $cache = WPN_Helper::get_nf_cache($this->form[ 'ID' ] );
        // For each field in the cache...
        foreach( $cache[ 'fields' ] as &$field ) {
            // If we have a new ID for this field...
            if ( isset( $this->insert_ids[ $field[ 'id' ] ] ) ) {
                // Update it.
                $field[ 'id' ] = intval( $this->insert_ids[ $field[ 'id' ] ] );
            }
            // TODO: Might also need to append some new settings here (Label)?
        }
        // Save the cache, passing 3 as the current stage.
        WPN_Helper::update_nf_cache( $this->form[ 'ID' ], $cache, 3 );
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
        // If we have locked processing...
        if ( $this->lock_process ) {
            // If we're continuing a process...
            if ( isset( $this->form[ 'field_ids' ] ) ) {
                // Reset the field_ids array.
                $this->field_ids = array();
                // For each field left to process...
                foreach ( $this->fields_by_id as $id => $field ) {
                    // If we've not already processed this field...
                    if ( in_array( $id, $this->form[ 'field_ids' ] ) ) {
                        // Save a reference to its ID.
                        array_push( $this->field_ids, $id );
                    }
                }
            }
            // Store our current data location.
            $this->form[ 'insert' ] = $this->insert;
            $this->form[ 'field_ids' ] = $this->field_ids;
            $this->form[ 'submission_updates' ] = $this->submission_updates;
            array_push( $this->running[ 0 ][ 'forms' ], $this->form );
        } else { // Otherwise... (The step is complete.)
            // Increment our step count.
            $this->running[ 0 ][ 'current' ] = intval( $this->running[ 0 ][ 'current' ] ) + 1;
            // Disable maintenance mode on the front end of the site.
            $this->disable_maintenance_mode( $this->db->prefix, $this->form[ 'ID' ] );
        }

        // Prepare to output our number of steps and current step.
        $this->response[ 'stepsTotal' ] = $this->running[ 0 ][ 'steps' ];
        $this->response[ 'currentStep' ] = $this->running[ 0 ][ 'current' ];
        
        // If all steps have been completed...
        if ( empty( $this->running[ 0 ][ 'forms' ] ) ) {
            // Run our cleanup method.
            $this->cleanup();
        }
        
        // Record our current location in the process.
        update_option( 'ninja_forms_doing_required_updates', $this->running );
        // Prepare to output the number of updates remaining.
        $this->response[ 'updatesRemaining' ] = count( $this->running );
    }

}