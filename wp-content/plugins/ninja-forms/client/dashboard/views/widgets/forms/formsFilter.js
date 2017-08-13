/**
 * Forms Widget Filter View
 *
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [], function() {
    var view = Marionette.View.extend( {
        template: "#tmpl-nf-widget-forms-filter",

        ui: {
            input: 'input',
        },

        events: {
            'keyup @ui.input': 'updateFilter'
        },

        initialize: function(){
            this.listenTo( nfRadio.channel( 'widget-forms' ), 'change:content', this.clearFilter );
        },

        updateFilter: function(){
            var term = this.getUI( 'input' ).val();
            nfRadio.channel( 'widget-forms' ).trigger( 'update:filter', term );
        },

        clearFilter: function(){
            this.getUI( 'input' ).val( '' );
        },

        updatePlaceholder: function( placeholder ){
            this.getUI( 'input' ).attr( 'placeholder', placeholder );
        }
    } );
    return view;
} );
