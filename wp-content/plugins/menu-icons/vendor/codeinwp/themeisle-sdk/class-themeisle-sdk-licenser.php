<?php
/**
 * The main loader class for license handling.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Licenser
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */
if ( ! class_exists( 'ThemeIsle_SDK_Licenser' ) ) :
	/**
	 * Class ThemeIsle_SDK_Licenser
	 *
	 * Used to update the themeisle products
	 */
	class ThemeIsle_SDK_Licenser {

		/**
		 * @var string $license_key The license key string
		 */
		public $license_key;

		/**
		 * @var bool $do_check This ensures that the custom API request only runs on the second time that WP fires the update check
		 */
		private $do_check = false;

		/**
		 * @var bool $failed_checks Number of failed checks to the api endpoint
		 */
		private $failed_checks = 0;
		/**
		 * @var ThemeIsle_SDK_Product $product The ThemeIsle Product.
		 */
		private $product;
		/**
		 * @var string $product_key The product update response key.
		 */
		private $product_key;
		/**
		 * @var int $max_failed Maximum failed checks allowed before show the notice
		 */
		private static $max_failed = 5;

		/**
		 * ThemeIsle_SDK_Licenser constructor.
		 *
		 * @param ThemeIsle_SDK_Product $product The product object.
		 */
		public function __construct( $product ) {
			$this->product     = $product;
			$this->product_key = $this->product->get_key() . '-update-response';
			if ( ! $this->product->requires_license() ) {
				$this->license_key = 'free';
			} else {
				$license_data        = get_option( $this->product->get_key() . '_license_data', '' );
				$this->failed_checks = intval( get_option( $this->product->get_key() . '_failed_checks', 0 ) );
				if ( $license_data !== '' ) {
					$this->license_key = isset( $license_data->key ) ? $license_data->key : get_option( $this->product->get_key() . '_license', '' );
				} else {
					$this->license_key = get_option( $this->product->get_key() . '_license', '' );
				}
				$this->register_license_hooks();
			}
		}

		/**
		 * Register license hooks for the themeisle products
		 */
		public function register_license_hooks() {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_init', array( $this, 'activate_license' ) );
			add_action( 'admin_init', array( $this, 'product_valid' ), 99999999 );
			add_action( 'admin_notices', array( $this, 'show_notice' ) );
			add_filter( $this->product->get_key() . '_license_status', array( $this, 'get_license_status' ) );
		}

		/**
		 * @param string $r Update payload.
		 * @param string $url The api url.
		 *
		 * @return mixed List of themes to check for update.
		 */
		function disable_wporg_update( $r, $url ) {

			if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/' ) ) {
				return $r;
			}

			// Decode the JSON response
			$themes = json_decode( $r['body']['themes'] );

			unset( $themes->themes->{$this->product->get_slug()} );

			// Encode the updated JSON response
			$r['body']['themes'] = json_encode( $themes );

			return $r;
		}

		/**
		 * Register the setting for the license of the product
		 *
		 * @return bool
		 */
		public function register_settings() {
			if ( ! is_admin() ) {
				return false;
			}
			add_settings_field(
				$this->product->get_key() . '_license',
				$this->product->get_name() . ' license',
				array( $this, 'license_view' ),
				'general'
			);
		}

		/**
		 *  The license view field
		 */
		public function license_view() {
			$status = $this->get_license_status();
			$value  = $this->license_key;

			$activate_string   = apply_filters( $this->product->get_key() . '_lc_activate_string', 'Activate' );
			$deactivate_string = apply_filters( $this->product->get_key() . '_lc_deactivate_string', 'Deactivate' );
			$valid_string      = apply_filters( $this->product->get_key() . '_lc_valid_string', 'Valid' );
			$invalid_string    = apply_filters( $this->product->get_key() . '_lc_invalid_string', 'Invalid' );
			$license_message   = apply_filters( $this->product->get_key() . '_lc_license_message', 'Enter your license from %s purchase history in order to get %s updates' );

			echo '<p ><input ' . ( ( $status === 'valid' ) ? ( 'style="border:1px solid #7ad03a; "' ) : '' ) . ' type="text" id="' . $this->product->get_key() . '_license" name="' . $this->product->get_key() . '_license" value="' . $value . '" /><a ' . ( ( $status === 'valid' ) ? ( 'style="color:#fff;background:  #7ad03a; display: inline-block;text-decoration: none;font-size: 13px;line-height: 26px;height: 26px; margin-left:5px; padding: 0 10px 1px;  -webkit-border-radius: 3px;border-radius: 3px; ">' . $valid_string ) : ( 'style="color:#fff;background:  #dd3d36; display: inline-block;text-decoration: none;font-size: 13px;line-height: 26px;height: 26px; margin-left:5px; padding: 0 10px 1px;  -webkit-border-radius: 3px;border-radius: 3px; ">' . $invalid_string ) ) . ' </a>&nbsp;&nbsp;&nbsp;<button name="' . $this->product->get_key() . '_btn_trigger" ' . ( ( $status === 'valid' ) ? ( ' class="button button-primary">' . $deactivate_string ) : ( ' class="button button-primary" value="yes" type="submit" >' . $activate_string ) ) . ' </button></p><p class="description">' . sprintf( $license_message, '<a  href="' . $this->product->get_store_url() . '">' . $this->product->get_store_name() . '</a> ', $this->product->get_type() ) . '</p>';

		}

		/**
		 * Return the license status.
		 *
		 * @return string The License status.
		 */
		public function get_license_status() {
			$license_data = get_option( $this->product->get_key() . '_license_data', '' );
			if ( $license_data !== '' ) {
				return isset( $license_data->license ) ? $license_data->license : get_option( $this->product->get_key() . '_license_status', 'not_active' );
			} else {
				return get_option( $this->product->get_key() . '_license_status', 'not_active' );
			}

		}

		/**
		 *  Check if the license is active or not
		 *
		 * @return bool
		 */
		public function check_activation() {
			$license_data = get_option( $this->product->get_key() . '_license_data', '' );
			if ( $license_data !== '' ) {
				return isset( $license_data->error ) ? ( $license_data->error == 'no_activations_left' ) : false;
			}

			return false;
		}

		/**
		 *  Check if the license is about to expire in the next month
		 *
		 * @return bool
		 */
		function check_expiration() {
			$license_data = get_option( $this->product->get_key() . '_license_data', '' );
			if ( $license_data !== '' ) {
				if ( isset( $license_data->expires ) ) {
					if ( strtotime( $license_data->expires ) - time() < 30 * 24 * 3600 ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Return the renew url from the store used
		 *
		 * @return string The renew url.
		 */
		function renew_url() {
			$license_data = get_option( $this->product->get_key() . '_license_data', '' );
			if ( $license_data !== '' ) {
				if ( isset( $license_data->download_id ) && isset( $license_data->key ) ) {
					return $this->product->get_store_url() . '/checkout/?edd_license_key=' . $license_data->key . '&download_id=' . $license_data->download_id;
				}
			}

			return $this->product->get_store_url();
		}

		/**
		 * Check if we hide the notificatin nag or not
		 *
		 * @param string $hide The notification to hide.
		 *
		 * @return bool Either hide them or not.
		 */
		function check_hide( $hide ) {
			return true;
		}

		/**
		 *  Show the admin notice regarding the license status
		 *
		 * @return bool
		 */
		function show_notice() {
			if ( ! is_admin() ) {
				return false;
			}
			$status                = $this->get_license_status();
			$no_activations_string = apply_filters(
				$this->product->get_key() . '_lc_no_activations_string', 'No activations left for %s !!!. You need to
									upgrade your plan in order to use %s on more
									websites. Please ask the %s
									Staff for more details.'
			);
			$no_valid_string       = apply_filters(
				$this->product->get_key() . '_lc_no_valid_string', 'In order to benefit from updates and support for %s, please add
								your license code from your  <a href="%s" target="_blank">purchase history</a> and validate it <a
									href="%s">here</a>. '
			);
			$expiration_string     = apply_filters(
				$this->product->get_key() . '_lc_expiration_string', 'Your license is about to expire
									for %s. You can go to %s and renew it   '
			);
			if ( $status != 'valid' ) {
				if ( $this->check_activation() ) {
					if ( $this->check_hide( 'activation' ) ) {
						?>
						<div class="error">
							<p><strong>
									<?php
									echo sprintf(
										$no_activations_string, $this->product->get_name(), $this->product->get_name(), '<a href="' . $this->product->get_store_url() . '"
																	target="_blank">' . $this->product->get_store_name() . '</a>'
									);
									?>
								</strong>
							</p>
						</div>
						<?php
						return false;
					}
				}
				?>
				<?php if ( $this->check_hide( 'valid' ) ) : ?>
					<div class="error">
						<p>
							<strong><?php echo sprintf( $no_valid_string, $this->product->get_name() . ' ' . $this->product->get_type(), $this->product->get_store_url(), admin_url( 'options-general.php' ) . '#' . $this->product->get_key() ); ?> </strong>
						</p>
					</div>
				<?php endif; ?>
				<?php
			} else {
				if ( $this->check_expiration() ) {
					if ( $this->check_hide( 'expiration' ) ) {
						?>
						<div class="update-nag">
							<p>
								<strong>
									<?php
									echo sprintf(
										$expiration_string, $this->product->get_name() . ' ' . $this->product->get_type(), '<a
										href="' . $this->renew_url() . '"
										target="_blank">' . $this->product->get_store_name() . '</a>'
									);
									?>
								</strong>
							</p>
						</div>
						<?php
					}
				}
			}
		}

		/**
		 * Run the license check call
		 */
		public function product_valid() {
			if ( false === ( $license = get_transient( $this->product->get_key() . '_license_data' ) ) ) {
				$license = $this->check_license();
				set_transient( $this->product->get_key() . '_license_data', $license, 12 * HOUR_IN_SECONDS );
				update_option( $this->product->get_key() . '_license_data', $license );
			}

		}

		/**
		 * Increment the failed checks
		 */
		private function increment_failed_checks() {
			$this->failed_checks ++;
			update_option( $this->product->get_key() . '_failed_checks', $this->failed_checks );
		}

		/**
		 * Reset the failed checks
		 */
		private function reset_failed_checks() {
			$this->failed_checks = 1;
			update_option( $this->product->get_key() . '_failed_checks', $this->failed_checks );
		}

		/**
		 *  Check the license status
		 *
		 * @return object The license data.
		 */
		public function check_license() {
			$status = $this->get_license_status();
			if ( $status == 'not_active' ) {
				$license_data          = new stdClass();
				$license_data->license = 'not_active';

				return $license_data;
			}
			$license    = trim( $this->license_key );
			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => $license,
				'item_name'  => rawurlencode( $this->product->get_name() ),
				'url'        => rawurlencode( home_url() ),
			);
			// Call the custom API.
			$response = wp_remote_get(
				add_query_arg( $api_params, $this->product->get_store_url() ), array(
					'timeout'   => 15,
					'sslverify' => false,
				)
			);
			if ( is_wp_error( $response ) ) {
				$license_data          = new stdClass();
				$license_data->license = 'valid';

			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				if ( ! is_object( $license_data ) ) {
					$license_data          = new stdClass();
					$license_data->license = 'valid';
				}
			}
			$license_old = get_option( $this->product->get_key() . '_license_data', '' );
			if ( $license_old->license == 'valid' && ( $license_data->license != $license_old->license ) ) {
				$this->increment_failed_checks();
			} else {
				$this->reset_failed_checks();
			}

			if ( $this->failed_checks <= self::$max_failed ) {
				return $license_old;
			}

			if ( isset( $license_old->hide_valid ) ) {
				$license_data->hide_valid = true;
			}

			if ( ! isset( $license_data->key ) ) {
				$license_data->key = isset( $license_old->key ) ? $license_old->key : '';
			}

			if ( isset( $license_old->hide_expiration ) ) {
				$license_data->hide_expiration = true;
			}

			if ( isset( $license_old->hide_activation ) ) {
				$license_data->hide_activation = true;
			}

			return $license_data;

		}

		/**
		 * Activate the license remotely
		 */
		function activate_license() {
			// listen for our activate button to be clicked
			if ( isset( $_POST[ $this->product->get_key() . '_btn_trigger' ] ) ) {
				$status = $this->get_license_status();
				// retrieve the license from the database
				$license    = $_POST[ $this->product->get_key() . '_license' ];
				$api_params = array(
					'license'   => $license,
					'item_name' => rawurlencode( $this->product->get_name() ),
					'url'       => rawurlencode( home_url() ),
				);
				if ( $status != 'valid' ) {
					// data to send in our API request
					$api_params['edd_action'] = 'activate_license';
				} else {
					$api_params['edd_action'] = 'deactivate_license';
				}
				// Call the custom API.
				$response = wp_remote_get( add_query_arg( $api_params, $this->product->get_store_url() ) );
				// make sure the response came back okay
				if ( is_wp_error( $response ) ) {
					$license_data          = new stdClass();
					$license_data->license = ( $status != 'valid' ) ? 'valid' : 'invalid';

				} else {
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( ! is_object( $license_data ) ) {
						$license_data          = new stdClass();
						$license_data->license = ( $status != 'valid' ) ? 'valid' : 'invalid';
					}
				}
				if ( ! isset( $license_data->key ) ) {
					$license_data->key = $license;
				}
				if ( $license_data->license == 'valid' ) {
					$this->reset_failed_checks();
				}

				if ( isset( $license_data->plan ) ) {
					update_option( $this->product->get_key() . '_license_plan', $license_data->plan );
				}

				update_option( $this->product->get_key() . '_license_data', $license_data );
				delete_transient( $this->product->get_key() . '_license_data' );
				set_transient( $this->product->get_key() . '_license_data', $license_data, 12 * HOUR_IN_SECONDS );

			}
		}

		/**
		 * Enable the license system
		 */
		public function enable() {
			if ( $this->product->get_type() == 'plugin' ) {
				add_filter(
					'pre_set_site_transient_update_plugins', array(
						$this,
						'pre_set_site_transient_update_plugins_filter',
					)
				);
				add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
				add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );
			}
			if ( $this->product->get_type() == 'theme' ) {
				add_filter( 'site_transient_update_themes', array( &$this, 'theme_update_transient' ) );
				add_filter( 'delete_site_transient_update_themes', array( &$this, 'delete_theme_update_transient' ) );
				add_action( 'load-update-core.php', array( &$this, 'delete_theme_update_transient' ) );
				add_action( 'load-themes.php', array( &$this, 'delete_theme_update_transient' ) );
				add_action( 'load-themes.php', array( &$this, 'load_themes_screen' ) );
				add_filter( 'http_request_args', array( $this, 'disable_wporg_update' ), 5, 2 );

			}

		}

		/**
		 * Load the Themes screen
		 */
		function load_themes_screen() {
			add_thickbox();
			add_action( 'admin_notices', array( &$this, 'update_nag' ) );
		}

		/**
		 * Alter the nag for themes update
		 */
		function update_nag() {
			$theme        = wp_get_theme( $this->product->get_slug() );
			$api_response = get_transient( $this->product_key );
			if ( false === $api_response ) {
				return;
			}
			$update_url     = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $this->product->get_slug() ), 'upgrade-theme_' . $this->product->get_slug() );
			$update_message = apply_filters( 'themeisle_sdk_license_update_message', 'Updating this theme will lose any customizations you have made. Cancel to stop, OK to update.' );
			$update_onclick = ' onclick="if ( confirm(\'' . esc_js( $update_message ) . '\') ) {return true;}return false;"';
			if ( version_compare( $this->product->get_version(), $api_response->new_version, '<' ) ) {
				echo '<div id="update-nag">';
				printf(
					'<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.',
					$theme->get( 'Name' ),
					$api_response->new_version,
					'#TB_inline?width=640&amp;inlineId=' . $this->product->get_version() . '_changelog',
					$theme->get( 'Name' ),
					$update_url,
					$update_onclick
				);
				echo '</div>';
				echo '<div id="' . $this->product->get_slug() . '_' . 'changelog" style="display:none;">';
				echo wpautop( $api_response->sections['changelog'] );
				echo '</div>';
			}
		}

		/**
		 * @param mixed $value The transient data.
		 *
		 * @return mixed
		 */
		function theme_update_transient( $value ) {
			$update_data = $this->check_for_update();
			if ( $update_data ) {
				$value->response[ $this->product->get_slug() ] = $update_data;
			}

			return $value;
		}

		/**
		 * Delete the update transient
		 */
		function delete_theme_update_transient() {
			delete_transient( $this->product_key );
		}

		/**
		 * Check for updates
		 *
		 * @return array|bool Either the update data or false in case of failure
		 */
		function check_for_update() {
			$theme       = wp_get_theme( $this->product->get_slug() );
			$update_data = get_transient( $this->product_key );
			if ( false === $update_data ) {
				$failed = false;
				if ( empty( $this->license_key ) ) {
					return false;
				}
				$api_params = array(
					'edd_action' => 'get_version',
					'version'    => $this->product->get_version(),
					'license'    => $this->license_key,
					'name'       => $this->product->get_name(),
					'slug'       => $this->product->get_slug(),
					'author'     => $this->product->get_store_name(),
					'url'        => rawurlencode( home_url() ),
				);
				$response   = wp_remote_post(
					$this->product->get_store_url(), array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params,
					)
				);
				// make sure the response was successful
				if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
					$failed = true;
				}
				$update_data = json_decode( wp_remote_retrieve_body( $response ) );
				if ( ! is_object( $update_data ) ) {
					$failed = true;
				}
				// if the response failed, try again in 30 minutes
				if ( $failed ) {
					$data              = new stdClass;
					$data->new_version = $this->product->get_version();
					set_transient( $this->product_key, $data, strtotime( '+30 minutes' ) );

					return false;
				}
				// if the status is 'ok', return the update arguments
				if ( ! $failed ) {
					$update_data->sections = maybe_unserialize( $update_data->sections );
					set_transient( $this->product_key, $update_data, strtotime( '+12 hours' ) );
				}
			}
			if ( version_compare( $this->product->get_version(), $update_data->new_version, '>=' ) ) {
				return false;
			}

			return (array) $update_data;
		}

		/**
		 * Check for Updates at the defined API endpoint and modify the update array.
		 *
		 * This function dives into the update API just when WordPress creates its update array,
		 * then adds a custom API call and injects the custom plugin data retrieved from the API.
		 * It is reassembled from parts of the native WordPress plugin update code.
		 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
		 *
		 * @uses api_request()
		 *
		 * @param array $_transient_data Update array build by WordPress.
		 *
		 * @return array Modified update array with custom plugin data.
		 */
		public function pre_set_site_transient_update_plugins_filter( $_transient_data ) {
			if ( empty( $_transient_data ) || ! $this->do_check ) {
				$this->do_check = true;

				return $_transient_data;
			}
			$api_response = $this->api_request();
			if ( false !== $api_response && is_object( $api_response ) && isset( $api_response->new_version ) ) {
				if ( version_compare( $this->product->get_version(), $api_response->new_version, '<' ) ) {
					$_transient_data->response[ $this->product->get_slug() . '/' . $this->product->get_file() ] = $api_response;
				}
			}

			return $_transient_data;
		}

		/**
		 * Calls the API and, if successfull, returns the object delivered by the API.
		 *
		 * @uses         get_bloginfo()
		 * @uses         wp_remote_post()
		 * @uses         is_wp_error()
		 *
		 * @param string $_action The requested action.
		 * @param array  $_data Parameters for the API action.
		 *
		 * @return false||object
		 */
		private function api_request( $_action = '', $_data = '' ) {
			if ( empty( $this->license_key ) ) {
				return;
			}
			$api_params = array(
				'edd_action' => 'get_version',
				'license'    => $this->license_key,
				'name'       => rawurlencode( $this->product->get_name() ),
				'slug'       => rawurlencode( $this->product->get_slug() ),
				'author'     => $this->product->get_store_name(),
				'url'        => rawurlencode( home_url() ),
			);
			$request    = wp_remote_post(
				$this->product->get_store_url(), array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);
			if ( ! is_wp_error( $request ) ) :
				$request = json_decode( wp_remote_retrieve_body( $request ) );
				if ( $request && isset( $request->sections ) ) {
					$request->sections = maybe_unserialize( $request->sections );
				}

				return $request;
			else :
				return false;
			endif;
		}

		/**
		 * Updates information on the "View version x.x details" page with custom data.
		 *
		 * @uses api_request()
		 *
		 * @param mixed  $_data Plugin data.
		 * @param string $_action Action to send.
		 * @param object $_args Arguments to use.
		 *
		 * @return object $_data
		 */
		public function plugins_api_filter( $_data, $_action = '', $_args = null ) {
			if ( ( $_action != 'plugin_information' ) || ! isset( $_args->slug ) || ( $_args->slug != $this->product->get_slug() ) ) {
				return $_data;
			}
			$api_response = $this->api_request();
			if ( false !== $api_response ) {
				$_data = $api_response;
			}

			return $_data;
		}

		/**
		 * Disable SSL verification in order to prevent download update failures
		 *
		 * @param array  $args Http args.
		 * @param string $url Url to check.
		 *
		 * @return object $array
		 */
		function http_request_args( $args, $url ) {
			// If it is an https request and we are performing a package download, disable ssl verification
			if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd_action=package_download' ) ) {
				$args['sslverify'] = false;
			}

			return $args;
		}

	}
endif;
