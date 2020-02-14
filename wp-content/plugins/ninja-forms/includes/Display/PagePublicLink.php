<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Display_PagePublicLink extends NF_Display_Page
{
	public function __construct($form_id)
	{
		$this->form_id = $form_id;
		$this->form = Ninja_Forms()->form($this->form_id)->get();

		if($this->form->get_setting('allow_public_link')) {
			parent::__construct();
		}
	}

	/**
	 * @return string HTML
	 */
	public function get_content()
	{
		return "[ninja_forms id='$this->form_id']";
	}

	/**
	 * @return string
	 */
    public function get_title()
    {
        return ''; // Public form pages should not have visible page titles
    }

	/**
	 * @return string
	 */
    public function get_guid()
    {
        return 'ninja-forms-public-form';
    }
}
