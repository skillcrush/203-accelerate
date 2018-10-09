/**
 * Forms Widget Table Row View
 *
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [], function() {
    var view = Marionette.View.extend( {
        template: "#tmpl-nf-widget-forms-table-row",
        tagName: 'tr',
        replaceElement: true,
        ui: {
            delete: '.delete',
            duplicate: '.duplicate',
            edit: '.nf-item-edit'
        },
        events: {
            'click @ui.delete': function() {
                nfRadio.channel( 'dashboard' ).trigger( 'forms:delete', this );
            },
            'click @ui.duplicate': function() {
                nfRadio.channel( 'dashboard' ).trigger( 'forms:duplicate', this );
            },
            'click @ui.edit': function( event ) {
                this.$el.toggleClass( 'show-actions' ).siblings().removeClass( 'show-actions' );
            }
        },

        templateContext: function() {
            var model = this.model;
            return {
                created_at: moment( model.get( 'created_at' ) ).format( 'MM/DD/YY h:mm A' )
            }
        }
    } );
    return view;
} );
