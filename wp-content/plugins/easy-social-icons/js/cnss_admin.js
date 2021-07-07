jQuery(document).ready(function($) {
	
	var custom_logo_uploader;
	
	$('#logo_image_button').click(function(e) {
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (custom_logo_uploader) {
            custom_logo_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_logo_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Select icon',
            button: {
                text: 'Set icon image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_logo_uploader.on('select', function() {
            attachment = custom_logo_uploader.state().get('selection').first().toJSON();
			//alert(attachment.id);
            $('#image_file').val(attachment.url);
			$('#logoimg').attr('width','32');
			$('#logoimg').attr('height','32');
            $('#logoimg').attr('src',attachment.url);
            $('#fa-placeholder').hide();
			$('#logoimg').show();
			//$('#ftrimg').attr('src',attachment.sizes.thumbnail.url);
			
        });
        //Open the uploader dialog
        custom_logo_uploader.open();
    });
});