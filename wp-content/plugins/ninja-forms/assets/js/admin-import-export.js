jQuery( document ).ready( function( $ ) {

	var clickedElement;

	$(document).off( 'mousedown' ).on( 'mousedown', function( e ) {
		clickedElement = e.target;
	});

	$( '#nf_export_form_2' ).off( 'focus' ).on( 'focus', function () {
		//show the dropdown on focus of the input
		$( '.nf-form-dropdown' ).show();
	});

	$( '#nf_export_form_2' ).off( 'keyup' ).on( 'keyup', function () {
		//show the dropdown if it isn't show
		$( '.nf-form-dropdown' ).show();
		// get the value of the input, which we filter on
		var filter = $( this ).val();
		if( '' === filter ) {
			//if the filter val is empty, show all form options
			$( '.nf-form-dropdown' ).find( 'li' ).show();
		} else {

			$.each( $( '#nf_form_export_options span' ), function ( index, span ) {
				var tmpSpan = $( span );
				// test to see if span text contains the entered value
				if ( 0 <= tmpSpan.text().toLowerCase().indexOf( filter.toLowerCase() ) ) {
					// shows options that DO contain the text entered
					tmpSpan.parent().show();
				} else {
					// hides options the do not contain the text entered
					tmpSpan.parent().hide();
				}
			});
		}
	});

	$( '#nf_export_form_2' ).off( 'blur' ).on( 'blur' , function( e ) {
		if( 'undefined' !== typeof clickedElement ) {
			if ( ! $( clickedElement ).hasClass( 'nf-form-option-item' ) ) {
				$( '#nf_export_form_2' ).val( '' );
				$( '.nf-form-dropdown' ).hide();
			}
		}
	});

	$( '.nf-form-option' ).off( 'click' ).on( 'click', function() {
		// on click get the value of the input
		var val = $( this ).data( 'val' );
		// nf_export_form is now a hidden field instead of select element
		$( '#nf_export_form' ).val( val );
		// set the text of the input field
		$( '#nf_export_form_2' ).val( '' );
		// and hide the option.
		$( '.nf-form-dropdown' ).hide();
	});

});