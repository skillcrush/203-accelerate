<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_Forms extends NF_Abstracts_Migration
{

    /**
     * Constructor method for the NF_Database_Migrations_Actions class.
     * 
     * @since 3.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'nf3_forms',
            'nf_migration_create_table_forms'
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
            `created_at` TIMESTAMP,
            `updated_at` DATETIME,
            `views` int(11),
            `subs` int(11),
            `form_title` longtext,
            `default_label_pos` varchar(15),
            `show_title` bit,
            `clear_complete` bit,
            `hide_complete` bit,
            `logged_in` bit,
            `seq_num` int,
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

        // If the form_title column exists...
        if ( $this->column_exists( 'form_title' ) ) {
            // Update our existing columns.
            $query = "ALTER TABLE {$this->table_name()}
                MODIFY `form_title` longtext {$this->charset_collate()},
                MODIFY `default_label_pos` varchar(15) {$this->charset_collate()};";
        } // Otherwise... (The form_title column does not exist.)
        else {
            // Create the new columns.
            $query = "ALTER TABLE {$this->table_name()}
                ADD `form_title` longtext {$this->charset_collate()},
                ADD `default_label_pos` varchar(15) {$this->charset_collate()},
                ADD `show_title` bit,
                ADD `clear_complete` bit,
                ADD `hide_complete` bit,
                ADD `logged_in` bit,
                ADD `seq_num` int;";
        }
        $wpdb->query( $query );
    }

}
