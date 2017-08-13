<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_AJAX_REST_NewFormTemplates extends NF_AJAX_REST_Controller
{
    protected $action = 'nf_new_form_templates';

    /**
     * GET new-form-templates/
     * @return array [ $new_form_templates ]
     */
    public function get()
    {
        $templates = Ninja_Forms()->config( 'NewFormTemplates' );
        array_unshift( $templates, array(
            'id' => 'new',
            'title' => __( 'Blank Form', 'ninja-forms' ),
            'template-desc' => __( 'The blank form allows you to create any type of form using our drag & drop builder.' )
        ) );
        return array_values( $templates ); // Remove keys so that the JSON is an array.
    }

}
