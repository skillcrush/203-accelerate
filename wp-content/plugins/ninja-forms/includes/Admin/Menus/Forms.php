<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_Admin_Menus_Forms extends NF_Abstracts_Menu
{
    public $page_title = 'Ninja Forms';

    public $menu_slug = 'ninja-forms';

    public $icon_url = 'dashicons-feedback';

    public $position = '35.1337';

    public $ver = Ninja_Forms::VERSION;

    // Stores whether or not this form has a password field.
    private $legacy_password = false;

    public function __construct()
    {
        parent::__construct();

        if( ! defined( 'DOING_AJAX' ) ) {
            add_action('current_screen', array($this, 'admin_init'));
            add_action( 'current_screen', array( 'NF_Admin_AllFormsTable', 'process_bulk_action' ) );
        }

        add_action( 'admin_body_class', array( $this, 'body_class' ) );
        add_action( 'admin_init', array( $this, 'nf_upgrade_redirect' ) );
    }

    /**
     * If we have required updates, redirect to the main Ninja Forms page
     */
    public function nf_upgrade_redirect() {
        global $pagenow;
            
        if( "1" == get_option( 'ninja_forms_needs_updates' ) &&
            'admin.php' == $pagenow && 
            'ninja-forms' == $_GET[ 'page' ] &&
            isset( $_GET[ 'form_id' ] ) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=ninja-forms' ), 301 );
                exit;
            
        }
    }

    public function body_class( $classes )
    {
        // Add class for the builder.
        if( isset( $_GET['page'] ) && isset( $_GET[ 'form_id' ] ) && $_GET['page'] == $this->menu_slug ) {
            $classes = "$classes ninja-forms-app";
        }

        return $classes;
    }

    public function get_page_title()
    {
        return esc_html__( 'Ninja Forms', 'ninja-forms' );
    }

    public function admin_init()
    {
        /*
         * If we aren't on the Ninja Forms menu page, don't admin_init.
         */
        if ( empty( $_GET[ 'page' ] ) || 'ninja-forms' !== $_GET[ 'page' ] ) {
            return false;
        }

        /*
         * Database Table Check
         * If the nf3_ database tables do not exist, then re-run activation.
         */
        if ( ! ninja_forms_three_table_exists() ) {
            Ninja_Forms()->activation();
        }

        if( isset( $_GET[ 'form_id' ] ) && ! is_numeric( $_GET[ 'form_id' ] ) && 'new' != $_GET[ 'form_id' ] ) {
            if( current_user_can( apply_filters( 'ninja_forms_admin_import_template_capabilities', 'manage_options' ) ) ) {
                $this->import_from_template();
            }
        }

        /* DISABLE OLD FORMS TABLE IN FAVOR OF NEW DASHBOARD */
//        $this->table = new NF_Admin_AllFormsTable();
    }

    public function display()
    {
        if( isset( $_GET[ 'form_id' ] ) ){


            if( 'new' == $_GET[ 'form_id' ] ) {
                $form_id = 'tmp-' . time();
            } else {
                $form_id = (is_numeric($_GET['form_id'])) ? absint($_GET['form_id']) : '';
            }

            /*
             * FORM BUILDER
             */

            Ninja_Forms::template( 'admin-menu-new-form.html.php' );

            Ninja_Forms::template( 'fields-label--builder.html' ); // Fork for the builder.

            Ninja_Forms::template( 'fields-address.html' );
            Ninja_Forms::template( 'fields-address2.html' );
            Ninja_Forms::template( 'fields-button.html' );
            Ninja_Forms::template( 'fields-checkbox.html' );
            Ninja_Forms::template( 'fields-city.html' );
            Ninja_Forms::template( 'fields-color.html' );
            Ninja_Forms::template( 'fields-date.html' );
            Ninja_Forms::template( 'fields-email.html' );
            Ninja_Forms::template( 'fields-file.html' );
            Ninja_Forms::template( 'fields-firstname.html' );
            Ninja_Forms::template( 'fields-hidden.html' );
            Ninja_Forms::template( 'fields-hr.html' );
            Ninja_Forms::template( 'fields-html.html' );
            Ninja_Forms::template( 'fields-input.html' );
            Ninja_Forms::template( 'fields-lastname.html' );
            Ninja_Forms::template( 'fields-listcheckbox.html' );
            Ninja_Forms::template( 'fields-listradio.html' );
            Ninja_Forms::template( 'fields-listselect--builder.html' ); // Fork that removes the `for` attribute, which hijacks click events.
            Ninja_Forms::template( 'fields-number.html' );
            Ninja_Forms::template( 'fields-password.html' );
            Ninja_Forms::template( 'fields-recaptcha.html' );
            Ninja_Forms::template( 'fields-starrating.html' );
            Ninja_Forms::template( 'fields-submit.html' );
            Ninja_Forms::template( 'fields-tel.html' );
            Ninja_Forms::template( 'fields-terms.html' );
            Ninja_Forms::template( 'fields-textarea.html' );
            Ninja_Forms::template( 'fields-textbox.html' );
            Ninja_Forms::template( 'fields-zip.html' );
            
            // Deprecated Fields
            Ninja_Forms::template( 'fields-total.html' );
            Ninja_Forms::template( 'fields-tax.html' );
            Ninja_Forms::template( 'fields-product.html' );
            Ninja_Forms::template( 'fields-shipping.html' );

            

            $this->_enqueue_the_things( $form_id );

            delete_user_option( get_current_user_id(), 'nf_form_preview_' . $form_id );

            if( ! isset( $_GET[ 'ajax' ] ) ) {
                $this->_localize_form_data( $form_id );

                $this->_localize_field_type_data();

                $this->_localize_action_type_data();

                $this->_localize_form_settings();

                $this->_localize_merge_tags();
            }
        } else {

            /*
             * ALL FORMS TABLE
             * - DISABLE IN FAVOR OF NEW DASHBOARD.
             */

//            $this->table->prepare_items();
//
//            Ninja_Forms::template( 'admin-menu-all-forms.html.php', array(
//                'table' => $this->table,
//                'add_new_url' => admin_url( 'admin.php?page=ninja-forms&form_id=new' ),
//                'add_new_text' => __( 'Add New Form', 'ninja-forms' )
//            ) );

            $use_services = true; // Feature Flag.
            $use_services = apply_filters( 'ninja_forms_use_services', $use_services ); // The WordPress Way.
            if ( apply_filters( 'ninja_forms_disable_marketing', false ) ) $use_services = false;
            $use_services = $use_services && ( version_compare( PHP_VERSION, '5.6', '>=' ) ); // PHP Version Check.


            /*
             * DASHBOARD
             */
            $dash_items = Ninja_Forms()->config('DashboardMenuItems');
            ?>
            <script>
                var nfDashItems = <?php echo( json_encode( array_values( $dash_items ) ) ); ?>;
                var useServices = <?php echo ( $use_services ) ? 'true' : 'false'; ?>;
                var serviceSuccess = '<?php echo ( isset( $_GET[ 'success' ] ) ) ? htmlspecialchars( $_GET[ 'success' ] ) : ''; ?>';
            </script>
            <?php

            $required_updates = get_option( 'ninja_forms_needs_updates', 0 );

            wp_enqueue_script( 'backbone-radio', Ninja_Forms::$url . 'assets/js/lib/backbone.radio.min.js', array( 'jquery', 'backbone' ) );
            wp_enqueue_script( 'backbone-marionette-3', Ninja_Forms::$url . 'assets/js/lib/backbone.marionette3.min.js', array( 'jquery', 'backbone' ) );
            wp_enqueue_script( 'nf-jbox', Ninja_Forms::$url . 'assets/js/lib/jBox.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'nf-ninjamodal', Ninja_Forms::$url . 'assets/js/lib/ninjaModal.js', array( 'jquery' ), $this->ver );
            wp_enqueue_script( 'nf-batch-processor', Ninja_Forms::$url . 'assets/js/lib/batch-processor.js', array( 'nf-ninjamodal' ), $this->ver );
            wp_enqueue_script( 'nf-moment', Ninja_Forms::$url . 'assets/js/lib/moment-with-locales.min.js', array( 'jquery', 'nf-dashboard' ) );
            wp_enqueue_script( 'nf-dashboard', Ninja_Forms::$url . 'assets/js/min/dashboard.min.js', array( 'backbone-radio', 'backbone-marionette-3' ), $this->ver );
            wp_enqueue_script( 'nf-sendwp', Ninja_Forms::$url . 'assets/js/lib/sendwp.js', array(), $this->ver );
            wp_enqueue_script( 'nf-feature-scripts', Ninja_Forms::$url . 'assets/js/lib/feature-scripts.js', array(), $this->ver );

            $current_user = wp_get_current_user();
            wp_localize_script( 'nf-dashboard', 'nfi18n', Ninja_Forms::config( 'i18nDashboard' ) );

            $promotions = get_option( 'nf_active_promotions' );
            $promotions = json_decode( $promotions, true );

            if( ! empty( $promotions ) ) {
                wp_localize_script( 'nf-dashboard', 'nfPromotions', array_values( $promotions[ 'dashboard' ] ) );
            }

            wp_localize_script( 'nf-dashboard', 'nfAdmin', array(
                'ajaxNonce'         => wp_create_nonce( 'ninja_forms_dashboard_nonce' ),
                'batchNonce'        => wp_create_nonce( 'ninja_forms_batch_nonce' ),
                'updateNonce'       => wp_create_nonce( 'ninja_forms_required_update_nonce' ),
                'formTelemetry'     => ( get_option( 'nf_form_tel_sent' ) ) ? 0 : 1,
                'showOptin'         => ( get_option( 'ninja_forms_do_not_allow_tracking' ) ||
                                         get_option( 'ninja_forms_allow_tracking' ) ) ? 0 : 1,
                'requiredUpdates'    => $required_updates,
                'currentUserEmail'  => $current_user->user_email,
                'builderURL'        => admin_url( 'admin.php?page=ninja-forms&form_id=' ),
            ) );

            wp_enqueue_style( 'nf-builder', Ninja_Forms::$url . 'assets/css/builder.css', array(), $this->ver );
            wp_enqueue_style( 'nf-dashboard', Ninja_Forms::$url . 'assets/css/dashboard.min.css', array(), $this->ver );
            wp_enqueue_style( 'nf-jbox', Ninja_Forms::$url . 'assets/css/jBox.css' );
            wp_enqueue_style( 'nf-font-awesome', Ninja_Forms::$url . 'assets/css/font-awesome.min.css' );

            if( $required_updates ) {
                wp_enqueue_style( 'nf-updates-styles', Ninja_Forms::$url . '/assets/css/required-updates.css' );
            }

            Ninja_Forms::template( 'admin-menu-dashboard.html.php' );
        }
    }

    public function submenu_separators()
    {
        add_submenu_page( 'ninja-forms', '', '', 'read', '', '' );
    }

    /**
     * TODO: Remove this function and its hook because we are handling template imports via the batch processor.
     * @since  3.0
     * @return void
     */
    private function import_from_template()
    {
        $template = sanitize_title( $_GET['form_id'] );

        $templates = Ninja_Forms::config( 'NewFormTemplates' );

        if( isset( $templates[ $template ] ) && ! empty( $templates[ $template ][ 'form' ] ) ) {
            $form = $templates[ $template ][ 'form' ];
        } else {
            $form = Ninja_Forms::template( $template . '.nff', array(), TRUE );
        }

        if( ! $form ) die( 'Template not found' );

        $form = json_decode( html_entity_decode( $form ), true );

        $form_id = Ninja_Forms()->form()->import_form( $form );

        if( ! $form_id ){
            $error_message = ( function_exists( 'json_last_error_msg' ) && json_last_error_msg() ) ? json_last_error_msg() : esc_html__( 'Form Template Import Error.', 'ninja-forms' );
            wp_die( $error_message );
        }

        header( "Location: " . admin_url( "admin.php?page=ninja-forms&form_id=$form_id" ) );
        exit();
    }

    private function _enqueue_the_things( $form_id )
    {
        global $wp_locale;

        wp_enqueue_media();

        wp_enqueue_style( 'nf-builder', Ninja_Forms::$url . 'assets/css/builder.css', array(), $this->ver );
        wp_enqueue_style( 'nf-font-awesome', Ninja_Forms::$url . 'assets/css/font-awesome.min.css' );
        /**
         * CSS Libraries
         */
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'jBox', Ninja_Forms::$url . 'assets/css/jBox.css' );
        wp_enqueue_style( 'summernote', Ninja_Forms::$url . 'assets/css/summernote.css' );
        wp_enqueue_style( 'codemirror', Ninja_Forms::$url . 'assets/css/codemirror.css' );
        wp_enqueue_style( 'codemirror-monokai', Ninja_Forms::$url . 'assets/css/monokai-theme.css' );
        wp_enqueue_style( 'pikaday-responsive', Ninja_Forms::$url . 'assets/css/pikaday-package.css' );

        /**
         * JS Libraries
         */
        wp_enqueue_script( 'wp-util' );
        wp_enqueue_script( 'jquery-autoNumeric', Ninja_Forms::$url . 'assets/js/lib/jquery.autoNumeric.min.js', array( 'jquery', 'backbone' ) );
        wp_enqueue_script( 'jquery-maskedinput', Ninja_Forms::$url . 'assets/js/lib/jquery.maskedinput.min.js', array( 'jquery', 'backbone' ) );
        wp_enqueue_script( 'backbone-marionette', Ninja_Forms::$url . 'assets/js/lib/backbone.marionette.min.js', array( 'jquery', 'backbone' ) );
        wp_enqueue_script( 'backbone-radio', Ninja_Forms::$url . 'assets/js/lib/backbone.radio.min.js', array( 'jquery', 'backbone' ) );
        wp_enqueue_script( 'jquery-perfect-scrollbar', Ninja_Forms::$url . 'assets/js/lib/perfect-scrollbar.jquery.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'jquery-hotkeys-new', Ninja_Forms::$url . 'assets/js/lib/jquery.hotkeys.min.js' );
        wp_enqueue_script( 'jBox', Ninja_Forms::$url . 'assets/js/lib/jBox.min.js' );
        wp_enqueue_script( 'nf-ninjamodal', Ninja_Forms::$url . 'assets/js/lib/ninjaModal.js', array( 'jBox' ), $this->ver );
        wp_enqueue_script( 'nf-jquery-caret', Ninja_Forms::$url . 'assets/js/lib/jquery.caret.min.js' );
        wp_enqueue_script( 'speakingurl', Ninja_Forms::$url . 'assets/js/lib/speakingurl.js' );
        wp_enqueue_script( 'jquery-slugify', Ninja_Forms::$url . 'assets/js/lib/slugify.min.js', array( 'jquery', 'speakingurl' ) );
        wp_enqueue_script( 'jquery-mobile-events', Ninja_Forms::$url . 'assets/js/lib/jquery.mobile-events.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'jquery-ui-touch-punch', Ninja_Forms::$url . 'assets/js/lib/jquery.ui.touch-punch.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'jquery-classy-wiggle', Ninja_Forms::$url . 'assets/js/lib/jquery.classywiggle.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'moment-with-locale', Ninja_Forms::$url . 'assets/js/lib/moment-with-locales.min.js', array( 'jquery', 'nf-builder' ) );
        wp_enqueue_script( 'pikaday', Ninja_Forms::$url . 'assets/js/lib/pikaday.min.js', array( 'moment-with-locale' ) );
        wp_enqueue_script( 'pikaday-responsive', Ninja_Forms::$url . 'assets/js/lib/pikaday-responsive.min.js', array( 'pikaday', 'modernizr' ) );

        wp_enqueue_script( 'bootstrap', Ninja_Forms::$url . 'assets/js/lib/bootstrap.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'codemirror', Ninja_Forms::$url . 'assets/js/lib/codemirror.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'codemirror-xml', Ninja_Forms::$url . 'assets/js/lib/codemirror-xml.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'codemirror-formatting', Ninja_Forms::$url . 'assets/js/lib/codemirror-formatting.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'summernote', Ninja_Forms::$url . 'assets/js/lib/summernote.min.js', array( 'jquery', 'speakingurl' ) );


        wp_enqueue_script( 'nf-builder', Ninja_Forms::$url . 'assets/js/min/builder.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-effects-bounce', 'wp-color-picker' ), $this->ver );
        wp_localize_script( 'nf-builder', 'nfi18n', Ninja_Forms::config( 'i18nBuilder' ) );

        $home_url = parse_url( home_url() );

        global $wp_rewrite;
        if($wp_rewrite->permalink_structure) {
            $public_link_structure = site_url() . '/ninja-forms/[FORM_ID]';
        } else {
            $public_link_structure = site_url('?nf_public_link=[FORM_ID]');
        }

        if(isset($_GET['nf_dev_mode']) && $_GET['nf_dev_mode']){
            $dev_mode = absint($_GET['nf_dev_mode']);
        } else {
            // @NOTE Check the settings array to avoid a default value in place of zero.
            $settings = Ninja_Forms()->get_settings();
            if( ! isset($settings['builder_dev_mode'])){
                $dev_mode = 1;
            } else {
                $dev_mode = $settings['builder_dev_mode'];
            }
        }

        wp_localize_script( 'nf-builder', 'nfAdmin', array(
            'ajaxNonce'         => wp_create_nonce( 'ninja_forms_builder_nonce' ),
            'batchNonce'        => wp_create_nonce( 'ninja_forms_batch_nonce' ),
            'requireBaseUrl'    => Ninja_Forms::$url . 'assets/js/',
            'previewurl'        => home_url() . '/?nf_preview_form=',
            'wp_locale'         => $wp_locale->number_format,
            'editFormText'      => esc_html__( 'Edit Form', 'ninja-forms' ),
            'mobile'            => ( wp_is_mobile() ) ? 1: 0,
            'currencySymbols'   => array_merge( array( '' => Ninja_Forms()->get_setting( 'currency_symbol' ) ), Ninja_Forms::config( 'CurrencySymbol' ) ),
            'dateFormat'        => Ninja_Forms()->get_setting( 'date_format' ),
            'formID'            => isset( $_GET[ 'form_id' ] ) ? absint( $_GET[ 'form_id' ] ) : 0,
            'home_url_host'     => $home_url[ 'host' ],
            'publicLinkStructure' => $public_link_structure,
            'devMode'           => (bool) $dev_mode,
        ));

        do_action( 'nf_admin_enqueue_scripts' );
    }

    private function _localize_form_data( $form_id )
    {
        $form = Ninja_Forms()->form( $form_id )->get();
        $form_cache = false;

        if( ! $form->get_tmp_id() ) {

            if(WPN_Helper::use_cache()) {
                $form_cache = WPN_Helper::get_nf_cache( $form_id );
            } 

            if( $form_cache ) {
                $fields = $form_cache[ 'fields' ];
            } else {
                $fields = ($form_id) ? Ninja_Forms()->form($form_id)->get_fields() : array();
            }
            $actions = ($form_id) ? Ninja_Forms()->form($form_id)->get_actions() : array();
        } else {
            $fields = array();
            $actions = array();
        }

        $fields_settings = array();

        if( ! empty( $fields ) ) {

            // TODO: Replace unique field key checks with a refactored model/factory.
//            $unique_field_keys = array();
//            $form_cache = get_option( 'nf_form_' . $form_id, false );
//            $cache_updated = false;

            foreach ($fields as $field) {
                
                $field_id = ( is_object( $field ) ) ? $field->get_id() : $field[ 'id' ];
                /*
                 * Duplicate field check.
                 * TODO: Replace unique field key checks with a refactored model/factory.
                 */
//                $field_key = $field->get_setting( 'key' );
//                if( in_array( $field_key, $unique_field_keys ) || '' == $field_key ){
//
//                    // Delete the field.
//                    Ninja_Forms()->request( 'delete-field' )->data( array( 'field_id' => $field_id ) )->dispatch();
//
//                    // Remove the field from cache.
//                    if( $form_cache ) {
//                        if( isset( $form_cache[ 'fields' ] ) ){
//                            foreach( $form_cache[ 'fields' ] as $cached_field_key => $cached_field ){
//                                if( ! isset( $cached_field[ 'id' ] ) ) continue;
//                                if( $field_id != $cached_field[ 'id' ] ) continue;
//
//                                // Flag cache to update.
//                                $cache_updated = true;
//
//                                unset( $form_cache[ 'fields' ][ $cached_field_key ] ); // Remove the field.
//                            }
//                        }
//                    }
//
//                    continue; // Skip the duplicate field.
//                }
//                array_push( $unique_field_keys, $field_key ); // Log unique key.
                /* END Duplicate field check. */

                $type = ( is_object( $field ) ) ? $field->get_setting( 'type' ) : $field[ 'settings' ][ 'type' ];

                /*
                 * As of version 3.3.16, we want password fields to only show up if the user is using an add-on that requires them.
                 * But, because we don't want to break any forms that may already have a password field, we enable them if the current form already has them.
                 * The $legacy_password class var holds whether or not this form has a pre-existing password or confirm password field.
                 */
                if ( 'password' == $type || 'passwordconfirm' == $type ) {
                    $this->legacy_password = true;
                }

                if( ! isset( Ninja_Forms()->fields[ $type ] ) ){
                    $field = NF_Fields_Unknown::create( $field );
                }

                $settings = ( is_object( $field ) ) ? $field->get_settings() : $field[ 'settings' ];
                $settings[ 'id' ] =  $field_id;

                $settings = $this->null_data_check( $settings );

                $fields_settings[] = $settings;
            }

//            if( $cache_updated ) {
//                update_option('nf_form_' . $form_id, $form_cache); // Update form cache without duplicate fields.
//            }
        }

        $actions_settings = array();

        if( ! empty( $actions ) ) {
            foreach ($actions as $action) {

                $type = $action->get_setting( 'type' );

                if( ! isset( Ninja_Forms()->actions[ $type ] ) ) continue;

                $settings = $action->get_settings();
                $settings['id'] = $action->get_id();

                $settings = $this->null_data_check( $settings );

                $actions_settings[] = $settings;
            }
        }

        if( $form->get_tmp_id() ){

            $actions_settings = Ninja_Forms()->config( 'FormActionDefaults' );
        }

        $form_data = array();
        $form_data['id'] = $form_id;

        // Use form cache for form settings.
        // TODO: Defer to refactor of factory/model.
        if( $form_cache && isset( $form_cache[ 'settings' ] ) ) {
            $form_data['settings'] = $form_cache[ 'settings' ];
        } else {
            $form_data['settings'] = $form->get_settings();
        }

        $form_data['fields'] = $fields_settings;
        $form_data['actions'] = $actions_settings;

        ?>
        <script>
            var preloadedFormData = <?php echo wp_json_encode( $form_data ); ?>;
            // console.log( preloadedFormData );
        </script>
        <?php
    }

    /**
     * Null Data Check
     * Accepts array of settings and removes null data the array.
     *
     * @param $settings - a key/value pair of settings.
     * @return array
     */
    private function null_data_check( $settings )
    {
        // Loop over the settings we receive.
        foreach ($settings as $key => $setting) {
            // Check for null values in the settings array.
            if ( null === $setting ) {
                // Remove null settings from the array.
                unset( $settings[ $key ] );
                continue;
            }
        }
        return $settings;
    }

    private function _localize_field_type_data()
    {
        $field_type_sections = array_values( Ninja_Forms()->config( 'FieldTypeSections' ) );
        $field_type_settings = array();

        $master_settings = array();

        $setting_defaults = array();

        foreach( Ninja_Forms()->fields as $field ){
            if ( 'password' == $field->get_type() || 'passwordconfirm' == $field->get_type() ) {
                if( ! $this->legacy_password && ! apply_filters( 'ninja_forms_enable_password_fields', false ) ){
                    continue;
                }
            }

            $name = $field->get_name();
            $settings = $field->get_settings();
            $groups = Ninja_Forms::config( 'SettingsGroups' );

            $unique_settings = $this->_unique_settings( $settings );

            $master_settings = array_merge( $master_settings, $unique_settings );

            $settings_groups = $this->_group_settings( $settings, $groups );

            $settings_defaults = $this->_setting_defaults( $unique_settings );

            $field_type_settings[ $name ] = array(
                'id' =>  $name,
                'nicename' => $field->get_nicename(),
                'alias' => $field->get_aliases(),
                'parentType' => $field->get_parent_type(),
                'section' => $field->get_section(),
                'icon' => $field->get_icon(),
                'type' => $field->get_type(),
                'settingGroups' => $settings_groups,
                'settingDefaults' => $settings_defaults
            );
        }

        $saved_fields = Ninja_Forms()->form()->get_fields( array( 'saved' => 1) );

        foreach( $saved_fields as $saved_field ){

            $settings = $saved_field->get_settings();
            unset( $settings['cellcid'] );

            $id     = $saved_field->get_id();
            $type   = $settings[ 'type' ];
            $label  = $settings[ 'label' ];

            $field_type_settings[ $id ] = $field_type_settings[ $type ];
            $field_type_settings[ $id ][ 'id' ] = $id;
            $field_type_settings[ $id ][ 'type' ] = $type;
            $field_type_settings[ $id ][ 'nicename' ] = $label;
            $field_type_settings[ $id ][ 'section' ] = 'saved';

            $defaults = $field_type_settings[ $id ][ 'settingDefaults' ];
            $defaults = array_merge( $defaults, $settings );
            $defaults[ 'saved' ] = TRUE;

            $field_type_settings[ $id ][ 'settingDefaults' ] = $defaults;
        }

        ?>
        <script>
            var fieldTypeData     = <?php echo wp_json_encode( array_values( $field_type_settings ) ); ?>;
            var fieldSettings     = <?php echo wp_json_encode( array_values( $master_settings ) ); ?>;
            var fieldTypeSections = <?php echo wp_json_encode( $field_type_sections ); ?>;
            // console.log( fieldTypeData );
        </script>
        <?php
    }

    private function _localize_action_type_data()
    {
        $action_type_settings = array();

        $master_settings_list = array();

        foreach( Ninja_Forms()->actions as $action ){

            $name = $action->get_name();
            $settings = $action->get_settings();
            $groups = Ninja_Forms::config( 'SettingsGroups' );

            $settings_groups = $this->_group_settings( $settings, $groups );

            $master_settings_list = array_merge( $master_settings_list, $settings );

            $action_type_settings[ $name ] = array(
                'id' => $name,
                'section' => $action->get_section(),
                'nicename' => $action->get_nicename(),
                'image' => $action->get_image(),
                'settingGroups' => $settings_groups,
                'settingDefaults' => $this->_setting_defaults( $master_settings_list )
            );
        }

        $external_actions = $this->_fetch_action_feed();
        $u_id = get_option( 'nf_aff', false );
        if ( !$u_id ) $u_id = apply_filters( 'ninja_forms_affiliate_id', false );
        foreach( $external_actions as $action){

            if( ! isset( $action[ 'name' ] ) || ! $action[ 'name' ] ) continue;

            $name = $action[ 'name' ];
            $nicename = ( isset( $action[ 'nicename' ] ) ) ? $action[ 'nicename' ] : '';
            $image = ( isset( $action[ 'image' ] ) ) ? $action[ 'image' ] : '';
            $link = ( isset( $action[ 'link' ] ) ) ? $action[ 'link' ] : '';
            $modal_content = ( isset( $action[ 'modal_content' ] ) ) ? $action[ 'modal_content' ] : '';

            if ( $u_id ) {
                $last_slash = strripos( $link, '/' );
                $link = substr( $link, 0, $last_slash );
                $link =  urlencode( $link );
                $link = 'http://www.shareasale.com/r.cfm?u=' . $u_id . '&b=812237&m=63061&afftrack=&urllink=' . $link;
            }

            if( isset( $action_type_settings[ $name ] ) ) continue;

            $action_type_settings[ $name ] = array(
                'id' => $name,
                'section' => 'available',
                'nicename' => $nicename,
                'image' => $image,
                'link' => $link,
                'modal_content' => $modal_content,
                'settingGroups' => array(),
                'settingDefaults' => array()
            );
        }

        $action_type_settings = apply_filters( 'ninja_forms_action_type_settings', $action_type_settings );

        ?>
        <script>
            var actionTypeData = <?php echo wp_json_encode( array_values( $action_type_settings ) ); ?>;
            var actionSettings = <?php echo wp_json_encode( array_values( $master_settings_list ) ); ?>;
            // console.log( actionTypeData );
        </script>
        <?php
    }

    protected function _localize_form_settings()
    {
        $form_settings_types = Ninja_Forms::config( 'FormSettingsTypes' );

        $form_settings[ 'display' ] = Ninja_Forms::config( 'FormDisplaySettings' );
        $form_settings[ 'restrictions' ] = Ninja_Forms::config( 'FormRestrictionSettings' );
        $form_settings[ 'calculations' ] = Ninja_Forms::config( 'FormCalculationSettings' );
        $form_settings = apply_filters( 'ninja_forms_localize_forms_settings', $form_settings );

        foreach( $form_settings_types as $group_name => $form_setting_group ){
            if( ! isset( $form_settings[ $group_name ] ) ) $form_settings[ $group_name ] = array();
            $form_settings[ $group_name ] = apply_filters( 'ninja_forms_localize_form_' . $group_name . '_settings', $form_settings[ $group_name ] );
        }

        $groups = Ninja_Forms::config( 'SettingsGroups' );

        $master_settings = array();

        foreach( $form_settings_types as $id => $type ) {

            if( ! isset( $form_settings[ $id ] ) ) $form_settings[ $id ] = '';

            $unique_settings = $this->_unique_settings( $form_settings[ $id ] );
            $master_settings = array_merge( $master_settings, $unique_settings );

            $form_settings_types[ $id ]['settingGroups'] = $this->_group_settings($form_settings[ $id ], $groups);
            $form_settings_types[ $id ]['settingDefaults'] = $this->_setting_defaults($unique_settings);
        }
        ?>
        <script>
        var formSettingTypeData = <?php echo wp_json_encode( array_values( $form_settings_types ) )?>;
        var formSettings = <?php echo wp_json_encode( array_values( $master_settings ) )?>;
        </script>
        <?php
    }

    protected function _localize_merge_tags()
    {
        $merge_tags = array(
            'fields' => array(
                'id'    => 'fields',
                'label' => esc_html__( 'Fields', 'ninja-forms' )
            )
        );

        foreach( Ninja_Forms()->merge_tags as $key => $group ){
            /*
             * If the merge tag group doesn't have a title, don't localise it.
             *
             * This convention is used to allow merge tags to continue to function,
             * even though they can't be added to new forms.
             */
            $title = $group->get_title();
            if ( empty( $title ) ) continue;

            $merge_tags[ $key ] = array(
                'id'    => $group->get_id(),
                'label' => $group->get_title(),
                'tags'  => array_values( $group->get_merge_tags() ),
                'default_group' => $group->is_default_group()
            );
        }
        ?>
        <script>
            var mergeTags = <?php echo wp_json_encode( array_values( $merge_tags ) ); ?>;
        </script>
        <?php
    }


    protected function _group_settings( $settings, $groups )
    {
        if( ! is_array( $settings ) ) return $groups;

        foreach( $settings as $setting ){

            $group = ( isset( $setting[ 'group' ] ) ) ? $setting[ 'group' ] : '';

            if( isset( $setting[ 'type'] ) && 'fieldset' == $setting[ 'type' ] ){
                $setting[ 'settings' ] = array_values( $setting[ 'settings' ] );
            }

            $groups[ $group ][ 'settings'][] = $setting;
        }

        foreach( $groups as $id => $group ) {
            if ( empty( $group[ 'settings' ] ) ) {
                unset( $groups[ $id ] );
            }
        }

        unset( $groups[ "" ] );

        usort($groups, array( $this, 'setting_group_priority' ) );

        return $groups;
    }

    protected function _unique_settings( $settings )
    {
        $unique_settings = array();

        if( ! is_array( $settings ) ) return $unique_settings;

        foreach( $settings as $setting ){

            if( isset( $setting[ 'type' ] ) && 'fieldset' == $setting[ 'type' ] ){

                $unique_settings = array_merge( $unique_settings, $this->_unique_settings( $setting[ 'settings' ] ) );
            } else {

                $name = $setting[ 'name' ];
                $unique_settings[ $name ] = $setting;
            }

        }

        return $unique_settings;
    }

    protected function _setting_defaults( $settings )
    {
        $setting_defaults = array();

        foreach( $settings as $setting ){

            $name = ( isset( $setting[ 'name' ] ) ) ? $setting[ 'name' ] : '';
            $default = ( isset( $setting[ 'value' ] ) ) ? $setting[ 'value' ] : null;
            $setting_defaults[ $name ] = $default;
        }

        return $setting_defaults;
    }

    protected function _fetch_action_feed()
    {
        return Ninja_Forms::config( 'AvailableActions' );
    }

    protected function setting_group_priority( $a, $b )
    {
        $priority[ 0 ] = ( isset( $a[ 'priority' ] ) ) ? $a[ 'priority' ] : 500;
        $priority[ 1 ] = ( isset( $b[ 'priority' ] ) ) ? $b[ 'priority' ] : 500;

        return $priority[ 0 ] - $priority[ 1 ];
    }

    public function get_capability()
    {
        return apply_filters( 'ninja_forms_admin_parent_menu_capabilities', $this->capability );
    }

}
