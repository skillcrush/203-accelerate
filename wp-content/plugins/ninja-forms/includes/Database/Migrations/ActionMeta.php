<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_ActionMeta extends NF_Abstracts_Migration
{

    /**
     * Constructor method for the NF_Database_Migrations_ActionMeta class.
     * 
     * @since 3.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'nf3_action_meta',
            'nf_migration_create_table_action_meta'
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
            `parent_id` int NOT NULL,
            `key` longtext NOT NULL,
            `value` longtext,
            `meta_key` longtext,
            `meta_value` longtext,
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
        // If the meta_key column has not already been defined...
        if ( ! $this->column_exists( 'meta_key' ) ) {
            global $wpdb;
            // Modify our table.
            $query = "ALTER TABLE {$this->table_name()}
                ADD `meta_key` longtext {$this->charset_collate()},
                ADD `meta_value` longtext {$this->charset_collate()};";

            $wpdb->query( $query );
        }
    }

}
