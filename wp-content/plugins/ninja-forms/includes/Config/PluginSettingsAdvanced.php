<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_plugin_settings_advanced', array(

    /*
    |--------------------------------------------------------------------------
    | Delete on Uninstall
    |--------------------------------------------------------------------------
    */

    'delete_on_uninstall' => array(
        'id'    => 'delete_on_uninstall',
        'type'  => 'html',
        'html'  => '<button type="button" id="delete_on_uninstall" href="" class="button">' .
                   __(	'Delete All Data', 'ninja-forms' ) . '</button>',
        'label' => __( 'Remove ALL Ninja Forms data upon uninstall?', 'ninja-forms' ),
        'desc'  => sprintf( __( 'If this button is checked, ALL Ninja Forms data will be removed from the database and the Ninja Forms plug-in will be deactivated. %sAll form and submission data will be unrecoverable.%s', 'ninja-forms' ), '<span class="nf-nuke-warning">', '</span>' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Delete Prompt for Delete on Uninstall
    |--------------------------------------------------------------------------
    */

    'delete_prompt' => array(
        'id'    => 'delete_prompt',
        'type'  => 'prompt',
        'desc'  => __( 'This setting will COMPLETELY remove anything Ninja Forms related upon plugin deletion. This includes SUBMISSIONS and FORMS. It cannot be undone.', 'ninja-forms' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Disable Admin Notices
    |--------------------------------------------------------------------------
    */

    'disable_admin_notices' => array(
        'id'    => 'disable_admin_notices',
        'type'  => 'checkbox',
        'label' => __( 'Disable Admin Notices', 'ninja-forms' ),
        'desc'  => __( 'Never see an admin notice on the dashboard from Ninja Forms. Uncheck to see them again.', 'ninja-forms' ),
    ),

    /*
     |--------------------------------------------------------------------------
     | Tracking Opt-in
     |--------------------------------------------------------------------------
     */

    'allow_tracking' => array(
        'id'    => 'allow_tracking',
        'type'  => 'html',
        'html'  => '<span id="nfTelOptin" class="button hidden">' . __( 'Opt-in', 'ninja-forms' ) . '</span><span id="nfTelOptout" class="button hidden">' . __( 'Opt-out', 'ninja-forms' ) . '</span><span id="nfTelSpinner" class="nf-loading-spinner" style="display:none;"></span>',
        'label' => __( 'Allow Telemetry', 'ninja-forms' ),
        'desc'  => __( 'Opt-in to allow Ninja Forms to collect anonymous usage statistics from your site, such as PHP version, installed plugins, and other non-personally idetifiable informations.', 'ninja-forms' ),
    ),

//    'allow_tracking' => array(
//        'id'    => 'allow_tracking',
//        'type'  => 'checkbox',
//        'label' => __( 'Allow Tracking', 'ninja-forms' ),
//        'desc'  => __( 'If you opt-in, some data about your installation of Ninja Forms will be sent to NinjaForms.com (this does NOT include your submissions).', 'ninja-forms' )
//    ),

    /*
    |--------------------------------------------------------------------------
    | Opinionated Styles
    |--------------------------------------------------------------------------
    */

    'opinionated_styles' => array(
        'id'    => 'opinionated_styles',
        'type'  => 'select',
        'label' => __( 'Opinionated Styles', 'ninja-forms' ),
        'options' => array(
            array(
                'label' => __( 'None', 'ninja-forms' ),
                'value' => '',
            ),
            array(
                'label' => __( 'Light', 'ninja-forms' ),
                'value' => 'light',
            ),
            array(
                'label' => __( 'Dark', 'ninja-forms' ),
                'value' => 'dark',
            ),
        ),
        'desc'  => __( 'Use default Ninja Forms styling conventions.', 'ninja-forms' ),
        'value' => ''
    ),

    /*
    |--------------------------------------------------------------------------
    | Rollback to v2.9.x
    |--------------------------------------------------------------------------
    */

    'downgrade' => array(
        'id'    => 'downgrade',
        'type'  => 'html',
        'html'  => '<div id="nfDowngrade" class="button">' . __( 'Downgrade', 'ninja-forms' ) . '</div>',
        'label' => __( 'Downgrade to v2.9.x', 'ninja-forms' ),
        'desc'  => __( 'Downgrade to the most recent 2.9.x release.', 'ninja-forms' ) . '<br /><div style="color: red">' . __( 'IMPORTANT: All 3.0 data will be removed.', 'ninja-forms' ) . '<br />' . __( 'Please export any forms or submissions you do not want to be lost during this process.', 'ninja-forms' ) . '</div>',
    ),

    'trash_expired_submissions' => array(
        'id' => 'trash_expired_submissions',
        'type' => 'html',
        'html' => '<div id="nfTrashExpiredSubmissions" class="button">' . __( 'Move To Trash', 'ninja-forms' ) . '</div>',
        'label' => __( 'Trash Expired Submissions', 'ninja-forms' ),
        'desc' => __( 'This setting maybe helpful if your WordPress installation is not moving expired submissions to the trash properly.', 'ninja-forms' ),
    ),

));
