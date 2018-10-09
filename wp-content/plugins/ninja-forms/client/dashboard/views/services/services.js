define( [ 'views/services/service', 'models/serviceCollection' ], function( ServiceView, ServiceCollection ) {
    var view = Marionette.CollectionView.extend( {

      collection: new ServiceCollection(),

      className: 'wrap apps-container', /* Reusing "App" section styles. */

      childView: ServiceView,

      initialize: function() {
        this.updateCollection();
        this.listenTo( nfRadio.channel( 'dashboard' ), 'fetch:services', this.updateCollection );
      },

      updateCollection: function() {
        this.collection = nfRadio.channel( 'dashboard' ).request( 'get:services' );
        this.render();
      }

    } );
    return view;
} );
