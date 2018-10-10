define( [], function() {
  var model = Backbone.Model.extend( {
    defaults: {
      objectType: 'service',
      name: '',
      slug: '',
      installPath: '',
      description: '',
      enabled: null,
      infoLink: null,
      serviceLink: null,
      is_installing: false,
      classes: ''
    },

    url: function() {
      return ajaxurl + "?action=nf_service_" + this.get( 'slug' );
    },

    /*
     * - Check for "Success"/Setup modal.
     * - (Maybe) Auto-redirect to the service.
     */
    initialize: function() {

      // Check for successful setup.
      if( this.get( 'slug' ) == serviceSuccess && this.get( 'successMessage' ) ){
        new jBox( 'Modal', {
            width: 300,
            addClass: 'dashboard-modal',
            overlay: true,
            closeOnClick: true,
            content: this.get( 'successMessage' ),
            title: this.get( 'successMessageTitle' ),
            closeButton: 'box'
        } ).open();
      }

      // Auto-redirect to the serviceLink on install.
      var that = this;
      nfRadio.channel( 'dashboard' ).reply( 'install:service:' + this.get( 'slug' ), function(){

        // If no service link url, then no need to auto-redirect.
        if( ! that.get( 'serviceLink' ) ) return;
        if( ! that.get( 'serviceLink' ).href ) return;

        var redirect = that.get( 'serviceLink' ).href;

        // Display a redirect notice.
        new jBox( 'Modal', {
                        width: 300,
                        addClass: 'dashboard-modal',
                        overlay: true,
                        closeOnClick: 'body',
                        content: nfi18n.serviceRedirect,
                    } ).open();

        // Trigger a redirect, where depends on the connected status.
        var oauth = nfRadio.channel( 'dashboard' ).request( 'get:oauth' );
        if( ! oauth.get( 'connected' ) ){
          if( that.get( 'connect_url' ) ){
            return window.location = that.get( 'connect_url' ) + '&redirect=' + redirect;
          }
          window.location = oauth.get( 'connect_url' ) + '&redirect=' + redirect;
        } else {
          window.location = redirect;
        }
      } );
    },

    /*
     * Sync the server with the model.
     */
    save: function() {
      var that = this;
      jQuery.ajax({
          type: "POST",
          url: this.url(),
          data: this.toJSON()
      }).done( function( response ){
        var data = JSON.parse( response );
        if( 'undefined' !== typeof data.error ) {
          alert( nfi18n.serviceUpdateError + ' ' + data.error );
          that.set( 'enabled', ! that.get( 'enabled' ) );
        }
        nfRadio.channel( 'dashboard').trigger( 'save:service-' + that.get( 'slug' )  );
      });
    }

  } );

  return model;
} );
