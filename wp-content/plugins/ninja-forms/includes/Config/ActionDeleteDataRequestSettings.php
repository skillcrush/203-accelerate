<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'ninja_forms_action_deletedatarequest_settings', array(
	'message'   => array(
		'name'           => 'message',
		'type'           => 'html',
		'group'          => 'primary',
		'label'          => __( 'This is a message', 'ninja-forms' ),
		'value'          => __( 'This action adds users to WordPress\' personal data delete tool, allowing admins to comply with the GDPR and other privacy regulations from the site\'s front end.',	'ninja-forms' ),
		'width'          => 'full',
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
	'anonymize'   => array(
		'name'           => 'anonymize',
		'type'           => 'toggle',
		'group'          => 'advanced',
		'label'          => __( 'Anonymize Data', 'ninja-forms' ),
		'width'          => 'full',
	),
) );