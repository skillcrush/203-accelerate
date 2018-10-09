<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Whip
{
    /**
     * NF_Whip constructor.
     * Hooks into the WordPress admin notice system and calls our whip_message.
     * @Since 3.2.19
     */
    public function __construct()
    {
        // Gets our transient
        $transient = get_transient( 'nf_dismiss_whip' );

        // Checks our transient and fires the message.
        if( false == $transient && current_user_can('administrator') ) {
            add_action( 'admin_notices', array( $this, 'whip_message' ) );
            $this->set_dismiss();
        }
    }

    /**
     * Whip Message
     * Builds and outputs our message.
     * @Since 3.2.19
     */
    public function whip_message()
    {
        // Builds our Whip message.
        $message = array();
        $message[] = '<strong>' . __( 'Hey, we\'ve noticed that you\'re running an outdated version of PHP.', 'ninja-forms' ) . "</strong><br /><br />";
        $message[] = __( 'PHP is the programming language that WordPress, Ninja Forms, and themes are built on. The version that is currently used for your site is no longer supported. Newer versions of PHP are both faster and more secure. In fact, your version of PHP no longer receives security updates, which is why we\'re sending you to this notice.', 'ninja-forms' ) . "<br /><br />";
        $message[] = '<strong>' . __( 'Your site could be faster and more secure with a newer PHP version.', 'ninja-forms' ) . '</strong><br /><br />';
        $message[] = __( 'Hosts have the ability to update your PHP version, but sometimes they don\'t dare to do that because they\'re afraid they\'ll break your site.', 'ninja-forms' ) . '<br /><br />';
        $message[] = '<strong>' .__( 'Which version should I update?', 'ninja-forms' ) . '</strong><br /><br />';
        $message[] = sprintf( __( 'You should update your PHP version to either 5.6 or to 7.0 or 7.1. On a normal WordPress site, switching to PHP 5.6 should never cause issues. We would however actually recommend you switch to PHP7. There are some plugins that are not ready for PHP7 though, so do some testing first. Yoast have an article on how to test whether that\'s an option for you %1$shere%2$s. PHP7 is much faster than PHP 5.6. It\'s also the only PHP version still in active development and therefore the better option for your site in the long run.', 'ninja-forms' ) . '<br /><br />',
				'<a href="https://yoa.st/wg" target="_blank">',
				'</a>'
			);
        $message[] = '<strong>' . __( 'Can\'t update? Ask your host!', 'ninja-forms' ) . '</strong><br /><br />';
        $message[] = sprintf( __( 'If you cannot upgrade your PHP version yourself, you can send an email to your host. Yoast has %1$sexamples here%2$s. If they don\'t want to upgrade your PHP version, we would suggest you switch hosts. Have a look at one of the recommended %3$sWordPress hosting partners%2$s.','ninja-forms' ) . '<br /><br /><br />' ,
					'<a href="https://yoa.st/wh" target="_blank">',
					'</a>',
					sprintf( '<a href="%1$s" target="_blank">', esc_url( 'https://wordpress.org/hosting/' ) )
			);

        // Builds our
        $message[] = sprintf( __( '%1$sDismiss this for 4 weeks.%2$s', 'ninja-forms' ) . '<br />',
            '<a href="' . admin_url() . 'admin.php?page=ninja-forms&dismiss-whip-message=true" target="_self">',
            '</a>'
        );

        // Change our array to string to be displayed.
        $message = implode( $message, "\n" );

        // Output our message.
        echo '<div class="notice notice-error" style="padding: 20px">' . $message . '</div>';
    }

    /**
     * Set Dismiss
     * Sets a transient for 4 weeks out that will remove the whip notice.
     * @Since 3.2.19
     */
    public function set_dismiss()
    {
        if( isset( $_GET[ 'page' ] ) && 'ninja-forms' == $_GET[ 'page' ]
            && isset( $_GET[ 'dismiss-whip-message' ] ) && 'true' == $_GET[ 'dismiss-whip-message' ]  ) {
            set_transient( 'nf_dismiss_whip', 1, 60 * 60 * 24 * 28 );
        }
    }
}