<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_FormMeta extends NF_Abstracts_Migration
{

    /**
     * Constructor method for the NF_Database_Migrations_Actions class.
     * 
     * @since 3.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'nf3_form_meta',
            'nf_migration_create_table_form_meta'
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
     * Function to be run as part of our CacheCollateForms required update.
     *
     * @since 3.4.0
     */
    public function cache_collate_forms()
    {
        global $wpdb;

        // If the meta_key column exists...
        if ( $this->column_exists( 'meta_key' ) ) {
            // Update our existing columns.
            $query = "ALTER TABLE {$this->table_name()}
                MODIFY `meta_key` longtext {$this->charset_collate()},
                MODIFY `meta_value` longtext {$this->charset_collate()};";
        } // Otherwise... (The meta_key column does not exist.)
        else {
            // Create the new columns.
            $query = "ALTER TABLE {$this->table_name()}
                ADD `meta_key` longtext {$this->charset_collate()},
                ADD `meta_value` longtext {$this->charset_collate()};";
        }
        $wpdb->query( $query );
    }

}
