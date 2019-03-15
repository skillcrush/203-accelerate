<?php
/*
Plugin Name: Easy Social Icons
Plugin URI: http://www.cybernetikz.com
Description: You can upload your own social icon, set your social URL, choose weather you want to display vertical or horizontal. You can use the shortcode <strong>[cn-social-icon]</strong> in page/post, template tag for php file <strong>&lt;?php if ( function_exists('cn_social_icon') ) echo cn_social_icon(); ?&gt;</strong> also you can use the widget <strong>"Easy Social Icons"</strong> for sidebar.
Version: 3.0.6
Author: cybernetikz
Author URI: http://www.cybernetikz.com
License: GPL2
*/

if( !defined('ABSPATH') ) die();

$cnssUploadDir = wp_upload_dir();
$cnssBaseDir = $cnssUploadDir['basedir'].'/';
$cnssBaseURL = $cnssUploadDir['baseurl'].'';
$cnssPluginsURI = plugins_url('/easy-social-icons/');

add_action('init', 'cnss_init_script');
add_action('init', 'cn_process_post');
add_action('wp_ajax_update-social-icon-order', 'cnss_save_ajax_order' );
add_action('admin_menu', 'cnss_add_menu_pages');
add_action('wp_head', 'social_profile_links_fn');
add_action('admin_enqueue_scripts', 'cnss_admin_style');
if( isset($_GET['page']) ) {
	if( $_GET['page']=='cnss_social_icon_add' ) {
		add_action('admin_enqueue_scripts', 'cnss_admin_enqueue' );
	}
}
register_activation_hook(__FILE__,'cnss_db_install');
add_shortcode('cn-social-icon', 'cn_social_icon');
if (isset($_GET['cnss-delete'])) {
	if ($_GET['id'] != '')
	{
		$table_name = $wpdb->prefix . "cn_social_icon";
		$image_file_path = $cnssBaseDir;
		$wpdb->delete( $table_name, array( 'id' => $_GET['id'] ), array( '%d' ) );
		$msg = "Delete Successful !"."<br />";
	}
}

function cnss_admin_sidebar() {

	$banners = array(
		array(
			'url' => 'http://www.cybernetikz.com/wordpress-magento-plugins/wordpress-plugins/?utm_source=easy-social-icons&utm_medium=banner&utm_campaign=wordpress-plugins',
			'img' => 'banner-1.jpg',
			'alt' => 'Banner 1',
		),
		array(
			'url' => 'http://www.cybernetikz.com/web-development/wordpress-website/?utm_source=easy-social-icons&utm_medium=banner&utm_campaign=wordpress-plugins',
			'img' => 'banner-2.jpg',
			'alt' => 'Banner 2',
		),
		array(
			'url' => 'http://www.cybernetikz.com/seo-consultancy/?utm_source=easy-social-icons&utm_medium=banner&utm_campaign=wordpress-plugins',
			'img' => 'banner-3.jpg',
			'alt' => 'Banner 3',
		),
	);
	//shuffle( $banners );
	?>
	<div class="cn_admin_banner">
	<?php
	$i = 0;
	foreach ( $banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '"><img width="261" height="190" src="' . plugins_url( 'images/' . $banner['img'], __FILE__ ) . '" alt="' . esc_attr( $banner['alt'] ) . '"/></a>';
		$i ++;
	}
	?>
	</div>
<?php
}

function cnss_generate_random_code($length) {
	$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	$url = "";
	while ($i <= $length) {
		$url .= $chars{mt_rand(0,strlen($chars))};
		$i++;
	}
	return $url;
}

function cnss_admin_style() {
	global $cnssPluginsURI;
	wp_register_style( 'cnss_admin_css', $cnssPluginsURI . 'css/admin-style.css', false, '1.0' );
	wp_register_style( 'cnss_font_awesome_css', $cnssPluginsURI . 'css/font-awesome/css/all.min.css', false, '5.7.2' );
	wp_register_style( 'cnss_font_awesome_v4_shims', $cnssPluginsURI . 'css/font-awesome/css/v4-shims.min.css', false, '5.7.2' );
	wp_enqueue_style( 'cnss_admin_css' );
	wp_enqueue_style( 'cnss_font_awesome_css' );
	wp_enqueue_style( 'cnss_font_awesome_v4_shims' );
	wp_enqueue_style( 'wp-color-picker' );
}

function cnss_init_script() {
	global $cnssPluginsURI;
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('jquery-ui-sortable');
	wp_register_script('cnss_js', $cnssPluginsURI . 'js/cnss.js', array(), '1.0' );
	wp_enqueue_script( 'cnss_js' );

	wp_register_style( 'cnss_font_awesome_css', $cnssPluginsURI . 'css/font-awesome/css/all.min.css', false, '5.7.2' );
	wp_enqueue_style( 'cnss_font_awesome_css' );

	wp_register_style( 'cnss_font_awesome_v4_shims', $cnssPluginsURI . 'css/font-awesome/css/v4-shims.min.css', false, '5.7.2' );
	wp_enqueue_style( 'cnss_font_awesome_v4_shims' );

	wp_register_style('cnss_css', $cnssPluginsURI . 'css/cnss.css', array(), '1.0' );
	wp_enqueue_style( 'cnss_css' );
	wp_enqueue_script( 'wp-color-picker' );
}

function cnss_admin_enqueue() {
	global $cnssPluginsURI;
	wp_enqueue_media();
	wp_register_script('cnss_admin_js', $cnssPluginsURI . 'js/cnss_admin.js', array(), '1.0' );
	wp_enqueue_script( 'cnss_admin_js' );
}

function cnss_get_all_icons($where_sql = '') {
	global $wpdb;
	$table_name = $wpdb->prefix . "cn_social_icon";
	$sql = "SELECT id, title, url, image_url, sortorder, target FROM {$table_name} WHERE url<>'' AND image_url<>'' ORDER BY sortorder;";

	$social_icons = $wpdb->get_results($sql);
	if (count($social_icons)>0) {
		return $social_icons;
	} else {
		return array();
	}
}

function cn_esi_get_option($key='') {
	if ($key == '') {
		return;
	}

	$cnss_esi_settings = array(
		'cnss-width' => '32',
		'cnss-height' => '32',
		'cnss-margin' => '4',
		'cnss-row-count' => '1',
		'cnss-vertical-horizontal' => 'horizontal',
		'cnss-text-align' => 'center',
		'cnss-social-profile-links' => '0',
		'cnss-social-profile-type' => 'Person',
		'cnss-icon-bg-color' => '#999999',
		'cnss-icon-bg-hover-color' => '#666666',
		'cnss-icon-color' => '#ffffff',
		'cnss-icon-hover-color' => '#ffffff',
		'cnss-icon-shape' => 'square',
		'cnss-original-icon-color' => '1'
	);
	if ( get_option($key) != '' ) {
		return get_option($key);
	} else {
		return $cnss_esi_settings[$key];
	}
}

function social_profile_links_fn() {

	$social_profile_links = cn_esi_get_option('cnss-social-profile-links');
	$cnss_original_icon_color = cn_esi_get_option('cnss-original-icon-color');
	$icon_bg_color = cn_esi_get_option('cnss-icon-bg-color');
	$icon_bg_hover_color = cn_esi_get_option('cnss-icon-bg-hover-color');
	$icon_hover_color = cn_esi_get_option('cnss-icon-hover-color');

	$icons = cnss_get_all_icons();
	if ( !empty($icons) && $social_profile_links == 1 ) {
		$sameAs = '';
		$social_profile_type = get_option('cnss-social-profile-type');
		$profile_type = $social_profile_type == 'Person'?'Person':'Organization';
		foreach ($icons as $icon) {
			$sameAs .= '"'.$icon->url.'",';
		}
		$sameAs = rtrim($sameAs,',');
		echo '<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "'.$profile_type.'",
		  "name": "'.get_option('blogname').'",
		  "url": "'.esc_url( home_url( '/' ) ).'",
		  "sameAs": ['.$sameAs .']
		}
		</script>';
	}

	if ($cnss_original_icon_color == '1') {
		echo '<style type="text/css">
		ul.cnss-social-icon li.cn-fa-icon a:hover{opacity: 0.7!important;color:'.$icon_hover_color.'!important;}
		</style>';
	} else {
		echo '<style type="text/css">
		ul.cnss-social-icon li.cn-fa-icon a{background-color:'.$icon_bg_color.'!important;}
		ul.cnss-social-icon li.cn-fa-icon a:hover{background-color:'.$icon_bg_hover_color.'!important;color:'.$icon_hover_color.'!important;}
		</style>';
	}
}

