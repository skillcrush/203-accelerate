<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'ninja_forms_action_deletedatarequest_settings', array(
	'message'   => array(
		'name'           => 'message',
		'type'           => 'html',
		'group'          => 'primary',
		'label'          => esc_html__( 'This is a message', 'ninja-forms' ),
		'value'          => esc_html__( 'This action adds users to WordPress\' personal data delete tool, allowing admins to comply with the GDPR and other privacy regulations from the site\'s front end.',	'ninja-forms' ),
		'width'          => 'full',
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
	'anonymize'   => array(
		'name'           => 'anonymize',
		'type'           => 'toggle',
		'group'          => 'advanced',
		'label'          => esc_html__( 'Anonymize Data', 'ninja-forms' ),
		'width'          => 'full',
	),
) );