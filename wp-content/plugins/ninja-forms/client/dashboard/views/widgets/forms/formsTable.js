/**
 * Forms Widget Table View
 *
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [
    'views/widgets/forms/formsTableBody',
    'views/widgets/forms/formsTableLoading',
    'models/formCollection'
], function( TableBodyView, TableLoadingView, FormsCollection ) {
    var view = Marionette.View.extend( {
        template: "#tmpl-nf-widget-forms-table",
        className: 'nf-table-display',
        tagName: 'table',

        initialize: function(){
            var formsTable = this;
            this.listenTo( nfRadio.channel( 'dashboard' ), 'fetch:forms', function( collection ){
                formsTable.showChildView( 'body', new TableBodyView( { collection: collection } ) );
            });
        },

        regions: {
            body: {
                el: 'tbody',
                replaceElement: true
            }
        },

        ui: {
            sortable: '.sortable',
            body: 'tbody',
            action2: '.action2',
            more: '.more',
            less: '.less'
        },

        onRender: function() {
            this.getUI( 'less' ).hide();
            var collection = nfRadio.channel( 'dashboard' ).request( 'get:forms' );
            if( 'undefined' == typeof collection ) {
                this.showChildView('body', new TableLoadingView());
            } else {
                this.showChildView( 'body', new TableBodyView( { collection: collection } ) );
            }
            this.maybeHideMoreButton();
        },

        events: {
            'click @ui.sortable': 'sortFormsTable',
            'click @ui.more': 'showMore',
            'click @ui.less': 'showLess'
        },

        sortFormsTable: function( event ){
            this.getUI( 'sortable' ).removeClass( 'sorted-asc' );
            this.getUI( 'sortable' ).removeClass( 'sorted-desc' );
            var sortBy = jQuery( event.target ).data( 'sort' );
            var reverse = jQuery( event.target ).data( 'reverse' ) || 0;
            if( reverse ){
                jQuery( event.target ).addClass( 'sorted-desc' );
                jQuery( event.target ).removeClass( 'sorted-asc' );
            } else {
                jQuery( event.target ).addClass( 'sorted-asc' );
                jQuery( event.target ).removeClass( 'sorted-desc' );
            }

            var collection = this.getChildView( 'body' ).collection;

            collection.comparator = function( a, b ) {
                name1 = a.get( sortBy ).toLowerCase();
                name2 = b.get( sortBy ).toLowerCase();

                if ( name1 < name2 ) {
                    ret = -1;
                } else if ( name1 > name2 ) {
                    ret = 1;
                } else {
                    ret = 0;
                }

                if( reverse ){
                    ret = -ret;
                }
                return ret;
            }
            collection.sort();

            if( reverse ){
                collection.models.reverse();
                jQuery( event.target ).data( 'reverse', 0 );
            } else {
                jQuery( event.target ).data( 'reverse', 1 );
            }
        },

        showMore: function(){
            this.getUI( 'more' ).hide();
            this.getUI( 'less' ).show();
            this.getUI( 'body' ).addClass( 'more' );
        },

        showLess: function(){
            this.getUI( 'less' ).hide();
            this.getUI( 'more' ).show();
            this.getUI( 'body' ).removeClass( 'more' );
        },

        maybeHideMoreButton: function(){
            if( 'undefined' !== typeof this.collection && 10 < this.collection.length ) return;
            this.getUI( 'action2' ).hide();
        },
        
    } );
    return view;
} );
