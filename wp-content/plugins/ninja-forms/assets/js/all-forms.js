jQuery(document).ready(function($) {
	$( '.trash a' ).click( function( e ) {
		e.preventDefault();
		var answer = confirm( nfi18n.confirm_delete );
		if ( answer ) {
			window.location.href = e.currentTarget.href;
		}
	} );
} );