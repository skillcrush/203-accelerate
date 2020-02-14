/**
 * cacheMode should be 1 or 0
 * 
 * @param {int} cacheMode 
 */
function ninja_forms_update_cache_mode( cacheMode ) {
    var data = {
        'action': 'nf_update_cache_mode',
        'security': nfAdmin.ajaxNonce,
        'cache_mode': cacheMode
    };
    
    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
        var data = JSON.parse(response);
        console.log(data);
    });
}
