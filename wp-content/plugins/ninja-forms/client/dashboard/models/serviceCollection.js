define( ['models/serviceModel'], function( ServiceModel ) {
	var collection = Backbone.Collection.extend( {
		model: ServiceModel,
		comparator: 'name',

    url: function() {
        return ajaxurl + "?action=nf_services";
    },

		initialize: function() {
      /* ... */
		},

		/*
     * Use the `data` property of the response.
     */
    parse: function( response, options ){
        return response.data;
    },

	} );

	return collection;
} );
