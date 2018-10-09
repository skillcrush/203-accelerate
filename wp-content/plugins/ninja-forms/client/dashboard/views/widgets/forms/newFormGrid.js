/**
 * Forms Widget Templates View
 *
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [
    'models/formTemplateCollection',
    'views/widgets/forms/newFormTemplate'
], function( TemplateCollection, TemplateView ) {
    var view = Marionette.CollectionView.extend( {
        tagName: 'div',
        className: 'template-list',
        collection: new TemplateCollection(),
        childView: TemplateView,

        initialize: function(){
            this.listenTo( nfRadio.channel( 'widget-forms' ), 'update:filter', this.updateFilter );
        },

        updateFilter: function( term ){
            this.setFilter(function (child, index, collection) {
                return 0 <= child.get( 'title' ).toLowerCase().indexOf( term.toLowerCase() );
            });
        }
    } );
    return view;
} );
