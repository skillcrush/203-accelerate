<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Abstracts_Batch_Process
 */
class NF_Admin_Processes_ImportForm extends NF_Abstracts_BatchProcess
{
    protected $_slug = 'import_form';

    private $fields_per_step = 20;

    protected $form;

    /**
     * Store an array of columns that we want to store in our table rather than meta.
     *
     * This array stores the column name and the name of the setting that it maps to.
     * 
     * The format is:
     *
     * array( 'COLUMN_NAME' => 'SETTING_NAME' )
     */
    protected $forms_db_columns = array(
        'title'                     => 'title',
        'created_at'                => 'created_at',
        'form_title'                => 'title',
        'default_label_pos'         => 'default_label_pos',
        'show_title'                => 'show_title',
        'clear_complete'            => 'clear_complete',
        'hide_complete'             => 'hide_complete',
        'logged_in'                 => 'logged_in',
        'seq_num'                   => 'seq_num',
    );

    protected $fields_db_columns = array(
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

    protected $actions_db_columns = array(
        'title'                     => 'title',
        'key'                       =>'key',
        'type'                      =>'type',
        'active'                    =>'active',
        'parent_id'                 =>'parent_id',
        'created_at'                =>'created_at',
        'updated_at'                =>'updated_at',
        'label'                     =>'label',
    );

    /**
     * Function to run any setup steps necessary to begin processing.
     *
     * @since 3.4.0
     * @return  void
     */
    public function startup()
    {
        // If we aren't passed any form content, bail.
        if ( empty ( $_POST[ 'extraData' ][ 'content' ] ) ) {
            $this->add_error( 'empty_content', esc_html__( 'No export provided.', 'ninja-forms' ), 'fatal' );
            $this->batch_complete();
        }
        $extra_content = WPN_Helper::esc_html($_POST[ 'extraData' ][ 'content']);
        $data = explode( ';base64,', $extra_content );
        $data = base64_decode( $data[ 1 ] );
        
        /**
         * $data could now hold two things, depending on whether this was a 2.9 or 3.0 export.
         * 
         * If it's a 3.0 export, the data will be json encoded.
         * If it's a 2.9 export, the data will be serialized.
         *
         * We're first going to try to json_decode. If we don't get an array, we'll unserialize.
         */

        $decoded_data = json_decode( WPN_Helper::json_cleanup( html_entity_decode( $data, ENT_QUOTES ) ), true );

        // If we don't have an array, try unserializing
        if ( ! is_array( $decoded_data ) ) {
            $decoded_data = WPN_Helper::maybe_unserialize( $data );
            if ( ! is_array( $decoded_data ) ) {
                $decoded_data = json_decode( $decoded_data, true );
            }
        }

        // Try to utf8 decode our results.
        $data = WPN_Helper::utf8_decode( $decoded_data );

        // If json_encode returns false, then this is an invalid utf8 decode.
        if ( ! json_encode( $data ) ) {
            $data = $decoded_data;
        }

        if ( ! is_array( $data ) ) {
            $this->add_error( 'decode_failed', esc_html__( 'Failed to read export. Please try again.', 'ninja-forms' ), 'fatal' );
            $this->batch_complete();
        }

        $data = $this->import_form_backwards_compatibility( $data );

        // $data is now a form array.
        $this->form = $data;

        /**
         * Check to see if we've got new field columns.
         *
         * We do this here instead of the get_sql_queries() method so that we don't hit the db multiple times.
         */
        $sql = "SHOW COLUMNS FROM {$this->_db->prefix}nf3_fields LIKE 'field_key'";
        $results = $this->_db->get_results( $sql );

        /**
         * If we don't have the field_key column, we need to remove our new columns.
         *
         * Also, set our db stage 1 tracker to false.
         */
        if ( empty ( $results ) ) {
            unset( $this->actions_db_columns[ 'label' ] );
            $db_stage_one_complete = false;
        } else {
            // Add a form value that stores whether or not we have our new DB columns.
            $db_stage_one_complete = true;            
        }

        $this->form[ 'db_stage_one_complete' ] = $db_stage_one_complete;
    }

    /**
     * On processing steps after the first, we need to grab our data from our saved option.
     * 
     * @since  3.4.0
     * @return void
     */
    public function restart()
    {
        // Get our remaining fields from the database.
        $this->form = get_option( 'nf_import_form', array() );
    }

    /**
     * Function to loop over the batch.
     *
     * @since  3.4.0
     * @return void
     */
    public function process()
    {
        /**
         * Check to see if our $this->form var contains an 'ID' index.
         *
         * If it doesn't, then we need to:
         *     Insert our Form.
         *         Insert our Form Meta.
         *         Insert our Actions.
         *         Insert our Action Meta.
         *         Unset [ 'settings' ] and [ 'actions' ] from $this->form.
         *         Update $this->form[ 'ID' ].
         *     Save our processing option.
         *     Move on to the next step.
         */
        if ( ! isset( $this->form[ 'ID' ] ) ) {
            $this->insert_form();
        } else { // We have a form ID set.
            $this->insert_fields();
        }

        // If we don't have any more fields to insert, we're done.
        if ( empty( $this->form[ 'fields' ] ) ) {
            // Update our form cache for the new form.
            WPN_Helper::build_nf_cache( $this->form[ 'ID' ] );
            // We're done with this batch process.
            $this->batch_complete();
        } else { // We have fields left to process.
            /**
             * If we have fields left, we need to reset the index.
             * Since fields is a non-associative array, we are looping over it by sequential numeric index.
             * Resetting the index ensures we always have a 0 -> COUNT() keys.
             */
            $this->form[ 'fields' ] = array_values( $this->form[ 'fields' ] );
            // Save our progress.
            update_option( 'nf_import_form', $this->form, 'no' );
            // Move on to the next step in processing.
            $this->next_step();
        }
    }

    /**
     * Function to cleanup any lingering temporary elements of a batch process after completion.
     */
    public function cleanup()
    {
        // Remove the option we used to track between
        delete_option( 'nf_import_form' );
        // Return our new Form ID
        $this->response[ 'form_id' ] = $this->form[ 'ID' ];
    }

    /*
     * Get Steps
     * Determines the amount of steps needed for the step processors.
     *
     * @return int of the number of steps.
     */
    public function get_steps()
    {
        /**
         * We want to run a step for every $this->fields_per_step fields on this form.
         *
         * If we have no fields, then we want to return 0.
         */
        if ( ! isset ( $this->form[ 'fields' ] ) || empty ( $this->form[ 'fields' ] ) ) {
            return 0;
        }

        $steps = count( $this->form[ 'fields' ] ) / $this->fields_per_step;
        $steps = ceil( $steps );
        return $steps;
    }

    /**
     * Insert our form using $this->_db->insert by building an array of column => value pairs and %s, %d types.
     *
     * @since  3.4.0
     * @return void
     */
    public function insert_form()
    {
        $insert_columns = array();
        $insert_columns_types = array();
        foreach ( $this->forms_db_columns as $column_name => $setting_name ) {
            // Make sure we don't try to set created_at to NULL.
            if( 'created_at' === $column_name && is_null( $this->form[ 'settings' ][ $setting_name ] ) ) continue;
            $insert_columns[ $column_name ] = $this->form[ 'settings' ][ $setting_name ];
            if ( is_numeric( $this->form[ 'settings' ][ $setting_name ] ) ) {
                array_push( $insert_columns_types, '%d' );
            } else {
                array_push( $insert_columns_types, '%s' );
            }
        }

        $this->_db->insert( "{$this->_db->prefix}nf3_forms", $insert_columns, $insert_columns_types );

        // Update our form ID with the newly inserted row ID.
        $this->form[ 'ID' ] = $this->_db->insert_id;

        if ( 0 === $this->form[ 'ID' ] ) {
            $this->add_error( 'insert_failed', esc_html__( 'Failed to insert new form.', 'ninja-forms' ), 'fatal' );
            $this->batch_complete();
        }

        $this->insert_form_meta();
        $this->insert_actions();

        // Remove our settings and actions array items.
        unset( $this->form[ 'settings' ], $this->form[ 'actions' ] );
    }

    /**
     * Insert Form Meta.
     * 
     * Loop over our remaining form settings that we need to insert into meta.
     * Add them to our "Values" string for insertion later.
     * 
     * @since  3.4.0
     * @return void
     */
    public function insert_form_meta()
    {
        $insert_values = '';

        $blacklist = array(
            'embed_form',
            'public_link',
            'public_link_key',
            'allow_public_link',
        );
        $blacklist = apply_filters( 'ninja_forms_excluded_import_form_settings', $blacklist );

        foreach( $this->form[ 'settings' ] as $meta_key => $meta_value ) {
            if ( in_array( $meta_key, $blacklist ) ) continue;
            $meta_value = maybe_serialize( $meta_value );
            $this->_db->escape_by_ref( $meta_value );
            $insert_values .= "( {$this->form[ 'ID' ]}, '{$meta_key}', '{$meta_value}'";
            if ( $this->form[ 'db_stage_one_complete'] ) {
                $insert_values .= ", '{$meta_key}', '{$meta_value}'";
            }
            $insert_values .= "),";
        }

        // Remove the trailing comma.
        $insert_values = rtrim( $insert_values, ',' );
        $insert_columns = '`parent_id`, `key`, `value`';
        if ( $this->form[ 'db_stage_one_complete'] ) {
            $insert_columns .= ', `meta_key`, `meta_value`';
        }
        
        // Create SQL string.
        $sql = "INSERT INTO {$this->_db->prefix}nf3_form_meta ( {$insert_columns} ) VALUES {$insert_values}";
        // Run our SQL query.
        $this->_db->query( $sql );
    }

    /**
     * Insert Actions and Action Meta.
     *
     * Loop over actions for this form and insert actions and action meta.
     * 
     * @since  3.4.0
     * @return void
     */
    public function insert_actions()
    {
        foreach( $this->form[ 'actions' ] as $action_settings ) {
            $action_settings[ 'parent_id' ] = $this->form[ 'ID' ];
            // Array that tracks which settings need to be meta and which are columns.
            $action_meta = $action_settings;
            $insert_columns = array();
            $insert_columns_types = array();
            // Loop over all our action columns to get their values.
            foreach ( $this->actions_db_columns as $column_name => $setting_name ) {
                $insert_columns[ $column_name ] = $action_settings[ $setting_name ];
                if ( is_numeric( $action_settings[ $setting_name ] ) ) {
                    array_push( $insert_columns_types, '%d' );
                } else {
                    array_push( $insert_columns_types, '%s' );
                }
            }

            // Insert Action
            $this->_db->insert( "{$this->_db->prefix}nf3_actions", $insert_columns, $insert_columns_types );
            
            // Get our new action ID.
            $action_id = $this->_db->insert_id;

            // Insert Action Meta.
            $insert_values = '';
            /**
             * Anything left in the $action_meta array should be inserted as meta.
             *
             * Loop over each of our settings and add it to our insert sql string.
             */
            $insert_values = '';
            foreach ( $action_meta as $meta_key => $meta_value ) {
                $meta_value = maybe_serialize( $meta_value );
                $this->_db->escape_by_ref( $meta_value );
                $insert_values .= "( {$action_id}, '{$meta_key}', '{$meta_value}'";
                if ( $this->form[ 'db_stage_one_complete'] ) {
                    $insert_values .= ", '{$meta_key}', '{$meta_value}'";
                }
                $insert_values .= "),";
            }
            
            // Remove the trailing comma.
            $insert_values = rtrim( $insert_values, ',' );
            $insert_columns = '`parent_id`, `key`, `value`';
            if ( $this->form[ 'db_stage_one_complete'] ) {
                $insert_columns .= ', `meta_key`, `meta_value`';
            }
            // Create SQL string.
            $sql = "INSERT INTO {$this->_db->prefix}nf3_action_meta ( {$insert_columns} ) VALUES {$insert_values}";

            // Run our SQL query.
            $this->_db->query( $sql );
        }
    }

    /**
     * If we have a Form ID set, then we've already inserted our Form, Form Meta, Actions, and Action Meta.
     * All we have left to insert are fields.
     *
     * Loop over our fields array and insert up to $this->fields_per_step.
     * After we've inserted the field, unset it from our form array.
     * Update our processing option with $this->form.
     * Respond with the remaining steps.
     * 
     * @since  3.4.0
     * @return void
     */
    public function insert_fields()
    {
        // Remove new field table columns if we haven't completed stage one of our DB conversion.
        if ( ! $this->form[ 'db_stage_one_complete' ] ) {
            // Remove field columns added after stage one.
            unset( $this->fields_db_columns[ 'field_key' ] );
            unset( $this->fields_db_columns[ 'field_label' ] );
            unset( $this->fields_db_columns[ 'order' ] );
            unset( $this->fields_db_columns[ 'required' ] );
            unset( $this->fields_db_columns[ 'default_value' ] );
            unset( $this->fields_db_columns[ 'label_pos' ] );
            unset( $this->fields_db_columns[ 'personally_identifiable' ] );
        }

        /**
         * Loop over our field array up to $this->fields_per_step.
         */
        for ( $i = 0; $i < $this->fields_per_step; $i++ ) {
            // If we don't have a field, skip this $i.
            if ( ! isset ( $this->form[ 'fields' ][ $i ] ) ) {
                // Remove this field from our fields array.
                unset( $this->form[ 'fields' ][ $i ] );
                // If we haven't exceeded the field total...
                if ( $i < count( $this->form[ 'fields' ] ) ) {
                    $this->add_error( 'empty_field', esc_html__( 'Some fields might not have been imported properly.', 'ninja-forms' ) );
                }
                continue;
            }

            $field_settings = $this->form[ 'fields' ][ $i ];
            // Remove a field ID if we have one set.
            unset( $field_settings[ 'id' ] );
            $field_settings[ 'parent_id' ] = $this->form[ 'ID' ];
            // Array that tracks which settings need to be meta and which are columns.
            $field_meta = $field_settings;
            $insert_columns = array();
            $insert_columns_types = array();
            // Loop over all our action columns to get their values.
            foreach ( $this->fields_db_columns as $column_name => $setting_name ) {
                $insert_columns[ $column_name ] = $field_settings[ $setting_name ];
                if ( is_numeric( $field_settings[ $setting_name ] ) ) {
                    array_push( $insert_columns_types, '%d' );
                } else {
                    array_push( $insert_columns_types, '%s' );
                }
            }

            // Add our field to the database.
            $this->_db->insert( "{$this->_db->prefix}nf3_fields", $insert_columns, $insert_columns_types );

            /**
             * Get our new field ID.
             */
            $field_id = $this->_db->insert_id;

            $insert_values = '';
            /**
             * Anything left in the $field_meta array should be inserted as meta.
             *
             * Loop over each of our settings and add it to our insert sql string.
             */
            $insert_values = '';
            foreach ( $field_meta as $meta_key => $meta_value ) {
                $meta_value = maybe_serialize( $meta_value );
                $this->_db->escape_by_ref( $meta_value );
                $insert_values .= "( {$field_id}, '{$meta_key}', '{$meta_value}'";
                if ( $this->form[ 'db_stage_one_complete'] ) {
                    $insert_values .= ", '{$meta_key}', '{$meta_value}'";
                }
                $insert_values .= "),";
            }
            
            // Remove the trailing comma.
            $insert_values = rtrim( $insert_values, ',' );
            $insert_columns = '`parent_id`, `key`, `value`';
            if ( $this->form[ 'db_stage_one_complete'] ) {
                $insert_columns .= ', `meta_key`, `meta_value`';
            }
            // Create SQL string.
            $sql = "INSERT INTO {$this->_db->prefix}nf3_field_meta ( {$insert_columns} ) VALUES {$insert_values}";

            // Run our SQL query.
            $this->_db->query( $sql );

            // Remove this field from our fields array.
            unset( $this->form[ 'fields' ][ $i ] );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Backwards Compatibility
    |--------------------------------------------------------------------------
    */

    public function import_form_backwards_compatibility( $import )
    {
        // Rename `data` to `settings`
        if( isset( $import[ 'data' ] ) ){
            $import[ 'settings' ] = $import[ 'data' ];
            unset( $import[ 'data' ] );
        }

        // Rename `notifications` to `actions`
        if( isset( $import[ 'notifications' ] ) ){
            $import[ 'actions' ] = $import[ 'notifications' ];
            unset( $import[ 'notifications' ] );
        }

        // Rename `form_title` to `title`
        if( isset( $import[ 'settings' ][ 'form_title' ] ) ){
            $import[ 'settings' ][ 'title' ] = $import[ 'settings' ][ 'form_title' ];
            unset( $import[ 'settings' ][ 'form_title' ] );
        }

        // Convert `last_sub` to `_seq_num`
        if( isset( $import[ 'settings' ][ 'last_sub' ] ) ) {
            $import[ 'settings' ][ '_seq_num' ] = $import[ 'settings' ][ 'last_sub' ] + 1;
        }

        // Make sure
        if( ! isset( $import[ 'fields' ] ) ){
            $import[ 'fields' ] = array();
        }

        // `Field` to `Fields`
        if( isset( $import[ 'field' ] ) ){
            $import[ 'fields' ] = $import[ 'field' ];
            unset( $import[ 'field' ] );
        }

        $import = apply_filters( 'ninja_forms_upgrade_settings', $import );

        // Combine Field and Field Data
        foreach( $import[ 'fields' ] as $key => $field ){

            if( '_honeypot' == $field[ 'type' ] ) {
                unset( $import[ 'fields' ][ $key ] );
                continue;
            }

            if( ! $field[ 'type' ] ) {
                unset( $import[ 'fields'][ $key ] );
                continue;
            }

            // TODO: Split Credit Card field into multiple fields.
            $field = $this->import_field_backwards_compatibility( $field );

            if( isset( $field[ 'new_fields' ] ) ){
                foreach( $field[ 'new_fields' ] as $new_field ){
                    $import[ 'fields' ][] = $new_field;
                }
                unset( $field[ 'new_fields' ] );
            }

            $import[ 'fields' ][ $key ] = $field;
        }

        $has_save_action = FALSE;
        foreach( $import[ 'actions' ] as $key => $action ){
            $action = $this->import_action_backwards_compatibility( $action );
            $import[ 'actions' ][ $key ] = $action;

            if( 'save' == $action[ 'type' ] ) $has_save_action = TRUE;
        }

        if( ! $has_save_action ) {
            $import[ 'actions' ][] = array(
                'type' => 'save',
                'label' => esc_html__( 'Save Form', 'ninja-forms' ),
                'active' => TRUE
            );
        }

        $import = $this->import_merge_tags_backwards_compatibility( $import );

        return apply_filters( 'ninja_forms_after_upgrade_settings', $import );
    }

    public function import_merge_tags_backwards_compatibility( $import )
    {
        $field_lookup = array();

        foreach( $import[ 'fields' ] as $key => $field ){

            if( ! isset( $field[ 'id' ] ) ) continue;

            $field_id  = $field[ 'id' ];
            $field_key = $field[ 'type' ] . '_' . $field_id;
            $field_lookup[ $field_id ] = $import[ 'fields' ][ $key ][ 'key' ] = $field_key;
        }

        foreach( $import[ 'actions' ] as $key => $action_settings ){
            foreach( $action_settings as $setting => $value ){
                foreach( $field_lookup as $field_id => $field_key ){

                    // Convert Tokenizer
                    $token = 'field_' . $field_id;
                    if( ! is_array( $value ) ) {
                        if (FALSE !== strpos($value, $token)) {
                            $value = str_replace($token, '{field:' . $field_key . '}', $value);
                        }
                    }

                    // Convert Shortcodes
                    $shortcode = "[ninja_forms_field id=$field_id]";
                    if( ! is_array( $value ) ) {
                        if ( FALSE !== strpos( $value, $shortcode ) ) {
                            $value = str_replace( $shortcode, '{field:' . $field_key . '}', $value );
                        }
                    }
                }

                //Checks for the nf_sub_seq_num short code and replaces it with the submission sequence merge tag
                $sub_seq = '[nf_sub_seq_num]';
                if( ! is_array( $value ) ) {
                    if( FALSE !== strpos( $value, $sub_seq ) ){
                        $value = str_replace( $sub_seq, '{submission:sequence}', $value );
                    }
                }

                if( ! is_array( $value ) ) {
                    if (FALSE !== strpos($value, '[ninja_forms_all_fields]')) {
                        $value = str_replace('[ninja_forms_all_fields]', '{field:all_fields}', $value);
                    }
                }
                $action_settings[ $setting ] = $value;
                $import[ 'actions' ][ $key ] = $action_settings;
            }
        }

        return $import;
    }

    public function import_action_backwards_compatibility( $action )
    {
        // Remove `_` from type
        if( isset( $action[ 'type' ] ) ) {
            $action['type'] = str_replace('_', '', $action['type']);
        }

        if( 'email' == $action[ 'type' ] ){
            $action[ 'to' ]             = str_replace( '`', ',', $action[ 'to' ] );
            $action[ 'email_subject' ]  = str_replace( '`', ',', $action[ 'email_subject' ] );
            $action[ 'cc' ]         = str_replace( '`', ',', $action[ 'cc' ] );
            $action[ 'bcc' ]        = str_replace( '`', ',', $action[ 'bcc' ] );
            // If our email is in plain text...
            if ( $action[ 'email_format' ] == 'plain' ) {
                // Record it as such.
                $action[ 'email_message_plain' ] = $action[ 'email_message' ];
            } // Otherwise... (It's not plain text.)
            else {
                // Record it as HTML.
                $action[ 'email_message' ] = nl2br( $action[ 'email_message' ] );
            }
        }

        // Convert `name` to `label`
        if( isset( $action[ 'name' ] ) ) {
            $action['label'] = $action['name'];
            unset($action['name']);
        }

        return apply_filters( 'ninja_forms_upgrade_action_' . $action[ 'type' ], $action );
    }

    public function import_field_backwards_compatibility( $field )
    {
        // Flatten field settings array
        if( isset( $field[ 'data' ] ) && is_array( $field[ 'data' ] ) ){
            $field = array_merge( $field, $field[ 'data' ] );
        }
        unset( $field[ 'data' ] );

        // Drop form_id in favor of parent_id, which is set by the form.
        if( isset( $field[ 'form_id' ] ) ){
            unset( $field[ 'form_id' ] );
        }

        // Remove `_` prefix from type setting
        $field[ 'type' ] = ltrim( $field[ 'type' ], '_' );

        // Type: `text` -> `textbox`
        if( 'text' == $field[ 'type' ] ){
            $field[ 'type' ] = 'textbox';
        }

        if( 'submit' == $field[ 'type' ] ){
            $field[ 'processing_label' ] = 'Processing';
        }

        if( isset( $field[ 'email' ] ) ){

            if( 'textbox' == $field[ 'type' ] && $field[ 'email' ] ) {
                $field['type'] = 'email';
            }
            unset( $field[ 'email' ] );
        }

        if( isset( $field[ 'class' ] ) ){
            $field[ 'element_class' ] = $field[ 'class' ];
            unset( $field[ 'class' ] );
        }

        if( isset( $field[ 'req' ] ) ){
            $field[ 'required' ] = $field[ 'req' ];
            unset( $field[ 'req' ] );
        }

        if( isset( $field[ 'default_value_type' ] ) ){

            /* User Data */
            if( '_user_id' == $field[ 'default_value_type' ] )           $field[ 'default' ] = '{wp:user_id}';
            if( '_user_email' == $field[ 'default_value_type' ] )        $field[ 'default' ] = '{wp:user_email}';
            if( '_user_lastname' == $field[ 'default_value_type' ] )     $field[ 'default' ] = '{wp:user_last_name}';
            if( '_user_firstname' == $field[ 'default_value_type' ] )    $field[ 'default' ] = '{wp:user_first_name}';
            if( '_user_display_name' == $field[ 'default_value_type' ] ) $field[ 'default' ] = '{wp:user_display_name}';

            /* Post Data */
            if( 'post_id' == $field[ 'default_value_type' ] )    $field[ 'default' ] = '{wp:post_id}';
            if( 'post_url' == $field[ 'default_value_type' ] )   $field[ 'default' ] = '{wp:post_url}';
            if( 'post_title' == $field[ 'default_value_type' ] ) $field[ 'default' ] = '{wp:post_title}';

            /* System Data */
            if( 'today' == $field[ 'default_value_type' ] ) $field[ 'default' ] = '{other:date}';

            /* Miscellaneous */
            if( '_custom' == $field[ 'default_value_type' ] && isset( $field[ 'default_value' ] ) ){
                $field[ 'default' ] = $field[ 'default_value' ];
            }
            if( 'querystring' == $field[ 'default_value_type' ] && isset( $field[ 'default_value' ] ) ){
                $field[ 'default' ] = '{querystring:' . $field[ 'default_value' ] . '}';
            }

            unset( $field[ 'default_value' ] );
            unset( $field[ 'default_value_type' ] );
        } else if ( isset ( $field[ 'default_value' ] ) ) {
            $field[ 'default' ] = $field[ 'default_value' ];
        }

        if( 'list' == $field[ 'type' ] ) {

            if ( isset( $field[ 'list_type' ] ) ) {

                if ('dropdown' == $field['list_type']) {
                    $field['type'] = 'listselect';
                }
                if ('radio' == $field['list_type']) {
                    $field['type'] = 'listradio';
                }
                if ('checkbox' == $field['list_type']) {
                    $field['type'] = 'listcheckbox';
                }
                if ('multi' == $field['list_type']) {
                    $field['type'] = 'listmultiselect';
                }
            }

            if( isset( $field[ 'list' ][ 'options' ] ) ) {
                $field[ 'options' ] = array_values( $field[ 'list' ][ 'options' ] );
                unset( $field[ 'list' ][ 'options' ] );
            }

            foreach( $field[ 'options' ] as &$option ){
                if( isset( $option[ 'value' ] ) && $option[ 'value' ] ) continue;
                $option[ 'value' ] = $option[ 'label' ];
            }
        }

        if( 'country' == $field[ 'type' ] ){
            $field[ 'type' ] = 'listcountry';
            $field[ 'options' ] = array();
        }

        // Convert `textbox` to other field types
        foreach( array( 'fist_name', 'last_name', 'user_zip', 'user_city', 'user_phone', 'user_email', 'user_address_1', 'user_address_2', 'datepicker' ) as $item ) {
            if ( isset( $field[ $item ] ) && $field[ $item ] ) {
                $field[ 'type' ] = str_replace( array( '_', 'user', '1', '2', 'picker' ), '', $item );

                unset( $field[ $item ] );
            }
        }

        if( 'timed_submit' == $field[ 'type' ] ) {
            $field[ 'type' ] = 'submit';
        }

        if( 'checkbox' == $field[ 'type' ] ){

            if( isset( $field[ 'calc_value' ] ) ){

                if( isset( $field[ 'calc_value' ][ 'checked' ] ) ){
                    $field[ 'checked_calc_value' ] = $field[ 'calc_value' ][ 'checked' ];
                    unset( $field[ 'calc_value' ][ 'checked' ] );
                }
                if( isset( $field[ 'calc_value' ][ 'unchecked' ] ) ){
                    $field[ 'unchecked_calc_value' ] = $field[ 'calc_value' ][ 'unchecked' ];
                    unset( $field[ 'calc_value' ][ 'unchecked' ] );
                }
            }
        }

        if( 'rating' == $field[ 'type' ] ){
            $field[ 'type' ] = 'starrating';

            if( isset( $field[ 'rating_stars' ] ) ){
                $field[ 'default' ] = $field[ 'rating_stars' ];
                unset( $field[ 'rating_stars' ] );
            }
        }

        if( 'number' == $field[ 'type' ] ){

            if( ! isset( $field[ 'number_min' ] ) || ! $field[ 'number_min' ] ){
                $field[ 'num_min' ] = '';
            } else {
                $field[ 'num_min' ] = $field[ 'number_min' ];
            }

            if( ! isset( $field[ 'number_max' ] ) || ! $field[ 'number_max' ] ){
                $field[ 'num_max' ] = '';
            } else {
                $field[ 'num_max' ] = $field[ 'number_max' ];
            }

            if( ! isset( $field[ 'number_step' ] ) || ! $field[ 'number_step' ] ){
                $field[ 'num_step' ] = 1;
            } else {
                $field[ 'num_step' ] = $field[ 'number_step' ];
            }
        }

        if( 'profile_pass' == $field[ 'type' ] ){
            $field[ 'type' ] = 'password';

            $passwordconfirm = array_merge( $field, array(
                'id' => '',
                'type' => 'passwordconfirm',
                'label' => $field[ 'label' ] . ' ' . esc_html__( 'Confirm', 'ninja-forms' ),
                'confirm_field' => 'password_' . $field[ 'id' ]
            ));
            $field[ 'new_fields' ][] = $passwordconfirm;
        }

        if( 'desc' == $field[ 'type' ] ){
            $field[ 'type' ] = 'html';
        }

        if( 'credit_card' == $field[ 'type' ] ){

            $field[ 'type' ] = 'creditcardnumber';
            $field[ 'label' ] = $field[ 'cc_number_label' ];
            $field[ 'label_pos' ] = 'above';

            if( $field[ 'help_text' ] ){
                $field[ 'help_text' ] = '<p>' . $field[ 'help_text' ] . '</p>';
            }

            $credit_card_fields = array(
                'creditcardcvc'        => $field[ 'cc_cvc_label' ],
                'creditcardfullname'   => $field[ 'cc_name_label' ],
                'creditcardexpiration' => $field[ 'cc_exp_month_label' ] . ' ' . $field[ 'cc_exp_year_label' ],
                'creditcardzip'        => esc_html__( 'Credit Card Zip', 'ninja-forms' ),
            );


            foreach( $credit_card_fields as $new_type => $new_label ){
                $field[ 'new_fields' ][] = array_merge( $field, array(
                    'id' => '',
                    'type' => $new_type,
                    'label' => $new_label,
                    'help_text' => '',
                    'desc_text' => ''
                ));
            }
        }

        /*
         * Convert inside label position over to placeholder
         */
        if ( isset ( $field[ 'label_pos' ] ) && 'inside' == $field[ 'label_pos' ] ) {
            if ( ! isset ( $field[ 'placeholder' ] ) || empty ( $field[ 'placeholder' ] ) ) {
                $field[ 'placeholder' ] = $field[ 'label' ];
            }
            $field[ 'label_pos' ] = 'hidden';
        }

        if( isset( $field[ 'desc_text' ] ) ){
            $field[ 'desc_text' ] = nl2br( $field[ 'desc_text' ] );
        }
        if( isset( $field[ 'help_text' ] ) ){
            $field[ 'help_text' ] = nl2br( $field[ 'help_text' ] );
        }


        return apply_filters( 'ninja_forms_upgrade_field', $field );
    }
}