/*
 * Handles setting up our forms table.
 *
 * Holds a collection of our forms.
 * Replies to requests for form data.
 * Updates form models.
 */
define([ 'models/formModel', 'models/formCollection' ], function( FormModel, FormCollection ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.forms = new FormCollection();

            nfRadio.channel( 'dashboard' ).reply( 'get:forms', this.getForms, this );

			this.forms.fetch({
				success: function( collection ){
                    nfRadio.channel( 'dashboard' ).trigger( 'fetch:forms', collection );
				}
			});
		},

		getForms: function() {
			return this.forms;
		},
	});

	return controller;
} );