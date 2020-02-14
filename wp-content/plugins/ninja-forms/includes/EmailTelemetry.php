<?php

/**
 * Measure email throughput to determine the potential scale of email related issues.
 * @TODO: Remove this entire file at a later date.
 */
class NF_EmailTelemetry
{
    private $is_opted_in = false;

    /**
     * Constructor which takes in a paremeter to tell the class whether the site is opted 
     * in for telemetry or not
     * 
     * @param $opted_in
     * 
     * @since 3.3.21
     */
    public function __construct( $opted_in = false ) {
        $this->is_opted_in = $opted_in;
    }

    /**
     * @hook phpmailer_init The last action before the email is sent.
     */
    public function setup()
    {

        if( $this->is_opted_in ) {
            /**
             * @link https://codex.wordpress.org/Plugin_API/Action_Reference/phpmailer_init
             */
            // Stop collecting data.
            // add_action( 'phpmailer_init', array( $this, 'update_metrics' ) );

            // Stop scheduling new events.
            // add_action( 'wp', array( $this, 'maybe_schedule_push' ) );

            // Leave this function registered for now to avoid throwing a cron error.
            add_action( 'nf_email_telemetry_push', array( $this, 'push_telemetry' ) );
        }

    }

    /** 
     * @NOTE No need to return $phpmailer as it is passed in by reference (aka Output Parameter). 
     */
    public function update_metrics(&$phpmailer)
    {
        $send_count_metric = NF_Telemetry_MetricFactory::create( 'CountMetric', 'nf_email_send_count' );
        $send_count_metric->increment();

        $sent_with_attachments = NF_Telemetry_MetricFactory::create( 'CountMetric', 'nf_email_with_attachment_count' );
        if( $phpmailer->attachmentExists() ) $sent_with_attachments->increment();

        $to_count = count( $phpmailer->getToAddresses() );
        $to_count_metric = NF_Telemetry_MetricFactory::create( 'CountMetric', 'nf_email_to_count' );
        $to_count_metric->increment( $to_count );

        $cc_count = count( $phpmailer->getCcAddresses() );
        $cc_count_metric = NF_Telemetry_MetricFactory::create( 'CountMetric', 'nf_email_cc_count' );
        $cc_count_metric->increment( $cc_count );

        $bcc_count = count( $phpmailer->getBccAddresses() );
        $bcc_count_metric = NF_Telemetry_MetricFactory::create( 'CountMetric', 'nf_email_bcc_count' );
        $bcc_count_metric->increment( $bcc_count );

        $to_max_metric = NF_Telemetry_MetricFactory::create( 'MaxMetric', 'nf_email_to_max' );
        $to_max_metric->update( $to_count );

        $cc_max_metric = NF_Telemetry_MetricFactory::create( 'MaxMetric', 'nf_email_cc_max' );
        $cc_max_metric->update( $cc_count );

        $bcc_max_metric = NF_Telemetry_MetricFactory::create( 'MaxMetric', 'nf_email_bcc_max' );
        $bcc_max_metric->update( $bcc_count );

        $recipient_max_metric = NF_Telemetry_MetricFactory::create( 'MaxMetric', 'nf_email_recipient_max' );
        $recipient_max_metric->update( count( $phpmailer->getAllRecipientAddresses() ) );

        $attachment_count = count( $phpmailer->getAttachments() );
        $attachment_count_metric = NF_Telemetry_MetricFactory::create( 'CountMetric', 'nf_email_attachment_count' );
        $attachment_count_metric->increment( $attachment_count );

        $attachment_filesize_count_metric = NF_Telemetry_MetricFactory::create( 'CountMetric', 'nf_email_attachment_filesize_count' );
        $attachment_filesize_max_metric = NF_Telemetry_MetricFactory::create( 'MaxMetric', 'nf_email_attachment_filesize_max' );
        foreach( $phpmailer->getAttachments() as $attachment ) {
            $filename = $attachment[0];
            if( $filesize = filesize( $filename ) ){
                $attachment_filesize_count_metric->increment( $filesize );
                $attachment_filesize_max_metric->update( $filesize );
            }
        }
    }

    public function maybe_schedule_push()
    {
        if ( ! wp_next_scheduled( 'nf_email_telemetry_push' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'nf-weekly', 'nf_email_telemetry_push' );
        }
    }

    public function push_telemetry()
    {
        // (Deprecated) Exit without doing anything.
        return false;
        $metrics = array(
            'nf_email_send_count',
            'nf_email_with_attachment_count',
            'nf_email_to_count',
            'nf_email_to_max',
            'nf_email_cc_count',
            'nf_email_cc_max',
            'nf_email_bcc_count',
            'nf_email_bcc_max',
            'nf_email_recipient_max',
            'nf_email_attachment_count',
            'nf_email_attachment_filesize_count',
            'nf_email_attachment_filesize_max',
        );

        $telemetry_data = array();
        foreach( $metrics as $metric ) {
            $repository = new NF_Telemetry_MetricRepository( $metric, $default = 0 );
            $telemetry_data[ $metric ] = $repository->get();
            $repository->save( 0 );
        }

        Ninja_Forms()->dispatcher()->send( 'wpsend_stats', $telemetry_data );
    }
}
