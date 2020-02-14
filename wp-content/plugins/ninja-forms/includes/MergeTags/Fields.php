<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MergeTags_Fields
 */
final class NF_MergeTags_Fields extends NF_Abstracts_MergeTags
{
    protected $id = 'fields';
    protected $form_id;

    public function __construct()
    {
        parent::__construct();
        $this->title = esc_html__( 'Fields', 'ninja-forms' );
        $this->merge_tags = Ninja_Forms()->config( 'MergeTagsFields' );

        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            $this->merge_tags = array_merge( $this->merge_tags, Ninja_Forms()->config( 'MergeTagsFieldsAJAX' ) );
        }

        add_filter( 'ninja_forms_calc_setting', array( $this, 'pre_parse_calc_settings' ), 9 );
        //add_filter( 'ninja_forms_calc_setting',  array( $this, 'calc_replace' ) );
    }

    public function __call($name, $arguments)
    {
        if(isset($arguments[0]['calc'])) {
            return $this->merge_tags[ $name ][ 'calc_value' ];
        }
        return $this->merge_tags[ $name ][ 'field_value' ];
    }

    public function all_fields()
    {
        if( is_rtl() ){
            $return = '<table style="direction: rtl;">';
        } else {
            $return = '<table>';
        }

        $hidden_field_types = array( 'html', 'submit', 'password', 'passwordconfirm' );

        foreach( $this->get_fields_sorted() as $field ){

            if( ! isset( $field[ 'type' ] ) ) continue;

            if( in_array( $field[ 'type' ], array_values( $hidden_field_types ) ) ) continue;

            $field[ 'value' ] = apply_filters( 'ninja_forms_merge_tag_value_' . $field[ 'type' ], $field[ 'value' ], $field );

            if( is_array( $field[ 'value' ] ) ) $field[ 'value' ] = implode( ', ', $field[ 'value' ] );

            $return .= '<tr><td>' . apply_filters('ninja_forms_merge_label', $field[ 'label' ], $field, $this->form_id) .':</td><td>' . $field[ 'value' ] . '</td></tr>';
        }
        $return .= '</table>';
        return $return;
    }

    public function all_fields_table()
    {
        if( is_rtl() ){
            $return = '<table style="direction: rtl;">';
        } else {
            $return = '<table>';
        }

        $hidden_field_types = array( 'submit', 'password', 'passwordconfirm' );

        $list_fields_types = array( 'listcheckbox', 'listmultiselect', 'listradio', 'listselect' );

        foreach( $this->get_fields_sorted() as $field ){
            if( ! isset( $field[ 'type' ] ) ) continue;

            // Skip specific field types.
            if( in_array( $field[ 'type' ], array_values( $hidden_field_types ) ) ) continue;

            $field[ 'value' ] = apply_filters( 'ninja_forms_merge_tag_value_' . $field[ 'type' ], $field[ 'value' ], $field );

            // Check to see if the type is a list field and if it is...
            if( in_array( $field[ 'type' ], array_values( $list_fields_types ) ) ) {
                // If we have a comma separated value...
                if( strpos( $field[ 'value' ], ',' ) ) {
                    // ...build the value back into an array.
                    $field[ 'value' ] = explode( ',', $field[ 'value' ] );
                }
                // ...then set the value equal to the field label.
                $field[ 'value' ] = $this->get_list_labels( $field );
            }

            if( is_array( $field[ 'value' ] ) ) $field[ 'value' ] = implode( ', ', $field[ 'value' ] );

            // Check to see if the type is a list field and if it is...
            $return .= '<tr><td valign="top">' . apply_filters('ninja_forms_merge_label', $field[ 'label' ], $field, $this->form_id) .':</td><td>' . $field[ 'value' ] . '</td></tr>';
        }
        $return .= '</table>';
        return $return;
    }


    public function fields_table()
    {
        if( is_rtl() ){
            $return = '<table style="direction: rtl;">';
        } else {
            $return = '<table>';
        }

        $hidden_field_types = array( 'html', 'submit', 'password', 'passwordconfirm', 'hidden' );

        $list_fields_types = array( 'listcheckbox', 'listmultiselect', 'listradio', 'listselect' );

        foreach( $this->get_fields_sorted() as $field ){

            if( ! isset( $field[ 'type' ] ) ) continue;

            // Skip specific field types.
            if( in_array( $field[ 'type' ], array_values( $hidden_field_types ) ) ) continue;

            // TODO: Skip hidden fields, ie conditionally hidden.
            if( isset( $field[ 'visible' ] ) && false === $field[ 'visible' ] ) continue;

            // Check to see if the type is a list field and if it is...
            if( in_array( $field[ 'type' ], array_values( $list_fields_types ) ) ) {
                // If we have a comma separated value...
                if( strpos( $field[ 'value' ], ',' ) ) {
                    // ...build the value back into an array.
                    $field[ 'value' ] = explode( ',', $field[ 'value' ] );
                }
                // ...then set the value equal to the field label.
                $field[ 'value' ] = $this->get_list_labels( $field );
            }

            $field[ 'value' ] = apply_filters( 'ninja_forms_merge_tag_value_' . $field[ 'type' ], $field[ 'value' ], $field );

            // Skip fields without values.
            if( ! $field[ 'value' ] ) continue;

            if( is_array( $field[ 'value' ] ) ) $field[ 'value' ] = implode( ', ', $field[ 'value' ] );

            $return .= '<tr><td valign="top">' . apply_filters('ninja_forms_merge_label', $field[ 'label' ], $field, $this->form_id) .':</td><td>' . $field[ 'value' ] . '</td></tr>';
        }
        $return .= '</table>';
        return $return;
    }

    // TODO: Is this being used?
    public function all_field_plain()
    {
        $return = '';

        foreach( $this->get_fields_sorted() as $field ){

            $field[ 'value' ] = apply_filters( 'ninja_forms_merge_tag_value_' . $field[ 'type' ], $field[ 'value' ], $field );

            if( is_array( $field[ 'value' ] ) ) $field[ 'value' ] = implode( ', ', $field[ 'value' ] );

            $return .= $field[ 'label' ] .': ' . $field[ 'value' ] . "\r\n";
        }
        return $return;
    }

    public function add_field( $field )
    {
        //print_r($field);
        $hidden_field_types = apply_filters( 'nf_sub_hidden_field_types', array() );

        if( in_array( $field[ 'type' ], $hidden_field_types )
            && 'html' != $field[ 'type' ] // Specifically allow the HTML field in merge tags.
            && 'password' != $field[ 'type' ] // Specifically allow the Password field in merge tags for actions, ie User Management
        ) return;

        $field_id  = $field[ 'id' ];
        $callback  = 'field_' . $field_id;

        $list_fields_types = array( 'listcheckbox', 'listmultiselect', 'listradio', 'listselect' );

        if( is_array( $field[ 'value' ] ) ) $field[ 'value' ] = implode( ',', $field[ 'value' ] );

        $field[ 'value' ] = strip_shortcodes( $field[ 'value' ] );

        $this->merge_tags[ 'all_fields' ][ 'fields' ][ $field_id ] = $field;

	    $value = apply_filters('ninja_forms_merge_tag_value_' . $field['type'], $field['value'], $field);

	    $this->add( $callback, $field['id'], '{field:' . $field['id'] . '}', $value );

        if( isset( $field[ 'key' ] ) ) {
            $field_key =  $field[ 'key' ];
            $calc_value = apply_filters( 'ninja_forms_merge_tag_calc_value_' . $field[ 'type' ], $field['value'], $field );

            // Add Field Key Callback
            $callback = 'field_' . $field_key;
            $this->add( $callback, $field_key, '{field:' . $field_key . '}', $value, $calc_value );

            // Add Field by Key for All Fields
            $this->merge_tags[ 'all_fields_by_key' ][ 'fields' ][ $field_key ] = $field;

            // Add Field Calc Callabck
            if( '' == $calc_value ) $calc_value = '0';
            //var_dump($calc_value);
            //echo('myspace');
            $callback = 'field_' . $field_key . '_calc';
            $this->add( $callback, $field_key, '{field:' . $field_key . ':calc}', $calc_value, $calc_value );


            /*
             * Adds the ability to add :label to list field merge tags
             * this will cause the label to be displayed on the front end
             * instead of the value.
             *
             * @since 3.3.3
             */
            // Check to see if the type is a list field and if it is...
            if( in_array( $field[ 'type' ], array_values( $list_fields_types ) ) ) {
                // If we have a comma separated value...
                if ( strpos( $field[ 'value' ], ',' ) ) {
                    // ...build the value back into an array.
                    $field[ 'value' ] = explode( ',', $field[ 'value' ] );
                }
                // ...then set the value equal to the field label.
                $field[ 'value' ] = $this->get_list_labels( $field );

                // If we have multiple values in from the list field...
                if( is_array( $field[ 'value' ] ) ){
                    // ...convert our values into an array.
                    $field[ 'value' ] = implode( ', ', $field[ 'value' ] );
                }

                // Set callback and add this merge tag.
                $callback = 'field_' . $field_key . '_label';
                $this->add( $callback, $field_key, '{field:' . $field_key . ':label}', $field[ 'value' ] );
            }
        }
    }

    /**
     * Get List Labels
     * Accepts a field loops over options, compares field values and returns the labels.
     * @since 3.2.22
     *
     * @param $field array
     * @return array - label of the option.
     */
    public function get_list_labels( $field )
    {
        // Build our array to store our labels.
        $labels = array();
        // Loop over our options...
        $field[ 'options' ] = apply_filters( 'ninja_forms_render_options', $field[ 'options' ], $field );
        $field[ 'options' ] = apply_filters( 'ninja_forms_render_options_' . $field['type'], $field[ 'options' ], $field );
	    $field[ 'options' ] = apply_filters( 'ninja_forms_localize_list_labels', $field[ 'options' ], $field, $this->form_id );
        foreach( $field[ 'options' ] as $options ) {
            // ...checks to see if our list has multiple values.
            if( is_array( $field[ 'value' ] ) ) {
                // Loop over our values...
                foreach( $field[ 'value' ] as $value ) {
                    // ...See if our values match...
                    if( $options[ 'value' ] == $value ) {
                        // if they do build an array of the labels.
                        $labels[] = $options[ 'label' ];
                    }
                }
              // Otherwise if we are dealing with a single value, then...
            } elseif( $field[ 'value' ] == $options[ 'value' ] ) {
                // ...Set the label.
                $labels = $options[ 'label' ];
            }
        }
        return $labels;
    }

    /**
     * @param $callback
     * @param $id
     * @param $tag
     * @param $value
     * @param bool $calc_value
     */
	public function add( $callback, $id, $tag, $value, $calc_value = false )
	{
		$this->merge_tags[ $callback ] = array(
			'id'          => $id,
			'tag'         => $tag,
			'callback'    => $callback,
			'field_value' => $value,
            'calc_value'  => ($calc_value === false) ? $value : $calc_value,
		);
	}

    public function set_form_id( $form_id )
    {
        $this->form_id = $form_id;
    }

    private function get_fields_sorted()
    {
        $fields = $this->merge_tags[ 'all_fields' ][ 'fields' ];

        // Filterable Sorting for Add-ons (ie Layout and Multi-Part ).
        if ( has_filter( 'ninja_forms_get_fields_sorted' ) ) {
            $fields_by_key = $this->merge_tags[ 'all_fields_by_key' ][ 'fields' ];
            $fields = apply_filters( 'ninja_forms_get_fields_sorted', array(), $fields, $fields_by_key, $this->form_id );
        } else {
            // Default Sorting by Field Order.
            uasort( $fields, array( $this, 'sort_fields' ) );
        }

        return $fields;
    }

    public static function sort_fields( $a, $b )
    {
        if ( $a[ 'order' ] == $b[ 'order' ] ) {
            return 0;
        }
        return ( $a[ 'order' ] < $b[ 'order' ] ) ? -1 : 1;
    }

    public function calc_replace( $subject ) {
        if( is_array( $subject ) ){
            foreach( $subject as $i => $s ){
                $subject[ $i ] = $this->replace( $s );
            }
            return $subject;
        }
        //print_r($subject);

        preg_match_all("/{(.*?)}/", $subject, $matches );

        if( empty( $matches[0] ) ) return $subject;

        foreach( $this->merge_tags as $merge_tag ){

            if( ! in_array( $merge_tag[ 'tag' ], $matches[0] ) ) continue;

            if( ! isset($merge_tag[ 'callback' ])) continue;
            //print_r($merge_tag);
            //echo( ' = ' );

            $replace = ( is_callable( array( $this, $merge_tag[ 'callback' ] ) ) ) ? $this->{$merge_tag[ 'callback' ]}(array('calc' => true)) : '0';
            //print_r($replace);
            //echo('  myspace  ');
            if( '' == $replace ) $replace = '0';

            $subject = str_replace( $merge_tag[ 'tag' ], $replace, $subject );
        }

        return $subject;
    }

    /*
     |--------------------------------------------------------------------------
     | Calculations
     |--------------------------------------------------------------------------
     | Force {field:...:calc} in this context of calculations.
     |      Example: {field:list} -> {field:list:calc}
     | When parsing the {field:...:calc} tag, if no calc value is found then the value will be used.
     | TODO: This makes explicit list field "values" inaccessible in calculations.
     */

    public function pre_parse_calc_settings( $eq )
    {
        return preg_replace_callback( '/{field:([a-z0-9]|_|-)*}/',
	        array( $this, 'force_field_calc_tags' ), $eq );
    }

    private function force_field_calc_tags( $matches )
    {
        return str_replace( '}', ':calc}', $matches[0] );
    }

} // END CLASS NF_MergeTags_Fields
