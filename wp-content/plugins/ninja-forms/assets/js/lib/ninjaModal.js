/**
 * Definition of the NinjaModal class.
 * 
 * @param data (object) The default data to be passed into the class.
 *   data.width (int) The width of the modal.
 *   data.class (string) The class to be applied to the modal.
 *   data.closeOnClick (string/bool) The click options to close the modal.
 *   data.closeOnEsc (bool) Whether or not to close the modal on escape.
 *   data.title (string) The title of the modal.
 *   data.content (string) The content of the modal.
 *   data.btnPrimary (object) Information about the primary button of the modal.
 *     btnPrimary.text (string) The text content of the button.
 *     btnPrimary.class (string) The class to be added to the button.
 *     btnPrimary.callback (function) The function to be called when the button is clicked.
 *   data.btnSecondary (object) Information about the secondary button of the modal.
 *     btnSecondary.text (string) The text content of the button.
 *     btnSecondary.class (string) The class to be added to the button.
 *     btnSecondary.callback (function) The function to be called when the button is clicked.
 *   data.useProgressBar (bool) Whether or not this modal needs the progress bar.
 *   data.loadingText (string) The text to be shown while the progress bar is visible.
 */
function NinjaModal ( data ) {
    // Setup our modal settings.
    this.settings = {
        width: ( 'undefined' != typeof data.width ? data.width : 400 ),
        class: ( 'undefined' != typeof data.class ? data.class : 'dashboard-modal' ),
        closeOnClick: ( 'undefined' != typeof data.closeOnClick ? data.closeOnClick : 'body' ),
        closeOnEsc: ( 'undefined' != typeof data.closeOnEsc ? data.closeOnEsc : true )
    }
    // Setup our title.
    this.title = ( 'undefined' != typeof data.title ? data.title : '' );
    // Setup our content.
    this.content = ( 'undefined' != typeof data.content ? data.content : '' );
    // See if we need buttons.
    this.buttons = {};
    this.buttons.primary = {};
    this.buttons.secondary = {};
    this.buttons.primary.data = ( 'undefined' != typeof data.btnPrimary ? data.btnPrimary : false );
    this.buttons.secondary.data = ( 'undefined' != typeof data.btnSecondary ? data.btnSecondary : false );
    // See if we need the progress bar.
    this.useProgressBar = ( 'undefined' !=  typeof data.useProgressBar ? data.useProgressBar : false );
    if ( this.useProgressBar ) {
        // TODO: translate
        this.loadingText = ( 'undefined' != typeof data.loadingText ? data.loadingText : 'Loading...' );
    }
    // Declare our popup item.
    this.popup;
    // Declare our button booleans.
    this.hasPrimary = false;
    this.hasSecondary = false;
    // Initialize the popup.
    this.initModal();
    // Show the popup.
    this.toggleModal( true );
}


/**
 * Function to destory the modal.
 */
NinjaModal.prototype.destroy = function () {
    this.popup.destroy();
}


/**
 * Function to increment the progress bar.
 * 
 * @param max (int) The maximum percentage of complete the progress bar can be.
 */
NinjaModal.prototype.incrementProgress = function ( max ) {
    var progressBar = document.getElementById( 'nf-progress-bar-slider-' + this.popup.dataId );
    // Get our current progress.
    var currentProgress = progressBar.offsetWidth / progressBar.parentElement.offsetWidth * 100;
    // If we've not already passed the max value...
    if ( max > currentProgress ) {
        // Increase the progress by 1 step.
        currentProgress = Number( currentProgress ) + 1;
        this.setProgress( currentProgress );
    }
}


/**
 * Function to initialize the buttons.
 */
