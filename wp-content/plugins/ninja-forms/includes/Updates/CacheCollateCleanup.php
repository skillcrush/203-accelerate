<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Updates_CacheCollateCleanup
 * 
 * This class manages the step process of running through the CacheCollateCleanup required update.
 * It will define an object to pull data from (if necessary) to pick back up if exited early.
 * It will then step over each table in our structure and ensure that orphan records are removed from storage.
 * It will then step over all submissions, removing any orphans.
 * It will then step over all submissions (by form), updating or removing any orphan field records.
 * After completing the above for every form on the site, it will remove the data object that manages its location.
 */
class NF_Updates_CacheCollateCleanup extends NF_Abstracts_RequiredUpdate
{
    
    private $data = array();
    
    private $running = array();
    
    /**
     * The denominator object for calculating our steps.
     * @var Integer
     */
    private $divisor = 500;
    
    private $stage;
    
    private $stage_complete = false;

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
            'slug' => 'CacheCollateCleanup',
            'class_name' => 'NF_Updates_CacheCollateCleanup',
            'debug' => false,
        );
        $this->data = $data;
        $this->running = $running;

        // Call the parent constructor.
        parent::__construct( $args );

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
        $this->stage = $this->running[ 0 ][ 'stages' ][ 0 ];

        // Determine what process to run.
        switch ( $this->stage[ 'table' ] ) {
            case 'submissions':
                // If we've cleaned out all orphan submissions...
                if ( $this->stage[ 'purged' ] ) {
                    // Work on clearing out orphan field records.
                    $this->adopt_fields();
                } // Otherwise... (We still have orphan submissions.)
                else {
                    // Remove orphan submissions.
                    $this->adopt_subs();
                }
                break;
            default:
                // If we need to step over this process...
                if ( $this->divisor < $this->stage[ 'parent_total' ] ) {
                    // Call stepped deletion.
                    $this->do_step_delete();
                } // Otherwise... (We don't need to step over this.)
                else {
                    // Call simple deletion.
                    $this->do_easy_delete();
                }
                // Increment our step count.
                $this->running[ 0 ][ 'current' ] += 1;
                break;
        }
        // If we have completed the current stage...
        if ( $this->stage_complete ) {
            // Remove it from our list.
            array_shift( $this->running[ 0 ][ 'stages' ] );
        } // Otherwise... (We have not completed the stage)
        else {
            // Record our changes.
            $this->running[ 0 ][ 'stages' ][ 0 ] = $this->stage;
        }
        // Prepare to output our number of steps and current step.
        $this->response[ 'stepsTotal' ] = $this->running[ 0 ][ 'steps' ];
        $this->response[ 'currentStep' ] = $this->running[ 0 ][ 'current' ];
        
        // If we have no stages left...
        if ( empty( $this->running[ 0 ][ 'stages' ] ) ) {
            // Run our cleanup method.
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

        // Get the number of records in the forms table.
        $form_count = $this->get_total( 'nf3_forms' );
        // Get the number of records in the fields table.
        $action_count = $this->get_total( 'nf3_actions' );
        // Get the number of records in the actions table.
        $field_count = $this->get_total( 'nf3_fields' );
        // Get the number of records in the objects table.
        $object_count = $this->get_total( 'nf3_objects' );
        
        // Get all form_ids from the db for the submissions portion.
        $sql = "SELECT `id` FROM `{$this->db->prefix}nf3_forms`";
        $forms = $this->db->get_results( $sql, 'ARRAY_A' );
        
        $stages = array(
            array(
                'table' => $this->db->prefix . 'nf3_form_meta',
                'parent' => $this->db->prefix . 'nf3_forms',
                'parent_total' => $form_count,
            ),
            array(
                'table' => $this->db->prefix . 'nf3_actions',
                'parent' => $this->db->prefix . 'nf3_forms',
                'parent_total' => $form_count,
            ),
            array(
                'table' => $this->db->prefix . 'nf3_action_meta',
                'parent' => $this->db->prefix . 'nf3_actions',
                'parent_total' => $action_count,
            ),
            array(
                'table' => $this->db->prefix . 'nf3_fields',
                'parent' => $this->db->prefix . 'nf3_forms',
                'parent_total' => $form_count,
            ),
            array(
                'table' => $this->db->prefix . 'nf3_field_meta',
                'parent' => $this->db->prefix . 'nf3_fields',
                'parent_total' => $field_count,
            ),
            array(
                'table' => $this->db->prefix . 'nf3_object_meta',
                'parent' => $this->db->prefix . 'nf3_objects',
                'parent_total' => $object_count,
            ),
            array(
                'table' => 'submissions',
                'parent_total' => $form_count,
                'purged' => false,
                'forms' => $forms,
            ),
        );

        $add = 0;
        // Set the steps for form meta (enforcing a minimum step count).
        $add = ceil( $form_count / $this->divisor );
        $steps = ( 0 == $add ) ? 1 : $add;
        // Add actions and fields.
        $steps *= 3;
        // Add action meta (enforcing a minimum step count).
        $add = ceil( $action_count / $this->divisor );
        $add = ( 0 == $add ) ? 1 : $add;
        $steps += $add;
        // Add field meta (enforcing a minimum step count).
        $add = ceil( $field_count / $this->divisor );
        $add = ( 0 == $add ) ? 1 : $add;
        $steps += $add;
        // Add object meta (enforcing a minimum step count).
        $add = ceil( $object_count / $this->divisor );
        $add = ( 0 == $add ) ? 1 : $add;
        $steps += $add;
        // Add one plus the form count for submissions (enforcing a minimum step count).
        $add = $form_count + 1;
        $add = ( 1 == $add ) ? 2 : $add;
        $steps += $add;
        
        $this->running[ 0 ][ 'stages' ] = $stages;

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
     * Function to get the number of objects in a table.
     * 
     * @param $table (String) The name of the target table.
     * 
     * @return (Int) The count of rows.
     * 
     * @since 3.4.0
     */
    public function get_total( $table )
    {
        $sql = "SELECT COUNT( `id` ) AS Total FROM `{$this->db->prefix}{$table}`;";
        $result = $this->db->get_results( $sql, 'ARRAY_A' );
        return intval( $result[ 0 ][ 'Total' ] );
    }

    /**
     * Function to perform a simple, single-step delete of orphan data.
     * 
     * @since 3.4.0
     * @updated 3.4.11
     */
    private function do_easy_delete()
    {
        // Remove the orphan records.
        $sql = "DELETE FROM `{$this->stage[ 'table' ]}`
                WHERE `parent_id` NOT IN(
                    SELECT `id` FROM `{$this->stage[ 'parent' ]}`
                )";
        // Protect favorite fields from being removed.
        if ( false !== strpos( $this->stage[ 'table' ], 'nf3_fields' ) ) {
            $sql .= " AND `parent_id` <> 0";
        }
        $this->query( $sql );
        // Confirm that this stage is complete.
        $this->stage_complete = true;
    }

    /**
     * Function to perform a multi-step delete of orphan data.
     * 
     * @since 3.4.0
     * @updated 3.4.11
     */
    private function do_step_delete()
    {
        // Get records from our table.
        $sub_sql = "SELECT DISTINCT( `parent_id` ) AS id FROM `{$this->stage[ 'table' ]}` ";
        // If we have a previous last record...
        if ( isset( $this->stage[ 'last' ] ) ) {
            // Make sure we exclude anything before it from the result.
            $sub_sql .= "WHERE `parent_id` > " . $this->stage[ 'last' ] . " ";
        } // Protect favorite fields from being removed.
        elseif ( false !== strpos( $this->stage[ 'table' ], 'nf3_fields' ) ) {
            $sub_sql .= "WHERE `parent_id` <> 0 ";
        }
        $sub_sql .= "ORDER BY `parent_id` ASC
                     LIMIT {$this->divisor};";
        $result = $this->db->get_results( $sub_sql, 'ARRAY_A' );
        // Get the last affected row.
        $last = end( $result );
        // Squash our results to get a non-associative array.
        $result = $this->array_squash( $result );
        $this->stage[ 'last' ] = intval( $last[ 'id' ] );
        // Get records from the parent table.
        $sql = "SELECT `id` FROM `{$this->stage[ 'parent' ]}`
                WHERE `id` IN(" . implode( ', ', $result ) .
                ");";
        $parent_result = $this->db->get_results( $sql, 'ARRAY_A' );
        // If we didn't get the same number of results...
        if ( count( $result ) !== count( $parent_result ) ) {
            // Merge our results.
            $result = array_merge( $result, $parent_result );
            // Convert the array to something we can sort by duplicates.
            $temp = array_count_values( array_column( $result, 'id' ) );
            // Get rid of all values that had more than 1 result.
            $temp = array_filter( $temp, array( $this, 'uniquify' ) );
            // Schedule all of the single result values for deletion.
            $delete = implode( ', ', array_keys( $temp ) );
            $sql = "DELETE FROM `{$this->stage[ 'table' ]}`
                    WHERE `parent_id` IN( {$delete} );";
            $this->query( $sql );
        }
        // If we've not already determined the limit of this table...
        if ( ! isset( $this->stage[ 'max' ] ) ) {
            // Fetch the maximum value.
            $sql = "SELECT MAX( `parent_id` ) as target FROM `{$this->stage[ 'table' ]}`";
            $result = $this->db->get_results( $sql, 'ARRAY_A' );
            // Save a reference to it.
            $this->stage[ 'max' ] = intval( $result[ 0 ][ 'target' ] );
        }
        // If our last record is equal to our maximum record...
        if ( $this->stage[ 'last' ] == $this->stage[ 'max' ] ) {
            // Record that we're done with this stage.
            $this->stage_complete = true;
        }
    }

    /**
     * Function called by array_filter to remove ALL duplicate results.
     * 
     * @param $v (Int) The number of results.
     * 
     * @return (Boolean)
     * 
     * @since 3.4.0
     */
    private function uniquify( $v )
    {
        // If we have 1 result, keep it.
        return $v == 1;
    }

    /**
     * Function to remove orphan submissions from the posts and postmeta tables.
     * 
     * @since 3.4.0
     */
    private function adopt_subs()
    {
        // Fetch a limited number of orphan subs to delete.
        $sub_sql = "SELECT m.post_id AS id from `{$this->db->prefix}postmeta` AS m
                    LEFT OUTER JOIN `{$this->db->prefix}posts` AS p
                    ON m.post_id = p.id
                    WHERE m.meta_key = '_form_id'
                    AND p.post_type = 'nf_sub'
                    AND m.meta_value NOT IN(
                      SELECT `id` from `{$this->db->prefix}nf3_forms`
                    )
                    ORDER BY m.post_id ASC
                    LIMIT {$this->divisor};";
        $result = $this->db->get_results( $sub_sql, 'ARRAY_A' );
        // Count them.
        $count = count( $result );
        // Squash our results to get a non-associative array.
        $result = $this->array_squash( $result );
        // If we have records to be deleted...
        if ( 0 < $count ) {
            // Remove their postmeta.
            $sql = "DELETE FROM `{$this->db->prefix}postmeta`
                    WHERE post_id IN(" . implode( ', ', $result ) . ");";
            $this->query( $sql );
            // Remove the posts.
            $sql = "DELETE FROM `{$this->db->prefix}posts`
                    WHERE id IN(" . implode( ', ', $result ) . ");";
            $this->query( $sql );
        }
        // If the number of affected rows was less than our divisor...
        if ( $count < $this->divisor ) {
            // Mark that we've completed the process.
            $this->stage[ 'purged' ] = true;
            // Increment our step count.
            $this->running[ 0 ][ 'current' ] += 1;
        }
    }

    /**
     * Function to remove orphan field data that's still attached
     * to valid submissions from the postmeta table.
     * 
     * @since 3.4.0
     */
    private function adopt_fields()
    {
        // Get the form to work with.
        $form = array_pop( $this->stage[ 'forms' ] );
        // Fetch meta rows that match our criteria.
        $sql = "SELECT r.meta_id, r.meta_key AS field_id
                FROM `{$this->db->prefix}posts` AS p
                LEFT JOIN `{$this->db->prefix}postmeta` AS r
                ON r.post_id = p.id
                LEFT JOIN `{$this->db->prefix}postmeta` AS f
                ON f.post_id = p.id
                WHERE p.post_type = 'nf_sub'
                AND f.meta_key = '_form_id'
                AND f.meta_value = " . intval( $form[ 'id' ] ) . "
                AND r.meta_key LIKE '_field_%' ";
        // If last is set...
        if ( isset( $form[ 'last' ] ) ) {
            // Make sure we're getting new results instead of old ones.
            $sql .= "AND r.meta_id > {$form[ 'last' ]} ";
        }
        $sql .= "ORDER BY r.meta_id ASC
                 LIMIT {$this->divisor};";
        $results = $this->db->get_results( $sql, 'ARRAY_A' );
        // Count them.
        $count = count( $results );
        // Get the last result.
        if ( 0 < $count ) {
            $last = end( $results );
            $last = $last[ 'meta_id' ];
            reset( $results );
        }
        // Get all fields associated with this form.
        $sql = "SELECT id FROM `{$this->db->prefix}nf3_fields` WHERE parent_id = " . intval( $form[ 'id' ] ) . ";";
        $fields = $this->db->get_results( $sql, 'ARRAY_A' );
        // Squash our results to get a non-associative array.
        $fields = $this->array_squash( $fields );
        $bad_records = array();
        // For each result...
        foreach( $results as $result ) {
            // Pull the text out of our meta.
            $id = str_replace( '_field_', '', $result[ 'field_id' ] );
            $id = intval( $id );
            // If this field isn't on the form...
            if ( ! in_array( $id, $fields ) ) {
                // Add it to our list to check later.
                $bad_records[ $id ][] = $result[ 'meta_id' ];
            }
        }
        
        // Get a list of any old_field_ids from our field table update.
        $sql = "SELECT f.id AS new_id, m.meta_value AS id
                FROM `{$this->db->prefix}nf3_fields` AS f
                LEFT JOIN `{$this->db->prefix}nf3_field_meta` AS m
                ON f.id = m.parent_id
                WHERE f.parent_id = " . intval( $form[ 'id' ] ) . "
                AND m.meta_key = 'old_field_id';";
        $old_ids = $this->db->get_results( $sql, 'ARRAY_A' );
        // Squash our results to get an associative array.
        $old_ids = $this->array_squash( $old_ids );
        
        // For each id in the bad records list...
        foreach ( $bad_records as $field => $meta ) {
            // If we have a new ID for that record...
            if ( isset( $old_ids[ $field ] ) ) {
                // Update our submissions.
                $sql = "UPDATE `{$this->db->prefix}postmeta`
                        SET `meta_key` = '_field_" . $old_ids[ $field ] . "'
                        WHERE `meta_id` IN(" . implode( ', ', $meta ) . ");";
                $this->query( $sql );
            } // Otherwise... (We don't have a new ID for it.)
            else {
                // Delete the orphan record.
                $sql = "DELETE FROM `{$this->db->prefix}postmeta`
                        WHERE `meta_id` IN(" . implode( ', ', $meta ) . ");";
                $this->query( $sql );
            }
        }
        // If the number of affected rows was less than our divisor...
        if ( $count < $this->divisor ) {
            // Increment our step count.
            $this->running[ 0 ][ 'current' ] += 1;
        } // Otherwise... (We need to continue.)
        else {
            // Record where we stopped.
            $form[ 'last' ] = intval( $last );
            // Put our form back on the stack.
            array_push( $this->stage[ 'forms' ], $form );
        }
        // If there are no forms left to process...
        if ( empty( $this->stage[ 'forms' ] ) ) {
            // Mark that this stage is done.
            $this->stage_complete = true;
        }
    }

    /**
     * Function to compress our db results into a more useful format.
     * 
     * @param $data (Array) The result to be compressed.
     * 
     * @return (Array) Associative if our data was complex.
     *                 Non-associative if our data was a single item.
     * 
     * @since 3.4.0
     */
    private function array_squash( $data )
    {
        $response = array();
        // For each item in the array...
        foreach ( $data as $row ) {
            // If the item has more than 1 attribute...
            if ( 1 < count( $row ) ) {
                // Assign the data to an associated result.
                $response[ $row[ 'id' ] ] = $row;
                // Unset the id setting, as that will be the key.
                unset( $response[ $row[ 'id' ] ][ 'id' ] );
            } // Otherwise... (We only have 1 attribute.)
            else {
                // Add the id to the stack in a non-associated result.
                $response[] = intval( $row[ 'id' ] );
            }
        }
        return $response;
    }

}