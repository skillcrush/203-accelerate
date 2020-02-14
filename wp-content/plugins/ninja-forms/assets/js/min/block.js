/**
 * Ninja Forms Form Block
 *
 * A block for embedding a Ninja Forms form into a post/page.
 */
( function( blocks, i18n, editor, element, components ) {

	var el = element.createElement, // function to create elements
		TextControl = components.TextControl,// text input control
        InspectorControls = editor.InspectorControls; // sidebar controls
	
	// register our block
	blocks.registerBlockType( 'ninja-forms/form', {
		title: 'Ninja Forms',
		icon: 'feedback',
		category: 'common',

		attributes: {
            formID: {
                type: 'integer',
                default: 0
            },
			formName: {
            	type: 'string',
				default: ''
			}
		},

		edit: function( props ) {

	        var formID = props.attributes.formID;

	        var formName = props.attributes.formName;

	        var children = [];

	        if( ! formID ) formID = ''; // Default.
			if( ! formName ) formName = ''; // Default

			// this function is required, but we don't need it to do anything
			function nfOnValueChange( formName ) { }

			// show the dropdown when we click on the input
			function nfFocusClick( event ) {
				var elID = event.target.getAttribute( 'id' );
				var idArray = elID.split( '-' );
				var nfOptions = document.getElementById( 'nf-filter-container-' + idArray[ idArray.length -1 ] );
				// get the related input element
				var nfInput = document.getElementById( 'nf-formFilter-' + idArray[ idArray.length -1 ] );
				// set focus to the element so the onBlur function runs properly
				nfInput.focus();
				nfOptions.style.display = 'block';
			}

			// function for select the form on filter drop down item click
			function selectForm( event ) {
				//set the attributes from the selected for item
				props.setAttributes( {
					formID: parseInt( event.target.getAttribute( 'data-formid' ) ),
					formName: event.target.innerText
				} );
				/**
				 * Get the main div of the filter to tell if this is being
				 * selected from the sidebar or block so we can hide the dropdown
	             */
				var elID = event.target.parentNode.parentNode;
				var idArray = elID.getAttribute( 'id' ).split( '-' );
				var nfOptions = document.getElementById( 'nf-filter-container-' + idArray[ idArray.length -1 ] );
				var inputEl = document.getElementById( 'nf-formFilter-sidebar' );
				
				if( inputEl ) {
					inputEl.value = '';
				}
				nfOptions.style.display = 'none';
			}

			function nfHideOptions( event ) {
				/**
				 * Get the main div of the filter to tell if this is being
				 * selected from the sidebar or block so we can hide the dropdown
				 */
				var elID = event.target.getAttribute( 'id' );
				var idArray = elID.split( '-' );
				var nfOptions = document.getElementById( 'nf-filter-container-' + idArray[ idArray.length -1 ] );
				nfOptions.style.display = 'none';
			}

			function nfInputKeyUp( event ) {
				var val = event.target.value;
				/**
				 * Get the main div of the filter to tell if this is being
				 * selected from the sidebar or block so we can SHOW the dropdown
				 */
				var filterInputContainer = event.target.parentNode.parentNode.parentNode;
				filterInputContainer.querySelector( '.nf-filter-option-container' ).style.display = 'block';
				filterInputContainer.style.display = 'block';

				// Let's filter the forms here
				_.each( ninjaFormsBlock.forms, function( form, index ) {
					var liEl = filterInputContainer.querySelector( "[data-formid='" + form.value + "']" );
					if ( 0 <= form.label.toLowerCase().indexOf( val.toLowerCase() ) ) {
						// shows options that DO contain the text entered
						liEl.style.display = 'block';
					} else {
						// hides options the do not contain the text entered
						liEl.style.display = 'none';
					}
				});
			}

			// Set up the form items from the localized php variables
			var formItems = [];
			_.each( ninjaFormsBlock.forms, function( form, index ) {
				formItems.push( el( 'li', { className: 'nf-filter-option',
						'data-formid': form.value, onMouseDown: selectForm},
						form.label + " ( ID: " + form.value + " )" ))
			});

			// Set up form filter for the block
			var inputFilterMain = el( 'div', { id: 'nf-filter-input-main',
					className: 'nf-filter-input' },
				el( TextControl, { id: 'nf-formFilter-main',
					placeHolder: 'Select a Form',
					className: 'nf-filter-input-el blocks-select-control__input',
					onChange: nfOnValueChange,
					onClick: nfFocusClick,
					onKeyUp: nfInputKeyUp,
					onBlur: nfHideOptions
				} ),
				el( 'span', { id: 'nf-filter-input-icon-main',
					className: 'nf-filter-input-icon',
					onClick: nfFocusClick,
					dangerouslySetInnerHTML: { __html: '&#9662;' } } ),
				el( 'div', { id: 'nf-filter-container-main',
						className: 'nf-filter-option-container' },
						el( 'ul', null, formItems )
				)
			);
			// Create filter input for the sidebar blocks settings
			var inputFilterSidebar = el( 'div', { id: 'nf-filter-input-sidebar',
					className: 'nf-filter-input' },
				el( TextControl, { id: 'nf-formFilter-sidebar',
					placeHolder: 'Select a Form',
					className: 'nf-filter-input-el blocks-select-control__input',
					onChange: nfOnValueChange,
					onClick: nfFocusClick,
					onKeyUp: nfInputKeyUp,
					onBlur: nfHideOptions
				} ),
				el( 'span', { id: 'nf-filter-input-icon-sidebar',
					className: 'nf-filter-input-icon',
					onClick: nfFocusClick,
					dangerouslySetInnerHTML: { __html: '&#9662;' } } ),
				el( 'div', { id: 'nf-filter-container-sidebar',
						className: 'nf-filter-option-container' },
					el( 'ul', null, formItems )
				)
			);

			// Set up the form filter dropdown in the side bar 'block' settings
	        var inspectorControls = el( InspectorControls, {},
		        el( 'span', null, 'Current selected form:' ),
		        el( 'br', null ),
		        el( 'span', null, formName ),
		        el( 'br', null ),
		        el ('hr', null ),
		        el ( 'label', { for: 'nf-formFilter-sidebar' }, 'Type to' +
			        ' filter' +
			        ' forms' ),
		        inputFilterSidebar
	            // el( SelectControl, { label: 'Form ID', value: formID, options: ninjaFormsBlock.forms, onChange: onFormChange } )
	        );

			/**
			 * Create the div container, add an overlay so the user can interact
			 * with the form in Gutenberg, then render the iframe with form
			 */
			if( '' === formID ) {
				children.push( el( 'div', {style : {width: '100%'}},
					el( 'img', { src: ninjaFormsBlock.block_logo}),
					el ( 'div', null, 'Type to Filter'),
					inputFilterMain
				) );
			} else {
				children.push(
					el( 'div', { className: 'nf-iframe-container' },
						el( 'div', { className: 'nf-iframe-overlay' } ),
						el( 'iframe', { src: ninjaFormsBlock.siteUrl + '?nf_preview_form='
							+ formID + '&nf_iframe', height: '0', width: '500', scrolling: 'no' })
					)
				)
			}
			children.push(inspectorControls);
			return [
				children
	        ];
		},

		save: function( props ) {
            var formID = props.attributes.formID;
			
            if( ! formID ) return '';
			/**
			 * we're essentially just adding a short code, here is where
			 * it's save in the editor
			 *
			 * return content wrapped in DIV b/c raw HTML is unsupported
			 * going forward
			 */
			var returnHTML = '[ninja_forms id=' + parseInt( formID ) + ']';
			return el( 'div', null, returnHTML );
		}
	} );


} )(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.editor,
	window.wp.element,
	window.wp.components
);
