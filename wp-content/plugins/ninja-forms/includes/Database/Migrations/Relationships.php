<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_Relationships extends NF_Abstracts_Migration
{
    /**
     * Constructor method for the NF_Database_Migrations_Relationships class.
     * 
     * @since 3.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'nf3_relationships',
            'nf_migration_create_table_relationships'
        );
    }

    /**
     * Function to run our initial migration.
     * 
     * @since 3.0.0
     */
    public function run()
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table_name()} (
            `id` int NOT NULL AUTO_INCREMENT,
            `child_id` int NOT NULL,
            `child_type` longtext NOT NULL,
            `parent_id` int NOT NULL,
            `parent_type` longtext NOT NULL,
            `created_at` TIMESTAMP,
            `updated_at` DATETIME,
            UNIQUE KEY (`id`)
        ) {$this->charset_collate( true )};";

        dbDelta( $query );
    }

    /**
     * Function to ensure proper collation of the relationships table.
     *
     * @since 3.4.0
     */
    public function cache_collate_objects()
    {
        global $wpdb;
        // Modify our table.
        $query = "ALTER TABLE {$this->table_name()}
            MODIFY `child_type` longtext {$this->charset_collate()} NOT NULL,
            MODIFY `parent_type` longtext {$this->charset_collate()} NOT NULL;";
        $wpdb->query( $query );
    }

}
