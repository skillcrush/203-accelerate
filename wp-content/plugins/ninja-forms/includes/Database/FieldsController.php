<?php
final class NF_Database_FieldsController
{
    private $db;
    private $factory;
    private $fields_data;
    private $new_field_ids;
    private $insert_fields;
    private $insert_field_meta = array();
    private $insert_field_meta_chunk = 0;
    private $update_fields = array( 'key' => '', 'label' => '', 'type' => '' );
    private $update_field_meta = array();
    private $update_field_meta_chunk = 0;
    public function __construct( $form_id, $fields_data )
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->form_id = $form_id;
        $this->fields_data = $fields_data;
    }
    public function run()
    {
        $this->db->hide_errors();
        
        /* FIELDS */
        $this->parse_fields();
        
        $insert_fields_query = $this->get_insert_fields_query();
        if( ! empty( $insert_fields_query ) ){
            $this->db->query( $insert_fields_query );
            $this->update_new_field_ids();
        }
        
        $update_fields_query = $this->get_update_fields_query();
        if( ! empty( $update_fields_query ) ){
            $this->db->query( $update_fields_query );
        }

        /* FIELD META */
        $this->parse_field_meta();
        $this->run_insert_field_meta_query();
        $this->run_update_field_meta_query();
    }
    public function get_updated_fields_data()
    {
        return $this->fields_data;
    }
    private function parse_fields()
    {
        foreach( $this->fields_data as $field_data ){
            $field_id = $field_data[ 'id' ];
            $settings = array(
                'key' => $field_data[ 'settings' ][ 'key' ],
                'label' => $field_data[ 'settings' ][ 'label' ],
                'type' => $field_data[ 'settings' ][ 'type' ]
            );
            if( ! is_numeric( $field_id ) ) {
                $this->insert_field( $settings ); // New Field.
            } else {
                $this->update_field( $field_id, $settings );
            }
        }
    }
    private function parse_field_meta()
    {
        $existing_meta = $this->get_existing_meta();
        foreach( $this->fields_data as $field_data ){
            $field_id = $field_data[ 'id' ];
            foreach( $field_data[ 'settings' ] as $key => $value ){
                // we don't need object type or domain stored in the db
                if( ! in_array( $key, array( 'objectType', 'objectDomain' ) ) ) {
                    if( isset( $existing_meta[ $field_id ][ $key ] ) ){
                        if( $value == $existing_meta[ $field_id ][ $key ] ) continue;
                        $this->update_field_meta( $field_id, $key, $value );
                    } else {
                        $this->insert_field_meta( $field_id, $key, $value );
                    }
                }
            }
        }
    }
    private function get_existing_meta()
    {
        $results = $this->db->get_results("
        SELECT m.parent_id, m.key, m.value
        FROM `{$this->db->prefix}nf3_field_meta` AS m
        LEFT JOIN `{$this->db->prefix}nf3_fields` AS f
            ON m.parent_id = f.id
        WHERE f.parent_id = {$this->form_id}
        ");
        $field_meta = array();
        foreach( $results as $meta ){
            if( ! isset( $field_meta[ $meta->parent_id ] ) ) $field_meta[ $meta->parent_id ] = array();
            $field_meta[ $meta->parent_id ][ $meta->key ] = $meta->value;
        }
        return $field_meta;
    }
    private function update_new_field_ids()
    {
        $field_id_lookup = $this->db->get_results("
            SELECT `key`, `id`
            FROM {$this->db->prefix}nf3_fields
            WHERE `parent_id` = {$this->form_id}
        ", OBJECT_K);
        foreach( $this->fields_data as $i => $field_data ){
            $field_key = $field_data[ 'settings' ][ 'key' ];
            if( ! is_numeric( $field_data[ 'id' ] ) && isset( $field_id_lookup[ $field_key ] ) ){
                $tmp_id = $field_data[ 'id' ];
                $this->fields_data[ $i ][ 'id' ] = $this->new_field_ids[ $tmp_id ] = $field_id_lookup[ $field_key ]->id;
            }
        }
    }
    public function get_new_field_ids()
    {
        return $this->new_field_ids;
    }
    /*
    |--------------------------------------------------------------------------
    | INSERT (NEW) FIELDS
    |--------------------------------------------------------------------------
    */
    private function insert_field( $settings )
    {
        $this->insert_fields .= "(";
        foreach ( $settings as $setting => $value ) {
            $this->db->escape_by_ref( $value );
            $this->insert_fields .= "'{$value}',";
        }
        $this->insert_fields .= "'{$this->form_id}'";
        $this->insert_fields .=  '),';
    }
    public function get_insert_fields_query()
    {
        if( ! $this->insert_fields ) return "";
        $insert_fields = rtrim( $this->insert_fields, ',' ); // Strip trailing comma from SQl.
        return "
            INSERT INTO {$this->db->prefix}nf3_fields ( `key`, `label`, `type`, `parent_id` )
            VALUES {$insert_fields}
        ";
    }
    /*
    |--------------------------------------------------------------------------
    | UPDATE (EXISTING) FIELDS
    |--------------------------------------------------------------------------
    */
    private function update_field( $field_id, $settings )
    {
        foreach ( $settings as $setting => $value ) {
            $line = "WHEN `id` = '{$field_id}' ";
            $this->db->escape_by_ref( $value );
            $line .= "THEN '{$value}'";
            $this->update_fields[ $setting ] .= $line;
        }
    }
    public function get_update_fields_query()
    {
        if(
            empty( $this->update_fields[ 'key'   ] ) ||
            empty( $this->update_fields[ 'label' ] ) ||
            empty( $this->update_fields[ 'type'  ] )
            ) return "";
        return "
            UPDATE {$this->db->prefix}nf3_fields
            SET `key` = CASE {$this->update_fields[ 'key' ]}
                ELSE `key`
                END
            , `label` = CASE {$this->update_fields[ 'label' ]}
                ELSE `label`
                END
            , `type` = CASE {$this->update_fields[ 'type' ]}
                ELSE `type`
                END
        ";
    }
    /*
    |--------------------------------------------------------------------------
    | INSERT (NEW) META
    |--------------------------------------------------------------------------
    */
    private function insert_field_meta( $field_id, $key, $value )
    {
        static $counter;
        
        $value = maybe_serialize( $value );
        
        $this->db->escape_by_ref( $field_id );
        $this->db->escape_by_ref( $key );
        $this->db->escape_by_ref( $value );

        if( ! isset( $this->insert_field_meta[ $this->insert_field_meta_chunk ] ) || ! $this->insert_field_meta[ $this->insert_field_meta_chunk ] ) {
            $this->insert_field_meta[ $this->insert_field_meta_chunk ] = '';
        }
        $this->insert_field_meta[ $this->insert_field_meta_chunk ] .= "('{$field_id}','{$key}','{$value}' ),";
        $counter++;
        if( 0 == $counter % 5000 ) $this->insert_field_meta_chunk++;
    }
    public function run_insert_field_meta_query()
    {
        if( ! $this->insert_field_meta ) return "";
        foreach( $this->insert_field_meta as $insert_field_meta ){
            $insert_field_meta = rtrim( $insert_field_meta, ',' ); // Strip trailing comma from SQl.
            $this->db->query( "
                INSERT INTO {$this->db->prefix}nf3_field_meta ( `parent_id`, `key`, `value` )
                VALUES {$insert_field_meta}
            ");
        }
    }
    /*
    |--------------------------------------------------------------------------
    | UPDATE (EXISTING) META
    |--------------------------------------------------------------------------
    */
    private function update_field_meta( $field_id, $key, $value )
    {
        static $counter;

        $value = maybe_serialize( $value );
        $this->db->escape_by_ref( $key   );
        $this->db->escape_by_ref( $value );
        if( ! isset( $this->update_field_meta[ $this->update_field_meta_chunk ] ) || ! $this->update_field_meta[ $this->update_field_meta_chunk ] ) {
            $this->update_field_meta[ $this->update_field_meta_chunk ] = '';
        }
        $this->update_field_meta[ $this->update_field_meta_chunk ] .= " WHEN `parent_id` = '{$field_id}' AND `key` = '{$key}' THEN '{$value}'";

        $counter++;
        if( 0 == $counter % 5000 ) $this->update_field_meta_chunk++;
    }
    public function run_update_field_meta_query()
    {
        if( empty( $this->update_field_meta ) ) return '';
        foreach( $this->update_field_meta as $update_field_meta ){
            $this->db->query("
                UPDATE {$this->db->prefix}nf3_field_meta as field_meta
                SET `value` = CASE {$update_field_meta} ELSE `value` END
            ");
            return;
        }
    }
}