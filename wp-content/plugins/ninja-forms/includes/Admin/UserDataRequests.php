<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Admin_UserDataRequests {

	/**
	 * @var array
	 */
	protected $ignored_field_types = array (
		'html',
		'submit',
		'hr',
		'recaptcha',
		'spam',
		'creditcard',
		'creditcardcvc',
		'creditcardexpiration',
		'creditcardfullname',
		'creditcardnumber',
		'creditcardzip'
	);

	/**
	 * @var WP_User
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $request_email;

	/** Class constructor */
	public function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array(
			$this, 'plugin_register_exporters' ) );

		add_filter( 'wp_privacy_personal_data_erasers', array(
			$this, 'plugin_register_erasers' ) );
	}

	/**
	 * Register exporter for Plugin user data.
	 *
	 * @param array $exporters
	 *
	 * @return array
	 */
	function plugin_register_exporters( $exporters = array() ) {
		$exporters[] = array(
			'exporter_friendly_name' => esc_html__( 'Ninja Forms Submission Data', 'ninja-forms' ),
			'callback'               => array( $this, 'plugin_user_data_exporter' ),
		);
		return $exporters;
	}

	/**
	 * Register eraser for Plugin user data.
	 *
	 * @param array $erasers
	 *
	 * @return array
	 */
	function plugin_register_erasers( $erasers = array() ) {
		$erasers[] = array(
			'eraser_friendly_name' => esc_html__( 'Ninja Forms Submissions Data', 'ninja-forms' ),
			'callback'               => array( $this, 'plugin_user_data_eraser' ),
		);
		return $erasers;
	}

	/**
	 * Adds Ninja Forms Submission data to the default HTML export file that
	 * WordPress creates on converted request
	 *
	 * @param $email_address
	 * @param int $page
	 *
	 * @return array
	 */
	function plugin_user_data_exporter( $email_address, $page = 1 ) {
		$export_items = array();

		// get the user
		$this->user = get_user_by( 'email', $email_address );
		$this->request_email = $email_address;

		if( $this->user && $this->user->ID ) {
			$item_id = "ninja-forms-" . $this->user->ID;
		} else {
			$item_id = "ninja-forms";
		}

		$group_id = 'ninja-forms';

		$group_label = esc_html__( 'Ninja Forms Submission Data', 'ninja-forms' );

		$subs = $this->get_related_subs( $email_address );

		foreach($subs as $sub) {
			$data = array();
			// get the field values from postmeta
			$sub_meta = get_post_meta( $sub->ID );

			// make sure we have a form submission
			if ( isset( $sub_meta[ '_form_id' ] ) ) {
				$form = Ninja_Forms()->form( $sub_meta[ '_form_id' ][ 0 ] )
                    ->get();
				$fields = Ninja_Forms()->form( $sub_meta[ '_form_id' ][ 0 ] )
					->get_fields();

				foreach ( $fields as $field_id => $field ) {
					// we don't care about submit, hr, divider, html fields
					if ( ! in_array( $field->get_setting( 'type' ),
							$this->ignored_field_types ) ) {
						// make sure there is a value
						if ( isset( $sub_meta[ '_field_' . $field_id ] ) ) {

							//multi-value fields may need to be unserialized
							if( in_array( $field->get_setting( 'type' ),
								array( 'listcheckbox', 'listmultiselect' ) ) ){

								//implode the unserialized array
								$value = implode( ',', maybe_unserialize(
									$sub_meta[	'_field_' . $field_id ][ 0 ] ) );
							} else {
								$value = $sub_meta[	'_field_' . $field_id ][ 0 ];
							}
							// Add label/value pairs to data array
							$data[] = array(
								'name'  => $field->get_setting( 'label' ),
								'value' => $value
							);
						}
					}
				}

				// Add this group of items to the exporters data array.
				$export_items[] = array(
					'group_id'    => $group_id . '-' . $sub->ID,
					'group_label' => $group_label . '-' .
					                 $form->get_setting( 'title' ),
					'item_id'     => $item_id . '-' . $sub->ID,
					'data'        => $data,
				);
			}
		}
		// Returns an array of exported items for this pass, but also a boolean whether this exporter is finished.
		//If not it will be called again with $page increased by 1.
		return array(
			'data' => $export_items,
			'done' => true,
		);
	}

	/**
	 * Eraser for Plugin user data. This will completely erase all Ninja Form
	 * submission data for the user when converted by the admin.
	 *
	 * @param $email_address
	 * @param int $page
	 *
	 * @return array
	 */
	function plugin_user_data_eraser( $email_address, $page = 1 ) {

		if ( empty( $email_address ) ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		// get the user
		$this->user = get_user_by( 'email', $email_address );
		$this->request_email = $email_address;

		if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
			return array();
		}
		$request_id = absint($_REQUEST[ 'id' ]);

		$make_anonymous = get_post_meta( $request_id, 'nf_anonymize_data',
			true);

		$messages = array();
		$items_removed  = false;
		$items_retained = false;

		$subs = $this->get_related_subs( $email_address );

		if( 0 < sizeof( $subs ) ) {
			$items_removed = true;
		}

		if( '1' != $make_anonymous ) {
			$this->delete_submissions( $subs );
			$items_removed = true;
		} else {
			$this->anonymize_submissions( $subs, $email_address );
		}

		/**
		 * Returns an array of exported items for this pass, but also a boolean
		 * whether this exporter is finished.
		 * If not it will be called again with $page increased by 1.
		 * */
		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => true,
		);
	}

	/**
	 * Retrieve all submissions related(by author id or email address) to the
	 * given email address
	 *
	 * @param $email_address
	 *
	 * @return array
	 */
	private function get_related_subs( $email_address ) {

		// array if subs where user is author
		$logged_in_subs = array();

		if ( $this->user && $this->user->ID ) {
			// get submission ids the old-fashioned way if user is author
			$logged_in_subs = get_posts(
				array(
					'author'         => $this->user->ID,
					'post_type'      => 'nf_sub',
					'posts_per_page' => - 1,
					'fields'         => 'ids'
				)
			);
		}

		// get submission ids where email address is a field value
		$anon_sub_ids = $this->get_subs_by_email( $email_address );

		// merge anonymous and author submissions ids and get unique
		$sub_ids = array_unique( array_merge( $logged_in_subs, $anon_sub_ids ) );

		// return empty array if $sub_ids is empty
		if( 1 > count( $sub_ids ) ) {
			return array();
		}

		// get post objects related to the email address
		return get_posts(
			array(
				'include' => implode(',', $sub_ids),
				'post_type' => 'nf_sub',
				'posts_per_page' => -1,
			)
		);
	}

	/**
	 * Get submission ids where the submission has the give email address as
	 * data
	 *
	 * @param $email_address
	 *
	 * @return array
	 */
	private function get_subs_by_email( $email_address ) {
		global $wpdb;

		// query to find any submission with our requester's email as value
		$anon_subs_query = "SELECT DISTINCT(m.post_id) FROM `" . $wpdb->prefix
               . "postmeta` m
				JOIN `" . $wpdb->prefix . "posts` p ON p.id = m.post_id
				WHERE m.meta_value = '" . $email_address . "'
				AND p.post_type = 'nf_sub'";

		$anon_subs = $wpdb->get_results( $anon_subs_query );

		$sub_id_array = array();
		// let's get the integer value of those submission ids
		if( 0 < sizeof( $anon_subs ) ) {
			foreach( $anon_subs as $sub ) {
				$sub_id_array[] = intval( $sub->post_id );
			}
		}

		return $sub_id_array;
	}

	/**
	 * Delete Submissions
	 *
	 * @param $subs
	 */
	private function delete_submissions( $subs ) {
		if( 0 < sizeof( $subs ) ) {
			// iterate and delete the submissions
			foreach($subs as $sub) {
				wp_delete_post( $sub->ID, true );
			}
		}
	}

	/**
	 * This will (redact) personal data and anonymize submissions
	 *
	 * @param $subs
	 */
	private function anonymize_submissions( $subs ) {
		$form_id_array = array();
		$submitter_field = '';

		if( 0 < sizeof( $subs ) ) {
			$anonymize_data = false;
			foreach( $subs as $sub ) {
				// get the form id
				$form_id = get_post_meta( $sub->ID, '_form_id', true );

				$form = Ninja_Forms()->form( $form_id );

				/*
				 * Do we have a use, if so does the post(submission) author
				 * match the user. If so, then anonymize
				 */
				if( $this->user && $this->user->ID
				    && $sub->post_author == $this->user->ID ) {
					$anonymize_data = true;
				} else {
					/*
					 * Otherwise, does the submitter email for the submission
					 *  equal the email for the request
					 */
					$form_submitter_email = '';
					if( in_array( $form_id, array_keys( $form_id_array ) ) ) {
						/*
						 * if we already have the submitter field key, no
						 * need to iterate over the actions again
						 */
						$submitter_field = $form_id_array[ $form_id ];
					} else {
						$actions = $form->get_actions();
						if ( 0 < sizeof( $actions ) ) {
							foreach ( $actions as $action ) {
								// we only care about the save action
								if ( 'save' == $action->get_setting( 'type' )
								     && null != $action->get_setting( 'submitter_email' )
								     && '' != $action->get_setting( 'submitter_email' ) ) {
									// get the submitter field
									$submitter_field = $action->get_setting( 'submitter_email' );
									/*
									 * Add the form id and submitter field to
									 *  this array so we don't have to load
									 * the form again if we have multiple
									 * submissions for the same form
									 */
									$form_id_array[ $form_id ] = $submitter_field;
									break;
								}
							}
						}
					}

					/*
					 * If the submitter field is not empty, then let's
					 * get the value given in the form submission for
					 * that field
					 */
					if ( '' != $submitter_field ) {
						$fields = $form->get_fields();
						foreach ( $fields as $field ) {
							$key = $field->get_setting( 'key' );
							// we only care about email fields
							if ( 'email' == $field->get_setting( 'type' )
							     && $submitter_field == $key ) {
								// if we have a match, get the value
								$form_submitter_email = get_post_meta(
									$sub->ID,
									'_field_' . $field->get_id(),
									true );
								break;
							}
						}
					}
					// if form submitter email matches requester's email
					if( $form_submitter_email === $this->request_email ) {
						$anonymize_data = true;
					}
				}

				if( $anonymize_data ) {
					// anonymize the actual submitted for values
					$this->anonymize_fields($sub, $form->get_fields() );
				}
			}
		}
	}

	/**
	 * This will anonymize personally identifiable fields and anonymize
	 * submissions submitted by the user with the provided email address
	 *
	 * @param $sub
	 * @param $fields
	 */
	private function anonymize_fields( $sub, $fields ) {
		foreach( $fields as $field ) {
			$type = $field->get_setting( 'type' );

			// ignore fields that aren't saved
			if( ! in_array( $type, $this->ignored_field_types ) ) {
				$is_personal = $field->get_setting( 'personally_identifiable' );

				/**
				 * If this is personally identifiable, redact it
				 */
				if( null != $is_personal && '1' == $is_personal ) {
					$field_id = $field->get_id();

					// make sure we have that field saved.
					$field_value = get_post_meta(
						$sub->ID,
						'_field_' . $field_id,
						true
					);
					if( '' != $field_value ) {
						update_post_meta(
							$sub->ID,
							'_field_' . $field_id,
							'(redacted)'
						);
					}
				}
			}
		}

		// Remove the author id if the the email address belongs to the author
		if( $this->user && $this->user->ID &&
		    $this->user->ID == $sub->post_author ) {
			wp_update_post(
				array(
					'ID' => $sub->ID,
					'post_author' => 0
				)
			);
		}
	}
}