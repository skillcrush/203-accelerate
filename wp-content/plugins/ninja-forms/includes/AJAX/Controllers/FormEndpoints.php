<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_Controllers_FormEndpoints extends NF_Abstracts_Controller
{
    /*
     * Constructor
     */
    public function __construct()
    {
//        add_action( 'wp_ajax_nf_get_forms', array( $this, 'get_forms' ) );
//        add_action( 'wp_ajax_nf_get_new_form_templates', array( $this, 'get_new_form_templates' ) );
//        add_action( 'wp_ajax_nf_delete', array( $this, 'delete' ) );
//        add_action( 'wp_ajax_nf_duplicate', array( $this, 'duplicate' ) );
    }

    /*
     *
     */
    public function get_forms()
    {
        $db_forms_controller = new NF_Database_FormsController();
        $forms_json = $db_forms_controller->getFormsData();
        $this->_respond( $forms_json );
    }

    /*
     *
     */
    public function get_new_form_templates()
    {
        $templates = Ninja_Forms()->config( 'NewFormTemplates' );
        die( json_encode( $templates ) );
    }

    /*
     *
     */
    public function delete()
    {
        if (!isset($_REQUEST['form_id']) || empty($_REQUEST['form_id'])) {
            $this->_data['errors'][] = 'Invalid Form ID';
            $this->_respond();
        }
        $id = absint($_REQUEST['form_id']);

        try{
            $form = Ninja_Forms()->form( $id )->get();
            $this->_data[ 'delete' ] = $form->delete();;
        } catch( Exception $e ) {
            $this->_data[ 'errors' ][] = $e->getMessage();
        }
        $this->_respond();
    }

    public function duplicate()
    {
        $form_id = absint($_REQUEST[ 'form_id' ]);

        //Copied and pasted from NF_Database_models_Form::duplicate line 136
        $form = Ninja_Forms()->form( $form_id )->get();

        $settings = $form->get_settings();

        $new_form = Ninja_Forms()->form()->get();
        $new_form->update_settings( $settings );

        $form_title = $form->get_setting( 'title' );

        $new_form_title = $form_title . " - " . esc_html__( 'copy', 'ninja-forms' );

        $new_form->update_setting( 'title', $new_form_title );

        $new_form->update_setting( 'lock', 0 );

        $new_form->save();

        $new_form_id = $new_form->get_id();

        $fields = Ninja_Forms()->form( $form_id )->get_fields();

        foreach( $fields as $field ){

            $field_settings = $field->get_settings();

            $field_settings[ 'parent_id' ] = $new_form_id;

            $new_field = Ninja_Forms()->form( $new_form_id )->field()->get();
            $new_field->update_settings( $field_settings )->save();
        }

        $actions = Ninja_Forms()->form( $form_id )->get_actions();

        foreach( $actions as $action ){

            $action_settings = $action->get_settings();

            $new_action = Ninja_Forms()->form( $new_form_id )->action()->get();
            $new_action->update_settings( $action_settings )->save();
        }

        return $new_form_id;

    }
}
