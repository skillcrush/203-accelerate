define( [ 'models/oauthModel' ], function( OAuthModel ) {
    var view = Marionette.View.extend( {

      model: new OAuthModel(),

      template: '#tmpl-nf-notices-oauth',

      className: 'nf-notices--oauth',

      ui: {
          disconnect: '.js--disconnect',
      },

      /*
       * Update when the OAuth controller is synced.
       */
      initialize: function( oauthModel ) {
        this.listenTo( nfRadio.channel( 'dashboard' ), 'fetch:oauth', this.updateModel );
      },

      updateModel: function() {
        this.model = nfRadio.channel( 'dashboard' ).request( 'get:oauth' );
        this.render();
      },

      events: {
          'click @ui.disconnect': function() {
            nfRadio.channel( 'dashboard' ).request( 'disconnect:oauth' );
          },
      }

    } );
    return view;
} );
