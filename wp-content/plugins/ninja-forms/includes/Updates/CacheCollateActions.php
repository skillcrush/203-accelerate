<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Updates_CacheCollateActions
 * 
 * This class manages the step process of running through the CacheCollateActions required update.
 * It will define an object to pull data from (if necessary) to pick back up if exited early.
 * It will run an upgrade function to alter the nf3_actions and nf3_action_meta tables.
 * Then, it will step over each form on the site, following this process:
 * - Actions that exist in the data tables but not in the cache will be deleted.
 * - Actions that exist in the cache but not in the data tables will be inserted.
 * - Actions that exist in the data tables but have an incorrect form ID will be inserted as a new ID and referenced from the cache.
 * - Actions that exist in both will be updated from the cache to ensure the data is correct.
 * After completing the above for every form on the site, it will remove the data object that manages its location.
 */
class NF_Updates_CacheCollateActions extends NF_Abstracts_RequiredUpdate
{
    
    private $data = array();
    
    private $running = array();

    /**
     * Stores information about the current form being processed.
     * @var array
     */
    private $form;

    /**
     * Stores the actions for the current form being processed.
     * @var  array
     */
    private $actions;

    /**
     * Associative array of actions keyed by action id.
     * @var array
     */
    private $actions_by_id = array();

    /**
     * Non-associative array of action ids.
     * @var array
     */
    private $action_ids = array();

    /**
     * Hard limit for the number of querys we run during a single step.
     * @var integer
     */
    private $limit = 10;

    /**
     * Array of action ids that need an update.
     * @var array
     */
    private $update = array();

