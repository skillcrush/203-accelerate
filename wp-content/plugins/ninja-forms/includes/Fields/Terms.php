<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Fields_Terms
 */
class NF_Fields_Terms extends NF_Fields_ListCheckbox
{
    protected $_name = 'terms';
    protected $_type = 'terms';

    protected $_nicename = 'Terms List';

    protected $_section = '';

    protected $_icon = 'tags';

    protected $_templates = array( 'terms', 'listcheckbox' );

    protected $_settings = array( 'taxonomy', 'add_new_terms' );

    protected $_settings_exclude = array( 'required' );

    protected $_excluded_taxonomies = array(
        'post_format'
    );

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Terms List', 'ninja-forms' );

        // If we are on the ninja-forms page...
        // OR we're looking at nf_sub post types...
        // OR we're editing a single post...
        if ( ( ! empty( $_GET[ 'page' ] ) && 'ninja-forms' == $_GET[ 'page' ] ) ||
           ( ! empty( $_GET[ 'post_type' ] ) && 'nf_sub' == $_GET[ 'post_type' ] ) ||
           isset( $_GET[ 'post' ] ) ) {
            // Initiate the termslist.
            add_action( 'admin_init', array( $this, 'init_settings' ) );
        }

        add_filter( 'ninja_forms_display_field', array( $this, 'active_taxonomy_field_check' ) );
        add_filter( 'ninja_forms_localize_field_' . $this->_type, array( $this, 'add_term_options' ) );
        add_filter( 'ninja_forms_localize_field_' . $this->_type . '_preview', array( $this, 'add_term_options' ) );

        add_filter( 'ninja_forms_merge_tag_value_' . $this->_type, array( $this, 'merge_tag_value' ), 10, 2 );

        $this->_settings[ 'options' ][ 'group' ] = '';
    }

    public function process( $field, $data )
    {
        return $data;
    }

    public function init_settings()
    {
        $term_settings = array();
        $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
        foreach( $taxonomies as $name => $taxonomy ){

            $tax_term_settings = array();

            if( in_array( $name, $this->_excluded_taxonomies ) ) continue;

            $this->_settings[ 'taxonomy' ][ 'options' ][] = array(
                'label' => $taxonomy->labels->name,
                'value' => $name
            );

            $terms = get_terms( $name, array( 'hide_empty' => false ) );

            foreach( $terms as $term ){

                // Check the slug instead of term_id to ensure we ONLY remove 'uncategorized'.
                if( 'uncategorized' == $term->slug ) continue;

                $tax_term_settings[] =  array(
                    'name' => 'taxonomy_term_' . $term->term_id,
                    'type' => 'toggle',
                    'label' => $term->name . ' (' . $term->count .')',
                    'width' => 'one-third',
                    'deps' => array(
                        'taxonomy' => $name
                    ),
                );
            }

            if( empty( $tax_term_settings ) ){
                $tax_term_settings[] =  array(
                    'name' => $name . '_no_terms',
                    'type' => 'html',
                    'width' => 'full',
                    'value' => sprintf( __( 'No available terms for this taxonomy. %sAdd a term%s', 'ninja-forms' ), '<a href="' . admin_url( "edit-tags.php?taxonomy=$name" ) . '">', '</a>' ),
                    'deps' => array(
                        'taxonomy' => $name
                    )
                );
            }

            $term_settings = array_merge( $term_settings, $tax_term_settings );

        }

        $term_settings[] =  array(
            'name' => '_no_taxonomy',
            'type' => 'html',
            'width' => 'full',
            'value' => __( 'No taxonomy selected.', 'ninja-forms' ),
            'deps' => array(
                'taxonomy' => ''
            )
        );

        $this->_settings[ 'taxonomy_terms' ] = array(
            'name' => 'taxonomy_terms',
            'type' => 'fieldset',
            'label' => __( 'Available Terms', 'ninja-forms' ),
            'width' => 'full',
            'group' => 'primary',
            'settings' => $term_settings
        );
    }

    public function active_taxonomy_field_check( $field )
    {
        if( $this->_type != $field->get_setting( 'type' ) ) return $field;

        $taxonomy = $field->get_setting( 'taxonomy' );

        if( ! taxonomy_exists( $taxonomy ) ) return FALSE;

        return $field;
    }

    public function add_term_options( $field )
    {
        $settings = ( is_object( $field ) ) ? $field->get_settings() : $field[ 'settings' ];

        $settings[ 'options' ] = array();

        if( isset( $settings[ 'taxonomy' ] ) && $settings[ 'taxonomy' ] ){

            $terms = get_terms( $settings[ 'taxonomy' ], array( 'hide_empty' => false ) );

            if( ! is_wp_error( $terms ) ){
                foreach( $terms as $term ) {

                    if( ! isset( $settings[ 'taxonomy_term_' . $term->term_id ] ) ) continue;
                    if( ! $settings[ 'taxonomy_term_' . $term->term_id ] ) continue;

                    $settings['options'][] = array(
                        'label' => $term->name,
                        'value' => $term->term_id,
                        'calc' => '',
                        'selected' => 0,
                        'order' => 0
                    );
                }
            }
        }

        if( is_object( $field ) ) {
            $field->update_settings( $settings );
        } else {
            $field[ 'settings' ] = $settings;
        }

        return $field;
    }

    public function merge_tag_value( $value, $field )
    {
        $terms = explode( ',', $value );
        if( ! is_array( $terms ) ) {
            $term = get_term_by( 'id', $value, $field[ 'taxonomy' ] );
            if( $term ) {
                return $term->name;
            } else {
                return $value;
            }
        }

        $term_names = array();
        foreach( $terms as $term_id ){
            $term = get_term_by( 'id', $term_id, $field[ 'taxonomy' ] );
            $term_names[] = ( $term ) ? $term->name : $term_id; // If the term is `false`, fallback to the term_id.
        }

        return implode( ',', $term_names );
    }

    public function get_parent_type()
    {
        return 'listcheckbox';
    }
}
