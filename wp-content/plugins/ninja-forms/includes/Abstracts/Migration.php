<?php if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

abstract class NF_Abstracts_Migration
{
    public $table_name = '';

    public $charset_collate = '';

    public $flag = '';

    public function __construct( $table_name, $flag )
    {
        $this->table_name =  $table_name;
    }

    public function table_name()
    {
        global $wpdb;
        return $wpdb->prefix . $this->table_name;
    }

    public function charset_collate()
    {
        global $wpdb;
        // If our mysql version is 5.5.3 or higher...
        if ( version_compare( $wpdb->db_version(), '5.5.3', '>=' ) ) {
            // We can use mb4.
            return 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
        } // Otherwise...
        else {
            // We use standard utf8.
            return 'DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
        }   
    }

    /**
     * Function to get the column collate for ALTER TABLE statements.
     * 
     * @since 3.3.7
     * 
     * @return string
     */
    public function collate()
    {
        global $wpdb;
        // If our mysql version is 5.5.3 or higher...
        if ( version_compare( $wpdb->db_version(), '5.5.3', '>=' ) ) {
            // We can use mb4.
            return 'COLLATE utf8mb4_general_ci';
        } // Otherwise...
        else {
            // We use standard utf8.
            return 'COLLATE utf8_general_ci';
        }   
        
    }
    
    /**
     * Function to run our stage one db updates.
     */
    public function _stage_one()
    {
        if ( method_exists( $this, 'do_stage_one' ) ) {
            $this->do_stage_one();
        }
    }

    public function _run()
    {
        // Check the flag
        if( get_option( $this->flag, FALSE ) ) return;

        // Run the migration
        $this->run();

        // Set the Flag
        update_option( $this->flag, TRUE );
    }

    protected abstract function run();

    public function _drop()
    {
        global $wpdb;
        if( ! $this->table_name ) return;
        if( 0 == $wpdb->query( $wpdb->prepare( "SHOW TABLES LIKE '%s'", $this->table_name() ) ) ) return;
        $wpdb->query( "DROP TABLE " . $this->table_name() );
        return $this->drop();
    }

    protected function drop()
    {
        // This section intentionally left blank.
    }
}
