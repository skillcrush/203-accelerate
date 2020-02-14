<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Adds Ninja Forms widget.
 */
class NF_FormBlock {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		add_action( 'ninja_forms_loaded', array($this, 'nf_form_block_load' ) );
		// load the preview information and form
		add_action( 'wp_head', array( $this, 'load_preview_data' ) );
		// load the iframe containing the iframe
//		add_action( 'init', array( $this, 'load_preview_iframe' ) );
	}

	function nf_form_block_load() {
		// wait for Gutenberg to enqueue it's block assets
		add_action( 'enqueue_block_editor_assets', array ( $this, 'nf_form_block' ) );
	}

	function nf_form_block() {

		$js_dir  = Ninja_Forms::$url . 'assets/js/min/';
		$css_dir = Ninja_Forms::$url . 'assets/css/';

		// Once we have Gutenberg block javascript, we can enqueue our assets
		wp_register_script(
			'ninja-forms-block',
			$js_dir . 'block.js',
			array( 'wp-blocks', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-element', 'underscore' )
		);

		wp_register_style(
			'ninja-forms-block-style',
			$css_dir . 'nf-form-block-style.css',
			array( 'wp-edit-blocks' )
		);

		/**
		 * we need to get our forms so that the block can build a dropdown
		 * with the forms
		 * */
		wp_enqueue_script( 'ninja-forms-block' );

		$forms = array();

		foreach( Ninja_Forms()->form()->get_forms() as $form ){
			$forms[] = array (
				'value' => $form->get_id(),
				'label' => $form->get_setting( 'title' ),
			);
		}

		$block_logo = NF_PLUGIN_URL . 'assets/img/nf-logo-dashboard.png';
		$thumbnail_logo = NF_PLUGIN_URL . 'assets/img/ninja-forms-app-header-logo.png';

		wp_localize_script( 'ninja-forms-block', 'ninjaFormsBlock', array(
			'forms' => $forms,
			'siteUrl' => get_home_url(),
			'block_logo'     => $block_logo,
			'thumbnail_logo' => $thumbnail_logo
		) );
		wp_enqueue_style( 'ninja-forms-block-style' );
		wp_enqueue_style( 'ninja-forms-block-editor' );
	}

	public function load_preview_data() {

		$js_dir  = Ninja_Forms::$url . 'assets/js/min/';

		// check for preview and iframe get parameters
		if( isset( $_GET[ 'nf_preview_form' ] ) && isset( $_GET[ 'nf_iframe' ] ) ){
			$form_id = absint( $_GET[ 'nf_preview_form' ] );
			// Style below: update width and height for particular form
			?>
			<style media="screen">
				#wpadminbar {
					display: none;
				}
				#nf-form-<?php echo $form_id; ?>-cont {
					z-index: 9001;
					position: fixed;
					top: 0; left: 0;
					width: 100vw;
					height: 100vh;
					background-color: white;
					/* overflow-x: hidden; */
				}

				div.site-branding, header.entry-header, .site-footer {
					display:none;
				}

			</style>
			
			<?php

			// register our script to target the form iFrame in page builder
			wp_register_script(
				'ninja-forms-block-setup',
				$js_dir . 'blockFrameSetup.js',
				array( 'underscore', 'jquery' )
			);

			wp_localize_script( 'ninja-forms-block-setup', 'ninjaFormsBlockSetup', array(
				'form_id' => $form_id
			) );

			wp_enqueue_script( 'ninja-forms-block-setup' );
		}

	}

	public function load_preview_iframe() {
		if( ! isset( $_GET[ 'form_preview_iframe' ] ) ) return;

		// Style below: set styling for iframe
		?>
		<style media="screen">
			.iframe-container {
				position: relative;
			}
			.iframe-overlay {
				position: absolute;
				top: 0; right: 0; bottom: 0; left: 0;
			}
		</style>
		<div class="iframe-container">
			<div class="iframe-overlay"></div>
			<iframe
				id="idIframe"
				src="/?nf_preview_form=1&nf_iframe"
				frameborder="0"
				width="100%"
				onload="iframeLoaded()"
			></iframe>
		</div>
		<script type="text/javascript">
			function iframeLoaded() {
				var iFrameID = document.getElementById('idIframe');
				if(iFrameID) {
					var target = iFrameID.contentWindow.document.getElementById('nf-form-1-cont');

					// here you can make the height, I delete it first, then I make it again
					iFrameID.height = "";
					// set initial iFrame height
					iFrameID.height = target.scrollHeight;
				}
			}
		</script>
		<?php
		exit();
	}
}
