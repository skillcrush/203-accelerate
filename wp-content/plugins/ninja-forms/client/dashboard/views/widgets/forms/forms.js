/**
 * Forms Widget View
 *
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [
    'views/widgets/forms/formsFilter',
    'views/widgets/forms/formsTable',
    'views/widgets/forms/newFormGrid'
], function( FilterView, FormsTableView, NewFormGridView ) {
    var view = Marionette.View.extend( {
        template: "#tmpl-nf-widget-forms",
        
        regions: {
            filter: '.filter',
            content: '.content'
        },

        ui: {
            add: '.add',
            cancel: '.cancel',
        },

        initialize: function(){
            nfRadio.channel( 'widget-forms' ).reply( 'show:newFormsGrid', this.showNewFormGrid, this );
            nfRadio.channel( 'widget-forms' ).reply( 'show:formsTable', this.showFormsTable, this );
        },
        
        onRender: function() {
            this.getUI( 'cancel' ).hide();
            this.showChildView( 'filter', new FilterView() );

            if( '#new-form' == window.location.hash ){
                this.getUI( 'add' ).hide();
                this.getUI( 'cancel' ).show();
                this.showChildView('content', new NewFormGridView() );
            } else {
                this.showChildView('content', new FormsTableView() );
            }
        },
        
        events: {
            'click @ui.add': 'showNewFormGrid',
            'click @ui.cancel': 'showFormsTable',
        },

        showNewFormGrid: function(){
            window.location.hash = 'new-form';
            this.showChildView( 'content', new NewFormGridView() );
            nfRadio.channel( 'widget-forms' ).trigger( 'change:content' );
            this.getUI( 'add' ).hide();
            this.getUI( 'cancel' ).show();
            this.getChildView( 'filter' ).updatePlaceholder( 'Search Templates' );
        },

        showFormsTable: function(){
            console.log( this );
            window.location.hash = 'forms';
            this.showChildView( 'content', new FormsTableView() );
            nfRadio.channel( 'widget-forms' ).trigger( 'change:content' );
            this.getUI( 'cancel' ).hide();
            this.getUI( 'add' ).show();
            this.getChildView( 'filter' ).updatePlaceholder('Search Forms' );
        }
    } );
    return view;
} );