NinjaModal.prototype.initButtons = function () {
    // If we have data for a primary button...
    if ( this.buttons.primary.data ) {
        // Create the button.
        var primary = document.createElement( 'div' );
        primary.id = 'nf-button-primary-' + this.popup.dataId;
        primary.classList.add( 'nf-button', 'primary', 'pull-right' );
        // If we have a class...
        if ( this.buttons.primary.data.class ) {
            // Add it to the class list.
            primary.classList.add( this.buttons.primary.data.class );
        }
        // If we were given button text...
        if ( 'undefined' != typeof this.buttons.primary.data.text ) {
            // Use it.
            primary.innerHTML = this.buttons.primary.data.text;
        } // Otherwise... (We were not given text.)
        else {
            // Use default text.
            // TODO: translate
            primary.innerHTML = 'Confirm';
        }
        this.buttons.primary.dom = primary;
        // Attach the callback.
        this.buttons.primary.callback = this.buttons.primary.data.callback;
        // Record that we have a primary button.
        this.hasPrimary = true;
        // Garbage collection...
        delete this.buttons.primary.data;
    }
    // If we have data for a secondary button...
    if ( this.buttons.secondary.data ) {
        // Create the button.
        var secondary = document.createElement( 'div' );
        secondary.id = 'nf-button-secondary-' + this.popup.dataId;
        secondary.classList.add( 'nf-button', 'secondary' );
        // If we have a class...
        if ( this.buttons.secondary.data.class ) {
            // Add it to the class list.
            secondary.classList.add( this.buttons.secondary.data.class );
        }
        // If we were given button text...
        if ( 'undefined' != typeof this.buttons.secondary.data.text ) {
            // Use it.
            secondary.innerHTML = this.buttons.secondary.data.text;
        } // Otherwise... (We were not given text.)
        else {
            // Use default text.
            // TODO: translate
            secondary.innerHTML = 'Cancel';
        }
        this.buttons.secondary.dom = secondary;
        // Attach the callback.
        this.buttons.secondary.callback = this.buttons.secondary.data.callback;
        // Record that we have a secondary button.
        this.hasSecondary = true;
        // Garbage collection...
        delete this.buttons.secondary.data;
    }
}


/**
 * Function to initialize the popup modal.
 */
NinjaModal.prototype.initModal = function () {
    // Save the context of this for callbacks.
    var that = this;
    // Setup our popup.
    this.popup = new jBox( 'Modal', {
        width: this.settings.width,
        addClass: this.settings.class,
        overlay: true,
        closeOnClick: this.settings.closeOnClick,
        closeOnEsc: this.settings.closeOnEsc,
        onOpen: function() {
            // If we have a primary button...
            if ( that.hasPrimary ) {
                // Attach the callback.
                jQuery( this.content ).find( '#nf-button-primary-' + this.dataId ).click( that.buttons.primary.callback );
            }
            // If we have a secondary button...
            if ( that.hasSecondary ) {
                // Attach the callback.
                jQuery( this.content ).find( '#nf-button-secondary-' + this.dataId ).click( that.buttons.secondary.callback );
            }
        },
    } );
    // Setup our data id to keep the DOM ids unique.
    this.popup.dataId = this.popup.id.replace( 'jBoxID', '' );
    // Render the title.
    this.renderTitle();
    // Initialize the buttons (if they exist).
    this.initButtons();
    // Render the content.
    this.renderContent();
}


/**
 * Function to toggle the display of the action block.
 * 
 * @param show (bool) Whether to show the block.
 */
NinjaModal.prototype.maybeShowActions = function ( show ) {
    if ( this.hasPrimary || this.hasSecondary ) {
        if ( show ) {
            document.getElementById( 'nf-action-block-' + this.popup.dataId ).style.display = 'block';
        }
        else {
            document.getElementById( 'nf-action-block-' + this.popup.dataId ).style.display = 'none';
        }
    }
}


/**
 * Function to toggle the display of the progress block.
 * 
 * @param show (bool) Whether to show the block.
 */
NinjaModal.prototype.maybeShowProgress = function ( show ) {
    if ( this.useProgressBar ) {
        if ( show ) {
            document.getElementById( 'nf-progress-block-' + this.popup.dataId ).style.display = 'block';
        }
        else {
            document.getElementById( 'nf-progress-block-' + this.popup.dataId ).style.display = 'none';
        }
    }
}


