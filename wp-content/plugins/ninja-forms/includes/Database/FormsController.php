<?php
final class NF_Database_FormsController
{
    private $db;
    private $factory;
    private $forms_data = array();

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function setFormsData()
    {
        try {
            $forms_sql = "
                SELECT `id`, `title`, `created_at`
                FROM `{$this->db->prefix}nf3_forms`
                ORDER BY `title`
            ";
            $forms_data = $this->db->get_results($forms_sql, OBJECT_K);

            $public_form_keys_sql = "SELECT link.parent_id as 'form_id', link.value as 'public_link_key'
            FROM `{$this->db->prefix}nf3_form_meta` as link
            LEFT JOIN `{$this->db->prefix}nf3_form_meta` as allowed
            ON allowed.parent_id = link.parent_id
            WHERE link.key = 'public_link_key'
            AND allowed.key = 'allow_public_link'
            AND allowed.value = '1';";
            $public_form_keys = $this->db->get_results($public_form_keys_sql, OBJECT_K);

            foreach($public_form_keys as $public_form_key){
                $form_id = $public_form_key->form_id;
                if(!isset($forms_data[$form_id])) continue;
                $forms_data[$form_id]->public_link_key = $public_form_key->public_link_key;
            }

        } catch( Exception $e ) {
            return array();
        }

        // Provided as array of
        // object {id => Str, title => Str, created_at => Str}

        return $forms_data;
    }

    public function getFormsData()
    {
        if( empty( $this->forms_data ) ) {
            $this->forms_data = $this->setFormsData();
        }
        return(  array_values( $this->forms_data ) );
    }
}
