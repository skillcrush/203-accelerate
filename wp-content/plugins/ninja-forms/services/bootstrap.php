<?php

namespace NinjaForms;

if( ! defined( 'NF_SERVER_URL' ) )
  define( 'NF_SERVER_URL', 'https://my.ninjaforms.com' );

// Setup OAuth as a prerequisite for services.
include_once plugin_dir_path( __FILE__ ) . 'oauth.php';
OAuth::set_base_url( NF_SERVER_URL . '/oauth' );
OAuth::getInstance()->setup();

add_action( 'wp_ajax_nf_services', function(){
  $services = apply_filters( 'ninja_forms_services', [
    'ninja-forms-addon-manager' => [
      'name' => esc_html__( 'Add-on Manager (Beta)', 'ninja-mail' ),
      'slug' => 'ninja-forms-addon-manager',
      'installPath' => 'ninja-forms-addon-manager/ninja-forms-addon-manager.php',
      'description' => 'Install any purchased Ninja Forms add-ons with a single click. No need to download a file or copy/paste a license key! <strong>* Won\'t work on a local dev environment.</strong>',
      'enabled' => null,
      'learnMore' => '
      <div style="padding:20px;">
        <p>Here at Ninja Forms, we love add-ons. Add-ons let us create awesome products that serve very specific users with integrations, workflows, and power features, while keeping those options away from users that don’t need them. It also lets our users pay for what they need, rather than a bloated, one-size-fits-all solution.</p>
        <p>Despite all the great things about add-ons, there’s a glaring downside: installing 40+ add-ons and setting up licensing for all of those add-ons is a pain. We’re trying to change all of that with the Ninja Forms Add-on Manager, and we want your help putting it through its paces!</p>
        <p>The Add-on Manager makes installing Ninja Forms Add-ons a snap. Once you connect your site to my.ninjaforms.com using the Setup button, you can install add-ons and setup their licenses with a single click!</p>
        <p>
          How it works:
          <ol>
            <li>Connect to my.ninjaforms.com using the Try the Add-on Manager Beta button.</li>
            <li>Click the “Install Plugins” button.</li>
            <li>Click “Install” for the plugins you want to install.</li>
            <li>Make awesome stuff using Ninja Forms!</li>
          </ol>
        </p>
        <p>The add-on manager is free to use for anyone that has a Ninja Forms add-on purchase. At the moment, it’s in a Beta state as we work out some bugs. We’d love your feedback. Please, try it out today!</p>
        <br />
        <button style="display:block;width:100%;text-align:center;" class="nf-button primary" onclick="Backbone.Radio.channel( \'dashboard\' ).request( \'install:service\', \'ninja-forms-addon-manager\' );var spinner = document.createElement(\'span\'); spinner.classList.add(\'dashicons\', \'dashicons-update\', \'dashicons-update-spin\'); this.innerHTML = spinner.outerHTML; console.log( spinner )">Try the Add-on Manager (Beta)</button>
      </div>
      ',
    ],
  ] );
  wp_die( json_encode( [ 'data' => array_values( $services ) ] ) );
});

add_action( 'admin_enqueue_scripts', function() {
  wp_localize_script( 'nf-dashboard', 'nfPromotions', 'YOLO' );
});

add_action( 'wp_ajax_nf_services_install', function() {

  // register_shutdown_function(function(){
  //   if( ! error_get_last() ) return;
  //   echo '<pre>';
  //   print_r( error_get_last() );
  //   echo '</pre>';
  // });

  if ( ! current_user_can('install_plugins') )
    die( json_encode( [ 'error' => esc_html__( 'Sorry, you are not allowed to install plugins on this site.' ) ] ) );

  $plugin = WPN_Helper::sanitize_text_field($_REQUEST['plugin']);
  $install_path = WPN_Helper::sanitize_text_field($_REQUEST['install_path']);

  include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); //for plugins_api..
  $api = plugins_api( 'plugin_information', array(
    'slug' => $plugin,
    'fields' => array(
      'short_description' => false,
      'sections' => false,
      'requires' => false,
      'rating' => false,
      'ratings' => false,
      'downloaded' => false,
      'last_updated' => false,
      'added' => false,
      'tags' => false,
      'compatibility' => false,
      'homepage' => false,
      'donate_link' => false,
    ),
  ) );

  if ( is_wp_error( $api ) ) {
    die( json_encode( [ 'error' => $api->get_error_message() ] ) );
  }

  $plugins = get_plugins();
  if( ! isset( $plugins[ $install_path ] ) ){
    if ( ! class_exists( 'Plugin_Upgrader' ) ) {
      include_once ABSPATH . 'wp-admin/includes/file.php';
      include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    include_once plugin_dir_path( __FILE__ ) . 'remote-installer-skin.php';
    ob_start();
    $upgrader = new \Plugin_Upgrader( new Remote_Installer_Skin() );
    $install = $upgrader->install( $api->download_link );
    ob_clean();

    if( ! $install ){
      die( json_encode( [ 'error' => $upgrader->skin->get_errors() ] ) );
    }
  }

  if( ! is_plugin_active($plugin) ){
    ob_start();
    $activated = activate_plugin( $install_path );
    ob_clean();
    if( is_wp_error( $activated ) ){
      die( json_encode( [ 'error' => $activated->get_error_message() ] ) );
    }
  }

  $response = apply_filters( 'nf_services_installed_' . $plugin, '1' );

  echo json_encode( $response );
  die( '1' );
});

/**
 * Override the Ninja Mail download link until published in the repository.
 */
/*
add_filter( 'plugins_api_result', function( $response, $action, $args ){
  if( 'plugin_information' !== $action ) return $response;
  if( 'ninja-mail' !== $args->slug ) return $response;

  $response = new \stdClass();
  $response->download_link = 'http://my.ninjaforms.com/wp-content/uploads/ninja-mail-792d39446223d14b8464e214773e7786627855d8.zip';

  return $response;
}, 10, 3 );
*/
/**
 * Override the Add-on Manager download link until published in the repository.
 */
/*
add_filter( 'plugins_api_result', function( $response, $action, $args ){
  if( 'plugin_information' !== $action ) return $response;
  if( 'ninja-forms-addon-manager' !== $args->slug ) return $response;

  $response = new \stdClass();
  $response->download_link = 'http://my.ninjaforms.com/wp-content/uploads/ninja-forms-addon-manager-4b6a3f724b27d6d9f7d4e89ebe12dad215ec1b20.zip';

  return $response;
}, 10, 3 );

add_filter( 'http_request_args', function( $args, $url ){
  if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    $args['sslverify'] = false; // Local development
    $args['reject_unsafe_urls'] = false;
  }
  return $args;
}, 10, 2 );
*/
add_action( 'wp_ajax_nf_update_cache_mode', function() {
  $use_cache = false;
  $response = array();

  check_ajax_referer( 'ninja_forms_dashboard_nonce', 'security' );

  if( ! current_user_can('manage_options') ) {
    $response[ 'errors' ] = array( "Current user doesn't have permission." );

    echo json_encode( $response );
    die();
  }

  

  if(!isset( $_POST[ 'cache_mode' ] ) ) {
    $response[ 'errors' ] = array( 'No cache mode value given' );

    echo json_encode( $response );
    die();
  }

  $use_cache = ( intval($_POST[ 'cache_mode' ]) === 1 ) ? true : false;

  update_option( 'ninja_forms_cache_mode', $use_cache );

  $response['message'] = 'Cache mode successfully saved';

  echo json_encode($response);
  die();
});
