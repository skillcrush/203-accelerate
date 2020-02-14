<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_Admin_Menus_Dashboard extends NF_Abstracts_Submenu
{
    public $parent_slug = 'ninja-forms';

    public $page_title = 'Dashboard';

    public $menu_slug = 'ninja-forms';

    public $priority = 1;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_page_title()
    {
        if( isset( $_GET[ 'form_id' ] ) ) {
            return esc_html__( 'Form Builder', 'ninja-forms' );
        }
        return esc_html__( 'Dashboard', 'ninja-forms' );
    }

    public function get_capability()
    {
        return apply_filters( 'ninja_forms_admin_all_forms_capabilities', $this->capability );
    }

    public function display()
    {
        // This section intentionally left blank.
    }
} // End Class NF_Admin_Settings
