<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_Fields extends NF_Abstracts_Migration
{

    /**
     * Constructor method for the NF_Database_Migrations_Fields class.
     * 
     * @since 3.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'nf3_fields',
            'nf_migration_create_table_fields'
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
            `label` longtext,
            `key` longtext,
            `type` longtext,
            `parent_id` int NOT NULL,
            `created_at` TIMESTAMP,
            `updated_at` DATETIME,
            `field_label` longtext,
            `field_key` longtext,
            `order` int(11),
            `required` bit,
            `default_value` longtext,
            `label_pos` varchar(15),
            `personally_identifiable` bit,
            UNIQUE KEY (`id`)
        ) {$this->charset_collate( true )};";

        dbDelta( $query );
    }

    /**
     * Function to run our stage two upgrades.
     *
     * @since 3.3.12
     * 
     * @updated 3.4.0
     */
    public function cache_collate_fields()
    {
        // If the field_label column has not already been defined...
        if ( ! $this->column_exists( 'field_label' ) ) {
            global $wpdb;
            // Modify our table.
            $query = "ALTER TABLE {$this->table_name()}
                ADD `field_label` longtext {$this->charset_collate()},
                ADD `field_key` longtext {$this->charset_collate()},
                ADD `order` int(11),
                ADD `required` bit,
                ADD `default_value` longtext {$this->charset_collate()},
                ADD `label_pos` varchar(15) {$this->charset_collate()},
                ADD `personally_identifiable` bit,
                MODIFY `type` longtext {$this->charset_collate()};";
            $wpdb->query( $query );
        }
    }

}
