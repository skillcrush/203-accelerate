<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Abstracts_ModelFactory
 */
class NF_Abstracts_ModelFactory
{
    /**
     * Database Object
     *
     * @var
     */
    protected $_db;

    /**
     * The last set object.
     *   Used to create context between two objects in a chain.
     *
     * @var object
     */
    protected $_object;

    /**
     * Form
     */
     protected $_form;

    /**
     * Fields
     *
     * An array of field model objects.
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Actions
     *
     * An array of action model objects.
     *
     * @var array
     */
    protected $_actions = array();

    /**
     * Objects
     *
     * An array of generic model objects.
     *
     * @var array
     */
    protected $_objects = array();

    //-----------------------------------------------------
    // Public Methods
    //-----------------------------------------------------

    /**
     * NF_Abstracts_ModelFactory constructor.
     * @param $db
     * @param $id
     */
    public function __construct( $db, $id )
    {
        $this->_db = $db;

        $this->_object = $this->_form = new NF_Database_Models_Form( $this->_db, $id );

        if(WPN_Helper::use_cache()) {
            $form_cache = WPN_Helper::get_nf_cache( $id );

            if( $form_cache && isset ( $form_cache[ 'settings' ] ) ){
                $this->_object->update_settings( $form_cache[ 'settings' ] );
            }
        }

        return $this;
    }

    /**
     * PHP MAGIC method for catching undefined methods.
     * Use this as a passthrough for methods of the form model.
     * 
     * @param $method (String) The name of the called method.
     * @param $args (Array) The arguments supplied to the method.
     * 
     * @return (Mixed) Will match the return type of the supplied method.
     * 
     * @since UPDATE_VERSION_ON_MERGE
     */
    public function __call( $method, $args ) {
        // If this is a method of the form model...
        if ( method_exists( $this->_object, $method ) ) {
            // Instantiate a reflector method.
            $mirror = new ReflectionMethod( get_class( $this->_object ), $method );
            // If the method is not private...
            if ( ! $mirror->isPrivate() ) {
                // If args were supplied...
                if ( ! empty( $args ) ) {
                    // Pass them into the function.
                    return $this->_object->{$method}( $args );
                }
                return $this->_object->{$method}();
            }
        }
    }

    /**
     * Returns the parent object set by the constructor for chained methods.
     *
     * @return object
     */
    public function get()
    {
        $object = $this->_object;
        $this->_object = $this->_form;
        return $object;
    }

    /**
     * Get Forms
     *
     * Returns an array of Form Model Objects.
     *
     * @param array $where
     * @return array|bool
     */
    public function get_forms( array $where = array() )
    {
        if( 'form' != $this->_object->get_type() ) return FALSE;

        return $this->_object->find( NULL, $where );
    }

    /**
     * Export Form
     *
     * A wrapper for the Form Model export method.
     *
     * @param bool|FALSE $return
     * @return array
     */
    public function export_form( $return = FALSE )
    {
        $form_id = $this->_object->get_id();

        return NF_Database_Models_Form::export( $form_id, $return );
    }

    /**
     * Import Form
     *
     * A wrapper for the Form Model import method.
     *
     * @param $import
     * @param $decode_utf8
     * @param $id
     * @param $is_conversion
     */
    public function import_form( $import, $decode_utf8 = TRUE, $id = FALSE,
		$is_conversion = FALSE )
    {
        
        if( ! is_array( $import ) ){
			// Check to see if user turned off UTF-8 encoding
        	if( $decode_utf8 ) {
		        $data = WPN_Helper::utf8_decode( json_decode( WPN_Helper::json_cleanup( html_entity_decode( $import ) ), true ) );
	        } else {
		        $data = json_decode( WPN_Helper::json_cleanup( html_entity_decode( $import ) ), true );
	        }

            if( ! is_array( $data ) ) {
	            if( $decode_utf8 ) {
		            $data = WPN_Helper::utf8_decode( json_decode( WPN_Helper::json_cleanup( $import ), true ) );
	            } else {
		            $data = json_decode( WPN_Helper::json_cleanup( $import ), true );
	            }
            }

            if( ! is_array( $data ) ){
                $data = WPN_Helper::maybe_unserialize( $import );

                if( ! is_array( $data ) ){
                    return false;
                }
            }
            $import = $data;
        }

        return NF_Database_Models_Form::import( $import, $id, $is_conversion );
    }

