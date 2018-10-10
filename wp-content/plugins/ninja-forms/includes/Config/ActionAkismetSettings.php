<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'ninja_forms_action_akismet_settings', array(
	'name'    => array(
		'name'           => 'name',
		'type'           => 'textbox',
		'group'          => 'primary',
		'label'          => __( 'Name', 'ninja-forms' ),
		'placeholder'    => __( 'Name field', 'ninja-forms' ),
		'width'          => 'one-half',
		'use_merge_tags' => true,
	),
	'email'   => array(
		'name'           => 'email',
		'type'           => 'textbox',
		'group'          => 'primary',
		'label'          => __( 'Email', 'ninja-forms' ),
		'placeholder'    => __( 'Email address field', 'ninja-forms' ),
		'width'          => 'one-half',
		'use_merge_tags' => true,
	),
	'url'     => array(
		'name'           => 'url',
		'type'           => 'textbox',
		'group'          => 'primary',
		'label'          => __( 'URL', 'ninja-forms' ),
		'placeholder'    => __( 'Field for a URL', 'ninja-forms' ),
		'width'          => 'one-half',
		'use_merge_tags' => true,
	),
	'message' => array(
		'name'           => 'message',
		'type'           => 'textbox',
		'group'          => 'primary',
		'label'          => __( 'Message', 'ninja-forms' ),
		'placeholder'    => __( 'Field for the message', 'ninja-forms' ),
		'width'          => 'one-half',
		'use_merge_tags' => true,
	),
) );