<select name="form_id" id="form_id">
    <option value="0"><?php echo __( '- Select a form', 'ninja-forms' ); ?></option>
    <?php foreach( $form_options as $id => $title ): ?>
        <option value="<?php echo $id; ?>" <?php if( $id == $form_selected ) echo 'selected'; ?>>
            <?php echo $title . " ( ID: " . $id . " )"; ?>
        </option>
    <?php endforeach; ?>
</select>
<input type="text" name="nf_form_filter"
       id="nf_form_filter"
       class="nf_filter_sub_input"
       autocomplete="off"
       placeholder="Filter Forms"
/>
<div class="nf-form-dropdown">
	<ul class="widefat" id="nf_form_options">
		<?php foreach( $form_options as $id => $title ): ?>
			<li class="nf-form-option" data-val="<?php echo intval( $id ); ?>">
	                <span class="nf-form-option-item">
		                <?php echo $title . " ( ID: " . $id . " )"; ?>
	                </span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php if( isset( $_GET[ 'form_id' ] ) ): ?>
<input type="text" name="begin_date" class="datepicker" placeholder="<?php echo __( 'Begin Date', 'ninja-forms' ); ?>" value="<?php echo $begin_date; ?>">

<input type="text" name="end_date" class="datepicker" placeholder="<?php echo __( 'End Date', 'ninja-forms' ); ?>" value="<?php echo $end_date; ?>">
<?php endif; ?>

<script>
    jQuery( document).ready( function($) {

        $( '.datepicker').datepicker();

        $( '#form_id' ).change(function () {

            $( this ).parents( 'form:first' ).submit();

        });
    });
</script>