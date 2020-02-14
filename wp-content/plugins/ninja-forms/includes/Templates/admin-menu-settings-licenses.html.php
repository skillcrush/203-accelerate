<?php if( ! $licenses ): ?>

    <?php echo sprintf( esc_html__( 'To activate licenses for Ninja Forms extensions you must first %sinstall and activate%s the chosen extension. License settings will then appear below.', 'ninja-forms' ), '<a target="_blank" href="https://ninjaforms.com/docs/installation/">', '</a>' ); ?>

<?php else: ?>

    <table class="form-table">
        <tbody>
        <?php foreach( $licenses as $license ): ?>
            <tr>
                <th>
                    <?php echo $license[ 'name' ]; ?>
                    <br /><small>v<?php echo $license[ 'version' ]; ?></small>
                </th>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="ninja_forms_license[name]" value="<?php echo $license[ 'id' ]; ?>">
                        <input type="text" class="widefat" name="ninja_forms_license[key]" value="<?php echo $license[ 'license' ];?>">

                        <?php if( $license[ 'error' ] ): ?>
                        <div>
                            <?php echo $license[ 'error' ]; ?>
                        </div>
                        <?php endif; ?>

                        <?php if( ! $license[ 'is_valid' ] ): ?>
                        <button type="submit" class="button button-primary" name="ninja_forms_license[action]" value="activate"><?php esc_html_e( 'Activate', 'ninja-forms' ); ?></button>
                        <?php else: ?>
                        <button type="submit" class="button button-secondary" name="ninja_forms_license[action]" value="deactivate"><?php esc_html_e( 'De-activate', 'ninja-forms' ); ?></button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php endif; ?>
