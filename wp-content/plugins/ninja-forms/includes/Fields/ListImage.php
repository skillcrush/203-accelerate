<?php

if (!defined('ABSPATH')) exit;

/**
 * Class NF_Fields_ListImage
 */
class NF_Fields_ListImage extends NF_Abstracts_List
{
    protected $_name = 'listimage';

    protected $_type = 'listimage';

    protected $_nicename = 'Select Image';

    protected $_section = 'common';

    protected $_icon = 'image';

    protected $_templates = 'listimage';

    protected $option_type = 'radio';

    protected $_settings =  ['label', 'image_options', 'show_option_labels', 'allow_multi_select', 'list_orientation', 'num_columns'];

    protected $_settings_exclude = ['options'];

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__('Select Image', 'ninja-forms');

        add_filter('ninja_forms_merge_tag_calc_value_' . $this->_type, [$this, 'get_calc_value'], 10, 2);

        add_filter('ninja_forms_localize_field_listimage', [$this,'localizeField'], 10, 2);

        add_filter('ninja_forms_localize_field_listimage_preview', [$this,'localizeField'], 10, 2);
    }

    public function admin_form_element($id, $value)
    {
        $form_id = get_post_meta(absint($_GET['post']), '_form_id', true);

        $field = Ninja_Forms()->form($form_id)->get_field($id);

        $settings = $field->get_settings();
        $multi_select = $field->get_setting('allow_multi_select');
        $options = $field->get_setting('image_options');
        $options = apply_filters('ninja_forms_render_options', $options, $settings);
        $options = apply_filters('ninja_forms_render_options_' . $this->_type, $options, $settings);

        $list = '';
        foreach ($options as $option) {
            $checked = '';
            $type = '';
            if ($multi_select === 1 || is_array($value)) {
                $type = 'checkbox';
            } else {
                $type = 'radio';
            }
            if (is_array($value) && in_array($option['value'], $value)) {
                $checked = "checked";
            } elseif ($value === $option['value']) {
                $checked = 'checked';
            }
            $list .= "<li><label><input type='" . $type . "' value='{$option['value']}' name='fields[$id][]' $checked>{$option['label']}</label></li>";
        }

        return "<input type='hidden' name='fields[$id]' value='0' ><ul>$list</ul>";
    }

    /*
     * Appropriate output for a column cell in submissions list.
     */
    public function custom_columns( $value, $field )
    {
        if( $this->_name != $field->get_setting( 'type' ) ) return $value;
        
        //Consider &amp; to be the same as the & values in database in a selectbox saved value:
        if( ! is_array( $value ) ) $value = array( htmlspecialchars_decode($value) );

        $settings = $field->get_settings();
        $options = $field->get_setting( 'image_options' );
        $options = apply_filters( 'ninja_forms_render_options', $options, $settings );
        $options = apply_filters( 'ninja_forms_render_options_' . $field->get_setting( 'type' ), $options, $settings );

        $output = '';
        if( ! empty( $options ) ) {
            foreach ($options as $option) {

                if ( ! in_array( $option[ 'value' ], $value ) ) continue;

                $output .= esc_html( $option[ 'label' ] ) . "<br />";
            }
        }

        return $output;
    }

    public function get_calc_value($value, $field)
    {
        $selected = explode( ',', $value );
        $value = 0;
        if (isset($field['image_options'])) {
            foreach ($field['image_options'] as $option) {
                if( ! isset( $option[ 'value' ] ) || ! in_array( $option[ 'value' ], $selected )  || ! isset( $option[ 'calc' ] )  || ! is_numeric( $option[ 'calc' ] )) continue;
                $value +=  $option[ 'calc' ];
            }
        }
        return $value;
    }

    public function localizeField($field)
    {
        foreach ($field['settings']['image_options'] as $index => $img) {
            if (isset($img['image_id']) && is_numeric($img['image_id'])) {
                $post = get_post(intval($img['image_id']));
                if ($post) {
                    $img_alt = get_post_meta($img['image_id'], '_wp_attachment_image_alt');

                    $field['settings']['image_options'][$index]['img_title'] = $post->post_title;
                    if (is_array($img_alt) && ! empty($img_alt)) {
                        $field['settings']['image_options'][$index]['alt_text'] = $img_alt[0];
                    } else {
                        $field['settings']['image_options'][$index]['alt_text'] = '';
                    }
                } else {
                    $field['settings']['image_options'][$index]['image'] = Ninja_forms::$url . 'assets/img/no-image-available-icon-6.jpg';
                    $field['settings']['image_options'][$index]['img_title'] = '';
                    $field['settings']['image_options'][$index]['alt_text'] = '';
                }
            } else {
                $field['settings']['image_options'][$index]['image'] = Ninja_forms::$url . 'assets/img/no-image-available-icon-6.jpg';
                $field['settings']['image_options'][$index]['img_title'] = '';
                $field['settings']['image_options'][$index]['alt_text'] = '';
            }
        }

        return $field;
    }
}
