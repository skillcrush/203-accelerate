<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MergeTags_WordPress
 */
final class NF_MergeTags_WP extends NF_Abstracts_MergeTags
{
    protected $id = 'wp';

    public function __construct()
    {
        parent::__construct();
        $this->title = __( 'WordPress', 'ninja-forms' );
        $this->merge_tags = Ninja_Forms()->config( 'MergeTagsWP' );
    }

    /**
     * Custom replace() method for custom post meta or user meta.
     * @param string|array $subject
     * @return string
     */
    public function replace( $subject )
    {
        // Recursively replace merge tags.

        if( is_array( $subject ) ){
            foreach( $subject as $i => $s ){
                $subject[ $i ] = $this->replace( $s );
            }
            return $subject;
        }

        /**
         * Replace Custom Post Meta
         * {post_meta:foo} --> meta key is 'foo'
         */
        preg_match_all( "/{post_meta:(.*?)}/", $subject, $post_meta_matches );
        if( ! empty( $post_meta_matches[0] ) ) {
            /**
             * $matches[0][$i]  merge tag match     {post_meta:foo}
             * $matches[1][$i]  captured meta key   foo
             */
            foreach( $post_meta_matches[0] as $i => $search ) {
                $meta_key   = $post_meta_matches[1][$i];
                $meta_value = get_post_meta( $this->post_id(), $meta_key, true  );

                if ( '' != $meta_value ) {
                    $subject = str_replace( $search, $meta_value, $subject );
                } else {
                    $subject = str_replace( $search, '', $subject );
                }
            }
        }
        /**
         * Replace Custom User Meta
         * {user_meta:foo} --> meta key is 'foo'
         */
        $user_id = get_current_user_id();
        preg_match_all( "/{user_meta:(.*?)}/", $subject, $user_meta_matches );
        // if user is logged in and we have user_meta merge tags
        if( ! empty( $user_meta_matches[0] ) && $user_id != 0  ) {
            /**
             * $matches[0][$i]  merge tag match     {user_meta:foo}
             * $matches[1][$i]  captured meta key   foo
             */
            foreach( $user_meta_matches[0] as $i => $search ) {
                $meta_key = $user_meta_matches[1][$i];
                $meta_value = get_user_meta( $user_id, $meta_key, /* $single */ true );
                $subject = str_replace( $search, $meta_value, $subject );
            }
        // if a user is not logged in, but there are user_meta merge tags
        } elseif ( ! empty( $user_meta_matches[0] ) && $user_id == 0 ) {
        	$subject = '';
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
