<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_merge_tags_form', array(

   /*
   |--------------------------------------------------------------------------
   | Form ID
   |--------------------------------------------------------------------------
   */

	'form_id' => array(
		'id' => 'form_id',
		'tag' => '{form:id}',
		'label' => esc_html__( 'Form ID', 'ninja_forms' ),
		'callback' => 'get_form_id',
	),

    /*
    |--------------------------------------------------------------------------
    | Form Title
    |--------------------------------------------------------------------------
    */

    'form_title' => array(
        'id' => 'form_title',
        'tag' => '{form:title}',
        'label' => esc_html__( 'Form Title', 'ninja_forms' ),
        'callback' => 'get_form_title',
    ),

    /*
    |--------------------------------------------------------------------------
    | Submission Sequence Number
    |--------------------------------------------------------------------------
    */

    'sub_seq' => array(
        'id' => 'sub_seq',
        'tag' => '{submission:sequence}',
        'label' => esc_html__( 'Sub Sequence', 'ninja_forms' ),
        'callback' => 'getSubSeq',
    ),

    /*
    |--------------------------------------------------------------------------
    | Submission Count
    |--------------------------------------------------------------------------
    */

    'sub_count' => array(
        'id' => 'sub_count',
        'tag' => '{submission:count}',
        'label' => esc_html__( 'Submission Count', 'ninja_forms' ),
        'callback' => 'get_sub_count',
    ),

    /*
     |--------------------------------------------------------------------------
     | Display ONLY
     |--------------------------------------------------------------------------
     | These merge tags are for display only and are processed elsewhere.
     */

    'all_fields_table' => array(
        'id' => 'all_fields_table',
        'tag' => '{all_fields_table}',
        'label' => esc_html__( 'All Fields Table', 'ninja_forms' ),
        'callback' => '',
    ),

    'fields_table' => array(
        'id' => 'fields_table',
        'tag' => '{fields_table}',
        'label' => esc_html__( 'Fields Table', 'ninja_forms' ),
        'callback' => '',
    ),

));