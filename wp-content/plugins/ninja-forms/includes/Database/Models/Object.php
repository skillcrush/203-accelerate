<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Database_Models_Object
 */
final class NF_Database_Models_Object extends NF_Abstracts_Model
{
    protected $_type = 'object';

    protected $_table_name = 'nf3_objects';

    protected $_meta_table_name = 'nf3_object_meta';

    protected $_columns = array(
        'type',
        'created_at',
        'object_title'
    );

    public function __construct( $db, $id, $parent_id = '', $parent_type = '' )
    {
        parent::__construct( $db, $id, $parent_id );

        $this->_parent_type = $parent_type;

        /**
         * Remove new DB columns from our $_columns list if the user hasn't completed required upgrades stage 1.
         */
        $sql = "SHOW COLUMNS FROM {$db->prefix}nf3_objects LIKE 'object_title'";
        $results = $db->get_results( $sql );
        /**
         * If we don't have the object_title column, we need to remove our new columns.
         *
         * Also, set our db stage 1 tracker to false.
         */
        if ( empty ( $results ) ) {
            foreach( $this->_columns as $i => $col ) {
                if( 'object_title' === $col ) {
                    unset( $this->_columns[ $i ] );
                }
            }
            $this->db_stage_1_complete = false;
        }
    }

    public function save()
    {
        if( ! $this->_id ){

            $data = array( 'created_at' => time() );

            $result = $this->_db->insert(
                $this->_table_name,
                $data
            );

            $this->_id = $this->_db->insert_id;
        }

        $this->_save_settings();
    }

} // End NF_Database_Models_Object
