<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_i18n_builder', array(

    'ninjaForms' 					=> __( 'Ninja Forms', 'ninja-forms' ),
    'fieldsProductsPrice' 			=> __( 'Price:', 'ninja-forms' ),
    'fieldsProductsQuantity' 		=> __( 'Quantity:', 'ninja-forms' ),
    'fieldsTermsAdd' 				=> __( 'Add', 'ninja-forms' ),
    'fieldsTextareaOpenNewWindow' 	=> __( 'Open in new window', 'ninja-forms' ),
    'formHoneypot' 					=> __( 'If you are a human seeing this field, please leave it empty.', 'ninja-forms' ),
    'available'						=> __( 'Available', 'ninja-forms' ),
    'installed'						=> __( 'Installed', 'ninja-forms' ),
    'domainFormFields'              => __( 'Form Fields', 'ninja-forms' ),
    'domainActions'                 => __( 'Emails & Actions', 'ninja-forms' ),
    'domainAdvanced'                => __( 'Advanced', 'ninja-forms' ),
    'errorInvalidEmailFromAddress'  => sprintf( __( 'Possible issue detected. %sLearn More%s', 'ninja-forms' ), '<a href="https://ninjaforms.com/docs/troubleshooting-email-problems/" target="_blank">', '</a>' ),
    'previousMonth'                         => __( 'Previous Month', 'ninja-forms' ),
    'nextMonth'                             => __( 'Next Month', 'ninja-forms' ),
    'months'                                => array(
                                                __( 'January', 'ninja-forms' ),
                                                __( 'February', 'ninja-forms' ),
                                                __( 'March', 'ninja-forms' ),
                                                __( 'April', 'ninja-forms' ),
                                                __( 'May', 'ninja-forms' ),
                                                __( 'June', 'ninja-forms' ),
                                                __( 'July', 'ninja-forms' ),
                                                __( 'August', 'ninja-forms' ),
                                                __( 'September', 'ninja-forms' ),
                                                __( 'October', 'ninja-forms' ),
                                                __( 'November', 'ninja-forms' ),
                                                __( 'December', 'ninja-forms' )
                                            ),
    'monthsShort'                           => array(
                                                __( 'Jan', 'ninja-forms' ),
                                                __( 'Feb', 'ninja-forms' ),
                                                __( 'Mar', 'ninja-forms' ),
                                                __( 'Apr', 'ninja-forms' ),
                                                __( 'May', 'ninja-forms' ),
                                                __( 'Jun', 'ninja-forms' ),
                                                __( 'Jul', 'ninja-forms' ),
                                                __( 'Aug', 'ninja-forms' ),
                                                __( 'Sep', 'ninja-forms' ),
                                                __( 'Oct', 'ninja-forms' ),
                                                __( 'Nov', 'ninja-forms' ),
                                                __( 'Dec', 'ninja-forms' ),
                                            ),
    'weekdays'                              => array(
                                                __( 'Sunday', 'ninja-forms' ),
                                                __( 'Monday', 'ninja-forms' ),
                                                __( 'Tuesday', 'ninja-forms' ),
                                                __( 'Wednesday', 'ninja-forms' ),
                                                __( 'Thursday', 'ninja-forms' ),
                                                __( 'Friday', 'ninja-forms' ),
                                                __( 'Saturday', 'ninja-forms' ),
                                            ),
    'weekdaysShort'                         => array(
                                                __( 'Sun', 'ninja-forms' ),
                                                __( 'Mon', 'ninja-forms' ),
                                                __( 'Tue', 'ninja-forms' ),
                                                __( 'Wed', 'ninja-forms' ),
                                                __( 'Thu', 'ninja-forms' ),
                                                __( 'Fri', 'ninja-forms' ),
                                                __( 'Sat', 'ninja-forms' ),
                                            ),
    'weekdaysMin'                           => array(
                                                __( 'Su', 'ninja-forms' ),
                                                __( 'Mo', 'ninja-forms' ),
                                                __( 'Tu', 'ninja-forms' ),
                                                __( 'We', 'ninja-forms' ),
                                                __( 'Th', 'ninja-forms' ),
                                                __( 'Fr', 'ninja-forms' ),
                                                __( 'Sa', 'ninja-forms' )
                                            ),
	'fieldDataDeleteMsg'   => sprintf( __( '%sThis will also DELETE all submission data associated with this field.%sYou will not be able to retrieve this data later!%s' ), '<p>', '</p><p>', '</p><br />' ),
	'delete'    => __( 'Delete' ),
	'cancel'    => __( 'Cancel' ),
    'minVal'    => __( 'Min Value' ),
    'maxVal'    => __( 'Max Value' ),
    'valueChars' => __( 'In order to prevent errors, values may only contain'
		. ' a specific subset of characters ( a-z, 0-9, -, _, @, space ). You'
		. ' can use the option label in your success message(s) or email action(s) by adding'
		. ' the :label attribute to your list field merge tags. For example:'
		. ' {field:key:label}', 'ninja-forms' ),
));
