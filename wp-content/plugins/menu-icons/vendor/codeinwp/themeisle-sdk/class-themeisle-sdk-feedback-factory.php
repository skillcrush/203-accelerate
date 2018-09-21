<?php
/**
 * The feedback factory class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Feedback
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'ThemeIsle_SDK_Feedback_Factory' ) ) :
	/**
	 * Feedback model for ThemeIsle SDK.
	 */
	class ThemeIsle_SDK_Feedback_Factory {

		/**
		 * @var array $instances collection of the instances that are registered with the factory
		 */
		private $_instances = array();

		/**
		 * ThemeIsle_SDK_Feedback_Factory constructor.
		 *
		 * @param ThemeIsle_SDK_Product $product_object Product Object.
		 * @param array                 $feedback_types the feedback types.
		 */
		public function __construct( $product_object, $feedback_types ) {
			if ( $product_object instanceof ThemeIsle_SDK_Product && $feedback_types && is_array( $feedback_types ) ) {
				foreach ( $feedback_types as $type ) {
					$class                     = 'ThemeIsle_SDK_Feedback_' . ucwords( $type );
					$instance                  = new $class( $product_object );
					$this->_instances[ $type ] = $instance;
					$instance->setup_hooks();
				}
			}
		}

		/**
		 * Get the registered instances
		 */
		public function get_instances() {
			return $this->_instances;
		}
	}
endif;
