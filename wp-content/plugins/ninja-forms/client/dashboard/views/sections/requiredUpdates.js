/**
 * @package Ninja Forms
 * @subpackage Dashboard
 * @copyright (c) 2017 WP Ninjas
 * @since 3.2
 */
define( [], function() {
    var view = Marionette.View.extend( {
        template: '#tmpl-nf-requiredUpdates',

        updates: [], //an object containing updates to be performed
       
        currentUpdate: 0,  // current update out of totalUpdate

        totalUpdates: -1, // we start with -1 and overwrite it 

        updatesRemaining: -1,// how many update are left

        ui: {
            requiredUpdates: '.nf-required-update',
    
        },

        /**
         * When we render this section, check for updates
         */
        onRender: function() {
            this.getRequiredUpdates();
        },

        /**
         * Set up the initial on click for the button, as it changes after
         * the updates are done
         */
        setButtonClickEvent: function() {
            var that = this;

            // tell the button to do updates on click
            jQuery( '#nf-required-updates-btn' )
                            .off( 'click' )
                            .on( 'click', function( e ) {
                                e.preventDefault();
                                that.doRequiredUpdates();
                                jQuery( this ).hide();
                            } );
        },

        /**
         * Get any required updates that might need to happen
         */
        getRequiredUpdates: function() {

            var that = this;
            jQuery.get( ajaxurl, { action: 'nf_required_update' } )
                .then( function( response ) {
                    var res = JSON.parse( response ); 
                    
                    if( 0 === res.errors.length) {
                        // get the number of updates
                        that.totalUpdates = res.data.updates.length;

                        // get the updates into an array
                        that.updates = res.data.updates;

                        if( 0 < that.updates.length ) {
                            that.requiredUpdates = that.updates.length;
                            // now that we have updates, let's make our table
                            that.constructUpdateTable();

                            // set up the click event for the button to do updates
                            that.setButtonClickEvent();
                        } else {
                           window.location = window.location.origin + 
                            window.location.pathname + window.location.search;
                        }

                    }
                } );
        },

        /**
         * Construct a table that shows required updates
         */
        constructUpdateTable: function() {
            var that = this;
            // get the table and header
            var updateTable = document.getElementById( 'nf-upgrades-table' );
            var tableHeader = updateTable.getElementsByTagName( 'thead' )[0];

            // create the header row
            var headerRow = document.createElement( 'tr' );

            // create the header cell for update name column
            var updateNameHeaderCell = document.createElement( 'th' );
            updateNameHeaderCell.innerHTML = "Update";
            updateNameHeaderCell.classList.add( "nf-update-name-cell" );

            // create header cell for progress bar column
            var updateProgressHeaderCell = document.createElement( 'th' );
            updateProgressHeaderCell.innerHTML = "Progress";
            updateProgressHeaderCell.classList.add( "nf-update-progress-cell" );

            // append header cells to header row
            headerRow.appendChild( updateNameHeaderCell );
            headerRow.appendChild( updateProgressHeaderCell );

            // append header row to table header
            tableHeader.appendChild( headerRow );

            // get the table body
            var tableBody = updateTable.getElementsByTagName( 'tbody' )[0];

            // create a table row for each required update
            jQuery.each( this.updates, function( i, update ) { 
                var tableRow = document.createElement( 'tr' );

                var updateNameCell = document.createElement( 'td' );
                updateNameCell.innerHTML = update.nicename;

                var updateProgressCell = document.createElement( 'td' );
                var updateProgressBar = document.createElement( 'div' );
                
                updateProgressBar.id = "update-progress-" + i;

                var newProgressBar = that.createNewProgressBar( i );

                updateProgressBar.appendChild( newProgressBar );

                updateProgressCell.appendChild( updateProgressBar );

                tableRow.appendChild( updateNameCell );
                tableRow.appendChild( updateProgressCell );

                tableBody.appendChild( tableRow );
            } );

            var updateBtn = document.getElementById( 'nf-required-updates-btn' );
            updateBtn.style.display = 'block';

        },

        /**
         * Function that starts running required updates if we have any
         */

        doRequiredUpdates: function() {
            // set the window.location hash just in case
            window.location.hash = '#requiredUpdates';

            var context = this;
            
            // disable the button once we've clicked
            jQuery( '#nf-required-updates-btn' ).addClass( 'disabled' ).attr( 'disabled', 'disabled' );
            
            //make initial call to initiate required updates
            jQuery.post( ajaxurl, { action: 'nf_required_update', security: nfAdmin.updateNonce } )
                .then( function( response ) {
                    var res = JSON.parse( response );
                    
                    // if we still have updates remaining, call the ajax again
                    if( res.updatesRemaining > 0 ) {

                        /**
                         * We had to add this if/else b/c the classes were returning
                         * results where the currentStep and stepsTotal values
                         * were the same, but the updatesRemaining value had changed,
                         * thus causing any progress bars after the first to 
                         * automatically show 100% even though the updates continue
                         */
                        if( context.updatesRemaining !== res.updatesRemaining 
                            && res.currentStep === res.stepsTotal ) {
                            
                            // finish the current update
                            context.finishUpdate( context.currentUpdate );

                            // update the remaining updates
                            context.updatesRemaining = res.updatesRemaining;
                        } else {
                            // this will show progress bars that are processing
                            context.showProgressBars( res );

                            // update the remaining updates
                            context.updatesRemaining = res.updatesRemaining;
                        }

                         // keep moving through required updates
                         context.doRequiredUpdates();
                    } else {

                        // finish the current update
                        context.finishUpdate( context.currentUpdate );

                        // set the globle required updates variable to 0 
                        nfAdmin.requiredUpdates = 0;

                        // remove the disabled items and set the click to the dashboard
                        jQuery( '#nf-required-updates-btn' )
                            .removeClass( 'disabled' )
                            .removeAttr( 'disabled' )
                            .val( 'Go To Dashboard' )
                            .off( 'click' )
                            .on( 'click', function( e ) {
                                e.preventDefault();
                                window.location = window.location.origin + 
                                   window.location.pathname + window.location.search;
                            } )
                            .show();

                        console.log( "UPDATES DONE" );
                    }
                });
        },

        /**
         * Function create and display progress bars. 
         * We create one for each update
         * 
         * @param data 
         */
        showProgressBars: function( data ) {
            var update = this.totalUpdates - data.updatesRemaining;
            var progress = data.currentStep;
            var totalSteps = data.stepsTotal;

            // get the progress bar we are dealing with
            var currentProgressBar = document.getElementById( 'nf_progressBar_' + update );
            
            if( null == currentProgressBar ) {
                // if the element requested is null, then we know this is a new update
                this.currentUpdate += 1;
                if( 1 === this.currentUpdate && -1 === this.totalUpdates ) {
                    // the initial 'update' value with be how many remaining(total updates)
                    this.totalUpdates = update;
                }
                // create a new progress bar if it doesn't exist
                currentProgressBar = this.createNewProgressBar( update );
            }

            // update the progress bar
            this.incrementProgress( update, progress, totalSteps)
        },

        /**
         * Create a new progress bar for the new update
         * 
         * @param update
         * 
         * @returns newProgressBarContainer
         */
        createNewProgressBar: function( update ) {
            //create new container
            var newProgressBarContainer = document.createElement( 'div' );
            newProgressBarContainer.id = 'nf_progressBar_' + update;
            newProgressBarContainer.classList.add( 'jBox-content' );
            newProgressBarContainer.style.display = 'none';

            // create new progress bar
            var newProgressBar = document.createElement( 'div' );
            newProgressBar.classList.add( 'nf-progress-bar' );

            // create the slider
            var newProgressSlider = document.createElement( 'div' );
            newProgressSlider.id = 'nf-progress-bar-slider-' + update;
            newProgressSlider.classList.add( 'nf-progress-bar-slider' );

            // append the slider to the progress bar
            newProgressBar.appendChild( newProgressSlider );

            // append the progress bar to the container
            newProgressBarContainer.appendChild( newProgressBar );

            return newProgressBarContainer;
        },


        /**
         * Increment the progress based on total steps and current progress
         * 
         * @param update
         * @param progress
         * @param totalSteps
         */
        incrementProgress: function( update, currentStep, totalSteps ) {
            var progressBarContainer = document.getElementById( 'nf_progressBar_' + update );
            progressBarContainer.style.display = 'block';
            
            // get the slider element
            var progressBar = document.getElementById( 'nf-progress-bar-slider-' + update );

            // get the current progress(%) based on total steps and currentStep
            var newValue = ( Number( currentStep ) / Number( totalSteps ) ) * 100;
            
            // Get our current progress.
            var currentProgress = progressBar.offsetWidth / progressBar.parentElement.offsetWidth * 100;
            
            // If the new value is greater than the currentProgress, update it
            if ( newValue > currentProgress ) {
                this.setProgress( update, newValue );
            }
        },

        /**
         * Sets the current progress for the current progress bar
         * 
         * @param update
         * @param percent
         */
        setProgress: function( update, percent ) {
            // Update the width of the element as a percentage.
            var progressBar = document.getElementById( 'nf-progress-bar-slider-' + update );
            progressBar.style.width = percent + '%';

            if( 100 <= percent ) {
                this.finishUpdate( update );
            }
        },

        /**
         * If an update is done, then let's set it to done in the updates table
         */
        finishUpdate: function( update ) {
            // get the progress bar container of the one that is done
            var progressBarContainer = document.getElementById( 'nf_progressBar_' + update );

            // get it's parent table cell
            var progressCell = progressBarContainer.parentNode;

            // remove the progress bar element
            progressCell.removeChild( progressBarContainer );

            // add the span with 'Done' check mark
            var finishedSpan = document.createElement( 'span' );
            finishedSpan.classList.add( 'dashicons' );
            finishedSpan.classList.add( 'dashicons-yes' );

            // append it to the progress cell
            progressCell.appendChild( finishedSpan );

            // update the current update
            this.currentUpdate = this.currentUpdate + 1;

        }
    } );
    return view;
} );
