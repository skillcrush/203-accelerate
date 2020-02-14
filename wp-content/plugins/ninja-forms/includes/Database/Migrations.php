<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Database_Migrations
{
    protected $migrations = array();


    /**
     * Constructor method for the NF_Database_Migrations class.
     * 
     * @since 3.0.0
     * 
     * @updated 3.3.8
     */
    public function __construct()
    {
        $this->migrations[ 'forms' ]         = new NF_Database_Migrations_Forms();
        $this->migrations[ 'form_meta' ]     = new NF_Database_Migrations_FormMeta();
        $this->migrations[ 'fields' ]        = new NF_Database_Migrations_Fields();
        $this->migrations[ 'field_meta' ]    = new NF_Database_Migrations_FieldMeta();
        $this->migrations[ 'actions' ]       = new NF_Database_Migrations_Actions();
        $this->migrations[ 'action_meta' ]   = new NF_Database_Migrations_ActionMeta();
        $this->migrations[ 'objects' ]       = new NF_Database_Migrations_Objects();
        $this->migrations[ 'object_meta' ]   = new NF_Database_Migrations_ObjectMeta();
        $this->migrations[ 'relationships' ] = new NF_Database_Migrations_Relationships();
        $this->migrations[ 'settings' ]      = new NF_Database_Migrations_Settings();
        $this->migrations[ 'upgrades' ]      = new NF_Database_Migrations_Upgrades();
        $this->migrations[ 'chunks' ]        = new NF_Database_Migrations_Chunks();
    }


    /**
     * Function to run each migration on the stack.
     * 
     * @since 3.0.0
     */
    public function migrate()
    {
        foreach( $this->migrations as $migration ){
            $migration->_run();
        }
    }


    /**
     * Function to run any required database upgrades.
     * 
     * @param $callback (String) The method this upgrade will call from individual migration files.
     * 
     * @since 3.4.0
     */
    public function do_upgrade( $callback )
    {
        foreach( $this->migrations as $migration ) {
            $migration->_do_upgrade( $callback );
        }
    }

    /**
     * This function drops ninja forms tables and options
     * 
     * @param $areYouSure (Boolean)
     * @param $areYouReallySure (Boolean)
     * @param $nuke_multisite (Boolean)
     * 
     * @since 2.9.34
     * @updated 3.3.16
     */
    public function nuke( $areYouSure = FALSE, $areYouReallySure = FALSE, $nuke_multisite = TRUE )
    {
        if( ! $areYouSure || ! $areYouReallySure ) return;

        global $wpdb;

        if( ! function_exists( 'is_multisite' ) || ! is_multisite() ){
            $this->_nuke();
            return;
        }
        // adding this to make sure we don't nuke ALL subsites when upgrading one subsite
        if ( $nuke_multisite ) {
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

            foreach( $blog_ids as $blog_id ){
                switch_to_blog( $blog_id );
                $this->_nuke();
                restore_current_blog(); // Call after EVERY switch_to_blog().
            }
        } else {
            $this->_nuke();
            return;
        }
    }


    /**
     * Function to handle the actual deletion of tables and caches.
     * 
     * @since 3.1.0
     */
    private function _nuke()
    {
        global $wpdb;

        /* Drop THREE Tables */
        foreach( $this->migrations as $migration ){
            $migration->_drop();
        }

        /* Delete form caches */
        $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE 'nf_form_%'" );
        $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_nf_form_%'" );
        $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_timeout_nf_form_%'" );
    }


    /**
     * Function to nuke our 3.0 settings.
     * 
     * @param $areYouSure (Boolean)
     * @param $areYouReallySure (Boolean)
     * 
     * @since 3.1.0
     */
    public function nuke_settings( $areYouSure = FALSE, $areYouReallySure = FALSE )
    {
        if( ! $areYouSure || ! $areYouReallySure ) return;

        global $wpdb;

        if( ! function_exists( 'is_multisite' ) || ! is_multisite() ){
            $this->_nuke_settings();
            return;
        }

        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

        foreach( $blog_ids as $blog_id ){
            switch_to_blog( $blog_id );
            $this->_nuke_settings();
            restore_current_blog(); // Call after EVERY switch_to_blog().
        }
    }


    /**
     * Function to handle the actual deletion of our 3.0 settings.
     * 
     * @since 3.1.0
     */
    private function _nuke_settings()
    {
        global $wpdb;
        /* Delete known options */
        delete_option( 'nf_admin_notice' );
        delete_option( 'nf_form_tel_data' );
        delete_option( 'nf_form_tel_sent' );
        delete_option( 'nf_tel_collate' );
        delete_option( 'ninja_forms_allow_tracking' );
        delete_option( 'ninja_forms_db_version' );
        delete_option( 'ninja_forms_do_not_allow_tracking' );
        delete_option( 'ninja_forms_load_deprecated' );
        delete_option( 'ninja_forms_mailchimp_interests' );
        delete_option( 'ninja_forms_oauth_client_secret' );
        delete_option( 'ninja_forms_optin_reported' );
        delete_option( 'ninja_forms_settings' );
        delete_option( 'ninja_forms_transactional_email_enabled' );
        delete_option( 'ninja_forms_version' );

        /* Delete possible options */
        $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE 'wp_nf_%'" );
        $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE 'ninja_forms_%'" );

        /* Delete background processing flags */
        $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE 'wp_nf_update_fields_%'" );
    }


    /**
     * Function to nuke our 2.9 database tables.
     * 
     * @param $areYouSure (Boolean)
     * @param $areYouReallySure (Boolean)
     * 
     * @since 3.1.0
     */
    public function nuke_deprecated( $areYouSure = FALSE, $areYouReallySure = FALSE  )
    {
        if( ! $areYouSure || ! $areYouReallySure ) return;

        global $wpdb;

        if( ! function_exists( 'is_multisite' ) || ! is_multisite() ){
            $this->_nuke_deprecated();
            return;
        }

        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

        foreach( $blog_ids as $blog_id ){
            switch_to_blog( $blog_id );
            $this->_nuke_deprecated();
            restore_current_blog(); // Call after EVERY switch_to_blog().
        }
    }


    /**
     * Function to handle the actual deletion of deprecated tables and options.
     * 
     * @since 3.1.0
     */
    private function _nuke_deprecated()
    {
        global $wpdb;

        /* Drop Deprecated Tables (v2.9.x) */
        $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}nf_objectmeta`" );
        $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}nf_objects`" );
        $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}nf_relationships`" );
        $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}ninja_forms_fav_fields`" );
        $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}ninja_forms_fields`" );

        /* Delete Deprecated Options (v2.9.x) */
        delete_option( 'nf_upgrade_notice' );
        delete_option( 'nf_converted_subs' );
        delete_option( 'nf_converted_forms' );
        delete_option( 'nf_convert_subs_num' );
        delete_option( 'nf_convert_subs_step' );
        delete_option( 'nf_convert_subs_step' );
        delete_option( 'nf_email_fav_updated' );
        delete_option( 'nf_database_migrations' );
        delete_option( 'nf_converted_form_reset' );
        delete_option( 'nf_version_upgraded_from' );
        delete_option( 'nf_convert_forms_complete' );
        delete_option( 'nf_convert_notifications_forms' );
        delete_option( 'nf_convert_notifications_complete' );
        delete_option( 'nf_update_email_settings_complete' );

        /* Delete Deprecarted Upgrade Options (v2.9.x) */
        $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE 'nf_upgrade_%'" );

        /* Maybe Remove Deprecated Scheduled Cron (v2.9.x) */
        if( $timestamp = wp_next_scheduled( 'ninja_forms_daily_action' ) ){
            wp_unschedule_event( $timestamp, 'ninja_forms_daily_action' );
        }
    }

}