function cnss_add_menu_pages() {
	add_menu_page('Easy Social Icons', 'Easy Social Icons', 'manage_options', 'cnss_social_icon_page', 'cnss_social_icon_page_fn',plugins_url('/images/scc-sc.png', __FILE__) );
	//add_menu_page('Easy Social Icons', 'Easy Social Icons', 'manage_options', 'cnss_social_icon_page', 'cnss_social_icon_page_fn', 'dashicons-share' );

	add_submenu_page('cnss_social_icon_page', 'All Icons', 'All Icons', 'manage_options', 'cnss_social_icon_page', 'cnss_social_icon_page_fn');

	add_submenu_page('cnss_social_icon_page', 'Add New', 'Add New', 'manage_options', 'cnss_social_icon_add', 'cnss_social_icon_add_fn');

	add_submenu_page('cnss_social_icon_page', 'Sort Icons', 'Sort Icons', 'manage_options', 'cnss_social_icon_sort', 'cnss_social_icon_sort_fn');

	add_submenu_page('cnss_social_icon_page', 'Settings &amp; Instructions', 'Settings &amp; Instructions', 'manage_options', 'cnss_social_icon_option', 'cnss_social_icon_option_fn');

	add_action( 'admin_init', 'register_cnss_settings' );

}

function register_cnss_settings() {
	register_setting( 'cnss-settings-group', 'cnss-width' );
	register_setting( 'cnss-settings-group', 'cnss-height' );
	register_setting( 'cnss-settings-group', 'cnss-margin' );
	register_setting( 'cnss-settings-group', 'cnss-row-count' );
	register_setting( 'cnss-settings-group', 'cnss-vertical-horizontal' );
	register_setting( 'cnss-settings-group', 'cnss-text-align' );
	register_setting( 'cnss-settings-group', 'cnss-social-profile-links' );
	register_setting( 'cnss-settings-group', 'cnss-social-profile-type' );
	register_setting( 'cnss-settings-group', 'cnss-icon-bg-color' );
	register_setting( 'cnss-settings-group', 'cnss-icon-bg-hover-color' );
	register_setting( 'cnss-settings-group', 'cnss-icon-color' );
	register_setting( 'cnss-settings-group', 'cnss-icon-hover-color' );
	register_setting( 'cnss-settings-group', 'cnss-icon-shape' );
	register_setting( 'cnss-settings-group', 'cnss-original-icon-color', 'cnss_original_icon_color_fn' );
}

function cnss_original_icon_color_fn($value) {
	return $value==''?'0':$value;
}

