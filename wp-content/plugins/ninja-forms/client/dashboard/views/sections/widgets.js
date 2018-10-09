/**
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [ 'views/widgets/forms/forms' ], function( WidgetFormsView ) {
    var view = Marionette.View.extend( {
        template: '#tmpl-nf-widgets',

        regions: {
            forms: '.widget-forms'
        },

        onRender: function() {
            this.showChildView( 'forms', new WidgetFormsView() );
        }
    } );
    return view;
} );
