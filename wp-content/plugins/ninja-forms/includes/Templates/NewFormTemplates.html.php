<?php if ( ! defined( 'ABSPATH' ) ) exit;
    $templates = Ninja_Forms::config( 'NewFormTemplates' );

    foreach( $templates as $template ) {
        ?>
        <a href="?page=ninja-forms&form_id=<?php echo $template[ 'id' ]; ?>" class="nf-one-third template-box">
            <div class="template-box-inside">
                <h4><?php echo $template[ 'title' ]; ?></h4>
                <p class="template-desc"><?php echo $template[ 'template-desc' ]; ?></p>
            </div>
        </a>
        <?php
    }