function cnss_social_icon_option_fn() {

	$cnss_width = get_option('cnss-width');
	$cnss_height = get_option('cnss-height');
	$cnss_margin = get_option('cnss-margin');
	$cnss_rows = get_option('cnss-row-count');
	$vorh = get_option('cnss-vertical-horizontal');
	$text_align = get_option('cnss-text-align');
	$social_profile_links = get_option('cnss-social-profile-links');
	$social_profile_type = get_option('cnss-social-profile-type');
	$icon_bg_color = get_option('cnss-icon-bg-color');
	$icon_bg_hover_color = get_option('cnss-icon-bg-hover-color');
	$icon_color = get_option('cnss-icon-color');
	$icon_hover_color = get_option('cnss-icon-hover-color');
	$icon_shape = get_option('cnss-icon-shape');
	$cnss_original_icon_color = get_option('cnss-original-icon-color');

	$vertical ='';
	$horizontal ='';
	if($vorh=='vertical') $vertical = 'checked="checked"';
	if($vorh=='horizontal') $horizontal = 'checked="checked"';

	$center ='';
	$left ='';
	$right ='';
	if($text_align=='center') $center = 'checked="checked"';
	if($text_align=='left') $left = 'checked="checked"';
	if($text_align=='right') $right = 'checked="checked"';

	?>
	<div class="wrap">
	<?php echo cnss_esi_review_text(); ?>
    <h2>Icon Settings</h2>
    <div class="content_wrapper">
    <div class="left">
	<form method="post" action="options.php" enctype="multipart/form-data">
		<?php settings_fields( 'cnss-settings-group' ); ?>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Icon Width</th>
			<td><input type="number" name="cnss-width" id="cnss-width" class="small-text" value="<?php echo $cnss_width?>" />px</td>
			</tr>
			<tr valign="top">
			<th scope="row">Icon Height</th>
			<td><input type="number" name="cnss-height" id="cnss-height" class="small-text" value="<?php echo $cnss_height?>" />px</td>
			</tr>
			<tr valign="top">
			<th scope="row">Icon Margin</th>
			<td><input type="number" name="cnss-margin" id="cnss-margin" class="small-text" value="<?php echo $cnss_margin?>" />px  <em><small>(Gap between each icon)</small></em></td>
			</tr>

			<tr valign="top">
			<th scope="row">Icon Display</th>
			<td>
				<input <?php echo $horizontal ?> type="radio" name="cnss-vertical-horizontal" id="horizontal" value="horizontal" />&nbsp;<label for="horizontal">Horizontally</label><br />
				<input <?php echo $vertical ?> type="radio" name="cnss-vertical-horizontal" id="vertical" value="vertical" />&nbsp;<label for="vertical">Vertically</label></td>
			</tr>

            <tr valign="top">
			<th scope="row">Icon Alignment</th>
			<td>
				<input <?php echo $center ?> type="radio" name="cnss-text-align" id="center" value="center" />&nbsp;<label for="center">Center</label><br />
				<input <?php echo $left ?> type="radio" name="cnss-text-align" id="left" value="left" />&nbsp;<label for="left">Left</label><br />
				<input <?php echo $right ?> type="radio" name="cnss-text-align" id="right" value="right" />&nbsp;<label for="right">Right</label></td>
			</tr>

			<tr valign="top">
				<th scope="row">Google Social Profile Links</th>
				<td>
					<input type="checkbox" id="cnss_social_profile_links" name="cnss-social-profile-links" value="1" <?php echo $social_profile_links==1?'checked="checked"':''; ?>>
					<a target="_blank" href="https://developers.google.com/search/docs/data-types/social-profile-links" style="text-decoration: none;" id="show_whatis_social_profile_links">What is Social Profile Links?</a>
					<div style="position: relative;">
						<img width="300" style="position: absolute; top:-300px; right: 0;" id="whatis_social_profile_links" class="hidden" src="<?php echo plugins_url( 'images/' . 'social-profiles.png', __FILE__ ); ?>">
					</div>
				</td>
			</tr>
			<tr id="wrap-social-profile-type" valign="top" style="<?php echo $social_profile_links==1?'':'display: none;'; ?>">
				<th scope="row">Google Social Profile Type</th>
				<td>
					<select name="cnss-social-profile-type">
						<option <?php echo $social_profile_type=='Person'?'selected="selected"':''; ?> value="Person">Person</option>
						<option <?php echo $social_profile_type=='Organization'?'selected="selected"':''; ?> value="Organization">Organization</option>
					</select>
				</td>
			</tr>

			<tr>
				<th colspan="2"><h2>Following settings is for Font-Awesome icons only</h2></th>
			</tr>

			<tr valign="top">
				<th scope="row">Use Original Color</th>
				<td><input type="checkbox" id="cnss_use_original_color" name="cnss-original-icon-color" value="1" <?php echo $cnss_original_icon_color==1?'checked="checked"':''; ?>> <em>This will show original icon color for social icons, like <span style="background:#3b5998; color: #fff;">facebook</span> color is blue, <span style="background:#e62f27; color: #fff;">youtube</span> color is red.</em></td>
			</tr>

			<tr class="wrap-icon-bg-color" valign="top" style="<?php echo $cnss_original_icon_color==1?'display: none;':''; ?>">
				<th scope="row">Icon Background Color</th>
				<td><input type="text" name="cnss-icon-bg-color" id="cnss-icon-bg-color" class="cnss-fa-icon-color" value="<?php echo $icon_bg_color ?>" /></td>
			</tr>
			<tr class="wrap-icon-bg-color" valign="top" style="<?php echo $cnss_original_icon_color==1?'display: none;':''; ?>">
				<th scope="row">Icon Background Hover Color</th>
				<td><input type="text" name="cnss-icon-bg-hover-color" id="cnss-icon-bg-hover-color" class="cnss-fa-icon-color" value="<?php echo $icon_bg_hover_color ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">Icon Color</th>
				<td><input type="text" name="cnss-icon-color" id="cnss-icon-color" class="cnss-fa-icon-color" value="<?php echo $icon_color ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">Icon Hover Color</th>
				<td><input type="text" name="cnss-icon-hover-color" id="cnss-icon-hover-color" class="cnss-fa-icon-color" value="<?php echo $icon_hover_color ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">Icon Shape</th>
				<td><select name="cnss-icon-shape" id="cnss-icon-shape">
					<option <?php selected( $icon_shape,'square'); ?> value="square">Square</option>
					<option <?php selected( $icon_shape,'circle'); ?> value="circle">Circle</option>
					<option <?php selected( $icon_shape,'round-corner'); ?> value="round-corner">Round Corner</option>
				</select></td>
			</tr>
		</table>
		<p class="submit" style="text-align:center"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /><?php echo cnss_back_to_link() ?></p>
	</form>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#show_whatis_social_profile_links').hover(function() {
				//e.preventDefault();
				$('#whatis_social_profile_links').fadeToggle('fast');
			});
			$('input#cnss_social_profile_links').change(function(event) {
				if( $(this).prop( "checked" ) == true ) {
					$('#wrap-social-profile-type').fadeIn('fast');
				} else {
					$('#wrap-social-profile-type').fadeOut('fast');
				}
			});
			$('input#cnss_use_original_color').change(function(event) {
				if( $(this).prop( "checked" ) == false ) {
					$('.wrap-icon-bg-color').fadeIn('fast');
				} else {
					$('.wrap-icon-bg-color').fadeOut('fast');
				}
			});

		});
		jQuery(document).ready(function($){
		    $('.cnss-fa-icon-color').wpColorPicker();
		});
	</script>

    <h2 id="shortcode">How to use</h2>
    <fieldset class="cnss-esi-shadow">
        <legend><h4 class="sec-title">Using Widget</h4></legend>
        <p>Simply go to <strong>Appearance -> <a href="widgets.php">Widgets</a></strong>
        then drag drop <code>Easy Social Icons</code> widget to <strong>Widget Area</strong></p>
    </fieldset>

    <fieldset class="cnss-esi-shadow">
    	<legend><h4 class="sec-title">Using Shortcode</h4></legend>
		<?php
        $shortcode = '[cn-social-icon';
        if( isset($_POST['generate_shortcode']) && check_admin_referer('cn_gen_sc') )
        {
            if(is_numeric($_POST['_width']) && $cnss_width != $_POST['_width']){
                $shortcode .= ' width=&quot;'.$_POST['_width'].'&quot;';
            }
            if(is_numeric($_POST['_height']) && $cnss_height != $_POST['_height'] ){
                $shortcode .= ' height=&quot;'.$_POST['_height'].'&quot;';
            }
            if(is_numeric($_POST['_margin']) && $cnss_margin != $_POST['_margin'] ){
                $shortcode .= ' margin=&quot;'.$_POST['_margin'].'&quot;';
            }
            if(isset($_POST['_alignment']) && $text_align != $_POST['_alignment'] ){
                $shortcode .= ' alignment=&quot;'.$_POST['_alignment'].'&quot;';
                $text_align = $_POST['_alignment'];
            }
            if(isset($_POST['_display']) && $vorh != $_POST['_display'] ){
                $shortcode .= ' display=&quot;'.$_POST['_display'].'&quot;';
                $vorh = $_POST['_display'];
            }
            if(isset($_POST['_attr_id']) && $_POST['_attr_id']!=''){
                $shortcode .= ' attr_id=&quot;'.$_POST['_attr_id'].'&quot;';
            }
            if(isset($_POST['_attr_class']) && $_POST['_attr_class']!=''){
                $shortcode .= ' attr_class=&quot;'.$_POST['_attr_class'].'&quot;';
            }
            if( isset($_POST['_selected_icons']) ) {
                if(is_array($_POST['_selected_icons'])) {
                    $ids = "";
                    foreach($_POST['_selected_icons'] as $icon) {
                        $ids .= $icon.",";
                    }
                    $ids = rtrim($ids,",");
                    $shortcode .= ' selected_icons=&quot;'.$ids .'&quot;';
                }
            }
        }
        $shortcode .= ']';
        ?>
        <p>Copy and paste following shortcode to any <strong>Page</strong> or <strong>Post</strong>.<p>

        <p><input onclick="this.select();" readonly="readonly" type="text" value="<?php echo $shortcode; ?>" class="large-text" /></p>
        <p>Or you can change following icon settings and click <strong>Generate Shortcode</strong> button to get updated shortcode.</p>
        <form method="post" action="admin.php?page=cnss_social_icon_option#shortcode" enctype="application/x-www-form-urlencoded">
        	<?php wp_nonce_field('cn_gen_sc'); ?>
            <input type="hidden" name="generate_shortcode" value="1" />
            <table width="100%" border="0">
              <tr>
                <td width="110"><label><?php _e( 'Icon Width <em>(px)</em>:' ); ?></label>
                <input class="widefat" name="_width" type="number" value="<?php echo isset($_POST['_width'])?$_POST['_width']:$cnss_width; ?>" /></td>
                <td>&nbsp;</td>
                <td width="110"><label><?php _e( 'Icon Height <em>(px)</em>:' ); ?></label>
                <input class="widefat" name="_height" type="number" value="<?php echo isset($_POST['_height'])?$_POST['_height']:$cnss_height; ?>" /></td>
                <td>&nbsp;</td>
                <td><label><?php _e( 'Margin <em>(px)</em>:' ); ?></label><br />
                <input class="widefat" name="_margin" type="number" value="<?php echo isset($_POST['_margin'])?$_POST['_margin']:$cnss_margin; ?>" /></td>
                <td>&nbsp;</td>
                <td><label><?php _e( 'Alignment:' ); ?></label><br />
                    <select name="_alignment">
                        <option <?php if($text_align=='center')echo 'selected="selected"'; ?> value="center">Center</option>
                        <option <?php if($text_align=='left')echo 'selected="selected"'; ?> value="left">Left</option>
                        <option <?php if($text_align=='right')echo 'selected="selected"'; ?> value="right">Right</option>
                    </select></td>
                <td>&nbsp;</td>
                <td><label><?php _e( 'Display:' ); ?></label><br />
                    <select name="_display">
                        <option <?php if($vorh=='horizontal')echo 'selected="selected"'; ?> value="horizontal">Horizontally</option>
                        <option <?php if($vorh=='vertical')echo 'selected="selected"'; ?> value="vertical">Vertically</option>
                    </select></td>
                <td>&nbsp;</td>
                <td><label><?php _e( 'Custom ID:' ); ?></label>
                <input class="widefat" placeholder="ID" name="_attr_id" type="text" value="<?php echo isset($_POST['_attr_id'])?$_POST['_attr_id']:''; ?>" /></td>
                <td>&nbsp;</td>
                <td><label><?php _e( 'Custom Class:' ); ?></label>
                <input class="widefat" placeholder="Class" name="_attr_class" type="text" value="<?php echo isset($_POST['_attr_class'])?$_POST['_attr_class']:''; ?>" /></td>
              </tr>
            </table>
            <p></p>
            <?php echo _cn_social_icon_sc( isset($_POST['_selected_icons'])?$_POST['_selected_icons']:array() ); ?>
            <p><label><?php _e( 'Select Social Icons:' ); ?></label> <em>(If select none all icons will be displayed)</em></p>
            <p>
                <input type="submit" class="button-primary" value="<?php _e('Generate Shortcode') ?>" />
            </p>
        </form>
        <p><strong>Note</strong>: You can also add shortcode to <strong>Text Widget</strong>  but this code <code>add_filter('widget_text', 'do_shortcode');</code> needs to be added to your themes <strong>functions.php</strong> file.</p>
    </fieldset>

    <fieldset class="cnss-esi-shadow" style="margin-bottom:0px;">
        <legend><h4 class="sec-title">Using PHP Template Tag</h4></legend>
        <p><strong>Simple Use</strong></p>
        <p>If you are familiar with PHP code, then you can use PHP Template Tag</p>
        <pre><code>&lt;?php if ( function_exists('cn_social_icon') ) echo cn_social_icon(); ?&gt;</code></pre>
        <p><strong>Advanced Use</strong></p>
