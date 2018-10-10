/**
 * Model that represents our form template.
 * 
 * @package Ninja Forms client
 * @copyright (c) 2017 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var model = Backbone.Model.extend( {
		defaults: {
            objectType: 'template',
            id: 'none',
            title: 'unknown',
            type: ''
		},

		initialize: function() {
            this.set( 'desc', this.get( 'template-desc' ) );
            this.set( 'modal-content', this.get( 'modal-content' ) );
            this.set( 'modal-title', this.get( 'modal-title' ) );
        }
        
	} );
	
	return model;
} );