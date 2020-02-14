<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Database_Models_Action
 */
final class NF_Database_Models_Action extends NF_Abstracts_Model
{
    private $form_id = '';

    protected $_type = 'action';

    protected $_table_name = 'nf3_actions';

    protected $_meta_table_name = 'nf3_action_meta';

    protected $_columns = array(
        'title',
        'key',
        'type',
        'active',
        'created_at',
        'label'
    );

    public function __construct( $db, $id, $parent_id = '' )
    {
        parent::__construct( $db, $id, $parent_id );

        /**
         * Remove new DB columns from our $_columns list if the user hasn't completed required upgrades stage 1.
         */
        $sql = "SHOW COLUMNS FROM {$db->prefix}nf3_actions LIKE 'label'";
        $results = $db->get_results( $sql );
        /**
         * If we don't have the label column, we need to remove our new columns.
         *
         * Also, set our db stage 1 tracker to false.
         */
        if ( empty ( $results ) ) {
            foreach( $this->_columns as $i => $col ) {
                if( 'label' === $col ) {
                    unset( $this->_columns[ $i ] );
                }
            }
            $this->db_stage_1_complete = false;
        }
    }

} // End NF_Database_Models_Action