<pre><code>&lt;?php
$attr = array (
    'width' => '32', //input only number, in pixel
    'height' => '32', //input only number, in pixel
    'margin' => '4', //input only number, in pixel
    'display' => 'horizontal', //horizontal | vertical
    'alignment' => 'center', //center | left | right
    'attr_id' => 'custom_icon_id', //add custom id to &lt;ul&gt; wraper
    'attr_class' => 'custom_icon_class', //add custom class to &lt;ul&gt; wraper
    'selected_icons' => array ( '1', '3', '5', '6' )
    //you can get the icon ID form <strong><a href="admin.php?page=cnss_social_icon_page">All Icons</a></strong> page
);
if ( function_exists('cn_social_icon') ) echo cn_social_icon( $attr );
?&gt;</code></pre>
    </fieldset>

    </div>
    <div class="right">
    <?php cnss_admin_sidebar(); ?>
    </div>
    </div>
	</div>
	<?php
}

function cnss_db_install () {
	global $wpdb;

	$table_name = $wpdb->prefix . "cn_social_icon";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql_create_table = "CREATE TABLE IF NOT EXISTS `$table_name` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`title` VARCHAR(255) NULL,
		`url` VARCHAR(255) NOT NULL,
		`image_url` VARCHAR(255) NOT NULL,
		`sortorder` INT NOT NULL DEFAULT '0',
		`date_upload` VARCHAR(50) NULL,
		`target` tinyint(1) NOT NULL DEFAULT '1',
		PRIMARY KEY (`id`)) ENGINE = InnoDB;
		INSERT INTO `wp_cn_social_icon` (`id`, `title`, `url`, `image_url`, `sortorder`, `date_upload`, `target`) VALUES
		(1, 'Facebook', 'https://facebook.com/', 'fa fa-facebook', 0, '1487164658', 1),
		(2, 'Twitter', 'https://twitter.com/', 'fa fa-twitter', 1, '1487164673', 1),
		(3, 'LinkedIn', 'https://linkedin.com/', 'fa fa-linkedin', 2, '1487164712', 1);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql_create_table);
	}

	$cnss_esi_settings = array(
		'cnss-width' => '32',
		'cnss-height' => '32',
		'cnss-margin' => '4',
		'cnss-row-count' => '1',
		'cnss-vertical-horizontal' => 'horizontal',
		'cnss-text-align' => 'center',
		'cnss-social-profile-links' => '0',
		'cnss-social-profile-type' => 'Person',
		'cnss-icon-bg-color' => '#666666',
		'cnss-icon-bg-hover-color' => '#333333',
		'cnss-icon-color' => '#ffffff',
		'cnss-icon-hover-color' => '#ffffff',
		'cnss-icon-shape' => 'square',
		'cnss-original-icon-color' => '1'
	);

	foreach ($cnss_esi_settings as $key => $value) {
		add_option( trim($key), trim($value) );
	}
}

function cn_process_post() {
	global $wpdb,$err,$msg,$cnssBaseDir;
	if ( isset($_POST['submit_button']) && check_admin_referer('cn_insert_icon') ) {

		if ($_POST['action'] == 'update')
		{

			$err = "";
			$msg = "";

			$image_file_path = $cnssBaseDir;

			if ($err == '')
			{
				$table_name = $wpdb->prefix . "cn_social_icon";

				$results = $wpdb->insert(
					$table_name,
					array(
						'title' => sanitize_text_field($_POST['title']),
						'url' => sanitize_text_field($_POST['url']),
						'image_url' => sanitize_text_field($_POST['image_file']),
						'sortorder' => sanitize_text_field($_POST['sortorder']),
						'date_upload' => time(),
						'target' => sanitize_text_field($_POST['target']),
					),
					array(
						'%s',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
					)
				);

				if (!$results)
					$err .= "Fail to update database" . "<br />";
				else
					$msg .= "Update successful !" . "<br />";
			}
			/*
			$allSocialMediaIcons = array('500px','amazon','android','angellist','apple','bandcamp','behance','behance-square','bitbucket','bluetooth','cc-amex','cc-mastercard','cc-paypal','cc-stripe','cc-visa','codepen','css3','delicious','deviantart','digg','dribbble ','dropbox','drupal','edge ','etsy','expeditedssl','facebook','facebook-f','facebook-official','facebook-square','firefox','flickr','forumbee ','foursquare','free-code-camp','get-pocket','git ','git-square ','github ','github-square ','gitlab','google ','google-plus','google-plus-circle','google-plus-official','google-plus-square','google-wallet','gratipay','hacker-news','houzz','html5','imdb','instagram','internet-explorer','joomla','lastfm','linkedin','linkedin-square','linux','maxcdn ','medium ','meetup','odnoklassniki','opera','paypal','pinterest ','pinterest-p ','pinterest-square ','product-hunt','quora ','reddit ','rss ','scribd','skype','slack','slideshare ','snapchat','soundcloud','spotify','stack-exchange','stack-overflow','steam','stumbleupon','telegram','trello','tripadvisor','tumblr','tumblr-square','twitch','twitter','twitter-square','viadeo','vimeo ','vimeo-square ','vine ','wechat','whatsapp ','wikipedia-w','windows','wordpress ','xing','xing-square','yahoo','yelp','youtube','youtube-square');
			$table_name = $wpdb->prefix . "cn_social_icon";
			$i = 0;
			foreach ($allSocialMediaIcons as $icon) {
				$results = $wpdb->insert(
					$table_name,
					array(
						'title' => $icon,
						'url' => 'https://'.$icon.'.com/',
						'image_url' => 'fa fa-'.$icon,
						'sortorder' => $i,
						'date_upload' => time(),
						'target' => '1',
					),
					array(
						'%s',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
					)
				);
				$i++;
			}
			*/
		}// end if update

		if ( $_POST['action'] == 'edit' and $_POST['id'] != '' )
		{
			$err = "";
			$msg = "";

			$url = $_POST['url'];
			$target = $_POST['target'];
			$image_file_path = $cnssBaseDir;

			$update = "";
			$type = 1;

			if ($err == '')
			{
				$table_name = $wpdb->prefix . "cn_social_icon";
				$result3 = $wpdb->update(
					$table_name,
					array(
						'title' => sanitize_text_field($_POST['title']),
						'url' => sanitize_text_field($_POST['url']),
						'image_url' => sanitize_text_field($_POST['image_file']),
						'sortorder' => sanitize_text_field($_POST['sortorder']),
						'date_upload' => time(),
						'target' => sanitize_text_field($_POST['target']),
					),
					array( 'id' => sanitize_text_field($_POST['id']) ),
					array(
						'%s',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
					),
					array( '%d' )
				);

				if (false === $result3){
					$err .= "Update fails !". "<br />";
				}
				else
				{
					$msg = "Update successful !". "<br />";
				}
			}

		} // end edit
	}
}

