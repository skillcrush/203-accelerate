var nfRadio = Backbone.Radio;
var nfDebug = false;

if( ! useServices ){
  /**
   * If the feature flag isn't set then filter out the "Services" tab.
   */
  nfDashItems = nfDashItems.filter(function(item){
    return 'services' !== item.slug;
  });
}

jQuery( document ).ready( function( $ ) {
    require( [ 'controllers/formsController', 'controllers/oauthController', 'controllers/servicesController', 'views/dashboardView' ], function( FormsController, OAuthController, ServicesController, DashboardView ) {

        var NinjaFormsDashboard = Marionette.Application.extend( {

            // The root element for the application.
            region: '#ninja-forms-dashboard',

            controllers: {},

            initialize: function( options ) {

                // Assign this instance to a named variable.
                var dashboard = this;

                // Override the Renderer for WordPress compatibility.
                Marionette.Renderer.render = function(template, data){
                    var template = dashboard.template( template );
                    return template( data );
                };

                /* DEBUG */ if( nfDebug ) console.log( 'Dashboard Initialized' );
            },

            onStart: function() {

                // Show the root view for the application.
                this.showView( new DashboardView() );

                this.controllers.forms = new FormsController();
                if( useServices ) this.controllers.oauth = new OAuthController();
                if( useServices ) this.controllers.services = new ServicesController();

                //var data = {id: 1, title: 'Contact Me', created: '10-23-2016'};
                //var form = new FormModel(data);
//                var formCollection = new FormCollection();
//                formCollection.fetch( {
//                    success: function(response) { console.log(response); }
//                });

                /* DEBUG */ if( nfDebug ) console.log( 'Dashboard Started' );
            },

            /**
             * Custom render function for WordPress style templates.
             * @param template
             */
            template: function( template ) {
                return _.template( $( template ).html(),  {
                    evaluate:    /<#([\s\S]+?)#>/g,
                    interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                    escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
                    variable:    'data'
                } );
            }
        } );

        var nfDashboard = new NinjaFormsDashboard();
        nfDashboard.start();
    } );
} );



/**
 * Submenu Routing
 */

jQuery( 'a[href="admin.php?page=ninja-forms#new-form"]' ).on( 'click', function( event ){
    event.preventDefault();
    // We won't allow access to add forms if there are required updates
    if( "1" !== nfAdmin.requiredUpdates ) {
        window.location.hash = 'new-form';
        nfRadio.channel( 'dashboard' ).request( 'show:widgets' );
        nfRadio.channel( 'widget-forms' ).request( 'show:newFormsGrid' );
    }
} );

jQuery( 'a[href="admin.php?page=ninja-forms#apps"]' ).on( 'click', function( event ){
    event.preventDefault();
    // We won't allow access to the apps tab if there are required updates
    if( "1" !== nfAdmin.requiredUpdates ) {
        window.location.hash = 'apps';
        nfRadio.channel( 'dashboard' ).request( 'show:apps' );
    }

} );

jQuery( 'a[href="admin.php?page=ninja-forms"]' ).on( 'click', function( event ){
    event.preventDefault();
    // We won't allow access to the dashboard if there are required updates
    if( "1" !== nfAdmin.requiredUpdates ) {
        window.location.hash = 'forms';
        nfRadio.channel( 'dashboard' ).request( 'show:widgets' );
        nfRadio.channel( 'widget-forms' ).request( 'show:formsTable' );
    }
} );

/**
 * Hash Change Routing Fallback
 * To avoid the need to manually add listeners to views, this is a generic hash change listener.
 */
jQuery(window).on('hashchange', function() {
  var hash = window.location.hash.substr(1);
  nfRadio.channel( 'dashboard' ).request( 'show:' + hash );
});
