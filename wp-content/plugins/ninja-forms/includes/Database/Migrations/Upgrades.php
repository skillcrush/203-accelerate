<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations_Upgrades extends NF_Abstracts_Migration
{
    public function __construct()
    {
        parent::__construct(
            'nf3_upgrades',
            'nf_migration_create_table_upgrades'
        );
    }

    public function run()
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table_name()} (
            `id` INT(11) NOT NULL,
            `cache` LONGTEXT,
            `stage` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY ( id )
        ) {$this->charset_collate()};";

        dbDelta( $query );
    }

}