    /*
     * FIELDS
     */

    /**
     * Sets the parent object for chained methods as a Field.
     *
     * @param string $id
     * @return $this
     */
    public function field( $id = '' )
    {
        $form_id = $this->_object->get_id();

        $this->_object = new NF_Database_Models_Field( $this->_db, $id, $form_id );

        return $this;
    }

    /**
     * Returns a field object.
     *
     * @param $id
     * @return NF_Database_Models_Field
     */
    public function get_field( $id )
    {
    	if( isset( $this->_fields[ $id ] ) ){
            return $this->_fields[ $id ];
        }

        /* MISSING FORM ID FALLBACK */
        /*
        if( ! $form_id ){
            $form_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT parent_id from {$wpdb->prefix}nf3_fields WHERE id = %d", $id
            ));
            $this->_object = $this->_form = new NF_Database_Models_Form( $this->_db, $id );
        }
        */

        if( ! $this->_fields ){
			$this->get_fields();
        }

        if( ! isset( $this->_fields[ $id ] ) ){
            $form_id = $this->_object->get_id();
            $this->_fields[ $id ] = new NF_Database_Models_Field( $this->_db, $id, $form_id );
        }

        return $this->_fields[ $id ];
    }

    /**
     * Returns an array of field objects for the set form (object).
     *
     * @param array $where
     * @param bool|FALSE $fresh
     * @return array
     */
    public function get_fields( $where = array(), $fresh = FALSE)
    {

        $field_by_key = array();

        $form_id = $this->_object->get_id();
        if ( ! $form_id && empty( $where ) ) {
            $this->_fields = array();
        }

        if( $where || $fresh || ! $this->_fields ){

            // @TODO: Remove the second half of this IF block and replace it with a required update check.
            if(0 !== $form_id && (WPN_Helper::use_cache() || 1 == $form_id)) {
                $form_cache = WPN_Helper::get_nf_cache( $form_id );
            } else {
                $form_cache = false;
            }

            if( ! $form_cache ) {
                $model_shell = new NF_Database_Models_Field($this->_db, 0);

                $fields = $model_shell->find($form_id, $where);

                foreach ($fields as $field) {
                    $this->_fields[$field->get_id()] = $field;
                    $field_by_key[ $field->get_setting( 'key' ) ] = $field;
                }
            } else {
                foreach( $form_cache[ 'fields' ] as $cached_field ){
                    $field = new NF_Database_Models_Field( $this->_db, $cached_field[ 'id' ], $form_id );
                    $field->update_settings( $cached_field[ 'settings' ] );
                    $this->_fields[$field->get_id()] = $field;
                    $field_by_key[ $field->get_setting( 'key' ) ] = $field;
                }
            }

            /*
             * If a filter is registered to modify field order, then use that filter.
             * If not, then usort??.
             */
            $order = apply_filters( 'ninja_forms_get_fields_sorted', array(), $this->_fields, $field_by_key, $form_id );

            if ( ! empty( $order ) ) {
                $this->_fields = $order;
            }
        }

        /*
         * Broke the sub edit screen order when I have this enabled.
         */
        // usort( $this->_fields, "NF_Abstracts_Field::sort_by_order" );

        return $this->_fields;
    }

    /**
     * Import Field
     *
     * A wrapper for the Form Model import method.
     *
     * @param $import
     */
    public function import_field( $settings, $field_id = '', $is_conversion = FALSE )
    {
        $settings = maybe_unserialize( $settings );
        NF_Database_Models_Field::import( $settings, $field_id, $is_conversion );
    }


    /*
     * ACTIONS
     */

    /**
     * Sets the parent object for chained methods as an Action.
     *
     * @param string $id
     * @return $this
     */
    public function action( $id ='' )
    {
        $form_id = $this->_object->get_id();

        $this->_object = new NF_Database_Models_Action( $this->_db, $id, $form_id );

        return $this;
    }

    /**
     * Returns an action object.
     *
     * @param $id
     * @return NF_Database_Models_Action
     */
    public function get_action( $id )
    {
        $form_id = $this->_object->get_id();

        return $this->_actions[ $id ] = new NF_Database_Models_Action( $this->_db, $id, $form_id );
    }

