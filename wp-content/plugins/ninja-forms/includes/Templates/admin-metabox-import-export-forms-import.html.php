<div class="wrap">

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="nf_import_security" id="nf_import_security"
            value="<?php echo wp_create_nonce( 'ninja_forms_import_form_nonce' );?>" />
        <table class="form-table">
            <tbody>
            <tr id="row_nf_import_form">
                <th scope="row">
                    <label for="nf_import_form"><?php echo __( 'Select a file', 'ninja-forms' ); ?></label>
                </th>
                <td>
                    <input type="file" name="nf_import_form" id="nf_import_form" class="widefat">
                </td>
            </tr>
            <tr id="row_nf_import_form_encoding">
	            <th scope="row">
		            <label for="nf_import_form_turn_off_encoding"><?php _e( 'Disable UTF-8 Encoding', 'ninja-forms' ); ?></label>
	            </th>
	            <td>
		            <input type="checkbox" name="nf_import_form_turn_off_encoding"
		                   id="nf_import_form_turn_off_encoding">
		            <label style="color:red;font-style: italic;"
				            for="nf_import_form_turn_off_encoding">
			            If you are having trouble importing forms, please
			            click here to disable UTF-8 encoding
			            and try again.
		            </label>
	            </td>
            </tr>
            <tr id="row_nf_import_form_submit">
                <th scope="row">
                    <label for="nf_import_form_submit"><?php _e( 'Import Form', 'ninja-forms' ); ?></label>
                </th>
                <td>
                    <input type="submit" id="nf_import_form_submit" class="button-secondary" value="<?php echo __( 'Import Form', 'ninja-forms' ) ;?>">
                </td>
            </tr>
            </tbody>
        </table>

    </form>

</div>