<?php
if (!defined('ABSPATH')) {
    exit;
}

class NF_Php_Version_Whip
{
    /**
     * NF_Whip constructor.
     * Hooks into the WordPress admin notice system and calls our whip_message.
     * @Since 3.2.19
     */
    public function __construct()
    {
        // Gets our transient
        $transient = get_transient('nf_dismiss_php_version_whip');

        // Checks our transient and fires the message.
        if (false == $transient && current_user_can('administrator')) {
            add_action('admin_notices', array( $this, 'whipMessage'));
            $this->set_dismiss();
        }
    }

    /**
     * Whip Message
     * Builds and outputs our message.
     * @Since 3.2.19
     */
    public function whipMessage()
    {
        wp_enqueue_style( 'nf-admin-notices', Ninja_Forms::$url .'assets/css/admin-notices.css?nf_ver=' . Ninja_Forms::VERSION );

        $dismiss_url = add_query_arg(
            array(
                'page' => 'ninja-forms',
                'dismiss-php-version-whip-message' => 'true'
            ),
            admin_url() . 'admin.php'
        );
        $admin_display_link = sprintf( esc_html__('%sDismiss this for 4 weeks.%s', 'ninja-forms'),
            '<a href="' . esc_url($dismiss_url) . '" target="_self">',
            '</a>'
        );
        $admin_display_msg = '<p>' . sprintf( esc_html__( 'We have detected that your website is currently running an older version of PHP than is %srecommended by WordPress%s. This may cause security vulnerabilities, performance issues, and compatibility problems with many modern plugins including Ninja Forms.', 'ninja-forms' ), '<a href="https://wordpress.org/about/requirements/" target="_blank">', '</a>' ); 
        $admin_display_msg .= '</p><p>';
        $admin_display_msg .= esc_html__( 'Please contact your hosting provider to upgrade your PHP version and prevent these issues. You should also make sure that your plugins and theme are tested with and support PHP version 7.2 or higher.', 'ninja-forms' );
        $admin_display_msg .= '</p>';

        echo '<div class="update-nag nf-admin-notice">';
        echo '<div class="nf-notice-logo"></div>';
        echo ' <p class="nf-notice-body">';
        echo $admin_display_msg;
        echo ' </p>';
        echo '<ul class="nf-notice-body nf-red">
                ' . $admin_display_link . '
              </ul>';
        echo '</div>';
    }

    /**
     * Set Dismiss
     * Sets a transient for 4 weeks out that will remove the whip notice.
     * @Since 3.2.19
     */
    public function set_dismiss()
    {
        if (isset($_GET[ 'page' ]) && 'ninja-forms' == $_GET['page']
            && isset($_GET['dismiss-php-version-whip-message']) && 'true' == $_GET['dismiss-php-version-whip-message']) {
                set_transient('nf_dismiss_php_version_whip', 1, 60 * 60 * 24 * 28);
        }
    }
}
