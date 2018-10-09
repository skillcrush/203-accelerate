<?php do_action( 'ninja_forms_before_form_display', $form_id ); ?>
<?php do_action( 'ninja_forms_display_pre_init', $form_id ); ?>
<?php do_action( 'ninja_forms_display_init', $form_id ); ?>
<?php if( is_user_logged_in() )do_action( 'ninja_forms_display_user_not_logged_in', $form_id ); ?>
<?php
	/*
	 * If we have a form wrapper, output it in the nf-form-cont div.
	 */
	$form_wrap = Ninja_Forms()->form( $form_id )->get()->get_setting( 'wrapper_class' );

	$wrapper_class = ( ! empty( $form_wrap ) ) ? ' ' . Ninja_Forms()->form( $form_id )->get()->get_setting( 'wrapper_class' ) : '';
?>
<div id="nf-form-<?php echo $form_id; ?>-cont" class="nf-form-cont<?php echo esc_attr( $wrapper_class ); ?>" aria-live="polite" aria-labelledby="nf-form-title-<?php echo $form_id ?>" aria-describedby="nf-form-errors-<?php echo $form_id; ?>" role="form">

    <div class="nf-loading-spinner"></div>

</div>
<?php do_action( 'ninja_forms_display_after_form', $form_id ); ?>
<?php do_action( 'ninja_forms_after_form_display', $form_id ); ?>
