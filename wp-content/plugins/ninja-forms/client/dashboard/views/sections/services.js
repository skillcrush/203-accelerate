/**
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [ 'views/services/services' ], function( ServicesView ) {
    var view = Marionette.View.extend( {
        template: '#tmpl-nf-services',

        regions: {
            services: '.services'
        },

        onRender: function() {
            this.showChildView( 'services', new ServicesView() );
        }
    } );
    return view;
} );
