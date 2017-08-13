jQuery(document).ready(function($) {
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

    $( '#nfRollback' ).on( 'click', function( event ){
        var rollback = confirm( nf_settings.i18n.rollbackConfirm );
        if( ! rollback ){
            event.preventDefault();
        }
    });
});
