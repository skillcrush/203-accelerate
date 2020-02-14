<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_Objects extends NF_Abstracts_Migration
{
    /**
     * Constructor method for the NF_Database_Migrations_Objects class.
     * 
     * @since 3.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'nf3_objects',
            'nf_migration_create_table_objects'
        );
    }

    /**
     * Function to run our initial migration.
     * 
     * @since 3.0.0
     * 
     * @updated 3.4.0
     */
    public function run()
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table_name()} (
            `id` int NOT NULL AUTO_INCREMENT,
            `type` longtext,
            `title` longtext,
            `created_at` TIMESTAMP,
            `updated_at` DATETIME,
            `object_title` longtext,
            UNIQUE KEY (`id`)
        ) {$this->charset_collate( true )};";

        dbDelta( $query );
    }

    /**
     * Function to ensure proper collation of the objects table.
     *
     * @since 3.4.0
     */
    public function cache_collate_objects()
    {
        // If the object_title column has not already been defined...
        if ( ! $this->column_exists( 'object_title' ) ) {
            global $wpdb;
            // Modify our table.
            $query = "ALTER TABLE {$this->table_name()}
                ADD `object_title` longtext {$this->charset_collate()},
                MODIFY `type` longtext {$this->charset_collate()};";
            $wpdb->query( $query );
        }
    }

}
