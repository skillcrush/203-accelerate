<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MergeTags_System
 */
final class NF_MergeTags_System extends NF_Abstracts_MergeTags
{
    protected $id = 'system';

    public function __construct()
    {
        parent::__construct();
        $this->title = esc_html__( 'System', 'ninja-forms' );
        $this->merge_tags = Ninja_Forms()->config( 'MergeTagsSystem' );
    }

    protected function admin_email()
    {
        return get_option( 'admin_email' );
    }

    protected function site_title()
    {
        return get_bloginfo( 'name' );
    }

    protected function site_url()
    {
        return get_bloginfo( 'url' );
    }

} // END CLASS NF_MergeTags_System
