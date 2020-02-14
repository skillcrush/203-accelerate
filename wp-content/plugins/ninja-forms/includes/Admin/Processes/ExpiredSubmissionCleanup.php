<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Abstracts_Batch_Process
 */
class NF_Admin_Processes_ExpiredSubmissionCleanup extends NF_Abstracts_BatchProcess
{
    protected $_slug = 'expired_submission_cleanup';
    protected $expired_subs = array();

    /**
     * Function to run any setup steps necessary to begin processing.
     */
    public function startup()
    {
        // Retrieves the option that contains all of our expiration data.
        $expired_sub_option = get_option( 'nf_sub_expiration', array() );

        // Loop over our options and ...
        foreach( $expired_sub_option as $sub ) {
            /*
             * Separate our $option values into two positions
             *  $option[ 0 ] = ( int ) form_id
             *  $option[ 1 ] = ( int ) expiration time in days.
             */
            $sub = explode( ',', $sub );

            $expired_subs = $this->get_expired_subs( $sub[ 0 ], $sub[ 1 ] );

            // Use the helper method to build an array of expired subs.
            $this->expired_subs = array_merge( $this->expired_subs, $expired_subs );
        }
    }

    /**
     * Function to run any setup steps necessary to begin processing for steps after the first.
     *
     * @since 3.4.0
     * @return  void 
     */
    public function restart()
    {
        // Get our remaining submissions from record.
        $this->expired_subs = get_option( 'nf_expired_submissions', array() );
    }

    /**
     * Function to loop over the batch.
     *
     * @since 3.4.0
     * @return  void 
     */
    public function process()
    {

        // For the first 250 in the array.
        for( $i = 0; $i < 250; $i++ ){
            // if we've already finished bail..
            if( empty( $this->expired_subs ) ) break;

            // Pop off a sub and delete it.
            $sub = array_pop( $this->expired_subs );
            wp_trash_post( $sub );
        }

        // If our subs array isn't empty...
        if( ! empty( $this->expired_subs ) ) {
            // Update nf_expired_submissions so that we can use it in our next step.
            update_option( 'nf_expired_submissions', $this->expired_subs );
            // End processing and move to the next step.
            $this->next_step();
        }

        // If we get here, then we're ready to end batch processing.
        $this->batch_complete();
    }

    /**
     * Function to cleanup any lingering temporary elements of a batch process after completion.
     *
     * @since 3.4.0
     * @return  void 
     */
    public function cleanup()
    {
        delete_option( 'nf_expired_submissions' );
    }

    /**
     * Get Steps
     * Determines the amount of steps needed for the step processors.
     *
     * @since 3.4.0
     * @return int of the number of steps.
     */
    public function get_steps()
    {
        // Convent our number from int to float
        $steps = count( $this->expired_subs );
        $steps = floatval( $steps );

        // Get the amount of steps and return.
        $steps = ceil( $steps / 250.0 );
        return $steps;
    }

    /**
     * Get Expired Subs
     * Gathers our expired subs puts them into an array and returns it.
     *
     * @param $form_id - ( int ) ID of the Form.
     * @param $expiration_time - ( int ) number of days the submissions
     *                                  are set to expire in
     *
     * @return array of all the expired subs that were found.
     */
    public function get_expired_subs( $form_id, $expiration_time )
    {
        // Create the that will house our expired subs.
        $expired_subs = array();

        // Create our deletion timestamp.
        $deletion_timestamp = time() - ( 24 * 60 * 60 * $expiration_time );

        // Get our subs and loop over them.
        $sub = Ninja_Forms()->form( $form_id )->get_subs();
        foreach( $sub as $sub_model ) {
            // Get the sub date and change it to a UNIX time stamp.
            $sub_timestamp = strtotime( $sub_model->get_sub_date( 'Y-m-d') );
            // Compare our timestamps and any expired subs to the array.
            if( $sub_timestamp <= $deletion_timestamp ) {
                $expired_subs[] = $sub_model->get_id();
            }
        }
        return $expired_subs;
    }
}