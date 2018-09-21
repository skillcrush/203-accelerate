<?php
/**
 * The feedback model class for ThemeIsle SDK
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
if ( ! class_exists( 'ThemeIsle_SDK_Feedback' ) ) :
	/**
	 * Feedback model for ThemeIsle SDK.
	 */
	abstract class ThemeIsle_SDK_Feedback {
		/**
		 * @var ThemeIsle_SDK_Product $product Themeisle Product.
		 */
		protected $product;

		/**
		 * @var string $feedback_url Url where to send the feedback
		 */
		private $feedback_url = 'http://feedback.themeisle.com/wordpress/wp-json/__pirate_feedback_/v1/feedback';

		/**
		 * ThemeIsle_SDK_Feedback constructor.
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
		 * Calls the API
		 *
		 * @param string $attributes The attributes of the post body.
		 */
		protected function call_api( $attributes ) {
			$slug                  = $this->product->get_slug();
			$version               = $this->product->get_version();
			$attributes['slug']    = $slug;
			$attributes['version'] = $version;

			$response = wp_remote_post(
				$this->feedback_url, array(
					'body' => $attributes,
				)
			);
		}

		/**
		 * Randomizes the options array
		 *
		 * @param array $options The options array.
		 */
		function randomize_options( $options ) {
			$new  = array();
			$keys = array_keys( $options );
			shuffle( $keys );

			foreach ( $keys as $key ) {
				$new[ $key ] = $options[ $key ];
			}

			return $new;
		}

		/**
		 * Abstract function for delegating to the child
		 */
		protected abstract function setup_hooks_child();

	}
endif;
