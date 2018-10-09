define( [ 'models/promotionModel' ], function( PromotionModel ) {
    var view = Marionette.View.extend( {

      model: null,

      template: '#tmpl-nf-promotion',

      className: 'nf-promotion',

      /*
       * Display a single promotion - chosen at random.
       */
      initialize: function() {
        var promotion = nfPromotions[Math.floor(Math.random()*nfPromotions.length)];
        this.model = new PromotionModel( promotion );
        // this.listenTo( nfRadio.channel( 'dashboard' ), 'did:something', this.something );
      }

    } );
    return view;
} );
