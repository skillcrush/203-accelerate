/**
 * Batch Processor JS Object
 */
function NinjaBatchProcessor( settings ) {
	var that = this;
	var modalData = {
		closeOnClick: false,
        closeOnEsc: true,
        useProgressBar: true
	};

    /**
     * If we haven't been passed any content, make sure we pass an empty content var.
     */
    if ( 'undefined' == typeof settings.content ) {
        settings.content = '';
    }
    // Set our modalData content var.
    modalData.content = settings.content;

    /**
     * If we've been passed a loadingText var, pass that along.
     */
    if ( 'undefined' != typeof settings.loadingText ) {
        modalData.loadingText = settings.loadingText;
    }

    /**
     * If we haven't defined button text for our primary button, we don't want to pass button settings.
     *
     * Check to see if we've defined primary button text, and if we have, add button settings to the modalData.
     */
    if ( 'undefined' != typeof settings.btnPrimaryText ) {
        modalData.btnPrimary = {
            text: settings.btnPrimaryText,
            callback: function( e ) {
                // Hide the buttons.
                modalInstance.maybeShowActions( false );
                // Show the progress bar.
                modalInstance.maybeShowProgress( true );
                // Begin our cleanup process.
                that.postToProcessor( that, -1, modalInstance );

            }
        };

        modalData.btnSecondary = {
            text: settings.btnSecondaryText,
            callback: function( e ) {
                modalInstance.toggleModal( false );
            }
        };
    } else { // If we don't have any buttons defined, then we want to run the batch process on modal open.
        modalData.onOpenCallback = function() {
            // Hide the buttons.
            this.maybeShowActions( false );
            // Show the progress bar.
            this.maybeShowProgress( true );
            // Begin our cleanup process.
            that.postToProcessor( that, -1, this );
        }
    }

    this.postToProcessor = function( context, steps, modal, data ) {
        if ( 'undefined' == typeof data ) {
            var data = {
                action: 'nf_batch_process',
                batch_type: settings.batch_type,
                security: nfAdmin.batchNonce,
                extraData: settings.extraData
            };            
        }

        jQuery.post( ajaxurl, data, function( response ) {
            response = JSON.parse( response );
            // If we're done...
            if ( response.batch_complete ) {
                // Push our progress bar to 100%.
                modal.setProgress( 100 );
                modal.toggleModal( false );
                if ( 'undefined' != typeof settings.onCompleteCallback ) {
                    settings.onCompleteCallback( response );
                }
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
            // If our PHP edited our extraData variable, update our JS var and pass it along.
            if ( 'undefined' != typeof response.extraData ) {
                // Update our extraData property.
                data.extraData = response.extraData;                
            }

            // Calculate our current step.
            var step = steps - response.step_remaining;
            // Calculate our maximum progress for this step.
            var maxProgress = Math.round( step / steps * 100 );
            // Increment the progress.
            modal.incrementProgress ( maxProgress );
            // Recall our function...
            context.postToProcessor( context, steps, modal, data );
        } );
    }

	var modalInstance = new NinjaModal( modalData );
}