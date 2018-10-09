<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MergeTags_Form
 */
final class NF_MergeTags_Form extends NF_Abstracts_MergeTags
{
    protected $id = 'form';

    protected $form_id;

    protected $sub_seq;

    public function __construct()
    {
        parent::__construct();
        $this->title = __( 'Form', 'ninja-forms' );

        $this->merge_tags = Ninja_Forms()->config( 'MergeTagsForm' );

		add_action( 'ninja_forms_save_sub', array( $this, 'setSubSeq' ) );

        // Gets the form ID.
        add_action( 'nf_get_form_id', array( $this, 'set_form_id' ), 15, 1 );
    }

    /**
     * @return mixed
     */
    public function getSubSeq()
    {
        return $this->sub_seq;
    }

    /**
     * @param mixed $sub_seq
     */
    public function setSubSeq( $sub_id )
    {
        $submission = Ninja_Forms()->form()->sub( $sub_id )->get();
        $this->sub_seq = $submission->get_seq_num();
    }

    /**
     * Setter method for the form_id and callback for the nf_get_form_id action.
     * @since 3.2.2
     *
     * @param string $form_id The ID of the current form.
     * @return void
     */
    public function set_form_id( $form_id )
    {
        $this->form_id = $form_id;
    }

    /**
     * Gets a count of the form submissions and callback for the sub_count merge tag setting.
     * @since 3.2.2
     *
     * @return array|int Count of the form submissions.
     */
    public function get_sub_count()
    {
		global $wpdb;

		// Query the database for the total amount of submissions for a form.
    	$query = "SELECT DISTINCT( COUNT( wpp.id ) ) AS sub_count FROM `" .
			$wpdb->prefix . "posts` wpp JOIN 
`" . $wpdb->prefix . "postmeta` 
			wpm ON	wpp.id = wpm.post_id
			WHERE wpm.meta_key = '_form_id' AND wpm.meta_value = %s";

    	$count = $wpdb->get_results( $wpdb->prepare( $query, $this->form_id ) );

        return $count[ 0 ]->sub_count;
    }
} // END CLASS NF_MergeTags_Form
