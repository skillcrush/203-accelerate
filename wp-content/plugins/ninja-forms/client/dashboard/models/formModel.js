/**
 * Model that represents our form.
 * 
 * @package Ninja Forms client
 * @copyright (c) 2017 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var model = Backbone.Model.extend( {
		defaults: {
            objectType: 'form',
            id: 0,
            title: 'unknown',
            created_at: 'unknown'
		},

        url: function() {
            return ajaxurl + "?action=nf_forms&form_id=" + this.get( 'id' );
        },

		initialize: function() {

            this.set( 'id', Number( this.get( 'id' ) ) );
            
            if( this.get( 'id' ) ) {
                this.initShortcode( this.get( 'id' ) );
            }

            // Strip HTML tags from the form title.
            if ( this.get( 'title' ) ) {
               this.set( 'title', this.get( 'title' ).replace(/<\/?[^>]+(>|$)/g, "") ); 
            }
        },
        
        initShortcode: function( id ) {
            var shortcode = '[ninja_form id=' + id + ']';
            this.set( 'shortcode', shortcode);
        },
        
        /* Overwrite the standard backbone delete method
         * allowing us to send a POST request instead of DELETE
         */
        destroy: function() {
            var that = this;
            jQuery.ajax({
                type: "POST",
                url: ajaxurl + '?action=nf_forms&method_override=delete&form_id=' + this.get( 'id' ),
                success: function( response ){
                    var response = JSON.parse( response );
                    that.collection.remove( that );
                }
            });
        }
        
	} );
	
	return model;
} );
