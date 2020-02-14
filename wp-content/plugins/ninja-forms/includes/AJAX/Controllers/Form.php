<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_Controllers_Form extends NF_Abstracts_Controller
{
    private $publish_processing;

    public function __construct()
    {
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

        // All Ajax call here are handled in this file
        add_action( 'wp_ajax_nf_ajax_get_new_nonce', array( $this, 'get_new_nonce' ) );
        add_action( 'wp_ajax_nopriv_nf_ajax_get_new_nonce', array( $this, 'get_new_nonce' ) );
        add_action( 'wp_ajax_nf_save_form',   array( $this, 'save' )   );
        add_action( 'wp_ajax_nf_delete_form', array( $this, 'delete' ) );
        add_action( 'wp_ajax_nf_remove_maintenance_mode', array( $this, 'remove_maintenance_mode' ) );
    }

    public function plugins_loaded()
    {
        $this->publish_processing = new NF_Database_PublishProcessing();
    }

    public function save()
    {
        // Does the current user have admin privileges
        if (!current_user_can('manage_options')) {
            $this->_data['errors'] = esc_html__('Access denied. You must have admin privileges to view this data.', 'ninja-forms');
            $this->_respond();
        }

        check_ajax_referer( 'ninja_forms_builder_nonce', 'security' );

        if( ! isset( $_POST[ 'form' ] ) ){
            $this->_errors[] = esc_html__( 'Form Not Found', 'ninja-forms' );
            $this->_respond();
        }

        $form_data = json_decode( stripslashes( $_POST['form'] ), ARRAY_A );

        if( is_string( $form_data[ 'id' ] ) ) {
            $tmp_id = $form_data[ 'id' ];
            $form = Ninja_Forms()->form()->get();
            $form->save();
            $form_data[ 'id' ] = $form->get_id();
            $this->_data[ 'new_ids' ][ 'forms' ][ $tmp_id ] = $form_data[ 'id' ];
        } else {
            $form = Ninja_Forms()->form($form_data['id'])->get();
        }

        unset( $form_data[ 'settings' ][ '_seq_num' ] );

        $form->update_settings( $form_data[ 'settings' ] )->save();

        if( isset( $form_data[ 'fields' ] ) ) {
            $db_fields_controller = new NF_Database_FieldsController( $form_data[ 'id' ], $form_data[ 'fields' ] );
            $db_fields_controller->run();
            $form_data[ 'fields' ] = $db_fields_controller->get_updated_fields_data();
            $this->_data['new_ids']['fields'] = $db_fields_controller->get_new_field_ids();
        }

        if( isset( $form_data[ 'deleted_fields' ] ) ){

            foreach( $form_data[ 'deleted_fields' ] as  $deleted_field_id ){

                $field = Ninja_Forms()->form( $form_data[ 'id' ])->get_field( $deleted_field_id );
                $field->delete();
            }
        }

        if( isset( $form_data[ 'actions' ] ) ) {

            /*
             * Loop Actions and fire Save() hooks.
             */
            foreach ($form_data['actions'] as &$action_data) {

                $id = $action_data['id'];

                $action = Ninja_Forms()->form( $form_data[ 'id' ] )->get_action( $id );

                $action->update_settings($action_data['settings'])->save();

                $action_type = $action->get_setting( 'type' );

                if( isset( Ninja_Forms()->actions[ $action_type ] ) ) {
                    $action_class = Ninja_Forms()->actions[ $action_type ];

                    $action_settings = $action_class->save( $action_data['settings'] );
                    if( $action_settings ){
                        $action_data['settings'] = $action_settings;
                        $action->update_settings( $action_settings )->save();
                    }
                }

                if ($action->get_tmp_id()) {

                    $tmp_id = $action->get_tmp_id();
                    $this->_data['new_ids']['actions'][$tmp_id] = $action->get_id();
                    $action_data[ 'id' ] = $action->get_id();
                }

                $this->_data[ 'actions' ][ $action->get_id() ] = $action->get_settings();
            }
        }

        /*
         * Loop Actions and fire Publish() hooks.
         */
        foreach ($form_data['actions'] as &$action_data) {

            $action = Ninja_Forms()->form( $form_data[ 'id' ] )->get_action( $action_data['id'] );

            $action_type = $action->get_setting( 'type' );

            if( isset( Ninja_Forms()->actions[ $action_type ] ) ) {
                $action_class = Ninja_Forms()->actions[ $action_type ];

                if( $action->get_setting( 'active' ) && method_exists( $action_class, 'publish' ) ) {
                    $data = $action_class->publish( $this->_data );
                    if ($data) {
                        $this->_data = $data;
                    }
                }
            }
        }

        if( isset( $form_data[ 'deleted_actions' ] ) ){

            foreach( $form_data[ 'deleted_actions' ] as  $deleted_action_id ){

                $action = Ninja_Forms()->form()->get_action( $deleted_action_id );
                $action->delete();
            }
        }

        delete_user_option( get_current_user_id(), 'nf_form_preview_' . $form_data['id'] );
        WPN_Helper::update_nf_cache( $form_data[ 'id' ], $form_data );

        do_action( 'ninja_forms_save_form', $form->get_id() );

        $this->_respond();
    }

    public function delete()
    {
        // Does the current user have admin privileges
        if (!current_user_can('manage_options')) {
            $this->_data['errors'] = esc_html__('Access denied. You must have admin privileges to view this data.', 'ninja-forms');
            $this->_respond();
        }

        check_ajax_referer( 'ninja_forms_builder_nonce', 'security' );

        $this->_respond();
    }

    /**
     * This function will take all form out of maintenance mode( in case some
     * are still in maintenance mode after some required updates )
     * 
     * @since 3.4.0
     */
    public function remove_maintenance_mode() {

        // Does the current user have admin privileges
        if (!current_user_can('manage_options')) {
            $this->_data['errors'] = esc_html__('Access denied. You must have admin privileges to view this data.', 'ninja-forms');
            $this->_respond();
        }

        check_ajax_referer( 'ninja_forms_settings_nonce', 'security' );

        WPN_Helper::set_forms_maintenance_mode();

        $this->_respond();
    }

	/**
	 * Let's generate a unique nonce for each form render so that we don't get
	 * caught with an expiring nonce accidentally and fail to allow a submission
	 * @since 3.2
	 */
    public function get_new_nonce() {
    	// get a timestamp to append to nonce name
		$current_time_stamp = time();

		// Let's generate a unique nonce
    	$new_nonce_name = 'ninja_forms_display_nonce_' . $current_time_stamp;

		$res = array(
			'new_nonce' => wp_create_nonce( $new_nonce_name ),
			'nonce_ts' => $current_time_stamp );

		$this->_respond( $res );
    }
}
