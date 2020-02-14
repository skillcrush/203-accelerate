<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Action_Save
 */
final class NF_Actions_Save extends NF_Abstracts_Action
{
    /**
    * @var string
    */
    protected $_name  = 'save';

    /**
    * @var array
    */
    protected $_tags = array();

    /**
    * @var string
    */
    protected $_timing = 'late';

    /**
    * @var int
    */
    protected $_priority = '-1';

    /**
    * Constructor
    */
    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__( 'Store Submission', 'ninja-forms' );

        $settings = Ninja_Forms::config( 'ActionSaveSettings' );

        $this->_settings = array_merge( $this->_settings, $settings );

    }

    /*
    * PUBLIC METHODS
    */

    public function save( $action_settings )
    {
        if( ! isset( $_POST[ 'form' ] ) ) return;
        // Get the form data from the Post variable and send it off for processing.
        $form = json_decode( stripslashes( $_POST[ 'form' ] ) );
        $this->submission_expiration_processing( $action_settings, $form->id );
    }

    /**
     * Submission Expiration Processing
     * Decides if the submission expiration data should be added to the
     * submission expiration option or not.
     *
     * @param $action_settings - array.
     * @param $form_id - ( int ) The ID of the Form.
     *
     * @return void
     */
    public function submission_expiration_processing( $action_settings, $form_id )
    {
        /*
         * Comma separated value of the form id and action setting.
         * Example: 5,90
         */
        $expiration_value = $form_id . ',' . $action_settings[ 'subs_expire_time' ];

        // Check for option value...
        $option = get_option( 'nf_sub_expiration', array() );

        // If our expiration setting is turned on...
        if( 1 == $action_settings[ 'set_subs_to_expire' ] ) {
            // Send our data to the compare method to be added to the expiration option
            $this->compare_expiration_option( $expiration_value, $option );
        } else {
            // Otherwise send the data to be removed from the expiration option.
            $this->remove_expiration_option( $expiration_value, $option );
        }
    }

    /**
     * Compare Expiration Option
     * Accepts $expiration_data and checks to see if the values already exist in the array.
     * @since 3.3.2
     *
     * @param array $expiration_value - key/value pair
     *      $expiration_value[ 'form_id' ]      = form_id(int)
     *      $expiration_value[ 'expire_time' ]  = subs_expire_time(int)
     * @param array $expiration_option - list of key/value pairs of the expiration options.
     *
     * @return void
     */
    public function compare_expiration_option( $expiration_value, $expiration_option )
    {
        /*
         * Breaks a part our options.
         *      $value[ 0 ] - ( int ) Form ID
         *      $value[ 1 ] - ( int ) Expiration time in days
         */
        $values = explode( ',', $expiration_value );

        // Find the position of the value we are tyring to update.
        $array_position = array_search( ( int ) $values[ 0 ], $expiration_option );

        /*
         * TODO: Refactor this to only run when needed.
         * Remove this value from the array.
         */
        if( isset( $array_position ) ) {
            unset( $expiration_option[ $array_position ] );
        }

        // Check for our value in the options and then add it if it doesn't exist.
        if( ! in_array( $expiration_value, $expiration_option ) ) {
            $expiration_option[] = $expiration_value;
        }

        // Update our option.
        update_option( 'nf_sub_expiration', $expiration_option  );
    }

    /**
     * Remove Expiration Option
     * If the expiration action setting is turned off this helper method
     * removes the form id and expiration time from the option.
     *
     * @param array $expiration_value - key/value pair
     *      $expiration_value[ 'form_id' ]      = form_id(int)
     *      $expiration_value[ 'expire_time' ]  = subs_expire_time(int)
     * @param array $expiration_option - list of key/value pairs of the expiration options.
     *
     * @return void
     */
    public function remove_expiration_option( $expiration_value, $expiration_option )
    {
        $values = explode( ',', $expiration_value );

        // Find the position of the value we are tyring to update.
        $array_position = array_search( ( int ) $values[ 0 ], $expiration_option );

        /*
         * TODO: Refactor this to only run when needed.
         * Remove this value from the array.
         */
        if( isset( $array_position ) ) {
            unset( $expiration_option[ $array_position ] );
        }

        // Update our option.
        update_option( 'nf_sub_expiration', $expiration_option  );
    }

    public function process( $action_settings, $form_id, $data )
    {
        if( isset( $data['settings']['is_preview'] ) && $data['settings']['is_preview'] ){
            return $data;
        }

        if( ! apply_filters ( 'ninja_forms_save_submission', true, $form_id ) ) return $data;

        $sub = Ninja_Forms()->form( $form_id )->sub()->get();

        $hidden_field_types = apply_filters( 'nf_sub_hidden_field_types', array() );

        // For each field on the form...
        foreach( $data['fields'] as $field ){

            // If this is a "hidden" field type.
            if( in_array( $field[ 'type' ], array_values( $hidden_field_types ) ) ) {
                // Do not save it.
                $data[ 'actions' ][ 'save' ][ 'hidden' ][] = $field[ 'type' ];
                continue;
            }

            $field[ 'value' ] = apply_filters( 'nf_save_sub_user_value', $field[ 'value' ], $field[ 'id' ] );

            $save_all_none = $action_settings[ 'fields-save-toggle' ];
            $save_field = true;

            // If we were told to save all fields...
            if( 'save_all' == $save_all_none ) {
            	$save_field = true;
                // For each exception to that rule...
            	foreach( $action_settings[ 'exception_fields' ] as $exception_field ) {
                    // Remove it from the list.
            		if( $field[ 'key' ] == $exception_field[ 'field'] ) {
            			$save_field = false;
            			break;
		            }
	            }
            } // Otherwise... (We were told to save no fields.)
            else if( 'save_none' == $save_all_none ) {
            	$save_field = false;
                // For each exception to that rule...
	            foreach( $action_settings[ 'exception_fields' ] as
		            $exception_field ) {
                    // Add it to the list.
		            if( $field[ 'key' ] == $exception_field[ 'field'] ) {
			            $save_field = true;
			            break;
		            }
	            }
            }

            // If we're supposed to save this field...
            if( $save_field ) {
                // Do so.
	            $sub->update_field_value( $field[ 'id' ], $field[ 'value' ] );
            } // Otherwise...
            else {
                // If this field is not a list...
                // AND If this field is not a checkbox...
                // AND If this field is not a product...
                // AND If this field is not a termslist...
                if ( false == strpos( $field[ 'type' ], 'list' ) &&
                    false == strpos( $field[ 'type' ], 'checkbox' ) &&
                    'products' !== $field[ 'type' ] &&
                    'terms' !== $field[ 'type' ] ) {
                    // Anonymize it.
                    $sub->update_field_value( $field[ 'id' ], '(redacted)' );
                }
            }
        }

        // If we have extra data...
        if( isset( $data[ 'extra' ] ) ) {
            // Save that.
            $sub->update_extra_values( $data[ 'extra' ] );
        }

        do_action( 'nf_before_save_sub', $sub->get_id() );

        $sub->save();

        do_action( 'nf_save_sub', $sub->get_id() );
        do_action( 'nf_create_sub', $sub->get_id() );
        do_action( 'ninja_forms_save_sub', $sub->get_id() );

        $data[ 'actions' ][ 'save' ][ 'sub_id' ] = $sub->get_id();

        return $data;
    }
}
