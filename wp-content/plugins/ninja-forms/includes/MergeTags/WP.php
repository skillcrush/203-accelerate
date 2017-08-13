<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MergeTags_WordPress
 */
final class NF_MergeTags_WP extends NF_Abstracts_MergeTags
{
    protected $id = 'wp';

    /**
     * @var array
     * $post_meta[ $meta_key ] = $meta_value;
     */
    protected $post_meta = array();

    public function __construct()
    {
        parent::__construct();
        $this->title = __( 'WordPress', 'ninja-forms' );
        $this->merge_tags = Ninja_Forms()->config( 'MergeTagsWP' );

        // Setup merge tag data for each post in The Loop.
        add_action( 'the_post', array( $this, 'init' ) );

        // Setup merge tag data when Doing AJAX.
        add_action( 'admin_init', array( $this, 'init' ) );
    }

    public function init()
    {
        global $post;

        $this->setup_post_meta( $this->post_id() );
    }

    public function replace( $subject )
    {

        /*
         * If we are dealing with a post meta merge tag, we need to overwrite the parent replace() method.
         *
         * Otherwise, we use the parent's method.
         */

        /**
         * {post_meta:foo} --> meta key is 'foo'
         */
        if (is_string($subject)) {
            preg_match_all("/{post_meta:(.*?)}/", $subject, $matches );
        }

        // If not matching merge tags are found, then return early.
        if( empty( $matches[0] ) ) return parent::replace( $subject );


        // Recursively replace merge tags.
        if( is_array( $subject ) ){
            foreach( $subject as $i => $s ){
                $subject[ $i ] = $this->replace( $s );
            }
            return $subject;
        }

        /**
         * $matches[0][$i]  merge tag match     {post_meta:foo}
         * $matches[1][$i]  captured meta key   foo
         */
        foreach( $matches[0] as $i => $search ){
            $meta_key = $matches[ 1 ][ $i ];
            if( ! isset( $this->post_meta[ $meta_key ] ) ) continue;
            $subject = str_replace( $search, $this->post_meta[ $meta_key ], $subject );
        }

      return parent::replace( $subject );
    }

    protected function post_id()
    {
        global $post;

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            // If we are doing AJAX, use the referer to get the Post ID.
            $post_id = url_to_postid( wp_get_referer() );
        } elseif( $post ) {
            $post_id = $post->ID;
        } else {
            return false; // No Post ID found.
        }

        return $post_id;
    }

    protected function post_title()
    {
        $post_id = $this->post_id();
        if( ! $post_id ) return;
        $post = get_post( $post_id );
        return ( $post ) ? $post->post_title : '';
    }

    protected function post_url()
    {
        $post_id = $this->post_id();
        if( ! $post_id ) return;
        $post = get_post( $post_id );
        return ( $post ) ? get_permalink( $post->ID ) : '';
    }

    protected function post_author()
    {
        $post_id = $this->post_id();
        if( ! $post_id ) return;
        $post = get_post( $post_id );
        if( ! $post ) return '';
        $author = get_user_by( 'id', $post->post_author);
        return $author->display_name;
    }

    protected function post_author_email()
    {
        $post_id = $this->post_id();
        if( ! $post_id ) return;
        $post = get_post( $post_id );
        if( ! $post ) return '';
        $author = get_user_by( 'id', $post->post_author );
        return $author->user_email;
    }

    public function setup_post_meta( $post_id )
    {
        global $wpdb;

        // Get ALL post meta for a given Post ID.
        $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT `meta_key`, `meta_value`
            FROM {$wpdb->postmeta}
            WHERE `post_id` = %d
        ", $post_id ) );

        foreach( $results as $result ){
            $this->post_meta[ $result->meta_key ] = $result->meta_value;
        }
    }

    protected function user_id()
    {
        $current_user = wp_get_current_user();

        return ( $current_user ) ? $current_user->ID : '';
    }

    protected function user_first_name()
    {
        $current_user = wp_get_current_user();

        return ( $current_user ) ? $current_user->user_firstname : '';
    }

    protected function user_last_name()
    {
        $current_user = wp_get_current_user();

        return ( $current_user ) ? $current_user->user_lastname : '';
    }

    protected function user_display_name()
    {
        $current_user = wp_get_current_user();

        return ( $current_user ) ? $current_user->display_name : '';
    }

    protected function user_email()
    {
        $current_user = wp_get_current_user();

        return ( $current_user ) ? $current_user->user_email : '';
    }

    protected function user_url()
    {
        $current_user = wp_get_current_user();

        return ( $current_user ) ? $current_user->user_url : '';
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
