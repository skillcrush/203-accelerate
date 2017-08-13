<ul>
    <?php foreach( $data as $name => $contents ):?>
        <li>
            <strong><?php echo( $name ); ?></strong>
            <?php
                echo( ' = ' . $contents[ 'value' ] );
                if( isset( $_GET[ 'calcs_debug' ] ) ) {
                    echo( '<br />RAW: ' . $contents[ 'raw' ]);
                    echo( '<br />PARSED: ' . $contents[ 'parsed' ]);
                }
            ?>
        </li>
    <?php endforeach; ?>
</ul>