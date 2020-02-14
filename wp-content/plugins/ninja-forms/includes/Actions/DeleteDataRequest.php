<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Actions_DataRemoval
 */
final class NF_Actions_DeleteDataRequest extends NF_Abstracts_Action
{
	/**
	 * @var string
	 */
	protected $_name  = 'deletedatarequest';

	/**
	 * @var array
	 */
	protected $_tags = array();

	/**
	 * @var string
	 */
	protected $_timing = 'late';

	/**
	 * @var int
	 */
	protected $_priority = 10;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_nicename = esc_html__( 'Delete Data Request', 'ninja-forms' );

		$settings = Ninja_Forms::config( 'ActionDeleteDataRequestSettings' );
		$this->_settings = array_merge( $this->_settings, $settings );
	}

	/*
	* PUBLIC METHODS
	*/

	public function save( $action_settings )
	{

	}

	/**
	 * Creates a Erase Personal Data request for the user with the email
	 * provided
	 *
	 * @param $action_settings
	 * @param $form_id
	 * @param $data
	 *
	 * @return array
	 */
	public function process( $action_settings, $form_id, $data )
	{
		$data = array();

		if( isset( $data['settings']['is_preview'] ) && $data['settings']['is_preview'] ){
			return $data;
		}

		// get the email setting
		$email = $action_settings[ 'email' ];

		// create request for user
		$request_id = wp_create_user_request( $email,
			'remove_personal_data' );

		/**
		 * Basically ignore if we get a user error as it will be one of two
		 * things.
		 *
		 * 1) The email in question is already in the erase data request queue
		 * 2) The email does not belong to an actual user.
		*/
		if( ! $request_id instanceof WP_Error ) {
			// send the request if it's not an error.

			// to anonymize or not to anonymize, that is the question
			add_post_meta( $request_id,
				'nf_anonymize_data',
				$action_settings[ 'anonymize' ] );

			wp_send_user_request( $request_id );
		}

		return $data;
	}
}
