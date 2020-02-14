<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_Upgrades extends NF_Abstracts_Migration
{

    /**
     * Constructor method for the NF_Database_Migrations_Fields class.
     * 
     * @since 3.3.11
     */
    public function __construct()
    {
        parent::__construct(
            'nf3_upgrades',
            'nf_migration_create_table_upgrades'
        );
    }


    /**
     * Function to run our initial migration.
     * 
     * @since 3.3.11
     * 
     * @updated 3.4.0
     */
    public function run()
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table_name()} (
            `id` INT(11) NOT NULL,
            `cache` LONGTEXT,
            `stage` INT(11) NOT NULL DEFAULT 0,
            `maintenance` bit DEFAULT 0,
            PRIMARY KEY ( id )
        ) {$this->charset_collate( true )};";

        dbDelta( $query );
    }

    /**
     * Function to define our maintenance column.
     *
     * @since 3.4.0
     */
    public function cache_collate_fields()
    {
        // If the maintenance column has not already been defined...
        if ( ! $this->column_exists( 'maintenance' ) ) {
            global $wpdb;
            // Modify our table.
            $query = "ALTER TABLE {$this->table_name()}
                ADD `maintenance` bit DEFAULT 0;";
            $wpdb->query( $query );
        }
    }
}
