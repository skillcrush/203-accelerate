<?php

namespace NinjaForms;

/**
 * A blank plugin installer skin.
 */
class Remote_Installer_Skin extends \Plugin_Installer_Skin
{
    protected $errors;

    public function error( $errors ){
        $this->errors = $errors;
    }

    public function get_errors(){
      return $this->errors;
    }

    public function feedback( $string ){
        // This section intentionally left blank
    }
    public function before(){
        // This section intentionally left blank.
    }
    public function after(){
        // This section intentionally left blank.
    }
}
