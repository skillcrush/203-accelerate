<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Abstracts_Batch_Process
 */
abstract class NF_Abstracts_BatchProcess
{
    protected $_db;

    /**
     * Array that holds data we're sending back to the JS front-end.
     * @var array
     */
    protected $response = array(
        'batch_complete' => false
    );

    /**
     * Constructor
     */
    public function __construct( $data = array() )
    {
        //Bail if we aren't in the admin.
        if ( ! is_admin() )
            return false;

        global $wpdb;

        /**
         * Set $_db to $wpdb.
         * This helps us by not requiring us to declare global $wpdb in every class method.
         */
        $this->_db = $wpdb;

        // Run init.
        $this->init();
    }

    /**
     * Decides whether we need to run startup or restart and then calls processing.
     *
     * @since  3.4.0
     * @return void
     */
    public function init()
    {
        if ( ! get_option( 'nf_doing_' . $this->_slug ) ) {
            // Run the startup process.
            $this->startup();
        } else {
            // Otherwise... (We've already run startup.)
            $this->restart();
        }

        // Determine how many steps this will take.
        $this->response[ 'step_total' ] = $this->get_steps();

        add_option( 'nf_doing_' . $this->_slug, true );

        // Run processing
        $this->process();
    }

    /**
     * Function to loop over the batch.
     *
     * @since 3.4.0
     * @return  void
     */
    public function process()
    {
        /**
         * This function intentionlly left empty.
         */
    }

    /**
     * Function to run any setup steps necessary to begin processing.
     *
     * @since 3.4.0
     * @return  void
     */
    public function startup()
    {
        /**
         * This function intentionally left empty.
         */
    }

    /**
     * Function to run any setup steps necessary to begin processing for steps after the first.
     *
     * @since 3.4.0
     * @return  void 
     */
    public function restart()
    {
        /**
         * This function intentionally left empty.
         */
    }

    /**
     * Returns how many steps we have in this process.
     *
     * If this method isn't overwritten by a child, it defaults to 1.
     *
     * @since 3.4.0
     * @return  int 
     */
    public function get_steps()
    {
        return 1;
    }

    /**
     * Adds an error to the response object.
     * 
     * @param $slug (String) The slug for this error code.
     * @param $msg (String) The error message to be displayed.
     * @param $type (String) warning or fatal, depending on the error.
     *                       Defaults to warning.
     * 
     * @since 3.4.11
     */
    public function add_error( $slug, $msg, $type = 'warning' )
    {
        // Setup our errors array if it doesn't exist already.
        if ( ! isset( $this->response[ 'errors' ] ) ) {
            $this->response[ 'errors' ] = array();
        }
        $this->response[ 'errors' ][] = array(
            'code' => $slug,
            'message' => $msg,
            'type' => $type
        );
    }

    /**
     * Function to cleanup any lingering temporary elements of a batch process after completion.
     *
     * @since 3.4.0
     * @return  void 
     */
    public function cleanup()
    {
        /**
         * This function intentionally left empty.
         */
    }

    /**
     * Method called when we are finished with this process.
     *
     * Deletes our "doing" option.
     * Set's our response 'batch_complete' to true.
     * Runs cleanup().
     * Responds to the JS front-end.
     *
     * @since 3.4.0
     * @return  void 
     */
    public function batch_complete()
    {
        // Delete our options.
        delete_option( 'nf_doing_' . $this->_slug );
        // Tell our JS that we're done.
        $this->response[ 'batch_complete' ] = true;

        $this->cleanup();
        $this->respond();
    }

    /**
     * Method that immediately moves on to the next step.
     *
     * Used in child methods to stop processing the current step an dmove to the next.
     *
     * @since 3.4.0
     * @return  void 
     */
    public function next_step()
    {
        // ..see how many steps we have left, update our option, and send the remaining step to the JS.
        $this->response[ 'step_remaining' ] = $this->get_steps();
        $this->respond();
    }

    /**
     * Method that encodes $this->response and sends the data to the front-end.
     * 
     * @since 3.4.0
     * @updated 3.4.11
     * @return  void 
     */
    public function respond()
    {
        if ( ! empty( $this->response[ 'errors' ] ) ) {
            $this->response[ 'errors' ] = array_unique( $this->response[ 'errors' ] );
        }

        echo wp_json_encode( $this->response );
        wp_die();
    }

}