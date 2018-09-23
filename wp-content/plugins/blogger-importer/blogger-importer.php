<?php
/*
Plugin Name: Blogger Importer
Plugin URI: http://wordpress.org/extend/plugins/blogger-importer/
Description: Import posts, comments, and categories from a Blogger blog and migrate authors to WordPress users.
Author: wordpressdotorg
Author URI: http://wordpress.org/
Version: 0.9
License: GPLv2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Text Domain: blogger-importer
Domain Path: /languages
*/

if ( ! defined( 'WP_LOAD_IMPORTERS' ) )
	return;

/** Display verbose errors */
define( 'IMPORT_DEBUG', false );

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

require_once ABSPATH . WPINC . '/class-feed.php';

// Custom classes used by importer
require_once dirname( __FILE__ ) . '/blogger-importer-sanitize.php';
require_once dirname( __FILE__ ) . '/blogger-importer-blogitem.php';
require_once dirname( __FILE__ ) . '/blogger-entry.php';
require_once dirname( __FILE__ ) . '/comment-entry.php';


if ( ! class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require $class_wp_importer;
}

/**
 * Blogger Importer class for managing the import process of a XML file
 *
 */
if ( !class_exists( 'Blogger_Importer' ) ) {
class Blogger_Importer extends WP_Importer {
	const IMPORT_IMG = true;         // Should we import the images (boolean)
	const LARGE_IMAGE_SIZE = '1024'; // The size of large images downloaded (string)
	const POST_PINGBACK = 0;         // Turn off the post pingback, set to 1 to re-enabled(bool)

	var $id; // XML attachment ID

	var $authors = array();

	// mappings from old information to new
	var $processed_authors = array();
	var $author_mapping = array();

	function Blogger_Importer() { /* nothing */ }

	/**
	 * Registered callback function for the Blogger Importer
	 *
	 * Manages the three separate stages of the XML import process
	 */
	function dispatch() {
		$this->header();

		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
		switch ( $step ) {
			case 0:
				$this->greet();
				break;
			case 1:
				check_admin_referer( 'import-upload' );
				if ( $this->handle_upload() )
					$this->import_options();
				break;
			case 2:
				check_admin_referer( 'import-blogger' );
				$this->id = (int) $_POST['import_id'];
				$file = get_attached_file( $this->id );
				set_time_limit(0);
				$this->import( $file );
				break;
		}

		$this->footer();
	}

	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the XML file for importing
	 */
	function import( $file ) {
		add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

		$this->import_start( $file );

		$this->get_author_mapping();

		wp_suspend_cache_invalidation( true );
		$this->process_posts();
		$this->process_comments();

		if (Blogger_Importer::IMPORT_IMG)
		{
			$this->process_images();
		}

		$this->process_links();

		wp_suspend_cache_invalidation( false );

		// update incorrect/missing information in the DB
		//$this->backfill_parents();
		//$this->backfill_attachment_urls();
		//$this->remap_featured_images();

		$this->import_end();
	}

	/**
	 * Parses the XML file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the XML file for importing
	 */
	function import_start( $file ) {
		if ( ! is_file($file) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'blogger-importer' ) . '</strong><br />';
			echo __( 'The file does not exist, please try again.', 'blogger-importer' ) . '</p>';
			$this->footer();
			die();
		}

		$import_data = $this->parse( $file );

		if ( is_wp_error( $import_data ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'blogger-importer' ) . '</strong><br />';
			echo esc_html( $import_data->get_error_message() ) . '</p>';
			$this->footer();
			die();
		}

		$this->import_data = $import_data;
		
		// <link rel='alternate' type='text/html' href='http://example.blogspot.com/'/>
		$links = $import_data->get_links('alternate');
		$this->host = parse_url($links[0], PHP_URL_HOST);
		
		$this->images_progress = 0;
		$this->images_skipped = 0;
		$this->links_done = 0;
		$this->links_progress = 0;

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		do_action( 'import_start' );
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	function import_end() {
		wp_import_cleanup( $this->id );

		wp_cache_flush();
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		echo '<p>' . __( 'All done.', 'blogger-importer' ) . ' <a href="' . admin_url() . '">' . __( 'Have fun!', 'blogger-importer' ) . '</a>' . '</p>';
		echo '<p>' . __( 'Remember to update the passwords and roles of imported users.', 'blogger-importer' ) . '</p>';

		do_action( 'import_end' );
	}

	/**
	 * Handles the WXR upload and initial parsing of the file to prepare for
	 * displaying author import options
	 *
	 * @return bool False if error uploading or invalid file, true otherwise
	 */
	function handle_upload() {
		$file = wp_import_handle_upload();

		if ( isset( $file['error'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'blogger-importer' ) . '</strong><br />';
			echo esc_html( $file['error'] ) . '</p>';
			return false;
		} else if ( ! file_exists( $file['file'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'blogger-importer' ) . '</strong><br />';
			printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'blogger-importer' ), esc_html( $file['file'] ) );
			echo '</p>';
			return false;
		}

		$this->id = (int) $file['id'];
		$import_data = $file['file'];
		
		if ( is_wp_error( $import_data ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'blogger-importer' ) . '</strong><br />';
			echo esc_html( $import_data->get_error_message() ) . '</p>';
			return false;
		}

		$this->get_authors_from_import( $import_data );

		return true;
	}

	/**
	 * Retrieve authors from parsed data
	 *
	 * @param array $import_data Data returned by a WXR parser
	 */
	function get_authors_from_import( $import_data ) {
		
		$feed = $this->parse($import_data);
		
		$authors = $feed->get_authors();

		foreach ($authors as $author) {
			$login = sanitize_user( $author->get_name(), true );
			$this->authors[$login] = array(
				'author_login' => $author->get_name(),
				'author_display_name' => $author->get_name(),
				'author_email' => $author->get_email(),
			);
		}
	}

	/**
	 * Display pre-import options, author importing/mapping and option to
	 * fetch attachments
	 */
	function import_options() {
		$j = 0;
?>
<form action="<?php echo admin_url( 'admin.php?import=blogger&amp;step=2' ); ?>" method="post">
	<?php wp_nonce_field( 'import-blogger' ); ?>
	<input type="hidden" name="import_id" value="<?php echo $this->id; ?>" />

<?php if ( ! empty( $this->authors ) ) : ?>
	<h3><?php _e( 'Assign Authors', 'blogger-importer' ); ?></h3>
	<p><?php _e( 'To make it easier for you to edit and save the imported content, you may want to reassign the author of the imported item to an existing user of this site. For example, you may want to import all the entries as <code>admin</code>s entries.', 'blogger-importer' ); ?></p>
<?php if ( $this->allow_create_users() ) : ?>
	<p><?php printf( __( 'If a new user is created by WordPress, a new password will be randomly generated and the new user&#8217;s role will be set as %s. Manually changing the new user&#8217;s details will be necessary.', 'blogger-importer' ), esc_html( get_option('default_role') ) ); ?></p>
<?php endif; ?>
	<ol id="authors">
<?php foreach ( $this->authors as $author ) : ?>
		<li><?php $this->author_select( $j++, $author ); ?></li>
<?php endforeach; ?>
	</ol>
<?php endif; ?>

	<p class="submit"><input type="submit" class="button" value="<?php esc_attr_e( 'Submit', 'blogger-importer' ); ?>" /></p>
</form>
<?php
	}

	/**
	 * Display import options for an individual author. That is, either create
	 * a new user based on import info or map to an existing user
	 *
	 * @param int $n Index for each author in the form
	 * @param array $author Author information, e.g. login, display name, email
	 */
	function author_select( $n, $author ) {
		_e( 'Import author:', 'blogger-importer' );
		echo ' <strong>' . esc_html( $author['author_display_name'] );
		echo '</strong><br />';

		$create_users = $this->allow_create_users();
		if ( $create_users ) {
			_e( 'as a new user:', 'blogger-importer' );
			$value = esc_attr( sanitize_user( $author['author_login'], true ) );

			echo ' <input type="text" name="user_new['.$n.']" value="'. $value .'" /><br />';
		}

		_e( 'or assign posts to an existing user:', 'blogger-importer' );
		wp_dropdown_users( array( 'name' => "user_map[$n]", 'multi' => true, 'show_option_all' => __( '- Select -', 'blogger-importer' ) ) );
		echo '<input type="hidden" name="imported_authors['.$n.']" value="' . esc_attr( $author['author_login'] ) . '" />';
	}

	/**
	 * Map old author logins to local user IDs based on decisions made
	 * in import options form. Can map to an existing user, create a new user
	 * or falls back to the current user in case of error with either of the previous
	 */
	function get_author_mapping() {
		if ( ! isset( $_POST['imported_authors'] ) )
			return;

		$create_users = $this->allow_create_users();

		foreach ( (array) $_POST['imported_authors'] as $i => $old_login ) {
			// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
			$santized_old_login = sanitize_user( $old_login, true );
			$old_id = isset( $this->authors[$old_login]['author_id'] ) ? intval($this->authors[$old_login]['author_id']) : false;

			if ( ! empty( $_POST['user_map'][$i] ) ) {
				$user = get_userdata( intval($_POST['user_map'][$i]) );
				if ( isset( $user->ID ) ) {
					if ( $old_id )
						$this->processed_authors[$old_id] = $user->ID;
					$this->author_mapping[$santized_old_login] = $user->ID;
				}
			} else if ( $create_users ) {
				if ( ! empty($_POST['user_new'][$i]) ) {
					$user_id = wp_create_user( $_POST['user_new'][$i], wp_generate_password() );
				} else if ( $this->version != '1.0' ) {
					$user_data = array(
						'user_login' => $old_login,
						'user_pass' => wp_generate_password(),
						'user_email' => isset( $this->authors[$old_login]['author_email'] ) ? $this->authors[$old_login]['author_email'] : '',
						'display_name' => $this->authors[$old_login]['author_display_name'],
						'first_name' => isset( $this->authors[$old_login]['author_first_name'] ) ? $this->authors[$old_login]['author_first_name'] : '',
						'last_name' => isset( $this->authors[$old_login]['author_last_name'] ) ? $this->authors[$old_login]['author_last_name'] : '',
					);
					$user_id = wp_insert_user( $user_data );
				}

				if ( ! is_wp_error( $user_id ) ) {
					if ( $old_id )
						$this->processed_authors[$old_id] = $user_id;
					$this->author_mapping[$santized_old_login] = $user_id;
				} else {
					printf( __( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'blogger-importer' ), esc_html($this->authors[$old_login]['author_display_name']) );
					if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
						echo ' ' . $user_id->get_error_message();
					echo '<br />';
				}
			}

			// failsafe: if the user_id was invalid, default to the current user
			if ( ! isset( $this->author_mapping[$santized_old_login] ) ) {
				if ( $old_id )
					$this->processed_authors[$old_id] = (int) get_current_user_id();
				$this->author_mapping[$santized_old_login] = (int) get_current_user_id();
			}
		}
	}

	/**
	 * Create new posts based on import information
	 */
	function process_posts() {
		$feed = $this->import_data;
		
		foreach ( $feed->get_items() as $item ) {
			// check that it is actually a post first
			// <category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/blogger/2008/kind#post'/>
			$is_post = false;
			$cats = $item->get_categories();
			foreach ( $cats as $cat ) {
				if ( $cat == 'http://schemas.google.com/blogger/2008/kind#post' ) {
					$is_post = true;
					break;
				}
			}
			
			// only import posts for now
			if ( ! $is_post ) {
				continue;
			}

			$blogentry = new BloggerEntry();

			$blogentry->blogurl = $this->host;
			$blogentry->id = $item->get_id();
			$blogentry->published = $item->get_published();
			$blogentry->updated = $item->get_updated();
			$blogentry->isDraft = $item->get_draft_status();
			$blogentry->title = $item->get_title();
			$blogentry->content = $item->get_content();
			$blogentry->geotags = $item->get_geotags();
			
			// map the post author
			$blogentry->bloggerauthor = sanitize_user( $item->get_author()->get_name(), true );
			if ( isset( $this->author_mapping[$blogentry->bloggerauthor] ) )
				$blogentry->author = $this->author_mapping[$blogentry->bloggerauthor];
			else
				$blogentry->author = (int) get_current_user_id();

			$blogentry->links = $item->get_links(array('replies', 'edit', 'self', 'alternate'));
			$blogentry->parselinks();
			
			foreach ( $cats as $cat ) {
				if ( false === strpos( $cat, 'http://schemas.google.com') ) {
					$blogentry->categories[] = $cat;
				}
			}

			// Checks for duplicates
			$post_id = $blogentry->post_exists();

			if ( $post_id != 0 ) {
				$this->posts_skipped++;
			} else {
				//Unique new post so import it
				$post_id = $blogentry->import();
				$this->posts_done++;
			}
		}                
	}

	/**
	 * Create new comments based on import information
	 */
	function process_comments() {
		$feed = $this->import_data;
		
		foreach ( $feed->get_items() as $item ) {
			// check that it is actually a comment first
			// <category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/blogger/2008/kind#comment'/>
			$is_comment = false;
			$cats = $item->get_categories();
			foreach ( $cats as $cat ) {
				if ( $cat == 'http://schemas.google.com/blogger/2008/kind#comment' ) {
					$is_comment = true;
					break;
				}
			}
			
			// we only import comments here
			if ( ! $is_comment ) {
				continue;
			}
			
			$commententry = new CommentEntry();

			$commententry->id = $item->get_id();
			$commententry->updated = $item->get_updated();
			$commententry->content = $item->get_content();
			$commententry->author = $item->get_author()->get_name();
			$commententry->authoruri = $item->get_author()->get_link();
			$commententry->authoremail = $item->get_author()->get_email();
			
			$replyto = $item->get_item_tags('http://purl.org/syndication/thread/1.0','in-reply-to');
			$commententry->source = $replyto[0]['attribs']['']['source'];
			
			$commententry->source = $item->get_source();
			$parts = parse_url($commententry->source);
			$commententry->old_post_permalink = $parts['path']; //Will be something like this '/feeds/417730729915399755/posts/default/8397846992898424746'

			$bloggerentry = new BloggerEntry();
			$commententry->post_ID = $bloggerentry->get_post_by_oldID($commententry->old_post_permalink);

			//Get the links
			$commententry->links = $item->get_links(array('edit', 'self', 'alternate', 'related'));
			$commententry->parselinks();

			// Nested comment?
			if ( isset($commententry->related) ) {
				$commententry->parentcommentid = $commententry->get_comment_by_oldID($commententry->related);
			}

			//Perhaps could log errors here?
			if ($commententry->post_ID != 0) {
				// Checks for duplicates
				if ($comment_id = $commententry->exists()) {
					$this->comments_skipped++;
				} else {
					$comment_id = $commententry->import();
					$this->comments_done++;
				}
			} else {
				$this->comments_skipped++;
			}
		}                
	}

	/*
	* Search for either a linked image or a non linked image within the supplied html
	* <a href="xxx" yyyy><img src="zzz" ></a> or <img src="zzz" >
	* Ref: http://www.the-art-of-web.com/php/parse-links/
	*        "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>"  
	*      http://wordpress.org/extend/plugins/blogger-image-import/
	*        "<a[^>]+href\=([\"'`])(.*)\\1[^<]*?<img[^>]*src\=([\"'`])(.*)\\3[^>]*>"
	*/
	function get_images($content) {
		$highrez = array();
		$lowrez = array();

		//First images with links
		//Might be nice to expand this top try and get Alt and/or Title attributes for use as description
		$regexp = "<a\s[^>]*href\=([\"'`])([^> ]*?)\\1[^<]*?<img[^>]*src\=([\"'`])([^\> ]*?)\\3[^>]*>";

		if (preg_match_all("/$regexp/siU", $content, $matches1, PREG_SET_ORDER)) {
			//http://www.techrepublic.com/article/17-useful-functions-for-manipulating-arrays-in-php/5792851
			foreach ($matches1 as $match) {
				if ($this->isurlimage($match[2])) {
					$highrez[$match[4]] = $match[2];
				} else {
					$lowrez[$match[4]] = '';
				}
			}
		}

		//Now any image (n.b. this overlaps the previous set)
		$regexp = "<img[^>]*src\=([\"'`])([^\> ]*?)\\1[^>]*>";

		if (preg_match_all("/$regexp/siU", $content, $matches2, PREG_SET_ORDER)) {
			foreach ($matches2 as $match) {
				$lowrez[$match[2]] = '';
			}
		}
		
		//Remove any rows from this second set that are already in the first set and merge two sets of results
		$images = array_merge($lowrez, $highrez);
		return $images;
	}

	/**
	 * Update all of the images in the posts that have already been imported
	 */
	function process_images() {
		$postsprocessed = $this->images_progress;
		if ($postsprocessed == 0) {
			$imagesprocessed = 0;
			$imagesskipped = 0;
		} else {
			$imagesprocessed = $this->images_done;
			$imagesskipped = $this->images_skipped;
		}

		$batchsize = 20;

		$loadedposts = get_posts( array(
			'meta_key' => 'blogger_blog', 
			'meta_value' => $this->host, 
			'posts_per_page' => $batchsize, 
			'offset' => $postsprocessed, 
			'post_status' => array('draft', 'publish', 'future')
		));

		//Stop if nothing left
		if (count($loadedposts) == 0) {
			return true;
		}

		foreach($loadedposts as $importedpost) {

			$importedcontent = $importedpost->post_content;
			$author = get_post_meta($importedpost->ID, 'blogger_author', true);

			$img_count = 0; //Count of images for this post
			foreach($this->get_images($importedcontent) as $lowrez => $highrez) {
				if (!$this->image_filter($lowrez)) {
					//Pass null for description so that the default (filename) is used, might be good to use Alt tag instead?
					$newcontent = $this->import_image($importedpost->ID, $lowrez, $highrez, null, $img_count, $importedcontent, $this->host, $author);
					if (!is_wp_error($newcontent)) {
						$importedcontent = $newcontent;
						$img_count++;
					} else {
						$imagesskipped++;
					}
				}
			}
			$imagesprocessed += $img_count;

			$importedpost->post_content = $importedcontent;
			wp_update_post($importedpost);
			$postsprocessed++;

			$this->images_done = $imagesprocessed;
			$this->images_progress = $postsprocessed;
			$this->images_skipped = $imagesskipped;
		}
		unset($loadedposts);
		return;
	}

	function image_urlremap($url, $large) {
		/* Fix problem urls
		e.g. change https://lh4.googleusercontent.com/-nt66qhxzDyY/TZOD-RhTYMI/AAAAAAAACd4/Elzm1smRFb4/s800-h/Ski%2520Trip.jpg to
		to     https://lh4.googleusercontent.com/-nt66qhxzDyY/TZOD-RhTYMI/AAAAAAAACd4/Elzm1smRFb4/s800/Ski%2520Trip.jpg
		Could use a apply_filter here to allow users to add their own tweeks
		*/
		$pattern = '/(\/)(s\d*)-h(\/)/i';
		$replacement = '$1$2$3';
		$img = preg_replace($pattern, $replacement, $url);

		/* Strip out ? and # on the end of files */
		$pattern = '/(.*)[#\?].*/i';
		$replacement = '$1';
		$img = preg_replace($pattern, $replacement, $img);

		if ($large) {
			// For images on blogger we can swap /sXXX/ with for example /s1600/ to get a larger file.
			// Use a standardised large size so we can control quality vs filesize.
			$pattern = '/(\/)(s\d*)(\/)/i';
			$replacement = '$1s'.Blogger_Importer::LARGE_IMAGE_SIZE.
			'$3';
			$img = preg_replace($pattern, $replacement, $img);
		}
		return $img;
	}

	function image_filter($url) {
		// Do we exclude this particular image?
		// Don't include images that are already loaded onto this site
		// Could use a apply_filter here to allow users to add their own tweeks
		return (substr($url, 0, strlen(site_url())) == site_url());
	}

	function import_image($post_id, $lowrez, $highrez, $description, $imgcount, $postcontent, $blogname, $author) {
		/* Import a new image unless we specifically filter it out or if it has already been downloaded on another page.
		Based on http://wordpress.stackexchange.com/questions//media-sideload-image-file-name and the tumblr-importer
		Simple filename cleaning as characters such as +, % cause issues ref: http://wordpress.org/extend/plugins/uploadplus/

		It's processing links of a form similar to these as provided by the "get_images" function
		<a href="myhighrezimage.jpg"><img src="mylowrezimage.jpg"></a>
		or
		<img src="mylowrezimage.jpg">

		If the high resolution (linked) file is not an image then the low resolution version is downloaded.           
		*/
		$lowrez_old = $lowrez;
		$highrez_old = $highrez;
		$highrezispage = false;

		$lowrez = $this->image_urlremap($lowrez, false);
		if ($lowrez == '')
			return new WP_Error('Not an image', $message = __('Lowrez not an image', 'blogger-importer'), $data = array($lowrez_old, $highrez_old));

		if ($highrez != '') {
			$highrez = $this->image_urlremap($highrez, true);
		} else {
			$highrez = $this->image_urlremap($lowrez, true);
		}

		if (!$att_id = $this->image_exists($lowrez)) {
			//Option to add a timeout to download_url, but don't use the wp_remote_get timeout as that's much shorter than the default here of 300s
			$tmp = @download_url($highrez);

			if (is_wp_error($tmp)) {
				@unlink($tmp); // clean up, copied this from other examples but how is this supposed to work if $tmp is an error??
				//Don't exit as can still try the small image
			}

			// If the highrez was not an image then try the lowrex
			if (!$this->is_image($tmp, $highrez)) {
				$highrezispage = true; //That image was not valid
				$tmp = @download_url($lowrez); // Option to add a timeout here

				if (is_wp_error($tmp)) {
					@unlink($tmp); // clean up
					return $tmp; // output wp_error
				}

				if (!$this->is_image($tmp, $lowrez)) {
					@unlink($tmp); // clean up None of items are actually images, for example might be a single pixel, deliberately filtered out or a 404 error?
					return new WP_Error('No Images', __('None of the images are valid', 'blogger-importer'), $data = array($lowrez_old, $highrez_old));
				}
			}

			$new_name = preg_replace('/[^A-Za-z0-9._ ]/i', '-', basename($lowrez));

			$file_array = array('name' => $new_name, 'tmp_name' => $tmp);

			if ( empty( $description ) ) {
				$description = $new_name;
			}
			
			$att_id = media_handle_sideload($file_array, $post_id, $description);
			if (is_wp_error($att_id)) {
				@unlink($file_array['tmp_name']);
				return $att_id;
			}

			// Link attachment upto old url, store the author so we can replace it later
			add_post_meta($att_id, 'blogger_permalink', $lowrez);
			add_post_meta($att_id, 'blogger_blog', $blogname, true);
			add_post_meta($att_id, 'blogger_author', $author, true);

			if ($highrezispage) //Not an image so store so we can link later
				add_post_meta($att_id, 'blogger_largeimgispage', true);

		} else {
			//Image already exists, check if the high rez one was valid last time
			$tmp = get_post_meta($att_id, 'blogger_largeimgispage', true);
			if ($tmp == true)
				$highrezispage = true;
		}

		//Always treat picassa webs as image so they get replaced with the new High rez link
		if (substr($highrez, 0, 27) == 'http://picasaweb.google.com')
			$highrezispage = false;

		//Replace the image strings
		if (!$highrezispage && $highrez_old != '') {
			$imagesrc = wp_get_attachment_image_src($att_id, 'full');
			$highrez_new = reset($imagesrc);
			$postcontent = str_replace($highrez_old, $highrez_new, $postcontent);
		}
		$imagesrc = wp_get_attachment_image_src($att_id, 'medium');
		$lowrez_new = reset($imagesrc);
		$postcontent = str_replace($lowrez_old, $lowrez_new, $postcontent);

		//Set the first image to be the post thumbnail (zero index)
		if ($imgcount == 0) {
			set_post_thumbnail($post_id, $att_id);
		}

		//media handle sideload moves the file so there should be no temp file left but cleanup just incase.
		@unlink($tmp);

		// incase something goes wrong
		if ($postcontent == '') {
			return new WP_Error('Empty Content', __("Attempting to write back empty content", 'blogger-importer'), $data = array($lowrez_old, $highrez_old));
		}
		return $postcontent;
	}

	function is_image($file, $filename) {
		//Is the downloaded file really an image
		//e.g. it looked like an image from the URL but when downloaded it was something else perhaps a html page
		//Also filter out tracking images of 1 pixel square
		//Found that wp_check_filetype_and_ext and wp_match_mime_types was giving false positives
		$imgstats = @getimagesize($file);
		if (!$imgstats) {
			return false;
		}

		return (($imgstats[0] > 1) && ($imgstats[1] > 1));
	}

	function image_exists($lowrez) {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts p INNER JOIN $wpdb->postmeta m ON p.ID = m.post_id AND meta_key = 'blogger_permalink' WHERE post_type = 'attachment' AND meta_value = %s LIMIT 0 , 1",
			$lowrez));
	}
	
        function process_links() {
		//Update all of the links in the blog
		global $wpdb;

		$postsprocessed = $this->links_progress;
		if ($postsprocessed == 0) {
			$linksprocessed = 0;
		} else {
			$linksprocessed = $this->links_done;
		}
		$batchsize = 20;

		$oldurlsearch = $this->host;

		if (substr($oldurlsearch, 0, 3) == 'www.') {
			$oldurlsearch = substr($oldurlsearch, 3, strlen($oldurlsearch - 3));
		}

		$oldurlsearch = str_replace('.', '\.', $oldurlsearch);

		$blogspot = stripos($oldurlsearch, '\.blogspot\.');
		if ($blogspot) { //Blogspot addresses can be international e.g. myblog.blogspot.com, myblog.blogspot.com.au or myblog.blogspot.co.uk or myblog.blogspot.de both resolve to the same blog.
			//See http://www.searchenginejournal.com/google-blogger-url-censorship/39724/
			$oldurlsearch = substr($oldurlsearch, 0, $blogspot + 12).
			'[\w\.]{2,6}';
		}

		$loadedposts = get_posts(array('meta_key' => 'blogger_blog', 'meta_value' => $this->host, 'posts_per_page' => $batchsize, 'offset' => $postsprocessed, 'post_status' => array('draft', 'publish', 'future')));

		//Stop if nothing left
		if (count($loadedposts) == 0) {
			return true;
		}

		foreach($loadedposts as $importedpost) {
			$importedcontent = $importedpost->post_content;

			$regexp = '<a\s[^>]*href=([\"\'`])(https?:\/\/(?:www\.)*'.$oldurlsearch.'\/)([^\" >]*?)\1[^>]*>(.*)<\/a>';
			if (preg_match_all("/$regexp/siU", $importedcontent, $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {
					$HostURL = substr($match[2], 0, strlen($match[2]) - 1); //e.g. http://minitemp.blogspot.co.uk
					$PageURL = '/'.$match[3]; //e.g. '/2011/04/what-happens-if-blog-title-is-really.html'
					$sql = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta  m
					inner join $wpdb->posts p on p.id = m.post_id and post_type = 'post'
					where meta_key = '%s'
					and meta_value = '%s'
					", 'blogger_permalink', $PageURL);

					$linkpostid = $wpdb->get_var($sql);

					if ($linkpostid != 0) {
						$NewURL = get_permalink($linkpostid);
					} else { // Page not found, update content with just the new domain
						$NewURL = site_url($PageURL);
					}

					$importedcontent = str_replace($HostURL.$PageURL, $NewURL, $importedcontent); $linksprocessed++;
				}

				if ($importedcontent == '') {
					return new WP_Error('Empty Content', __("Attempting to write back empty content"));
				}

				$importedpost->post_content = $importedcontent;
				wp_update_post($importedpost);

			}
			$postsprocessed++;

			//For some reason the intermediate values are not getting written, is it that the options are cached hence not read back?
			$this->links_done = $linksprocessed;
			$this->links_progress = $postsprocessed;
		}
		unset($loadedposts);
		return;
	}

	function isurlimage($srcurl) {
		//Process picasaweb links and files that are images
		if (substr($srcurl, 0, 27) == 'http://picasaweb.google.com')
			return true;
		return preg_match('/(?i)\.(jpe?g|png|gif|bmp)$/i', $srcurl);
	}
	/**
	 * Parse the atom file
	 *
	 * @param string $file Path to XML file for parsing
	 * @return SimplePie object of the feed
	 */
	function parse( $file ) {
		$data = file_get_contents( $file );
		// parse the feed
		$feed = new SimplePie();
		//set_xxxx methods depreciated (and not working?) replaced with get_registry as per docs
		$reg = $feed->get_registry();
		$reg->register('Sanitize', 'Blogger_Importer_Sanitize');
		$feed->sanitize = $reg->create('Sanitize'); //Should not really need to do this but there seems to be an issue with the SimplePie class?
		$reg->register('Item', 'WP_SimplePie_Blog_Item');
		$feed->set_raw_data( $data );
		$feed->init();
		return $feed;
	}

	// Display import page title
	function header() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>' . __( 'Import Blogger', 'blogger-importer' ) . '</h2>';
	}

	// Close div.wrap
	function footer() {
		echo '</div>';
	}

	/**
	 * Display introductory text and file upload form
	 */
	function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__( 'Howdy! Upload your Blogger Exported XML file and we&#8217;ll import the contents from it into this site.', 'blogger-importer' ).'</p>';
		echo '<p>'.__( 'Choose an XML file to upload, then click Upload file and import.', 'blogger-importer' ).'</p>';
		wp_import_upload_form( 'admin.php?import=blogger&amp;step=1' );
		echo '</div>';
	}

	/**
	 * Decide if the given meta key maps to information we will want to import
	 *
	 * @param string $key The meta key to check
	 * @return string|bool The key if we do want to import, false if not
	 */
	function is_valid_meta_key( $key ) {
		// skip attachment metadata since we'll regenerate it from scratch
		// skip _edit_lock as not relevant for import
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) )
			return false;
		return $key;
	}

	/**
	 * Decide whether or not the importer is allowed to create users.
	 * Default is true, can be filtered via import_allow_create_users
	 *
	 * @return bool True if creating users is allowed
	 */
	function allow_create_users() {
		return apply_filters( 'import_allow_create_users', true );
	}

	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
	 * @return int 60
	 */
	function bump_request_timeout( $val ) {
		return 60;
	}

	// return the difference in length between two strings
	function cmpr_strlen( $a, $b ) {
		return strlen($b) - strlen($a);
	}
}

} // class_exists( 'Blogger_Importer' )

function blogger_importer_init() {
	load_plugin_textdomain( 'blogger-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * WordPress Importer object for registering the import callback
	 * @global WP_Import $wp_import
	 */
	$GLOBALS['wp_import'] = new Blogger_Importer();
	register_importer('blogger', __('Blogger', 'blogger-importer'), __('Import posts, comments, and categories from a Blogger blog and migrate authors to WordPress users.', 'blogger-importer'), array( $GLOBALS['wp_import'], 'dispatch' ) );
}
add_action( 'admin_init', 'blogger_importer_init' );
