<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Abstracts_RequiredUpdate
 */
abstract class NF_Abstracts_RequiredUpdate
{

    protected $_slug = '';

    protected $_requires = array();

    protected $_class_name = '';

    protected $db;

    public $response = array();

    public $debug = false;
    
    public $lock_process = false;

    /**
     * Constructor
     * 
     * @since 3.4.0
     */
    public function __construct( $data = array() )
    {
        // Save a reference to wpdb.
        global $wpdb;
        $this->db = $wpdb;
        //Bail if we aren't in the admin.
        if ( ! is_admin() ) return false;
        // If we weren't provided with a slug or a class name...
        if ( ! isset( $data[ 'slug' ] ) || ! isset( $data[ 'class_name' ] ) ) {
            // Bail.
            return false;
        }
        $this->_slug = $data[ 'slug' ];
        $this->_class_name = $data[ 'class_name' ];
        // Record debug settings if provided.
        if ( isset( $data[ 'debug' ] ) ) $this->debug = $data[ 'debug' ];
    }


    /**
     * Function to loop over the batch.
     * 
     * @since 3.4.0
     */
    public function process()
    {
        /**
         * This function intentionlly left empty.
         */
    }


    /**
     * Function to run any setup steps necessary to begin processing.
     * 
     * @since 3.4.0
     */
    public function startup()
    {
        /**
         * This function intentionally left empty.
         */
    }


    /**
     * Function to cleanup any lingering temporary elements of required updates after completion.
     * 
     * @since 3.4.0
     */
    public function cleanup()
    {
        // Delete our required updates data.
        delete_option( 'ninja_forms_doing_required_updates' );
        // Flag that updates are done.
        update_option( 'ninja_forms_needs_updates', 0 );
        // Set our new db version.
        update_option( 'ninja_forms_db_version', Ninja_Forms::DB_VERSION );
        // Fetch our list of completed updates.
        $updates = get_option( 'ninja_forms_required_updates', array() );
        // If we got something back...
        if ( ! empty( $updates ) ) {
            // Send out a call to telemetry.
            Ninja_Forms()->dispatcher()->send( 'required_updates_complete', $updates );
        }
        // Output that we're done.
        $this->response[ 'updatesRemaining' ] = 0;
        $this->respond();
    }


    /**
     * Function to dump our JSON response and kill processing.
     * 
     * @since 3.4.0
     */
    public function respond()
    {
        // Dump the response.
        echo( json_encode( $this->response ) );
        // Terminate processing.
        die();
    }


    /**
     * Function to run our table migrations.
     * 
     * @param $callback (String) The callback function in the migration file.
     * 
     * @since 3.4.0
     */
    protected function migrate( $callback )
    {
        $migrations = new NF_Database_Migrations();
        $migrations->do_upgrade( $callback );
    }

    /**
     * Function to prepare our query values for insert.
     * 
     * @param $value (Mixed) The value to be escaped for SQL.
     * @return (String) The escaped (and possibly serialized) value of the string.
     * 
     * @since 3.4.0
     */
    public function prepare( $value )
    {
        // If our value is a number...
        if ( is_float( $value ) ) {
            // Exit early and return the value.
            return $value;
        }
        // Serialize the value if necessary.
        $escaped = maybe_serialize( $value );
        // Escape it.
        $escaped = $this->db->_real_escape( $escaped );

        return $escaped;
    }

    /**
     * Function used to call queries that are gated by debug.
     * 
     * @param $sql (String) The query to be run.
     * @return (Object) The response to the wpdb query call.
     * 
     * @since 3.4.0
     */
    protected function query( $sql )
    {
        // If we're not debugging...
        if ( ! $this->debug ) {
            // Run the query.
            return $this->db->query( $sql );
        } // Otherwise...
        // Append the query to the response object.
        $this->response[ 'queries' ][] = $sql;
        // Return false.
        return false;
    }

    /**
     * Function to record the completion of our update in the DB.
     * 
     * @since 3.4.0
     */
    protected function confirm_complete()
    {
        // If we're not debugging...
        if ( ! $this->debug ) {
            // Fetch our required updates array.
            $updates = get_option( 'ninja_forms_required_updates', array() );
            // Get a timestamp.
            date_default_timezone_set( 'UTC' );
            $now = date( "Y-m-d H:i:s" );
            // Append the current update to the array.
            $updates[ $this->_slug ] = $now;
            // Save it.
            update_option( 'ninja_forms_required_updates', $updates );
        }
    }

    /**
     * Enable Maintenance mode
     * Enables maintenance mode so the form will not render on the front end while updates are running.
     *
     * @since 3.4.0
     *
     * @param $prefix - the db prefix.
     * @param $form_id - The id of the form.
     */
    public function enable_maintenance_mode( $prefix, $form_id )
    {
        // Change maintenance column value to 1, run the query and then return.
        $sql = $this->db->prepare( 'UPDATE `' . $prefix . 'nf3_upgrades` SET `maintenance` = 1 WHERE `id` = %d', $form_id );
        $this->db->query( $sql );
        return;
    }

    /**
     * Disable Maintenance Mode
     * Disables maintenance mode, so the form will be displayed on the front end..
     *
     * @since 3.4.0
     *
     * @param $prefix - the db prefix.
     * @param $form_id - The id of the form.
     */
    public function disable_maintenance_mode( $prefix, $form_id )
    {
        // Change maintenance column value to 0, run the query and then return.
        $sql = $this->db->prepare( 'UPDATE `' . $prefix . 'nf3_upgrades` SET `maintenance` = 0 WHERE `id` = %d', $form_id );
        $this->db->query( $sql );
        return;
    }
}