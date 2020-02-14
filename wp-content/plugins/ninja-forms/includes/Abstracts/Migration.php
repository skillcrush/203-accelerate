<?php if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

abstract class NF_Abstracts_Migration
{
    public $table_name = '';

    public $charset_collate = '';

    public $flag = '';


    /**
     * Constructor method for the NF_Abstracts_Migration class.
     * 
     * @param $table_name (String) The database table name managed by the extension.
     * @param $flag (String) The wp option set to determine if this migration has already been run.
     * 
     * @since 3.0.0
     */
    public function __construct( $table_name, $flag )
    {
        $this->table_name =  $table_name;
    }


    /**
     * Function to retrieve the full table name of the extension.
     * 
     * @return (String) The full table name, including database prefix.
     * 
     * @since 3.0.28
     */
    public function table_name()
    {
        global $wpdb;
        return $wpdb->prefix . $this->table_name;
    }


    /**
     * Function to check for the existence of a column in the extension's table.
     * 
     * @param $column (String) The name of the column to search for.
     * 
     * @return (Boolean) Whether or not the column exists.
     * 
     * @since 3.4.0
     */
    public function column_exists( $column )
    {
        global $wpdb;
        $response = false;
        // Fetch any records of the target column.
        $sql = $wpdb->prepare( "SHOW COLUMNS FROM `{$this->table_name()}` WHERE `Field` = '%s';", $column );
        $result = $wpdb->query( $sql );
        // If we got anything back, say so.
        if ( ! empty( $result ) ) $response = true;
        return $response;
    }


    /**
     * Funciton to get the charset and collate for migrations.
     * 
     * @param $use_default (Boolean) Whether or not to include the DEFAULT keyword in the return pattern.
     * 
     * @return (String) A SQL formatted charset and collate for use by table definition.
     * 
     * @since 3.0.28
     * @updated 3.1.14
     */
    public function charset_collate( $use_default = false )
    {
        $response = '';
        global $wpdb;
        // If our mysql version is 5.5.3 or higher...
        if ( version_compare( $wpdb->db_version(), '5.5.3', '>=' ) ) {
            // We can use mb4.
            $response = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
        } // Otherwise...
        else {
            // We use standard utf8.
            $response = 'CHARACTER SET utf8 COLLATE utf8_general_ci';
        }
        // If we need to use default...
        if ( $use_default ) {
            // Append that to the response.
            $response = 'DEFAULT ' . $response;
        }
        return $response;
    }

    /**
     * Function to run our required update functions.
     * 
     * @param $callback (String) The function to be run by this call.
     * 
     * @since 3.4.0
     */
    public function _do_upgrade( $callback )
    {
        // If the method exists...
        if ( method_exists( $this, $callback ) ) {
            $blacklist = array(
                '__construct',
                '_do_upgrade',
                '_run',
                'run',
                '_drop',
                'drop'
            );
            // If this callback method isn't blacklisted...
            if ( ! in_array( $callback, $blacklist ) ) {
                // Run it.
                $this->{$callback}();
            }
        }
    }


    /**
     * Function to run our initial migration.
     * 
     * @since 3.0.0
     */
    public function _run()
    {
        // Check the flag
        if( get_option( $this->flag, FALSE ) ) return;

        // Run the migration
        $this->run();

        // Set the Flag
        update_option( $this->flag, TRUE );
    }


    /**
     * Abstract protection of inherited funciton run.
     * 
     * @since 3.0.0
     */
    protected abstract function run();


    /**
     * Function to drop the table managed by this migration.
     * 
     * @since 3.0.28
     */
    public function _drop()
    {
        global $wpdb;
        // If we don't have a table name, exit early.
        if( ! $this->table_name ) return;
        // If the table doesn't exist, exit early.
        if( 0 == $wpdb->query( $wpdb->prepare( "SHOW TABLES LIKE '%s'", $this->table_name() ) ) ) return;
        // Drop the table.
        $wpdb->query( "DROP TABLE " . $this->table_name() );
        return $this->drop();
    }


    /**
     * Protection of inherited function drop.
     * 
     * @since 3.0.28
     */
    protected function drop()
    {
        // This section intentionally left blank.
    }
}
