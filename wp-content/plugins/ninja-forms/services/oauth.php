<?php

namespace NinjaForms;

class OAuth
{
  protected static $instance;

  protected $base_url;

  protected $client_id,
            $client_secret;

  public static function getInstance() {
      if ( null == self::$instance ) {
          self::$instance = new static();
      }
      return self::$instance;
  }

  public static function set_base_url( $base_url ) {
    self::getInstance()->base_url = trailingslashit( $base_url );
  }

  protected function __construct() {

    $this->client_id = get_option( 'ninja_forms_oauth_client_id' );

    $this->client_secret = get_option( 'ninja_forms_oauth_client_secret' );
    if( ! $this->client_secret ){
      $this->client_secret = self::generate_secret();
      update_option( 'ninja_forms_oauth_client_secret', $this->client_secret );
    }
  }

  public function setup() {
    add_action( 'wp_ajax_nf_oauth', function(){
      wp_die( json_encode( [
        'data' => [
          'connected' => ( $this->client_id ),
          'connect_url' => self::connect_url(),
        ]
      ] ) );
    });

    // These Ajax calls handled in 'connect' and 'disconnect', respectively
    add_action( 'wp_ajax_nf_oauth_connect', [ $this, 'connect' ] );
    add_action( 'wp_ajax_nf_oauth_disconnect', [ $this, 'disconnect' ] );
  }

  public static function is_connected() {
    return ( self::getInstance()->client_id );
  }

  public static function get_client_id() {
    return self::getInstance()->client_id;
  }

  public static function get_client_secret() {
    return self::getInstance()->client_secret;
  }

  public static function connect_url( $endpoint = 'connect' ) {

    $client_redirect = add_query_arg( [
      'action' => 'nf_oauth_connect',
      'nonce'  => wp_create_nonce( 'nf-oauth-connect' )
    ], admin_url( 'admin-ajax.php' ) );

    return add_query_arg([
        'client_secret' => self::get_client_secret(),
        'client_redirect' => urlencode( $client_redirect ),
        'client_site_url' => urlencode( site_url() ),
    ], self::getInstance()->base_url . $endpoint );
  }

  public function connect() {
    // Does the current user have admin privileges
    if (!current_user_can('manage_options')) {
      return;
    }

    // wp_verify_nonce( $_REQUEST['nonce'], 'nf-oauth-connect' );

    if( ! isset( $_GET[ 'client_id' ] ) ) return;

    $client_id = sanitize_text_field( $_GET[ 'client_id' ] );
    update_option( 'ninja_forms_oauth_client_id', $client_id );

    if( isset( $_GET[ 'redirect' ] ) ){
      $redirect = sanitize_text_field( $_GET[ 'redirect' ] );
      $redirect = add_query_arg( 'client_id', $client_id, $redirect );
      wp_redirect( $redirect );
      exit;
    }

    wp_safe_redirect( admin_url( 'admin.php?page=ninja-forms#services' ) );
    exit;
  }

  public function disconnect() {

    // Does the current user have admin privileges
    if (!current_user_can('manage_options')) {
      return;
    }

    do_action( 'ninja_forms_oauth_disconnect' );

    $url = trailingslashit( $this->base_url ) . 'disconnect';
    $args = [
      'blocking' => false,
      'method' => 'DELETE',
      'body' => [
        'client_id' => get_option( 'ninja_forms_oauth_client_id' ),
        'client_secret' => get_option( 'ninja_forms_oauth_client_secret' )
      ]
    ];
    $response = wp_remote_request( $url, $args );

    delete_option( 'ninja_forms_oauth_client_id' );
    delete_option( 'ninja_forms_oauth_client_secret' );
    wp_die( 1 );
  }

  public static function generate_secret( $length = 40 ) {

    if( 0 >= $length ) $length = 40; // Min key length.
    if( 255 <= $length ) $length = 255; // Max key length.

    $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ( $i = 0; $i < $length; $i ++ ) {
        $random_string .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
    }

    return $random_string;
  }
}
