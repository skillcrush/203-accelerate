/**
 * Forms Widget Table Loading View
 *
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [], function() {
    var view = Marionette.View.extend( {
        template: "#tmpl-nf-widget-forms-table-loading",
        tagName: 'tr',
    } );
    return view;
} );
