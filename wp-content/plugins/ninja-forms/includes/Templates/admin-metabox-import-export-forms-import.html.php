<div class="wrap">
    <table class="form-table">
        <tbody>
            <tr id="row-nf-import-response" style="display:none;background-color:#ffc;">
                <th></th>
                <td><?php printf( esc_html__( 'Form Imported Successfully. %sView Form%s', 'ninja-forms' ), '<a id="nf-import-url" href="#">', '</a>' ); ?></td>
            </tr>
            <tr id="row-nf-import-response-error" style="display:none;background-color:#ffc;color:red;">
                <th></th>
                <td></td>
            </tr>
            <tr id="row_nf_import_form">
                <th scope="row">
                    <label for="nf-import-file"><?php esc_html_e( 'Select a file', 'ninja-forms' ); ?></label>
                </th>
                <td>
                    <input type="file" id="nf-import-file" class="widefat">
                </td>
            </tr>
            <tr id="row-nf-import-type-error" style="display:none;color:red;">
                <th></th>
                <td><?php printf( esc_html__( 'Please select a Ninja Forms export. %sMust be in .nff format%s.', 'ninja-forms' ), '<strong>', '</strong>' ); ?></td>
            </tr>
            <tr id="row_nf_import_form_submit">
                <th scope="row">
                    <label for="nf-import-form-submit"><?php esc_html_e( 'Import Form', 'ninja-forms' ); ?></label>
                </th>
                <td>
                    <input type="button" id="nf-import-form-submit" class="button-secondary" value="<?php esc_html_e( 'Import Form', 'ninja-forms' ) ;?>">
                </td>
            </tr>
        </tbody>
    </table>
</div>