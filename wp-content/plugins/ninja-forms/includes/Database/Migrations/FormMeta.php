<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_FormMeta extends NF_Abstracts_Migration
{
    public function __construct()
    {
        parent::__construct(
            'nf3_form_meta',
            'nf_migration_create_table_form_meta'
        );
    }

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
        ) {$this->charset_collate()};";

        dbDelta( $query );
    }
    
    /**
     * Function to run our stage one upgrades.
     */
    public function do_stage_one()
    {
        $query = "ALTER TABLE {$this->table_name()}
            ADD `meta_key` longtext {$this->collate()},
            ADD `meta_value` longtext {$this->collate()}";
        global $wpdb;
        $wpdb->query( $query );
    }

}
