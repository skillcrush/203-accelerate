<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_i18n_dashboard', array(

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
    'deleteWarningA'                        => __( 'You are about to delete the form', 'ninja-forms' ),
    'deleteWarningB'                        => __( 'Once deleted, it\'s fields and submissions cannot be recovered. Proceed with caution.',
                                                   'ninja-forms' ),
    'deleteConfirmA'                        => __( 'Type', 'ninja-forms' ),
    'deleteConfirmB'                        => __( 'to confirm', 'ninja-forms' ),
    'delete'                                => __( 'Delete', 'ninja-forms' ),
    'cancel'                                => __( 'Cancel', 'ninja-forms' ),
    'deleteTitle'                           => __( 'Confirm Delete', 'ninja-forms' ),
    'deleteXForm'                           => __( 'Export Form', 'ninja-forms' ),
    'deleteXSubs'                           => __( 'Export Submissions', 'ninja-forms' ),
    'optinContent'                          => sprintf( __( '%sWe would like to collect data about how Ninja Forms is used so that we can improve the experience for everyone. This data will not include ANY submission data or personally identifiable information.%sPlease check out our %sprivacy policy%s for additional clarification.%s', 'ninja-forms' ), '<p>', '</p><p>', '<a href="https://ninjaforms.com/privacy-policy/" target="_blank">', '</a>', '</p>' ),
    'optinYesplease'                        => __( 'Yes, please send me occasional emails about Ninja Forms.', 'ninja-forms' ),
    'optinSecondary'                        => __( 'Not Now', 'ninja-forms' ),
    'optinPrimary'                          => __( 'Yes, I agree!', 'ninja-forms' ),
    'optinAwesome'                          => __( 'Keep being awesome!', 'ninja-forms' ),
    'optinThanks'                           => __( 'Thank you for opting in!', 'ninja-forms' ),
    'cleanupContent'                        => sprintf( __( '%sOnce we begin this process, it might take several minutes to complete.%sNavigating away from this page before it is finished could lead to unexpected results.%sPlease confirm when you are ready to begin.%s', 'ninja-forms' ), '<p>', '</p><p>', '</p><p>', '</p>' ),
    'cleanupSecondary'                      => __( 'Cancel', 'ninja-forms' ),
    'cleanupPrimary'                        => sprintf( __( 'Clean up my data', 'ninja-forms' ) ),
    'cleanupLoading'                        => __( 'Processing...', 'ninja-forms' ),

    /**
     * Services Tab
     */

    /** OAuth Controller */
    'oauthDisconnectContent'               => sprintf( __( 'Disconnecting from my.ninjaforms.com will disrupt the functionality of all services. To manage your service subscriptions please visit %smy.ninjaforms.com%s', 'ninja-forms' ), '<a href="https://my.ninjaforms.com">', '</a>' ),
    'oauthDisconnectConfirm'               => __( 'Disconnect', 'ninja-forms' ),
    'oauthDisconnectCancle'                => __( 'Stay Connected', 'ninja-forms' ),
    'oauthLearnMoreContent'                => sprintf( __( '%sSince you’re using one of our Ninja Forms services, like Ninja Mail or our Add-on Manager, your site is connected to my.ninjaforms.com. This allows us to send data between your site and my.ninjaforms.com. For details about what is being shared, you can see our %sPrivacy Policy%s.%s', 'ninja-forms' ), '<p>', '<a href="https://ninjaforms.com/privacy-policy/">', '</a>', '</p>'),

    /** Service Model */
    'serviceRedirect'                     => sprintf( __( '%sRedirecting to NinjaForms.com%s', 'ninja-forms' ), '<p style="text-align:center;">', '</p>' ),
    'serviceUpdateError'                  => __( 'Unable to update the service.' , 'ninja-forms' ),
));
