<?php

class NF_Telemetry_MetricRepository implements NF_Telemetry_RepositoryInterface
{
    protected $option;

    public function __construct( $option, $default = false, $autoload = true )
    {
        $this->option = $option;
        $this->default = $default;
        $this->autoload = $autoload;
    }

    public function get()
    {
        return absint( get_option( $this->option, $this->default ) );
    }

    public function save( $new_value )
    {
        return update_option( $this->option, $new_value, $this->autoload );
    }
}