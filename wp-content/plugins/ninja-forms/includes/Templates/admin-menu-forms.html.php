<div id="nf-builder">
    <div id="nf-main">
        <!-- main content area. Where fields and actions are rendered. -->

        <?php
        for ($i=0; $i < 25; $i++) {
            echo '<div class="nf-field-wrap">' . esc_html__( 'Field', 'ninja-forms' ) . '</div>';
        }
        ?>

    </div>

    <div id="nf-drawer">
        <!-- drawer area. This is where settings and add fields are rendered. -->
        <!-- THIS IS THE CONTENT FOR EDITING FIELDS -->
        <header class="nf-full">
            <h2><?php esc_html_e( 'Editing Field', 'ninja-forms' ); ?></h2>
            <span><input type="submit" class="save-field-settings" value="SAVE" /></span>
        </header>
        <div class="nf-one-half">
            <label><?php esc_html_e( 'Label Name', 'ninja-forms' ); ?></label>
            <input type="text" />
        </div>
        <div class="nf-one-half">
            <label><?php esc_html_e( 'Label Position', 'ninja-forms' ); ?></label>
            <select>
                <option><?php esc_html_e( 'Above Field', 'ninja-forms' ); ?></option>
                <option><?php esc_html_e( 'Below Field', 'ninja-forms' ); ?></option>
                <option><?php esc_html_e( 'Left of Field', 'ninja-forms' ); ?></option>
                <option><?php esc_html_e( 'Right of Field', 'ninja-forms' ); ?></option>
                <option><?php esc_html_e( 'Hide Label', 'ninja-forms' ); ?></option>
            </select>
        </div>
        <div class="nf-full">
            <label><?php esc_html_e( 'Class Name', 'ninja-forms' ); ?></label>
            <input type="text" />
        </div>
    </div>


    <!--      THIS IS THE CONTENT FOR ADDING FIELDS
    <header class="nf-full">
        <input type="search" />
        <span><input type="submit" class="close-add-fields" value="DONE" /></span>
    </header>

    <div class="nf-reservoir">
        <span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span><span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span>
        <span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span><span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span>
        <span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span><span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span>
        <span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span><span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span>
        <span><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></span>
    </div>

    <h3><?php esc_html_e( 'Basic Fields', 'ninja-forms' ); ?></h3>

    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textarea', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Checkbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Dropdown', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Mult-Select', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e('Radio List', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Hidden Field', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Button', 'ninja-forms' ); ?></div>
    </div>


    <h3><?php esc_html_e( 'Basic Fields', 'ninja-fomrs' ); ?></h3>

    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textarea', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Checkbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Dropdown', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Mult-Select', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Radio List', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Hidden Field', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Button', 'ninja-forms' ); ?></div>
    </div>

    <div class="nf-full">
        <h3><?php esc_html_e( 'Basic Fields', 'ninja-forms' ); ?></h3>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textarea', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Checkbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Dropdown', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Mult-Select', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Radio List', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Hidden Field', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Button', 'ninja-forms' ); ?></div>
    </div>

    <div class="nf-full">
        <h3><?php esc_html_e( 'Basic Fields', 'ninja-forms' ); ?></h3>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textarea', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Checkbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Dropdown', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Mult-Select', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Radio List', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Hidden Field', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Button', 'ninja-forms' ); ?></div>
    </div>

    <div class="nf-full">
        <h3><?php esc_html_e( 'Basic Fields', 'ninja-forms' ); ?></h3>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Textarea', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Checkbox', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Dropdown', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Mult-Select', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Radio List', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Hidden Field', 'ninja-forms' ); ?></div>
    </div>
    <div class="nf-one-third">
        <div class="nf-field-button"><?php esc_html_e( 'Button', 'ninja-forms' ); ?></div>
    </div>
    -->
</div>
