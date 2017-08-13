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
            $sql = "SELECT `id`, `title`, `created_at` FROM `{$this->db->prefix}nf3_forms` ORDER BY `title`";
            $forms_data = $this->db->get_results($sql, OBJECT_K);
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
