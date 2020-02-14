<?php

return apply_filters( 'ninja-forms-dashboard-promotions', array(


  /*
  |--------------------------------------------------------------------------
  | Ninja Shop
  |--------------------------------------------------------------------------
  |
  */

  'ninja-shop'  => array(
    'id'        => 'ninja-shop',
    'location'  => 'dashboard',
    'type'      => 'ninja-shop',
    'content'   => '<a href="https://getninjashop.com/?utm_source=ninja-forms-plugin&utm_medium=dashboard-banner-ad&utm_campaign=ninja-shop-banner-ad" target="_blank" style="color:#FFF !important;background:#5DA54B;"><span class="dashicons dashicons-cart"></span>' . esc_html__( 'Are you frustrated with complicated eCommerce solutions?', 'ninja-forms' ) . '<br /><span class="cta">' . esc_html__( 'Start Selling Today With Ninja Shop!', 'ninja-forms' ) . '</span></a>',
    'script'    => "",
  ),


  /*
  |--------------------------------------------------------------------------
  | Personal 20
  |--------------------------------------------------------------------------
  |
  */

  'personal-20' => array(
    'id'        => 'personal-20',
    'location'  => 'dashboard',
    'type'      => 'personal',
    'content'   => '<a href="https://ninjaforms.com/personal-membership/?utm_source=ninja-forms-plugin&utm_medium=dashboard-banner-ad&utm_campaign=personal-banner-ad&utm_content=personal-20" target="_blank" class="nf-remove-promo-styling"><img src="' . Ninja_forms::$url . 'assets/img/promotions/dashboard-banner-personal-20.png"></a>',
    'script'    => "",
  ),

  
  /*
  |--------------------------------------------------------------------------
  | Personal 50
  |--------------------------------------------------------------------------
  |
  */

  'personal-50' => array(
    'id'        => 'personal-50',
    'location'  => 'dashboard',
    'type'      => 'personal',
    'content'   => '<a href="https://ninjaforms.com/personal-membership/?utm_source=ninja-forms-plugin&utm_medium=dashboard-banner-ad&utm_campaign=personal-banner-ad&utm_content=personal-50" target="_blank" class="nf-remove-promo-styling"><img src="' . Ninja_forms::$url . 'assets/img/promotions/dashboard-banner-personal-50.png"></a>',
    'script'    => "",
  ),

  /*
  |--------------------------------------------------------------------------
  | Send WP
  |--------------------------------------------------------------------------
  |
  */
  
  'sendwp-banner' => array(
    'id'      => 'sendwp-banner',
    'location'  => 'dashboard',
    'content' => '<span aria-label="SendWP. Getting WordPress email into an inbox shouldn\'t be that hard! Never miss another receipt, form submission, or any WordPress email ever again." style="cursor:pointer;width:800px;height:83px;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;background-image:url(\'' . NF_PLUGIN_URL . 'assets/img/promotions/dashboard-banner-sendwp.png\');display:block;"></span>',
    'type'    => 'sendwp',
    'script'  => "
      setTimeout(function(){ /* Wait for services to init. */
        var data = {
          width: 450,
          closeOnClick: 'body',
          closeOnEsc: true,
          content: '<p><h2>Frustrated that WordPress email isn’t being received?</h2><p>Form submission notifications not hitting your inbox? Some of your visitors getting form feedback via email, others not? By default, your WordPress site sends emails through your web host, which can be unreliable. Your host has spent lots of time and money optimizing to serve your pages, not send your emails.</p><h3>Sign up for SendWP today, and never deal with WordPress email issues again!</h3><p>SendWP is an email service that removes your web host from the email equation.</p><ul style=&quot;list-style-type:initial;margin-left: 20px;&quot;><li>Sends email through dedicated email service, increasing email deliverability.</li><li>Keeps form submission emails out of spam by using a trusted email provider.</li><li>On a shared web host? Don’t worry about emails being rejected because of blocked IP addresses.</li><li><strong>$1 for the first month. $9/month after. Cancel anytime!</strong></li></ul></p><br />',
          btnPrimary: {
            text: 'Sign me up!',
            callback: function() {
              var spinner = document.createElement('span');
              spinner.classList.add('dashicons', 'dashicons-update', 'dashicons-update-spin');
              var w = this.offsetWidth;
              this.innerHTML = spinner.outerHTML;
              this.style.width = w+'px';
              ninja_forms_sendwp_remote_install();
            }
          },
          btnSecondary: {
            text: 'Cancel',
            callback: function() {
              sendwpModal.toggleModal(false);
            }
          }
        }
        var sendwpModal = new NinjaModal(data);
      }, 500);
    "
  ),


));
