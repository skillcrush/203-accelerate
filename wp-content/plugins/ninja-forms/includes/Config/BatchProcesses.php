<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_batch_processes', array(
    'chunked_publish' => array(
        'class_name' => 'NF_Admin_Processes_ChunkPublish',
    ),
    'data_cleanup' => array(
        'class_name' => 'NF_Admin_Processes_DataCleanup',
    ),
    'expired_submission_cleanup' => array(
        'class_name' => 'NF_Admin_Processes_ExpiredSubmissionCleanup',
    ),
    'import_form' => array(
        'class_name' => 'NF_Admin_Processes_ImportForm',
    ),
    'import_form_template' => array(
        'class_name' => 'NF_Admin_Processes_ImportFormTemplate',
    ),
));