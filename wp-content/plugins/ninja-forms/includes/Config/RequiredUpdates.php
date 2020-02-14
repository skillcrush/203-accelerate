<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_required_updates', array(

    'CacheCollateActions' => array(
        'class_name' => 'NF_Updates_CacheCollateActions',
        'requires' => array( 'CacheCollateForms' ),
        'nicename' => esc_html__( 'Update Actions Tables', 'ninja-forms' ),
    ),
    'CacheCollateForms' => array(
        'class_name' => 'NF_Updates_CacheCollateForms',
        'requires' => array(),
        'nicename' => esc_html__( 'Update Forms Tables', 'ninja-forms' ),
    ),
    'CacheCollateFields' => array(
        'class_name' => 'NF_Updates_CacheCollateFields',
        'requires' => array( 'CacheCollateActions' ),
        'nicename' => esc_html__( 'Update Fields Tables', 'ninja-forms' ),
    ),
    'CacheCollateObjects' => array(
        'class_name' => 'NF_Updates_CacheCollateObjects',
        'requires' => array( 'CacheCollateFields' ),
        'nicename' => esc_html__( 'Update Objects Tables', 'ninja-forms' ),
    ),
    'CacheCollateCleanup' => array(
        'class_name' => 'NF_Updates_CacheCollateCleanup',
        'requires' => array( 'CacheCollateObjects' ),
        'nicename' => esc_html__( 'Cleanup Orphan Records', 'ninja-forms' ),
    ),
    'CacheFieldReconcilliation' => array(
        'class_name' => 'NF_Updates_CacheFieldReconcilliation',
        'requires' => array( 'CacheCollateCleanup' ),
        'nicename' => esc_html__( 'Field Meta Cleanup.', 'ninja-forms' ),
    ),

));
