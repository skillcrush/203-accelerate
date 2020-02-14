<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds a global exception handler for reporting on form rendering issues.
 *
 * Note: Exceptions before `document.ready` do not break scripts inside of `document.ready`.
 */
final class NF_ExceptionHandlerJS
{
    public function __construct()
    {
        add_action( 'wp_head', array( $this, 'wp_head' ), 6 );
    }

    public function wp_head()
    {
        if( ! current_user_can( 'manage_options' ) ) return;
        ?>
        <script type="text/javascript">
            window.addEventListener( 'error', function (e) {

                var source = e.filename.match( /plugins\/(...)/gi ) || e.filename;

                var message = '<pre>' +
                    '[ADMIN NOTICE] Form Rendering Error' +
                    '\r\rSource: ' + source +
                    '\r\r<span style="color:red;">' + e.error + '</span>' +
                    '</pre>';


                // Replace form loading animation.
                var forms = document.getElementsByClassName( 'nf-form-cont' );
                for ( var i = 0; i < forms.length; i++ ) {
                    forms[i].innerHTML = message;
                }
            });
        </script>
        <?php
    }
} // END CLASS NF_ExceptionHandlerJS
