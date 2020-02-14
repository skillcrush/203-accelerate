<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_Admin_Menus_AddNew extends NF_Abstracts_Submenu
{
    public $parent_slug = 'ninja-forms';

    public $page_title = 'Add New';

    public $menu_slug = 'admin.php?page=ninja-forms#new-form';

    public $priority = 2;

    public function __construct()
    {
        parent::__construct();

        add_action( 'admin_init', array( $this, 'nf_upgrade_redirect' ) );
    }

     /**
     * If we have required updates, unregister the menu item
     */
    public function nf_upgrade_redirect() {
        global $pagenow;
            
        if( "1" == get_option( 'ninja_forms_needs_updates' ) ) {
            remove_submenu_page( $this->parent_slug, $this->menu_slug );
        }
    }

    public function get_page_title()
    {
        return esc_html__( 'Add New', 'ninja-forms' );
    }

    public function get_capability()
    {
        return apply_filters( 'ninja_forms_admin_add_new_capabilities', $this->capability );
    }

    public function display()
    {
        // This section intentionally left blank.
    }

} // End Class NF_Admin_Settings
