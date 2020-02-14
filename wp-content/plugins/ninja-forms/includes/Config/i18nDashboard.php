<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_i18n_dashboard', array(

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
    'deleteWarningA'                        => esc_html__( 'You are about to delete the form', 'ninja-forms' ),
    'deleteWarningB'                        => esc_html__( 'Once deleted, it\'s fields and submissions cannot be recovered. Proceed with caution.',
                                                   'ninja-forms' ),
    'deleteConfirmA'                        => esc_html__( 'Type', 'ninja-forms' ),
    'deleteConfirmB'                        => esc_html__( 'to confirm', 'ninja-forms' ),
    'delete'                                => esc_html__( 'Delete', 'ninja-forms' ),
    'cancel'                                => esc_html__( 'Cancel', 'ninja-forms' ),
    'deleteTitle'                           => esc_html__( 'Confirm Delete', 'ninja-forms' ),
    'deleteXForm'                           => esc_html__( 'Export Form', 'ninja-forms' ),
    'deleteXSubs'                           => esc_html__( 'Export Submissions', 'ninja-forms' ),
    'optinContent'                          => sprintf( esc_html__( '%sWe would like to collect data about how Ninja Forms is used so that we can improve the experience for everyone. This data will not include ANY submission data or personally identifiable information.%sPlease check out our %sprivacy policy%s for additional clarification.%s', 'ninja-forms' ), '<p>', '</p><p>', '<a href="https://ninjaforms.com/privacy-policy/" target="_blank">', '</a>', '</p>' ),
    'optinYesplease'                        => esc_html__( 'Yes, please send me occasional emails about Ninja Forms.', 'ninja-forms' ),
    'optinSecondary'                        => esc_html__( 'Not Now', 'ninja-forms' ),
    'optinPrimary'                          => esc_html__( 'Yes, I agree!', 'ninja-forms' ),
    'optinAwesome'                          => esc_html__( 'Keep being awesome!', 'ninja-forms' ),
    'optinThanks'                           => esc_html__( 'Thank you for opting in!', 'ninja-forms' ),
    'cleanupContent'                        => sprintf( esc_html__( '%sOnce we begin this process, it might take several minutes to complete.%sNavigating away from this page before it is finished could lead to unexpected results.%sPlease confirm when you are ready to begin.%s', 'ninja-forms' ), '<p>', '</p><p>', '</p><p>', '</p>' ),
    'cleanupSecondary'                      => esc_html__( 'Cancel', 'ninja-forms' ),
    'cleanupPrimary'                        => sprintf( esc_html__( 'Clean up my data', 'ninja-forms' ) ),
    'cleanupLoading'                        => esc_html__( 'Processing...', 'ninja-forms' ),

    /**
     * Services Tab
     */

    /** OAuth Controller */
    'oauthDisconnectContent'               => sprintf( esc_html__( 'Disconnecting from my.ninjaforms.com will disrupt the functionality of all services. To manage your service subscriptions please visit %smy.ninjaforms.com%s', 'ninja-forms' ), '<a href="https://my.ninjaforms.com">', '</a>' ),
    'oauthDisconnectConfirm'               => esc_html__( 'Disconnect', 'ninja-forms' ),
    'oauthDisconnectCancle'                => esc_html__( 'Stay Connected', 'ninja-forms' ),
    'oauthLearnMoreContent'                => sprintf( esc_html__( '%sSince you’re using one of our Ninja Forms services, like Ninja Mail or our Add-on Manager, your site is connected to my.ninjaforms.com. This allows us to send data between your site and my.ninjaforms.com. For details about what is being shared, you can see our %sPrivacy Policy%s.%s', 'ninja-forms' ), '<p>', '<a href="https://ninjaforms.com/privacy-policy/">', '</a>', '</p>'),

    /** Service Model */
    'serviceRedirect'                     => sprintf( esc_html__( '%sRedirecting to NinjaForms.com%s', 'ninja-forms' ), '<p style="text-align:center;">', '</p>' ),
    'serviceUpdateError'                  => esc_html__( 'Unable to update the service.' , 'ninja-forms' ),
));
