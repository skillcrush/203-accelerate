<div class="wrap">

    <form action="" method="post">

        <table class="form-table">
            <tbody>
                <tr id="row_nf_export_form">
	                <th scope="row">
		                <label for="nf_export_form_2"><?php esc_html_e( 'Select a form', 'ninja-forms' ); ?></label>
	                </th>
	                <td style="width:30%;">
		                <select name="nf_export_form" id="nf_export_form" class="">
			                <?php foreach( $forms as $form ): ?>
				                <option value="<?php echo
				                		$form['id']; ?>"<?php echo $form[ 'selected' ];?>><?php echo $form['title'] . " ( ID: " .
						                $form['id'] . " )"; ?></option>
			                <?php endforeach; ?>
		                </select>
	                </td>
	                <td style="position:relative;" style="width:30%;">
		                <input type="text" name="nf_export_form_2"
		                       id="nf_export_form_2"
		                       class="nf_export_input" autocomplete="off"
		                       placeholder="Filter Forms"
		                />
		                <div class="nf-form-dropdown">
			                <ul class="widefat" id="nf_form_export_options">
				                <?php foreach( $forms as $form ): ?>
				                <li class="nf-form-option"
				                    data-val="<?php echo intval( $form[ 'id' ] );
				                    ?>">
					                <span class="nf-form-option-item"><?php echo
							                $form[ 'title' ]
							                    . " ( ID: "
							                    . $form[ 'id' ] . " )"; ?>
					                </span>
				                </li>
				                <?php endforeach; ?>
			                </ul>
		                </div>
	                </td>
                </tr>
                <tr id="row_nf_export_form_encoding">
                    <th scope="row">
                        <label for="nf_export_form_turn_off_encoding"><?php
	                        esc_html_e( 'Disable UTF-8 Encoding', 'ninja-forms' );
	                        ?></label>
                    </th>
                    <td colspan="2">
                        <input type="checkbox" name="nf_export_form_turn_off_encoding"
                               id="nf_export_form_turn_off_encoding">
	                    <label style="color:red;font-style: italic;"
			                    for="nf_export_form_turn_off_encoding">
		                    If you are having trouble with form imports, please
		                    click here to disable UTF-8 encoding and try again.
	                    </label>
                    </td>
                </tr>
                <tr id="row_nf_export_form_submit">
	                <th scope="row">
		                <label for="nf_export_form_submit"><?php esc_html_e( 'Export Form', 'ninja-forms' ); ?></label>
	                </th>
	                <td>
		                <input type="submit" id="nf_export_form_submit" class="button-secondary" value="<?php esc_html_e( 'Export Form', 'ninja-forms' ) ;?>">
	                </td>
                </tr>
            </tbody>
        </table>

    </form>
</div>