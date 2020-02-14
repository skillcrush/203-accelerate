<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Abstracts_Batch_Process
 */
class NF_Admin_Processes_ImportFormTemplate extends NF_Admin_Processes_ImportForm
{
    protected $_slug = 'import_form_template';

    /**
     * Function to run any setup steps necessary to begin processing.
     */
    public function startup()
    {
        global $wpdb;

        // If we haven't been passed a template as extraData, then bail.
        if ( ! isset ( $_POST[ 'extraData' ][ 'template' ] ) || empty ( $_POST[ 'extraData' ][ 'template' ] ) ) {
            $this->batch_complete();
        }

        $template_file_name = WPN_Helper::esc_html($_POST[ 'extraData' ][ 'template' ]);

        /**
         * If our template_file_name is set to 'new', then respond with 'new' as our form id.
         *
         * This will redirect to the builder with a new form.
         */
        if ( 'new' == $template_file_name ) {
            $this->form[ 'ID' ] = 'new';
            $this->batch_complete();            
        }

        // Grab the data from the appropriate file location.
        $registered_templates = Ninja_Forms::config( 'NewFormTemplates' );

        if( isset( $registered_templates[ $template_file_name ] ) && ! empty( $registered_templates[ $template_file_name ][ 'form' ] ) ) {
            $form_data = $registered_templates[ $template_file_name ][ 'form' ];
        } else {
            $form_data = Ninja_Forms::template( $template_file_name . '.nff', array(), TRUE );
        }

        /**
         * If we don't have any form data, run cleanup.
         * 
         * TODO: We probably need to show an error to the user here.
         */
        if( ! $form_data ) {
            $this->cleanup();
        }

        $this->form = json_decode( html_entity_decode( $form_data ), true );

        // Determine how many steps this will take.
        $this->response[ 'step_total' ] = $this->get_steps();

        /**
         * Check to see if we've got new field columns.
         *
         * We do this here instead of the get_sql_queries() method so that we don't hit the db multiple times.
         */
        $sql = "SHOW COLUMNS FROM {$wpdb->prefix}nf3_fields LIKE 'field_key'";
        $results = $wpdb->get_results( $sql );
        
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

        add_option( 'nf_doing_' . $this->_slug, 'true', false );
    }
}