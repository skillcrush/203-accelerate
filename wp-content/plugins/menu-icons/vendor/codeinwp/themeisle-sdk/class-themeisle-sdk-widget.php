<?php
/**
 * The widget model class for ThemeIsle SDK
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
if ( ! class_exists( 'ThemeIsle_SDK_Widget' ) ) :
	/**
	 * Widget model for ThemeIsle SDK.
	 */
	abstract class ThemeIsle_SDK_Widget {
		/**
		 * @var ThemeIsle_SDK_Product $product Themeisle Product.
		 */
		protected $product;

		/**
		 * ThemeIsle_SDK_Widget constructor.
		 *
		 * @param ThemeIsle_SDK_Product $product_object Product Object.
		 */
		public function __construct( $product_object ) {
			if ( $product_object instanceof ThemeIsle_SDK_Product ) {
				$this->product = $product_object;
			}
			$this->setup_hooks();
		}

		/**
		 * Registers the hooks and then delegates to the child
		 */
		public function setup_hooks() {
			$this->setup_hooks_child();
		}

		/**
		 * Abstract function for delegating to the child
		 */
		protected abstract function setup_hooks_child();

	}
endif;
