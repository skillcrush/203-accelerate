jQuery(document).ready(function($) {
	var working = false;
	var message, container, messageBox, deleteInput, deleteMsgs, buttons, confirm, cancel, lineBreak;
	container = document.createElement( 'div' );
	messageBox = document.createElement( 'p' );
	deleteInput = document.createElement( 'input' );
	deleteInput.type = 'text';
	deleteInput.id = 'confirmDeleteInput';
	buttons = document.createElement( 'div' );
	buttons.style.marginTop = '10px';
	buttons.style.backgroundColor = '#f4f5f6';
	confirm = document.createElement( 'div' );
	confirm.style.padding = '8px';
	confirm.style.cursor = 'default';
	confirm.style.backgroundColor = '#d9534f';
	confirm.style.borderColor = '#d9534f';
	confirm.style.fontSize = '14pt';
	confirm.style.fontWeight = 'bold';
	confirm.style.color = '#ffffff';
	confirm.style.borderRadius = '4px';
	cancel = document.createElement( 'div' );
	cancel.style.padding = '8px';
	cancel.style.cursor = 'default';
	cancel.style.backgroundColor = '#5bc0de';
	cancel.style.borderColor = '#5bc0de';
	cancel.style.fontSize = '14pt';
	cancel.style.fontWeight = 'bold';
	cancel.style.color = '#ffffff';
	cancel.style.borderRadius = '4px';
	lineBreak = document.createElement( 'br' );
	container.classList.add( 'message' );
	messageBox.innerHTML += 'This will DELETE all forms, form submissions,' +
		' and deactivate Ninja Forms';

	messageBox.appendChild( lineBreak );
	messageBox.innerHTML += '<br>Type <span style="color:red;">DELETE</span>' +
		' to' +
		' confirm';

	container.appendChild( messageBox );
	container.appendChild( deleteInput );
	container.appendChild( lineBreak );
	deleteMsgs = document.createElement( 'div' );
	deleteMsgs.id = 'progressMsg';
	deleteMsgs.style.display = 'none';
	deleteMsgs.style.color = 'red';
	container.appendChild( deleteMsgs );
	confirm.innerHTML = 'Delete';
	confirm.classList.add( 'confirm', 'nf-button', 'primary' );
	confirm.style.float = 'left';
	cancel.innerHTML = 'Cancel';
	cancel.classList.add( 'cancel', 'nf-button', 'secondary', 'cancel-delete-all' );
	cancel.style.float = 'right';
	buttons.appendChild( confirm );
	buttons.appendChild( cancel );
	buttons.classList.add( 'buttons' );
	container.appendChild( buttons );
	message = document.createElement( 'div' );
	message.appendChild( container );

	var downgradeContainer, downgradeTitle, downgradeWarning, downgradeCTA, downgradeInput;
	downgradeInput 						= document.createElement( 'input' );
	downgradeInput.setAttribute( 'type', 'text' );
	downgradeInput.id 					= 'downgradeConfirmInput';
	downgradeInput.style.marginBottom 	= '15px';
	downgradeInput.style.padding 		= '7px';
	downgradeInput.style.border 		= '1px solid #D3D3D3';
	downgradeInput.style.width 			= '100%';
	downgradeInput.style.height 		= '3em';
	downgradeCTA 						= document.createElement( 'p' );
	downgradeCTA.innerHTML 				= nf_settings.i18n.downgradeConfirmMessage;
	downgradeWarning 					= document.createElement( 'p' );
	downgradeWarning.innerHTML 			= nf_settings.i18n.downgradeWarningMessage;
	downgradeWarning.style.color 		= 'red';
	downgradeTitle 						= document.createElement( 'h3' );
	downgradeTitle.innerHTML 			= nf_settings.i18n.downgradeMessage;
	downgradeContainer 					= document.createElement( 'div' );
	downgradeContainer.appendChild( downgradeTitle );
    downgradeContainer.appendChild( downgradeWarning );
    downgradeContainer.appendChild( downgradeCTA );
    downgradeContainer.appendChild( downgradeInput );



	// set up delete model with all the elements created above
	deleteAllDataModal = new jBox( 'Modal', {
		width: 450,
		addClass: 'dashboard-modal',
		overlay: true,
		closeOnClick: 'body'
	} );

	deleteAllDataModal.setContent( message.innerHTML );
	deleteAllDataModal.setTitle( 'Delete All Ninja Forms Data?' );

	// add event listener for cancel button
	var btnCancel = deleteAllDataModal.container[0].getElementsByClassName('cancel')[0];
	btnCancel.addEventListener('click', function() {
		if( ! working ) {
			deleteAllDataModal.close();
		}
	} );

	var doAllDataDeletions = function( formIndex ) {
		var last_form = 0;
		// Gives the user confidence things are happening
	    $( '#progressMsg' ).html( 'Deleting submissions for '
	        + nf_settings.forms[ formIndex ].title + "" + ' ( ID: '
	        + nf_settings.forms[ formIndex ].id + ' )' );
		$( '#progressMsg').show();
		// notify php this is the last one so it delete data and deactivate NF
	    if( formIndex === nf_settings.forms.length - 1 ) {
	    	last_form = 1;
	    }
	    // do this deletion thang
		$.post(
			nf_settings.ajax_url,
			{
				'action': 'nf_delete_all_data',
				'form': nf_settings.forms[ formIndex ].id,
				'security': nf_settings.nonce,
				'last_form': last_form
			}
		).then (function( response ) {
			formIndex = formIndex + 1;
			response = JSON.parse( response );
			// we expect success and then move to the next form
			if( response.data.success ) {
				if( formIndex < nf_settings.forms.length ) {
					doAllDataDeletions( formIndex )
				} else {
					// if we're finished deleting data then redirect to plugins
					if( response.data.plugin_url ) {
						window.location = response.data.plugin_url;
					}
				}
			}
		} ).fail( function( xhr, status, error ) {
			// writes error messages to console to help us debug
			console.log( xhr.status + ' ' + error + '\r\n' +
				'There was an error deleting submissions for '
					+ nf_settings.forms[ formIndex ].title );
		});
	};
	// Add event listener for delete button
	var btnDelete = deleteAllDataModal.container[0].getElementsByClassName('confirm')[0];
	btnDelete.addEventListener('click', function() {
		var confirmVal = $('#confirmDeleteInput').val();

		if (! working) {
			working = true;
			// Gotta type DELETE to play
			if ('DELETE' === confirmVal) {
				this.style.backgroundColor = '#9f9f9f';
				this.style.borderColor = '#3f3f3f';

				var cancelBtn = $( '.cancel-delete-all' );
				cancelBtn.css( 'backgroundColor', '#9f9f9f' );
				cancelBtn.css( 'borderColor', '#3f3f3f');

				// this is the first one, so we'll start with index 0
				doAllDataDeletions(0);
			} else {
				deleteAllDataModal.close();
				working = false;
			}
		}
	} );

    $( '.js-delete-saved-field' ).click( function(){

        var that = this;

        var data = {
            'action': 'nf_delete_saved_field',
            'field': {
                id: $( that ).data( 'id' )
            },
            'security': nf_settings.nonce
        };

        $.post( nf_settings.ajax_url, data )
            .done( function( response ) {
                $( that ).closest( 'tr').fadeOut().remove();
            });
    });

	// Start building our downgrade model.
    jQuery( '#nfDowngrade' ).click( function( e ) {
        var data = {
            class: 'nfDowngradeModal',
            closeOnClick: 'body',
            closeOnEsc: true,
			// TODO: Maybe this should be build using DOM node construction?
            content: downgradeContainer.innerHTML,
            btnPrimary: {
                text: nf_settings.i18n.downgradeButtonPrimary,
				class: 'nfDowngradeButtonPrimary',
                callback: function( e ) {
                	// If our "Downgrade" button does not have have an attribute of disabled...
                    if( 'disabled' !== $('.nfDowngradeButtonPrimary').attr( 'disabled' ) ){
                    	// ...get the url...
                    	var url = window.location.href;
                    	// ...split the url based on the question mark from the query string...
                    	url = url.split( '?' );
                    	// build the downgrade url and redirect the user.
                    	url[0] = url[0] + '?page=ninja-forms&nf-switcher=rollback';
                    	window.location.replace( url[0] );
					}
                }
            },
            btnSecondary: {
                text: nf_settings.i18n.downgradeButtonSecondary,
				class: 'nfDowngradeButtonSecondary',
                callback: function( e ) {
                	// Close the modal if this button is clicked.
                    downgradeModel.toggleModal( false );
                }
            },
            useProgressBar: false,
        };
        var downgradeModel = new NinjaModal( data );
        // Style and add the disabled tag by default.
        $('.nfDowngradeButtonPrimary').css( 'background', '#ccc' );
        $('.nfDowngradeButtonPrimary').css( 'border', '#ccc 1px solid' );
        $('.nfDowngradeButtonPrimary').attr( 'disabled', true );

        // Listen to our input and...
        $('#downgradeConfirmInput').on( 'keyup', function(){
        	// ...if DOWNGRADE is typed then...
        	if( 'DOWNGRADE' == $('#downgradeConfirmInput').val() ) {
                // ...apply our blue styling to button and remove disabled attribute.
        		$('.nfDowngradeButtonPrimary').css( 'background', '#1EA9EA' );
                $('.nfDowngradeButtonPrimary').css( 'border', '#1EA9EA 1px solid' );
                $('.nfDowngradeButtonPrimary').removeAttr( 'disabled' );
            }

            // ...if DOWNGRADE is not typed then...
            if( 'DOWNGRADE' !== $('#downgradeConfirmInput').val() ) {
                // ...set styling back to default and reapply the disabled prop.
        		$('.nfDowngradeButtonPrimary').css( 'background', '#ccc' );
                $('.nfDowngradeButtonPrimary').css( 'border', '#ccc 1px solid' );
                $('.nfDowngradeButtonPrimary').prop( 'disabled', true );
			}
		})
    });

	$( document ).on( 'click', '#delete_on_uninstall', function( e ) {
		deleteAllDataModal.open();
	} );

	$( document ).on( 'click', '.nf-delete-on-uninstall-yes', function( e ) {
		e.preventDefault();
		$( "#delete_on_uninstall" ).attr( 'checked', true );

	} );
    
    // If we're allowed to track site data...
    if ( '1' == nf_settings.allow_telemetry ) {
        // Show the optout button.
        $( '#nfTelOptin' ).addClass( 'hidden' );
        $( '#nfTelOptout' ).removeClass( 'hidden' );
    } // Otherwise...
    else {
        // Show the optin button.
        $( '#nfTelOptout' ).addClass( 'hidden' );
        $( '#nfTelOptin' ).removeClass( 'hidden' );
    }
    
    // If optin is clicked...
    $( '#nfTelOptin' ).click( function( e ) {
        // Hide the button.
        $( '#nfTelOptin' ).addClass( 'hidden' );
        $( '#nfTelSpinner' ).css( 'display', 'inline-block' );
        // Hit AJAX endpoint and opt-in.
        $.post( ajaxurl, { action: 'nf_optin', ninja_forms_opt_in: 1 },
                    function( response ) {
            $( '#nfTelOptout' ).removeClass( 'hidden' );
            $( '#nfTelSpinner' ).css( 'display', 'none' );
        } );  
    } );
    
    // If optout is clicked...
    $( '#nfTelOptout' ).click( function( e ) {
        // Hide the button.
        $( '#nfTelOptout' ).addClass( 'hidden' );
        $( '#nfTelSpinner' ).css( 'display', 'inline-block' );
        // Hit AJAX endpoint and opt-out.
        $.post( ajaxurl, { action: 'nf_optin', ninja_forms_opt_in: 0 },
                    function( response ) {
            $( '#nfTelOptin' ).removeClass( 'hidden' );
            $( '#nfTelSpinner' ).css( 'display', 'none' );
        } );  
    } );

    jQuery( '#nfTrashExpiredSubmissions' ).click( function( e ) {
    	var that = this;
    	var data = {
    		closeOnClick: false,
            closeOnEsc: true,
            content: '<p>' + nf_settings.i18n.trashExpiredSubsMessage + '<p>',
            btnPrimary: {
				text: nf_settings.i18n.trashExpiredSubsButtonPrimary,
				callback: function( e ) {
                    // Hide the buttons.
                    deleteModal.maybeShowActions( false );
                    // Show the progress bar.
                    deleteModal.maybeShowProgress( true );
                    // Begin our cleanup process.
                    that.submissionExpirationProcess( that, -1, deleteModal );

				}
			},
            btnSecondary: {
            	text: nf_settings.i18n.trashExpiredSubsButtonSecondary,
				callback: function( e ) {
            		deleteModal.toggleModal( false );
				}
			},
            useProgressBar: true,
		};

        this.submissionExpirationProcess = function( context, steps, modal ) {
            var data = {
                action: 'nf_batch_process',
                batch_type: 'expired_submission_cleanup',
                security: nf_settings.batch_nonce
            };
            jQuery.post( nf_settings.ajax_url, data, function( response ) {
                response = JSON.parse( response );
                // If we're done...
                if ( response.batch_complete ) {
                    // Push our progress bar to 100%.
                    modal.setProgress( 100 );
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
                context.submissionExpirationProcess( context, steps, modal );
            } );
        }

    	var deleteModal = new NinjaModal( data );
	});
});
