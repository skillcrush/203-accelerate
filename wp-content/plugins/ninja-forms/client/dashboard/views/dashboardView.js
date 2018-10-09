/**
 * Dashboard Layout View
 *
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [ 'views/sections/widgets.js', 'views/sections/services.js', 'views/sections/apps.js', 'views/sections/memberships.js', 'views/oauth.js', 'views/promotion.js' ], function( WidgetView, ServicesView, AppsView, MembershipsView, OAuthView, PromotionView ) {
    var view = Marionette.View.extend( {
        template: "#tmpl-nf-dashboard",

        currentView: 'widgets',

        regions: {
            notices: '.notices',
            promotions: '.promotions',
            content: '.content'
        },

        events: {
            'click .widgets a': function(e){
                this.showChildView( 'content', new WidgetView() );
                jQuery( '.' + this.currentView).find( 'a' ).removeClass( 'active' );
                e.target.classList.add( 'active' );
                this.currentView = 'widgets';
            },
            'click .services a': function(e){
                this.showChildView( 'content', new ServicesView() );
                jQuery( '.' + this.currentView).find( 'a' ).removeClass( 'active' );
                e.target.classList.add( 'active' );
                this.currentView = 'services';
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
                case '#services':
                    this.currentView = 'services';
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
            nfRadio.channel( 'dashboard' ).reply( 'show:services', function(){
                this.showChildView('content', new ServicesView() );
                jQuery( 'nav.sections a.active' ).removeClass( 'active' );
                jQuery( 'nav.sections .services a' ).addClass( 'active' );
                this.currentView = 'services';
            }, this );
            nfRadio.channel( 'dashboard' ).reply( 'show:apps', function(){
                this.showChildView('content', new AppsView() );
                jQuery( 'nav.sections a.active' ).removeClass( 'active' );
                jQuery( 'nav.sections .apps a' ).addClass( 'active' );
                this.currentView = 'apps';
            }, this );
        },

        onRender: function() {

          if( useServices ) this.showChildView( 'notices', new OAuthView() );
          if( useServices ) this.showChildView( 'promotions', new PromotionView() );

            switch( window.location.hash ) {
                case '#apps':
                    var childView = new AppsView();
                    break;
                case '#memberships':
                    var childView = new MembershipsView();
                    break;
                case '#services':
                    var childView = new ServicesView();
                    break;
                case '#widgets':
                default:
                    var childView = new WidgetView();
            }
            this.showChildView('content', childView );
            // If the user has not seen the opt-in modal yet...
            if ( '1' == nfAdmin.showOptin ) {
                // Declare all of our opt-in code here.
                var optinModal = new jBox( 'Modal', {
                    closeOnEsc:     false,
                    closeOnClick:   false,
                    width:          400
                } );
                // Define the modal title.
                var title = document.createElement( 'div' );
                title.id = 'optin-modal-title';
                var titleStyling = document.createElement( 'h2' );
                titleStyling.innerHTML = 'Help make Ninja Forms better!';
                title.appendChild( titleStyling );
                // Define the modal content.
                var content = document.createElement( 'div' );
                content.classList.add( 'message' );
                content.style.padding = '0px 20px 20px 20px';
                content.innerHTML = nfi18n.optinContent;
                var p = document.createElement( 'p' );
                p.style.paddingBottom = '10px';
                var checkBox = document.createElement( 'input' );
                checkBox.id = 'optin-send-email';
                checkBox.setAttribute( 'type', 'checkbox' );
                checkBox.style.margin = '7px';
                var label = document.createElement( 'label' );
                label.setAttribute( 'for', 'optin-send-email' );
                label.innerHTML = nfi18n.optinYesplease;
                p.appendChild( checkBox );
                p.appendChild( label );
                content.appendChild( p );
                p = document.createElement( 'p' );
                p.id = 'optin-block';
                p.style.padding = '0px 5px 20px 5px';
                p.style.display = 'none';
                var email = document.createElement( 'input' );
                email.id = 'optin-email-address';
                email.setAttribute( 'type', 'text' );
                email.setAttribute( 'value', nfAdmin.currentUserEmail );
                email.style.width = '100%';
                email.style.fontSize = '16px';
                p.appendChild( email );
                content.appendChild( p );
                var spinner = document.createElement( 'span' );
                spinner.id = 'optin-spinner';
                spinner.classList.add( 'spinner' );
                spinner.style.display = 'none';
                content.appendChild( spinner );
                var actions = document.createElement( 'div' );
                actions.id = 'optin-buttons';
                actions.classList.add( 'buttons' );
                var cancel = document.createElement( 'div' );
                cancel.id = 'optout';
                cancel.classList.add( 'nf-button', 'secondary' );
                cancel.innerHTML = nfi18n.optinSecondary;
                actions.appendChild( cancel );
                var confirm = document.createElement( 'div' );
                confirm.id = 'optin';
                confirm.classList.add( 'nf-button', 'primary', 'pull-right' );
                confirm.innerHTML = nfi18n.optinPrimary;
                actions.appendChild( confirm );
                content.appendChild( actions );
                // Define the success title.
                var successTitle = document.createElement( 'h2' );
                successTitle.innerHTML = nfi18n.optinAwesome;
                // Define the success content.
                var successContent = document.createElement( 'div' );
                successContent.id = 'optin-thankyou';
                successContent.classList.add( 'message' );
                successContent.style.padding = '20px';
                successContent.innerHTML = nfi18n.optinThanks;
                // Set the options for the modal and open it.
                optinModal.setContent( document.createElement( 'div' ).appendChild( content ).innerHTML );
                optinModal.setTitle( document.createElement( 'div' ).appendChild( title ).innerHTML );
                optinModal.open();
                // Show/Hide email field, based on the opt-in checkbox.
                jQuery( '#optin-send-email' ).click( function( e ) {
                    if( jQuery( this ).is( ':checked' ) ) {
                        jQuery( '#optin-block' ).show();
                    } else {
                        jQuery( '#optin-block' ).hide();
                    }
                } );
                // Setup the optin click event.
                jQuery( '#optin' ).click( function( e ) {
                    var sendEmail;

                    if ( jQuery( '#optin-send-email' ).attr( 'checked' ) ) {
                        sendEmail = 1;
                        userEmail = jQuery( '#optin-email-address' ).val();
                    } else {
                        sendEmail = 0;
                        userEmail = '';
                    }
                    // Disable our buttons.
                    jQuery( '#optin' ).unbind( 'click' );
                    jQuery( '#optout' ).unbind( 'click' );
                    // Get a reference to the current width (to avoid resizing the button).
                    var width = jQuery( '#optin' ).width();
                    // Show spinner.
                    jQuery( '#optin' ).html( '<span class="dashicons dashicons-update dashicons-update-spin"></span>' );
                    jQuery( '#optin' ).width( width );
                    // Hit AJAX endpoint and opt-in.
                    jQuery.post( ajaxurl, { action: 'nf_optin', ninja_forms_opt_in: 1, send_email: sendEmail, user_email: userEmail },
                                function( response ) {
                        /**
                         * When we get a response from our endpoint, show a thank you and set a timeout
                         * to close the modal.
                         */
                        optinModal.setTitle( document.createElement( 'div' ).appendChild( successTitle ).innerHTML );
                        optinModal.setContent( document.createElement( 'div' ).appendChild( successContent ).innerHTML );
                        setTimeout (
                            function(){
                                optinModal.close();
                            },
                            2000
                        );
                    } );            
                } );
                // Setup the optout click event.
                jQuery( '#optout' ).click( function( e ) {
                    // Disable our buttons.
                    jQuery( '#optin' ).unbind( 'click' );
                    jQuery( '#optout' ).unbind( 'click' );
                    // Get a reference to the current width (to avoid resizing the button).
                    var width = jQuery( '#optout' ).width();
                    // Show spinner.
                    jQuery( '#optout' ).html( '<span class="dashicons dashicons-update dashicons-update-spin"></span>' );
                    jQuery( '#optout' ).width( width );
                    // Hit AJAX endpoint and opt-in.
                     jQuery.post( ajaxurl, { action: 'nf_optin', ninja_forms_opt_in: 0 }, function( response ) {
                        // When we get a response from our endpoint, close the modal. 
                        optinModal.close();
                    } );            
                } );
            } // If we've been told to run cleanup...
            else if ( '1' == nfAdmin.doingCleanup ) {
                // Get the context for later.
                var that = this;
                // Define our modal options.
                var modalData = {
                    width: 450,
                    closeOnClick: false,
                    closeOnEsc: false,
                    content: nfi18n.cleanupContent,
                    useProgressBar: true,
                    loadingText: nfi18n.cleanupLoading,
                    btnSecondary: {
                        text: nfi18n.cleanupSecondary,
                        callback: function() {
                            cleanupModal.toggleModal( false );
                        }
                    },
                    btnPrimary: {
                        text: nfi18n.cleanupPrimary,
                        callback: function() {
                            // Prevent the user from leaving without firing an alert.
                            jQuery( window ).bind( 'beforeunload', function() { 
                                return 'Are you sure? Leaving before the process completes could cause damage to your data.';
                            } );
                            // Hide the buttons.
                            cleanupModal.maybeShowActions( false );
                            // Show the progress bar.
                            cleanupModal.maybeShowProgress( true );
                            // Begin our cleanup process.
                            that.cleanupProcess( that, -1, cleanupModal );
                        },
                    },
                };
                // Setup our modal.
                var cleanupModal = new NinjaModal( modalData );
            }
            // If form telemetry is defined...
            // AND if we should run it...
            if ( 'undefined' !== typeof nfAdmin.formTelemetry && 1 == nfAdmin.formTelemetry ) {
                // Make our AJAX call.
                var data = {
                    action: 'nf_form_telemetry',
                    security: nfAdmin.ajaxNonce
                }
                // Make our AJAX call.
                jQuery.post( ajaxurl, data );
            }
        },

        templateContext: function() {
            var that = this;
            return {
                renderNav: function() {
                    var content = document.createElement( 'div' );
                    _.each( nfDashItems, function(section) {
                        var item = document.createElement( 'li' );
                        var link = document.createElement( 'a' );
                        link.href = '#' + section.slug;
                        if ( that.currentView == section.slug ) link.classList.add( 'active' );
                        link.innerHTML = section.niceName;
                        item.classList.add( section.slug );
                        item.appendChild( link );
                        content.appendChild( item );
                    } );
                    return content.innerHTML;
                },
            }
        },
        
        /**
         * Function to manage our data cleanup batch process response.
         * 
         * @since 3.3.1
         * 
         * @param context (this) The context at the time of function definition.
         * @param steps (int) The total number of steps in this process.
         * @param modal (jBox) A reference to the modal where this process is running.
         */
        cleanupProcess: function( context, steps, modal ) {
            var data = {
                action: 'nf_batch_process',
                batch_type: 'data_cleanup',
                security: nfAdmin.batchNonce
            };
            jQuery.post( ajaxurl, data, function( response ) {
                response = JSON.parse( response );
                // If we're done...
                if ( response.batch_complete ) {
                    // Push our progress bar to 100%.
                    modal.setProgress( 100 );
                    // Allow the user to leave the page now.
                    jQuery( window ).unbind( 'beforeunload' );
                    modal.toggleModal( false );
                    // Exit.
                    return false;
                }
                // If we do not yet have a determined number of steps...
                if ( -1 == steps ) {
                    // If step_toal is defined...
                    if ( 'undefined' != typeof response.step_total ) {
                        // Use the step_total.
                        steps = response.step_total;
                    } // Otherwise... (step_total is not defined)
                    else {
                        // Use step_remaining.
                        steps = response.step_remaining;
                    }
                }
                // Calculate our current step.
                var step = steps - response.step_remaining;
                // Calculate our maximum progress for this step.
                var maxProgress = Math.round( step / steps * 100 );
                // Increment the progress.
                modal.incrementProgress ( maxProgress );
                // Recall our function...
                context.cleanupProcess( context, steps, modal );
            } );
        }
    } );
    return view;
} );
