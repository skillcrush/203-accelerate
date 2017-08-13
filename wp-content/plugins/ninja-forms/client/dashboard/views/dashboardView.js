/**
 * Dashboard Layout View
 *
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [ 'views/sections/widgets.js', 'views/sections/apps.js', 'views/sections/memberships.js' ], function( WidgetView, AppsView, MembershipsView ) {
    var view = Marionette.View.extend( {
        template: "#tmpl-nf-dashboard",
        
        currentView: 'widgets',

        regions: {
            content: '.content'
        },

        events: {
            'click .widgets a': function(e){
                this.showChildView( 'content', new WidgetView() );
                jQuery( '.' + this.currentView).find( 'a' ).removeClass( 'active' );
                e.target.classList.add( 'active' );
                this.currentView = 'widgets';
            },
            'click .apps a': function(e){
                this.showChildView( 'content', new AppsView() );
                jQuery( '.' + this.currentView).find( 'a' ).removeClass( 'active' );
                e.target.classList.add( 'active' );
                this.currentView = 'apps';
            },
            'click .memberships a': function(e){
                this.showChildView( 'content', new MembershipsView() );
                jQuery( '.' + this.currentView).find( 'a' ).removeClass( 'active' );
                e.target.classList.add( 'active' );
                this.currentView = 'memberships';
            },
        },

        initialize: function() {
            switch( window.location.hash ) {
                case '#apps':
                    this.currentView = 'apps';
                    break;
                case '#memberships':
                    this.currentView = 'memberships';
                    break;
                case '#widgets':
                default:
                    this.currentView = 'widgets';
            }

            /**
             * Radio Routers
             * TODO: Clean this up.
             */
            nfRadio.channel( 'dashboard' ).reply( 'show:widgets', function(){
                this.showChildView('content', new WidgetView() );
                jQuery( 'nav.sections a.active' ).removeClass( 'active' );
                jQuery( 'nav.sections .widgets a' ).addClass( 'active' );
                this.currentView = 'widgets';
            }, this );
            nfRadio.channel( 'dashboard' ).reply( 'show:apps', function(){
                this.showChildView('content', new AppsView() );
                jQuery( 'nav.sections a.active' ).removeClass( 'active' );
                jQuery( 'nav.sections .apps a' ).addClass( 'active' );
                this.currentView = 'apps';
            }, this );
        },

        onRender: function() {
            switch( window.location.hash ) {
                case '#apps':
                    var childView = new AppsView();
                    break;
                case '#memberships':
                    var childView = new MembershipsView();
                    break;
                case '#widgets':
                default:
                    var childView = new WidgetView();
            }
            this.showChildView('content', childView );
        },
        
        templateContext: function() {
            var that = this;
            return {
                renderNav: function() {
                    var content = '';
                    _.each( nfDashItems, function(section) {
                        var classes = (that.currentView == section.slug ) ? ' active' : '';
                        content += '<li class="' + section.slug + '"><a href="#' + section.slug + '" class="' + classes + '">' + section.niceName + '</a></li>';
                    } );
                    return content;
                },
            }
        }
    } );
    return view;
} );
