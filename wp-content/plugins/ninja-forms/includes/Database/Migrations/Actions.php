<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_Actions extends NF_Abstracts_Migration
{

    /**
     * Constructor method for the NF_Database_Migrations_Actions class.
     * 
     * @since 3.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'nf3_actions',
            'nf_migration_create_table_actions'
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
            `title` longtext,
            `key` longtext,
            `type` longtext,
            `active` boolean DEFAULT TRUE,
            `parent_id` int NOT NULL,
            `created_at` TIMESTAMP,
            `updated_at` DATETIME,
            `label` longtext,
            UNIQUE KEY (`id`)
        ) {$this->charset_collate( true )};";

        dbDelta( $query );
    }


    /**
     * Function to be run as part of our CacheCollateActions required update.
     *
     * @since 3.3.12
     * 
     * @updated 3.4.0
     */
    public function cache_collate_actions()
    {
        // If the label column has not already been defined...
        if ( ! $this->column_exists( 'label' ) ) {
            global $wpdb;
            // Modify our table.
            $query = "ALTER TABLE {$this->table_name()}
                ADD `label` longtext {$this->charset_collate()},
                MODIFY `type` longtext {$this->charset_collate()};";

            $wpdb->query( $query );
        }
    }

}
