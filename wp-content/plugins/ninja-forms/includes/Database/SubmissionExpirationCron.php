<?php
final class NF_Database_SubmissionExpirationCron
{

    /**
     * NF_Database_SubmissionExpirationCron constructor.
     * Sets up our our submission expiration CRON job.
     *
     */
    public function __construct()
    {
        // Retrieves the option that contains all of our expiration data.
        $options = get_option( 'nf_sub_expiration', false );

        // Schedules our CRON job.
        if( ! wp_next_scheduled( 'nf_submission_expiration_cron' ) &&  ! empty( $options ) ) {
            wp_schedule_event( time(), 'daily', 'nf_submission_expiration_cron' );
        }
        add_action( 'nf_submission_expiration_cron', array( $this, 'expired_submission_cron' ) );
    }

    /**
     * Expired Submission Cron
     * Checks our subs to see if any are expired and sends them to be
     * deleted if there are any that need to be removed.
     *
     * @param $options
     * @return void
     */
    public function expired_submission_cron()
    {
        $options = get_option( 'nf_sub_expiration', false );

        // If options are empty bail..
        if( ! $options || ! is_array( $options ) ) return;

        // Loop over our options and ...
        foreach( $options as $option ) {
            /*
             * Separate our $option values into two positions
             *  $option[ 0 ] = ( int ) form_id
             *  $option[ 1 ] = ( int ) expiration time in days.
             */
            $option = explode( ',', $option );

            // Use the helper method to build an array of expired subs.
            $expired_subs[] = $this->get_expired_subs( $option[ 0 ], $option[ 1 ] );
        }
        // If the expired subs array is empty bail.
        if( empty( $expired_subs ) ) return;
        // Call the helper method that deletes the expired subs.
        $this->delete_expired_subs( $expired_subs );
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

    /**
     * Delete Expired Subs
     * Helper method that removes our expired subs.
     *
     * @param $expired_subs - array of sub ids that need to be deleted.
     * @param $cap - The cap of the amount of subs you want deleted at 1 time.
     *
     * @return void
     */
    public function delete_expired_subs( $expired_subs )
    {
        $i = 0;
        // Loop over our subs
        foreach( $expired_subs as $subs ) {
            foreach( $subs as $sub ) {
                if( $i >= 100 ) break;
                wp_trash_post( $sub );
                $i++;
            }
        }
    }
}