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
                   esc_html__(	'Delete All Data', 'ninja-forms' ) . '</button>',
        'label' => esc_html__( 'Remove ALL Ninja Forms data upon uninstall?', 'ninja-forms' ),
        'desc'  => sprintf( esc_html__( 'ALL Ninja Forms data will be removed from the database and the Ninja Forms plug-in will be deactivated. %sAll form and submission data will be unrecoverable.%s', 'ninja-forms' ), '<span class="nf-nuke-warning">', '</span>' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Delete Prompt for Delete on Uninstall
    |--------------------------------------------------------------------------
    */

    'delete_prompt' => array(
        'id'    => 'delete_prompt',
        'type'  => 'prompt',
        'desc'  => esc_html__( 'This setting will COMPLETELY remove anything Ninja Forms related upon plugin deletion. This includes SUBMISSIONS and FORMS. It cannot be undone.', 'ninja-forms' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Disable Admin Notices
    |--------------------------------------------------------------------------
    */

    'disable_admin_notices' => array(
        'id'    => 'disable_admin_notices',
        'type'  => 'checkbox',
        'label' => esc_html__( 'Disable Admin Notices', 'ninja-forms' ),
        'desc'  => esc_html__( 'Never see an admin notice on the dashboard from Ninja Forms. Uncheck to see them again.', 'ninja-forms' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | "Dev Mode"
    |--------------------------------------------------------------------------
    */

    'builder_dev_mode' => array(
        'id'    => 'builder_dev_mode',
        'type'  => 'checkbox',
        'label' => esc_html__( 'Form Builder "Dev Mode"', 'ninja-forms' ),
    ),

    /*
     |--------------------------------------------------------------------------
     | Tracking Opt-in
     |--------------------------------------------------------------------------
     */

    'allow_tracking' => array(
        'id'    => 'allow_tracking',
        'type'  => 'html',
        'html'  => '<span id="nfTelOptin" class="button hidden">' . esc_html__( 'Opt-in', 'ninja-forms' ) . '</span><span id="nfTelOptout" class="button hidden">' . esc_html__( 'Opt-out', 'ninja-forms' ) . '</span><span id="nfTelSpinner" class="nf-loading-spinner" style="display:none;"></span>',
        'label' => esc_html__( 'Allow Telemetry', 'ninja-forms' ),
        'desc'  => esc_html__( 'Opt-in to allow Ninja Forms to collect anonymous usage statistics from your site, such as PHP version, installed plugins, and other non-personally idetifiable informations.', 'ninja-forms' ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Opinionated Styles
    |--------------------------------------------------------------------------
    */

    'opinionated_styles' => array(
        'id'    => 'opinionated_styles',
        'type'  => 'select',
        'label' => esc_html__( 'Opinionated Styles', 'ninja-forms' ),
        'options' => array(
            array(
                'label' => esc_html__( 'None', 'ninja-forms' ),
                'value' => '',
            ),
            array(
                'label' => esc_html__( 'Light', 'ninja-forms' ),
                'value' => 'light',
            ),
            array(
                'label' => esc_html__( 'Dark', 'ninja-forms' ),
                'value' => 'dark',
            ),
        ),
        'desc'  => esc_html__( 'Use default Ninja Forms styling conventions.', 'ninja-forms' ),
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
        'html'  => '<div id="nfDowngrade" class="button">' . esc_html__( 'Downgrade', 'ninja-forms' ) . '</div>',
        'label' => esc_html__( 'Downgrade to v2.9.x', 'ninja-forms' ),
        'desc'  => esc_html__( 'Downgrade to the most recent 2.9.x release.', 'ninja-forms' ) . '<br /><div style="color: red">' . esc_html__( 'IMPORTANT: All 3.0 data will be removed.', 'ninja-forms' ) . '<br />' . esc_html__( 'Please export any forms or submissions you do not want to be lost during this process.', 'ninja-forms' ) . '</div>',
    ),

    'trash_expired_submissions' => array(
        'id' => 'trash_expired_submissions',
        'type' => 'html',
        'html' => '<div id="nfTrashExpiredSubmissions" class="button">' . esc_html__( 'Move To Trash', 'ninja-forms' ) . '</div>',
        'label' => esc_html__( 'Trash Expired Submissions', 'ninja-forms' ),
        'desc' => esc_html__( 'This setting maybe helpful if your WordPress installation is not moving expired submissions to the trash properly.', 'ninja-forms' ),
    ),

    // Add a button for removing all forms from maintenance
    'remove_maintenance_mode' => array(
        'id' => 'remove_maintenance_mode',
        'type' => 'html',
        'html' => '<div id="nfRemoveMaintenanceMode" class="button">' . esc_html__( 'Remove Maintenance Mode', 'ninja-forms' ) . '</div><span id="nf_maintenanceModeProgress" style="display:none;margin-left:15px;"></span>',
        'label' => esc_html__( 'Remove Maintenance Mode', 'ninja-forms' ),
        'desc' => esc_html__( 'Click this button if any of your forms are still in \'Maintenance Mode\' after performing any required updates.' , 'ninja-forms' ),
    ),

));
