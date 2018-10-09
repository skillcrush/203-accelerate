define( [], function() {
  var model = Backbone.Model.extend( {
    defaults: {
      connected: null,
      connect_url: '',
    },

    url: function() {
      return ajaxurl + "?action=nf_oauth";
    },

    initialize: function() {
      /* ... */
    },

    /*
     * Use the `data` property of the response.
     */
    parse: function( response, options ){
        return response.data;
    }

  } );

  return model;
} );
