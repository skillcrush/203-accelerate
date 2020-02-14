<?php if ( ! defined( 'ABSPATH' ) ) exit;

// Ajax call handled just below this add_action call
add_action( 'wp_ajax_ninja_forms_ajax_migrate_database', 'ninja_forms_ajax_migrate_database' );
function ninja_forms_ajax_migrate_database(){
    if( ! current_user_can( apply_filters( 'ninja_forms_admin_upgrade_migrate_database_capabilities', 'manage_options' ) ) ) return;
    if ( ! isset( $_POST[ 'security' ] ) ) return;
    if ( ! wp_verify_nonce( $_POST[ 'security' ], 'ninja_forms_upgrade_nonce' ) ) return;
    $migrations = new NF_Database_Migrations();
    
    $sure = true;
    $really_sure = true;
    $nuke_multisite = false;
    $migrations->nuke( $sure, $really_sure, $nuke_multisite );
    $migrations->migrate();
    // Reset our required updates.
    delete_option( 'ninja_forms_required_updates' );
    // Prevent recent 2.9x conversions from running required updates within a week.
    set_transient( 'ninja_forms_prevent_updates', 'true', WEEK_IN_SECONDS );
    echo json_encode( array( 'migrate' => 'true' ) );
    wp_die();
}

// Ajax call handled just below this 'add_action' call
add_action( 'wp_ajax_ninja_forms_ajax_import_form', 'ninja_forms_ajax_import_form' );
function ninja_forms_ajax_import_form(){
    if( ! current_user_can( apply_filters( 'ninja_forms_admin_upgrade_import_form_capabilities', 'manage_options' ) ) ) return;

    $import = stripslashes( $_POST[ 'import' ] );

    $form_id = ( isset( $_POST[ 'formID' ] ) ) ? absint( $_POST[ 'formID' ] ) : '';

    WPN_Helper::delete_nf_cache( $form_id ); // Bust the cache.

    Ninja_Forms()->form()->import_form( $import, TRUE, $form_id, TRUE );

    if( isset( $_POST[ 'flagged' ] ) && $_POST[ 'flagged' ] ){
        $form = Ninja_Forms()->form( $form_id )->get();
        $form->update_setting( 'lock', TRUE );
        $form->save();
    }

    echo json_encode( array( 'export' => WPN_Helper::esc_html($_POST['import']), 'import' => $import ) );
    wp_die();
}

// Ajax call handled just below this 'add_action' call
add_action( 'wp_ajax_ninja_forms_ajax_import_fields', 'ninja_forms_ajax_import_fields' );
function ninja_forms_ajax_import_fields(){
    if( ! current_user_can( apply_filters( 'ninja_forms_admin_upgrade_import_fields_capabilities', 'manage_options' ) ) ) return;
    $fields = stripslashes( WPN_Helper::esc_html($_POST[ 'fields' ]) ); // TODO: How to sanitize serialized string?
    $fields = maybe_unserialize( $fields );

    foreach( $fields as $field ) {
        Ninja_Forms()->form()->import_field( $field, $field[ 'id' ], TRUE );
    }

    echo json_encode( array( 'export' => WPN_Helper::esc_html($_POST['fields']), 'import' => $fields ) );
    wp_die();
}