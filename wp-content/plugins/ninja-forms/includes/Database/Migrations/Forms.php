<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_Forms extends NF_Abstracts_Migration
{
    public function __construct()
    {
        parent::__construct(
            'nf3_forms',
            'nf_migration_create_table_forms'
        );
    }

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
        ) {$this->charset_collate()};";

        dbDelta( $query );
    }
    
    /**
     * Function to run our stage one upgrades.
     */
    public function do_stage_one()
    {
		/**
		 * TODO:
		 * 
            DROP `key`,
            DROP `views`,
            DROP `subs`,
		 * 
		 */
        $query = "ALTER TABLE {$this->table_name()}
            ADD `form_title` longtext {$this->collate()},
            ADD `default_label_pos` varchar(15) {$this->collate()},
            ADD `show_title` bit,
            ADD `clear_complete` bit,
            ADD `hide_complete` bit,
            ADD `logged_in` bit,
            ADD `seq_num` int";
        global $wpdb;
        $wpdb->query( $query );
    }

}
