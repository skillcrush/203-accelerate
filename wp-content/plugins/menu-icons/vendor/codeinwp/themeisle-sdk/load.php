<?php
/**
 * Loader for the ThemeIsleSDK
 *
 * Logic for loading always the latest SDK from the installed themes/plugins.
 *
 * @package     ThemeIsleSDK
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */

// Current SDK version and path.
$themeisle_sdk_version = '2.1.1';
$themeisle_sdk_path    = dirname( __FILE__ );

global $themeisle_sdk_max_version;
global $themeisle_sdk_max_path;

if ( version_compare( $themeisle_sdk_version, $themeisle_sdk_max_version ) >= 0 ) {
	$themeisle_sdk_max_version = $themeisle_sdk_version;
	$themeisle_sdk_max_path    = $themeisle_sdk_path;
}

// load the latest sdk version from the active Themeisle products
if ( ! function_exists( 'themeisle_sdk_load_latest' ) ) :
	/**
	 * Always load the latest sdk version.
	 */
	function themeisle_sdk_load_latest() {
		global $themeisle_sdk_max_path;
		require_once $themeisle_sdk_max_path . '/start.php';
	}
endif;
add_action( 'init', 'themeisle_sdk_load_latest' );
