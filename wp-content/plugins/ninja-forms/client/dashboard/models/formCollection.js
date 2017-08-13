/**
 * Collection that holds our form models.
 *
 * @package Ninja Forms client
 * @copyright (c) 2017 WP Ninjas
 * @since 3.0
 */
define( ['models/formModel'], function( FormModel ) {
	var collection = Backbone.Collection.extend( {
		model: FormModel,
		comparator: 'title',
		tmpNum: 1,
        url: function() {
            return ajaxurl + "?action=nf_forms";
        },

		initialize: function() {
			this.newIDs = [];
            this.listenTo( nfRadio.channel( 'dashboard' ), 'forms:delete', this.modalConfirm );
            this.listenTo( nfRadio.channel( 'dashboard' ), 'forms:duplicate', this.duplicate );
            this.modal = new jBox( 'Modal', {
                width: 300,
                addClass: 'dashboard-modal',
                overlay: true,
                closeOnClick: 'body'
            } );
		},

        parse: function( response, options ){
		    return response.data;
        },

        modalConfirm: function( view ){
            var message = '<div class="message"><p>Once deleted, a Form cannot be recovered.<br />Are you sure you want to delete <em>' + view.model.get( 'title' ) + '</em>?</p></div>';
            message +=  '<div class="buttons"><div class="confirm nf-button primary">Delete</div> <div style="float:right;" class="cancel nf-button secondary">Cancel</div></div>';

            this.modal.setContent( message );
            this.modal.setTitle( 'Confirm Delete' );

            this.modal.open();

            var that = this;

            var btnCancel = this.modal.container[0].getElementsByClassName('cancel')[0];
            btnCancel.addEventListener('click', function() {
                that.modalClose();
            } );

            var btnConfirm = this.modal.container[0].getElementsByClassName('confirm')[0];
            btnConfirm.addEventListener('click', function() {
                that.confirmDelete( view );
            } );
        },

        modalClose: function() {
            this.modal.close();
        },

        confirmDelete: function( view ) {
            jQuery( view.el ).removeClass( 'show-actions' );
            jQuery( view.el ).addClass( 'deleting' );
            jQuery( view.el ).animate({
                'opacity': 0,
                'line-height': 0,
                'display': 'none'
            }, 500 );
            view.model.destroy();
            this.modalClose();
        },

        duplicate: function( view ) {
            var message = '<div class="message">Duplicating <em>' + view.model.get( 'title' ) +
                          '</em>...' + '<div class="nf-loading-spinner"></div></div>';
            this.modal.setContent( message );
            this.modal.setTitle( 'Please Wait' );
            this.modal.open();

            var that = this;
            jQuery.ajax({
                type: "POST",
                url: ajaxurl + '?action=nf_forms&clone_id=' + view.model.get( 'id' ),
                success: function( response ){
                    var response = JSON.parse( response );
                    var newID = response.data.new_form_id;
                    var clone = view.model.clone();
                    clone.set({
                        id: newID,
                        title: clone.get( 'title' ) + ' - copy',
                        created_at: new Date(),
                    });
                    clone.initShortcode( newID );
                    view.model.collection.add( clone );
                    that.modalClose();
                }
            });
        }
	} );

	return collection;
} );
