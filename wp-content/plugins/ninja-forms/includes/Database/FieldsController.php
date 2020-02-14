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
    /**
     * An array of UPDATE SQL strings.
     *
     * i.e. array( 'key' => 'WHERE `id` = X THEN...' )
     * 
     * @var array
     */
    private $update_fields = array( 
        'id' => '', 
        'key' => '', 
        'label' => '', 
        'type' => '', 
        'field_key' => '', 
        'field_label' => '', 
        'order' => '', 
        'default_value' => '', 
        'label_pos' => '', 
        'required' => '',
        'personally_identifiable' => '',
    );
    private $update_field_meta = array();
    private $update_field_meta_chunk = 0;

    private $db_stage_1_complete = true;

    /**
     * Store an array of columns that we want to store in our table rather than meta.
     *
     * This array stores the column name and the name of the setting that it maps to.
     * 
     * The format is:
     *
     * array( 'COLUMN_NAME' => 'SETTING_NAME' )
     */
    private $db_columns = array(
        'parent_id'                 => 'parent_id',
        'id'                        => 'id',
        'key'                       => 'key',
        'type'                      => 'type',
        'label'                     => 'label',
        'field_key'                 => 'key',
        'field_label'               => 'label',
        'order'                     => 'order',
        'required'                  => 'required',
        'default_value'             => 'default',
        'label_pos'                 => 'label_pos',
        'personally_identifiable'   => 'personally_identifiable',
    );

    private $db_bit_columns = array(
        'required',
        'personally_identifiable',
    );

    public function __construct( $form_id, $fields_data )
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->form_id = $form_id;
        $this->fields_data = $fields_data;

        /**
         * Remove new DB columns from our $db_columns list if the user hasn't completed required upgrades stage 1.
         */
        $sql = "SHOW COLUMNS FROM {$this->db->prefix}nf3_fields LIKE 'field_key'";
        $results = $this->db->get_results( $sql );
        /**
         * If we don't have the field_key column, we need to remove our new columns.
         *
         * Also, set our db stage 1 tracker to false.
         */
        if ( empty ( $results ) ) {
            unset( $this->db_columns[ 'field_key' ] );
            unset( $this->db_columns[ 'field_label' ] );
            unset( $this->db_columns[ 'order' ] );
            unset( $this->db_columns[ 'required' ] );
            unset( $this->db_columns[ 'default_value' ] );
            unset( $this->db_columns[ 'label_pos' ] );
            unset( $this->db_columns[ 'personally_identifiable' ] );

            $this->db_stage_1_complete = false;
        }
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

            /**
             * We've defined which items go into our DB, as well as which settings they map to.
             * 
             * Loop over our $db_columns array and setup an array for $settings.
             */
            $settings = array();

            foreach( $this->db_columns as $column_name => $setting_name ) {
                $value = '';
                if( isset( $field_data[ 'settings' ][ $setting_name ] ) ) {
                    // If the setting value is numeric, make sure it's intval'd.
                    if ( is_numeric( $field_data[ 'settings' ][ $setting_name ] ) ) {
                        $value = intval( $field_data[ 'settings' ][ $setting_name ]  );
                    } else {
                        $value = $field_data[ 'settings' ][ $setting_name ];
                    }
                }

                if ( in_array( $column_name, $this->db_bit_columns ) ) {
                    $value = absint( $value );
                }

                $settings[ $column_name ] = $value;
            }

            /**
             * We need to decide if we need to insert this field or update it in our fields table.
             * 
             * If we don't have a numeric field id, we're dealing with a tmp field, which is a new field
             *
             * If this field exists in our cache, but doesn't exist in our table, we need to insert it.
             *
             * Otherwise, we're updating.
             *
             * Check our DB for a field with this id.
             */
            if ( is_numeric( $field_id ) ) {
                $field_in_db = $this->db->get_row( "SELECT `id` FROM `{$this->db->prefix}nf3_fields` WHERE `id` = {$field_id}" );
            } else {
                $field_in_db = array();
            }

            /**
             * If $field_id isn't a number, then it's a tmp-id.
             *
             * If we have a tmp-id OR the field hasn't been found in our DB, we need to insert it.
             */
            if( ! is_numeric( $field_id ) || empty( $field_in_db ) ) {

                /**
                 * If our $field_id is numeric, we want to insert it into the db with the row.
                 *
                 * If it's not, we want to pass NULL so that we get an autoincrement.
                 */
                if ( is_numeric( $field_id ) ) {
                    $settings[ 'id' ] = $field_id;
                } else {
                    $settings[ 'id' ] = NULL;
                }
                // New Field.
                $this->insert_field( $settings );
            } else {
                // We're updating field settings.
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
                        if( $value == $existing_meta[ $field_id ][ $key ] && $value == $existing_meta[ $field_id ][ 'meta_key' ][ $key ] ) continue;
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

        $sql_select = "m.parent_id, m.key, m.value";

        /**
         * If we have completed stage 1 of our db migration, pull meta_key and meta_value as well as key and value.
         */
        if ( $this->db_stage_1_complete ) {
            $sql_select .= " , m.meta_key, m.meta_value";
        }

        $results = $this->db->get_results("
        SELECT {$sql_select}
        FROM `{$this->db->prefix}nf3_field_meta` AS m
        LEFT JOIN `{$this->db->prefix}nf3_fields` AS f
            ON m.parent_id = f.id
        WHERE f.parent_id = {$this->form_id}
        ");
        $field_meta = array();
        foreach( $results as $meta ){
            $meta_value = '';
            if( ! isset( $field_meta[ $meta->parent_id ] ) ) $field_meta[ $meta->parent_id ] = array();
            $field_meta[ $meta->parent_id ][ $meta->key ] = $meta->value;
            if ( property_exists( $meta, 'meta_value' ) ) {
                if ( ! is_null( $meta->meta_value ) ) {
                    $meta_value = $meta->meta_value;
                }
            }
            $field_meta[ $meta->parent_id ]['meta_key'][ $meta->key ] = $meta_value;
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
        // Add our initial opening parenthesis.
        $this->insert_fields .= "(";
        // Add our form id to our settings as 'parent_id'.
        $settings[ 'parent_id' ] = $this->form_id;

        /**
         * Loop over each of our $this->db_columns to create a value list for our SQL statement.
         */
        foreach ( $this->db_columns as $column_name => $setting_name ) {

            $value = $settings[ $column_name ];

            // For new fields, specify the `id` as NULL for insert.
            if('id' == $column_name && is_null($value)){
                $this->insert_fields .= "NULL,";
                continue;
            }
            
            $this->db->escape_by_ref( $value );
            if ( is_numeric( $value ) ) {
                $this->insert_fields .= "{$value},";
            } else {
                $this->insert_fields .= "'{$value}',";
            }
            
        }
        // Remove any trailing commas from our SQL string.
        $this->insert_fields = rtrim( $this->insert_fields, ',' );
        $this->insert_fields .=  '),';
    }
    public function get_insert_fields_query()
    {
        if( ! $this->insert_fields ) return "";
        $insert_fields = rtrim( $this->insert_fields, ',' ); // Strip trailing comma from SQl.
        
        /**
         * Loop over each of our $this->db_columns to create a column list for our SQL statement below.
         */
        $columns = '';
        foreach( $this->db_columns as $column_name => $setting_name ) {
            $columns .= "`{$column_name}` ,";
        }

        $columns = rtrim( $columns, ',' );

        return "
            INSERT INTO {$this->db->prefix}nf3_fields ( {$columns} )
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
            $line .= "THEN ";
            if ( is_numeric( $value ) ) {
                $line .= "{$value} ";
            } else {
                $line .= "'{$value}' ";
            }
            
            if( isset( $this->update_fields[ $setting ] ) ) {
                $this->update_fields[ $setting ] .= $line;
            } else {
                $this->update_fields[ $setting ] = $line;
            }
        }
    }
    public function get_update_fields_query()
    {
        /**
         * Loop over our $db_columns class var and make sure that none of them are empty.
         *
         * If they are empty, return an empty string to prevent errors.
         */

        foreach ( $this->db_columns as $column_name => $setting_name ) {
            if ( empty( $this->update_fields[ $column_name ] ) ) {
                return "";
            }
        }

        /**
         * Build our return statement based upon our $db_columns class var.
         */
        
        $return = "UPDATE {$this->db->prefix}nf3_fields
            SET ";

        foreach( $this->db_columns as $column_name => $setting_name ) {
            // We don't need to update our parent_id or our id.
            if ( 'id' == $column_name || 'parent_id' == $column_name ) {
                continue;
            }
            $return .= "`{$column_name}` = CASE {$this->update_fields[ $column_name ]}
                ELSE `{$column_name}`
                END
            ,";
        }

        $return = rtrim( $return, ',' );
        return $return;
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

        $insert_values = "'{$field_id}','{$key}','{$value}'";

        /**
         * If we have completed stage 1 of our db update process, then we want to add meta_key and meta_value as well as key and value.
         */
        if ( $this->db_stage_1_complete ) {
            $insert_values .= ", '{$key}','{$value}'";
        }
  
        $this->insert_field_meta[ $this->insert_field_meta_chunk ] .= "( {$insert_values} ),";

        $counter++;
        if( 0 == $counter % 5000 ) $this->insert_field_meta_chunk++;
    }

    public function run_insert_field_meta_query()
    {
        if( ! $this->insert_field_meta ) return "";
        foreach( $this->insert_field_meta as $insert_field_meta ){
            $insert_field_meta = rtrim( $insert_field_meta, ',' ); // Strip trailing comma from SQl.
            
            /**
             * If we have completed stage 1 of our db update process, then we want to insert meta_key and meta_value as well.
             */
            $insert_columns = '`parent_id`, `key`, `value`';
            if ( $this->db_stage_1_complete ) {
                $insert_columns .= ', `meta_key`, `meta_value`';
            }

            $this->db->query( "
                INSERT INTO {$this->db->prefix}nf3_field_meta ( {$insert_columns} )
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

            $sql = "UPDATE {$this->db->prefix}nf3_field_meta as field_meta
                SET `value` = CASE {$update_field_meta} ELSE `value` END";
            /**
             * If we have completed stage 1 of our db update process, then we want to update meta_value as well as value.
             */
            if ( $this->db_stage_1_complete ) {
                $sql .= ", `meta_value` = CASE {$update_field_meta} ELSE `meta_value` END, `meta_key` = CASE WHEN `parent_id` = '-999' THEN NULL ELSE `key` END";
            }

            $this->db->query( $sql );
            return;
        }
    }
}