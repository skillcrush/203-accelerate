<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'ninja_forms_action_akismet_settings', array(
	'name'    => array(
		'name'           => 'name',
		'type'           => 'textbox',
		'group'          => 'primary',
		'label'          => esc_html__( 'Name', 'ninja-forms' ),
		'placeholder'    => esc_attr__( 'Name field', 'ninja-forms' ),
		'width'          => 'one-half',
		'use_merge_tags' => true,
	),
	'email'   => array(
		'name'           => 'email',
		'type'           => 'textbox',
		'group'          => 'primary',
		'label'          => esc_html__( 'Email', 'ninja-forms' ),
		'placeholder'    => esc_attr__( 'Email address field', 'ninja-forms' ),
		'width'          => 'one-half',
		'use_merge_tags' => true,
	),
	'url'     => array(
		'name'           => 'url',
		'type'           => 'textbox',
		'group'          => 'primary',
		'label'          => esc_html__( 'URL', 'ninja-forms' ),
		'placeholder'    => esc_attr__( 'Field for a URL', 'ninja-forms' ),
		'width'          => 'one-half',
		'use_merge_tags' => true,
	),
	'message' => array(
		'name'           => 'message',
		'type'           => 'textbox',
		'group'          => 'primary',
		'label'          => esc_html__( 'Message', 'ninja-forms' ),
		'placeholder'    => esc_attr__( 'Field for the message', 'ninja-forms' ),
		'width'          => 'one-half',
		'use_merge_tags' => true,
	),
) );