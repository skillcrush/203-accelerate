function ninja_forms_sendwp_remote_install() {
    var data = {
        'action': 'ninja_forms_sendwp_remote_install',
    };
    
    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
        var data = JSON.parse(response);
        ninja_forms_sendwp_register_client(data.register_url, data.client_name, data.client_secret, data.client_redirect, data.partner_id);
    });
}

function ninja_forms_sendwp_register_client(register_url, client_name, client_secret, client_redirect, partner_id) {

    var form = document.createElement("form");
    form.setAttribute("method", 'POST');
    form.setAttribute("action", register_url);

    function ninja_forms_sendwp_append_form_input(name, value) {
        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", name);
        input.setAttribute("value", value);
        form.appendChild(input);
    }

    ninja_forms_sendwp_append_form_input('client_name', client_name);    
    ninja_forms_sendwp_append_form_input('client_secret', client_secret);
    ninja_forms_sendwp_append_form_input('client_redirect', client_redirect);   
    ninja_forms_sendwp_append_form_input('partner_id', partner_id);    
    
    document.body.appendChild(form);
    form.submit();
}