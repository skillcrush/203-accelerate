<h1><?php echo esc_html( $title ); ?></h1>

<p>
    <?php echo esc_html( $message ); ?>
</p>
<p>
    <a style="cursor: pointer" onclick="nfWpDieShowMore()"><?php esc_html_e( 'Show More', 'ninja-forms' ); ?></a>
    <pre style="display: none;" id="nfWpDieMore"><?php var_dump( $debug ); ?></pre>
</p>

<script>
    function nfWpDieShowMore(){
        console.log( 'show' );
        document.getElementById( 'nfWpDieMore' ).style.display = "block";
    }
</script>
