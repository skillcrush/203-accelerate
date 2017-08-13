/**
 * Collection that holds our form models.
 *
 * @package Ninja Forms client
 * @copyright (c) 2017 WP Ninjas
 * @since 3.0
 */
define( ['models/formTemplateModel'], function( TemplateModel ) {
	var collection = Backbone.Collection.extend( {
		model: TemplateModel,
		tmpNum: 1,
        url: function() {
            return ajaxurl + "?action=nf_new_form_templates";
        },

        parse: function( response, options ){
            return response.data;
        },

		initialize: function() {
            this.fetch( {
                success: function(response) {
                    // This section intentionally left blank.
                },
                error: function(response) {
                    // This section intentionally left blank.
                },
            });
		}
        
	} );
	return collection;
} );