    /**
     * List of setting keys we don't want to save in the database.
     * @var array
     */
    private $blacklist = array(
        'objectType',
        'objectDomain',
        'editActive',
        'title',
        'key',
    );
    
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
            'slug' => 'CacheCollateActions',
            'class_name' => 'NF_Updates_CacheCollateActions',
            'debug' => false,
        );
        $this->data = $data;
        $this->running = $running;

        // Call the parent constructor.
        parent::__construct( $args );
        
        // Set our table names.
        $this->table = $this->db->prefix . 'nf3_actions';
        $this->meta_table = $this->db->prefix . 'nf3_action_meta';

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
         * Update action values and meta if necessary.
         */
        $this->maybe_update_actions();

        /**
         * Saves our current location, along with any processing data we may need for the next step.
         * If we're done with our step, runs cleanup instead.
         */
        $this->end_of_step();        

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
            $this->migrate( 'cache_collate_actions' );
            // Set out new db version.
            update_option( 'ninja_forms_db_version', '1.2' );
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
     * Setup our global variables used in other methods.
     * 
     * @since  3.4.0
     * @return void
     */
    private function setup_vars()
    {
        // See which form we're currently working with.
        $this->form = array_pop( $this->running[ 0 ][ 'forms' ] );

        // Get the actions for that form.
        $this->actions = Ninja_Forms()->form( $this->form[ 'ID' ] )->get_actions();

        // For each action...
        foreach ( $this->actions as $action ) {
            // Add the ID to the list.
            array_push( $this->action_ids, $action->get_id() );
            $this->actions_by_id[ $action->get_id() ] = $action->get_settings();
        }

        // If we're continuing an old process...
        if ( isset( $this->form[ 'update' ] ) ) {
            // Fetch our remaining udpates.
            $this->update = $this->form[ 'update' ];
        } // Otherwise... (We're beginning a new process.)
        else {
            // Copy all IDs to our update list.
            $this->update = $this->action_ids;
        }
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
     * Check to see if we've locked processing.
     * If we have, then we need to run this process again.
     *
     * If we haven't locked processing, prepare to end this process.
     * 
     * @since  3.4.0
     * @return void
     */
    private function end_of_step()
    {
        // If we have locked processing...
        if ( $this->lock_process ) {
            // Record that we have more to do.
            $this->form[ 'update' ] = $this->update;
            array_push( $this->running[ 0 ][ 'forms' ], $this->form );
        } // Otherwise... (Processing isn't locked.)
        else {
            // If we have actions...
            if ( ! empty( $this->action_ids ) ) {
                // Update our meta keys.
                $sql = "UPDATE `{$this->meta_table}` SET `meta_key` = `key` WHERE `parent_id` IN(" . implode( ',', $this->action_ids ) . ")";
                $this->query( $sql );
            }
            /**
             * Update our form cache with any action changes.
             */
            $this->update_form_cache();
            // Increment our step count.
            $this->running[ 0 ][ 'current' ] = intval( $this->running[ 0 ][ 'current' ] ) +1;
        }


        // Prepare to output our number of steps and current step.
        $this->response[ 'stepsTotal' ] = $this->running[ 0 ][ 'steps' ];
        $this->response[ 'currentStep' ] = $this->running[ 0 ][ 'current' ];

        // If we do not have locked processing...
        if ( ! $this->lock_process ) {
            // If all steps have been completed...
            if ( empty( $this->running[ 0 ] [ 'forms' ] ) ) {
                // Run our cleanup method.
                $this->cleanup();
            }
        }

        // Record our current location in the process.
        update_option( 'ninja_forms_doing_required_updates', $this->running );
        // Prepare to output the number of updates remaining.
        $this->response[ 'updatesRemaining' ] = count( $this->running );
    }

    /**
     * If we've made any changes to our form actions, update our form cache to match.
     * 
     * @since  3.4.0
     * @return void
     */
    private function update_form_cache()
    {
        // Get the cache for that form.
        $cache = WPN_Helper::get_nf_cache( $this->form[ 'ID' ] );
        // Bust the cache.
        $cache[ 'actions' ] = array();
        // For each action...
        foreach ( $this->actions_by_id as $id => $settings ) {
            // Append the settings for that action to the cache.
            $action = array();
            $action[ 'settings' ] = $settings;
            $action[ 'id' ] = $id;
            array_push( $cache[ 'actions' ], $action );
        }
        // Save the cache, passing 2 as the current stage.
        WPN_Helper::update_nf_cache( $this->form[ 'ID' ], $cache, 2 );
    }

    /**
     * Loop over all of our actions and update our database if necessary.
     * Check each setting against $this->blacklist to make sure we want to insert that value.
     * 
     * @since  3.4.0
     * @return void
     */
    private function maybe_update_actions()
    {
        // Declare placeholder values.
        $sub_sql = array();
        $meta_values = array();

        // While we have actions to update...
        while ( 0 < count( $this->update ) ) {
            // If we have hit our limit...
            if ( 1 > $this->limit ) {
                // Lock processing.
                $this->lock_process = true;
                // Exit the loop.
                break;
            }
            // Get our action to be updated.
            $action = array_pop( $this->update );
            // Get our settings.
            $settings = $this->actions_by_id[ $action ];
            // Update the new label column.
            array_push( $sub_sql, "WHEN `id` = " . intval( $action ) . " THEN '" . $this->prepare( $settings[ 'label' ] ) . "'" );
            // For each setting...
            foreach ( $settings as $key => $setting ) {
                // If the key is not blacklisted...
                if ( ! in_array( $key, $this->blacklist ) ) {
                    // Add the value to be updated.
                    $action = intval( $action );
                    array_push( $meta_values, "WHEN `key` = '{$key}' AND `parent_id` = {$action} THEN '" . $this->prepare( $setting ) . "'" );
                }
            }
            $this->limit--;
        }

        // If we've got updates to run...
        if ( ! empty( $sub_sql ) ) {
            // Update our actions table.
            $sql = "UPDATE `{$this->table}` SET `label` = CASE " . implode ( ' ', $sub_sql ) . " ELSE `label` END;";
            $this->query( $sql );
            // Update our meta values.
            $sql = "UPDATE `{$this->meta_table}` SET `meta_value` = CASE " . implode( ' ', $meta_values ) . " ELSE `meta_value` END;";
            $this->query( $sql );
        }
    }

}