<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
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
	'cnss-icon-bg-hover-color' => '#ffffff',
	'cnss-icon-color' => '#ffffff',
	'cnss-icon-hover-color' => '#ffffff',
	'cnss-icon-shape' => 'square',
	'cnss-original-icon-color' => '1'
);

foreach ($cnss_esi_settings as $key => $value) {
	delete_option(trim($key));
}
 
global $wpdb;
$table_name = $wpdb->prefix . "cn_social_icon";
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");
