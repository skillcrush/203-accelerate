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
            return ajaxurl + "?action=nf_forms&security=" + nfAdmin.ajaxNonce;
        },

		initialize: function() {
			this.newIDs = [];
			this.baseUrl = window.location.href.split('?')[0];
			
            this.listenTo( nfRadio.channel( 'dashboard' ), 'forms:delete', this.modalConfirm );
            this.listenTo( nfRadio.channel( 'dashboard' ), 'forms:duplicate', this.duplicate );
            this.modal = new jBox( 'Modal', {
                width: 400,
                addClass: 'dashboard-modal',
                overlay: true,
                closeOnClick: 'body'
            } );
		},

        parse: function( response, options ){
            if (response.data.hasOwnProperty('error')) {
                alert(response.data.error);
                return null;
            }
		    return response.data;
        },

        modalConfirm: function( view ){
            var message, container, messageBox, title, buttons, confirm, cancel, lineBreak;
            var formID = view.model.get( 'id' );
            var formTitle = view.model.get( 'title' );

            container = document.createElement( 'div' );
            container.style.paddingRight = '20px';
            container.style.paddingLeft = '20px';
            container.style.paddingBottom = '20px';
            messageBox = document.createElement( 'p' );
            title = document.createElement( 'em' );
            buttons = document.createElement( 'div' );
            confirm = document.createElement( 'button' );
            cancel = document.createElement( 'div' );

            container.classList.add( 'message' );
            title.innerHTML = formTitle;
            messageBox.innerHTML += nfi18n.deleteWarningA + ' (<strong>'
	            + formTitle + '</strong>). ' + nfi18n.deleteWarningB;
            messageBox.appendChild( document.createElement( 'br') );
            messageBox.appendChild( document.createElement( 'br') );

	        var exportFormLink = document.createElement( 'a' );
	        // link to export page with this form selected
	        exportFormLink.href = this.baseUrl + '?page=nf-import-export&exportFormId='
                + formID;
	        exportFormLink.innerHTML = '<i class="fa fa-download"' +
		        ' style="padding:5px;"></i>' + nfi18n.deleteXForm;
	        exportFormLink.target = '_blank'; // open in new tab
	        messageBox.appendChild( exportFormLink );
	        messageBox.appendChild( document.createElement( 'br') );

	        var exportSubmissionLink = document.createElement( 'a' );

	        // link to export submissions page
	        exportSubmissionLink.href = this.baseUrl + '?page=nf-processing&action=download_all_subs&form_id='
	            + formID + '&redirect=' + encodeURIComponent( this.baseUrl.replace( 'admin.php', 'edit.php' ) + '?post_status=all&post_type=nf_sub&form_id='
	            + formID );
	        exportSubmissionLink.target = '_blank';
	        exportSubmissionLink.innerHTML = '<i class="fa fa-download" ' +
	            'style="padding:5px;"></i>' + nfi18n.deleteXSubs;

	        messageBox.appendChild( exportSubmissionLink );
            messageBox.appendChild( document.createElement( 'br') );

            container.appendChild( messageBox );

            var inputLabel = document.createElement( 'label' );
            inputLabel.for = 'confirmDeleteFormInput';
            inputLabel.innerHTML = nfi18n.deleteConfirmA + ' <span style="color:red;">DELETE</span> ' + nfi18n.deleteConfirmB;

	        var deleteInput = document.createElement( 'input' );
	        deleteInput.type = 'text';
	        deleteInput.id = 'confirmDeleteFormInput';
	        deleteInput.style.marginTop = '10px';
	        deleteInput.style.width = '100%';
	        deleteInput.style.height = '2.5em';
	        deleteInput.style.fontSize = '1em';

	        container.appendChild( inputLabel );
	        container.appendChild( document.createElement( 'br' ) );
	        container.appendChild( deleteInput );
	        container.appendChild( document.createElement( 'br' ) );
	        container.appendChild( document.createElement( 'br' ) );

            confirm.innerHTML = nfi18n.delete;
            confirm.classList.add( 'confirm', 'nf-button', 'primary', 'pull-right'  );
            cancel.innerHTML = nfi18n.cancel;
            cancel.classList.add( 'cancel', 'nf-button', 'secondary' );
            buttons.appendChild( cancel );
	        buttons.appendChild( confirm );
            buttons.classList.add( 'buttons' );
            container.appendChild( buttons );
            message = document.createElement( 'div' );
            message.appendChild( container );

            this.modal.setContent( message.innerHTML );
            this.modal.setTitle( nfi18n.deleteTitle );

            this.modal.open();

            var that = this;

            var btnCancel = this.modal.container[0].getElementsByClassName('cancel')[0];
            btnCancel.addEventListener('click', function() {
                that.modalClose();
            } );

            var btnConfirm = this.modal.container[0].getElementsByClassName('confirm')[0];
            btnConfirm.addEventListener('click', function( e ) {
                e.preventDefault();
                var deleteInputVal = document.getElementById( 'confirmDeleteFormInput' ).value;
                
                if( 'DELETE' === deleteInputVal ) {
	                that.confirmDelete(view);
                } else {
                    that.modalClose();
                }
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
            console.log(view);
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
                url: ajaxurl + '?action=nf_forms&clone_id=' + view.model.get( 'id' ) + '&security=' + nfAdmin.ajaxNonce,
                success: function( response ){
                    var response = JSON.parse( response );

                    if(response.data.hasOwnProperty('error')) {
                        alert(response.data.error);
                    } else {
                        var newID = response.data.new_form_id;
                        var clone = view.model.clone();
                        clone.set({
                            id: newID,
                            title: clone.get( 'title' ) + ' - copy',
                            created_at: new Date(),
                        });
                        clone.initShortcode( newID );
                        view.model.collection.add( clone );
                    }
                    that.modalClose();
                }
            });
        }
	} );

	return collection;
} );
