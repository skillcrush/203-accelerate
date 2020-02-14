<?php

abstract class NF_Telemetry_Metric
{
    protected $repository;

    public function __construct( NF_Telemetry_RepositoryInterface $repository )
    {
        $this->repository = $repository;
    }

    public function get()
    {
        return $this->repository->get();
    }

    public function save( $new_value )
    {
        return $this->repository->save( $new_value );
    }
}