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
        // Does the current user have admin privileges
        if (!current_user_can('manage_options')) {
            return ['error' => esc_html__('Access denied. You must have admin privileges to view this data.', 'ninja-forms')];
        }

        // If we don't have a nonce...
        // OR if the nonce is invalid...
        if (!isset($_REQUEST['security']) || !wp_verify_nonce($_REQUEST['security'], 'ninja_forms_dashboard_nonce')) {
            // Kick the request out now.
            $data['error'] = esc_html__('Request forbidden.', 'ninja-forms');
            return $data;
        }

        $templates = Ninja_Forms()->config( 'NewFormTemplates' );
        usort( $templates, array( $this, 'cmp' ) );
        array_unshift( $templates, array(
            'id'            => 'new',
            'title'         => esc_html__( 'Blank Form', 'ninja-forms' ),
            'template-desc' => esc_html__( 'The blank form allows you to create any type of form using our drag & drop builder.', 'ninja-forms' ),
            'type'          => 'default'
        ) );
        return array_values( $templates ); // Remove keys so that the JSON is an array.
    }

    /**
     * Comparison function used to sort templates alphabetically by title
     * @since  3.2.22
     * @param  array   $a item being compared
     * @param  array   $b item being compared
     * @return int
     */
    private function cmp( $a, $b )
    {
        return strcmp( $a[ 'title' ], $b[ 'title' ] );
    }

}
