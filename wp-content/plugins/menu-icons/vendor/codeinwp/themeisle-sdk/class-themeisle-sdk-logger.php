<?php
/**
 * The main loader class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Logger
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */
if ( ! class_exists( 'ThemeIsle_SDK_Logger' ) ) :
	/**
	 * Class ThemeIsle_SDK_Logger
	 *
	 * Send the statistics to the Themeisle Endpoint
	 */
	/**
	 * Class ThemeIsle_SDK_Logger
	 */
	class ThemeIsle_SDK_Logger {

		/**
		 * @var string $logging_url Url where to send the logs
		 */
		private $logging_url = 'http://log.themeisle.com/wp-json/v1/logs/';

		/**
		 * @var ThemeIsle_SDK_Product $product Themeisle Product.
		 */
		private $product;

		/**
		 * @var string $product_cron Cron name handler
		 */
		private $product_cron;

		/**
		 * @var string $heading The heading of the modal
		 */
		private $heading = 'Do you enjoy <b>{product}</b>? Become a contributor by opting in to our anonymous data tracking. We guarantee no sensitive data is collected.';

		/**
		 * @var string $button_submit The text of the submit button
		 */
		private $button_submit = 'Sure, I would love to help.';

		/**
		 * @var string $button_cancel The text of the cancel button
		 */
		private $button_cancel = 'No, thanks.';

		/**
		 * ThemeIsle_SDK_Logger constructor.
		 *
		 * @param ThemeIsle_SDK_Product $product_object Product Object.
		 */
		public function __construct( $product_object ) {
			if ( $product_object instanceof ThemeIsle_SDK_Product ) {
				$this->product      = $product_object;
				$this->product_cron = $product_object->get_key() . '_log_activity';
			}
			add_action( 'wp_ajax_' . $this->product->get_key() . __CLASS__, array( $this, 'dismiss' ) );
		}


		/**
		 * Start the cron to send the log. It will randomize the interval in order to not send all the logs at the same time.
		 */
		public function enable() {
			if ( ! wp_next_scheduled( $this->product_cron ) ) {
				wp_schedule_single_event( time() + ( rand( 15, 24 ) * 3600 ), $this->product_cron );
			}
			add_action( $this->product_cron, array( $this, 'send_log' ) );
		}

		/**
		 * Send the statistics to the api endpoint
		 */
		public function send_log() {
			$environment                    = array();
			$theme                          = wp_get_theme();
			$environment['theme']           = array();
			$environment['theme']['name']   = $theme->get( 'Name' );
			$environment['theme']['author'] = $theme->get( 'Author' );
			$environment['plugins']         = get_option( 'active_plugins' );

			wp_remote_post(
				$this->logging_url, array(
					'method'      => 'POST',
					'timeout'     => 3,
					'redirection' => 5,
					'headers'     => array(
						'X-ThemeIsle-Event' => 'log_site',
					),
					'body'        => array(
						'site'        => get_site_url(),
						'slug'        => $this->product->get_slug(),
						'version'     => $this->product->get_version(),
						'data'        => apply_filters( $this->product->get_key() . '_logger_data', array() ),
						'environment' => $environment,
						'license'     => apply_filters( $this->product->get_key() . '_license_status', '' ),
					),
				)
			);
		}

		/**
		 * Dismiss the notification
		 */
		function dismiss() {
			check_ajax_referer( (string) __CLASS__, 'nonce' );

			$flag = intval( $_POST['enable'] ) === 1;
			update_option( $this->product->logger_option, ( $flag ? 'yes' : 'no' ) );

			if ( true === $flag ) {
				$this->enable();
			}
		}

		/**
		 * Either we should show the notification or not.
		 *
		 * @return bool Valida notification.
		 */
		function can_notify() {
			$show    = $this->product->is_logger_active();
			$checked = get_option( $this->product->logger_option, '' );
			if ( ! $show && $checked == '' ) {
				return true;
			}

			return false;
		}

		/**
		 * Shows the notification
		 */
		function show_notification() {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		/**
		 * Shows the admin notice
		 */
		function admin_notices() {
			$id = $this->product->get_key() . '_logger';

			$this->add_media( $this->product->get_key() );

			echo '<div class="notice notice-success is-dismissible "  id="' . $this->product->get_key() . '-logger-notification" ><div id="' . $id . '" class="themeisle-logger-box">' . $this->get_html( $this->product->get_key() ) . '</div></div>';
		}

		/**
		 * Generates the HTML
		 *
		 * @param string $key The product key.
		 */
		function get_html( $key ) {
			$heading       = apply_filters( $this->product->get_key() . '_logger_heading', $this->heading );
			$heading       = str_replace(
				array( '{product}' ), array(
					trim( str_replace( 'Lite', '', $this->product->get_name() ) ),
				),
				$heading
			);
			$button_submit = apply_filters( $this->product->get_key() . '_logger_button_submit', $this->button_submit );
			$button_cancel = apply_filters( $this->product->get_key() . '_logger_button_cancel', $this->button_cancel );

			return '<div >'
				   . '<p>' . $heading . '</p>'
				   . '<div class="actions">'
				. get_submit_button(
					$button_submit, 'primary ' . $this->product->get_key() . '-ti-logger', $this->product->get_key() . 'ti-logger-yes', false, array(
						'data-ti-log-enable' => 1,
					)
				)
				. get_submit_button(
					$button_cancel, 'secondary ' . $this->product->get_key() . '-ti-logger', $this->product->get_key() . 'ti-logger-no', false, array(
						'data-ti-log-enable' => 0,
					)
				)
				   . '</div></div>';
		}

		/**
		 * Loads the js
		 *
		 * @param string $key The product key.
		 */
		function add_media( $key ) {
			?>
			<style type="text/css">
				#<?php echo $key; ?>-logger-notification {
					padding-bottom: 5px;
				}

				#<?php echo $key; ?>-logger-notification .button {
					margin-left: 5px;
				}
			</style>
			<script type="text/javascript" id="<?php echo $key; ?>ti-logger-js">
				(function ($) {
					$(document).ready(function () {
						$('.<?php echo $key; ?>-ti-logger').on('click', function (e) {

							$.ajax({
								url: ajaxurl,
								method: "post",
								data: {
									'nonce': '<?php echo wp_create_nonce( (string) __CLASS__ ); ?>',
									'action': '<?php echo $this->product->get_key() . __CLASS__; ?>',
									'enable': $(this).attr('data-ti-log-enable')
								},
								success: function () {
									$('#<?php echo $key; ?>-logger-notification').hide();
								}
							});
						});
					});
				})(jQuery);
			</script>
			<?php
		}

	}
endif;
