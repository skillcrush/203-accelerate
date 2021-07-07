<?php
/**
 * File responsible for sdk files loading.
 *
 * @package     ThemeIsleSDK
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */
$products      = apply_filters( 'themeisle_sdk_products', array() );
$path          = dirname( __FILE__ );
$files_to_load = array(
	'class-themeisle-sdk-loader.php',
	'class-themeisle-sdk-product.php',
	'class-themeisle-sdk-logger.php',
	'class-themeisle-sdk-licenser.php',
	'class-themeisle-sdk-rollback.php',
	'class-themeisle-sdk-feedback-factory.php',
	'class-themeisle-sdk-feedback.php',
	'class-themeisle-sdk-feedback-deactivate.php',
	'class-themeisle-sdk-feedback-review.php',
	'class-themeisle-sdk-feedback-translate.php',
	'class-themeisle-sdk-notification-manager.php',
	'class-themeisle-sdk-widget.php',
	'class-themeisle-sdk-widget-dashboard-blog.php',
	'class-themeisle-sdk-widgets-factory.php',
	'class-themeisle-sdk-endpoints.php',
);

foreach ( $files_to_load as $file ) {
	$file_path = $path . '/' . $file;
	if ( is_readable( $file_path ) ) {
		require_once $file_path;
	}
}
foreach ( $products as $product ) {
	ThemeIsle_SDK_Loader::init_product( $product );
}
