<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_i18n_builder', array(

    'ninjaForms' 					=> esc_html__( 'Ninja Forms', 'ninja-forms' ),
    'fieldsProductsPrice' 			=> esc_html__( 'Price:', 'ninja-forms' ),
    'fieldsProductsQuantity' 		=> esc_html__( 'Quantity:', 'ninja-forms' ),
    'fieldsTermsAdd' 				=> esc_html__( 'Add', 'ninja-forms' ),
    'fieldsTextareaOpenNewWindow' 	=> esc_html__( 'Open in new window', 'ninja-forms' ),
    'formHoneypot' 					=> esc_html__( 'If you are a human seeing this field, please leave it empty.', 'ninja-forms' ),
    'available'						=> esc_html__( 'Available', 'ninja-forms' ),
    'installed'						=> esc_html__( 'Installed', 'ninja-forms' ),
    'domainFormFields'              => esc_html__( 'Form Fields', 'ninja-forms' ),
    'domainActions'                 => esc_html__( 'Emails & Actions', 'ninja-forms' ),
    'domainAdvanced'                => esc_html__( 'Advanced', 'ninja-forms' ),
    'errorInvalidEmailFromAddress'  => sprintf( esc_html__( 'Possible issue detected. %sLearn More%s', 'ninja-forms' ), '<a href="https://ninjaforms.com/docs/troubleshooting-email-problems/" target="_blank">', '</a>' ),
    'previousMonth'                         => esc_html__( 'Previous Month', 'ninja-forms' ),
    'nextMonth'                             => esc_html__( 'Next Month', 'ninja-forms' ),
    'months'                                => array(
                                                esc_html__( 'January', 'ninja-forms' ),
                                                esc_html__( 'February', 'ninja-forms' ),
                                                esc_html__( 'March', 'ninja-forms' ),
                                                esc_html__( 'April', 'ninja-forms' ),
                                                esc_html__( 'May', 'ninja-forms' ),
                                                esc_html__( 'June', 'ninja-forms' ),
                                                esc_html__( 'July', 'ninja-forms' ),
                                                esc_html__( 'August', 'ninja-forms' ),
                                                esc_html__( 'September', 'ninja-forms' ),
                                                esc_html__( 'October', 'ninja-forms' ),
                                                esc_html__( 'November', 'ninja-forms' ),
                                                esc_html__( 'December', 'ninja-forms' )
                                            ),
    'monthsShort'                           => array(
                                                esc_html__( 'Jan', 'ninja-forms' ),
                                                esc_html__( 'Feb', 'ninja-forms' ),
                                                esc_html__( 'Mar', 'ninja-forms' ),
                                                esc_html__( 'Apr', 'ninja-forms' ),
                                                esc_html__( 'May', 'ninja-forms' ),
                                                esc_html__( 'Jun', 'ninja-forms' ),
                                                esc_html__( 'Jul', 'ninja-forms' ),
                                                esc_html__( 'Aug', 'ninja-forms' ),
                                                esc_html__( 'Sep', 'ninja-forms' ),
                                                esc_html__( 'Oct', 'ninja-forms' ),
                                                esc_html__( 'Nov', 'ninja-forms' ),
                                                esc_html__( 'Dec', 'ninja-forms' ),
                                            ),
    'weekdays'                              => array(
                                                esc_html__( 'Sunday', 'ninja-forms' ),
                                                esc_html__( 'Monday', 'ninja-forms' ),
                                                esc_html__( 'Tuesday', 'ninja-forms' ),
                                                esc_html__( 'Wednesday', 'ninja-forms' ),
                                                esc_html__( 'Thursday', 'ninja-forms' ),
                                                esc_html__( 'Friday', 'ninja-forms' ),
                                                esc_html__( 'Saturday', 'ninja-forms' ),
                                            ),
    'weekdaysShort'                         => array(
                                                esc_html__( 'Sun', 'ninja-forms' ),
                                                esc_html__( 'Mon', 'ninja-forms' ),
                                                esc_html__( 'Tue', 'ninja-forms' ),
                                                esc_html__( 'Wed', 'ninja-forms' ),
                                                esc_html__( 'Thu', 'ninja-forms' ),
                                                esc_html__( 'Fri', 'ninja-forms' ),
                                                esc_html__( 'Sat', 'ninja-forms' ),
                                            ),
    'weekdaysMin'                           => array(
                                                esc_html__( 'Su', 'ninja-forms' ),
                                                esc_html__( 'Mo', 'ninja-forms' ),
                                                esc_html__( 'Tu', 'ninja-forms' ),
                                                esc_html__( 'We', 'ninja-forms' ),
                                                esc_html__( 'Th', 'ninja-forms' ),
                                                esc_html__( 'Fr', 'ninja-forms' ),
                                                esc_html__( 'Sa', 'ninja-forms' )
                                            ),
	'fieldDataDeleteMsg'   => sprintf( esc_html__( '%sThis will also DELETE all submission data associated with this field.%sYou will not be able to retrieve this data later!%s' ), '<p>', '</p><p>', '</p><br />' ),
	'delete'    => esc_html__( 'Delete' ),
	'cancel'    => esc_html__( 'Cancel' ),
    'minVal'    => esc_html__( 'Min Value' ),
    'maxVal'    => esc_html__( 'Max Value' ),
    'valueChars' => esc_html__( 'In order to prevent errors, values may only contain'
		. ' a specific subset of characters ( a-z, 0-9, -, _, @, space ). You'
		. ' can use the option label in your success message(s) or email action(s) by adding'
		. ' the :label attribute to your list field merge tags. For example:'
		. ' {field:key:label}', 'ninja-forms' ),
));
