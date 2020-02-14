<?php if ( ! defined( 'ABSPATH' ) ) exit;

abstract class NF_Display_Page
{
	public function __construct()
	{
		$this->_wp_query = $GLOBALS['wp_query'];
		$this->modify_wp_query();
	}

	/**
	 * @return string HTML
	 */
	public function get_content()
	{
		ob_start();
		// ...
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * @return string
	 */
	public abstract function get_title();

	/**
	 * @return string
	 */
	public abstract function get_guid();

	/**
	 * Modifies the WP Query to our liking
	 *
	 * @return void
	*/
	public function modify_wp_query()
	{
		$this->_wp_query->posts_per_page = 1;
		$this->_wp_query->nopaging = true;
		$this->_wp_query->post_count = 1;

		// If we don't have a post, load an empty one
		if ( empty( $this->_wp_query->post ) )
			$this->_wp_query->post = new WP_Post( new \stdClass() );

		$this->_wp_query->post->ID = 0;
		$this->_wp_query->post->post_date = current_time( 'mysql' );
		$this->_wp_query->post->post_date_gmt = current_time( 'mysql', 1 );
		$this->_wp_query->post->post_content = $this->get_content();
		$this->_wp_query->post->post_title = $this->get_title();
		$this->_wp_query->post->post_excerpt = '';
		$this->_wp_query->post->post_status = 'publish';
		$this->_wp_query->post->comment_status = false;
		$this->_wp_query->post->ping_status = false;
		$this->_wp_query->post->post_password = '';
		$this->_wp_query->post->post_name = $this->get_guid();
		$this->_wp_query->post->to_ping = '';
		$this->_wp_query->post->pinged = '';
		$this->_wp_query->post->post_modified = $this->_wp_query->post->post_date;
		$this->_wp_query->post->post_modified_gmt = $this->_wp_query->post->post_date_gmt;
		$this->_wp_query->post->post_content_filtered = '';
		$this->_wp_query->post->post_parent = 0;
		$this->_wp_query->post->guid = get_home_url() . '/' . $this->get_guid();
		$this->_wp_query->post->menu_order = 0;
		$this->_wp_query->post->post_type = 'page';
		$this->_wp_query->post->post_mime_type = '';
		$this->_wp_query->post->comment_count = 0;
		$this->_wp_query->post->filter = 'raw';

		$this->_wp_query->posts = array( $this->_wp_query->post );
		$this->_wp_query->found_posts = 1;
		$this->_wp_query->is_single = false; //false -- so comments_template() doesn't add comments
		$this->_wp_query->is_preview = false;
		$this->_wp_query->is_page = false; //false -- so comments_template() doesn't add comments
		$this->_wp_query->is_archive = false;
		$this->_wp_query->is_date = false;
		$this->_wp_query->is_year = false;
		$this->_wp_query->is_month = false;
		$this->_wp_query->is_day = false;
		$this->_wp_query->is_time = false;
		$this->_wp_query->is_author = false;
		$this->_wp_query->is_category = false;
		$this->_wp_query->is_tag = false;
		$this->_wp_query->is_tax = false;
		$this->_wp_query->is_search = false;
		$this->_wp_query->is_feed = false;
		$this->_wp_query->is_comment_feed = false;
		$this->_wp_query->is_trackback = false;
		$this->_wp_query->is_home = false;
		$this->_wp_query->is_404 = false;
		$this->_wp_query->is_comments_popup = false;
		$this->_wp_query->is_paged = false;
		$this->_wp_query->is_admin = false;
		$this->_wp_query->is_attachment = false;
		$this->_wp_query->is_singular = false;
		$this->_wp_query->is_posts_page = false;
		$this->_wp_query->is_post_type_archive = false;

		$GLOBALS['wp_query'] = $this->_wp_query;
	}
}