/**
 * Function to append the content to the popup.
 */
NinjaModal.prototype.renderContent = function () {
    // Delcare our template.
    var contentBox = document.createElement( 'div' );
    contentBox.classList.add( 'message' );
    contentBox.style.padding = '0px 20px 20px 20px';
    // Import our content.
    contentBox.innerHTML = this.content;

    // If we were told to use the progress bar...
    if ( this.useProgressBar ) {
        // Define our progress block.
        var progressBlock = document.createElement( 'div' );
        progressBlock.id = 'nf-progress-block-' + this.popup.dataId;
        progressBlock.style.display = 'none';
        // Define our progress bar.
        var progressBar = document.createElement( 'div' );
        progressBar.classList.add( 'nf-progress-bar' );
        var progressSlider = document.createElement( 'div' );
        progressSlider.id = 'nf-progress-bar-slider-' + this.popup.dataId;
        progressSlider.classList.add( 'nf-progress-bar-slider' );
        progressBar.appendChild( progressSlider );
        progressBlock.appendChild( progressBar );
        // Define our loading text.
        var loadingText = document.createElement( 'p' );
        loadingText.style.color = '#1ea9ea';
        loadingText.style.fontWeight = 'bold';
        loadingText.innerHTML = this.loadingText;
        progressBlock.appendChild( loadingText );
        // Append it to the content box.
        contentBox.appendChild( progressBlock );
    }
    // If we have buttons...
    if ( this.hasPrimary || this.hasSecondary ) {
        // Define our action block.
        var actionBlock = document.createElement( 'div' );
        actionBlock.id = 'nf-action-block-' + this.popup.dataId;
        actionBlock.classList.add( 'buttons' );
        // Insert the primary button, if one exists.
        if ( this.hasPrimary ) actionBlock.appendChild( this.buttons.primary.dom );
        // Insert the secondary button, if one exists.
        if ( this.hasSecondary ) actionBlock.appendChild( this.buttons.secondary.dom );
        // Append it to the content box.
        contentBox.appendChild( actionBlock );
        this.popup.onOpen = function() {
            this.buttons.primary.dom.onclick = this.buttons.primary.callback;
            this.buttons.secondary.dom.onclick = this.buttons.secondary.callback;
        }
    }
    // Set our content.
    this.popup.setContent( document.createElement( 'div' ).appendChild( contentBox ).parentElement.innerHTML );
}


/**
 * Function to append the title to the popup.
 */
NinjaModal.prototype.renderTitle = function () {
    // If we have a title...
    if ( '' != this.title ) {
        // Set our title.
        this.popup.setTitle( this.title );
    }
}


/**
 * Function to set the value of the progress bar.
 * 
 * @param percent (int) The value to set the progress bar to.
 */
NinjaModal.prototype.setProgress = function ( percent ) {
    // Update the width of the element as a percentage.
    var progressBar = document.getElementById( 'nf-progress-bar-slider-' + this.popup.dataId );
    progressBar.style.width = percent + '%';
}


/**
 * Function to toggle the visibility of the popup.
 * 
 * @param show (bool) Whether or not to show the popup.
 */
NinjaModal.prototype.toggleModal = function ( show ) {
    // If we were told to show the modal...
    if ( show ) {
        // Open it.
        this.popup.open();
    } // Otherwise... (We were told to hide it.)
    else {
        // Close it.
        this.popup.close();
    }
}


/**
 * Function to update the content of the popup.
 * 
 * @param content (string) The new content.
 */
NinjaModal.prototype.updateContent = function ( content ) {
    // Set the new content.
    this.content = content;
    // Re-render.
    this.renderContent();
}



/**
 * Function to update the title of the popup.
 * 
 * @param title (string) The new title.
 */
NinjaModal.prototype.updateTitle = function ( title ) {
    // Set the new title.
    this.title = title;
    // Re-render.
    this.renderTitle();
}