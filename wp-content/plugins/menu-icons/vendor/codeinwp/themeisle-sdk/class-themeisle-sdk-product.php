<?php
/**
 * The product model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Product
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'ThemeIsle_SDK_Product' ) ) :
	/**
	 * Product model for ThemeIsle SDK.
	 */
	class ThemeIsle_SDK_Product {
		/**
		 * @var string $slug THe product slug.
		 */
		private $slug;
		/**
		 * @var string $basefile The file with headers.
		 */
		private $basefile;
		/**
		 * @var string $type The product type ( plugin | theme ).
		 */
		private $type;
		/**
		 * @var string $file The file name.
		 */
		private $file;
		/**
		 * @var string $name The product name.
		 */
		private $name;
		/**
		 * @var string $key The product ready key.
		 */
		private $key;
		/**
		 * @var string $author_url The url of the author.
		 */
		private $author_url;
		/**
		 * @var string $store_url The store url.
		 */
		private $store_url;
		/**
		 * @var int $install The date of install.
		 */
		private $install;
		/**
		 * @var string $store_name The store name.
		 */
		private $store_name;
		/**
		 * @var array $allowed_authors The allowed authors.
		 */
		private $allowed_authors = array( 'proteusthemes.com', 'anarieldesign.com', 'prothemedesign.com', 'cssigniter.com' );
		/**
		 * @var bool $requires_license Either user needs to activate it with license.
		 */
		private $requires_license;
		/**
		 * @var bool $wordpress_available Either is available on WordPress or not.
		 */
		private $wordpress_available;
		/**
		 * @var string $version The product version.
		 */
		private $version;
		/**
		 * @var string $logger_option Logger option key.
		 */
		public $logger_option;
		/**
		 * @var string $pro_slug Pro slug, if available.
		 */
		public $pro_slug;
		/**
		 * @var string $feedback_types All the feedback types the product supports
		 */
		private $feedback_types = array();

		/**
		 * @var string $widget_types All the widget types the product supports
		 */
		private $widget_types = array( 'dashboard_blog' );

		/**
		 * ThemeIsle_SDK_Product constructor.
		 *
		 * @param string $basefile Product basefile.
		 */
		public function __construct( $basefile ) {
			if ( ! empty( $basefile ) ) {
				if ( is_readable( $basefile ) ) {
					$this->basefile = $basefile;
					$this->setup_from_path();
					$this->setup_from_fileheaders();
				}
			}
			$install = get_option( $this->get_key() . '_install', 0 );
			if ( $install === 0 ) {
				$install = time();
				update_option( $this->get_key() . '_install', time() );
			}
			$this->install = $install;

			$this->logger_option = $this->get_key() . '_logger_flag';

		}

		/**
		 * Setup props from fileheaders.
		 */
		public function setup_from_fileheaders() {
			$file_headers = array(
				'Requires License'    => 'Requires License',
				'WordPress Available' => 'WordPress Available',
				'Pro Slug'            => 'Pro Slug',
				'Version'             => 'Version',
			);
			if ( $this->type == 'plugin' ) {
				$file_headers['Name']       = 'Plugin Name';
				$file_headers['AuthorName'] = 'Author';
				$file_headers['AuthorURI']  = 'Author URI';
			}
			if ( $this->type == 'theme' ) {
				$file_headers['Name']       = 'Theme Name';
				$file_headers['AuthorName'] = 'Author';
				$file_headers['AuthorURI']  = 'Author URI';
			}
			$file_headers = get_file_data( $this->basefile, $file_headers );

			$this->name       = $file_headers['Name'];
			$this->store_name = $file_headers['AuthorName'];
			$this->author_url = $file_headers['AuthorURI'];
			$this->store_url  = $file_headers['AuthorURI'];
			if ( $this->is_external_author() ) {
				$this->store_url  = 'https://themeisle.com';
				$this->store_name = 'ThemeIsle';
			}
			$this->requires_license    = ( $file_headers['Requires License'] == 'yes' ) ? true : false;
			$this->wordpress_available = ( $file_headers['WordPress Available'] == 'yes' ) ? true : false;
			$this->pro_slug            = ! empty( $file_headers['Pro Slug'] ) ? $file_headers['Pro Slug'] : '';
			$this->version             = $file_headers['Version'];
			if ( $this->require_uninstall_feedback() ) {
				$this->feedback_types[] = 'deactivate';
			}
			if ( $this->is_wordpress_available() ) {
				$this->feedback_types[] = 'review';
				$this->feedback_types[] = 'translate';
			}
		}

		/**
		 * Check if the product is by external author or not.
		 *
		 * @return bool Either is external author or no.
		 */
		public function is_external_author() {
			foreach ( $this->allowed_authors as $author ) {
				if ( strpos( $this->author_url, $author ) !== false ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * The magic var_dump info method.
		 *
		 * @return array Debug info.
		 */
		public function __debugInfo() {
			return array(
				'name'                => $this->name,
				'slug'                => $this->slug,
				'version'             => $this->version,
				'basefile'            => $this->basefile,
				'key'                 => $this->key,
				'type'                => $this->type,
				'store_name'          => $this->store_name,
				'store_url'           => $this->store_url,
				'wordpress_available' => $this->wordpress_available,
				'requires_license'    => $this->requires_license,
			);

		}

		/**
		 * Setup props from path.
		 */
		public function setup_from_path() {
			$this->file = basename( $this->basefile );
			$dir        = dirname( $this->basefile );
			$this->slug = basename( $dir );
			$exts       = explode( '.', $this->basefile );
			$ext        = $exts[ count( $exts ) - 1 ];
			if ( $ext == 'css' ) {
				$this->type = 'theme';
			}
			if ( $ext == 'php' ) {
				$this->type = 'plugin';
			}
			$this->key = self::key_ready_name( $this->slug );
		}

		/**
		 * @param string $string the String to be normalized for cron handler.
		 *
		 * @return string $name         The normalized string.
		 */
		static function key_ready_name( $string ) {
			return str_replace( '-', '_', strtolower( trim( $string ) ) );
		}

		/**
		 * Getter for product name.
		 *
		 * @return string The product name.
		 */
		public function get_name() {
			return $this->name;
		}

		/**
		 * Getter for product version.
		 *
		 * @return string The product version.
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * If product is available on wordpress.org or not.
		 *
		 * @return bool Either is wp available or not.
		 */
		public function is_wordpress_available() {
			return $this->wordpress_available;
		}

		/**
		 * @return array Array of available versions.
		 */
		private function get_plugin_versions() {

			$url      = sprintf( 'https://api.wordpress.org/plugins/info/1.0/%s', $this->get_slug() );
			$response = wp_remote_get( $url );
			if ( is_wp_error( $response ) ) {
				return array();
			}
			$response = wp_remote_retrieve_body( $response );
			$response = maybe_unserialize( $response );

			if ( ! is_object( $response ) ) {
				return array();
			}
			if ( ! isset( $response->versions ) ) {
				return array();
			}
			$versions = array();
			foreach ( $response->versions as $version => $zip ) {
				$versions[] = array(
					'version' => $version,
					'url'     => $zip,
				);
			}

			return $versions;
		}

		/**
		 * Return theme versions.
		 *
		 * @return array Theme versions array.
		 */
		public function get_theme_versions() {
			$url      = sprintf( 'https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=%s&request[fields][versions]=true', $this->get_slug() );
			$response = wp_remote_get( $url );
			if ( is_wp_error( $response ) ) {
				return array();
			}
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response );

			if ( ! is_object( $response ) ) {
				return array();
			}
			if ( ! isset( $response->versions ) ) {
				return array();
			}
			$versions = array();
			foreach ( $response->versions as $version => $zip ) {
				$versions[] = array(
					'version' => $version,
					'url'     => $zip,
				);
			}

			return $versions;
		}

		/**
		 * Get versions array from wp.org
		 *
		 * @return array Array of versions.
		 */
		private function get_api_versions() {
			if ( ! $this->is_wordpress_available() ) {
				return array();
			}

			$cache_key      = $this->get_key() . '_' . preg_replace( '/[^0-9a-zA-Z ]/m', '', $this->version ) . 'versions';
			$cache_versions = get_transient( $this->get_key() . '_' . preg_replace( '/[^0-9a-zA-Z ]/m', '', $this->version ) . 'versions' );
			if ( false == $cache_versions ) {
				$versions = array();
				if ( $this->get_type() === 'plugin' ) {
					$versions = $this->get_plugin_versions();
				}

				if ( $this->get_type() === 'theme' ) {
					$versions = $this->get_theme_versions();
				}
				set_transient( $cache_key, $versions, MONTH_IN_SECONDS );
			} else {
				$versions = is_array( $cache_versions ) ? $cache_versions : array();
			}

			return $versions;
		}

		/**
		 * Get the last rollback for this product.
		 *
		 * @return array The rollback version.
		 */
		public function get_rollback() {
			$rollback = array();
			$versions = $this->get_api_versions();
			$versions = apply_filters( $this->get_key() . '_rollbacks', $versions );

			if ( $versions ) {
				usort( $versions, array( $this, 'sort_rollback_array' ) );
				foreach ( $versions as $version ) {
					if ( isset( $version['version'] ) && isset( $version['url'] ) && version_compare( $this->version, $version['version'], '>' ) ) {
						$rollback = $version;
						break;
					}
				}
			}

			return $rollback;
		}

		/**
		 * Sort the rollbacks array in descending order.
		 */
		public function sort_rollback_array( $a, $b ) {
			return version_compare( $a['version'], $b['version'], '<' ) > 0;
		}

		/**
		 * If product can be rolled back.
		 *
		 * @return bool Can the product be rolled back or not.
		 */
		public function can_rollback() {
			if ( $this->get_type() === 'theme' ) {
				if ( ! current_user_can( 'switch_themes' ) ) {
					return false;
				}
			}
			if ( $this->get_type() === 'plugin' ) {
				if ( ! current_user_can( 'install_plugins' ) ) {
					return false;
				}
			}
			$rollback = $this->get_rollback();

			return ! empty( $rollback );
		}

		/**
		 * Return the product key.
		 *
		 * @return string The product key.
		 */
		public function get_key() {
			return $this->key;
		}

		/**
		 * Return friendly name.
		 *
		 * @return string Friendly name.
		 */
		public function get_friendly_name() {
			$name = apply_filters( $this->get_key() . '_friendly_name', trim( str_replace( 'Lite', '', $this->get_name() ) ) );
			$name = rtrim( $name, '- ' );

			return $name;
		}

		/**
		 * Either the product requires license or not.
		 *
		 * @return bool Either requires license or not.
		 */
		public function requires_license() {
			return $this->requires_license;
		}

		/**
		 * Check if the product is either theme or plugin.
		 *
		 * @return string Product type.
		 */
		public function get_type() {
			return $this->type;
		}

		/**
		 * Returns the Store name.
		 *
		 * @return string Store name.
		 */
		public function get_store_name() {
			return $this->store_name;
		}

		/**
		 * Returns the store url.
		 *
		 * @return string The store url.
		 */
		public function get_store_url() {
			return $this->store_url;
		}

		/**
		 * Returns the product slug.
		 *
		 * @return string The product slug.
		 */
		public function get_slug() {
			return $this->slug;
		}

		/**
		 * Returns product basefile, which holds the metaheaders.
		 *
		 * @return string The product basefile.
		 */
		public function get_basefile() {
			return $this->basefile;
		}

		/**
		 * Returns product filename.
		 *
		 * @return string The product filename.
		 */
		public function get_file() {
			return $this->file;
		}

		/**
		 * Returns feedback types
		 *
		 * @return array The feedback types.
		 */
		public function get_feedback_types() {
			return apply_filters( $this->get_key() . '_feedback_types', $this->feedback_types );
		}

		/**
		 * Returns widget types
		 *
		 * @return array The widget types.
		 */
		public function get_widget_types() {
			return apply_filters( $this->get_key() . '_widget_types', $this->widget_types );
		}

		/**
		 * We log the user website and product version.
		 *
		 * @return bool Either we log the data or not.
		 */
		public function is_logger_active() {
			// If is not available on WordPress log this automatically.
			if ( ! $this->is_wordpress_available() ) {
				return true;
			} else {
				$pro_slug = $this->get_pro_slug();
				if ( ! empty( $pro_slug ) ) {

					$all_products = ThemeIsle_SDK_Loader::get_products();
					if ( isset( $all_products[ $pro_slug ] ) ) {
						return true;
					}
				}

				return ( get_option( $this->get_key() . '_logger_flag', 'no' ) === 'yes' );

			}
		}

		/**
		 * Returns the pro slug, if available.
		 *
		 * @return string The pro slug.
		 */
		public function get_pro_slug() {
			return $this->pro_slug;
		}

		/**
		 * Return the install timestamp.
		 *
		 * @return int The install timestamp.
		 */
		public function get_install_time() {
			return $this->install;
		}

		/**
		 * We require feedback on uninstall.
		 *
		 * @return bool Either we should require feedback on uninstall or not.
		 */
		public function require_uninstall_feedback() {
			if ( $this->get_type() == 'theme' && ! $this->is_external_author() ) {
				return ! get_transient( 'ti_sdk_pause_' . $this->get_key(), false );
			}

			if ( $this->get_type() == 'plugin' ) {

				return true;
			}

			return false;
		}

	}
endif;