function cnss_social_icon_sort_fn() {
	global $wpdb,$cnssBaseURL;

	$cnss_width = get_option('cnss-width');
	$cnss_height = get_option('cnss-height');

	$image_file_path = $cnssBaseURL;
	$icons = cnss_get_all_icons();

?>
	<div class="wrap">
		<?php echo cnss_esi_review_text(); ?>
		<h2>Sort Icons</h2>

		<div id="ajax-response"></div>
        <div class="content_wrapper">
		<div class="left">

		<noscript>
			<div class="error message">
				<p><?php _e('This plugin can\'t work without javascript, because it\'s use drag and drop and AJAX.', 'cpt') ?></p>
			</div>
		</noscript>

		<div id="order-post-type" style="padding:15px 20px 20px; background:#fff; border:1px solid #ebebeb;">
			<ul id="sortable">
			<?php
			foreach($icons as $icon) {
			?>
					<li id="item_<?php echo $icon->id ?>">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr style="background:#f7f7f7">
						<td style="padding:5px 5px 0;" width="64"><?php echo cnss_get_icon_html($icon->image_url, $icon->title); ?></td>
						<td width="200"><span><?php echo $icon->title;?></span></td>
                        <td align="left" style="text-align:left;"><span><?php echo $icon->url;?></span></td>
					  </tr>
					</table>
					</li>
			<?php } ?>
			</ul>
			<div class="clear"></div>
		</div>

		<p class="submit" style="text-align:center"><input type="submit" id="save-order" class="button-primary" value="<?php _e('Save Changes') ?>" /><?php echo cnss_back_to_link() ?></p>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#sortable").sortable({
					tolerance:'intersect',
					cursor:'pointer',
					items:'li',
					placeholder:'placeholder'
				});
				jQuery("#sortable").disableSelection();
				jQuery("#save-order").bind( "click", function() {
					jQuery.post( ajaxurl, { action:'update-social-icon-order', order:jQuery("#sortable").sortable("serialize") }, function(response) {
						jQuery("#ajax-response").html('<div class="message updated fade"><p>Items Order Updated</p></div>');
						jQuery("#ajax-response div").delay(1000).hide("slow");
					});
				});
			});
		</script>

        </div>
        <div class="right">
        <?php cnss_admin_sidebar(); ?>
        </div>
        </div>
	</div>
<?php
}

function cnss_save_ajax_order() {
	global $wpdb;
	$table_name = $wpdb->prefix . "cn_social_icon";
	parse_str($_POST['order'], $data);
	if (is_array($data)) {
		foreach($data as $key => $values )
		{
			if ( $key == 'item' )
			{
				foreach( $values as $position => $id )
				{
					$wpdb->update( $table_name, array('sortorder' => $position), array('id' => $id) );
				}
			}
		}
	}
}

function cnss_get_icon_html($url = '', $title = '', $width = '', $height = '', $margin = '') {
	if ($url == '') {
		return '<span>Input source invalid.</span>';
	}

	$width  = ($width=='')	?	get_option('cnss-width')	:	$width;
	$height = ($height=='')	?	get_option('cnss-height')	:	$height;
	$icon_output_html = '';

	if ( cnss_is_image_icon($url) ) {
		$imgStyle = '';
		$imgStyle .= ($margin == '') ? '' : 'margin:'.$margin.'px;';
		$imgStyle .= ($width == $height) ? '' : 'height:'.$height.'px;';
		$icon_output_html = '<img src="'.cnss_get_img_url($url).'" border="0" width="'.$width.'" height="'.$height.'" alt="'.$title.'" title="'.$title.'" style="'.$imgStyle.'" />';
	} else {
		$icon_output_html = '<i title="'.$title.'" style="font-size:'.$width.'px;" class="'.$url.'"></i>';
	}
	return $icon_output_html;
}

function cnss_get_img_url($url)
{
	global $cnssBaseURL;
	if ($url == '') {
		return;
	}

	if( strpos($url,'/') === false ) {
		return $cnssBaseURL.'/'.$url;
	} else {
		return $url;
	}
}

function cnss_is_image_icon($url) {
	return ! preg_match('/fa[srb]?\s+fa-[a-z0-9-]+/', $url);
}

