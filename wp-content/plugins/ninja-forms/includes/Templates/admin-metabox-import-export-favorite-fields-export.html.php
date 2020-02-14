<div class="wrap">

    <form action="" method="post">

        <table class="form-table">
            <tbody>
            <tr id="row_nf_export_fields">
                <th scope="row">
                    <label for="nf_export_fields"><?php esc_html_e( 'Favorite Fields', 'ninja-forms' ); ?></label>
                </th>
                <td>
                    <ul>
                    <?php foreach( $fields as $field ): ?>
                        <li>
                            <input type="checkbox" name="nf_export_fields[]" value="<?php echo intval( $field->get_id() ); ?>">
                            <?php echo $field->get_setting( 'label' ); ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr id="row_nf_export_fields_submit">
                <th scope="row">
                    <label for="nf_export_fields_submit"><?php esc_html_e( 'Export Fields', 'ninja-forms' ); ?></label>
                </th>
                <td>
                    <input type="submit" id="nf_export_fields_submit" class="button-secondary" value="<?php esc_html_e( 'Export Fields', 'ninja-forms' ) ;?>">
                </td>
            </tr>
            </tbody>
        </table>

    </form>

</div>