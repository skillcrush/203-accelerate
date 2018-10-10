<?php

return apply_filters( 'ninja-forms-dashboard-promotions', array(

  /*
  |--------------------------------------------------------------------------
  | Ninja Mail
  |--------------------------------------------------------------------------
  |
  */

  'ninja-mail' => array(
    'id' => 'ninja-mail',
    'content' => '<a href="#services"><span class="dashicons dashicons-email-alt"></span>' . __( 'Hosts are bad at sending emails. Improve the reliability of your submission emails! ', 'ninja-forms' ) . '<br /><span class="cta">' . __( 'Try our new Ninja Mail service!', 'ninja-forms' ) . '</span></a>',
    'script' => "
      setTimeout(function(){ /* Wait for services to init. */
        Backbone.Radio.channel( 'dashboard' ).request( 'more:service:ninja-mail' );
      }, 500);
    "
  ),


));