function cnss_social_icon_add_fn() {

	global $wpdb,$err,$msg,$cnssBaseURL;

	$social_sites = array(
		"https://500px.com/" => "500px",
		"https://angellist.com/" => "AngelList",
		"https://bandcamp.com/" => "Bandcamp",
		"https://behance.com/" => "Behance",
		"https://bitbucket.org/" => "BitBucket",
		"https://bloglovin.com/" => "Blog Lovin'",
		"https://codepen.com/" => "Codepen",
		"mail:" => "Email",
		"https://delicious.com/" => "Delicious",
		"https://deviantart.com/" => "DeviantArt",
		"https://digg.com/" => "Digg",
		"https://dribbble.com/" => "Dribbble",
		"https://dropbox.com/" => "Dropbox",
		"https://facebook.com/" => "Facebook",
		"https://flickr.com/" => "Flickr",
		"https://foursquare.com/" => "Foursquare",
		"https://github.com/" => "Github",
		"https://plus.google.com/" => "Google+",
		"https://houzz.com/" => "Houzz",
		"https://instagram.com/" => "Instagram",
		"https://itunes.com/" => "iTunes",
		"https://jsfiddle.com/" => "JSFiddle",
		"https://lastfm.com/" => "Last.fm",
		"https://linkedin.com/" => "LinkedIn",
		"https://mixcloud.com/" => "Mixcloud",
		"https://paper-plane.com/" => "Newsletter",
		"https://pinterest.com/" => "Pinterest",
		"https://reddit.com/" => "Reddit",
		"rss" => "RSS",
		"skype" => "Skype",
		"https://snapchat.com/" => "Snapchat",
		"https://soundcloud.com/" => "Soundcloud",
		"https://spotify.com/" => "Spotify",
		"https://stackoverflow.com/" => "Stack Overflow",
		"https://steam.com/" => "Steam",
		"https://stumbleupon.com/" => "Stumbleupon",
		"https://tripadvisor.com/" => "Trip Advisor",
		"https://tumblr.com/" => "Tumblr",
		"https://twitch.com/" => "Twitch",
		"https://twitter.com/" => "Twitter",
		"viber" => "Viber",
		"https://vimeo.com/" => "Vimeo",
		"https://vine.com/" => "Vine",
		"https://vkontakte.com/" => "VK",
		"https://wordpress.com/" => "WordPress",
		"https://xing.com/" => "Xing",
		"https://yelp.com/" => "Yelp",
		"https://youtube.com/" => "YouTube",
		"https://yahoo.com/" => "Yahoo"
	);

	$cnss_width = get_option('cnss-width');
	$cnss_height = get_option('cnss-height');
	$blank_img = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";

	if (isset($_GET['mode'])) {
		if ( $_GET['mode'] == 'edit' and $_GET['id'] != '' ) {

			if( ! is_numeric($_GET['id']) )
				wp_die('Sequrity Issue.');

			$page_title = 'Edit Icon';
			$uptxt = 'Icon';

			$table_name = $wpdb->prefix . "cn_social_icon";
			$image_file_path = $cnssBaseURL;
			$sql = sprintf("SELECT * FROM %s WHERE id=%d", $table_name, $_GET['id']);
			$icon_info = $wpdb->get_row($sql);

			if (!empty($icon_info))
			{
				$id = $icon_info->id;
				$title = $icon_info->title;
				$url = $icon_info->url;
				$image_url = $icon_info->image_url;
				$sortorder = $icon_info->sortorder;
				$target = $icon_info->target;
			}
		}
	}
	else
	{
		$page_title = 'Add New Icon';
		$title = "";
		$url = "";
		$image_url = "";

		$sortorder = count(cnss_get_all_icons());
		$target = "";
		$uptxt = 'Icon';
	}
?>
<?php add_thickbox(); ?>
<div id="cnss-font-awesome-icons-list" style="display:none;">
     <?php include_once 'fa-brand-icons.php'; ?>
</div>
<div class="wrap">
<?php echo cnss_esi_review_text(); ?>
<?php
if($msg!='') echo '<div id="message" class="updated fade">'.$msg.'</div>';
if($err!='') echo '<div id="message" class="error fade">'.$err.'</div>';
?>
<h2><?php echo $page_title;?></h2>
<div class="content_wrapper">
<div class="left">

<script type="text/javascript">
	jQuery(document).ready(function($) {

		$('.fontawesome-icon-list a').click(function(event) {
			event.preventDefault();
			id = $(this).find('i').attr('class');
			$('input#image_file').val(id);
			$('#fa-placeholder').removeClass().addClass(id);
			$('#logoimg').hide();
			$('#fa-placeholder').show();
			$("#TB_closeWindowButton").trigger('click');
		});
	});
</script>
<style type="text/css">
	img#logoimg{max-width: 32px;}
</style>
<form method="post" enctype="multipart/form-data" action="">
    <?php wp_nonce_field('cn_insert_icon'); ?>
    <table class="form-table">
        <tr valign="top">
			<th scope="row">Title<em>*</em></th>
			<td>
				<input list="title-autofill" type="text" name="title" id="title" class="regular-text" value="<?php echo $title?>" /><br /><i>Type few char for suggestions</i>
				<datalist style="display: none;" id="title-autofill">
				<?php foreach ($social_sites as $key => $value) { ?>
				  <option value="<?php echo $value; ?>">
				<?php } ?>
			</td>
        </tr>

        <tr valign="top">
			<th scope="row"><?php echo $uptxt;?><em>*</em></th>
			<td>
				<i id="fa-placeholder" class="fa <?php echo $image_url; ?>" aria-hidden="true" style="font-size: 2em;"></i>

				<img id="logoimg" style="vertical-align:top" src="<?php echo cnss_is_image_icon($image_url)?$image_url:$blank_img; ?>" border="0" width="<?php //echo $cnss_width; ?>"  height="<?php //echo $cnss_height; ?>" alt="<?php echo $title; ?>" />

				<a title="Choose Font Awesome Icon (Version 5.7.2)" href="#TB_inline?width=600&height=500&inlineId=cnss-font-awesome-icons-list" class="thickbox button">Choose From FontAwesome Icon </a>
				<span style="vertical-align:middle;">or</span>
				<input style="vertical-align:top" id="logo_image_button" class="button" type="button" value="Upload Your Own Image Icon" />

				<input style="vertical-align:top" type="hidden" name="image_file" id="image_file" class="regular-text" value="<?php echo $image_url ?>" readonly="readonly" />
				<p><em>You can download image icon from <a target="_blank" href="http://www.cybernetikz.com/wordpress-magento-plugins/wordpress-plugins/easy-social-icons/#inputbox-srcfield">here</a></em></p>
			</td>
        </tr>

        <tr valign="top">
			<th scope="row">URL<em>*</em></th>
			<td><input list="url-autofill" type="text" name="url" id="url" class="regular-text" value="<?php echo $url?>" />
			<datalist style="display: none;" id="url-autofill">
			<?php foreach ($social_sites as $key => $value) { ?>
			  <option value="<?php echo $key; ?>">
			<?php } ?>
			</datalist><br /><i>Type few char for suggestions &ndash; don't forget the <strong><code>http(s)://</code></strong></i></td>
        </tr>

        <tr valign="top">
			<th scope="row">Sort Order</th>
			<td>
				<input type="number" name="sortorder" id="sortorder" class="small-text" value="<?php echo $sortorder?>" />
			</td>
        </tr>

		<tr valign="top">
			<th scope="row">Target</th>
			<td>
				<input type="radio" name="target" id="new" checked="checked" value="1" />&nbsp;<label for="new">Open new window</label>&nbsp;<br />
				<input type="radio" name="target" id="same" value="0" />&nbsp;<label for="same">Open same window</label>&nbsp;
			</td>
        </tr>
    </table>

	<?php if (isset($_GET['mode']) ) { ?>
	<input type="hidden" name="action" value="edit" />
	<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
	<?php } else {?>
	<input type="hidden" name="action" value="update" />
	<?php } ?>

    <p class="submit" style="text-align:center"><input id="submit_button" name="submit_button" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /><?php echo cnss_back_to_link() ?></p>
</form>
</div>
<div class="right">
<?php cnss_admin_sidebar(); ?>
</div>
</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('form').submit(function(event) {
			if ($('#url').val() == '' ||
				$('#image_file').val() == '' ||
				$('#title').val() == '') {
				event.preventDefault();
				alert('Please input Title, Icon & Url field(s)');
			}
		});
	});
</script>
<?php
}

function cnss_back_to_link()
{
	return '&nbsp;&nbsp;<a href="admin.php?page=cnss_social_icon_page"><input type="button" class="button-secondary" value="All Icons" /></a><small>&nbsp;&larr;Back to</small>';
}

function cnss_manage_icon_table_header() {
	return '
	<th class="manage-column column-title" scope="col" width="20">ID</th>
	<th class="manage-column column-title" scope="col">Title</th>
	<th class="manage-column column-title" scope="col">URL</th>
	<th class="manage-column column-title" scope="col" width="100">Open In</th>
	<th class="manage-column column-title" scope="col" width="100">Icons</th>
	<th class="manage-column column-title" scope="col" width="60"><a href="admin.php?page=cnss_social_icon_sort">Order <i class="fa fa-sort"></i></a></th>
	<th class="manage-column column-title" scope="col" width="50">Action</th>
	<th class="manage-column column-title" scope="col" width="50">Action</th>
	';
}

function cnss_esi_review_text()
{
	return '<div class="cnss-esi-review"><p><span>Please <a target="_blank" href="https://wordpress.org/support/plugin/easy-social-icons/reviews/">review</a> this plugin</span><span style="float: right;">Need support please <a target="_blank" href="http://www.cybernetikz.com/wordpress-magento-plugins/wordpress-plugins/easy-social-icons/#disqus_thread">contact us here</a></span></p></div>';
}

function cnss_social_icon_page_fn() {

	global $wpdb,$cnssBaseURL;

	$cnss_width = get_option('cnss-width');
	$cnss_height = get_option('cnss-height');

	$image_file_path = $cnssBaseURL;
	$icons = cnss_get_all_icons();
	?>
	<div class="wrap">
		<?php echo cnss_esi_review_text(); ?>
		<h1 style="margin-bottom: 10px;" class="wp-heading-inline">Social Icons</h1> <a href="admin.php?page=cnss_social_icon_add" class="page-title-action">Add New</a>
		<script type="text/javascript">
			function show_confirm(title, id)
			{
				var rpath1 = "";
				var rpath2 = "";
				var r=confirm('Are you confirm to delete "'+title+'"');
				if (r==true)
				{
					rpath1 = '<?php echo $_SERVER['PHP_SELF'].'?page=cnss_social_icon_page'; ?>';
					rpath2 = '&cnss-delete=y&id='+id;
					window.location = rpath1+rpath2;
				}
			}
		</script>
		<div class="content_wrapper">
			<div class="left">
				<table class="widefat page fixed" cellspacing="0">
					<thead>
						<tr valign="top">
							<?php echo cnss_manage_icon_table_header(); ?>
						</tr>
					</thead>

					<tbody>
					<?php
					if ($icons) {
						foreach($icons as $icon) {
					?>
						<tr valign="top">
							<td>
								<?php echo $icon->id;?>
							</td>
							<td>
								<?php echo $icon->title;?>
							</td>
							<td>
								<a target="_blank" href="<?php echo $icon->url;?>"><?php echo $icon->url;?></a>
							</td>
							<td>
								<?php echo $icon->target==1?'New Window':'Same Window' ?>
							</td>
							<td>
								<?php echo cnss_get_icon_html($icon->image_url, $icon->title); ?>
							</td>
							<td align="center">
								<?php echo $icon->sortorder;?>
							</td>
							<td align="center">
								<a title="Edit <?php echo $icon->title;?>" href="?page=cnss_social_icon_add&mode=edit&id=<?php echo $icon->id;?>"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i></a>
							</td>
							<td align="center">
								<a title="Delete <?php echo $icon->title;?>" onclick="show_confirm('<?php echo addslashes($icon->title)?>','<?php echo $icon->id;?>');" href="#delete"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></a>
							</td>
						</tr>
					<?php
						} //endforeach
					} else {
						echo '<tr valign="top"><td align="center" colspan="8">No icon found, please <a href="admin.php?page=cnss_social_icon_add">Add New</a> icon</td></tr>';
					}
					?>
					</tbody>
					<tfoot>
						<tr valign="top">
							<?php echo cnss_manage_icon_table_header(); ?>
						</tr>
					</tfoot>
				</table>
				<h4>Please visit <a href="admin.php?page=cnss_social_icon_option#shortcode">How to use</a> or <a href="admin.php?page=cnss_social_icon_option">Settings</a> page</h4>
			</div>
			<div class="right">
	        	<?php cnss_admin_sidebar(); ?>
			</div>
		</div>
	</div>
	<?php
}

