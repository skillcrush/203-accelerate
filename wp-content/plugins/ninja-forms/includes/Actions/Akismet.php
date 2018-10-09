<?php

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' ) ) {
	exit;
}

/**
 * Class NF_Actions_Akismet
 */
final class NF_Actions_Akismet extends NF_Abstracts_Action {

	/**
	 * @var string
	 */
	protected $_name = 'akismet';

	/**
	 * @var array
	 */
	protected $_tags = array( 'spam', 'filtering', 'akismet' );

	/**
	 * @var string
	 */
	protected $_timing = 'normal';

	/**
	 * @var int
	 */
	protected $_priority = '10';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->_nicename = __( 'Akismet Anti-Spam', 'ninja-forms' );
		$settings        = Ninja_Forms::config( 'ActionAkismetSettings' );
		$this->_settings = array_merge( $this->_settings, $settings );

		add_filter( 'ninja_forms_action_type_settings', array( $this, 'maybe_remove_action' ) );
	}

	/**
	 * Remove the action registration if Akismet functions not available.
	 *
	 * @param array $action_type_settings
	 *
	 * @return array
	 */
	public function maybe_remove_action( $action_type_settings ) {
		if ( ! $this->akismet_available() ) {
			unset( $action_type_settings[ $this->_name ] );
		}

		return $action_type_settings;
	}

	/**
	 * Is Akismet installed and connected with a valid key?
	 *
	 * @return bool
	 */
	protected function akismet_available() {
		if ( ! is_callable( array( 'Akismet', 'get_api_key' ) ) ) {
			// Not installed and activated
			return false;
		}

		$akismet_key = Akismet::get_api_key();
		if ( empty( $akismet_key ) ) {
			// No key entered
			return false;
		}

		return 'valid' === Akismet::verify_key( $akismet_key );
	}

	/**
	 * Process the action
	 *
	 * @param array $action_settings
	 * @param int   $form_id
	 * @param array $data
	 *
	 * @return array
	 */
	public function process( $action_settings, $form_id, $data ) {
		if ( ! $this->akismet_available() ) {
			return $data;
		}

		if ( $this->is_submission_spam( $action_settings['name'], $action_settings['email'], $action_settings['url'], $action_settings['message'] ) ) {
			$data['errors']['form']['spam'] = __( 'There was an error trying to send your message. Please try again later', 'ninja-forms' );
		}

		return $data;
	}

	/**
	 * Verify submission
	 *
	 * @param $name
	 * @param $email
	 * @param $url
	 * @param $message
	 *
	 * @return bool
	 */
	protected function is_submission_spam( $name, $email, $url, $message ) {
		$body_request = array(
			'blog'                 => get_option( 'home' ),
			'blog_lang'            => get_locale(),
			'permalink'            => get_permalink(),
			'comment_type'         => 'contact-form',
			'comment_author'       => $name,
			'comment_author_email' => $email,
			'comment_author_url'   => $url,
			'comment_content'      => $message,
			'user_agent'           => ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null ),
		);

		if ( method_exists( 'Akismet', 'http_post' ) ) {
			$body_request['user_ip'] = Akismet::get_ip_address();
			$response                = Akismet::http_post( build_query( $body_request ), 'comment-check' );
		} else {
			global $akismet_api_host, $akismet_api_port;
			$body_request['user_ip'] = ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null );
			$response                = akismet_http_post( build_query( $body_request ), $akismet_api_host, '/1.1/comment-check', $akismet_api_port );
		}

		if ( ! empty( $response ) && isset( $response[1] ) && 'true' == trim( $response[1] ) ) {
			// Spam!
			return true;
		}

		return false;
	}
}