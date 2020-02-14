<?php

interface NF_Telemetry_RepositoryInterface
{
    public function get();
    public function save( $new_value );
}