function cn_social_icon_table() {

	$cnss_width = get_option('cnss-width');
	$cnss_height = get_option('cnss-height');
	$cnss_margin = get_option('cnss-margin');
	$cnss_rows = get_option('cnss-row-count');
	$vorh = get_option('cnss-vertical-horizontal');

	global $wpdb,$cnssBaseURL;
	$table_name = $wpdb->prefix . "cn_social_icon";
	$image_file_path = $cnssBaseURL;
	$sql = "SELECT * FROM ".$table_name." WHERE image_url<>'' AND url<>'' ORDER BY sortorder";
	$icons = $wpdb->get_results($sql);
	$icon_count = count($icons);

	$_collectionSize = count($icons);
	$_rowCount = $cnss_rows ? $cnss_rows : 1;
	$_columnCount = ceil($_collectionSize/$_rowCount);

	if($vorh=='vertical')
		$table_width = $cnss_width;
	else
		$table_width = $_columnCount*($cnss_width+$cnss_margin);

	$td_width = $cnss_width+$cnss_margin;

	ob_start();
	echo '<table class="cnss-social-icon" style="width:'.$table_width.'px" border="0" cellspacing="0" cellpadding="0">';
	$i=0;
	foreach($icons as $icon)
	{

	echo $vorh=='vertical'?'<tr>':'';
	if($i++%$_columnCount==0 && $vorh!='vertical' )echo '<tr>';
	?><td style="width:<?php echo $td_width ?>px"><a <?php echo ($icon->target==1)?'target="_blank"':'' ?> title="<?php echo $icon->title ?>" href="<?php echo $icon->url ?>"><?php echo cnss_get_icon_html($icon->image_url, $icon->title); ?></a></td><?php
	if ( ($i%$_columnCount==0 || $i==$_collectionSize) && $vorh!='vertical' )echo '</tr>';
	echo $vorh=='vertical'?'</tr>':'';
	}
	echo '</table>';
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}

function cnss_format_title($str) {
	$pattern = '/[^a-zA-Z0-9]/';
	return strtolower(preg_replace($pattern,'-',$str));
}

function cnss_format_class($str) {
	return str_replace(array('fa ','fab ','fas ','far ','fa-'), array('','','','','cnss-'), $str);
}

function cn_social_icon($attr = array(), $call_from_widget = NULL) {

	global $wpdb, $cnssBaseURL;
	$image_file_path = $cnssBaseURL;
	$attr_id = isset($attr['attr_id'])?$attr['attr_id']:'';
	$attr_class = isset($attr['attr_class'])?$attr['attr_class']:'';
	$where_sql = "";

	if(isset($attr['selected_icons']))
	{
		if(is_string($attr['selected_icons'])) {
			$attr['selected_icons'] = preg_replace('/[^0-9,]/','',$attr['selected_icons']);
			$attr['selected_icons'] = explode(',', $attr['selected_icons']);
		}

		if(is_array($attr['selected_icons'])) {
			$where_sql .= ' AND `id` IN(';
			foreach($attr['selected_icons'] as $iid)
			{
				$where_sql .= $iid.',';
			}
			$where_sql = rtrim($where_sql,',');
			$where_sql .= ') ';
		}
	}

	$cnss_width = isset($attr['width'])?$attr['width']:get_option('cnss-width');
	$cnss_height = isset($attr['height'])?$attr['height']:get_option('cnss-height');
	$cnss_margin = isset($attr['margin'])?$attr['margin']:get_option('cnss-margin');
	$cnss_rows = get_option('cnss-row-count');
	$vorh = isset($attr['display'])?$attr['display']:get_option('cnss-vertical-horizontal');
	$text_align = isset($attr['alignment'])?$attr['alignment']:get_option('cnss-text-align');

	// settings for font-awesome icons
	$icon_bg_color = cn_esi_get_option('cnss-icon-bg-color');
	$icon_color = cn_esi_get_option('cnss-icon-color');
	$icon_hover_color = cn_esi_get_option('cnss-icon-hover-color');
	$icon_shape = cn_esi_get_option('cnss-icon-shape');
	$cnss_original_icon_color = cn_esi_get_option('cnss-original-icon-color');

	$table_name = $wpdb->prefix . "cn_social_icon";
	$sql = "SELECT * FROM ".$table_name." WHERE image_url<>'' AND url<>'' $where_sql ORDER BY sortorder";
	$icons = $wpdb->get_results($sql);
	$icon_count = count($icons);
	$li_margin = round($cnss_margin/2);

	ob_start();
	echo '<ul id="'.$attr_id.'" class="cnss-social-icon '.$attr_class.'" style="text-align:'.$text_align.';">';
	$i=0;
	foreach($icons as $icon)
	{
		$aStyle = '';
		$liClass = 'cn-fa-'.cnss_format_title($icon->title);
		$aClass = '';
		$liStyle = ($vorh == 'horizontal') ? 'display:inline-block;' : '';
		$aTarget = ($icon->target ==1 ) ? 'target="_blank"' : '';
		if ( !cnss_is_image_icon($icon->image_url) ) {
			$liClass .= " cn-fa-icon ";
			$aPadding = round($cnss_width/4);
			$aWidth = $cnss_width + $aPadding*2;
			$aHeight = $aWidth;
			$aStyle .= "width:{$aWidth}px;";
			$aStyle .= "height:{$aHeight}px;";
			$aStyle .= "padding:{$aPadding}px 0;";
			$aStyle .= "margin:{$li_margin}px;";
			$aStyle .= "color: {$icon_color};";
			if ($cnss_original_icon_color == '1') {
				$aClass = cnss_format_class($icon->image_url);
			} else {
				//$aStyle .= "background-color:{$icon_bg_color};";
			}
			if ($icon_shape == 'circle') {
				$borderRadius = '50%';
			} elseif ($icon_shape == 'round-corner') {
				$borderRadius = '10%';
			} else {
				$borderRadius = '0%';
			}
			$aStyle .= "border-radius: {$borderRadius};";
		}
	?><li class="<?php echo $liClass; ?>" style="<?php echo $liStyle; ?>"><a class="<?php echo $aClass; ?>" <?php echo $aTarget ?> href="<?php echo $icon->url ?>" title="<?php echo $icon->title ?>" style="<?php echo $aStyle ?>"><?php echo cnss_get_icon_html($icon->image_url, $icon->title, $cnss_width, $cnss_height, $li_margin); ?></a></li><?php
	$i++;
	}
	echo '</ul>';
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}

