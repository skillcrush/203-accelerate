<?php
/**
 * The widgets factory class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Widgets
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'ThemeIsle_SDK_Widgets_Factory' ) ) :
	/**
	 * Widgets factory model for ThemeIsle SDK.
	 */
	class ThemeIsle_SDK_Widgets_Factory {

		/**
		 * ThemeIsle_SDK_Widgets_Factory constructor.
		 *
		 * @param ThemeIsle_SDK_Product $product_object Product Object.
		 * @param array                 $widgets the widgets.
		 */
		public function __construct( $product_object, $widgets ) {
			if ( $product_object instanceof ThemeIsle_SDK_Product && $widgets && is_array( $widgets ) ) {
				foreach ( $widgets as $widget ) {
					$class    = 'ThemeIsle_SDK_Widget_' . str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $widget ) ) );
					$instance = new $class( $product_object );
					$instance->setup_hooks();
				}
			}
		}
	}
endif;
