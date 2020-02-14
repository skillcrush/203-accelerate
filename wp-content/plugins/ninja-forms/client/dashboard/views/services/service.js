define( [], function() {
    var view = Marionette.View.extend( {

      template: '#tmpl-nf-service',

      className: function(){
        return 'nf-extend nf-box ' + this.model.get( 'classes' );
      },

      ui: {
          install: '.js--install',
          learnMore: '.js--learn-more',
          enabled: '.nf-toggle.setting',
          toggleEnable: '.nf-toggle + label',
      },

      events: {
          'click @ui.install': function() {
            nfRadio.channel( 'dashboard' ).request( 'install:service', this.model );
          },
          'click @ui.learnMore': function() {
            this.showLearnMore();
          },
          'click @ui.toggleEnable': function() {
              if( null == this.model.get( 'enabled' ) ){
                if( this.model.get( 'link' ) ){
                  window.location = this.model.get( 'link' );
                  return this.render();
                }
              }
              this.model.set( 'enabled', ! this.model.get( 'enabled' ) );
              this.model.save("enabled");
              this.render();
          },
      },

      initialize: function( oauthModel ) {
        this.updateOAuth();

        this.listenTo( this.model, 'change', this.render );

        // Show the "Learn More" modal.
        nfRadio.channel( 'dashboard' ).reply( 'more:service:' + this.model.get( 'slug' ), this.showLearnMore, this );

        // Update connected status when the OAuth model is synced with the server.
        this.listenTo( nfRadio.channel( 'dashboard' ), 'fetch:oauth', this.updateOAuth );

        // Re-render the view after syncing with the server.
        this.listenTo( nfRadio.channel( 'dashboard' ), 'save:service-' + this.model.get( 'slug' ), this.render );
      },

      /*
       * Open the modal with the "Learn More" content.
       */
      showLearnMore: function() {
        var that = this;

        var modal = new jBox( 'Modal', {
          width: 750,
          title: this.model.get( 'learnMoreTitle' ) || this.model.get( 'name' ),
          content: this.model.get( 'learnMore' ),
          closeButton: 'box',
          blockScroll: true
        } );
        modal.open();

        // Add a radio call for accessing the modal instance.
        nfRadio.channel( 'dashboard' ).reply( 'service:' + this.model.get( 'slug' ) + ':modal', function(){
          return modal;
        } );
      },

      /*
       * Update connected status when the OAuth model is synced with the server.
       */
      updateOAuth: function() {
        var oauth = nfRadio.channel( 'dashboard' ).request( 'get:oauth' );
        this.connected = oauth.get( 'connected' );
        this.render();
      },

      templateContext: function() {
        return {
          is_connected: this.connected,
        }
      }

    } );
    return view;
} );