function _cn_social_icon_sc( $selected_icons_array = array() ) {

		global $wpdb,$cnssBaseURL;

		$cnss_width = get_option('cnss-width');
		$cnss_height = get_option('cnss-height');
		$image_file_path = $cnssBaseURL;

		$icons = cnss_get_all_icons();
		$icon_count = count($icons);

		ob_start();
		echo '<ul class="cnss-social-icon-admin" style="text-align:left;">'."\r\n";
		$i=0;
		foreach($icons as $icon)
		{
			?><li style="display:inline-block; padding:2px 8px; border:1px dotted #ccc;">
            <div style="text-align: center; width: <?php echo $cnss_width ?>px;">
            	<label for="icon<?php echo $icon->id; ?>">
            		<?php echo cnss_get_icon_html($icon->image_url, $icon->title); ?>
            	</label>
            </div>
            <div style="text-align: center;"><input <?php if( in_array($icon->id, $selected_icons_array ) ) echo 'checked="checked"'; ?> style="margin:0;" type="checkbox" name="_selected_icons[]" id="icon<?php echo $icon->id; ?>" value="<?php echo $icon->id; ?>" /></div>
			</li>
			<?php
			$i++;
		}
		echo '</ul>'."\r\n";
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

class Cnss_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'cnss_widget', // Base ID
			'Easy Social Icons', // Name
			array( 'description' => __( 'Add social media icons to your Sidebar.' ) ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo cn_social_icon($instance, 1);
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['attr_id'] = strip_tags( $new_instance['attr_id'] );
		$instance['attr_class'] = strip_tags( $new_instance['attr_class'] );
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['height'] = strip_tags( $new_instance['height'] );
		$instance['margin'] = strip_tags( $new_instance['margin'] );
		$instance['display'] = strip_tags( $new_instance['display'] );
		$instance['alignment'] = strip_tags( $new_instance['alignment'] );
		$instance['selected_icons'] = $new_instance['selected_icons'];
		return $instance;
	}

	public function form( $instance ) {

		$cnss_width = get_option('cnss-width');
		$cnss_height = get_option('cnss-height');
		$cnss_margin = get_option('cnss-margin');
		$cnss_rows = get_option('cnss-row-count');
		$vorh = get_option('cnss-vertical-horizontal');
		$text_align = get_option('cnss-text-align');

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Follow Us' );
		}
		$instance[ 'alignment' ] = isset($instance[ 'alignment' ])?$instance[ 'alignment' ]:$text_align;
		$instance[ 'display' ] = isset($instance[ 'display' ])?$instance[ 'display' ]:$vorh;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        <p><em>Following settings will override the default <a href="admin.php?page=cnss_social_icon_option">Icon Settings</a></em></p>
		<table width="100%" border="0">
          <tr>
            <td><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Icon Width <em>(px)</em>:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="number" value="<?php echo esc_attr( isset($instance[ 'width' ])?$instance[ 'width' ]:$cnss_width ); ?>" /></td>
            <td>&nbsp;</td>
            <td><label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Icon Height <em>(px)</em>:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="number" value="<?php echo esc_attr( isset($instance[ 'height' ])?$instance[ 'height' ]:$cnss_height ); ?>" /></td>
          </tr>
        </table>

		<table width="100%" border="0">
          <tr>
            <td><label for="<?php echo $this->get_field_id( 'alignment' ); ?>"><?php _e( 'Alignment:' ); ?></label><br />
                <select id="<?php echo $this->get_field_id( 'alignment' ); ?>" name="<?php echo $this->get_field_name( 'alignment' ); ?>">
                    <option <?php selected( $instance[ 'alignment' ], 'center' ); ?> value="center">Center</option>
                    <option <?php selected( $instance[ 'alignment' ], 'left' ); ?> value="left">Left</option>
                    <option <?php selected( $instance[ 'alignment' ], 'right' ); ?> value="right">Right</option>
                </select></td>
        	<td>&nbsp;</td>
            <td><label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Display:' ); ?></label><br />
                <select id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
                    <option <?php selected( $instance[ 'display' ], 'horizontal' ); ?> value="horizontal">Horizontally</option>
                    <option <?php selected( $instance[ 'display' ], 'vertical' ); ?> value="vertical">Vertically</option>
                </select></td>
        	<td>&nbsp;</td>
            <td><label for="<?php echo $this->get_field_id( 'margin' ); ?>"><?php _e( 'Margin <em>(px)</em>:' ); ?></label><br />
		<input maxlength="3" class="widefat" id="<?php echo $this->get_field_id( 'margin' ); ?>" name="<?php echo $this->get_field_name( 'margin' ); ?>" type="number" value="<?php echo esc_attr( isset($instance[ 'margin' ])?$instance[ 'margin' ]:$cnss_margin ); ?>" /></td>
          </tr>
        </table>

        <p>
        <label><?php _e( 'Select Social Icons:' ); ?></label> <em>(If select none all icons will be displayed)</em><br />
		<?php echo $this->_cn_social_icon_widget( isset($instance['selected_icons'])?$instance['selected_icons']:array() ); ?>
        </p>

		<table style="margin-bottom:15px;" width="100%" border="0">
          <tr>
            <td><label for="<?php echo $this->get_field_id( 'attr_id' ); ?>"><?php _e( 'Add Custom ID:' ); ?></label>
            <input class="widefat" placeholder="ID" id="<?php echo $this->get_field_id( 'attr_id' ); ?>" name="<?php echo $this->get_field_name( 'attr_id' ); ?>" type="text" value="<?php echo esc_attr( isset($instance[ 'attr_id' ])?$instance[ 'attr_id' ]:'' ); ?>" /></td>
            <td>&nbsp;</td>
            <td><label for="<?php echo $this->get_field_id( 'attr_class' ); ?>"><?php _e( 'Add Custom Class:' ); ?></label>
            <input class="widefat" placeholder="Class" id="<?php echo $this->get_field_id( 'attr_class' ); ?>" name="<?php echo $this->get_field_name( 'attr_class' ); ?>" type="text" value="<?php echo esc_attr( isset($instance[ 'attr_class' ])?$instance[ 'attr_class' ]:'' ); ?>" /></td>
          </tr>
        </table>
        <?php
	}

	public function _cn_social_icon_widget( $selected_icons_array = array() ) {

		global $wpdb,$cnssBaseURL;

		$cnss_width = get_option('cnss-width');
		$cnss_height = get_option('cnss-height');
		$image_file_path = $cnssBaseURL;

		$icons = cnss_get_all_icons();
		$icon_count = count($icons);

		ob_start();
		if ($icons) {
			echo '<ul class="cnss-social-icon-admin-widget" style="text-align:left;">'."\r\n";
			$i=0;
			foreach($icons as $icon)
			{
				?><li style="display:inline-block; padding:2px 8px; border:1px dashed #ccc;">
	            <div style="text-align: center; width: <?php echo $cnss_width ?>px;">
	            	<label for="<?php echo $this->get_field_id( 'selected_icons'.$icon->id ); ?>"><?php echo cnss_get_icon_html($icon->image_url, $icon->title); ?>
	            	</label>
	            </div>
	            <div style="text-align: center;"><input <?php if( in_array($icon->id, $selected_icons_array ) ) echo 'checked="checked"'; ?> style="margin:0;" type="checkbox" name="<?php echo $this->get_field_name( 'selected_icons' ); ?>[]" id="<?php echo $this->get_field_id( 'selected_icons'.$icon->id ); ?>" value="<?php echo $icon->id; ?>" /></div>
				</li>
				<?php
				$i++;
			}
			echo '</ul>'."\r\n";
		} else {
			echo 'No icon found, please <a href="admin.php?page=cnss_social_icon_add" class="page-title-action">Add New</a> icon.';
		}
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
} // class Cnss_Widget

if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
	add_action( 'widgets_init', function ()	{
		register_widget( "Cnss_Widget" );
	});
} else {
	add_action( 'widgets_init', create_function( '', 'register_widget( "Cnss_Widget" );' ) );
}