    /**
     * Returns an array of action objects for the set form (object).
     *
     * @param array $where
     * @param bool|FALSE $fresh
     * @return array
     */
    public function get_actions( $where = array(), $fresh = FALSE)
    {
        if( $where || $fresh || ! $this->_actions ){

            $form_id = $this->_object->get_id();

            $model_shell = new NF_Database_Models_Action($this->_db, 0);

            $actions = $model_shell->find($form_id, $where);

            foreach ($actions as $action) {
                $action->get_setting( 'type' ); // Pre-load the type of action for usort()
                $this->_actions[$action->get_id()] = $action;
            }
        }

        usort( $this->_actions, 'NF_Abstracts_Action::sort_actions' );

        return $this->_actions;
    }

    /*
     * OBJECTS
     */

    /**
     * Sets the parent object for chained methods as an Object.
     *
     * @param string $id
     * @return $this
     */
    public function object( $id = '' )
    {
        $parent_id = $this->_object->get_id();
        $parent_type = $this->_object->get_type();

        $this->_object = new NF_Database_Models_Object( $this->_db, $id, $parent_id, $parent_type );

        return $this;
    }

    /**
     * Returns an object.
     *
     * @param $id
     * @return NF_Database_Models_Object
     */
    public function get_object( $id )
    {
        return $this->_objects[ $id ] = new NF_Database_Models_Object( $this->_db, $id );
    }

    /**
     * Returns an array of objects for the set object.
     *
     * @param array $where
     * @param bool|FALSE $fresh
     * @return array
     */
    public function get_objects( $where = array(), $fresh = FALSE)
    {
        if( $where || $fresh || ! $this->_objects ){

            $form_id = $this->_object->get_id();

            $model_shell = new NF_Database_Models_Object( $this->_db, 0 );

            $objects = $model_shell->find( $form_id, $where );

            foreach( $objects as $object ){
                $this->_objects[ $object->get_id() ] = $object;
            }
        }

        return $this->_objects;
    }

    /*
     * SUBMISSIONS
     */

    /**
     * Submission
     *
     * Returns a single submission by ID,
     *   or an empty submission.
     *
     * @param string $id
     * @return $this
     */
    public function sub( $id = '' )
    {
        $form_id = $this->_object->get_id();

        $this->_object = new NF_Database_Models_Submission( $id, $form_id );

        return $this;
    }

    /**
     * Get Submission
     *
     * Returns a single submission by ID.
     *
     * @param $id
     * @return NF_Database_Models_Submission
     */
    public function get_sub( $id )
    {
        $parent_id = $this->_object->get_id();

        return $this->_objects[ $id ] = new NF_Database_Models_Submission( $id, $parent_id );
    }

    /**
     * Get Submissions
     *
     * Returns an array of Submission Model Objects.
     *
     * @param array $where
     * @param bool|FALSE $fresh
     * @return array
     */
    public function get_subs( $where = array(), $fresh = FALSE, $sub_ids = array() )
    {
        if( $where || $fresh || $sub_ids || ! $this->_objects ){

            $form_id = $this->_object->get_id();

            $model_shell = new NF_Database_Models_Submission( 0 );

            $objects = $model_shell->find( $form_id, $where, $sub_ids );

            foreach( $objects as $object ){
                $this->_objects[ $object->get_id() ] = $object;
            }
        }

        return $this->_objects;
    }

    /**
     * Export Submissions
     *
     * A wrapper for the Submission Model export method.
     *
     * @param array $sub_ids
     * @param bool|FALSE $return
     * @return string
     */
    public function export_subs( array $sub_ids = array(), $return = FALSE )
    {
        $form_id = $this->_object->get_id();

        return NF_Database_Models_Submission::export( $form_id, $sub_ids, $return );
    }

    /*
     * GENERIC
     */

    /**
     * Get Model
     *
     * A generic method call for any object model type.
     *
     * @param $id
     * @param $type
     * @return bool|NF_Database_Models_Action|NF_Database_Models_Field|NF_Database_Models_Form|NF_Database_Models_Object
     */
    public function get_model( $id, $type )
    {
        global $wpdb;

        switch( $type ){
            case 'form':
                return new NF_Database_Models_Form( $wpdb, $id );
                break;
            case 'field':
                return new NF_Database_Models_Field( $wpdb, $id );
                break;
            case 'action':
                return new NF_Database_Models_Action( $wpdb, $id );
                break;
            case 'object':
                return new NF_Database_Models_Object( $wpdb, $id );
                break;
            default:
                return FALSE;
        }
    }

} // End Class NF_Abstracts_ModelFactory
