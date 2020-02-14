<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_Admin_Menus_Settings extends NF_Abstracts_Submenu
{
    public $parent_slug = 'ninja-forms';

    public $menu_slug = 'nf-settings';

    public $priority = 11;

    protected $_prefix = 'ninja_forms';

    public function __construct()
    {
        parent::__construct();

        if( isset( $_POST[ 'update_ninja_forms_settings' ] ) ) {
            add_action( 'admin_init', array( $this, 'update_settings' ) );
        }

        add_action( 'admin_body_class', array( $this, 'body_class' ) );

        // Catch Contact Form 7 reCAPTCHA conflict.
        add_filter( 'nf_admin_notices', array( $this, 'ninja_forms_cf7_notice' ) );
    }

    public function body_class( $classes )
    {
        // Add class for the builder.
        if( isset( $_GET['page'] ) && $_GET['page'] == $this->menu_slug ) {
            $classes = "$classes ninja-forms-settings";
        }

        return $classes;
    }
    
    /**
     * Function to notify users of CF7 conflict
     * 
     * Since 3.0
     *
     * @param (array) $notices
     * @return (array) $notices
     */
    public function ninja_forms_cf7_notice( $notices )
    {
        // If we don't have recaptcha keys, bail.
        $recaptcha_site_key = Ninja_Forms()->get_settings();
        if ( $recaptcha_site_key[ 'recaptcha_site_key' ] === '' ) {
            return $notices;
        }
        // If we can detect Contact Form 7...
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
            $notices[ 'cf7' ] = array(
                'title' => esc_html__( 'Contact Form 7 is currently activated.', 'ninja-forms' ),
                'msg' => sprintf( esc_html__( 'Please be aware that there is an issue with Contact Form 7 that breaks reCAPTCHA in other plugins.%sIf you need to use reCAPTCHA on any of your Ninja Forms, you will need to disable Contact Form 7.', 'ninja-forms' ), '<br />' ),
                'int' => 0
            );
        }
        return $notices;
    }

    public function get_page_title()
    {
        return esc_html__( 'Settings', 'ninja-forms' );
    }

    public function get_capability()
    {
        return apply_filters( 'ninja_forms_admin_settings_capabilities', $this->capability );
    }

    public function display()
    {
        $tabs = apply_filters( 'ninja_forms_settings_tabs', array(
                'settings' => esc_html__( 'Settings', 'ninja-forms' ),
                'licenses' => esc_html__( 'Licenses', 'ninja-forms' )
            )
        );

        $tab_keys = array_keys( $tabs );
        $active_tab = ( isset( $_GET[ 'tab' ] ) ) ? WPN_Helper::sanitize_text_field($_GET[ 'tab' ]) : reset( $tab_keys );

        wp_enqueue_style( 'nf-admin-settings', Ninja_Forms::$url . 'assets/css/admin-settings.css' );

        $groups = Ninja_Forms()->config( 'PluginSettingsGroups' );

        $grouped_settings = $this->get_settings();

        $save_button_text = esc_html__( 'Save Settings', 'ninja-forms' );

        $setting_defaults = Ninja_Forms()->get_settings();

        $errors = array();

        foreach( $grouped_settings as $group => $settings ){

            foreach( $settings as $id => $setting ){

                $value = ( isset( $setting_defaults[ $id ] ) ) ? $setting_defaults[$id] : '';

                $grouped_settings[$group][$id]['id'] = $this->prefix( $grouped_settings[$group][$id]['id'] );
                $grouped_settings[$group][$id]['value'] = $value;

                $grouped_settings[$group][$id] = apply_filters( 'ninja_forms_check_setting_' . $id, $grouped_settings[$group][$id] );

                if( ! isset( $grouped_settings[$group][$id][ 'errors' ] ) || ! $grouped_settings[$group][$id][ 'errors' ] ) continue;

                if( ! is_array( $grouped_settings[$group][$id][ 'errors' ] ) ) $grouped_settings[$group][$id][ 'errors' ] = array( $grouped_settings[$group][$id][ 'errors' ] );

                foreach( $grouped_settings[$group][$id][ 'errors' ] as $old_key => $error ){
                    $new_key = $grouped_settings[$group][$id][ 'id' ] . "[" . $old_key . "]";
                    $errors[ $new_key ] = $error;
                    $grouped_settings[$group][$id][ 'errors'][ $new_key ] = $error;
                    unset( $grouped_settings[$group][$id][ 'errors' ][ $old_key ] );
                }
            }
        }

        $grouped_settings[ 'general' ][ 'version' ][ 'value' ] = Ninja_Forms::VERSION;

        $saved_fields = Ninja_Forms()->form()->get_fields( array( 'saved' => 1 ) );

        foreach( $saved_fields as $saved_field ){

            $saved_field_id = $saved_field->get_id();

            $grouped_settings[ 'saved_fields'][] = array(
                'id' => '',
                'type' => 'html',
                'html' => '<a class="js-delete-saved-field button button-secondary" data-id="' . $saved_field_id . '">' . esc_html__( 'Delete', 'ninja-forms' ) . '</a>',
                'label' => $saved_field->get_setting( 'label' ),

            );
        }

	    $forms = Ninja_Forms()->form()->get_forms();
	    $form_options = array();
	    foreach( $forms as $form ){
		    $form_options[] = array( 'id' => $form->get_id(),
			    'title' => $form->get_setting( 'title' ) );
	    }
	    $form_options = apply_filters( 'ninja_forms_submission_filter_form_options', $form_options );
	    asort($form_options);
        
        if ( get_option( 'ninja_forms_allow_tracking' ) && '1' == get_option( 'ninja_forms_allow_tracking' ) ) {
            $allow_tel = 1;
        } else {
            $allow_tel = 0;
        }

	    wp_enqueue_script( 'jBox', Ninja_Forms::$url . 'assets/js/lib/jBox.min.js', array( 'jquery' ) );
        wp_enqueue_style( 'nf-combobox', Ninja_Forms::$url . 'assets/css/combobox.css' );
	    wp_enqueue_style( 'jBox', Ninja_Forms::$url . 'assets/css/jBox.css' );
        wp_register_script( 'ninja_forms_admin_menu_settings', Ninja_Forms::$url . 'assets/js/admin-settings.js', array( 'jquery' ), FALSE, TRUE );
        
        /**
         * This wp_localize_script call should eventually be removed.
         *
         * TODO: Remove this function call when we've replaced references to nf_settings in our JS with nfAdmin.
         */
        wp_localize_script( 'ninja_forms_admin_menu_settings', 'nf_settings', array(
            'ajax_url'      => admin_url( 'admin-ajax.php' ),
            'forms'         => $form_options,
            'nf_nuke_title' => esc_html__( 'Remove ALL Ninja Forms data and uninstall?', 'ninja-forms' ),
            'nonce'         => wp_create_nonce( "ninja_forms_settings_nonce" ),
            'batchNonce'   => wp_create_nonce( 'ninja_forms_batch_nonce' ),
            'i18n'          => array(
                'downgradeMessage'                 => esc_html__( 'Are you sure you want to downgrade?', 'ninja-forms' ),
                'downgradeWarningMessage'          => esc_html__( 'You WILL lose any forms or submissions created on this version of Ninja Forms.', 'ninja-forms' ),
                'downgradeConfirmMessage'          => esc_html__( 'Type ', 'ninja-forms' ) . '<span style="color: red";>' . 'DOWNGRADE' . "</span>" . esc_html__( ' to confirm.', 'ninja-forms' ),
                'downgradeButtonPrimary'           => esc_html__( 'Downgrade', 'ninja-forms'),
                'downgradeButtonSecondary'         => esc_html__( 'Cancel', 'ninja-forms' ),
                'trashExpiredSubsMessage'          => esc_html__( 'Are you sure you want to trash all expired submissions?', 'ninja-forms' ),
                'trashExpiredSubsButtonPrimary'    => esc_html__( 'Trash', 'ninja-forms' ),
                'trashExpiredSubsButtonSecondary'  => esc_html__( 'Cancel', 'ninja-forms' ),
            ),
            'allow_telemetry' => $allow_tel,
        ));

        /**
         * Duplicating the localization above with an nfAdmin variable for consistency.
         *
         * Eventually, we should remove all references to nf_settings, which isn't very descriptive or specific with nfAdmin instead.
         *
         * TODO: Replace references to nf_settings object in admin JS files with nfAdmin.
         */
        wp_localize_script( 'ninja_forms_admin_menu_settings', 'nfAdmin', array(
            'ajax_url'      => admin_url( 'admin-ajax.php' ),
            'forms'         => $form_options,
            'nf_nuke_title' => esc_html__( 'Remove ALL Ninja Forms data and uninstall?', 'ninja-forms' ),
            'nonce'         => wp_create_nonce( "ninja_forms_settings_nonce" ),
            'batchNonce'   => wp_create_nonce( 'ninja_forms_batch_nonce' ),
            'i18n'          => array(
                'downgradeMessage'                 => esc_html__( 'Are you sure you want to downgrade?', 'ninja-forms' ),
                'downgradeWarningMessage'          => esc_html__( 'You WILL lose any forms or submissions created on this version of Ninja Forms.', 'ninja-forms' ),
                'downgradeConfirmMessage'          => esc_html__( 'Type ', 'ninja-forms' ) . '<span style="color: red";>' . 'DOWNGRADE' . "</span>" . esc_html__( ' to confirm.', 'ninja-forms' ),
                'downgradeButtonPrimary'           => esc_html__( 'Downgrade', 'ninja-forms'),
                'downgradeButtonSecondary'         => esc_html__( 'Cancel', 'ninja-forms' ),
                'trashExpiredSubsMessage'          => esc_html__( 'Are you sure you want to trash all expired submissions?', 'ninja-forms' ),
                'trashExpiredSubsButtonPrimary'    => esc_html__( 'Trash', 'ninja-forms' ),
                'trashExpiredSubsButtonSecondary'  => esc_html__( 'Cancel', 'ninja-forms' ),
            ),
            'allow_telemetry' => $allow_tel,
        ));
        wp_enqueue_script( 'nf-ninja-modal', Ninja_Forms::$url . 'assets/js/lib/ninjaModal.js' );
        wp_enqueue_script( 'nf-ninja-batch-processor', Ninja_Forms::$url . 'assets/js/lib/batch-processor.js' );
        wp_enqueue_style( 'nf-font-awesome', Ninja_Forms::$url . 'assets/css/font-awesome.min.css' );


        wp_enqueue_script( 'ninja_forms_admin_menu_settings' );

        Ninja_Forms::template( 'admin-menu-settings.html.php', compact( 'tabs', 'active_tab', 'groups', 'grouped_settings', 'save_button_text', 'errors' ) );

    }

    public function update_settings()
    {
        if( ! wp_verify_nonce( $_POST['update_ninja_forms_settings_nonce'], 'ninja_forms_settings_nonce' ) ) {
            wp_die( esc_html__( 'Your request could not be verified. Please try again.', 'ninja-forms' ) );
        }
        
        if( ! current_user_can( apply_filters( 'ninja_forms_admin_settings_capabilities', 'manage_options' ) ) ) return;

        if( ! isset( $_POST[ $this->_prefix ] ) ) return;

        $settings = WPN_Helper::sanitize_text_field($_POST[ 'ninja_forms' ]);

        if( isset( $settings[ 'currency' ] ) ){
            $currency = sanitize_text_field( $settings[ 'currency' ] );
            $currency_symbols = Ninja_Forms::config( 'CurrencySymbol' );
            $settings[ 'currency_symbol' ] = ( isset( $currency_symbols[ $currency ] ) ) ? $currency_symbols[ $currency ] : '';
        }

        if(isset($settings['builder_dev_mode'])){
            $builder_dev_mode = sanitize_text_field( $settings['builder_dev_mode'] );
            $has_builder_dev_mode_changed = ($builder_dev_mode !== Ninja_Forms()->get_setting('builder_dev_mode'));
            if($builder_dev_mode && $has_builder_dev_mode_changed){
                Ninja_Forms()->dispatcher()->send( 'builder_dev_mode', $builder_dev_mode );
            }
        }

        if(isset($settings['opinionated_styles'])){
            if('' == $settings['opinionated_styles']){
                Ninja_Forms()->dispatcher()->send( 'opinionated_styles_disabled', 'disabled' );
            }
        }

        foreach( $settings as $id => $value ){
            $value = sanitize_text_field( $value );
            $value = apply_filters( 'ninja_forms_update_setting_' . $id, $value );
            Ninja_Forms()->update_setting( $id, $value );
            do_action( 'ninja_forms_save_setting_' . $id, $value );
        }
    }

    private function get_settings()
    {
        return apply_filters( 'ninja_forms_plugin_settings', array(
            'general' => Ninja_Forms()->config( 'PluginSettingsGeneral' ),
            'recaptcha' => Ninja_Forms()->config( 'PluginSettingsReCaptcha' ),
            'advanced' => Ninja_Forms()->config( 'PluginSettingsAdvanced' ),
        ));
    }

    private function prefix( $value ){
        return "{$this->_prefix}[$value]";
    }

} // End Class NF_Admin_Settings
